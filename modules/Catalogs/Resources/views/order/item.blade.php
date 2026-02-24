@extends('general.index', $setup)

@section('content')
    @php
        $freezeOperations =
            in_array($order->status, ['delivered', 'dispatched', 'preparing', 'ready_to_dispatch', 'canceled']) ||
            in_array($order->payment_status, ['Refunded', 'Failed']);
        $freezeSpecific =
            in_array($order->status, ['delivered', 'dispatched', 'preparing', 'ready_to_dispatch', 'canceled']);
    @endphp
    <div class="header bg-gradient-primary pb-6 pt-5 pt-md-6">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-dark breadcrumb-transparent bg-transparent mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('catalog.orderIndex') }}">{{ __('Orders') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">#{{ $order->reference_id }}</li>
                        </ol>
                    </nav>
                    <h1 class="h2 text-white mb-0 mt-2">{{ __('Order') }} #{{ $order->reference_id }}</h1>
                    <p class="text-white opacity-8 mb-0">{{ $order->user_name }} · {{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div class="col-auto">
                    <a href="{{ route('catalog.orderIndex') }}" class="btn btn-sm btn-white">
                        <i class="ni ni-bold-left mr-2"></i>{{ __('Back to Orders') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt--6 px-lg-4">
        @include('partials.flash')

        @php
            $statusTexts = [
                'order' => 'Pending',
                'accepted' => 'Accepted',
                'preparing' => 'Preparing',
                'ready_to_dispatch' => 'Ready to Dispatch',
                'dispatched' => 'Dispatched',
                'delivered' => 'Delivered',
                'canceled' => 'Canceled',
            ];
            $statusLabel = $statusTexts[$order->status] ?? ucwords(str_replace('_', ' ', $order->status));
            $finalAmount = ($order->subtotal_offset ?? 1) != 0 ? $order->subtotal_value / $order->subtotal_offset : 0;
            $shipping = $order->shipping_cast ?? 0;
            $discountType = $order->discount_type ?? null;
            $discountValue = $order->discount ?? 0;
            $discountAmount = 0;
            if ($discountType && $discountValue > 0) {
                $discountAmount = $discountType === 'percent' ? ($finalAmount * $discountValue) / 100 : min($discountValue, $finalAmount);
            }
            $orderTotal = $finalAmount - $discountAmount + $shipping;
        @endphp

        <!-- Summary strip -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card card-body shadow-sm py-3 border-0">
                    <div class="row align-items-center">
                        <div class="col-md-3 col-6 mb-2 mb-md-0">
                            <span class="text-muted small d-block">{{ __('Status') }}</span>
                            <span class="badge badge-{{ $order->status == 'canceled' ? 'danger' : ($order->status == 'delivered' ? 'success' : 'primary') }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="col-md-3 col-6 mb-2 mb-md-0">
                            <span class="text-muted small d-block">{{ __('Payment') }}</span>
                            <span class="badge badge-{{ ($order->payment_status ?? '') === 'Paid' ? 'success' : 'warning' }}">{{ $order->payment_status ?? 'Pending' }}</span>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="text-muted small d-block">{{ __('Order total') }}</span>
                            <span class="h5 mb-0 text-dark font-weight-bold">₹{{ number_format($orderTotal, 2) }}</span>
                        </div>
                        <div class="col-md-3 col-6 text-md-right">
                            <a href="{{ route('catalog.itemEdit', $order->id) }}" class="btn btn-sm btn-outline-primary">{{ __('Edit Address') }}</a>
                            <button type="button" class="btn btn-sm btn-primary ml-1" data-toggle="modal" data-target="#printInvoiceModal" data-order-id="{{ $order->id }}"><i class="ni ni-printer mr-1"></i> {{ __('Print') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status alert (canceled / delivered only) -->
        <div id="status-bar-container" class="mb-4">
            @if ($order->status == 'canceled')
                <div class="alert alert-danger py-3 mb-0">
                    <i class="ni ni-notification-70 me-2"></i>
                    <strong>{{ __('Order Cancelled') }}</strong> — {{ $order->cancel_reason ?? 'N/A' }}
                    <span class="text-muted">· {{ \Carbon\Carbon::parse($order->updated_at)->format('d M Y, H:i') }}</span>
                </div>
            @elseif ($order->status == 'delivered')
                <div class="alert alert-success py-3 mb-0">
                    <i class="ni ni-check-bold me-2"></i>
                    <strong>{{ __('Delivered') }}</strong> — {{ __('Tracking') }}: {{ $order->tracking_number ?? 'N/A' }}
                    @if($order->delivery_datetime)
                        <span class="text-muted">· {{ \Carbon\Carbon::parse($order->delivery_datetime)->format('d M Y, H:i') }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="row">
            <!-- Left: Customer, Payment, Status, Shipping -->
            <div class="col-lg-5 col-xl-4 order-2 order-lg-1">
                @if ($order->status == 'delivered')
                    <div class="card shadow-sm mb-4">
                        <div class="card-body py-4">
                            <h5 class="font-weight-bold text-dark mb-3">{{ __('Delivery Information') }}</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ni ni-delivery-fast text-success mr-3" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <span class="font-weight-bold">{{ $order->delivery_partner ?? 'N/A' }}</span>
                                            <span class="text-muted small d-block">{{ __('Delivery partner') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ni ni-box-2 text-info mr-3" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <span class="font-weight-bold">{{ $order->tracking_number ?? 'N/A' }}</span>
                                            <span class="text-muted small d-block">{{ __('Tracking number') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ni ni-calendar-grid-58 text-warning mr-3" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <span class="font-weight-bold">
                                                @if ($order->delivery_datetime)
                                                    {{ \Carbon\Carbon::parse($order->delivery_datetime)->format('d M Y, H:i') }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                            <span class="text-muted small d-block">{{ __('Delivery date & time') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Order Details Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-transparent border-0 py-4">
                        <div class="card-title mb-0">
                            <span class="font-weight-bold text-dark">{{ __('Order Details') }}</span>
                            <span class="text-muted small d-block">#{{ $order->reference_id }}</span>

                            <!-- WhatsApp Window Status -->
                            @if ($lastContactReply)
                                <div class="mt-2" id="whatsapp-window-container">
                                    @if ($windowStatus === 'expired')
                                        <span class="badge badge-light-danger fs-8">
                                            <i class="bi bi-clock-history me-2"></i>
                                            Free message window expired
                                        </span>
                                        <!-- Action Buttons Container -->
                                        <div class="mt-2 d-flex gap-2">
                                            <button id="refresh-window-btn" class="btn btn-sm btn-light-primary">
                                                <i class="ni ni-refresh"></i>
                                            </button>
                                            <button id="send-template-btn" class="btn btn-sm btn-light-success">
                                                <i class="ni ni-send me-1"></i> Send Template Message
                                            </button>
                                        </div>
                                    @elseif(is_array($windowStatus))
                                        <span class="badge badge-light-success fs-8">
                                            <i class="bi bi-clock me-2"></i>
                                            Free messages available:
                                            {{ $windowStatus['remaining']->h }}h
                                            {{ $windowStatus['remaining']->i }}m remaining
                                        </span>
                                        <span class="text-muted fs-8 ms-2">
                                            (Expires: {{ $windowStatus['expires_at']->format('M j, H:i') }})
                                        </span>
                                    @endif
                                    <div class="text-muted fs-8 mt-1">
                                        Last client reply:
                                        {{ \Carbon\Carbon::parse($lastContactReply)->format('M j, Y H:i') }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <form id="order-update-form" method="post" autocomplete="off" enctype="multipart/form-data"
                            action="{{ route('catalog.orderUpdate', $order->id) }}">
                            @csrf
                            <div class="d-flex flex-column gap-5">
                                <!-- Customer Details -->
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-0">{{ __('Customer Information') }}</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-toggle="modal" data-target="#contactModal"
                                            @if ($freezeOperations) disabled @endif>
                                            <i class="ni ni-ruler-pencil mr-1"></i> {{ __('Edit Contact') }}
                                        </button>
                                    </div>

                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ni ni-single-02 text-primary mr-3" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <span class="font-weight-bold customer-name-display">{{ $order->user_name }}</span>
                                            <span class="text-muted small d-block">{{ __('Customer') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ni ni-mobile-button text-success mr-3" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <span class="font-weight-bold">{{ $order->phone_number }}</span>
                                            <span class="text-muted small d-block">{{ __('Phone') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ni ni-calendar-grid-58 text-info mr-3" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <span class="font-weight-bold">{{ $order->created_at->format('d M Y') }}</span>
                                            <span class="text-muted small d-block">{{ __('Order date') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <i class="ni ni-single-copy-04 text-warning mr-3" style="font-size: 1.25rem;"></i>
                                        <div>
                                            <span class="font-weight-bold">{{ $order->reference_id }}</span>
                                            <span class="text-muted small d-block">{{ __('Order number') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details -->
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-0">{{ __('Payment Details') }}</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-toggle="modal" data-target="#paymentModal"
                                            @if ($freezeSpecific) disabled @endif>
                                            <i class="ni ni-ruler-pencil mr-1"></i> {{ __('Edit Payment') }}
                                        </button>
                                    </div>

                                    <div class="bg-light-primary rounded p-5 mb-4">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <div class="fw-bold">Transaction ID:</div>
                                                <div>{{ $order->transaction_id ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Payment Method:</div>
                                                <div>{{ $order->transaction_type ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Payment Status:</div>
                                                <div>
                                                    <span
                                                        class="badge badge-light-{{ $order->payment_status === 'Paid' ? 'success' : 'danger' }}">
                                                        {{ $order->payment_status ?? 'Not specified' }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Currency:</div>
                                                <div>{{ $order->currency ?? 'Not specified' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Status Section -->
                                <div class="d-flex flex-column mb-5">
                                    <h5 class="font-weight-bold text-dark mb-3">{{ __('Order Status') }}</h5>
                                    <div class="d-flex align-items-center gap-5">
                                        <div class="w-100">
                                            <select class="form-control" id="order-status-select"
                                                data-order-id="{{ $order->id }}"
                                                @if (in_array($order->payment_status, ['Refunded', 'Failed'])) disabled @endif>
                                                <option value="order" {{ $order->status == 'order' ? 'selected' : '' }}>
                                                    Pending</option>
                                                <option value="accepted"
                                                    {{ $order->status == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                                <option value="preparing"
                                                    {{ $order->status == 'preparing' ? 'selected' : '' }}>Preparing
                                                </option>
                                                <option value="ready_to_dispatch"
                                                    {{ $order->status == 'ready_to_dispatch' ? 'selected' : '' }}>Ready to
                                                    Dispatch</option>
                                                <option value="dispatched"
                                                    {{ $order->status == 'dispatched' ? 'selected' : '' }}>Dispatched
                                                </option>
                                                <option value="delivered"
                                                    {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered
                                                </option>
                                            </select>
                                        </div>
                                        <div>
                                            <button type="button"
                                                class="btn btn-sm btn-success"
                                                data-toggle="modal" data-target="#printInvoiceModal"
                                                data-order-id="{{ $order->id }}" title="{{ __('Print Invoice') }}">
                                                <i class="ni ni-printer mr-1"></i> {{ __('Print') }}
                                            </button>
                                        </div>
                                    </div>
                                    <div id="status-message" class="mt-2"></div>
                                </div>

                                <!-- Shipping Information -->
                                <div class="d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-0">{{ __('Shipping Information') }}</h5>
                                        <div class="d-flex gap-2">
                                        <a href="{{ route('catalog.itemEdit', $order->id) }}" class="btn btn-sm btn-primary">
                                            <i class="ni ni-ruler-pencil me-1"></i> {{ __('Edit Address') }}
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                            data-toggle="modal" data-target="#shippingModal"
                                            @if ($freezeSpecific) disabled @endif>
                                            <i class="ni ni-delivery-fast me-1"></i> {{ __('Edit Shipping') }}
                                        </button>
                                    </div>
                                    </div>

                                    <div class="bg-light-primary rounded p-5 mb-5">
                                        <div class="row g-4">
                                            <div class="col-md-12">
                                                <div class="fw-bold">Shipping Method: <span
                                                        class="badge badge-light-success fs-6">{{ $order->shipping_method ?? 'Not specified' }}</span>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="fw-bold">Full Address:</div>
                                                <div>{{ $order->address ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Name:</div>
                                                <div>{{ $order->for_person ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Phone number:</div>
                                                <div>{{ $order->for_person_number ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">City:</div>
                                                <div>{{ $order->city ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">House Number:</div>
                                                <div>{{ $order->house_number ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Building Name:</div>
                                                <div>{{ $order->building_name ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Landmark:</div>
                                                <div>{{ $order->landmark_area ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Pin Code:</div>
                                                <div>{{ $order->pin_code ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Tower number:</div>
                                                <div>{{ $order->tower_number ?? 'Not specified' }}</div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="fw-bold">Delivery Note:</div>
                                                <div>{{ $order->delivery_note ?? 'No notes' }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Delivery Agent -->
                                    <div class="d-flex flex-column mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="font-weight-bold text-dark mb-0">{{ __('Delivery Agent') }}</h6>
                                        </div>
                                        <form method="post" action="{{ route('catalog.assignDeliveryAgent', $order) }}" class="form-inline">
                                            @csrf
                                            <div class="form-group mr-2 mb-2">
                                                <select name="delivery_agent_id" class="form-control form-control-sm" @if($freezeOperations) disabled @endif>
                                                    <option value="">{{ __('Not assigned') }}</option>
                                                    @foreach($deliveryAgents as $agent)
                                                        <option value="{{ $agent->id }}" @if($order->delivery_agent_id == $agent->id) selected @endif>
                                                            {{ $agent->name }} @if($agent->phone) ({{ $agent->phone }}) @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-primary mb-2" @if($freezeOperations) disabled @endif>
                                                <i class="ni ni-check-bold mr-1"></i> {{ __('Save') }}
                                            </button>
                                        </form>
                                    </div>

                                    <button type="button" class="btn btn-sm btn-light-success ms-2"
                                        id="resendAddressFormBtn" @if ($freezeOperations) disabled @endif>
                                        <i class="ni ni-refresh me-1"></i>
                                        {{ __('Re-request Address') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right: Order Items + Instructions (main content first on desktop) -->
            <div class="col-lg-7 col-xl-8 order-1 order-lg-2">
                <div id="order-items-section" class="mb-4">
                    @include('Catalogs::order.partials.order_items_section')
                </div>
                <!-- Order Instructions -->
                <div class="card shadow-sm mb-4 order-instructions-card">
                    <div class="card-header bg-transparent border-0 py-4">
                        <h3 class="card-title mb-0">
                            <span class="font-weight-bold text-dark">{{ __('Order Instructions') }}</span>
                            <span class="text-muted small d-block mt-1">{{ __('Special requests from customer') }}</span>
                        </h3>
                    </div>
                    <div class="card-body pt-0">
                        @if ($order->order_process_note)
                            <p class="mb-0 text-dark">{{ $order->order_process_note }}</p>
                        @else
                            <p class="mb-0 text-muted">{{ __('No special instructions provided.') }}</p>
                        @endif
                        @if (!$freezeOperations)
                            <div class="mt-3">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#orderNoteModal">
                                    <i class="ni ni-ruler-pencil mr-1"></i> {{ __('Update Instructions') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Modals -->
    <!-- ... All modals remain unchanged ... -->

    <!-- Shipping Details Modal -->
    <div class="modal fade" id="shippingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="shipping-form" method="post" action="{{ route('catalog.updateShipping', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">Update Shipping Details</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="mb-5">
                            <label class="form-label fs-6 fw-semibold text-gray-700">Shipping Method</label>
                            <select class="form-control" name="shipping_method" id="shipping_method">
                                <option value="">Select Method</option>
                                <option value="Self Pickup"
                                    {{ $order->shipping_method == 'Self Pickup' ? 'selected' : '' }}>Self Pickup</option>
                                <option value="inStore" {{ $order->shipping_method == 'inStore' ? 'selected' : '' }}>
                                    inStore
                                </option>
                                <option value="Delivery" {{ $order->shipping_method == 'Delivery' ? 'selected' : '' }}>
                                    Delivery
                                </option>
                                <option value="Express Shipping"
                                    {{ $order->shipping_method == 'Express Shipping' ? 'selected' : '' }}>Express Shipping
                                </option>
                                <option value="Courier" {{ $order->shipping_method == 'Courier' ? 'selected' : '' }}>
                                    Courier</option>
                            </select>
                        </div>

                        <div id="address-fields">
                            <!-- Add tracking number field -->
                            <div class="mb-5">
                                <label class="form-label fs-6 fw-semibold text-gray-700">Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control"
                                    placeholder="Enter tracking number" value="{{ $order->tracking_number ?? '' }}">
                            </div>
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Name</label>
                                    <input type="text" name="for_person" class="form-control"
                                        placeholder="BRIJ" value="{{ $order->for_person }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Phone number</label>
                                    <input type="text" name="for_person_number"
                                        class="form-control" placeholder="+9199XXXXXXXXX"
                                        value="{{ $order->for_person_number }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Full Address</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Full Address" required>{{ $order->address }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">House Number
                                        (Optional)</label>
                                    <input type="text" name="house_number" class="form-control"
                                        placeholder="House Number" value="{{ $order->house_number }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Building Name
                                        (Optional)</label>
                                    <input type="text" name="building_name" class="form-control"
                                        placeholder="Building Name" value="{{ $order->building_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Pin Code</label>
                                    <input type="text" name="pin_code" class="form-control"
                                        placeholder="Pin Code" value="{{ $order->pin_code }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Tower number
                                        (Optional)</label>
                                    <input type="text" name="tower_number" class="form-control"
                                        placeholder="Tower Number" value="{{ $order->tower_number }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Landmark/Area</label>
                                    <input type="text" name="landmark_area" class="form-control"
                                        placeholder="Landmark Area" value="{{ $order->landmark_area }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">City</label>
                                    <input type="text" name="city" class="form-control"
                                        placeholder="City" value="{{ $order->city }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">State</label>
                                    <input type="text" name="state" class="form-control"
                                        placeholder="State" value="{{ $order->state }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="update-shipping-btn">
                            <span class="indicator-label">Update Shipping</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="contact-form" method="post" action="{{ route('catalog.updateContact', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">Update Contact Information</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="row g-5">
                            <div class="col-md-12">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Customer Name</label>
                                <input type="text" name="user_name" class="form-control"
                                    placeholder="Customer Name" value="{{ $order->user_name }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="update-contact-btn">
                            <span class="indicator-label">Update Contact</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="payment-form" method="post" action="{{ route('catalog.updatePayment', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">Update Payment Details</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="row g-5">
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700">Transaction ID</label>
                                <input type="text" name="transaction_id" class="form-control"
                                    placeholder="Transaction ID" value="{{ $order->transaction_id }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Payment Method</label>
                                <select class="form-control" name="transaction_type" required>
                                    <option value="">Select Method</option>
                                    <option value="UPI" {{ $order->transaction_type == 'UPI' ? 'selected' : '' }}>UPI
                                    </option>
                                    <option value="CASH" {{ $order->transaction_type == 'CASH' ? 'selected' : '' }}>Cash
                                    </option>
                                    <option value="BANK Transfer"
                                        {{ $order->transaction_type == 'BANK Transfer' ? 'selected' : '' }}>Bank Transfer
                                    </option>
                                    <option value="Card" {{ $order->transaction_type == 'Card' ? 'selected' : '' }}>Card
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Payment Status</label>
                                <select class="form-control" name="payment_status" required>
                                    <option value="">Select Status</option>
                                    <option value="Pending" {{ $order->payment_status == 'Pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="Paid" {{ $order->payment_status == 'Paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="Failed" {{ $order->payment_status == 'Failed' ? 'selected' : '' }}>
                                        Failed</option>
                                    <option value="Refunded" {{ $order->payment_status == 'Refunded' ? 'selected' : '' }}>
                                        Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700">Currency</label>
                                <input type="text" name="currency" class="form-control"
                                    placeholder="Currency" value="{{ $order->currency ?? 'INR' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="update-payment-btn">
                            <span class="indicator-label">Update Payment</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Dispatch Modal -->
    <div class="modal fade" id="dispatchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="dispatch-form" method="post" autocomplete="off" enctype="multipart/form-data"
                    action="{{ route('catalog.sendOrderDispatch', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">Dispatch Order #{{ $order->reference_id }}</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700 required">Message</label>
                            <textarea rows="4" name="order_mess" class="form-control"
                                placeholder="Enter dispatch message" required>{{ $Paymenttemplate ? $Paymenttemplate->order_message : '' }}</textarea>
                        </div>

                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700 required">Tracking Number</label>
                            <input type="text" name="number" class="form-control"
                                placeholder="Enter tracking number" value="{{ $order->tracking_number ?? '' }}" required>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <span class="indicator-label">Dispatch Order</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add this modal for cancellation reason -->
    <!-- Update your cancelReasonModal with this code -->
    <div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Cancel Order #{{ $order->reference_id }}</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="cancel-form">
                    <div class="modal-body py-10 px-lg-17">
                        <!-- Reason Selection Buttons -->
                        <div class="mb-5">
                            <label class="form-label fs-6 fw-semibold text-gray-700 mb-4">Select a reason:</label>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @foreach (['Changed my mind', 'Found better price elsewhere', 'Item no longer needed', 'Shipping too slow', 'Shipping cost too high', 'Ordered by mistake', 'Item out of stock', 'Product specifications changed', 'Payment issues', 'Duplicate order', 'Delivery address issues', 'Other reason'] as $reason)
                                    <button type="button"
                                        class="btn btn-outline btn-outline-dashed btn-outline-primary reason-btn"
                                        data-reason="{{ $reason }}">
                                        {{ $reason }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Reason Textarea -->
                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700 required">Reason for
                                Cancellation</label>
                            <textarea rows="4" name="cancel_reason" id="cancel-reason-text" class="form-control"
                                placeholder="Enter reason for cancellation" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <span class="indicator-label">Confirm Cancellation</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add this modal for delivery confirmation -->
    <div class="modal fade" id="deliveryConfirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="fw-bold">Confirm Delivery for Order #{{ $order->reference_id }}</h2>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="mb-10">
                        <label class="form-label fs-6 fw-semibold text-gray-700 required">Delivery Note</label>
                        <textarea rows="4" id="delivery-note-text" class="form-control"
                            placeholder="Enter delivery notes (e.g., left at front desk)" required></textarea>
                    </div>
                    <div class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" id="send-whatsapp" checked />
                        <label class="form-check-label" for="send-whatsapp">
                            Send WhatsApp notification to customer
                        </label>
                    </div>
                </div>
                <div class="modal-footer flex-center">
                    <button type="button" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirm-delivery-btn">
                        <span class="indicator-label">Confirm Delivery</span>
                        <span class="indicator-progress">Please wait...
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <!-- Add this modal for updating order process note -->
    <div class="modal fade" id="orderNoteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="order-note-form" method="post" action="{{ route('catalog.updateOrderNote', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">Update Order Instructions</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700">Special Instructions</label>
                            <textarea rows="4" name="order_process_note" class="form-control"
                                placeholder="Enter special instructions (e.g., prepare without spices, extra sauce)">{{ $order->order_process_note }}</textarea>
                            <div class="form-text">These notes will be visible to kitchen staff and delivery personnel
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="update-note-btn">
                            <span class="indicator-label">Update Instructions</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Options Modal -->
    <div class="modal fade" id="printInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Print Format</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-5">
                        <!-- Thermal Printer Option -->
                        <div class="col-md-6">
                            <label class="d-block text-center">
                                <input type="radio" name="printType" value="thermal" class="d-none" checked>
                                <div class="card card-dashed cursor-pointer h-100 print-option" data-value="thermal">
                                    <div class="card-body text-center p-5">
                                        <div class="mb-5">
                                            <i class="ni ni-printer text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="fs-5 fw-bold">Thermal Printer</div>
                                        <div class="fs-7 text-muted">80mm Receipt</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- A4 Printer Option -->
                        <div class="col-md-6">
                            <label class="d-block text-center">
                                <input type="radio" name="printType" value="full" class="d-none">
                                <div class="card card-dashed cursor-pointer h-100 print-option" data-value="full">
                                    <div class="card-body text-center p-5">
                                        <div class="mb-5">
                                            <i class="ni ni-single-copy-04 text-success" style="font-size: 2rem;"></i>
                                        </div>
                                        <div class="fs-5 fw-bold">A4 Print</div>
                                        <div class="fs-7 text-muted">Standard Document</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button id="confirmPrintBtn" type="button" class="btn btn-primary">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-gray-800">Add Products to Order #{{ $order->reference_id }}</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Search Bar -->
                    <div class="mb-5">
                        <div class="input-group input-group-solid">
                            <span class="input-group-text bg-light">
                                <i class="ni ni-zoom-split-in text-muted"></i>
                            </span>
                            <input type="text" id="productSearch" class="form-control"
                                placeholder="Search products..." />
                        </div>
                    </div>

                    <!-- Product Grid -->
                    <div class="row g-4" id="productList">
                        @foreach ($products as $product)
                            <div class="col-md-3 product-item">
                                <div class="card h-100">
                                    <div class="card-body p-3 text-center">
                                        <div class="card-rounded-top overflow-hidden" style="height: 180px;">
                                            <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}"
                                                class="w-100 h-100 object-fit-cover" />
                                        </div>
                                        <h6 class="card-title mb-1 mt-2">{{ $product->product_name }}</h6>
                                        <p class="text-muted mb-2">{{ $product->price }}</p>
                                        <button class="btn btn-sm btn-primary add-product-btn"
                                            data-product-id="{{ $product->id }}"
                                            data-retailer-id="{{ $product->retailer_id }}"
                                            data-product-name="{{ $product->product_name }}"
                                            data-product-price="{{ $product->price }}">
                                            <i class="ni ni-fat-add mr-1"></i> {{ __('Add') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-gray-800">Edit Order Item</h5>
                    <button type="button" class="btn-close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-item-form">
                        @csrf
                        <input type="hidden" name="item_id" id="edit-item-id">

                        <div class="mb-5">
                            <label class="form-label fs-6 fw-semibold text-gray-700 required">Product</label>
                            <div class="fw-bold" id="edit-product-name"></div>
                        </div>

                        <div class="row g-5">
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Quantity</label>
                                <input type="number" name="quantity" id="edit-item-quantity"
                                    class="form-control" min="1" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Price (₹)</label>
                                <input type="number" name="price" id="edit-item-price" step="0.01"
                                    class="form-control" min="0.01" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save-item-btn">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('topjs')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const receiptUrlTemplate = "{{ route('catalog.receipt', ['id' => ':id']) }}";
        function printBasicReceipt(orderId) {
            const url = receiptUrlTemplate.replace(':id', orderId);
            window.open(url, '_blank');
        }
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('printInvoiceModal');
            const confirmBtn = document.getElementById('confirmPrintBtn');

            // Handle option selection
            const printOptions = document.querySelectorAll('.print-option');
            printOptions.forEach(option => {
                option.addEventListener('click', function() {
                    // Remove active class from all options
                    printOptions.forEach(opt => {
                        opt.classList.remove('active');
                        opt.classList.add('card-dashed');
                    });

                    // Add active class to selected option
                    this.classList.add('active');
                    this.classList.remove('card-dashed');

                    // Check corresponding radio button
                    const value = this.getAttribute('data-value');
                    document.querySelector(`input[name="printType"][value="${value}"]`).checked =
                        true;
                });
            });

            // Set thermal as default active
            document.querySelector('.print-option[data-value="thermal"]').classList.add('active');

            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const orderId = button.getAttribute('data-order-id');
                modal.setAttribute('data-order-id', orderId);
            });

            confirmBtn.addEventListener('click', function() {
                const orderId = modal.getAttribute('data-order-id');
                const printType = document.querySelector('input[name="printType"]:checked').value;
                const url = "{{ route('catalog.pdf', ['id' => ':id']) }}".replace(':id', orderId) +
                    `?size=${printType}`;

                window.open(url, '_blank');
                bootstrap.Modal.getInstance(modal).hide();
            });
        });

        function initTooltips() {
            $('[data-toggle="tooltip"]').tooltip();
        }

        $(document).ready(function() {
            // Initialize tooltips
            initTooltips();

            $('#dispatch-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submit

                let form = $(this);
                let actionUrl = form.attr('action');
                let formData = new FormData(this);

                let submitBtn = form.find('button[type="submit"]');
                submitBtn.prop('disabled', true);
                submitBtn.find('.indicator-label').hide();
                submitBtn.find('.indicator-progress').show();

                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: res.toast ||
                                    'Dispatch message sent successfully',
                                showConfirmButton: false,
                                timer: 3000
                            });

                            $('#dispatchModal').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: res.errMsg || 'Something went wrong.',
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: xhr.responseJSON?.message || 'Unexpected error.',
                        });
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        submitBtn.find('.indicator-label').show();
                        submitBtn.find('.indicator-progress').hide();
                    }
                });

                return false; // <- Prevents fallback submit behavior
            });


            // Add this at the top of your script section
            const orderStatus = "{{ $order->status }}";
            const initialWindowStatus = @json($windowStatus);
            let isFreeWindowActive = initialWindowStatus !== 'expired';
            const serverFreezeOperations = @json($freezeOperations);
            const serverFreezeSpecific = @json($freezeSpecific);
            // Function to freeze UI for free window expiration
            // Update UI freezing logic
            function freezeUIForFreeWindow() {
                // Always freeze if serverFreezeOperations is true

                if (serverFreezeSpecific) {
                    $('[data-target="#paymentModal"]').prop('disabled', false);
                    $('[data-target="#shippingModal"]').prop('disabled', true);
                }


                if (serverFreezeOperations) {
                    $('.btn-edit-shipping').prop('disabled', true);
                    $('.btn-dispatch-order').prop('disabled', true);
                    $('.manual-cancel-order-btn').prop('disabled', true);
                    $('.quantity-btn').prop('disabled', true);
                    $('.add-item-btn').prop('disabled', true);
                    $('.delete-item-btn').prop('disabled', true);
                    $('[data-target="#dispatchModal"]').prop('disabled', true);
                    $('[data-target="#contactModal"]').prop('disabled', true);
                    $('[data-target="#orderNoteModal"]').prop('disabled', true);
                    $('#resendAddressFormBtn').prop('disabled', true);
                    $('#apply-discount-btn').prop('disabled', true);
                    $('#resend-payment-link').prop('disabled', true);
                }
                // Only freeze specific elements if WhatsApp window expired
                else if (!isFreeWindowActive) {
                    $('.btn-edit-shipping').prop('disabled', true);
                    $('.btn-dispatch-order').prop('disabled', true);
                    $('.manual-cancel-order-btn').prop('disabled', true);
                    $('.quantity-btn').prop('disabled', true);
                    $('.add-item-btn').prop('disabled', true);
                    $('.delete-item-btn').prop('disabled', true);
                    $('[data-target="#shippingModal"]').prop('disabled', true);
                    $('[data-target="#dispatchModal"]').prop('disabled', true);
                    $('[data-target="#contactModal"]').prop('disabled', true);
                    $('[data-target="#orderNoteModal"]').prop('disabled', true);
                    $('#resendAddressFormBtn').prop('disabled', true);
                    $('#apply-discount-btn').prop('disabled', true);
                    $('#resend-payment-link').prop('disabled', true);
                }
            }

            // Function to unfreeze UI when free window is active
            function unfreezeUIForFreeWindow() {
                // Only enable if order is not in terminal state AND server freezeOperations is false
                if (orderStatus !== 'delivered' && orderStatus !== 'canceled' && !serverFreezeOperations) {
                    $('.btn-edit-shipping').prop('disabled', false);
                    $('.btn-dispatch-order').prop('disabled', false);
                    $('.manual-cancel-order-btn').prop('disabled', false);
                    $('.quantity-btn').prop('disabled', false);
                    $('.add-item-btn').prop('disabled', false);
                    $('.delete-item-btn').prop('disabled', false);
                    $('[data-target="#shippingModal"]').prop('disabled', false);
                    $('[data-target="#paymentModal"]').prop('disabled', false);
                    $('[data-target="#dispatchModal"]').prop('disabled', false);
                    $('[data-target="#contactModal"]').prop('disabled', false);
                    $('[data-target="#orderNoteModal"]').prop('disabled', false);
                    $('#resendAddressFormBtn').prop('disabled', false);
                    $('#apply-discount-btn').prop('disabled', false);
                    $('#resend-payment-link').prop('disabled', false);
                }
            }

            // Function to update UI state based on free window status
            function updateUIState() {
                // Get current order status from select element
                const currentStatus = $('#order-status-select').val();

                // Determine if we should enable operations
                const enableOperations =
                    isFreeWindowActive &&
                    !serverFreezeOperations &&
                    (currentStatus === 'order' || currentStatus === 'accepted');

                if (enableOperations) {
                    unfreezeUIForFreeWindow();
                } else {
                    freezeUIForFreeWindow();
                }
            }

            // Initialize UI state on page load
            updateUIState();

            // Handle free window expiration
            function handleFreeWindowExpired() {
                isFreeWindowActive = false;
                updateUIState();
            }

            // Handle free window renewal
            function handleFreeWindowRenewed() {
                isFreeWindowActive = true;
                updateUIState();
            }

            // Update the refresh button handler
            $(document).on('click', '#refresh-window-btn', function(e) {
                e.preventDefault();
                const btn = $(this);
                const container = $('#whatsapp-window-container');

                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span> Refreshing...'
                );

                $.ajax({
                    url: "{{ route('catalog.refreshWindowStatus', $order->id) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            container.html(response.html);
                            handleFreeWindowRenewed(); // Call this on successful renewal
                        } else {
                            Swal.fire('Error', response.message || 'Failed to refresh status',
                                'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to refresh status', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="ki-duotone ki-abstract-8"><span class="path1"></span><span class="path2"></span></i>'
                        );
                    }
                });
            });

            // Set expiration timer only if window is active
            if (isFreeWindowActive) {
                // Calculate remaining time from server data
                let freeWindowDuration = 30 * 60 * 1000; // Default 30 minutes

                if (initialWindowStatus.remaining) {
                    const hours = initialWindowStatus.remaining.h || 0;
                    const minutes = initialWindowStatus.remaining.i || 0;
                    const seconds = initialWindowStatus.remaining.s || 0;

                    freeWindowDuration = (hours * 3600 + minutes * 60 + seconds) * 1000;
                }

                setTimeout(() => {
                    handleFreeWindowExpired();
                }, freeWindowDuration);
            }

            // Send WhatsApp template message
            $(document).on('click', '#send-template-btn', function(e) {
                e.preventDefault();
                const btn = $(this);
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span> Sending...'
                );

                $.ajax({
                    url: "{{ route('catalog.sendTemplateMessage', $order->id) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Template Sent!',
                                text: 'Payment template message has been sent to the customer',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Failed to send template',
                                'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to send template', 'error');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="bi bi-send me-2"></i>Send Template Message'
                        );
                    }
                });
            });

            window.updateComponentStates = function() {
                console.log('Updating component states...');

                const currentStatus = $('#order-status-select').val();
                const paymentStatus = "{{ $order->payment_status }}";

                console.log('Current Status:', currentStatus);
                console.log('Payment Status:', paymentStatus);

                // Define arrays for statuses that should freeze components
                const freezeStatuses = ['preparing', 'ready_to_dispatch', 'dispatched', 'delivered'];
                const freezePaymentStatuses = ['Paid', 'Refunded', 'Failed'];

                // Check if current status is in freezeStatuses
                const shouldFreezeStatus = freezeStatuses.includes(currentStatus);

                // Check if payment status is in freezePaymentStatuses
                const shouldFreezePayment = freezePaymentStatuses.includes(paymentStatus);

                // Combine both conditions
                const shouldFreeze = shouldFreezeStatus || shouldFreezePayment;

                console.log('Should freeze components?', shouldFreeze);

                // Apply freeze state to components
                $('.manual-cancel-order-btn').prop('disabled', shouldFreeze);
                $('.quantity-btn').prop('disabled', shouldFreeze);
                $('.add-item-btn').prop('disabled', shouldFreeze);
                $('.delete-item-btn').prop('disabled', shouldFreeze);
                $('#apply-discount-btn').prop('disabled', shouldFreeze);
                $('#resend-payment-link').prop('disabled', shouldFreeze);
                $('#finalize-items-btn').prop('disabled', shouldFreeze);

            };

            // Function to unfreeze all buttons
            function unfreezeButtons() {
                $('button').not('[disabled]').prop('disabled', false);
                $('select').not('[disabled]').prop('disabled', false);
                $('input').not('[disabled]').prop('disabled', false);
            }


            // Order status change handler
            $('#order-status-select').change(function() {
                const orderId = $(this).data('order-id');
                const newStatus = $(this).val();

                // updateComponentStates();

                updateUIState();
                if (newStatus === 'delivered') {
                    // Check payment status
                    let paymentStatus = "{{ $order->payment_status }}";
                    if (paymentStatus !== 'Paid') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Cannot mark as delivered',
                            text: 'Payment has not been received. You cannot mark the order as delivered until payment is paid.',
                        });
                        $(this).val('{{ $order->status }}');
                        return;
                    }

                    // Show delivery note modal instead of SweetAlert
                    $('#deliveryConfirmationModal').modal('show');
                    $(this).val('{{ $order->status }}');
                    return;
                }

                // Automatically open dispatch modal for dispatched status

                // Handle canceled status
                if (newStatus === 'canceled') {
                    // Open cancellation reason modal
                    $('#cancelReasonModal').modal('show');

                    // Revert status until confirmed
                    $(this).val('{{ $order->status }}');
                    return;
                }

                if (newStatus === 'dispatched') {
                    // Build beautiful WhatsApp message
                    const customerName = "{{ $order->user_name }}";
                    const orderId = "{{ $order->reference_id }}";
                    let itemsText = "";

                    @foreach ($setup['items'] as $item)
                        @php
                            $product = App\Models\CatalogProduct::where('retailer_id', $item->retailer_id)->first();
                        @endphp
                        itemsText +=
                            `    ➡️ {{ $product->product_name ?? 'Product' }} x {{ $item->quantity }}\n`;
                    @endforeach

                    const message =
                        `Hello ${customerName},\nYour order #${orderId} has been dispatched and is on its way! 🚚\n\n📦 Items:\n${itemsText}\n🔢 Tracking Number: `;

                    // Set message in modal
                    $('#dispatchModal textarea[name="order_mess"]').val(message);

                    // Open modal
                    $('#dispatchModal').modal('show');

                    // Revert status until dispatch is confirmed
                    $(this).val('{{ $order->status }}');
                    return;
                }

                // Handle delivered status
                if (newStatus === 'delivered') {
                    Swal.fire({
                        title: 'Confirm Delivery',
                        text: 'Mark this order as delivered?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Mark as Delivered',
                        cancelButtonText: 'Cancel',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateOrderStatus(orderId, newStatus, true);
                        } else {
                            // Revert to previous status
                            $(this).val('{{ $order->status }}');
                        }
                    });
                    return;
                }


                // For other statuses, show SweetAlert confirmation
                Swal.fire({
                    title: 'Update Order Status',
                    text: 'Do you want to send a WhatsApp status update to the customer?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Send WhatsApp & Update',
                    cancelButtonText: 'Update Only',
                    reverseButtons: true
                }).then((result) => {
                    const sendWhatsapp = result.isConfirmed ? 1 : 0;
                    updateOrderStatus(orderId, newStatus, sendWhatsapp);

                    updateComponentStates();
                });


            });

            function updateOrderStatus(orderId, newStatus, sendWhatsapp, extraData = {}) {
                $('#status-message').html('<div class="alert alert-info">Updating status...</div>');

                $.ajax({
                    url: "{{ route('catalog.orderStatusUpdate') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order_id: orderId,
                        status: newStatus,
                        send_whatsapp: sendWhatsapp,
                        ...extraData
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update status bar
                            updateStatusBar(newStatus, {
                                tracking_number: response.tracking_number,
                                cancel_reason: response.cancel_reason
                            });

                            // Update order status display
                            const statusText = getStatusText(newStatus);
                            $('.order-status-display').text(statusText);
                            $('.order-status-badge').text(statusText.toUpperCase())
                                .removeClass()
                                .addClass('badge badge-light-' + getStatusColor(newStatus));

                            // Lock UI if delivered
                            if (newStatus === 'delivered') {
                                freezeOrderUI('delivered');
                            }

                            // Show success message
                            let message =
                                '<div class="alert alert-success">Status updated successfully!</div>';
                            if (response.whatsapp_sent) {
                                message +=
                                    '<div class="alert alert-success mt-2">WhatsApp notification sent to customer!</div>';
                            }
                            $('#status-message').html(message);
                        } else {
                            $('#status-message').html('<div class="alert alert-danger">Error: ' + (
                                response.message || 'Failed to update status') + '</div>');
                        }
                    },
                    error: function(xhr) {
                        $('#status-message').html('<div class="alert alert-danger">Error: ' + (xhr
                            .responseJSON?.message || 'Server error') + '</div>');
                    }
                });

            }

            // Lock UI if order is already delivered
            @if (in_array($order->status, ['delivered', 'canceled']))
                freezeOrderUI('{{ $order->status }}');
            @endif

            function getStatusColor(status) {
                const colors = {
                    pending: 'warning',
                    accepted: 'primary',
                    preparing: 'info',
                    ready_to_dispatch: 'success',
                    dispatched: 'success',
                    delivered: 'success'
                };
                return colors[status] || 'secondary';
            }

            // Shipping form handling
            $('#shipping-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const submitButton = form.find('#update-shipping-btn');

                submitButton.attr('data-kt-indicator', 'on');
                submitButton.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Shipping details updated successfully!',
                                showConfirmButton: false,
                                timer: 3000
                            });

                            // Close modal
                            $('#shippingModal').modal('hide');

                            // Reload page to reflect changes
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message ||
                                    'Failed to update shipping details',
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        submitButton.removeAttr('data-kt-indicator');
                        submitButton.prop('disabled', false);
                    }
                });

            });

            // Payment form handling
            $('#payment-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const submitButton = form.find('#update-payment-btn');
                const orderId = "{{ $order->id }}";

                submitButton.attr('data-kt-indicator', 'on');
                submitButton.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success message and ask about WhatsApp
                            Swal.fire({
                                title: 'Payment Updated!',
                                text: 'Do you want to send a WhatsApp payment confirmation to the customer?',
                                icon: 'success',
                                showCancelButton: true,
                                confirmButtonText: 'Send WhatsApp',
                                cancelButtonText: 'Skip',
                                reverseButtons: true
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Send WhatsApp message
                                    sendPaymentWhatsAppMessage(orderId);
                                } else {
                                    // Just reload
                                    setTimeout(() => location.reload(), 1500);
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message ||
                                    'Failed to update payment details',
                            });
                            submitButton.removeAttr('data-kt-indicator');
                            submitButton.prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: errorMessage,
                        });
                        submitButton.removeAttr('data-kt-indicator');
                        submitButton.prop('disabled', false);
                    }
                });

            });

            function sendPaymentWhatsAppMessage(orderId) {
                // Show loading indicator
                Swal.fire({
                    title: 'Sending WhatsApp...',
                    text: 'Please wait while we send the payment confirmation',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Make AJAX request to send WhatsApp
                $.ajax({
                    url: "{{ route('catalog.sendPaymentWhatsApp') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        order_id: orderId
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'WhatsApp payment confirmation sent!',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed to send WhatsApp',
                                text: response.message || 'Unknown error'
                            });
                        }
                        setTimeout(() => location.reload(), 1500);
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to send WhatsApp message. Please try again.'
                        });
                        setTimeout(() => location.reload(), 1500);
                    }
                });

            }

            $('#cancel-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const submitButton = form.find('button[type="submit"]');
                const cancelReason = form.find('textarea[name="cancel_reason"]').val();

                submitButton.prop('disabled', true);
                submitButton.find('.indicator-label').hide();
                submitButton.find('.indicator-progress').show();

                // Ask if they want to send WhatsApp
                Swal.fire({
                    title: 'Confirm Cancellation',
                    text: 'Do you want to send a WhatsApp cancellation notice to the customer?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Send WhatsApp & Cancel',
                    cancelButtonText: 'Cancel Without Notification',
                    reverseButtons: true
                }).then((result) => {
                    const sendWhatsapp = result.isConfirmed ? 1 : 0;

                    $.ajax({
                        url: "{{ route('catalog.cancelOrder', $order->id) }}",
                        method: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            cancel_reason: cancelReason,
                            send_whatsapp: sendWhatsapp
                        },
                        success: function(response) {
                            if (response.success) {
                                // Freeze UI elements
                                freezeOrderUI('canceled');

                                // Close modals
                                $('#cancelReasonModal').modal('hide');

                                // Show success message
                                let message = 'Order canceled successfully';
                                if (response.whatsapp_sent) {
                                    message += ' and WhatsApp notification sent';
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Order Canceled',
                                    text: message
                                });

                                // Refresh page to update UI
                                setTimeout(() => location.reload(), 1500);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message ||
                                        'Failed to cancel order'
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error',
                                text: xhr.responseJSON?.message ||
                                    'Failed to cancel order'
                            });
                        },
                        complete: function() {
                            submitButton.prop('disabled', false);
                            submitButton.find('.indicator-label').show();
                            submitButton.find('.indicator-progress').hide();
                        }
                    });
                });

            });

            $(document).on('click', '.manual-cancel-order-btn', function() {

                const paymentStatus = $(this).data('payment-status');

                if (paymentStatus === 'Paid') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cannot Cancel Order',
                        text: 'The payment for this order has been received. Please return the payment before canceling the order.',
                        confirmButtonText: 'OK',
                        footer: 'Process Payment Return</a>'
                    });
                } else {
                    $('#cancelReasonModal').modal('show');
                }

            });

            // Function to freeze UI when order is canceled or delivered
            function freezeOrderUI(status) {
                // Disable all interactive elements
                $('#order-status-select').prop('disabled', true);
                $('.btn-edit-shipping').prop('disabled', true);
                $('.btn-dispatch-order').prop('disabled', true);
                $('.manual-cancel-order-btn').prop('disabled', true);
                $('[data-target="#shippingModal"]').prop('disabled', true);
                $('[data-target="#paymentModal"]').prop('disabled', true);
                $('[data-target="#dispatchModal"]').prop('disabled', true);
                $('[data-target="#contactModal"]').prop('disabled', true);
                $('[data-target="#orderNoteModal"]').prop('disabled', true);
                $('#resendAddressFormBtn').prop('disabled', true); // Add this line
                // Freeze pricing section buttons
                $('#apply-discount-btn').prop('disabled', true);
                $('#resend-payment-link').prop('disabled', true);

                // Update status display to show it's locked
                $('.order-status-badge').removeClass().addClass(
                    `badge badge-light-${status === 'canceled' ? 'danger' : 'success'}`);
                $('.order-status-badge').html(
                    `${status.charAt(0).toUpperCase() + status.slice(1)} <i class="fas fa-lock ms-2"></i>`);
            }

            // Order note form
            $('#order-note-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const submitButton = form.find('#update-note-btn');

                submitButton.attr('data-kt-indicator', 'on');
                submitButton.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Order instructions updated successfully!',
                                showConfirmButton: false,
                                timer: 3000
                            });

                            // Close modal
                            $('#orderNoteModal').modal('hide');

                            // Update displayed note
                            $('.order-instructions-display').text(response.newNote ||
                                'No special instructions provided');

                            // Reload page to reflect changes
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message ||
                                    'Failed to update instructions',
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        submitButton.removeAttr('data-kt-indicator');
                        submitButton.prop('disabled', false);
                    }
                });

            });

            // Add this mapping function for status texts
            function getStatusText(status) {
                const statusTexts = {
                    'order': 'Pending',
                    'accepted': 'Accepted',
                    'preparing': 'Preparing',
                    'ready_to_dispatch': 'Ready to Dispatch',
                    'dispatched': 'Dispatched',
                    'delivered': 'Delivered',
                    'canceled': 'Canceled'
                };
                return statusTexts[status] || status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            }

            // Add this function to update the status bar
            function updateStatusBar(status, orderData = {}) {

                const statusText = getStatusText(status);
                const statusBarContainer = $('#status-bar-container');

                if (status === 'canceled') {
                    statusBarContainer.html(`
                    <div class="alert alert-danger d-flex justify-content-between align-items-center py-3 mb-2">
                        <div>
                            <i class="ki-duotone ki-information fs-2x me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <strong>Order Cancelled</strong> -
                            Reason: ${orderData.cancel_reason || 'N/A'} -
                            ${new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit' })}
                        </div>
                        <div>
                            <span class="badge badge-light-danger fs-6 fw-bold py-2 px-3">CANCELLED</span>
                        </div>
                    </div>
                `);
                } else if (status === 'delivered') {
                    statusBarContainer.html(`
                    <div class="alert alert-success d-flex justify-content-between align-items-center py-3 mb-2">
                        <div>
                            <i class="ki-duotone ki-check-circle fs-2x me-2"><span class="path1"></span><span class="path2"></span></i>
                            <strong>Order Delivered</strong> -
                            Tracking Number: ${orderData.tracking_number || 'N/A'} -
                            Delivered at: ${new Date().toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute:'2-digit' })}
                        </div>
                        <div>
                            <span class="badge badge-light-success fs-6 fw-bold py-2 px-3">DELIVERED</span>
                        </div>
                    </div>
                `);
                } else {
                    statusBarContainer.html(`
                    <div class="alert alert-info d-flex justify-content-between align-items-center py-3 mb-2">
                        <div>
                            <i class="ki-duotone ki-information fs-2x me-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                            <strong>Order Status:</strong> ${statusText}
                        </div>
                        <div>
                            <span class="badge badge-light-info fs-6 fw-bold py-2 px-3">${statusText.toUpperCase()}</span>
                        </div>
                    </div>
                `);
                }

            }

            // Delivery Confirmation Modal
            $('#deliveryConfirmationModal').on('shown.bs.modal', function() {
                $('#delivery-note-text').focus();
            });

            $('#confirm-delivery-btn').click(function() {
                const deliveryNote = $('#delivery-note-text').val().trim();
                const sendWhatsapp = $('#send-whatsapp').is(':checked') ? 1 : 0;

                if (!deliveryNote) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delivery note required',
                        text: 'Please enter a delivery note.',
                    });
                    return;
                }

                const orderId = "{{ $order->id }}";
                const button = $(this);
                button.prop('disabled', true);
                button.find('.indicator-label').hide();
                button.find('.indicator-progress').show();

                updateOrderStatus(orderId, 'delivered', sendWhatsapp, {
                    delivery_note: deliveryNote
                });

                $('#deliveryConfirmationModal').modal('hide');

            });

            // Initialize UI state
            @if (in_array($order->status, ['delivered', 'canceled']))
                freezeOrderUI('{{ $order->status }}');
            @endif

            // Discount calculation
            const subtotal = {{ $order->subtotal_value / $order->subtotal_offset }};
            let shipping = {{ $order->shipping_cast }};
            let discountType = "{{ $order->discount_type }}";
            let discountValue = {{ $order->discount ?? 0 }};

            // Update discount symbol when type changes
            $('[name="discount_type"]').change(function() {
                const symbol = $(this).val() === 'percent' ? '%' : '₹';
                $('#discount-symbol').text(symbol);
                calculateTotal();
            });

            // Update preview when values change
            function calculateTotal() {


                // Get current subtotal from preview element
                const subtotalText = $('#preview-subtotal').text().replace('₹', '').replace(/,/g, '');
                const subtotal = parseFloat(subtotalText) || 0;
                const shipping = parseFloat($('[name="shipping_amount"]').val()) || 0;
                const discountType = $('[name="discount_type"]').val();

                const discountValue = parseFloat($('[name="discount_value"]').val()) || 0;
                // Calculate discount amount
                let discountAmount = 0;
                if (discountType && discountValue > 0) {
                    if (discountType === 'percent') {
                        discountAmount = (subtotal * discountValue) / 100;
                    } else {
                        discountAmount = Math.min(discountValue, subtotal);
                    }
                }

                const newTotal = (subtotal - discountAmount) + shipping;

                // Update preview elements
                $('#preview-shipping').text('₹' + shipping.toFixed(2));
                // Update the preview-discount element
                // const discountDisplay = discountType ?
                //     (discountType === 'percent' ?
                //         '-' + discountValue + '%' :
                //         '-₹' + discountAmount.toFixed(2)) :
                //     '-₹0.00';
                $('#preview-discount').text('-₹' + discountAmount);
                $('#preview-total').text('₹' + newTotal.toFixed(2));
            }

            // Initialize calculation
            calculateTotal();

            // Update when values change
            $('[name="shipping_amount"], [name="discount_value"], [name="discount_type"]').on('input change',
                calculateTotal);

            // FIXED: Refresh order items section with proper subtotal extraction
            function refreshOrderItemsSection() {
                $.get("{{ route('catalog.orderItemsSection', $order->id) }}", function(data) {
                    $('#order-items-section').html(data);

                    // Extract new subtotal from the updated section
                    const subtotalMatch = data.match(/id="order-subtotal-amount"[^>]*>₹([\d,]+\.\d{2})/);
                    if (subtotalMatch) {
                        const newSubtotal = parseFloat(subtotalMatch[1].replace(',', ''));
                        updateAdjustSubtotal(newSubtotal);
                    }
                });
            }

            // WhatsApp update functionality
            $(document).on('click', '.send-updated-cart-btn', function() {
                const btn = $(this);
                btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span> Sending...');

                $.ajax({
                    url: "{{ route('catalog.sendUpdatedCart', $order->id) }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cart Sent!',
                                text: 'The updated cart has been sent to the customer',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Failed to send cart',
                                'error');
                        }
                    },
                    complete: function() {
                        btn.prop('disabled', false).html(
                            '<i class="ki-duotone ki-send fs-2 me-2"></i> Send Updated Cart to Customer'
                        );
                    }
                });
            });

            $(document).on('click', '#apply-discount-btn', function() {
                const btn = $(this);
                const container = $('#adjust-section');
                const orderId = container.data('order-id');

                // Get form values
                const shipping = parseFloat($('input[name="shipping_amount"]').val());
                const discountType = $('select[name="discount_type"]').val();
                const discountValue = parseFloat($('input[name="discount_value"]').val());

                // Show processing state
                btn.attr('data-kt-indicator', 'on');
                btn.prop('disabled', true);

                // AJAX request
                $.ajax({
                    url: '{{ route('catalog.applyDiscount', $order->id) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        shipping_amount: shipping,
                        discount_type: discountType,
                        discount_value: discountValue
                    },
                    success: function(response) {
                        // Update order summary
                        $('#order-shipping-amount').text('₹' + shipping.toFixed(2));

                        if (discountValue > 0) {
                            $('#order-discount-row').show();
                            if (discountType === 'percent') {
                                $('#order-discount-display').text('-' + discountValue + '%');
                            } else {
                                $('#order-discount-display').text('-₹' + discountValue.toFixed(
                                    2));
                            }
                        } else {
                            $('#order-discount-display').text('0.00%');
                        }

                        // Calculate new total
                        const subtotal = parseFloat($('input[name="subtotal_value"]').val());
                        const discountAmount = discountType === 'percent' ?
                            (subtotal * discountValue / 100) : discountValue;
                        const total = subtotal + shipping - discountAmount;

                        $('#order-total-amount').text('₹' + total.toFixed(2));

                        // Update preview
                        $('#preview-shipping').text('₹' + shipping.toFixed(2));
                        $('#preview-discount').text('-₹' + discountAmount.toFixed(2));
                        $('#preview-total').text('₹' + total.toFixed(2));

                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Pricing Updated!',
                            text: 'Shipping and discount applied successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON?.message || 'Update failed',
                            'error');
                    },
                    complete: function() {
                        btn.removeAttr('data-kt-indicator');
                        btn.prop('disabled', false);
                    }
                });
            });

            // Resend Payment Link with confirmation
            $(document).on('click', '#resend-payment-link', function(e) {
                // Skip if button is disabled
                if ($(this).is(':disabled')) return;

                e.preventDefault();
                const orderId = $(this).data('order-id');
                const btn = $(this);

                Swal.fire({
                    title: 'Resend Payment Link?',
                    text: 'Are you sure you want to resend the payment link to the customer?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Resend',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true);
                        btn.html(
                            '<span class="spinner-border spinner-border-sm me-2"></span> Sending...'
                        );

                        $.ajax({
                            url: "{{ route('catalog.resendPaymentLink') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                order_id: orderId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Payment Link Sent!',
                                        text: 'Customer will receive updated payment details',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                } else {
                                    Swal.fire('Error', response.message ||
                                        'Failed to resend link', 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Error', xhr.responseJSON?.message ||
                                    'Server error', 'error');
                            },
                            complete: function() {
                                btn.prop('disabled', false);
                                btn.html(
                                    '<i class="ki-duotone ki-send fs-2 me-2"></i> Resend Payment Link'
                                );
                            }
                        });
                    }
                });
            });
            // Reason selection functionality
            $(document).on('click', '.reason-btn', function() {

                const reason = $(this).data('reason');
                const textarea = $('#cancel-reason-text');

                // Set textarea value
                textarea.val(reason);

                // Highlight selected button
                $('.reason-btn').removeClass('active');
                $(this).addClass('active');

                // Scroll textarea into view
                textarea[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });

            });

            // Fix for reopening modal - use Bootstrap's jQuery method
            // Fix for reopening modal with proper cleanup
            // Remove this code block
            $(document).on('click', '#add-item-btn', function() {
                $('#addItemModal').modal('show');
            });

            // Properly handle modal hide event
            $('#addItemModal').on('hidden.bs.modal', function() {
                $(this).data('bs.modal', null);
            });

            // Textarea input handler to clear button selection
            $('#cancel-reason-text').on('input', function() {
                $('.reason-btn').removeClass('active');

                // If user clears textarea, clear selection
                if ($(this).val().trim() === '') {
                    $('.reason-btn').removeClass('active');
                }
            });

            // Open modal
            // $('#add-item-btn').click(function() {
            //     $('#addItemModal').modal('show');
            // });

            // Product search
            $('#productSearch').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();

                $('.product-item').each(function() {
                    const productName = $(this).find('.card-title').text().toLowerCase();
                    $(this).toggle(productName.includes(searchTerm));
                });
            });

            // Add product to order
            $(document).on('click', '.add-product-btn', function() {
                const retailerId = $(this).data('retailer-id');
                const productId = $(this).data('product-id');
                const productName = $(this).data('product-name');
                const price = $(this).data('product-price');

                Swal.fire({
                    title: 'Add Product',
                    html: `How many <b>${productName}</b> would you like to add?`,
                    input: 'number',
                    inputValue: 1,
                    inputAttributes: {
                        min: 1,
                        step: 1
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Add to Order'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const quantity = result.value || 1;

                        // Show loading indicator
                        const swalInstance = Swal.fire({
                            title: 'Adding Item',
                            html: 'Please wait while we add your item',
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('catalog.addItemToOrder', $order->id) }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                retailer_id: retailerId,
                                product_id: productId,
                                product_name: productName,
                                price: price,
                                quantity: quantity
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Item added successfully!',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                    refreshOrderItemsSection();
                                    calculateTotal();
                                    $('#addItemModal').modal('hide');
                                } else {
                                    Swal.fire('Error', response.message ||
                                        'Failed to add item', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to add item', 'error');
                            }
                        });
                    }
                });
            });

            // Edit item modal
            $(document).on('click', '.edit-item-btn', function() {
                const itemId = $(this).data('item-id');
                const productName = $(this).data('product-name');
                const quantity = $(this).data('quantity');
                const price = $(this).data('price');

                $('#edit-item-id').val(itemId);
                $('#edit-product-name').text(productName);
                $('#edit-item-quantity').val(quantity);
                $('#edit-item-price').val(price);

                $('#editItemModal').modal('show');
            });

            // Save edited item
            $('#save-item-btn').click(function() {
                const formData = $('#edit-item-form').serializeArray();
                const itemId = $('#edit-item-id').val();

                // Show loading indicator
                const swalInstance = Swal.fire({
                    title: 'Edit Item',
                    html: 'Please wait while we edit your item',
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: "{{ route('catalog.updateOrderItem', $order->id) }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: itemId,
                        quantity: $('#edit-item-quantity').val(),
                        price: $('#edit-item-price').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Item updated successfully!',
                                showConfirmButton: false,
                                timer: 3000
                            });
                            refreshOrderItemsSection();
                            $('#editItemModal').modal('hide');
                        } else {
                            Swal.fire('Error', response.message || 'Failed to update item',
                                'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Failed to update item', 'error');
                    }
                });
            });

            // Delete item
            $(document).on('click', '.delete-item-btn', function() {
                const itemId = $(this).data('item-id');
                const productName = $(this).data('item-name');
                //              const totalItems = $('.order-item-row').length;

                // if (totalItems <= 1) {
                //     Swal.fire('Error', 'At least one item is required in the order', 'error');
                //     return;
                // }
                // if (totalItems <= 1) {
                //     Swal.fire('Error', 'At least one item is required in the order', 'error');
                //     return;
                // }

                Swal.fire({
                    title: 'Confirm Deletion',
                    html: `Are you sure you want to remove <b>${productName}</b> from this order?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        // Show loading indicator
                        const swalInstance = Swal.fire({
                            title: 'Deleting Item',
                            html: 'Please wait while we delete your item',
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        $.ajax({
                            url: "{{ route('catalog.deleteOrderItem', $order->id) }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                item_id: itemId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Item deleted successfully!',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                    refreshOrderItemsSection();
                                    calculateTotal();
                                } else {
                                    Swal.fire('Error', response.message ||
                                        'Failed to delete item', 'error');
                                }
                            },
                            error: function() {
                                Swal.fire('Error', 'Failed to delete item', 'error');
                            }
                        });
                    }
                });
            });

            // Adjust quantity
            // Fixed quantity adjustment
            $(document).on('click', '.quantity-btn', function() {
                const itemId = $(this).data('item-id');
                const action = $(this).data('action');
                const quantityElement = $(this).closest('.quantity-controls').find('.item-quantity');
                let newQuantity = parseInt(quantityElement.text());

                if (action === 'increase') {
                    newQuantity++;
                } else if (action === 'decrease' && newQuantity > 1) {
                    newQuantity--;
                } else {
                    return;
                }

                // Show loading indicator
                const swalInstance = Swal.fire({
                    title: 'Updating Quantity',
                    html: 'Please wait while we update your order',
                    timerProgressBar: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });


                // AJAX call to update quantity
                $.ajax({
                    url: "{{ route('catalog.updateOrderItem', $order->id) }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        item_id: itemId,
                        quantity: newQuantity
                    },
                    success: function(response) {
                        Swal.close();
                        if (response.success) {
                            refreshOrderItemsSection();
                            calculateTotal(); // Refresh pricing section

                        } else {
                            Swal.fire('Error', response.message || 'Failed to update quantity',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        Swal.fire('Error', xhr.responseJSON?.message ||
                            'Failed to update quantity', 'error');
                    }
                });


            });

            // New function to refresh adjust section
            function refreshAdjustSection() {
                // $.get("{{ route('catalog.orderAdjustSection', $order->id) }}", function(data) {
                //     $('#adjust-section').html(data);
                //     initPricingCalculations();
                // });
            }

            // Initialize pricing calculations
            function initPricingCalculations() {
                // Shipping discount calculation
                $('[name="shipping_amount"], [name="discount_value"], [name="discount_type"]').off('input change')
                    .on('input change', function() {
                        calculateTotal();
                    });

                // Initialize calculation
                calculateTotal();
            }

            document.getElementById('resendAddressFormBtn').addEventListener('click', function() {
                const button = this;
                const orderId = {{ $order->id }};

                // Show confirmation dialog
                Swal.fire({
                    title: 'Resend Address Form?',
                    text: "This will send a WhatsApp message to the customer requesting address information.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, send it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        const originalHtml = button.innerHTML;

                        // Show loading state
                        button.innerHTML =
                            '<span class="spinner-border spinner-border-sm" role="status"></span> Sending...';
                        button.disabled = true;

                        // Make AJAX request
                        fetch('{{ route('order.resendAddressForm', $order->id) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Success toast notification
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Address form resent successfully',
                                        showConfirmButton: false,
                                        timer: 3000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast.addEventListener('mouseenter',
                                                Swal.stopTimer)
                                            toast.addEventListener('mouseleave',
                                                Swal.resumeTimer)
                                        }
                                    });
                                } else {
                                    // Error toast notification
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'error',
                                        title: data.message || 'Failed to resend form',
                                        showConfirmButton: false,
                                        timer: 5000,
                                        timerProgressBar: true,
                                        didOpen: (toast) => {
                                            toast.addEventListener('mouseenter',
                                                Swal.stopTimer)
                                            toast.addEventListener('mouseleave',
                                                Swal.resumeTimer)
                                        }
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Network Error',
                                    text: 'Failed to connect to server',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                console.error('Error:', error);
                            })
                            .finally(() => {
                                button.innerHTML = originalHtml;
                                button.disabled = false;
                            });
                    }
                });
            });

            // Shipping method toggle
            function toggleAddressFields() {
                const shippingMethod = $('#shipping_method').val();
                const addressFields = $('#address-fields');

                if (shippingMethod === 'Local Pickup' || shippingMethod === 'Dining') {
                    // Hide and disable validation for address fields
                    addressFields.hide();
                    addressFields.find('input, textarea').removeAttr('required');
                } else {
                    // Show and require address fields
                    addressFields.show();
                    addressFields.find('input, textarea').attr('required', 'required');
                }
            }

            // Initial toggle on page load
            toggleAddressFields();

            // Toggle when shipping method changes
            $('#shipping_method').change(toggleAddressFields);

            // Contact form submission
            $('#contact-form').submit(function(e) {
                e.preventDefault();
                const form = $(this);
                const submitButton = form.find('#update-contact-btn');

                submitButton.attr('data-kt-indicator', 'on');
                submitButton.prop('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Contact information updated successfully!',
                                showConfirmButton: false,
                                timer: 3000
                            });

                            // Update displayed name
                            $('.customer-name-display').text(response.newName);

                            // Close modal
                            $('#contactModal').modal('hide');
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message ||
                                    'Failed to update contact information',
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Server Error',
                            text: errorMessage,
                        });
                    },
                    complete: function() {
                        submitButton.removeAttr('data-kt-indicator');
                        submitButton.prop('disabled', false);
                    }
                });


            });
        });
    </script>
@endsection

@section('css')
    <style>
        /* Disabled state styling */
        [disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn[disabled] {
            background-color: #f5f8fa;
            color: #b5b5c3;
            border-color: #f5f8fa;
        }

        /* Cancellation bar styling */
        .cancellation-bar {
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Add smooth transitions for address fields */
        #address-fields {
            transition: all 0.3s ease;
        }

        .order-instructions-card {
            border-left: 4px solid #3699FF;
        }

        .ki-note:before {
            content: "\e9f3";
        }

        /* Custom styling */
        .card {
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-header {
            border-bottom: 1px solid #eff2f5;
        }

        .symbol img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 0.475rem;
        }

        .badge-light-info {
            background-color: #f1faff;
            color: #009ef7;
        }

        .badge-light-success {
            background-color: #e8fff3;
            color: #50cd89;
        }

        .badge-light-danger {
            background-color: #fff5f8;
            color: #f1416c;
        }

        .modal-content {
            border-radius: 0.475rem;
        }

        #shipping-form .form-control-solid,
        #payment-form .form-control-solid {
            background-color: #f5f8fa;
        }

        .bg-light-primary {
            background-color: #f1faff;
        }

        #order-status-select {
            max-width: 250px;
        }

        #status-message .alert {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
        }

        /* Lock icon styling */
        .badge .fas {
            font-size: 0.8em;
            vertical-align: middle;
        }

        .reason-btn {
            flex: 1 0 calc(33.333% - 0.5rem);
            min-width: 120px;
            text-align: center;
            padding: 0.5rem;
            white-space: normal;
            height: auto;
            transition: all 0.3s ease;
        }

        .reason-btn:hover,
        .reason-btn.active {
            background-color: #3699ff;
            color: white !important;
            border-color: #3699ff;
        }

        .print-option.active {
            border-color: #009ef7;
            background-color: #f1faff;
        }

        @media (max-width: 576px) {
            .reason-btn {
                flex: 1 0 calc(50% - 0.5rem);
            }
        }

        /* Quantity controls */
        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 14px;
        }

        .item-quantity {
            min-width: 30px;
            text-align: center;
            font-weight: bold;
        }

        /* Action buttons */
        .item-actions {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        /* Add item modal */
        #productList {
            max-height: 60vh;
            overflow-y: auto;
        }

        .product-item {
            transition: transform 0.3s ease;
        }

        .product-item:hover {
            transform: translateY(-5px);
        }

        /* Edit modal */
        #edit-product-name {
            font-size: 1.1rem;
            padding: 8px 0;
        }

        #order-items-container {
            max-height: 100vh;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 10px;
        }

        .symbol.position-relative {
            position: relative;
        }

        .btn-icon.btn-sm {
            width: 22px;
            height: 22px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-item-btn {
            transform: translate(-50%, -50%);
        }
    </style>
@endsection
