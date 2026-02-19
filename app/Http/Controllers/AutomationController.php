<?php

namespace App\Http\Controllers;

use App\Models\Template;
use App\Models\CompanyCampaign;
use App\Models\Automation;
use App\Models\FacebookLeads;
use App\Models\FacebookAdAccount;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Events\WhatsAppTriggerRequested;
use App\Models\Config;

class AutomationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->company_id) {
                return redirect()->route('dashboard')->withErrors('Company not associated.');
            }

            $companyCampaignIds = CompanyCampaign::where('company_id', $user->company_id)
                ->pluck('campaign_id')
                ->toArray();

            $templates = Template::where('company_id', $user->company_id)->get();
             $automations = Automation::with('template')->latest()->where('user_id', $user->id)->get();
            //  echo "<pre>";
            //  print_r($automations);die;
            $fbLeadsQuery = FacebookLeads::whereIn('campaign_id', $companyCampaignIds);

            if ($request->filled('campaign')) {
                $fbLeadsQuery->where('campaign_id', $request->query('campaign'));
            }

            $fbLeads = $fbLeadsQuery->orderBy('created_time', 'desc')
                ->paginate(10)
                ->appends($request->query());
                
            // Get distinct account names for dropdown/filter
            $accountNames = FacebookAdAccount::where('company_id', $user->company_id)->select('account_id', 'account_name')
                ->distinct()
                ->get();

            return view('automationform.index', [
                'fbLeads' => $fbLeads,
                'template' => $templates,
                'parameters' => $request->query->count() > 0,
                'automations' => $automations,
                'accountNames' => $accountNames,
            ]);
        } catch (\Exception $e) {
            Log::error('AutomationController@index error: ' . $e->getMessage());
            return redirect()->route('dashboard')->withErrors('Something went wrong.');
        }
    }
    
    // public function showReconnectForm()
    // {
    //     $user = auth()->user();
    
    //     // Optional: protect access
    //     if (!$user) {
    //         return redirect()->route('login')->withErrors('Please log in first.');
    //     }
    
    //     return view('automationform.reconnect', [
    //         'client_id' => $user->client_id,
    //         'client_secret' => $user->client_secret,
    //         'fb_long_lived_token' => $user->fb_long_lived_token,
    //         'whatsapp_sender_id' => $user->whatsapp_sender_id,
    //     ]);
    // }
    
    public function showReconnectForm()
    {
        $user = auth()->user();
    
        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }
    
        // Fetch CTWA config values
        $webhookToken = Config::where([
            ['key', '=', 'ctwa_webhook_token'],
            ['model_type', '=', get_class($user)],
            ['model_id', '=', $user->id],
        ])->value('value');
    
        $webhookUrl = Config::where([
            ['key', '=', 'ctwa_webhook_url'],
            ['model_type', '=', get_class($user)],
            ['model_id', '=', $user->id],
        ])->value('value');
    
        return view('automationform.reconnect', [
            'client_id' => $user->client_id,
            'client_secret' => $user->client_secret,
            'fb_long_lived_token' => $user->fb_long_lived_token,
            'whatsapp_sender_id' => $user->whatsapp_sender_id,
            'ctwa_webhook_token' => $webhookToken,
            'ctwa_webhook_url' => $webhookUrl,
        ]);
    }

    public function fb_automation()
    {
        $user = auth()->user();
    
        // Optional: protect access
        if (!$user) {
            return redirect()->route('login')->withErrors('Please log in first.');
        }
    
        return view('automationform.automation');
    }

    // public function reconnect(Request $request)
    // {
    //     try {
    //         $user = auth()->user();
    
    //         // Step 1: Check user auth
    //         if (!$user) {
    //             return redirect()->back()->withErrors('User not authenticated.');
    //         }
    
    //         // Step 2: Ensure company is associated
    //         if (!$user->company_id) {
    //             return redirect()->back()->withErrors('Company not associated with your account.');
    //         }
    
    //         // Step 3: Validate input fields
    //         $validated = $request->validate([
    //             'client_id' => 'required|string',
    //             'client_secret' => 'required|string',
    //             'fb_exchange_token' => 'required|string',
    //             'whatsapp_sender_id' => 'required|string',
    //         ]);
    
    //         // Step 4: Exchange short-lived token for long-lived token
    //         $tokenResponse = Http::get('https://graph.facebook.com/v21.0/oauth/access_token', [
    //             'grant_type' => 'fb_exchange_token',
    //             'client_id' => $validated['client_id'],
    //             'client_secret' => $validated['client_secret'],
    //             'fb_exchange_token' => $validated['fb_exchange_token'],
    //         ]);
    
    //         $data = $tokenResponse->json();
    
    //         // Log response for debugging
    //         Log::info('Facebook Token Exchange Response:', $data);
    
    //         if ($tokenResponse->successful() && isset($data['access_token'])) {
    //             // Step 5: Store tokens and credentials
    //             $user->fb_long_lived_token = $data['access_token'];
    //             $user->client_id = $validated['client_id'];
    //             $user->client_secret = $validated['client_secret'];
    //             $user->whatsapp_sender_id = $validated['whatsapp_sender_id'];
    //             $user->save();
    
    //             return redirect()->back()->with('success', 'Connected successfully!');
    //         } else {
    //             $error = $data['error']['message'] ?? 'Failed to retrieve long-lived token.';
    //             return redirect()->back()->withErrors($error);
    //         }
    
    //     } catch (\Exception $e) {
    //         // Log the full error
    //         Log::error('AutomationController@reconnect Exception: ' . $e->getMessage());
    
    //         return redirect()->back()->withErrors('Something went wrong. Please try again. (' . $e->getMessage() . ')');
    //     }
    // }
    
    public function reconnect(Request $request)
    {
        try {
            $user = auth()->user();
    
            // Step 1: Authentication check
            if (!$user) {
                return redirect()->back()->withErrors('User not authenticated.');
            }
    
            // Step 2: Ensure company is associated
            if (!$user->company_id) {
                return redirect()->back()->withErrors('Company not associated with your account.');
            }
    
            // Step 3: Validate input fields
            $validated = $request->validate([
                'client_id' => 'required|string|max:255',
                'client_secret' => 'required|string|max:255',
                'fb_exchange_token' => 'required|string',
                'whatsapp_sender_id' => 'required|string|max:255',
            ]);
    
            // Step 4: Exchange short-lived token for long-lived token
            $tokenResponse = Http::get('https://graph.facebook.com/v21.0/oauth/access_token', [
                'grant_type' => 'fb_exchange_token',
                'client_id' => $validated['client_id'],
                'client_secret' => $validated['client_secret'],
                'fb_exchange_token' => $validated['fb_exchange_token'],
            ]);
    
            $data = $tokenResponse->json();
            Log::info('Facebook Token Exchange Response:', $data);
    
            if ($tokenResponse->successful() && isset($data['access_token'])) {
                // Step 5: Save credentials to user
                $user->fb_long_lived_token = $data['access_token'];
                $user->client_id = $validated['client_id'];
                $user->client_secret = $validated['client_secret'];
                $user->whatsapp_sender_id = $validated['whatsapp_sender_id'];
                $user->save();
    
                // Step 6: Generate webhook token and URL
                $webhookToken = bin2hex(random_bytes(16)); // Secure token
                $webhookUrl = route('webhook.ctwa', ['token' => $webhookToken]);
    
                // Step 7: Store in config (polymorphic model)
                Config::updateOrCreate([
                    'key' => 'ctwa_webhook_token',
                    'model_type' => get_class($user),
                    'model_id' => $user->id,
                ], [
                    'value' => $webhookToken,
                ]);
    
                Config::updateOrCreate([
                    'key' => 'ctwa_webhook_url',
                    'model_type' => get_class($user),
                    'model_id' => $user->id,
                ], [
                    'value' => $webhookUrl,
                ]);
    
                // Optional: Save sender ID as config too
                Config::updateOrCreate([
                    'key' => 'whatsapp_sender_id',
                    'model_type' => get_class($user),
                    'model_id' => $user->id,
                ], [
                    'value' => $validated['whatsapp_sender_id'],
                ]);
    
                return redirect()->back()->with('success', 'Connected successfully. Webhook generated.');
            } else {
                $error = $data['error']['message'] ?? 'Failed to retrieve long-lived token.';
                return redirect()->back()->withErrors($error);
            }
    
        } catch (\Exception $e) {
            Log::error('AutomationController@reconnect Exception: ' . $e->getMessage());
    
            return redirect()->back()->withErrors('Something went wrong. Please try again. (' . $e->getMessage() . ')');
        }
    }

    public function fetchCampaignData(Request $request)
    {
        $campaignId = $request->campaign_id;
    
        // Fetch ads from FacebookLeads based on campaign_id
        $ads = FacebookAdAccount::where('campaign_id', $campaignId)
            ->select('ad_id', 'ad_name')
            ->distinct()
            ->get();
    
        $leadIds = FacebookAdAccount::where('campaign_id', $campaignId)
            ->select('lead_id')->distinct()->get();
    
        return response()->json([
            'ads' => $ads,
            'leads' => $leadIds,
        ]);
    }
    
    public function fetchCampaigns(Request $request)
    {
        $campaigns = FacebookAdAccount::where('account_id', $request->account_id)
            ->select('campaign_id', 'campaign_name')
            ->distinct()
            ->get();
    
        return response()->json(['campaigns' => $campaigns]);
    }

    public function fetchAdSets(Request $request)
    {
        $campaignId = $request->input('campaign_id');
    
        if (!$campaignId) {
            return response()->json(['status' => 'error', 'message' => 'Campaign ID is required.'], 400);
        }
    
        $query = FacebookAdAccount::where('campaign_id', $campaignId)
            ->select('adset_id', 'adset_name')
            ->distinct();
        $adsets = $query->get();
    
        return response()->json(['status' => 'success', 'adsets' => $adsets]);
    }

    public function fetchAds(Request $request)
    {
        $ads = FacebookAdAccount::where('adset_id', $request->adset_id)
            ->select('ad_id', 'ad_name')
            ->distinct()
            ->get();
    
        return response()->json(['ads' => $ads]);
    }

    // public function storeOrUpdate(Request $request)
    // {
    //     $validated = $request->validate([
    //         'ad_account_id' => 'required|string',
    //         'campaign_id'   => 'required|string',
    //         'adset_id'      => 'required|string',
    //         'ad_id'         => 'required|string',
    //         'template_id'   => 'required|numeric',
    //         'id'            => 'nullable|exists:automations,id',
    //     ]);
    
    //     try {
    //         if ($request->filled('id')) {
    //             // Update
    //             $automation = Automation::findOrFail($request->id);
    //             $automation->update($validated);
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Automation updated successfully.',
    //                 'data' => $automation
    //             ]);
    //         } else {
    //             // Create
    //             $automation = Automation::create($validated);
    //             return response()->json([
    //                 'status' => 'success',
    //                 'message' => 'Automation created successfully.',
    //                 'data' => $automation
    //             ]);
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Operation failed.',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    public function storeOrUpdate(Request $request)
    {
        $user = auth()->user();
    
        $validated = $request->validate([
            'ad_account_id' => 'required|string',
            'campaign_id'   => 'required|string',
            'adset_id'      => 'required|string',
            'ad_id'         => 'required|string',
            'template_id'   => 'required|numeric',
            'id'            => 'nullable|exists:automations,id',
        ]);
    
        // Add user_id manually
        $validated['user_id'] = $user->id;
    
        try {
            if ($request->filled('id')) {
                // Update
                $automation = Automation::findOrFail($request->id);
                $automation->update($validated);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Automation updated successfully.',
                    'data' => $automation
                ]);
            } else {
                // Create
                $automation = Automation::create($validated);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Automation created successfully.',
                    'data' => $automation
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Operation failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function destroy($id)
    {
        $automation = Automation::findOrFail($id);
        $automation->delete();
    
        return redirect()->back()->with('success', 'Automation deleted successfully.');
    }
    
    public function toggleStatus($id)
    {
        $automation = Automation::findOrFail($id);
        $automation->status = $automation->status === 'paused' ? 'active' : 'paused';
        // echo "<pre>";
        // print_r($automation);die;
        $automation->save();
    
        return redirect()->back()->with('success', 'Automation status updated successfully.');
    }


    public function triggerLeadWhatsApp___working(Request $request)
    {
        if ($request->query('key') !== env('CRON_SECRET_KEY')) {
            Log::warning('Unauthorized access to WhatsApp cron');
            abort(403, 'Unauthorized access');
        }
    
        $leads = FacebookLeads::where('whatsapp_sent_at', 0)->get();
    
        if ($leads->isEmpty()) {
            echo "⚠️ No pending leads found to send WhatsApp.";
            return;
        }
    
        $sentLeads = [];
        $senderId = env('WHATSAPP_SENDER_ID');
        $token = env('WHATSAPP_SYSTEM_USER_TOKEN');
    
        foreach ($leads as $lead) {
            $automation = Automation::where('campaign_id', $lead->campaign_id)
                ->where('adset_id', $lead->adset_id)
                ->where('ad_id', $lead->ad_id)
                ->first();
    
            if (!$automation) {
                echo "❌ No automation for Lead ID: {$lead->lead_id}<br>";
                continue;
            }
    
            $template = Template::find($automation->template_id);
            if (!$template) {
                echo "❌ No template for Automation ID: {$automation->id}<br>";
                continue;
            }
    
            $phone = $lead->phone_number;
            $name = $lead->full_name ?? 'User';
    
            if (!$phone) {
                echo "❌ No phone for Lead ID: {$lead->lead_id}<br>";
                continue;
            }
    
            // Start building components
            $rawComponents = json_decode($template->components, true);
            $components = [];
    
            foreach ($rawComponents as $component) {
                $type = strtolower($component['type']);
                $format = strtolower($component['format'] ?? '');
    
                // HEADER
                if ($type === 'header') {
                    if ($format === 'image' && !empty($component['example']['header_handle'][0])) {
                        $components[] = [
                            'type' => 'header',
                            'parameters' => [[
                                'type' => 'image',
                                'image' => ['link' => $component['example']['header_handle'][0]]
                            ]]
                        ];
                    } elseif ($format === 'video' && !empty($component['example']['header_handle'][0])) {
                        $components[] = [
                            'type' => 'header',
                            'parameters' => [[
                                'type' => 'video',
                                'video' => ['link' => $component['example']['header_handle'][0]]
                            ]]
                        ];
                    } elseif ($format === 'document' && !empty($component['example']['header_handle'][0])) {
                        $components[] = [
                            'type' => 'header',
                            'parameters' => [[
                                'type' => 'document',
                                'document' => ['link' => $component['example']['header_handle'][0]]
                            ]]
                        ];
                    } elseif ($format === 'text' && isset($component['text'])) {
                        if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                            $params = [];
                            foreach ($matches[0] as $i => $placeholder) {
                                $params[] = ['type' => 'text', 'text' => $name];
                            }
                            $components[] = ['type' => 'header', 'parameters' => $params];
                        } else {
                            $components[] = ['type' => 'header']; // No placeholders
                        }
                    }
                }
    
                // BODY
                if ($type === 'body' && isset($component['text'])) {
                    $parameters = [];
                    if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                        foreach ($matches[0] as $i => $ph) {
                            // You can replace $name with dynamic lead info mapping here
                            $parameters[] = ['type' => 'text', 'text' => $name];
                        }
                    }
                    $components[] = ['type' => 'body', 'parameters' => $parameters];
                }
    
                // FOOTER
                if ($type === 'footer') {
                    $components[] = ['type' => 'footer'];
                }
    
                // BUTTONS
                if ($type === 'button' && isset($component['buttons'])) {
                    foreach ($component['buttons'] as $index => $btn) {
                        $subType = strtolower($btn['type']);
    
                        $param = match ($subType) {
                            'url' => ['type' => 'text', 'text' => $btn['url'] ?? $btn['text']],
                            'phone_number' => ['type' => 'payload', 'payload' => $btn['phone_number']],
                            default => ['type' => 'payload', 'payload' => $btn['text']],
                        };
    
                        $components[] = [
                            'type' => 'button',
                            'sub_type' => $subType,
                            'index' => $index,
                            'parameters' => [$param]
                        ];
                    }
                }
            }
    
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'template',
                'template' => [
                    'name' => $template->name,
                    'language' => ['code' => $template->language],
                    'components' => $components
                ]
            ];
    
            Log::info("Sending WhatsApp to {$phone}", $payload);
    
            $response = Http::withToken($token)
                ->post("https://graph.facebook.com/v21.0/{$senderId}/messages", $payload);
    
            if ($response->successful()) {
                $lead->whatsapp_sent_at = 1;
                $lead->save();
                $sentLeads[] = $phone;
                echo "✅ Sent to {$name} ({$phone})<br>";
            } else {
                echo "❌ Failed for {$name} ({$phone}) — " . $response->body() . "<br>";
                Log::error("Failed WhatsApp for {$phone}: " . $response->body());
            }
        }
    
        echo "<br><strong>Total Sent:</strong> " . count($sentLeads);
    }
    
    public function triggerLeadWhatsApp__latest(Request $request)
    {
        if ($request->query('key') !== env('CRON_SECRET_KEY')) {
            Log::warning('Unauthorized access to WhatsApp cron');
            abort(403, 'Unauthorized access');
        }
    
        $leads = FacebookLeads::where('whatsapp_sent_at', 0)->get();
    
        if ($leads->isEmpty()) {
            echo "⚠️ No pending leads found to send WhatsApp.";
            return;
        }
    
        $user = auth()->user();
        if (!$user || !$user->fb_long_lived_token || !$user->whatsapp_sender_id) {
            echo "❌ Missing WhatsApp credentials.<br>";
            return;
        }
    
        $token = $user->fb_long_lived_token;
        $senderId = $user->whatsapp_sender_id;
        $sentLeads = [];
    
        foreach ($leads as $lead) {
            $automations = Automation::where('campaign_id', $lead->campaign_id)
                ->where('adset_id', $lead->adset_id)
                ->where('ad_id', $lead->ad_id)
                ->get();
    
            if ($automations->isEmpty()) {
                echo "❌ No automation found for Lead ID: {$lead->lead_id}<br>";
                continue;
            }
    
            $allSent = true;
    
            foreach ($automations as $automation) {
                $template = Template::find($automation->template_id);
                if (!$template) {
                    echo "❌ Template not found for Automation ID: {$automation->id}<br>";
                    $allSent = false;
                    continue;
                }
    
                $phone = $lead->phone_number;
                $name = $lead->full_name ?? 'User';
    
                if (!$phone) {
                    echo "❌ No phone number for Lead ID: {$lead->lead_id}<br>";
                    $allSent = false;
                    continue;
                }
    
                $rawComponents = json_decode($template->components, true);
                $components = [];
    
                foreach ($rawComponents as $component) {
                    $type = strtolower($component['type']);
                    $format = strtolower($component['format'] ?? '');
    
                    if ($type === 'header') {
                        if (in_array($format, ['image', 'video', 'document']) && !empty($component['example']['header_handle'][0])) {
                            try {
                                $mediaUrl = $component['example']['header_handle'][0];
                                $tempFile = tempnam(sys_get_temp_dir(), 'media_');
                                file_put_contents($tempFile, file_get_contents($mediaUrl));
                                $filename = basename(parse_url($mediaUrl, PHP_URL_PATH));
    
                                $upload = Http::withToken($token)
                                    ->attach('file', fopen($tempFile, 'r'), $filename)
                                    ->post("https://graph.facebook.com/v21.0/{$senderId}/media", [
                                        'messaging_product' => 'whatsapp',
                                        'type' => $format
                                    ]);
    
                                unlink($tempFile);
    
                                if ($upload->failed() || !$upload->json('id')) {
                                    Log::error("❌ Media upload failed for {$name}: " . $upload->body());
                                    echo "❌ Media upload failed for {$name} ({$phone})<br>";
                                    $allSent = false;
                                    continue;
                                }
    
                                $components[] = [
                                    'type' => 'header',
                                    'parameters' => [[
                                        'type' => $format,
                                        $format => ['id' => $upload->json('id')]
                                    ]]
                                ];
                            } catch (\Exception $e) {
                                Log::error("❌ Media upload exception for {$name}: " . $e->getMessage());
                                echo "❌ Media upload error for {$name} ({$phone})<br>";
                                $allSent = false;
                                continue;
                            }
                        } elseif ($format === 'text' && isset($component['text'])) {
                            $params = [];
                            if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                                foreach ($matches[0] as $match) {
                                    $params[] = ['type' => 'text', 'text' => $name];
                                }
                            }
                            $components[] = ['type' => 'header', 'parameters' => $params];
                        }
                    }
    
                    if ($type === 'body' && isset($component['text'])) {
                        $params = [];
                        if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                            foreach ($matches[0] as $match) {
                                $params[] = ['type' => 'text', 'text' => $name];
                            }
                        }
                        $components[] = ['type' => 'body', 'parameters' => $params];
                    }
    
                    if ($type === 'footer') {
                        $components[] = ['type' => 'footer'];
                    }
    
                    if ($type === 'buttons' && isset($component['buttons'])) {
                        foreach ($component['buttons'] as $index => $btn) {
                            $subType = strtolower($btn['type']);
                            $url = $btn['url'] ?? '';
                            $urlVariable = $btn['url_variable'] ?? '';
                            $isDynamic = preg_match('/{{\d+}}/', $url);
    
                            if ($subType === 'quick_reply') {
                                $components[] = [
                                    'type' => 'button',
                                    'sub_type' => 'quick_reply',
                                    'index' => (string)$index,
                                    'parameters' => [[
                                        'type' => 'payload',
                                        'payload' => $btn['text'] ?? 'Reply'
                                    ]]
                                ];
                            } elseif ($subType === 'phone_number') {
                                $components[] = [
                                    'type' => 'button',
                                    'sub_type' => 'phone_number',
                                    'index' => (string)$index,
                                    'parameters' => [[
                                        'type' => 'payload',
                                        'payload' => $btn['phone_number'] ?? ''
                                    ]]
                                ];
                            } elseif ($subType === 'url') {
                                if ($isDynamic) {
                                    $components[] = [
                                        'type' => 'button',
                                        'sub_type' => 'url',
                                        'index' => (string)$index,
                                        'parameters' => [[
                                            'type' => 'text',
                                            'text' => $urlVariable ?: 'value'
                                        ]]
                                    ];
                                } else {
                                    if (!$isDynamic && isset($buttonPayload['parameters'])) {
                                        unset($buttonPayload['parameters']);
                                         $components[] = [
                                        'type' => 'button',
                                        'sub_type' => 'url',
                                        'index' => (string)$index // Index required by Meta even for static
                                    ];
                                    }
                                   
                                }
                            }
                        }
                    }
                }
    
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'template',
                    'template' => [
                        'name' => $template->name,
                        'language' => ['code' => $template->language],
                        'components' => $components
                    ]
                ];
    
                Log::info("🚀 WhatsApp Payload for {$phone}", ['payload' => $payload]);
    
                $response = Http::withToken($token)
                    ->post("https://graph.facebook.com/v21.0/{$senderId}/messages", $payload);
    
                if ($response->successful()) {
                    $json = $response->json();
                    $messageId = $json['messages'][0]['id'] ?? null;
    
                    if ($messageId) {
                        echo "✅ Sent to {$name} ({$phone}) with Template: {$template->name}<br>";
                        Log::info("✅ Sent WhatsApp to {$phone}", $json);
                        $sentLeads[] = $phone;
                    } else {
                        echo "⚠️ Sent request accepted but no message ID returned for {$name} ({$phone})<br>";
                        Log::warning("⚠️ No message ID returned for {$phone}", $json);
                        $allSent = false;
                    }
                } else {
                    echo "❌ Failed for {$name} ({$phone}) — " . $response->body() . "<br>";
                    Log::error("❌ WhatsApp send failed for {$phone}: " . $response->body());
                    $allSent = false;
                }
            }
    
            if ($allSent) {
                $lead->whatsapp_sent_at = 1;
                $lead->save();
            }
        }
    
        echo "<br><strong>✅ Total Sent:</strong> " . count(array_unique($sentLeads));
    }
    
    public function triggerLeadWhatsApp(Request $request)
    {
        if ($request->query('key') !== env('CRON_SECRET_KEY')) {
            Log::warning('Unauthorized access to WhatsApp cron');
            abort(403, 'Unauthorized access');
        }
    
        $leads = FacebookLeads::where('whatsapp_sent_at', 0)->get();
        
        if ($leads->isEmpty()) {
            echo "No pending leads found to send WhatsApp.";
            return;
        }
    
        $sentLeads = [];
    
        foreach ($leads as $lead) {
            $user = User::find($lead->user_id);
    
            if (!$user || !$user->fb_long_lived_token || !$user->whatsapp_sender_id) {
                echo "Missing WhatsApp credentials for User ID: {$lead->user_id}<br>";
                continue;
            }
    
            $token = $user->fb_long_lived_token;
            $senderId = $user->whatsapp_sender_id;
    
            $automations = Automation::where('campaign_id', $lead->campaign_id)
                ->where('adset_id', $lead->adset_id)
                ->where('ad_id', $lead->ad_id)
                ->where('status', 'active')
                ->get();
    
            if ($automations->isEmpty()) {
                echo "No automation found for Lead ID: {$lead->lead_id}<br>";
                continue;
            }
    
            $allSent = true;
    
            foreach ($automations as $automation) {
                $template = Template::find($automation->template_id);
                if (!$template) {
                    echo "Template not found for Automation ID: {$automation->id}<br>";
                    $allSent = false;
                    continue;
                }
    
                $phone = $lead->phone_number;
                $name = $lead->full_name ?? 'User';
    
                if (!$phone) {
                    echo "No phone number for Lead ID: {$lead->lead_id}<br>";
                    $allSent = false;
                    continue;
                }
    
                $rawComponents = json_decode($template->components, true);
                $components = [];
    
                foreach ($rawComponents as $component) {
                    $type = strtolower($component['type']);
                    $format = strtolower($component['format'] ?? '');
    
                    // HEADER
                    if ($type === 'header') {
                        if (in_array($format, ['image', 'video', 'document']) && !empty($component['example']['header_handle'][0])) {
                            try {
                                $mediaUrl = $component['example']['header_handle'][0];
                                $tempFile = tempnam(sys_get_temp_dir(), 'media_');
                                file_put_contents($tempFile, file_get_contents($mediaUrl));
                                $filename = basename(parse_url($mediaUrl, PHP_URL_PATH));
    
                                $upload = Http::withToken($token)
                                    ->attach('file', fopen($tempFile, 'r'), $filename)
                                    ->post("https://graph.facebook.com/v21.0/{$senderId}/media", [
                                        'messaging_product' => 'whatsapp',
                                        'type' => $format
                                    ]);
    
                                unlink($tempFile);
    
                                if ($upload->failed() || !$upload->json('id')) {
                                    Log::error("Media upload failed for {$name}: " . $upload->body());
                                    echo "Media upload failed for {$name} ({$phone})<br>";
                                    $allSent = false;
                                    continue;
                                }
    
                                $components[] = [
                                    'type' => 'header',
                                    'parameters' => [[
                                        'type' => $format,
                                        $format => ['id' => $upload->json('id')]
                                    ]]
                                ];
                            } catch (\Exception $e) {
                                Log::error("Media upload exception for {$name}: " . $e->getMessage());
                                echo "Media upload error for {$name} ({$phone})<br>";
                                $allSent = false;
                                continue;
                            }
                        } elseif ($format === 'text' && isset($component['text'])) {
                            $params = [];
                            if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                                foreach ($matches[0] as $match) {
                                    $params[] = ['type' => 'text', 'text' => $name];
                                }
                            }
                            $components[] = ['type' => 'header', 'parameters' => $params];
                        }
                    }
    
                    // BODY
                    if ($type === 'body' && isset($component['text'])) {
                        $params = [];
                        if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
                            foreach ($matches[0] as $match) {
                                $params[] = ['type' => 'text', 'text' => $name];
                            }
                        }
                        $components[] = ['type' => 'body', 'parameters' => $params];
                    }
    
                    // FOOTER
                    if ($type === 'footer') {
                        $components[] = ['type' => 'footer'];
                    }
    
                    // BUTTONS
                    if ($type === 'buttons' && isset($component['buttons'])) {
                        foreach ($component['buttons'] as $index => $btn) {
                            $subType = strtolower($btn['type']);
                            $url = $btn['url'] ?? '';
                            $urlVariable = $btn['url_variable'] ?? '';
                            $isDynamic = preg_match('/{{\d+}}/', $url);
    
                            if ($subType === 'quick_reply') {
                                $components[] = [
                                    'type' => 'button',
                                    'sub_type' => 'quick_reply',
                                    'index' => (string)$index,
                                    'parameters' => [[
                                        'type' => 'payload',
                                        'payload' => $btn['text'] ?? 'Reply'
                                    ]]
                                ];
                            }  elseif ($subType === 'url') {
                                if ($isDynamic) {
                                    $components[] = [
                                        'type' => 'button',
                                        'sub_type' => 'url',
                                        'index' => (string)$index,
                                        'parameters' => [[
                                            'type' => 'text',
                                            'text' => $urlVariable ?: 'value'
                                        ]]
                                    ];
                                } else {
                                    if (!$isDynamic && isset($buttonPayload['parameters'])) {
                                        unset($buttonPayload['parameters']);
                                         $components[] = [
                                        'type' => 'button',
                                        'sub_type' => 'url',
                                        'index' => (string)$index 
                                    ];
                                    }
                                   
                                }
                            }   elseif ($subType === 'phone_number' || $subType === 'voice_call') {
                                $components[] = [
                                    'type' => 'button',
                                    'sub_type' => 'voice_call',
                                    'index' => (string)$index,
                                    'parameters' => [[
                                        'type' => 'payload',
                                        'payload' => $btn['phone_number'] ?? ''
                                    ]]
                                ];
                            }
                        }
                    }
                }
    
                $payload = [
                    'messaging_product' => 'whatsapp',
                    'to' => $phone,
                    'type' => 'template',
                    'template' => [
                        'name' => $template->name,
                        'language' => ['code' => $template->language],
                        'components' => $components
                    ]
                ];
    
                Log::info("WhatsApp Payload for {$phone}", ['payload' => $payload]);
    
                $response = Http::withToken($token)
                    ->post("https://graph.facebook.com/v21.0/{$senderId}/messages", $payload);
    
                if ($response->successful()) {
                    $json = $response->json();
                    $messageId = $json['messages'][0]['id'] ?? null;
    
                    if ($messageId) {
                        echo "Sent to {$name} ({$phone}) with Template: {$template->name}<br>";
                        Log::info(" Sent WhatsApp to {$phone}", $json);
                        $sentLeads[] = $phone;
                    } else {
                        echo "Sent request accepted but no message ID returned for {$name} ({$phone})<br>";
                        Log::warning("️ No message ID returned for {$phone}", $json);
                        $allSent = false;
                    }
                } else {
                    echo "Failed for {$name} ({$phone}) — " . $response->body() . "<br>";
                    Log::error(" WhatsApp send failed for {$phone}: " . $response->body());
                    $allSent = false;
                }
            }
    
            if ($allSent) {
                $lead->whatsapp_sent_at = 1;
                $lead->save();
            }
        }
    
        echo "<br><strong>Total Sent:</strong> " . count(array_unique($sentLeads));
    }

    
    public function fireWhatsAppTrigger()
    {
        broadcast(new WhatsAppTriggerRequested(env('CRON_SECRET_KEY')));
        return response('📨 WhatsApp trigger event broadcasted via Pusher.');
    }


    
    
    // public function triggerLeadWhatsApp(Request $request)
    // {
    //     // Validate key for GET route
    //     if ($request->isMethod('get') && $request->query('key') !== env('CRON_SECRET_KEY')) {
    //         Log::warning('Unauthorized GET WhatsApp trigger attempt');
    //         abort(403, 'Unauthorized access');
    //     }
    
    //     // Validate webhook secret for POST route
    //     if ($request->isMethod('post') && $request->input('webhook_secret') !== env('WEBHOOK_SECRET')) {
    //         Log::warning('Unauthorized POST WhatsApp trigger attempt');
    //         abort(403, 'Unauthorized webhook');
    //     }
    
    //     $leads = FacebookLeads::where('whatsapp_sent_at', 0)->get();
    
    //     if ($leads->isEmpty()) {
    //         echo "No pending leads found to send WhatsApp.";
    //         return;
    //     }
    
    //     $sentLeads = [];
    
    //     foreach ($leads as $lead) {
    //         $user = User::find($lead->user_id);
    //         if (!$user || !$user->fb_long_lived_token || !$user->whatsapp_sender_id) {
    //             echo "Missing WhatsApp credentials for User ID: {$lead->user_id}<br>";
    //             continue;
    //         }
    
    //         $token = $user->fb_long_lived_token;
    //         $senderId = $user->whatsapp_sender_id;
    
    //         $automations = Automation::where([
    //             ['campaign_id', $lead->campaign_id],
    //             ['adset_id', $lead->adset_id],
    //             ['ad_id', $lead->ad_id],
    //             ['status', 'active']
    //         ])->get();
    
    //         if ($automations->isEmpty()) {
    //             echo "No automation found for Lead ID: {$lead->lead_id}<br>";
    //             continue;
    //         }
    
    //         $allSent = true;
    
    //         foreach ($automations as $automation) {
    //             $template = Template::find($automation->template_id);
    //             if (!$template) {
    //                 echo "Template not found for Automation ID: {$automation->id}<br>";
    //                 $allSent = false;
    //                 continue;
    //             }
    
    //             $phone = $lead->phone_number;
    //             $name = $lead->full_name ?? 'User';
    
    //             if (!$phone) {
    //                 echo "No phone number for Lead ID: {$lead->lead_id}<br>";
    //                 $allSent = false;
    //                 continue;
    //             }
    
    //             $components = $this->buildTemplateComponents($template, $token, $name, $phone, $senderId, $allSent);
    
    //             $payload = [
    //                 'messaging_product' => 'whatsapp',
    //                 'to' => $phone,
    //                 'type' => 'template',
    //                 'template' => [
    //                     'name' => $template->name,
    //                     'language' => ['code' => $template->language],
    //                     'components' => $components
    //                 ]
    //             ];
    
    //             Log::info("WhatsApp Payload for {$phone}", ['payload' => $payload]);
    
    //             $response = Http::withToken($token)
    //                 ->post("https://graph.facebook.com/v21.0/{$senderId}/messages", $payload);
    
    //             if ($response->successful()) {
    //                 $json = $response->json();
    //                 $messageId = $json['messages'][0]['id'] ?? null;
    
    //                 if ($messageId) {
    //                     echo "Sent to {$name} ({$phone}) with Template: {$template->name}<br>";
    //                     Log::info("Sent WhatsApp to {$phone}", $json);
    //                     $sentLeads[] = $phone;
    //                 } else {
    //                     echo "Message ID not returned for {$name} ({$phone})<br>";
    //                     $allSent = false;
    //                 }
    //             } else {
    //                 echo "Failed for {$name} ({$phone}): " . $response->body() . "<br>";
    //                 $allSent = false;
    //             }
    //         }
    
    //         if ($allSent) {
    //             $lead->whatsapp_sent_at = 1;
    //             $lead->save();
    //         }
    //     }
    
    //     echo "<br><strong>Total Sent:</strong> " . count(array_unique($sentLeads));
    // }
    
    // private function buildTemplateComponents($template, $token, $name, $phone, $senderId, &$allSent)
    // {
    //     $rawComponents = json_decode($template->components, true);
    //     $components = [];
    
    //     foreach ($rawComponents as $component) {
    //         $type = strtolower($component['type']);
    //         $format = strtolower($component['format'] ?? '');
    
    //         // HEADER
    //         if ($type === 'header') {
    //             if (in_array($format, ['image', 'video', 'document']) && !empty($component['example']['header_handle'][0])) {
    //                 try {
    //                     $mediaUrl = $component['example']['header_handle'][0];
    //                     $tempFile = tempnam(sys_get_temp_dir(), 'media_');
    //                     file_put_contents($tempFile, file_get_contents($mediaUrl));
    //                     $filename = basename(parse_url($mediaUrl, PHP_URL_PATH));
    
    //                     $upload = Http::withToken($token)
    //                         ->attach('file', fopen($tempFile, 'r'), $filename)
    //                         ->post("https://graph.facebook.com/v21.0/{$senderId}/media", [
    //                             'messaging_product' => 'whatsapp',
    //                             'type' => $format
    //                         ]);
    
    //                     unlink($tempFile);
    
    //                     if ($upload->failed() || !$upload->json('id')) {
    //                         Log::error("Media upload failed for {$name}");
    //                         $allSent = false;
    //                         continue;
    //                     }
    
    //                     $components[] = [
    //                         'type' => 'header',
    //                         'parameters' => [[
    //                             'type' => $format,
    //                             $format => ['id' => $upload->json('id')]
    //                         ]]
    //                     ];
    //                 } catch (\Exception $e) {
    //                     Log::error("Header media upload error for {$name}: " . $e->getMessage());
    //                     $allSent = false;
    //                     continue;
    //                 }
    //             } elseif ($format === 'text' && isset($component['text'])) {
    //                 $params = [];
    //                 if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
    //                     foreach ($matches[0] as $match) {
    //                         $params[] = ['type' => 'text', 'text' => $name];
    //                     }
    //                 }
    //                 $components[] = ['type' => 'header', 'parameters' => $params];
    //             }
    //         }
    
    //         // BODY
    //         if ($type === 'body' && isset($component['text'])) {
    //             $params = [];
    //             if (preg_match_all('/{{\d+}}/', $component['text'], $matches)) {
    //                 foreach ($matches[0] as $match) {
    //                     $params[] = ['type' => 'text', 'text' => $name];
    //                 }
    //             }
    //             $components[] = ['type' => 'body', 'parameters' => $params];
    //         }
    
    //         // FOOTER
    //         if ($type === 'footer') {
    //             $components[] = ['type' => 'footer'];
    //         }
    
    //         // BUTTONS
    //          if ($type === 'buttons' && isset($component['buttons'])) {
    //             foreach ($component['buttons'] as $index => $btn) {
    //                 $subType = strtolower($btn['type']);
    //                 $url = $btn['url'] ?? '';
    //                 $urlVariable = $btn['url_variable'] ?? '';
    //                 $isDynamic = preg_match('/{{\d+}}/', $url);
    
    //                 if ($subType === 'quick_reply') {
    //                     $components[] = [
    //                         'type' => 'button',
    //                         'sub_type' => 'quick_reply',
    //                         'index' => (string)$index,
    //                         'parameters' => [[
    //                             'type' => 'payload',
    //                             'payload' => $btn['text'] ?? 'Reply'
    //                         ]]
    //                     ];
    //                 }  elseif ($subType === 'url') {
    //                     if ($isDynamic) {
    //                         $components[] = [
    //                             'type' => 'button',
    //                             'sub_type' => 'url',
    //                             'index' => (string)$index,
    //                             'parameters' => [[
    //                                 'type' => 'text',
    //                                 'text' => $urlVariable ?: 'value'
    //                             ]]
    //                         ];
    //                     } else {
    //                         if (!$isDynamic && isset($buttonPayload['parameters'])) {
    //                             unset($buttonPayload['parameters']);
    //                                 $components[] = [
    //                             'type' => 'button',
    //                             'sub_type' => 'url',
    //                             'index' => (string)$index 
    //                         ];
    //                         }
                            
    //                     }
    //                 }   elseif ($subType === 'phone_number' || $subType === 'voice_call') {
    //                     $components[] = [
    //                         'type' => 'button',
    //                         'sub_type' => 'voice_call',
    //                         'index' => (string)$index,
    //                         'parameters' => [[
    //                             'type' => 'payload',
    //                             'payload' => $btn['phone_number'] ?? ''
    //                         ]]
    //                     ];
    //                 }
    //             }
    //         }
    //     }
    
    //     return $components;
    // }
    
}