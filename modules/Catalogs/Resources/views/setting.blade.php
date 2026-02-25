@extends('general.index', ['title' => __('Catalog Settings')])

@section('content')
    <div class="header bg-gradient-primary pb-6 pt-5 pt-md-6">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('catalog.index') }}">{{ __('Catalog') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Settings') }}</li>
                        </ol>
                    </nav>
                    <h1 class="h2 text-white mb-0 mt-2">{{ __('Catalog Settings') }}</h1>
                    <p class="text-white opacity-8 mb-0">{{ __('Payment, storefront, messages & shipping') }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt--6">
        @include('partials.flash')
    <style>
        .image-preview-container { position: relative; display: inline-block; }
        .image-preview-container .btn-remove { position: absolute; top: -8px; right: -8px; border-radius: 50%; padding: 4px 8px; font-size: 12px; }
        .variable-list { background-color: #f8f9fa; border-radius: 0.5rem; padding: 1rem; margin-bottom: 1rem; }
        .variable-item { display: inline-block; margin: 2px 6px 2px 0; background: #e9ecef; padding: 4px 10px; border-radius: 4px; font-size: 12px; cursor: pointer; }
        .variable-item:hover { background: #dee2e6; }
        .settings-card { border: 0; box-shadow: 0 0 2rem 0 rgba(136, 152, 170, 0.15); border-radius: 0.5rem; }
        /* Stepper */
        .catalog-stepper-wrap { max-width: 100%; }
        .catalog-stepper-steps { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 0.5rem; padding: 1rem 0; margin-bottom: 1.5rem; border-bottom: 2px solid #e9ecef; }
        .catalog-stepper-step { display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #6c757d; font-size: 0.85rem; padding: 0.35rem 0.5rem; border-radius: 0.375rem; transition: all 0.2s; }
        .catalog-stepper-step:hover { color: #5e72e4; background: rgba(94, 114, 228, 0.08); }
        .catalog-stepper-step.active { color: #5e72e4; background: rgba(94, 114, 228, 0.12); font-weight: 600; }
        .catalog-stepper-step .step-num { display: inline-flex; align-items: center; justify-content: center; width: 1.75rem; height: 1.75rem; border-radius: 50%; background: #e9ecef; color: #6c757d; font-size: 0.8rem; margin-right: 0.5rem; }
        .catalog-stepper-step.active .step-num { background: linear-gradient(87deg, #5e72e4 0, #825ee4 100%); color: #fff; }
        .catalog-step-pane { display: none; min-height: 1px; }
        .catalog-step-pane.active { display: block; animation: catalogFadeIn 0.25s ease; }
        @keyframes catalogFadeIn { from { opacity: 0; } to { opacity: 1; } }
        .catalog-stepper-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid #e9ecef; }
        .catalog-stepper-title { font-size: 1.1rem; font-weight: 600; color: #32325d; margin-bottom: 0.25rem; }
        .catalog-stepper-subtitle { font-size: 0.875rem; color: #8898aa; }
        #catalog-step-content { min-height: 320px; overflow: visible; }
        .catalog-step-pane .form-control, .catalog-step-pane textarea, .catalog-step-pane select { max-width: 100%; }
        .catalog-step-pane form { display: block !important; visibility: visible !important; }
        .catalog-step-form-wrap { display: block !important; visibility: visible !important; width: 100%; }
        @media (max-width: 992px) { .catalog-stepper-step .step-label { display: none; } .catalog-stepper-step .step-num { margin-right: 0; } }
    </style>
        <div class="catalog-stepper-wrap">
            <!-- Stepper indicator -->
            <div class="card settings-card mb-4">
                <div class="card-body py-3">
                    <div class="catalog-stepper-steps" id="catalog-stepper-steps" role="navigation" aria-label="{{ __('Settings steps') }}">
                        <div class="catalog-stepper-step active" data-step="1" title="{{ __('Sync Catalog') }}"><span class="step-num">1</span><span class="step-label">{{ __('Sync Catalog') }}</span></div>
                        <div class="catalog-stepper-step" data-step="2"><span class="step-num">2</span><span class="step-label">{{ __('StoreFront') }}</span></div>
                        <div class="catalog-stepper-step" data-step="3"><span class="step-num">3</span><span class="step-label">{{ __('Address & Messages') }}</span></div>
                        <div class="catalog-stepper-step" data-step="4"><span class="step-num">4</span><span class="step-label">{{ __('Message Templates') }}</span></div>
                        <div class="catalog-stepper-step" data-step="5"><span class="step-num">5</span><span class="step-label">{{ __('Payment Method') }}</span></div>
                        <div class="catalog-stepper-step" data-step="6"><span class="step-num">6</span><span class="step-label">{{ __('Shipping & Discount') }}</span></div>
                        <div class="catalog-stepper-step" data-step="7"><span class="step-num">7</span><span class="step-label">{{ __('Catalog Options') }}</span></div>
                    </div>
                </div>
            </div>

            <!-- Step content (only one visible at a time) -->
            <div class="card settings-card">
                <div class="card-body">
                    <div id="catalog-step-header" class="mb-4">
                        <h3 class="catalog-stepper-title mb-0" id="catalog-step-title">{{ __('Sync Catalog') }}</h3>
                        <p class="catalog-stepper-subtitle mb-0" id="catalog-step-subtitle">{{ __('Sync your product catalog with WhatsApp.') }}</p>
                    </div>
                    <div id="catalog-step-content">
                    <div class="catalog-step-pane active" id="step-1" data-step="1" role="tabpanel">
                        <div class="d-flex align-items-center justify-content-between flex-wrap mb-4">
                            <span></span>
                            <button id="syncCatalogBtn" class="btn btn-primary btn-sm">
                                <i class="ni ni-refresh mr-1"></i> {{ __('Sync Now') }}
                            </button>
                        </div>
                        <div class="pt-0">
                                @if (Session::has('message'))
                                    <div class="alert alert-danger d-flex align-items-center p-4 mb-4">
                                        <i class="ni ni-notification-70 mr-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            <h5 class="mb-1">{{ __('Notice') }}</h5>
                                            <span>{{ Session::get('message') }}</span>
                                        </div>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <thead class="thead-light">
                                            <tr>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Catalogue Id') }}</th>
                                                <th scope="col">{{ __('Product Count') }}</th>
                                                <th scope="col">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($catalogs as $catalog)
                                                @php
                                                    $products_count = App\Models\CatalogProduct::where('catalog_id', $catalog->catalog_id)->where('company_id', $catalog->company_id)->get();
                                                @endphp
                                                <tr>
                                                    <td><span class="font-weight-bold text-dark">{{ $catalog->name }}</span></td>
                                                    <td>{{ $catalog->catalog_id }}</td>
                                                    <td><span class="badge badge-primary">{{ count($products_count) }}</span></td>
                                                    <td>
                                                        @if (!$catalog->status)
                                                            <span class="badge badge-danger">{{ __('Disabled') }}</span>
                                                        @else
                                                            <span class="badge badge-success">{{ __('Enabled') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>

                    <div class="catalog-step-pane" id="step-2" data-step="2" role="tabpanel">
                        <form id="storefront-form" method="post" autocomplete="off" enctype="multipart/form-data" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div class="mb-4">
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Business Logo') }}</label>
                                        <div class="d-flex align-items-center flex-wrap">
                                            @if ($Paymenttemplate && $Paymenttemplate->logo)
                                                <div class="image-preview-container mr-4 mb-3">
                                                    <img src="{{ asset('uploads/storefront/' . $Paymenttemplate->logo) }}" class="img-fluid rounded shadow-sm" alt="Logo" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove" data-target="logo"><i class="ni ni-fat-remove"></i></button>
                                                    <input type="hidden" name="remove_logo" value="0" id="remove_logo">
                                                </div>
                                            @endif
                                            <div class="flex-grow-1">
                                                <input type="file" name="logo" class="form-control" accept="image/*">
                                                <small class="form-text text-muted">{{ __('Recommended: 300x300 px') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Business Name') }}</label>
                                        <input type="text" name="business_name" class="form-control" value="{{ $Paymenttemplate->business_name ?? '' }}" placeholder="{{ __('Enter business name') }}">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Business Address') }}</label>
                                        <textarea rows="3" name="business_address" class="form-control" placeholder="{{ __('Enter business address') }}">{{ $Paymenttemplate->business_address ?? '' }}</textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label font-weight-bold text-dark">{{ __('Business Phone') }}</label>
                                            <input type="tel" name="business_phone" class="form-control" value="{{ $Paymenttemplate->business_phone ?? '' }}" placeholder="+1234567890">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label font-weight-bold text-dark">{{ __('Business WhatsApp') }}</label>
                                            <input type="tel" name="business_whatsapp" class="form-control" value="{{ $Paymenttemplate->business_whatsapp ?? '' }}" placeholder="+1234567890">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Business Email') }}</label>
                                        <input type="email" name="business_email" class="form-control" value="{{ $Paymenttemplate->business_email ?? '' }}" placeholder="contact@business.com">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('GSTIN/VAT') }}</label>
                                        <input type="text" name="gstin_vat" class="form-control" value="{{ $Paymenttemplate->gstin_vat ?? '' }}" placeholder="GSTIN1234567XYZ">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label font-weight-bold text-dark">{{ __('Currency Code') }}</label>
                                            <input type="text" name="currency_code" class="form-control" value="{{ $Paymenttemplate->currency_code ?? 'INR' }}" placeholder="INR">
                                        </div>
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label font-weight-bold text-dark">{{ __('Currency Symbol') }}</label>
                                            <input type="text" name="currency_symbol" class="form-control" value="{{ $Paymenttemplate->currency_symbol ?? '₹' }}" placeholder="₹">
                                        </div>
                                    </div>
                                    <div class="pt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="indicator-label">{{ __('Save Settings') }}</span>
                                            <span class="indicator-progress d-none">{{ __('Please wait...') }} <span class="spinner-border spinner-border-sm align-middle ml-2"></span></span>
                                        </button>
                                    </div>
                            </form>
                    </div>

                    <div class="catalog-step-pane" id="step-3" data-step="3" role="tabpanel">
                        <div class="catalog-step-form-wrap">
                            <form id="address-messages-form" method="post" autocomplete="off" enctype="multipart/form-data" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div>
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-4">
                                        <div>
                                            <h3 class="font-weight-bold text-dark mb-1">{{ __('Address Message') }}</h3>
                                            <p class="text-muted small mb-0">{{ __('Message shown when requesting delivery address.') }}</p>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="addressToggle" name="address_message_enable" {{ $Paymenttemplate && $Paymenttemplate->address_message_enable ? 'checked' : '' }}>
                                            <label class="form-check-label" for="addressToggle">{{ __('Enable') }}</label>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Message') }}</label>
                                        <textarea rows="4" name="address_mess" id="address_mess" class="form-control" placeholder="{{ __('Address message') }}">{{ $Paymenttemplate->address_mess ?? "🚚 How would you like to receive your order?\n\nPlease choose one of the options below to proceed:" }}</textarea>
                                    </div>
                                    <hr class="my-4">
                                    <h4 class="font-weight-bold text-dark mb-3">{{ __('Order Dispatch Message') }}</h4>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Message') }}</label>
                                        <textarea rows="4" name="order_message" id="order_message" class="form-control" placeholder="{{ __('Order dispatch message') }}">{{ $Paymenttemplate ? $Paymenttemplate->order_message : '' }}</textarea>
                                    </div>
                                    <hr class="my-4">
                                    <h4 class="font-weight-bold text-dark mb-2">{{ __('Catalog Message') }}</h4>
                                    <p class="text-muted small mb-3">{{ __('Product selected here will be used for catalog message (description + image).') }}</p>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Select Product') }}</label>
                                        <select name="product_id" id="product_id" class="form-control" data-placeholder="{{ __('Select a product') }}">
                                            <option value="">{{ __('Select Product') }}</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->retailer_id }}" {{ $Paymenttemplate && $Paymenttemplate->product_id == $product->retailer_id ? 'selected' : '' }}>{{ $product->product_name ?? $product->name ?? $product->retailer_id }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Catalog trigger keywords') }}</label>
                                        <input type="text" name="catalog_trigger_keywords" class="form-control"
                                               placeholder="{{ __('e.g. shop, menu, catalog') }}"
                                               value="{{ $Paymenttemplate->catalog_trigger_keywords ?? 'shop, catalog, menu' }}">
                                        <small class="text-muted d-block">{{ __('When a customer sends any of these words, the catalog will be sent automatically.') }}</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">{{ __('Update Settings') }}</span>
                                        <span class="indicator-progress d-none">{{ __('Please wait...') }} <span class="spinner-border spinner-border-sm align-middle ml-2"></span></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="catalog-step-pane" id="step-4" data-step="4" role="tabpanel">
                        <div class="catalog-step-form-wrap">
                            <form id="templates-form" method="post" autocomplete="off" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div>
                                    <div class="variable-list">
                                        <div class="font-weight-bold mb-2">{{ __('Available Variables') }}:</div>
                                        @php
                                            $variables = [
                                                'name',
                                                'order_number',
                                                'order_id',
                                                'phone',
                                                'for_person',
                                                'for_person_number',
                                                'final_amount',
                                                'shipping_amount',
                                                'discount',
                                                'items',
                                                'tracking_id',
                                                'currency_symbol',
                                            ];
                                        @endphp
                                        @foreach ($variables as $var)
                                            @php $varTag = "{{ $var }}"; @endphp
                                            <span class="variable-item"
                                                data-variable="{{ $varTag }}">{{ $varTag }}</span>
                                        @endforeach
                                    </div>

                                    @php
                                        // Define all default templates in PHP variables
                                        $defaultOrderAccepted =
                                            "✅ Order Accepted!\n\nHello {{ name }}, your order #{{ order_number }} has been accepted and is being processed.\n\nThank you for your purchase! 😊";
                                        $defaultOrderDispatched =
                                            "🚚 Order Dispatched!\n\nHello {{ name }}, your order #{{ order_number }} has been dispatched.\n\nTracking ID: {{ tracking_id }}\n\nEstimated delivery: 2-3 business days.";
                                        $defaultOrderPrepared =
                                            "🎉 Order Prepared!\n\nHello {{ name }}, your order #{{ order_number }} is ready for pickup/delivery.\n\nItems: {{ items }}\n\nTotal: {{ currency_symbol }}{{ final_amount }}";
                                        $defaultOrderDelivered =
                                            "📦 Order Delivered!\n\nHello {{ name }}, your order #{{ order_number }} has been successfully delivered.\n\nWe hope you enjoy your purchase! 😊\n\nPlease rate your experience: [Link to review]";
                                        $defaultReviewTemplate =
                                            "🌟 How Was Your Experience?\n\nHello {{ name }}, thank you for your order #{{ order_number }}!\n\nWe'd love to hear about your experience. Please take a moment to leave a review:\n[Link to review]\n\nYour feedback helps us improve! 😊";
                                        $defaultPaymentReceived =
                                            "💳 Payment Received!\n\nHello {{ name }}, we've received your payment of {{ currency_symbol }}{{ final_amount }} for order #{{ order_number }}.\n\nThank you for your payment! Your order is now being processed.";
                                        $defaultPaymentFailed =
                                            "❌ Payment Failed!\n\nHello {{ name }}, we encountered an issue processing your payment for order #{{ order_number }}.\n\nPlease try again or use a different payment method:\n[Payment Link]\n\nContact support if you need assistance.";
                                        $defaultPaymentRefunded =
                                            "↩️ Refund Processed!\n\nHello {{ name }}, your refund for order #{{ order_number }} has been processed.\n\nAmount: {{ currency_symbol }}{{ final_amount }} has been credited back to your account.\n\nIf you have any questions, contact our support team.";
                                        $defaultOrderCancel =
                                            "❌ Order Cancelled!\n\nHello {{ name }}, your order #{{ order_number }} has been cancelled as per your request.\n\nIf this was a mistake or you need assistance, please contact us.";
                                    @endphp

                                    <div class="mb-4">
                                        <h4 class="font-weight-bold text-dark mb-2">{{ __('Default template (24h inactive)') }}</h4>
                                        <p class="text-muted small mb-2">{{ __('Utility template sent when chat has been inactive 24 hours.') }}</p>
                                        <select name="default_template_id" class="form-control" data-placeholder="{{ __('Select a template') }}">
                                            <option value="">{{ __('No template selected') }}</option>
                                            @foreach ($utilityTemplates as $template)
                                                <option value="{{ $template->id }}" {{ isset($Paymenttemplate->default_template_id) && $Paymenttemplate->default_template_id == $template->id ? 'selected' : '' }}>{{ $template->name }} ({{ $template->language }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <h4 class="font-weight-bold text-dark mb-3">{{ __('Order Status Templates') }}</h4>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label class="form-label font-weight-bold text-dark">{{ __('Order Accepted') }}</label>
                                                <textarea rows="4" name="order_accepted" class="form-control template-field"
                                                    placeholder="Order accepted message">
@if (empty($Paymenttemplate->order_accepted))
{{ $defaultOrderAccepted }}@else{{ $Paymenttemplate->order_accepted }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Dispatched</label>
                                                <textarea rows="4" name="order_dispatched" class="form-control template-field"
                                                    placeholder="Order dispatched message">
@if (empty($Paymenttemplate->order_dispatched))
{{ $defaultOrderDispatched }}@else{{ $Paymenttemplate->order_dispatched }}
@endif
</textarea>
                                            </div>
                                        </div>

                                        <div class="row g-5 mb-8">
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Prepared</label>
                                                <textarea rows="4" name="order_prepared" class="form-control template-field"
                                                    placeholder="Order prepared message">
@if (empty($Paymenttemplate->order_prepared))
{{ $defaultOrderPrepared }}@else{{ $Paymenttemplate->order_prepared }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Delivered</label>
                                                <textarea rows="4" name="order_delivered" class="form-control template-field"
                                                    placeholder="Order delivered message">
@if (empty($Paymenttemplate->order_delivered))
{{ $defaultOrderDelivered }}@else{{ $Paymenttemplate->order_delivered }}
@endif
</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Review Template -->
                                    <div class="mb-10">
                                        <h4 class="fw-bold text-gray-700 mb-4">Review Template</h4>
                                        <textarea rows="4" name="review_template" class="form-control template-field"
                                            placeholder="Review request message">
@if (empty($Paymenttemplate->review_template))
{{ $defaultReviewTemplate }}@else{{ $Paymenttemplate->review_template }}
@endif
</textarea>
                                    </div>

                                    <!-- Payment Templates -->
                                    <div class="mb-10">
                                        <h4 class="fw-bold text-gray-700 mb-4">Payment Templates</h4>

                                        <div class="row g-5 mb-8">
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Payment
                                                    Received</label>
                                                <textarea rows="4" name="payment_received" class="form-control template-field"
                                                    placeholder="Payment received message">
@if (empty($Paymenttemplate->payment_received))
{{ $defaultPaymentReceived }}@else{{ $Paymenttemplate->payment_received }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Payment
                                                    Failed</label>
                                                <textarea rows="4" name="payment_failed" class="form-control template-field"
                                                    placeholder="Payment failed message">
@if (empty($Paymenttemplate->payment_failed))
{{ $defaultPaymentFailed }}@else{{ $Paymenttemplate->payment_failed }}
@endif
</textarea>
                                            </div>
                                        </div>

                                        <div class="row g-5">
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Payment
                                                    Refunded</label>
                                                <textarea rows="4" name="payment_refunded" class="form-control template-field"
                                                    placeholder="Payment refunded message">
@if (empty($Paymenttemplate->payment_refunded))
{{ $defaultPaymentRefunded }}@else{{ $Paymenttemplate->payment_refunded }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Cancelled</label>
                                                <textarea rows="4" name="order_cancel" class="form-control template-field"
                                                    placeholder="Order cancellation message">
@if (empty($Paymenttemplate->order_cancel))
{{ $defaultOrderCancel }}@else{{ $Paymenttemplate->order_cancel }}
@endif
</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <span class="indicator-label">{{ __('Save Templates') }}</span>
                                            <span class="indicator-progress d-none">{{ __('Please wait...') }} <span class="spinner-border spinner-border-sm align-middle ml-2"></span></span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="catalog-step-pane" id="step-5" data-step="5" role="tabpanel">
                            <form method="post" autocomplete="off" enctype="multipart/form-data" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                                    <span></span>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="paymentToggle" name="payment_method_enable" {{ $Paymenttemplate && $Paymenttemplate->payment_method_enable ? 'checked' : '' }}>
                                        <label class="form-check-label" for="paymentToggle">{{ __('Enable') }}</label>
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Payment message body') }}</label>
                                        <p class="text-muted small mb-2">{{ __('Shown to customer with the pay button.') }}</p>
                                        <textarea rows="3" name="body" id="body" class="form-control" placeholder="{{ __('Payment message body') }}">{{ $Paymenttemplate->body ?? "Please complete your payment using the *Pay now* below:\n\n🔐 Secure payment · ⚡ Instant confirmation" }}</textarea>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Footer') }}</label>
                                        <input type="text" name="footer" id="footer" class="form-control" placeholder="{{ __('Support URL or company name') }}" value="{{ $Paymenttemplate ? $Paymenttemplate->footer : '' }}">
                                    </div>

                                    <h5 class="font-weight-bold text-dark mb-3">{{ __('Select active payment gateway') }}</h5>
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered align-middle">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>{{ __('Gateway') }}</th>
                                                    <th class="text-center" style="width:90px">{{ __('Use') }}</th>
                                                    <th>{{ __('Configuration / Key name') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="ni ni-whatsapp mr-3 text-success" style="font-size:1.35rem"></span>
                                                            <div>
                                                                <span class="font-weight-bold text-dark">WhatsApp Pay</span>
                                                                <span class="d-block text-muted small">{{ __('Meta WhatsApp in-chat payment') }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="payment_type" value="0" {{ ($Paymenttemplate->payment_type ?? 0) == 0 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="payment_configuration" class="form-control form-control-sm" placeholder="{{ __('Configuration name') }}" value="{{ $Paymenttemplate->payment_configuration ?? '' }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="ni ni-credit-card mr-3 text-info" style="font-size:1.35rem"></span>
                                                            <div>
                                                                <span class="font-weight-bold text-dark">Razorpay</span>
                                                                <span class="d-block text-muted small">{{ __('Cards, UPI, NetBanking, Wallets') }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="payment_type" value="1" {{ ($Paymenttemplate->payment_type ?? 0) == 1 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="payment_configuration_other" class="form-control form-control-sm" placeholder="{{ __('Razorpay config name') }}" value="{{ $Paymenttemplate->payment_configuration_other ?? '' }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="ni ni-credit-card mr-3 text-primary" style="font-size:1.35rem"></span>
                                                            <div>
                                                                <span class="font-weight-bold text-dark">PayU</span>
                                                                <span class="d-block text-muted small">{{ __('PayU India – Cards, UPI, Wallets') }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="payment_type" value="2" {{ ($Paymenttemplate->payment_type ?? 0) == 2 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="payment_configuration_payu" class="form-control form-control-sm" placeholder="{{ __('PayU merchant key / config') }}" value="{{ $Paymenttemplate->payment_configuration_payu ?? '' }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="ni ni-credit-card mr-3 text-warning" style="font-size:1.35rem"></span>
                                                            <div>
                                                                <span class="font-weight-bold text-dark">Zaakpay</span>
                                                                <span class="d-block text-muted small">{{ __('Zaakpay – Payment gateway') }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="payment_type" value="3" {{ ($Paymenttemplate->payment_type ?? 0) == 3 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="payment_configuration_zaakpay" class="form-control form-control-sm" placeholder="{{ __('Zaakpay config / key') }}" value="{{ $Paymenttemplate->payment_configuration_zaakpay ?? '' }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="ni ni-world mr-3 text-primary" style="font-size:1.35rem"></span>
                                                            <div>
                                                                <span class="font-weight-bold text-dark">Meta Pay</span>
                                                                <span class="d-block text-muted small">{{ __('Meta Pay / Facebook Pay') }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <input class="form-check-input" type="radio" name="payment_type" value="4" {{ ($Paymenttemplate->payment_type ?? 0) == 4 ? 'checked' : '' }}>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="payment_configuration_meta" class="form-control form-control-sm" placeholder="{{ __('Meta Pay config name') }}" value="{{ $Paymenttemplate->payment_configuration_meta ?? '' }}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">{{ __('Update Payment Settings') }}</span>
                                        <span class="indicator-progress d-none">{{ __('Please wait...') }} <span class="spinner-border spinner-border-sm align-middle ml-2"></span></span>
                                    </button>
                                </div>
                            </form>
                    </div>

                    <div class="catalog-step-pane" id="step-6" data-step="6" role="tabpanel">
                            <form id="shipping-discount-form" method="post" autocomplete="off" enctype="multipart/form-data" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div>
                                    <h4 class="font-weight-bold text-dark mb-3">{{ __('Shipping methods') }}</h4>
                                    <p class="text-muted small mb-3">{{ __('At least one method must be enabled.') }}</p>
                                    <input type="hidden" name="enable_self_pickup" value="0">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input shipping-method" type="checkbox" name="enable_self_pickup" id="enable_self_pickup" value="1" {{ $Paymenttemplate && $Paymenttemplate->enable_self_pickup ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_self_pickup">{{ __('Self Pickup') }}</label>
                                    </div>
                                    <input type="hidden" name="enable_in_store" value="0">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input shipping-method" type="checkbox" name="enable_in_store" id="enable_in_store" value="1" {{ $Paymenttemplate && $Paymenttemplate->enable_in_store ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_in_store">{{ __('In-store') }}</label>
                                    </div>
                                    <input type="hidden" name="enable_delivery" value="0">
                                    <div class="form-check mb-4">
                                        <input class="form-check-input shipping-method" type="checkbox" name="enable_delivery" id="enable_delivery" value="1" {{ $Paymenttemplate && $Paymenttemplate->enable_delivery ? 'checked' : '' }}>
                                        <label class="form-check-label" for="enable_delivery">{{ __('Delivery') }}</label>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="isShippingFree" name="isShippingFree" value="1" {{ $Paymenttemplate && $Paymenttemplate->isShippingFree ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isShippingFree">{{ __('Free shipping above order amount') }}</label>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Free shipping minimum order amount') }}</label>
                                        <input type="number" name="shipping_free_from_amount" class="form-control" placeholder="0.00" value="{{ $Paymenttemplate ? $Paymenttemplate->shipping_free_from_amount : '' }}" step="0.01">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Default shipping charge') }}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">{{ optional($Paymenttemplate)->currency_symbol ?? '₹' }}</span></div>
                                            <input type="number" name="shipping" id="shipping_amount" class="form-control" placeholder="0.00" value="{{ $Paymenttemplate ? $Paymenttemplate->shipping : '' }}" step="0.01">
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Shipping description') }}</label>
                                        <input type="text" name="shipping_description" id="shipping_description" class="form-control" placeholder="{{ __('e.g. Standard delivery 2–5 days') }}" value="{{ $Paymenttemplate ? $Paymenttemplate->shipping_description : '' }}">
                                    </div>
                                    <hr class="my-4">
                                    <h4 class="font-weight-bold text-dark mb-3">{{ __('Discount settings') }}</h4>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="isDiscountAutoApply" name="isDiscountAutoApply" value="1" {{ $Paymenttemplate && $Paymenttemplate->isDiscountAutoApply ? 'checked' : '' }}>
                                        <label class="form-check-label" for="isDiscountAutoApply">{{ __('Auto-apply discount above order amount') }}</label>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Discount minimum order amount') }}</label>
                                        <input type="number" name="discount_from_amount" class="form-control" placeholder="0.00" value="{{ $Paymenttemplate ? $Paymenttemplate->discount_from_amount : '' }}" step="0.01">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Discount type') }}</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="form-check mr-4">
                                                <input class="form-check-input" type="radio" name="discount_type" id="discount_percentage" value="percent" {{ (optional($Paymenttemplate)->discount_type ?? 'percent') == 'percent' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="discount_percentage">{{ __('Percentage') }}</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="discount_type" id="discount_fixed" value="fixed" {{ (optional($Paymenttemplate)->discount_type ?? '') == 'fixed' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="discount_fixed">{{ __('Fixed amount') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Default discount value') }}</label>
                                        <input type="number" name="default_discount" class="form-control" value="{{ optional($Paymenttemplate)->default_discount ?? 0 }}" min="0" step="0.01" placeholder="0.00">
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-shipping-save">
                                        <span class="indicator-label">{{ __('Update Settings') }}</span>
                                        <span class="indicator-progress d-none">{{ __('Please wait...') }} <span class="spinner-border spinner-border-sm align-middle ml-2"></span></span>
                                    </button>
                                </div>
                            </form>
                    </div>

                    <div class="catalog-step-pane" id="step-7" data-step="7" role="tabpanel">
                            <form method="post" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Low stock alert threshold') }}</label>
                                        <input type="number" name="low_stock_alert_at" class="form-control" placeholder="e.g. 5" value="{{ optional($Paymenttemplate)->low_stock_alert_at }}" min="0">
                                        <small class="form-text text-muted">{{ __('Show warning when product quantity is at or below this number. Leave empty to disable.') }}</small>
                                    </div>
                                    <div class="form-check form-switch mb-4">
                                        <input type="hidden" name="allow_backorders" value="0">
                                        <input class="form-check-input" type="checkbox" name="allow_backorders" id="allow_backorders" value="1" {{ $Paymenttemplate && $Paymenttemplate->allow_backorders ? 'checked' : '' }}>
                                        <label class="form-check-label" for="allow_backorders">{{ __('Allow backorders') }}</label>
                                        <small class="d-block text-muted">{{ __('Allow customers to order when product is out of stock.') }}</small>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label font-weight-bold text-dark">{{ __('Catalog tagline') }}</label>
                                        <input type="text" name="catalog_tagline" class="form-control" placeholder="{{ __('e.g. Fresh products, fast delivery') }}" value="{{ optional($Paymenttemplate)->catalog_tagline }}">
                                        <small class="form-text text-muted">{{ __('Short line shown with your catalog (optional).') }}</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <span class="indicator-label">{{ __('Save Catalog Options') }}</span>
                                        <span class="indicator-progress d-none">{{ __('Please wait...') }} <span class="spinner-border spinner-border-sm align-middle ml-2"></span></span>
                                    </button>
                                </div>
                            </form>
                    </div>
                    </div><!-- #catalog-step-content -->

                    <!-- Stepper navigation -->
                    <div class="catalog-stepper-actions" id="catalog-stepper-actions">
                        <button type="button" class="btn btn-outline-secondary" id="catalog-step-prev" aria-label="{{ __('Previous') }}">
                            <i class="ni ni-bold-left mr-1"></i> {{ __('Previous') }}
                        </button>
                        <span class="text-muted small" id="catalog-step-counter">{{ __('Step') }} 1 {{ __('of') }} 7</span>
                        <button type="button" class="btn btn-primary" id="catalog-step-next" aria-label="{{ __('Next') }}">
                            {{ __('Next') }} <i class="ni ni-bold-right ml-1"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('topjs')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const syncButton = document.getElementById("syncCatalogBtn");
            if (!syncButton) return;

            syncButton.addEventListener("click", function(e) {
                e.preventDefault();
                syncButton.disabled = true;
                syncButton.innerHTML = '<i class="ni ni-refresh mr-1 spinner-border spinner-border-sm"></i> {{ __("Syncing...") }}';

                fetch("{{ route('catalog.fetchCatalog') }}", {
                        method: "POST", // or "GET" if your route uses GET
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json",
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        syncButton.disabled = false;
                        syncButton.innerHTML =
                            '<i class="ni ni-refresh mr-1"></i> {{ __("Sync Now") }}';

                        if (data.status === "success") {
                            toastr.success(data.message || "Catalog synced successfully");

                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);

                        } else {
                            toastr.error(data.message || "Something went wrong");
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        syncButton.disabled = false;
                        syncButton.innerHTML = '<i class="ni ni-refresh mr-1"></i> {{ __("Sync Now") }}';
                        toastr.error("{{ __('Server error. Please try again later.') }}");
                    });
            });
        });
    </script>
    <script>
        (function() {
            var TOTAL_STEPS = 7;
            var stepTitles = {
                '1': { title: '{{ __("Sync Catalog") }}', subtitle: '{{ __("Sync your product catalog with WhatsApp.") }}' },
                '2': { title: '{{ __("WhatsApp StoreFront") }}', subtitle: '{{ __("Business profile, logo and currency.") }}' },
                '3': { title: '{{ __("Address & Messages") }}', subtitle: '{{ __("Delivery address and order messages.") }}' },
                '4': { title: '{{ __("Message Templates") }}', subtitle: '{{ __("Order status and payment templates.") }}' },
                '5': { title: '{{ __("Payment Method") }}', subtitle: '{{ __("Payment gateways and pay button message.") }}' },
                '6': { title: '{{ __("Shipping & Discount") }}', subtitle: '{{ __("Shipping methods and automatic discounts.") }}' },
                '7': { title: '{{ __("Catalog Options") }}', subtitle: '{{ __("Stock alerts, backorders and tagline.") }}' }
            };

            function showStep(step) {
                var stepStr = String(step);
                if (!stepTitles[stepStr]) stepStr = '1';

                var panes = document.querySelectorAll('.catalog-step-pane');
                var steps = document.querySelectorAll('.catalog-stepper-step');

                panes.forEach(function(p) {
                    var isActive = p.getAttribute('data-step') === stepStr;
                    p.classList.toggle('active', isActive);
                });

                steps.forEach(function(s) {
                    s.classList.toggle('active', s.getAttribute('data-step') === stepStr);
                });

                var titleEl = document.getElementById('catalog-step-title');
                var subtitleEl = document.getElementById('catalog-step-subtitle');
                var counterEl = document.getElementById('catalog-step-counter');
                var prevBtn = document.getElementById('catalog-step-prev');
                var nextBtn = document.getElementById('catalog-step-next');

                if (titleEl && stepTitles[stepStr]) { titleEl.textContent = stepTitles[stepStr].title; }
                if (subtitleEl && stepTitles[stepStr]) { subtitleEl.textContent = stepTitles[stepStr].subtitle; }

                var stepNum = parseInt(stepStr, 10);
                if (counterEl) counterEl.textContent = '{{ __("Step") }} ' + stepNum + ' {{ __("of") }} ' + TOTAL_STEPS;
                if (prevBtn) { prevBtn.style.visibility = stepNum === 1 ? 'hidden' : 'visible'; }
                if (nextBtn) {
                    nextBtn.style.visibility = 'visible';
                    nextBtn.innerHTML = stepNum === TOTAL_STEPS
                        ? '{{ __("Done") }}'
                        : '{{ __("Next") }} <i class="ni ni-bold-right ml-1"></i>';
                }

                // Scroll to the selected step's content so it's clearly visible
                var targetPane = document.querySelector('.catalog-step-pane[data-step=\"' + stepStr + '\"]');
                if (targetPane) {
                    targetPane.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                var stepsContainer = document.getElementById('catalog-stepper-steps');
                if (stepsContainer) {
                    stepsContainer.addEventListener('click', function(e) {
                        var step = e.target.closest('.catalog-stepper-step');
                        if (step) {
                            e.preventDefault();
                            showStep(step.getAttribute('data-step'));
                        }
                    });
                }

                var prevBtn = document.getElementById('catalog-step-prev');
                var nextBtn = document.getElementById('catalog-step-next');

                if (prevBtn) prevBtn.addEventListener('click', function() {
                    var active = document.querySelector('.catalog-stepper-step.active');
                    var current = active ? parseInt(active.getAttribute('data-step'), 10) : 1;
                    if (current > 1) showStep(String(current - 1));
                });

                if (nextBtn) nextBtn.addEventListener('click', function() {
                    var active = document.querySelector('.catalog-stepper-step.active');
                    var current = active ? parseInt(active.getAttribute('data-step'), 10) : 1;
                    if (current < TOTAL_STEPS) showStep(String(current + 1));
                });

                // Initial state
                showStep('1');
                // Form submit: validate shipping form + submit all forms via fetch
                document.querySelectorAll('form').forEach(function(form) {
                    form.addEventListener('submit', function(e) {
                        if (form.id === 'shipping-discount-form') {
                            var checked = form.querySelectorAll('.shipping-method:checked');
                            if (checked.length === 0) {
                                e.preventDefault();
                                if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: '{{ __("Validation Error") }}', text: '{{ __("Please enable at least one shipping method (Self Pickup, In-store or Delivery).") }}', confirmButtonColor: '#5e72e4' });
                                else alert('{{ __("Please enable at least one shipping method.") }}');
                                return false;
                            }
                        }
                        e.preventDefault();
                        var submitBtn = form.querySelector('.indicator-label');
                        var progress = form.querySelector('.indicator-progress');
                        if (submitBtn) submitBtn.style.display = 'none';
                        if (progress) progress.classList.remove('d-none');
                        var formData = new FormData(form);
                        fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                            .then(function(r) {
                                return r.json().then(function(data) { return { ok: r.ok, status: r.status, data: data }; }).catch(function() { return { ok: r.ok, status: r.status, data: {} }; });
                            })
                            .then(function(res) {
                                var data = res.data;
                                if (res.ok && data.success !== false) {
                                    if (typeof Swal !== 'undefined') Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: '{{ __("Settings saved successfully!") }}', showConfirmButton: false, timer: 3000 });
                                    else if (typeof toastr !== 'undefined') toastr.success('{{ __("Settings saved successfully!") }}');
                                    else alert('{{ __("Settings saved.") }}');
                                } else {
                                    var msg = (data && data.message) ? data.message : '{{ __("Error saving settings") }}';
                                    if (typeof Swal !== 'undefined') Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, showConfirmButton: false, timer: 5000 });
                                    else alert(msg);
                                }
                            })
                            .catch(function() {
                                if (typeof Swal !== 'undefined') Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: '{{ __("Error saving settings") }}', showConfirmButton: false, timer: 5000 });
                                else alert('{{ __("Error saving settings") }}');
                            })
                            .finally(function() {
                                if (submitBtn) submitBtn.style.display = '';
                                if (progress) progress.classList.add('d-none');
                            });
                        return false;
                    });
                });
            });
        })();
    </script>
    <script>
        (function() {
            if (typeof window.jQuery === 'undefined') return;
            window.jQuery(document).ready(function($) {
            // Initialize select2
            $('#product_id').select2({
                placeholder: "Select a product",
                width: '100%'
            });

            $('.shipping-method').change(function() {
                const checkedCount = $('.shipping-method:checked').length;

                if (checkedCount === 0) {
                    // Swal.fire({
                    //     icon: 'warning',
                    //     title: 'Shipping Method Required',
                    //     text: 'At least one shipping method must be enabled.',
                    //     confirmButtonColor: '#5e72e4',
                    // }).then(() => {
                    //     $(this).prop('checked', true);
                    // });
                }
            });

            // Handle payment toggle with SweetAlert
            $('#paymentToggle').change(function() {
                const isChecked = $(this).prop('checked');
                Swal.fire({
                    title: 'Confirm Change',
                    text: `Are you sure you want to ${isChecked ? 'enable' : 'disable'} payment methods?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#5e72e4',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform AJAX request
                        $.ajax({
                            url: "{{ route('catalog.togglePaymentMethod') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                payment_method_enable: isChecked ? 1 : 0
                            },
                            success: function(response) {
                                // Show success toast
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Payment settings updated!',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            },
                            error: function(xhr) {
                                // Revert toggle on error
                                $('#paymentToggle').prop('checked', !isChecked);
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Error updating settings',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        });
                    } else {
                        // Revert toggle if user cancels
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Handle address toggle with SweetAlert
            $('#addressToggle').change(function() {
                const isChecked = $(this).prop('checked');
                Swal.fire({
                    title: 'Confirm Change',
                    text: `Are you sure you want to ${isChecked ? 'enable' : 'disable'} address messages?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#5e72e4',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, proceed'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform AJAX request
                        $.ajax({
                            url: "{{ route('catalog.toggleAddressMessage') }}",
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                address_message_enable: isChecked ? 1 : 0
                            },
                            success: function(response) {
                                // Show success toast
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: 'Address settings updated!',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            },
                            error: function(xhr) {
                                // Revert toggle on error
                                $('#addressToggle').prop('checked', !isChecked);
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Error updating settings',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        });
                    } else {
                        // Revert toggle if user cancels
                        $(this).prop('checked', !isChecked);
                    }
                });
            });

            // Handle logo removal
            $('.btn-remove').click(function() {
                const target = $(this).data('target');
                $(this).closest('.image-preview-container').remove();
                $('#remove_logo').val(1);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Logo removed!',
                    showConfirmButton: false,
                    timer: 2000
                });
            });

            // Variable insertion
            // Store the currently active textarea
            let activeTextarea = null;

            // Set active textarea when user focuses on it
            document.querySelectorAll('.template-field').forEach(textarea => {
                textarea.addEventListener('focus', function() {
                    activeTextarea = this;
                });
            });

            // Variable insertion with improved functionality
            document.querySelectorAll('.variable-item').forEach(item => {
                item.addEventListener('click', function() {
                    const variable = this.getAttribute('data-variable');

                    if (activeTextarea) {
                        const startPos = activeTextarea.selectionStart;
                        const endPos = activeTextarea.selectionEnd;
                        const text = activeTextarea.value;

                        // Insert variable at cursor position
                        activeTextarea.value = text.substring(0, startPos) + variable + text
                            .substring(endPos);

                        // Set new cursor position
                        activeTextarea.selectionStart = startPos + variable.length;
                        activeTextarea.selectionEnd = startPos + variable.length;

                        // Keep focus on textarea
                        activeTextarea.focus();

                        // Visual feedback
                        this.style.backgroundColor = '#cfe2ff';
                        this.style.borderColor = '#0d6efd';
                        setTimeout(() => {
                            this.style.backgroundColor = '';
                            this.style.borderColor = '';
                        }, 300);
                    } else {
                        // If no textarea is active, show alert
                        Swal.fire({
                            icon: 'warning',
                            title: 'Insertion Point Needed',
                            text: 'Please click on a textarea first to set the insertion point',
                            confirmButtonColor: '#0d6efd',
                            confirmButtonText: 'OK',
                            backdrop: true
                        });
                    }
                });
            });

            // Logo upload preview
            const logoUpload = document.getElementById('logo-upload');
            const logoPreview = document.getElementById('logo-preview');

            if (logoUpload) {
                logoUpload.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            logoPreview.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Remove logo functionality
            document.querySelectorAll('.btn-remove').forEach(button => {
                button.addEventListener('click', function() {
                    const container = this.closest('.image-preview-container');
                    const preview = container.querySelector('img');
                    preview.src = 'https://via.placeholder.com/150';
                    if (logoUpload) logoUpload.value = '';
                });
            });

            // Save button animation
            document.querySelectorAll('.btn-primary').forEach(button => {
                button.addEventListener('click', function() {
                    const btn = this;
                    btn.classList.add('saving');

                    setTimeout(() => {
                        btn.classList.remove('saving');

                        // Show success message
                        const toast = document.createElement('div');
                        toast.className = 'position-fixed top-0 end-0 p-3';
                        toast.style.zIndex = '11';
                        toast.innerHTML = `
                        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header bg-success text-white">
                                <strong class="me-auto"><i class="fas fa-check-circle me-2"></i> Success</strong>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                            </div>
                            <div class="toast-body">
                                Settings saved successfully!
                            </div>
                        </div>
                    `;

                        document.body.appendChild(toast);

                        // Remove toast after 3 seconds
                        setTimeout(() => {
                            toast.remove();
                        }, 3000);
                    }, 1500);
                });
            });

            });
        })();
    </script>
@endsection
