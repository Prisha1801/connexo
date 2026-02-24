@extends('general.index', ['title' => __('Catalog Payment Settings')])

@section('content')
    <style>
        .nav.nav-pills.nav-pills-custom .show>.nav-link.active,
        .nav.nav-pills.nav-pills-custom .nav-link.active {
            background-color: #502e4c;
            border: 1px solid var(--bs-border-dashed-color);
            transition-duration: 1ms;
            position: relative;
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
        }

        .image-preview-container .btn-remove {
            position: absolute;
            top: -10px;
            right: -10px;
            border-radius: 50%;
            padding: 5px 10px;
            font-size: 14px;
        }

        .variable-list {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .variable-item {
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 5px;
            background-color: #e9ecef;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            cursor: pointer;
        }

        .variable-item:hover {
            background-color: #dee2e6;
        }
    </style>
    <!--begin::Container-->
    <div class="container-xxl">
        <!--begin::Row-->
        <div class="row g-5 g-xl-8">
            <!--begin::Col-->
            <div class="col-xl-3 col-lg-4 col-md-12">
                <!--begin::Menu-->
                <div class="card card-flush">
                    <div class="card-body p-4 p-lg-5">
                        <div class="nav flex-column nav-pills nav-pills-custom" id="v-pills-tab" role="tablist"
                            aria-orientation="vertical">
                            <!-- Sync Catalog -->
                            <a class="nav-link d-flex align-items-center active py-3 px-4 mb-2" id="agents_tab1"
                                data-bs-toggle="pill" href="#agents1" role="tab" aria-controls="agents1"
                                aria-selected="true">
                                <i class="ki-duotone ki-cloud-add fs-2 me-3 text-success">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <span class="fs-6 fw-semibold">Sync Catalog</span>
                            </a>

                            <!-- WhatsApp StoreFront -->
                            <a class="nav-link d-flex align-items-center py-3 px-4 mb-2" id="storefront_tab"
                                data-bs-toggle="pill" href="#storefront" role="tab" aria-controls="storefront"
                                aria-selected="false">
                                <i class="ki-duotone ki-shop fs-2 me-3 text-info">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <span class="fs-6 fw-semibold">WhatsApp StoreFront</span>
                            </a>

                            <!-- Message -->
                            <a class="nav-link d-flex align-items-center py-3 px-4 mb-2" id="agents_tab"
                                data-bs-toggle="pill" href="#agents" role="tab" aria-controls="agents"
                                aria-selected="false">
                                <i class="ki-duotone ki-message-text-2 fs-2 me-3 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                                <span class="fs-6 fw-semibold">Address</span>
                            </a>

                            <!-- Message Templates -->
                            <a class="nav-link d-flex align-items-center py-3 px-4 mb-2" id="templates_tab"
                                data-bs-toggle="pill" href="#templates" role="tab" aria-controls="templates"
                                aria-selected="false">
                                <i class="ki-duotone ki-sms fs-2 me-3 text-primary">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <span class="fs-6 fw-semibold">Message Templates</span>
                            </a>

                            <!-- Payment Method -->
                            <a class="nav-link d-flex align-items-center py-3 px-4 mb-2" id="catalogs_tab"
                                data-bs-toggle="pill" href="#catalogs" role="tab" aria-controls="catalogs"
                                aria-selected="false">
                                <i class="ki-duotone ki-dollar fs-2 me-3 text-warning">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <span class="fs-6 fw-semibold">Payment Method</span>
                            </a>

                            <!-- Shipping -->
                            <a class="nav-link d-flex align-items-center py-3 px-4 mb-2" id="catalogs_shipping"
                                data-bs-toggle="pill" href="#catalogs-shipping" role="tab"
                                aria-controls="catalogs-shipping" aria-selected="false">
                                <i class="ki-duotone ki-delivery fs-2 me-3 text-danger">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                                <span class="fs-6 fw-semibold">Shipping & Discount</span> <!-- Changed name here -->
                            </a>
                        </div>
                    </div>
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Col-->

            <!--begin::Col-->
            <div class="col-xl-9 col-lg-8 col-md-12">
                <!--begin::Tab Content-->
                <div class="tab-content" id="v-pills-tabContent">
                    <!--begin::Tab Pane-->
                    <div class="tab-pane fade show active" id="agents1" role="tabpanel" aria-labelledby="agents_tab1">
                        <div class="card">
                            <div class="card-header border-0 py-5">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label fw-bold fs-3 mb-1">Sync Catalog</span>
                                </h3>
                                <div class="card-toolbar">
                                    <button id="syncCatalogBtn" class="btn btn-sm btn-secondary">
                                        <i class="ki-duotone ki-reload fs-3 me-1">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                        Sync Now
                                    </button>
                                </div>
                            </div>
                            <div class="card-body py-0">
                                @if (Session::has('message'))
                                    <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                                        <i class="ki-duotone ki-shield-tick fs-2hx me-4 text-danger">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <h4 class="mb-1 text-danger">Notice</h4>
                                            <span>{{ Session::get('message') }}</span>
                                        </div>
                                    </div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="min-w-150px">{{ __('Name') }}</th>
                                                <th class="min-w-150px">{{ __('Catalogue Id') }}</th>
                                                <th class="min-w-100px">{{ __('Product Count') }}</th>
                                                <th class="min-w-100px">{{ __('Status') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @foreach ($catalogs as $catalog)
                                                @php
                                                    $products_count = App\Models\CatalogProduct::where(
                                                        'catalog_id',
                                                        $catalog->catalog_id,
                                                    )
                                                        ->where('company_id', $catalog->company_id)
                                                        ->get();
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <span class="text-gray-800 fw-bold">{{ $catalog->name }}</span>
                                                    </td>
                                                    <td>{{ $catalog->catalog_id }}</td>
                                                    <td>
                                                        <span
                                                            class="badge badge-light-primary">{{ count($products_count ?? 0) }}</span>
                                                    </td>
                                                    <td>
                                                        @if (!$catalog->status)
                                                            <span class="badge badge-light-danger">Disabled</span>
                                                        @else
                                                            <span class="badge badge-light-success">Enabled</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Tab Pane-->

                    <!-- WhatsApp StoreFront Tab -->
                    <div class="tab-pane fade" id="storefront" role="tabpanel" aria-labelledby="storefront_tab">
                        <div class="card">
                            <form id="storefront-form" method="post" autocomplete="off" enctype="multipart/form-data"
                                action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div class="card-body p-9">
                                    <h3 class="fw-bold text-gray-900 mb-8">WhatsApp StoreFront Settings</h3>

                                    <!-- Business Logo -->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold text-gray-700">Business Logo</label>
                                        <div class="d-flex align-items-center">
                                            @if ($Paymenttemplate && $Paymenttemplate->logo)
                                                <div class="image-preview-container me-5 mb-5">
                                                    <img src="{{ asset('uploads/storefront/' . $Paymenttemplate->logo) }}"
                                                        class="img-fluid rounded" alt="Business Logo"
                                                        style="max-width: 120px; max-height: 120px;">
                                                    <button type="button" class="btn btn-danger btn-sm btn-remove"
                                                        data-target="logo">
                                                        <i class="ki-duotone ki-cross fs-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </button>
                                                    <input type="hidden" name="remove_logo" value="0"
                                                        id="remove_logo">
                                                </div>
                                            @endif
                                            <div class="w-100">
                                                <input type="file" name="logo"
                                                    class="form-control form-control-solid" accept="image/*">
                                                <div class="form-text">Recommended size: 300x300 pixels</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Business Name -->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold text-gray-700">Business Name</label>
                                        <input type="text" name="business_name"
                                            class="form-control form-control-solid"
                                            value="{{ $Paymenttemplate->business_name ?? '' }}"
                                            placeholder="Enter your business name">
                                    </div>

                                    <!-- Business Address -->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold text-gray-700">Business Address</label>
                                        <textarea rows="3" name="business_address" class="form-control form-control-solid"
                                            placeholder="Enter your business address">{{ $Paymenttemplate->business_address ?? '' }}</textarea>
                                    </div>

                                    <!-- Contact Information -->
                                    <div class="row mb-10">
                                        <div class="col-md-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">Business Phone</label>
                                            <input type="tel" name="business_phone"
                                                class="form-control form-control-solid"
                                                value="{{ $Paymenttemplate->business_phone ?? '' }}"
                                                placeholder="+1234567890">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">Business
                                                WhatsApp</label>
                                            <input type="tel" name="business_whatsapp"
                                                class="form-control form-control-solid"
                                                value="{{ $Paymenttemplate->business_whatsapp ?? '' }}"
                                                placeholder="+1234567890">
                                        </div>
                                    </div>

                                    <!-- Business Email -->
                                    <div class="mb-10">
                                        <label class="form-label fs-6 fw-semibold text-gray-700">Business Email</label>
                                        <input type="email" name="business_email"
                                            class="form-control form-control-solid"
                                            value="{{ $Paymenttemplate->business_email ?? '' }}"
                                            placeholder="contact@business.com">
                                    </div>

                                    <!-- Tax Information -->
                                    <div class="row mb-10">
                                        <div class="col-md-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">GSTIN/VAT</label>
                                            <input type="text" name="gstin_vat"
                                                class="form-control form-control-solid"
                                                value="{{ $Paymenttemplate->gstin_vat ?? '' }}"
                                                placeholder="GSTIN1234567XYZ">
                                        </div>
                                    </div>

                                    <!-- Currency Settings -->
                                    <div class="row mb-10">
                                        <div class="col-md-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">Currency Code</label>
                                            <input type="text" name="currency_code"
                                                class="form-control form-control-solid"
                                                value="{{ $Paymenttemplate->currency_code ?? 'INR' }}" placeholder="INR">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">Currency
                                                Symbol</label>
                                            <input type="text" name="currency_symbol"
                                                class="form-control form-control-solid"
                                                value="{{ $Paymenttemplate->currency_symbol ?? '₹' }}" placeholder="₹">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end pt-5">
                                        <button type="submit" class="btn btn-primary px-6 py-3">
                                            <span class="indicator-label">Save Settings</span>
                                            <span class="indicator-progress">Please wait...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End WhatsApp StoreFront Tab -->

                    <!--begin::Tab Pane-->
                    <div class="tab-pane fade" id="agents" role="tabpanel" aria-labelledby="agents_tab">
                        <div class="card">
                            <form id="restorant-apps-form" method="post" autocomplete="off"
                                enctype="multipart/form-data" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div class="card-body p-9">
                                    <div class="d-flex flex-stack mb-8">
                                        <div class="d-flex flex-column">
                                            <h3 class="fw-bold text-gray-900 mb-0">Address Message Detail</h3>
                                            <p class="text-muted fs-6 mt-1">Write a clear and concise address message here,
                                                including essential details like message information to guide the user
                                                effectively.</p>
                                        </div>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input h-30px w-50px" type="checkbox"
                                                id="addressToggle" name="address_message_enable"
                                                {{ $Paymenttemplate ? ($Paymenttemplate->address_message_enable == 1 ? 'checked' : '') : '' }}>
                                        </div>
                                    </div>

                                    <div class="mb-10">
                                        <label
                                            class="form-label fs-6 fw-semibold text-gray-700">{{ __('Message') }}</label>
                                        <textarea rows="5" name="address_mess" id="address_mess" class="form-control form-control-solid"
                                            placeholder="Enter your address message">{{ $Paymenttemplate?->address_mess ??
                                                '🚚 How would you like to receive your order?
                                                                                        
                                                                                        Please choose one of the options below to proceed:' }}</textarea>
                                    </div>

                                    <div class="separator my-10"></div>

                                    <h3 class="fw-bold text-gray-900 mb-8">Order Dispatch Message</h3>

                                    <div class="mb-10">
                                        <label
                                            class="form-label fs-6 fw-semibold text-gray-700">{{ __('Message') }}</label>
                                        <textarea rows="5" name="order_message" id="order_message" class="form-control form-control-solid"
                                            placeholder="Enter your order dispatch message">{{ $Paymenttemplate ? $Paymenttemplate->order_message : '' }}</textarea>
                                    </div>

                                    <div class="separator my-10"></div>

                                    <h3 class="fw-bold text-gray-900 mb-8">Catalog Message Detail</h3>
                                    <p class="text-muted fs-6 mb-6">Select your product from the dropdown, and a
                                        personalized catalog message will be sent, showcasing the product description and
                                        the first visible image.</p>

                                    <div class="mb-10 mr-10">
                                        <label
                                            class="form-label fs-6 fw-semibold text-gray-700 mr-6">{{ __('Select Product') }}</label>
                                        <select v-model="product_id" name="product_id" id="product_id"
                                            class="form-select form-select-solid w-10" data-control="select2"
                                            data-placeholder="Select a product">
                                            <option value="">{{ __('Select Product') }}</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->retailer_id }}"
                                                    {{ $Paymenttemplate ? ($product->retailer_id == $Paymenttemplate->product_id ? 'selected' : '') : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-flex justify-content-end pt-5">
                                        <button type="submit" class="btn btn-primary px-6 py-3">
                                            <span class="indicator-label">Update Settings</span>
                                            <span class="indicator-progress">Please wait...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end::Tab Pane-->

                    <!-- Message Templates Tab -->
                    <div class="tab-pane fade" id="templates" role="tabpanel" aria-labelledby="templates_tab">
                        <div class="card">
                            <form id="templates-form" method="post" autocomplete="off"
                                action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div class="card-body p-9">
                                    <h3 class="fw-bold text-gray-900 mb-8">Message Templates</h3>

                                    <div class="variable-list">
                                        <div class="fw-bold mb-2">Available Variables:</div>
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

                                    <!-- Default Template for 24hr Inactive Window -->
                                    <div class="mb-10">
                                        <h4 class="fw-bold text-gray-700 mb-4">Default Template for 24hr Inactive Window
                                        </h4>
                                        <p class="text-muted fs-6 mb-6">Select a default utility template to send when the
                                            chat window has been inactive for 24 hours. This template must be of category
                                            "utility".</p>

                                        <div class="mb-10">
                                            {{-- <label class="form-label fs-6 fw-semibold text-gray-700">Select Template</label> --}}
                                            <select name="default_template_id" class="form-select form-select-solid"
                                                data-control="select2" data-placeholder="Select a template">
                                                <option value="">No template selected</option>
                                                @foreach ($utilityTemplates as $template)
                                                    <option value="{{ $template->id }}"
                                                        data-category="{{ $template->category }}"
                                                        data-language="{{ $template->language }}"
                                                        @selected(isset($Paymenttemplate->default_template_id) && $Paymenttemplate->default_template_id == $template->id)>
                                                        {{ $template->name }} ({{ $template->language }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="form-text">This template will be used to restart conversations
                                                after 24 hours of inactivity</div>
                                        </div>
                                    </div>
                                    <!-- Order Status Templates -->
                                    <div class="mb-10">
                                        <h4 class="fw-bold text-gray-700 mb-4">Order Status Templates</h4>

                                        <div class="row g-5 mb-8">
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Accepted</label>
                                                <textarea rows="4" name="order_accepted" class="form-control form-control-solid template-field"
                                                    placeholder="Order accepted message">
@if (empty($Paymenttemplate->order_accepted))
{{ $defaultOrderAccepted }}@else{{ $Paymenttemplate->order_accepted }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Dispatched</label>
                                                <textarea rows="4" name="order_dispatched" class="form-control form-control-solid template-field"
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
                                                <textarea rows="4" name="order_prepared" class="form-control form-control-solid template-field"
                                                    placeholder="Order prepared message">
@if (empty($Paymenttemplate->order_prepared))
{{ $defaultOrderPrepared }}@else{{ $Paymenttemplate->order_prepared }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Delivered</label>
                                                <textarea rows="4" name="order_delivered" class="form-control form-control-solid template-field"
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
                                        <textarea rows="4" name="review_template" class="form-control form-control-solid template-field"
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
                                                <textarea rows="4" name="payment_received" class="form-control form-control-solid template-field"
                                                    placeholder="Payment received message">
@if (empty($Paymenttemplate->payment_received))
{{ $defaultPaymentReceived }}@else{{ $Paymenttemplate->payment_received }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Payment
                                                    Failed</label>
                                                <textarea rows="4" name="payment_failed" class="form-control form-control-solid template-field"
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
                                                <textarea rows="4" name="payment_refunded" class="form-control form-control-solid template-field"
                                                    placeholder="Payment refunded message">
@if (empty($Paymenttemplate->payment_refunded))
{{ $defaultPaymentRefunded }}@else{{ $Paymenttemplate->payment_refunded }}
@endif
</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fs-6 fw-semibold text-gray-700">Order
                                                    Cancelled</label>
                                                <textarea rows="4" name="order_cancel" class="form-control form-control-solid template-field"
                                                    placeholder="Order cancellation message">
@if (empty($Paymenttemplate->order_cancel))
{{ $defaultOrderCancel }}@else{{ $Paymenttemplate->order_cancel }}
@endif
</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end pt-5">
                                        <button type="submit" class="btn btn-primary px-6 py-3">
                                            <span class="indicator-label">Save Templates</span>
                                            <span class="indicator-progress">Please wait...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- End Message Templates Tab -->

                    <!--begin::Tab Pane-->
                    <div class="tab-pane fade" id="catalogs" role="tabpanel" aria-labelledby="catalogs_tab">
                        <div class="card">
                            <form id="restorant-apps-form" method="post" autocomplete="off"
                                enctype="multipart/form-data" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div class="card-body p-9">
                                    <div class="d-flex flex-stack mb-8">
                                        <div class="d-flex flex-column">
                                            <h3 class="fw-bold text-gray-900 mb-0">Payment Method</h3>
                                            <p class="text-muted fs-6 mt-1">Select payment methods are listed below and can
                                                be sorted to control their display order on the message.</p>
                                        </div>
                                        <div class="form-check form-switch form-check-custom form-check-solid">
                                            <input class="form-check-input h-30px w-50px" type="checkbox"
                                                id="paymentToggle" name="payment_method_enable"
                                                {{ $Paymenttemplate ? ($Paymenttemplate->payment_method_enable == 1 ? 'checked' : '') : '' }}>
                                        </div>
                                    </div>

                                    <div class="mb-10">
                                        <label
                                            class="form-label fs-6 fw-semibold text-gray-700">{{ __('Body') }}</label>
                                        <p class="text-muted fs-7 mb-3">Write a clear and concise payment message here,
                                            including essential details like order reference to guide the user effectively.
                                        </p>
                                        <textarea rows="5" name="body" id="body" class="form-control form-control-solid"
                                            placeholder="Enter payment message body">{{ $Paymenttemplate?->body ??
                                                'Please complete your payment using the *Pay now* below:
                                                                                        
                                                                                        🔐 Secure payment · ⚡ Instant confirmation' }}
</textarea>
                                    </div>

                                    <div class="mb-10">
                                        <label
                                            class="form-label fs-6 fw-semibold text-gray-700">{{ __('Footer') }}</label>
                                        <p class="text-muted fs-7 mb-3">This Input use for Include a URL, company name, or
                                            contact for support.</p>
                                        <input type="text" name="footer" id="footer"
                                            class="form-control form-control-solid" placeholder="Enter footer text"
                                            value="{{ $Paymenttemplate ? $Paymenttemplate->footer : '' }}">
                                    </div>

                                    <div class="table-responsive mb-10">
                                        <table class="table table-row-bordered table-row-gray-200 align-middle gs-0 gy-4">
                                            <thead class="bg-light">
                                                <tr class="fw-bold text-muted">
                                                    <th class="min-w-200px">Method</th>
                                                    <th class="min-w-100px">Enabled</th>
                                                    <th class="min-w-250px">Configuration</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-5">
                                                                <img src="https://www.keyivr.com/wp-content/uploads/WhatsApp-logo-1.png"
                                                                    class="h-30px" alt="">
                                                            </div>
                                                            <div class="d-flex flex-column">
                                                                <span class="text-gray-800 fw-bold">WhatsApp Pay</span>
                                                                <span class="text-muted">Allow customers to securely pay
                                                                    via whatsapp pay</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="radio"
                                                                id="whatsapp_payment" value="0" name="payment_type"
                                                                {{ $Paymenttemplate ? ($Paymenttemplate->payment_type == 0 ? 'checked' : '') : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="payment_configuration"
                                                            id="payment_configuration"
                                                            class="form-control form-control-solid"
                                                            placeholder="WhatsApp payment configuration"
                                                            value="{{ $Paymenttemplate ? $Paymenttemplate->payment_configuration : '' }}">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="symbol symbol-50px me-5">
                                                                <img src="https://sellonboard.com/wp-content/uploads/2021/09/razorpay.png"
                                                                    class="h-30px" alt="">
                                                            </div>
                                                            <div class="d-flex flex-column">
                                                                <span class="text-gray-800 fw-bold">Razorpay</span>
                                                                <span class="text-muted">Allow customers to securely pay
                                                                    via Razorpay (Credit/Debit Cards, NetBanking, UPI,
                                                                    Wallets)</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-check form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="radio"
                                                                id="other_payment" value="1" name="payment_type"
                                                                {{ $Paymenttemplate ? ($Paymenttemplate->payment_type == 1 ? 'checked' : '') : '' }}>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <input type="text" name="payment_configuration_other"
                                                            id="payment_configuration_other"
                                                            class="form-control form-control-solid"
                                                            placeholder="Razorpay configuration"
                                                            value="{{ $Paymenttemplate ? $Paymenttemplate->payment_configuration_other : '' }}">
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="d-flex justify-content-end pt-5">
                                        <button type="submit" class="btn btn-primary px-6 py-3">
                                            <span class="indicator-label">Update Payment Settings</span>
                                            <span class="indicator-progress">Please wait...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end::Tab Pane-->

                    <!--begin::Tab Pane-->
                    <div class="tab-pane fade" id="catalogs-shipping" role="tabpanel"
                        aria-labelledby="catalogs_shipping">
                        <div class="card">
                            <form id="restorant-apps-form" method="post" autocomplete="off"
                                enctype="multipart/form-data" action="{{ route('catalog.settingUpdate') }}">
                                @csrf
                                <div class="card-body p-9">
                                    <h3 class="fw-bold text-gray-900 mb-8">Shipping & Discount Settings</h3>
                                    <p class="text-muted fs-6 mb-8">Configure your shipping and discount options below.</p>

                                    <!-- Shipping Section -->
                                    <div class="mb-10">
                                        <!-- Shipping Methods -->
                                        <div class="mb-10">
                                            <h4 class="fw-bold text-gray-700 mb-6">Shipping Methods</h4>
                                            <p class="text-muted fs-6 mb-6">Select which shipping methods you want to offer
                                                to customers. At least one method must be enabled.</p>

                                            <div class="form-check form-check-custom form-check-solid mb-5">
                                                <input class="form-check-input shipping-method" type="checkbox"
                                                    name="enable_self_pickup" id="enable_self_pickup" value="1"
                                                    {{ $Paymenttemplate && $Paymenttemplate->enable_self_pickup ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_self_pickup">Self
                                                    Pickup</label>
                                            </div>

                                            <div class="form-check form-check-custom form-check-solid mb-5">
                                                <input class="form-check-input shipping-method" type="checkbox"
                                                    name="enable_in_store" id="enable_in_store" value="1"
                                                    {{ $Paymenttemplate && $Paymenttemplate->enable_in_store ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_in_store">inStore</label>
                                            </div>

                                            <div class="form-check form-check-custom form-check-solid mb-10">
                                                <input class="form-check-input shipping-method" type="checkbox"
                                                    name="enable_delivery" id="enable_delivery" value="1"
                                                    {{ $Paymenttemplate && $Paymenttemplate->enable_delivery ? 'checked' : '' }}>
                                                <label class="form-check-label" for="enable_delivery">Delivery</label>
                                            </div>
                                        </div>
                                        <!-- Free Shipping Toggle -->
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input h-30px w-50px" type="checkbox"
                                                    id="isShippingFree" name="isShippingFree" value="1"
                                                    {{ $Paymenttemplate && $Paymenttemplate->isShippingFree ? 'checked' : '' }}>
                                                <label class="form-check-label ms-3" for="isShippingFree">
                                                    Free Shipping
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Free Shipping Threshold -->
                                        <div class="mb-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">
                                                Free Shipping Minimum Order Amount
                                            </label>
                                            <input type="number" name="shipping_free_from_amount"
                                                class="form-control form-control-solid" placeholder="0.00"
                                                value="{{ $Paymenttemplate ? $Paymenttemplate->shipping_free_from_amount : '' }}"
                                                step="0.01">
                                            <div class="form-text">Enter the minimum order amount for free shipping</div>
                                        </div>

                                        <!-- Shipping Amount -->
                                        <div class="mb-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">
                                                {{ __('Shipping Amount') }}
                                            </label>
                                            <div class="input-group input-group-solid">
                                                <span
                                                    class="input-group-text">{{ $Paymenttemplate->currency_symbol ?? '₹' }}</span>
                                                <input type="number" name="shipping" id="shipping"
                                                    class="form-control form-control-solid" placeholder="0.00"
                                                    value="{{ $Paymenttemplate ? $Paymenttemplate->shipping : '' }}"
                                                    step="0.01">
                                            </div>
                                        </div>

                                        <!-- Shipping Description -->
                                        <div class="mb-10">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">
                                                {{ __('Shipping Description') }}
                                            </label>
                                            <input type="text" name="shipping_description" id="shipping_description"
                                                class="form-control form-control-solid"
                                                placeholder="Enter shipping description"
                                                value="{{ $Paymenttemplate ? $Paymenttemplate->shipping_description : '' }}">
                                        </div>
                                    </div>

                                    <div class="separator my-10"></div>

                                    <!-- Discount Settings Section -->
                                    <div class="mb-10">
                                        <h4 class="fw-bold text-gray-700 mb-6">Discount Settings</h4>

                                        <!-- Automatic Discount Toggle -->
                                        <div class="d-flex align-items-center mb-6">
                                            <div class="form-check form-switch form-check-custom form-check-solid">
                                                <input class="form-check-input h-30px w-50px" type="checkbox"
                                                    id="isDiscountAutoApply" name="isDiscountAutoApply" value="1"
                                                    {{ $Paymenttemplate && $Paymenttemplate->isDiscountAutoApply ? 'checked' : '' }}>
                                                <label class="form-check-label ms-3" for="isDiscountAutoApply">
                                                    Automatically Apply Discount
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Discount Threshold -->
                                        <div class="mb-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">
                                                Discount Minimum Order Amount
                                            </label>
                                            <input type="number" name="discount_from_amount"
                                                class="form-control form-control-solid" placeholder="0.00"
                                                value="{{ $Paymenttemplate ? $Paymenttemplate->discount_from_amount : '' }}"
                                                step="0.01">
                                            <div class="form-text">Enter the minimum order amount for automatic discount
                                            </div>
                                        </div>

                                        <!-- Discount Type -->
                                        <div class="mb-6">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">Discount Type</label>
                                            <div class="d-flex align-items-center mt-2">
                                                <div class="form-check form-check-custom form-check-solid me-10">
                                                    <input class="form-check-input" type="radio" name="discount_type"
                                                        id="discount_percentage" value="percent"
                                                        {{ ($Paymenttemplate->discount_type ?? 'percent') == 'percent' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="discount_percentage">
                                                        Percentage
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input" type="radio" name="discount_type"
                                                        id="discount_fixed" value="fixed"
                                                        {{ ($Paymenttemplate->discount_type ?? '') == 'fixed' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="discount_fixed">
                                                        Fixed Amount
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Default Discount -->
                                        <div class="mb-10">
                                            <label class="form-label fs-6 fw-semibold text-gray-700">Default
                                                Discount</label>
                                            <input type="number" name="default_discount"
                                                class="form-control form-control-solid"
                                                value="{{ $Paymenttemplate->default_discount ?? 0 }}" min="0"
                                                step="0.01" placeholder="0.00">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end pt-5">
                                        <button type="submit" class="btn btn-primary px-6 py-3">
                                            <span class="indicator-label">Update Settings</span>
                                            <span class="indicator-progress">Please wait...
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!--end::Tab Pane-->
                </div>
                <!--end::Tab Content-->
            </div>
            <!--end::Col-->
        </div>
        <!--end::Row-->
    </div>
    <!--end::Container-->
@endsection

@section('topjs')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const syncButton = document.getElementById("syncCatalogBtn");

            syncButton.addEventListener("click", function(e) {
                e.preventDefault();
                syncButton.disabled = true;
                syncButton.innerHTML =
                    `<i class="ki-duotone ki-reload fs-3 me-1 spinner-border spinner-border-sm"></i> Syncing...`;

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
                            `<i class="ki-duotone ki-reload fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> Sync Now`;

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
                        syncButton.innerHTML =
                            `<i class="ki-duotone ki-reload fs-3 me-1"><span class="path1"></span><span class="path2"></span></i> Sync Now`;
                        toastr.error("Server error. Please try again later.");
                    });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
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
                    //     confirmButtonColor: '#502e4c',
                    // }).then(() => {
                    //     $(this).prop('checked', true);
                    // });
                }
            });

            // Form submission validation for shipping methods
            $('form').submit(function(e) {
                const checkedCount = $('.shipping-method:checked').length;

                if (checkedCount === 0) {
                    e.preventDefault();
                    // Swal.fire({
                    //     icon: 'error',
                    //     title: 'Validation Error',
                    //     text: 'Please enable at least one shipping method.',
                    //     confirmButtonColor: '#502e4c',
                    // });
                    return false;
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
                    confirmButtonColor: '#502e4c',
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
                    confirmButtonColor: '#502e4c',
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

            // Form submission handling
            $('form').submit(function(e) {
                e.preventDefault();

                // Show loading state on submit button
                const submitBtn = $(this).find('.indicator-label');
                const progress = $(this).find('.indicator-progress');

                submitBtn.hide();
                progress.show();

                // Get form data
                const formData = new FormData(this);

                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // Show success toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Settings saved successfully!',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    },
                    error: function(xhr) {
                        // Show error toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error saving settings',
                            text: xhr.responseJSON?.message || 'Please try again',
                            showConfirmButton: false,
                            timer: 5000
                        });
                    },
                    complete: function() {
                        // Restore button state
                        submitBtn.show();
                        progress.hide();
                    }
                });
            });
        });
    </script>
@endsection
