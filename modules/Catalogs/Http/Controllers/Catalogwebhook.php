<?php

namespace Modules\Catalogs\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\Config;
use App\Models\Company;
use App\Models\Catalog;
use App\Models\Paymenttemplate;
use Modules\Catalogs\Models\ProductCategory;
use Modules\Catalogs\Models\OrderItem;
use Modules\Catalogs\Models\Order;
use Modules\Catalogs\Models\OrderAddress;
use App\Events\NewOrderReceived;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class Catalogwebhook extends Controller
{
    protected $accessToken;
    protected $whatsappBusinessPhoneNumberId;
    protected $companyId;
    const FACEBOOK_GRAPH_VERSION = 'v22.0';

    public function webhook(Request $request)
    {
        try {
            // Validate company exists
            $companyData = Company::find($request->company_id);
            if (!$companyData) {
                Log::error("Company not found: {$request->company_id}");
                return response('Company not found', 404);
            }

            $this->companyId = $companyData->id;

            // Get WhatsApp credentials
            $whatsapp_permanent_access_token = Config::where('model_id', $this->companyId)->where('key', 'whatsapp_permanent_access_token')->first();

            $whatsapp_phone_number_id = Config::where('model_id', $this->companyId)->where('key', 'whatsapp_phone_number_id')->first();

            if (!$whatsapp_permanent_access_token || !$whatsapp_phone_number_id) {
                Log::error("WhatsApp tokens not found for company: {$this->companyId}");
                return response('WhatsApp tokens not configured', 400);
            }

            $this->accessToken = $whatsapp_permanent_access_token->value;
            $this->whatsappBusinessPhoneNumberId = $whatsapp_phone_number_id->value;

            // Process POST request
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $requestBody = file_get_contents('php://input');
                $json = json_decode($requestBody, true);

                Log::info('Webhook received:', ['body' => $json]);

                // Check for status updates first
                if (isset($json['entry'][0]['changes'][0]['value']['statuses'])) {
                    $this->handleStatusUpdate($json);
                    return response('EVENT_RECEIVED', 200);
                }

                // Process message content
                if (!empty($requestBody) && isset($json['entry'][0]['changes'][0]['value']['messages'][0])) {
                    $this->processMessage($json);
                }

                return response('EVENT_RECEIVED', 200);
            }

            return response('Invalid request method', 405);
        } catch (\Exception $e) {
            Log::error('Webhook processing error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response('Internal Server Error', 500);
        }
    }

    private function processMessage($json)
    {
        try {
            $message = $json['entry'][0]['changes'][0]['value']['messages'][0];
            $waId = $json['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] ?? null;

            if (!$waId) {
                Log::error('Missing waId in message', $json);
                return;
            }

            // Handle interactive replies
            if (isset($message['interactive'])) {
                $this->handleInteractiveMessage($message, $waId, $json);
            }
            // Handle order messages
            elseif ($message['type'] === 'order') {
                $result = $this->processIncomingData($json);
                if (empty($result['errors'])) {
                    $this->handleNewOrder($result['order_details'], $json);
                } else {
                    Log::error('Order processing errors:', $result['errors']);
                }
            }
            // Handle standalone buttons
            elseif ($message['type'] === 'button' && isset($message['button']['payload']) && $message['button']['payload'] === 'Track Cart-Order') {
                $this->handleTrackOrderRequest($waId);
            }
            // Handle plain text messages (catalog trigger keywords)
            elseif ($message['type'] === 'text' && isset($message['text']['body'])) {
                $this->handleTextMessageForCatalog($message['text']['body'], $waId);
            }
        } catch (\Exception $e) {
            Log::error('Message processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle plain text messages that should trigger the catalog/menu reply.
     */
    private function handleTextMessageForCatalog(string $body, string $waId): void
    {
        try {
            $text = mb_strtolower(trim($body));
            if ($text === '') {
                return;
            }

            // Load catalog trigger keywords from settings
            $settings = Paymenttemplate::where('company_id', $this->companyId)->first();
            $keywordsRaw = $settings->catalog_trigger_keywords ?? 'shop,catalog,menu';

            $keywords = collect(explode(',', $keywordsRaw))
                ->map(function ($k) {
                    return mb_strtolower(trim($k));
                })
                ->filter()
                ->unique()
                ->values();

            if ($keywords->isEmpty()) {
                return;
            }

            // Check if incoming text contains any trigger keyword as a word
            $normalized = ' ' . preg_replace('/\s+/', ' ', $text) . ' ';
            $match = $keywords->first(function ($keyword) use ($normalized) {
                if ($keyword === '') {
                    return false;
                }
                // word boundary style match
                return mb_strpos($normalized, ' ' . $keyword . ' ') !== false;
            });

            if (!$match) {
                return;
            }

            Log::info("Catalog trigger keyword matched", ['keyword' => $match, 'text' => $text]);

            // Find any active catalog + first product category to show as menu
            $catalog = Catalog::where('company_id', $this->companyId)->where('status', 1)->first();
            $category = ProductCategory::where('company_id', $this->companyId)->first();

            if ($catalog && $category) {
                $this->sendWhatsAppMenuReply($waId, $category, $catalog->catalog_id);
            } else {
                // Fallback: simple text if catalog or category is missing
                $this->sendTextMessage($waId, __('Catalog is not configured yet. Please contact the store.'));
            }
        } catch (\Exception $e) {
            Log::error('Catalog keyword handling error: ' . $e->getMessage());
        }
    }

    private function handleInteractiveMessage($message, $waId, $json = null)
    {
        $interactive = $message['interactive'];
        $type = $interactive['type'] ?? 'unknown';
        Log::info("Handling interactive message of type: $type");

        // Button Reply Handling
        if ($type === 'button_reply' && isset($interactive['button_reply'])) {
            $buttonId = $interactive['button_reply']['id'];
            Log::info("Button reply received: $buttonId");
            $this->processReplyAction($buttonId, $waId);
        }
        // List Reply Handling
        elseif ($type === 'list_reply' && isset($interactive['list_reply'])) {
            $listId = $interactive['list_reply']['id'];
            $title = $interactive['list_reply']['title'];
            Log::info("List reply received - ID: $listId, Title: $title");

            // First try action-based handling
            if (!$this->processReplyAction($listId, $waId)) {
                // Fallback to product category lookup
                Log::info("Processing as product category: $title");
                $this->handleProductCategory($title, $waId);
            }
        }
        // NFM Reply Handling (Address form)
        elseif ($type === 'nfm_reply' && isset($interactive['nfm_reply'])) {
            Log::info('Address form received');

            if (isset($json['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'])) {
                $waId = $json['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] ?? null;
                $nameProfile = $json['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] ?? '';

                $responseJson = json_decode($json['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['nfm_reply']['response_json'], true);
                $responseJson = $responseJson['values'];
                $orderUpdate = Order::where('phone_number', $waId)->orderBy('created_at', 'DESC')->first();

                if ($orderUpdate) {
                    $items = $orderUpdate->items
                        ->map(function ($item) {
                            return [
                                'retailer_id' => $item->retailer_id,
                                'name' => $item->name,
                                'amount' => [
                                    'value' => $item->amount_value,
                                    'offset' => $item->amount_offset,
                                ],
                                'quantity' => $item->quantity,
                            ];
                        })
                        ->toArray();

                    $discountDis = strtoupper($orderUpdate->discount_type) . ($orderUpdate->discount_type == 'percent' ? ' : ' . $orderUpdate->discount . '%' : '');

                    // Update fields
                    $orderUpdate->for_person = $responseJson['name'] ?? '';
                    $orderUpdate->for_person_number = $responseJson['phone_number'] ?? '';
                    $orderUpdate->landmark_area = $responseJson['landmark_area'] ?? '';
                    $orderUpdate->address = $responseJson['address'] ?? '';
                    $orderUpdate->pin_code = $responseJson['in_pin_code'] ?? '';
                    $orderUpdate->city = $responseJson['city'] ?? '';
                    $orderUpdate->state = $responseJson['state'] ?? '';
                    $orderUpdate->house_number = $responseJson['house_number'] ?? '';
                    $orderUpdate->building_name = $responseJson['building_name'] ?? '';
                    $orderUpdate->tower_number = $responseJson['tower_number'] ?? '';
                    $orderUpdate->shipping_method = 'Delivery';

                    $orderUpdate->save();

                    if ($responseJson['landmark_area']) {
                        $message = "📦 Your address has been received for order *#{$orderUpdate->reference_id}*.\n\n🛍️ We will process your order as soon as possible and keep you updated! 😊";

                        $sentAddressConfirmation = $this->sendMessageAfterAddress($orderUpdate->phone_number, $message);
                    }

                    if ($sentAddressConfirmation) {
                        $paymentTemplate = Paymenttemplate::where('company_id', $this->companyId)->first();

                        if ($paymentTemplate && $paymentTemplate->payment_method_enable == 1) {
                            try {
                                $this->sendWhatsAppOrderDetailMessage(
                                    $waId,
                                    $this->accessToken,
                                    $this->whatsappBusinessPhoneNumberId,
                                    [
                                        'body_text' => $paymentTemplate->body ?? 'Thank you for placing order with us. Kindly make payment to proceed your order.',
                                        'footer_text' => $paymentTemplate->footer ?? '',
                                        'items' => $items,
                                        'subtotal_value' => $orderUpdate->subtotal_value,
                                        'subtotal_offset' => $orderUpdate->subtotal_offset,
                                        'tax_value' => $orderUpdate->tax_value,
                                        'tax_offset' => $orderUpdate->tax_offset,
                                        'tax_description' => '', // Add default or logic if needed
                                    ],
                                    $orderUpdate->reference_id,
                                    $paymentTemplate->getActivePaymentConfig(),
                                    $paymentTemplate->payment_configuration_other,
                                    $paymentTemplate->payment_type,
                                    $paymentTemplate->shipping_description,
                                    $paymentTemplate->shipping,
                                    $orderUpdate->discount_amount,
                                    $discountDis,
                                    $orderUpdate->final_amount,
                                );
                            } catch (\Exception $e) {
                            }
                        }
                    }

                    // Save address if not duplicate
                    $addressCheck = $responseJson['address'] ?? '';
                    $existingAddress = OrderAddress::where('phone_number', $waId)->where('address', $addressCheck)->first();

                    if (!$existingAddress) {
                        $OrderAddress = new OrderAddress();
                        $OrderAddress->landmark_area = $responseJson['landmark_area'] ?? '';
                        $OrderAddress->address = $responseJson['address'] ?? '';
                        $OrderAddress->pin_code = $responseJson['in_pin_code'] ?? '';
                        $OrderAddress->city = $responseJson['city'] ?? '';
                        $OrderAddress->state = $responseJson['state'] ?? '';
                        $OrderAddress->house_number = $responseJson['house_number'] ?? '';
                        $OrderAddress->building_name = $responseJson['building_name'] ?? '';
                        $OrderAddress->tower_number = $responseJson['tower_number'] ?? '';
                        $OrderAddress->order_id = $orderUpdate->reference_id;
                        $OrderAddress->phone_number = $waId;
                        $OrderAddress->user_name = $responseJson['name'] ?? $nameProfile;
                        $OrderAddress->save();
                    }
                } else {
                    // Log if no order found
                }
            } else {
            }
        }
        // Payment Handling
        elseif ($type === 'payment' && isset($interactive['payment'])) {
            Log::info('Payment update received');
            $this->handlePaymentUpdate($json);
        } else {
            Log::warning("Unhandled interactive type: $type", ['interactive' => $interactive]);
        }
    }

    private function calculateTotalAmount($subtotal, $tax)
    {
        return $subtotal + $tax;
    }

    public function sendMessageAfterAddress($toPhoneNumber, $messageBody)
    {
        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $toPhoneNumber,
                'type' => 'text',
                'text' => [
                    'preview_url' => false,
                    'body' => $messageBody,
                ],
            ];

            $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/{$this->whatsappBusinessPhoneNumberId}/messages";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer {$this->accessToken}"]);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            $response = curl_exec($ch);
            curl_close($ch);

            return $response;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function handleStatusUpdate($json)
    {
        try {
            $statusData = $json['entry'][0]['changes'][0]['value']['statuses'][0];
            $status = $statusData['status'] ?? null;
            $payment = $statusData['payment'] ?? null;

            if ($payment && ($referenceId = $payment['reference_id'] ?? null)) {
                $order = Order::where('reference_id', $referenceId)->where('company_id', $this->companyId)->first();

                if ($order) {
                    $order->status = $status;

                    if ($transaction = $payment['transaction'] ?? null) {
                        $order->transaction_id = $transaction['id'] ?? null;
                        $order->transaction_type = $transaction['type'] ?? null;
                        $order->currency = $transaction['currency'] ?? null;
                        $order->payment_status = $transaction['status'] ?? null;
                    }

                    $order->save();

                    // Send address form if payment completed
                    if ($status === 'completed' && isset($statusData['recipient_id'])) {
                        $this->sendWhatsAppAddressDetailMessage($statusData['recipient_id'], $referenceId);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Status update error: ' . $e->getMessage());
        }
    }

    private function handlePaymentUpdate($json)
    {
        try {
            $paymentData = $json['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['payment'];

            $transaction_id = $paymentData['transaction_id'] ?? null;
            $transaction_type = $paymentData['transaction_type'] ?? null;
            $reference_id = $paymentData['reference_id'] ?? null;
            $currency = $paymentData['currency'] ?? null;
            $status = $paymentData['status'] ?? null;
            $phoneNumber = $json['entry'][0]['changes'][0]['value']['contacts'][0]['wa_id'] ?? null;

            if ($reference_id) {
                $order = Order::where('reference_id', $reference_id)->where('company_id', $this->companyId)->first();

                if ($order) {
                    $order->update([
                        'transaction_id' => $transaction_id,
                        'transaction_type' => $transaction_type,
                        'currency' => $currency,
                        'payment_status' => $status,
                    ]);

                    // Send address form after payment if not already sent
                    if (!$order->address) {
                        $this->sendWhatsAppAddressDetailMessage($phoneNumber, $reference_id);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Payment update error: ' . $e->getMessage());
        }
    }

    private function handleNewOrder($orderDetails, $json)
    {
        try {
            $phoneNumber = $orderDetails['phone_number'] ?? null;
            $entry_id = $json['entry'][0]['id'] ?? null;

            if (!$phoneNumber) {
                Log::error('Missing phone number in new order');
                return;
            }

            $paymentTemplate = Paymenttemplate::where('company_id', $this->companyId)->first();

            if (!$paymentTemplate) {
                Log::error("Payment template not found for company: {$this->companyId}");
                return;
            }

            $referenceId = $this->generateReferenceId();

            DB::transaction(function () use ($orderDetails, $paymentTemplate, $phoneNumber, $entry_id, $referenceId) {
                // Convert subtotal to rupees
                $subtotalRupees = ($orderDetails['subtotal_value'] ?? 0) / 100;

                // Log initial values for debugging
                Log::debug('Order calculation values:', [
                    'subtotalRupees' => $subtotalRupees,
                    'paymentTemplate' => $paymentTemplate->toArray(),
                ]);

                // Calculate shipping
                $shippingRupees = (float) $paymentTemplate->shipping;
                $shippingRupees_amount = (float) $paymentTemplate->shipping_free_from_amount;
                $isShippingFree = $paymentTemplate->isShippingFree;

                if ($isShippingFree) {
                    if ($subtotalRupees >= $shippingRupees_amount) {
                        $shippingRupees = 0; // Free shipping for orders > ₹550
                    }
                }

                // Calculate discount
                $discountRupees = 0;
                $discountType = 'percent';
                $discountValue = 0;

                $isDiscountAutoApply = $paymentTemplate->isDiscountAutoApply;
                $discountFromAmount = (float) $paymentTemplate->discount_from_amount;

                if ($isDiscountAutoApply) {
                    if ($subtotalRupees >= $discountFromAmount) {
                        $discountType = $paymentTemplate->discount_type ?? 'percent';
                        $discountValue = (float) ($paymentTemplate->default_discount ?? 0);

                        if ($discountType === 'fixed') {
                            $discountRupees = $discountValue;
                        } elseif ($discountType === 'percent') {
                            $discountRupees = ($subtotalRupees * $discountValue) / 100;
                        }

                        Log::debug('Discount applied:', [
                            'type' => $discountType,
                            'value' => $discountValue,
                            'amount' => $discountRupees,
                        ]);
                    }
                }

                // Calculate final amount
                $finalRupees = $subtotalRupees + $shippingRupees - $discountRupees;

                // Create new order
                $order = Order::create([
                    'user_name' => $orderDetails['user_name'] ?? 'Customer',
                    'company_id' => $this->companyId,
                    'reference_id' => $referenceId,
                    'phone_number' => $phoneNumber,
                    'subtotal_value' => $orderDetails['subtotal_value'] ?? 0,
                    'final_amount' => $finalRupees,
                    'subtotal_offset' => $orderDetails['subtotal_offset'] ?? 100,
                    'shipping_cast' => $shippingRupees,
                    'message_id' => $orderDetails['message_id'] ?? null,
                    'tax_value' => $orderDetails['tax_value'] ?? 0,
                    'tax_offset' => $orderDetails['tax_offset'] ?? 100,
                    'entry_id' => $entry_id,
                    'status' => 'order',
                    'type' => 'order',
                    'discount_type' => $discountType,
                    'discount' => $discountValue,
                    'discount_amount' => $discountRupees,
                ]);

                // Log full order details
                Log::debug('Order created:', $order->toArray());

                // Create order items
                foreach ($orderDetails['items'] as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'retailer_id' => $item['retailer_id'] ?? null,
                        'name' => $item['name'] ?? 'Product',
                        'amount_value' => $item['amount']['value'] ?? 0,
                        'amount_offset' => $item['amount']['offset'] ?? 100,
                        'quantity' => $item['quantity'] ?? 1,
                    ]);
                }

                // Trigger event
                event(new NewOrderReceived($order));

                // Send cart review message
                $this->sendCartReviewConfirmation($order);
            });
        } catch (\Exception $e) {
            Log::error('New order error: ' . $e->getMessage());

            // Send detailed debug message
            $debugInfo = [
                'error' => $e->getMessage(),
                'orderDetails' => $orderDetails,
                'subtotalRupees' => $subtotalRupees ?? 'N/A',
                'discountType' => $discountType ?? 'N/A',
                'discountValue' => $discountValue ?? 'N/A',
                'discountRupees' => $discountRupees ?? 'N/A',
            ];
        }
    }

    private function sendCartReviewConfirmation(Order $order)
    {
        try {
            $messageBody = $this->generateCartReviewMessage($order);

            $this->sendWhatsAppCartReviewMessage($order->phone_number, $messageBody, $order->reference_id);
        } catch (\Exception $e) {
            Log::error('Cart review message error: ' . $e->getMessage());
        }
    }

    private function generateCartReviewMessage(Order $order)
    {
        // $message = "📋 *ORDER SUMMARY* 📋\n";
        // $message .= "——————————————\n";
        // $message .= "🆔 *Order ID*: #{$order->reference_id}\n";
        // $message .= "👤 *Customer*: {$order->user_name}\n\n";

        // $message .= "🛒 *YOUR ITEMS*\n";
        // $message .= "——————————————\n";

        // // Calculate totals
        // $subtotal = 0;
        // foreach ($order->items as $index => $item) {
        //     $itemNumber = $index + 1;
        //     $itemPrice = $item->amount_value / $item->amount_offset;
        //     $itemTotal = $itemPrice * $item->quantity;
        //     $subtotal += $itemTotal;

        //     $message .= "{$itemNumber}. *{$item->name}*\n";
        //     $message .= "   ├─ Quantity: {$item->quantity}\n";
        //     $message .= '   ├─ Unit Price: ₹' . number_format($itemPrice, 2) . "\n";
        //     $message .= '   └─ *Item Total: ₹' . number_format($itemTotal, 2) . "*\n\n";
        // }

        // $shipping = $order->shipping_cast ?? 0;
        // $total = $subtotal + $shipping;

        // $message .= "💰 *ORDER SUMMARY*\n";
        // $message .= "——————————————\n";
        // $message .= '├─ Subtotal: ₹' . number_format($subtotal, 2) . "\n";
        // $message .= '├─ Shipping: ₹' . number_format($shipping, 2) . "\n";
        // $message .= "╰──────────────────\n";
        // $message .= '💳 *GRAND TOTAL: ₹' . number_format($total, 2) . "*\n\n";

        // $message .= "📌 *NEXT STEPS*\n";
        // $message .= "——————————————\n";
        // $message .= "Please review your order. If everything looks correct, click the button below to confirm and proceed to delivery details.\n\n";
        // $message .= "✅ We'll prepare your order immediately after confirmation!";

        $message = "🧾 *Order #{$order->reference_id}* for {$order->user_name}\n";
        $message .= "🛍️ *Items:*\n";

        $subtotal = 0;
        foreach ($order->items as $item) {
            $itemPrice = $item->amount_value / $item->amount_offset;
            $itemTotal = $itemPrice * $item->quantity;
            $subtotal += $itemTotal;
            $message .= "- {$item->name} × {$item->quantity} = ₹" . number_format($itemTotal, 2) . "\n";
        }

        $shipping = $order->shipping_cast ?? 0;
        $discount = $order->discount_amount ?? 0;
        $total = $subtotal + $shipping - $discount;

        $message .= "\n💰 *Subtotal:* ₹" . number_format($subtotal, 2) . "\n";

        // Show discount if applied
        if ($discount > 0) {
            $discountType = $order->discount_type === 'percent' ? $order->discount . '%' : '₹' . number_format($order->discount, 2);

            $message .= '🎁 *Discount (' . $discountType . '):* -₹' . number_format($discount, 2) . "\n";
        }

        $message .= '🚚 *Shipping:* ₹' . number_format($shipping, 2) . "\n";
        $message .= '💳 *Total:* ₹' . number_format($total, 2) . "\n\n";
        $message .= '📌 Please confirm & share your delivery address.';

        return $message;
    }

    private function sendWhatsAppCartReviewMessage($toPhoneNumber, $messageBody, $reference_id)
    {
        try {
            $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/{$this->whatsappBusinessPhoneNumberId}/messages";

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $toPhoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'button',
                    'body' => [
                        'text' => $messageBody,
                    ],
                    'action' => [
                        'buttons' => [
                            [
                                'type' => 'reply',
                                'reply' => [
                                    'id' => 'DOTFLOPC_' . $reference_id,
                                    'title' => 'Yes, I confirm!',
                                ],
                            ],
                            [
                                'type' => 'reply',
                                'reply' => [
                                    'id' => 'DOTFLOCC_' . $reference_id,
                                    'title' => 'Cancel order',
                                ],
                            ],
                        ],
                    ],
                ],
            ];

            $response = Http::withToken($this->accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            Log::info('Cart review message response:', $response->json());
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Cart review message error: ' . $e->getMessage());
            return false;
        }
    }

    private function processIncomingData($data)
    {
        $orderDetails = [];
        $errors = [];

        try {
            if (isset($data['entry'][0]['changes'][0]['value'])) {
                $value = $data['entry'][0]['changes'][0]['value'];

                // Extract user name
                if (isset($value['contacts'][0]['profile']['name'])) {
                    $orderDetails['user_name'] = $value['contacts'][0]['profile']['name'];
                } else {
                    $errors[] = 'User name not found.';
                }

                // Extract phone number
                if (isset($value['messages'][0]['from'])) {
                    $orderDetails['phone_number'] = $value['messages'][0]['from'];
                } else {
                    $errors[] = 'Phone number not found.';
                }

                // Extract product items
                if (isset($value['messages'][0]['order']['product_items']) && is_array($value['messages'][0]['order']['product_items'])) {
                    $orderDetails['items'] = [];
                    $subtotal = 0;

                    foreach ($value['messages'][0]['order']['product_items'] as $item) {
                        if (isset($item['item_price'])) {
                            $productName = 'Unknown Product';

                            // Fetch product name from database
                            if (!empty($item['product_retailer_id'])) {
                                $product = DB::table('catalog_products')->where('retailer_id', $item['product_retailer_id'])->first();

                                if ($product) {
                                    $productName = $product->product_name;
                                }
                            }

                            $price = $item['item_price'] * 100;
                            $quantity = $item['quantity'];
                            $itemTotal = $price * $quantity;
                            $subtotal += $itemTotal;

                            $orderDetails['items'][] = [
                                'retailer_id' => $item['product_retailer_id'],
                                'name' => $productName,
                                'amount' => [
                                    'value' => $price,
                                    'offset' => 100,
                                ],
                                'quantity' => $quantity,
                            ];
                        }
                    }

                    $orderDetails['subtotal_value'] = $subtotal;
                    $orderDetails['subtotal_offset'] = 100;
                } else {
                    $errors[] = 'Product items not found.';
                }

                // Extract message ID
                if (isset($value['messages'][0]['id'])) {
                    $orderDetails['message_id'] = $value['messages'][0]['id'];
                } else {
                    $errors[] = 'Message ID not found.';
                }

                // Set static fields
                $orderDetails['tax_value'] = 0;
                $orderDetails['tax_offset'] = 100;
                $orderDetails['tax_description'] = 'Tax Included';
            } else {
                $errors[] = 'Invalid webhook structure';
            }
        } catch (\Exception $e) {
            $errors[] = 'Processing error: ' . $e->getMessage();
        }

        return [
            'order_details' => $orderDetails,
            'errors' => $errors,
        ];
    }

    private function buildOrderDetailsFromOrder(Order $order)
    {
        return [
            'user_name' => $order->user_name,
            'phone_number' => $order->phone_number,
            'items' => $order->items
                ->map(function ($item) {
                    return [
                        'retailer_id' => $item->retailer_id,
                        'name' => $item->name,
                        'amount' => [
                            'value' => $item->amount_value,
                            'offset' => $item->amount_offset,
                        ],
                        'quantity' => $item->quantity,
                    ];
                })
                ->toArray(),
            'subtotal_value' => $order->subtotal_value,
            'subtotal_offset' => $order->subtotal_offset,
            'tax_value' => $order->tax_value,
            'tax_offset' => $order->tax_offset,
            'tax_description' => 'Tax Included',
            'body_text' => 'Please confirm your payment to proceed with your order',
            'footer_text' => 'Thank you for your purchase!',
        ];
    }

    private function sendWhatsAppOrderDetailMessage($phoneNumber, $accessToken, $whatsappBusinessPhoneNumberId, $orderDetails, $referenceId, $paymentConfiguration, $payment_configuration_other, $payment_type, $shipping_description, $shipping, $discount, $discountDis, $finalAmount)
    {
        $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/$whatsappBusinessPhoneNumberId/messages";

        $subtotal = $orderDetails['subtotal_value'];
        $tax = $orderDetails['tax_value'];

        $totalAmountValue = $this->calculateTotalAmount($subtotal, $tax);
        if ($payment_type == 1) {
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'order_details',
                    'body' => [
                        'text' => $orderDetails['body_text'],
                    ],
                    'footer' => [
                        'text' => $orderDetails['footer_text'],
                    ],
                    'action' => [
                        'name' => 'review_and_pay',
                        'parameters' => [
                            'reference_id' => $referenceId,
                            'type' => 'digital-goods',
                            'payment_settings' => [
                                [
                                    'type' => 'payment_gateway',
                                    'payment_gateway' => [
                                        'type' => 'razorpay',
                                        'configuration_name' => $payment_configuration_other,
                                        'razorpay' => [
                                            'receipt' => $referenceId,
                                        ],
                                    ],
                                ],
                            ],
                            'currency' => 'INR',
                            'total_amount' => [
                                'value' => ($finalAmount + $shipping - $discount ?? 0) * 100,
                                'offset' => 100,
                            ],
                            'order' => [
                                'status' => 'pending',
                                'items' => $orderDetails['items'],
                                'subtotal' => [
                                    'value' => $subtotal,
                                    'offset' => 100,
                                ],
                                'tax' => [
                                    'value' => $tax,
                                    'offset' => 100,
                                    'description' => $orderDetails['tax_description'] ?? '',
                                ],
                                'shipping' => [
                                    'value' => ($shipping ?? 0) * 100,
                                    'offset' => 100,
                                    'description' => $shipping_description ?? 'no shipping',
                                ],
                                'discount' => [
                                    'value' => $discount ? ($discount ?? 0) * 100 : 0,
                                    'offset' => 100,
                                    'description' => $discountDis ?? 'No Discount',
                                    'discount_program_name' => 'None',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'order_details',
                    'body' => [
                        'text' => $orderDetails['body_text'],
                    ],
                    'footer' => [
                        'text' => $orderDetails['footer_text'],
                    ],
                    'action' => [
                        'name' => 'review_and_pay',
                        'parameters' => [
                            'reference_id' => $referenceId,
                            'type' => 'digital-goods',
                            'payment_type' => 'upi',
                            'payment_configuration' => $paymentConfiguration,
                            'currency' => 'INR',
                            'total_amount' => [
                                'value' => ($finalAmount + $shipping - $discount ?? 0) * 100,
                                'offset' => 100,
                            ],
                            'order' => [
                                'status' => 'pending',
                                'items' => $orderDetails['items'],
                                'subtotal' => [
                                    'value' => $subtotal,
                                    'offset' => 100,
                                ],
                                'tax' => [
                                    'value' => $tax,
                                    'offset' => 100,
                                    'description' => $orderDetails['tax_description'] ?? '',
                                ],
                                'shipping' => [
                                    'value' => ($shipping ?? 0) * 100,
                                    'offset' => 100,
                                    'description' => $shipping_description ?? 'no shipping',
                                ],
                                'discount' => [
                                    'value' => $discount ? ($discount ?? 0) * 100 : 0,
                                    'offset' => 100,
                                    'description' => $discountDis ?? 'No Discount',
                                    'discount_program_name' => 'None',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', "Authorization: Bearer $accessToken"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            echo "cURL error: $error";
            return null;
        }

        curl_close($ch);

        if ($httpStatus >= 400) {
            echo "HTTP request failed with status $httpStatus: $response";
            return null;
        }

        return json_decode($response, true);
    }

    private function sendWhatsAppAddressDetailMessage($phoneNumber, $referenceId)
    {
        // $message = "🛍️ Thanks for your order!\n📦 Please share the delivery address for your order #{$referenceId}.\nWe’ll take care of the rest! 😊";
        $message = "📦 Please share the delivery address for your order #{$referenceId}.\n\nWe’ll take care of the rest! 😊";

        $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/{$this->whatsappBusinessPhoneNumberId}/messages";

        $orders = OrderAddress::where('phone_number', $phoneNumber)->whereNotNull('address')->get();

        if (count($orders) > 0) {
            $savedAddresses = [];
            foreach ($orders as $order) {
                $savedAddresses[] = [
                    'id' => $order->id,
                    'value' => [
                        'name' => $order->user_name,
                        'phone_number' => $order->phone_number,
                        'in_pin_code' => $order->pin_code,
                        'floor_number' => $order->tower_number,
                        'building_name' => $order->building_name,
                        'address' => $order->address,
                        'landmark_area' => $order->landmark_area,
                        'city' => $order->city,
                        'state' => $order->state,
                    ],
                ];
            }
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'ADDRESS_MESSAGE',
                    'body' => [
                        'text' => $message,
                    ],
                    'action' => [
                        'name' => 'address_message',
                        'parameters' => [
                            'country' => 'IN',
                            'reference_id' => $referenceId,
                            'saved_addresses' => $savedAddresses,
                        ],
                    ],
                ],
            ];
        } else {
            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $phoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'ADDRESS_MESSAGE', // Corrected type
                    'body' => [
                        'text' => '📦 To proceed with delivery, kindly reply with your complete address, including any landmarks or special instructions',
                    ],
                    'action' => [
                        'name' => 'address_message',
                        'parameters' => [
                            'country' => 'IN',
                            'reference_id' => $referenceId,
                        ],
                    ],
                ],
            ];
        }

        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Authorization: Bearer {$this->accessToken}", // Ensure $accessToken is defined
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Execute the cURL request
        $response = curl_exec($ch);

        // Get HTTP status code
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Error handling
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            echo "cURL error: $error";
            return null;
        }

        curl_close($ch);

        // Check for HTTP errors
        if ($httpStatus >= 400) {
            echo "HTTP request failed with status $httpStatus: $response";
            return null;
        }

        return json_decode($response, true);
    }

    // private function sendWhatsAppAddressDetailMessage($phoneNumber, $referenceId)
    // {
    //     try {
    //         if (!$phoneNumber) {
    //             Log::error('Phone number missing for address message');
    //             return false;
    //         }

    //         $url = "https://graph.facebook.com/v17.0/{$this->whatsappBusinessPhoneNumberId}/messages";

    //         $savedAddresses = [];
    //         $addresses = OrderAddress::where('phone_number', $phoneNumber)
    //             ->whereNotNull('address')
    //             ->where('company_id', $this->companyId)
    //             ->get();

    //         Log::info("Found " . count($addresses) . " saved addresses for $phoneNumber");

    //         foreach ($addresses as $address) {
    //             $savedAddresses[] = [
    //                 'id' => $address->id,
    //                 'value' => [
    //                     'name' => $address->user_name,
    //                     'phone_number' => $address->phone_number,
    //                     'in_pin_code' => $address->pin_code,
    //                     'floor_number' => $address->tower_number,
    //                     'building_name' => $address->building_name,
    //                     'address' => $address->address,
    //                     'landmark_area' => $address->landmark_area,
    //                     'city' => $address->city,
    //                     'state' => $address->state,
    //                 ],
    //             ];
    //         }

    //         $data = [
    //             'messaging_product' => 'whatsapp',
    //             'recipient_type' => 'individual',
    //             'to' => $phoneNumber,
    //             'type' => 'interactive',
    //             'interactive' => [
    //                 'type' => 'address_message',
    //                 'body' => [
    //                     'text' => 'Thanks for your order! Please provide the address where you’d like this order delivered.',
    //                 ],
    //                 'action' => [
    //                     'name' => 'address_message',
    //                     'parameters' => [
    //                         'country' => 'IN',
    //                         'reference_id' => $referenceId,
    //                     ],
    //                 ],
    //             ],
    //         ];

    //         // Add saved addresses if available
    //         if (!empty($savedAddresses)) {
    //             $data['interactive']['action']['parameters']['saved_addresses'] = $savedAddresses;
    //         }

    //         Log::info("Sending address message to: $phoneNumber for order: $referenceId");
    //         $response = Http::withToken($this->accessToken)
    //             ->post($url, $data);

    //         $responseData = $response->json();
    //         Log::info('Address message response:', $responseData);

    //         // Check for errors in response
    //         if (isset($responseData['error'])) {
    //             Log::error('WhatsApp API error: ' . json_encode($responseData['error']));
    //         }

    //         return $responseData;
    //     } catch (\Exception $e) {
    //         Log::error('Address message error: ' . $e->getMessage());
    //         return false;
    //     }
    // }

    private function sendWhatsAppMenuReply($contact, $ProductCategory, $CatalogId)
    {
        try {
            $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/{$this->whatsappBusinessPhoneNumberId}/messages";

            if (!$ProductCategory) {
                Log::error('Product category not found');
                return false;
            }

            $sections = [];
            $retailerIds = explode(',', $ProductCategory->retailer_id);
            $productItems = [];

            foreach ($retailerIds as $retailerId) {
                $productItems[] = ['product_retailer_id' => trim($retailerId)];
            }

            $sections[] = [
                'title' => $ProductCategory->name,
                'product_items' => $productItems,
            ];

            $data = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $contact,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'product_list',
                    'header' => [
                        'type' => 'text',
                        'text' => $ProductCategory->name,
                    ],
                    'body' => [
                        'text' => 'Explore our product selection below!',
                    ],
                    'footer' => [
                        'text' => '👇 Choose an option',
                    ],
                    'action' => [
                        'catalog_id' => $CatalogId,
                        'sections' => $sections,
                    ],
                ],
            ];

            $response = Http::withToken($this->accessToken)->post($url, $data);

            Log::info('Menu reply response:', $response->json());
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Menu reply error: ' . $e->getMessage());
            return false;
        }
    }

    private function handleTrackOrderRequest($waId)
    {
        try {
            $pendingOrders = Order::where('phone_number', $waId)->where('company_id', $this->companyId)->where('status', '!=', 'delivered')->get();

            if ($pendingOrders->count() > 0) {
                $message = "📦 *Your Order Updates!*\nHere are your pending orders:\n\n";
                foreach ($pendingOrders as $order) {
                    $status = $order->status === 'order' ? 'Pending' : ucfirst($order->status);
                    $message .= "🔹 *Order ID:* #{$order->reference_id}\n📍 *Status:* {$status}\n\n";
                }
                $message .= "We'll notify you as soon as they move to the next stage!";
            } else {
                $message = "🎉 *Great news!* You don't have any pending orders. All your orders have been delivered. 😊";
            }

            $this->sendTextMessage($waId, $message);
        } catch (\Exception $e) {
            Log::error('Track order error: ' . $e->getMessage());
        }
    }

    private function handleProductCategory($title, $waId)
    {
        try {
            $productCategory = ProductCategory::where('company_id', $this->companyId)->where('name', $title)->first();

            if ($productCategory) {
                $catalog = Catalog::where('company_id', $this->companyId)->first();
                if ($catalog) {
                    $this->sendWhatsAppMenuReply($waId, $productCategory, $catalog->catalog_id);
                }
            }
        } catch (\Exception $e) {
            Log::error('Product category error: ' . $e->getMessage());
        }
    }

    private function processReplyAction($replyId, $waId)
    {
        $parts = explode('_', $replyId);
        $action = $parts[0];
        $referenceId = $parts[1] ?? null;

        if ($referenceId) {
            switch ($action) {
                case 'DOTFLOPC':
                    $this->handleProcessUpdatedCart($referenceId, $waId);
                    return true;
                case 'DOTFLOCC':
                    $this->handleCancelOrder($referenceId, $waId);
                    return true;
                case 'INSTORE':
                    $this->sendWhatsAppInStorePickup($waId, $referenceId, 'inStore');
                    return true;
                case 'PICKUP':
                    $this->sendWhatsAppInStorePickup($waId, $referenceId, 'Self Pickup');
                    return true;
                case 'DELIVERY':
                    $this->sendWhatsAppAddressDetailMessage($waId, $referenceId);
                    return true;
            }
        }
        return false;
    }

    // private function handleProcessUpdatedCart($referenceId, $waId)
    // {
    //     try {
    //         Log::info("Processing cart update for order: $referenceId, WA: $waId");
    //         $order = Order::where('reference_id', $referenceId)->where('company_id', $this->companyId)->where('phone_number', $waId)->first();

    //         if ($order) {
    //             $newStatus = $order->isOrderConfirmed ? 0 : 1;
    //             $order->update(['isOrderConfirmed' => $newStatus]);

    //             if ($newStatus == 1) {
    //                 Log::info("Order confirmed, sending address form for order: $referenceId");
    //                 $message = "Thank you for confirming your order! 🎉\n\n";
    //                 $message .= "We've received your confirmation for order #{$referenceId}. ";
    //                 $message .= 'Please provide your delivery details in the next step.';
    //                 $this->sendTextMessage($waId, $message);

    //                 // Send address form
    //                 //
    //                 $messageBody = "🚚 *How would you like to receive your order?*\n\nPlease choose one of the options below to proceed:";

    //                 $this->sendWhatsAppShippinTypeMessage($waId, $messageBody, $referenceId, $this->companyId);
    //             }
    //         } else {
    //             Log::error("Order not found for cart update: $referenceId, WA: $waId");
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Process cart error: {$e->getMessage()}");
    //     }
    // }

    private function handleProcessUpdatedCart($referenceId, $waId)
    {
        try {
            Log::info("Processing cart update for order: $referenceId, WA: $waId");
            $order = Order::where('reference_id', $referenceId)->where('company_id', $this->companyId)->where('phone_number', $waId)->first();

            if ($order) {
                // ✅ Check if order is cancelled
                if ($order->status === 'cancelled') {
                    Log::info("Order already cancelled: $referenceId, WA: $waId");

                    $cancelMessage = "❌ Your order #{$referenceId} is already *cancelled* and cannot be confirmed again.\n\n";
                    $cancelMessage .= 'But don’t worry! 😊 You can place a *new order* and we’ll happily confirm it for you.';

                    $this->sendTextMessage($waId, $cancelMessage);
                    return; // stop here
                }

                $newStatus = $order->isOrderConfirmed ? 0 : 1;
                $order->update(['isOrderConfirmed' => $newStatus]);

                if ($newStatus == 1) {
                    Log::info("Order confirmed, sending address form for order: $referenceId");
                    $message = "Thank you for confirming your order! 🎉\n\n";
                    $message .= "We've received your confirmation for order #{$referenceId}. ";
                    $message .= 'Please provide your delivery details in the next step.';
                    $this->sendTextMessage($waId, $message);

                    // Send address form
                    $messageBody = "🚚 *How would you like to receive your order?*\n\nPlease choose one of the options below to proceed:";
                    $this->sendWhatsAppShippinTypeMessage($waId, $messageBody, $referenceId, $this->companyId);
                }
            } else {
                Log::error("Order not found for cart update: $referenceId, WA: $waId");
            }
        } catch (\Exception $e) {
            Log::error("Process cart error: {$e->getMessage()}");
        }
    }

    private function handleCancelOrder($referenceId, $waId)
    {
        try {
            $order = Order::where('reference_id', $referenceId)->where('company_id', $this->companyId)->where('phone_number', $waId)->first();

            if ($order) {
                if ($order->status === 'order' || $order->isOrderConfirmed != 1) {
                    // Allow cancellation
                    $order->update(['status' => 'cancelled']);

                    $message = "Your order #{$referenceId} has been cancelled. ❌\n\n";
                    $message .= "No further action is needed from your side.\n\n";
                    $message .= "Our ordering window is always open if you'd like to place a new order!";
                } else {
                    // Already processed, can’t cancel
                    $message = "Your order #{$referenceId} is already being processed and cannot be cancelled now. ⚠️\n\n";
                    $message .= 'If you need further help, please contact our support team.';
                }

                $this->sendTextMessage($waId, $message);
            }
        } catch (\Exception $e) {
            Log::error("Cancel order error: {$e->getMessage()}");
        }
    }

    private function sendTextMessage($to, $message)
    {
        try {
            $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/{$this->whatsappBusinessPhoneNumberId}/messages";

            $response = Http::withToken($this->accessToken)->post($url, [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

            Log::info('Text message sent:', ['to' => $to, 'response' => $response->json()]);
            return $response->json();
        } catch (\Exception $e) {
            Log::error("WhatsApp text message failed: {$e->getMessage()}");
            return false;
        }
    }

    private function generateReferenceId()
    {
        return str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    }

    // private function sendWhatsAppShippinTypeMessage($toPhoneNumber, $messageBody, $reference_id)
    // {
    //     try {
    //         $url = "https://graph.facebook.com/v18.0/{$this->whatsappBusinessPhoneNumberId}/messages";

    //         $payload = [
    //             'messaging_product' => 'whatsapp',
    //             'recipient_type' => 'individual',
    //             'to' => $toPhoneNumber,
    //             'type' => 'interactive',
    //             'interactive' => [
    //                 'type' => 'button',
    //                 'body' => [
    //                     'text' => $messageBody,
    //                 ],
    //                 'action' => [
    //                     'buttons' => [
    //                         [
    //                             'type' => 'reply',
    //                             'reply' => [
    //                                 'id' => 'PICKUP_' . $reference_id,
    //                                 'title' => 'Self Pickup',
    //                             ],
    //                         ],
    //                         [
    //                             'type' => 'reply',
    //                             'reply' => [
    //                                 'id' => 'INSTORE_' . $reference_id,
    //                                 'title' => 'inStore',
    //                             ],
    //                         ],
    //                         [
    //                             'type' => 'reply',
    //                             'reply' => [
    //                                 'id' => 'DELIVERY_' . $reference_id,
    //                                 'title' => 'Delivery',
    //                             ],
    //                         ],
    //                     ],
    //                 ],
    //             ],
    //         ];

    //         $response = Http::withToken($this->accessToken)
    //             ->withHeaders(['Content-Type' => 'application/json'])
    //             ->post($url, $payload);

    //         Log::info('Cart review message response:', $response->json());
    //         return $response->json();
    //     } catch (\Exception $e) {
    //         Log::error('Cart review message error: ' . $e->getMessage());
    //         return false;
    //     }
    // }

    private function sendWhatsAppShippinTypeMessage($toPhoneNumber, $messageBody, $reference_id, $companyId)
    {
        try {
            $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/{$this->whatsappBusinessPhoneNumberId}/messages";

            // Get shipping methods settings
            //  $companyId = auth()->user()->company_id; // Or however you get company ID
            $settings = Paymenttemplate::where('company_id', $companyId)->select('enable_self_pickup', 'enable_in_store', 'enable_delivery')->first();

            // Build buttons array based on enabled methods
            $buttons = [];

            if ($settings->enable_self_pickup) {
                $buttons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => 'PICKUP_' . $reference_id,
                        'title' => 'Self Pickup',
                    ],
                ];
            }

            if ($settings->enable_in_store) {
                $buttons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => 'INSTORE_' . $reference_id,
                        'title' => 'inStore',
                    ],
                ];
            }

            if ($settings->enable_delivery) {
                $buttons[] = [
                    'type' => 'reply',
                    'reply' => [
                        'id' => 'DELIVERY_' . $reference_id,
                        'title' => 'Delivery',
                    ],
                ];
            }

            // Ensure at least one button is enabled
            if (empty($buttons)) {
                Log::error('No shipping methods enabled for company: ' . $companyId);
                return false;
            }

            $payload = [
                'messaging_product' => 'whatsapp',
                'recipient_type' => 'individual',
                'to' => $toPhoneNumber,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'button',
                    'body' => [
                        'text' => $messageBody,
                    ],
                    'action' => [
                        'buttons' => $buttons,
                    ],
                ],
            ];

            $response = Http::withToken($this->accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            Log::info('Cart review message response:', $response->json());
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Cart review message error: ' . $e->getMessage());
            return false;
        }
    }

    private function sendWhatsAppInStorePickup($toPhoneNumber, $reference_id, $selection)
    {
        try {
            $url = 'https://graph.facebook.com/' . self::FACEBOOK_GRAPH_VERSION . "/{$this->whatsappBusinessPhoneNumberId}/messages";

            $order = Order::where('reference_id', $reference_id)->where('phone_number', $toPhoneNumber)->where('company_id', $this->companyId)->first();

            // Normalize the selection title (optional)
            $selectionFormatted = ucfirst(strtolower($selection)); // "Instore" or "Self pickup"
            if ($order) {
                $order->shipping_method = $selection;
                $order->save();
            }

            $messageText = "✅ *Thank you, {$order->user_name}!* \n\n";
            $messageText .= "You've selected *{$selectionFormatted}* for order #{$reference_id}.\n";
            $messageText .= "We’ll be waiting for you! 🛍️\n\n";
            $messageText .= 'Let us know if you need any help along the way.';

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $toPhoneNumber,
                'type' => 'text',
                'text' => [
                    'body' => $messageText,
                    'preview_url' => false,
                ],
            ];

            $response = Http::withToken($this->accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $payload);

            Log::info('InStore/Pickup confirmation sent:', $response->json());
            return $response->json();
        } catch (\Exception $e) {
            Log::error('Error sending InStore/Pickup message: ' . $e->getMessage());
            return false;
        }
    }
}
