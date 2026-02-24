    <!-- Shipping Details Modal -->
    <div class="modal fade" id="shippingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content">
                <form id="shipping-form" method="post" action="{{ route('catalog.updateShipping', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">Update Shipping Details</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="mb-5">
                            <label class="form-label fs-6 fw-semibold text-gray-700">Shipping Method</label>
                            <select class="form-select form-select-solid" name="shipping_method" id="shipping_method">
                                <option value="">Select Method</option>
                                <option value="Standard Shipping"
                                    {{ $order->shipping_method == 'Standard Shipping' ? 'selected' : '' }}>Standard
                                    Shipping</option>
                                <option value="Express Shipping"
                                    {{ $order->shipping_method == 'Express Shipping' ? 'selected' : '' }}>Express
                                    Shipping
                                </option>
                                <option value="Local Pickup"
                                    {{ $order->shipping_method == 'Local Pickup' ? 'selected' : '' }}>Local Pickup
                                </option>
                                <option value="Courier" {{ $order->shipping_method == 'Courier' ? 'selected' : '' }}>
                                    Courier</option>
                            </select>
                        </div>

                        <div id="address-fields">
                            <!-- Add tracking number field -->
                            <div class="mb-5">
                                <label class="form-label fs-6 fw-semibold text-gray-700">Tracking Number</label>
                                <input type="text" name="tracking_number" class="form-control form-control-solid"
                                    placeholder="Enter tracking number" value="{{ $order->tracking_number ?? '' }}">
                            </div>
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Name</label>
                                    <input type="text" name="for_person" class="form-control form-control-solid"
                                        placeholder="BRIJ" value="{{ $order->for_person }}" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Phone number</label>
                                    <input type="text" name="for_person_number"
                                        class="form-control form-control-solid" placeholder="+9199XXXXXXXXX"
                                        value="{{ $order->for_person_number }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Full Address</label>
                                    <textarea name="address" class="form-control form-control-solid" rows="3" placeholder="Full Address" required>{{ $order->address }}</textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">House Number
                                        (Optional)</label>
                                    <input type="text" name="house_number" class="form-control form-control-solid"
                                        placeholder="House Number" value="{{ $order->house_number }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Building Name
                                        (Optional)</label>
                                    <input type="text" name="building_name" class="form-control form-control-solid"
                                        placeholder="Building Name" value="{{ $order->building_name }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Pin Code</label>
                                    <input type="text" name="pin_code" class="form-control form-control-solid"
                                        placeholder="Pin Code" value="{{ $order->pin_code }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Tower number
                                        (Optional)</label>
                                    <input type="text" name="tower_number" class="form-control form-control-solid"
                                        placeholder="Tower Number" value="{{ $order->tower_number }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">Landmark/Area</label>
                                    <input type="text" name="landmark_area" class="form-control form-control-solid"
                                        placeholder="Landmark Area" value="{{ $order->landmark_area }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">City</label>
                                    <input type="text" name="city" class="form-control form-control-solid"
                                        placeholder="City" value="{{ $order->city }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">State</label>
                                    <input type="text" name="state" class="form-control form-control-solid"
                                        placeholder="State" value="{{ $order->state }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="row g-5">
                            <div class="col-md-12">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Customer Name</label>
                                <input type="text" name="user_name" class="form-control form-control-solid"
                                    placeholder="Customer Name" value="{{ $order->user_name }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="row g-5">
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700">Transaction ID</label>
                                <input type="text" name="transaction_id" class="form-control form-control-solid"
                                    placeholder="Transaction ID" value="{{ $order->transaction_id }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Payment
                                    Method</label>
                                <select class="form-select form-select-solid" name="transaction_type" required>
                                    <option value="">Select Method</option>
                                    <option value="UPI" {{ $order->transaction_type == 'UPI' ? 'selected' : '' }}>
                                        UPI
                                    </option>
                                    <option value="CASH" {{ $order->transaction_type == 'CASH' ? 'selected' : '' }}>
                                        Cash
                                    </option>
                                    <option value="BANK Transfer"
                                        {{ $order->transaction_type == 'BANK Transfer' ? 'selected' : '' }}>Bank
                                        Transfer
                                    </option>
                                    <option value="Card" {{ $order->transaction_type == 'Card' ? 'selected' : '' }}>
                                        Card
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700 required">Payment
                                    Status</label>
                                <select class="form-select form-select-solid" name="payment_status" required>
                                    <option value="">Select Status</option>
                                    <option value="Pending"
                                        {{ $order->payment_status == 'Pending' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="Paid" {{ $order->payment_status == 'Paid' ? 'selected' : '' }}>
                                        Paid
                                    </option>
                                    <option value="Failed" {{ $order->payment_status == 'Failed' ? 'selected' : '' }}>
                                        Failed</option>
                                    <option value="Refunded"
                                        {{ $order->payment_status == 'Refunded' ? 'selected' : '' }}>
                                        Refunded</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fs-6 fw-semibold text-gray-700">Currency</label>
                                <input type="text" name="currency" class="form-control form-control-solid"
                                    placeholder="Currency" value="{{ $order->currency ?? 'INR' }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700 required">Message</label>
                            <textarea rows="4" name="order_mess" class="form-control form-control-solid"
                                placeholder="Enter dispatch message" required>{{ $Paymenttemplate ? $Paymenttemplate->order_message : '' }}</textarea>
                        </div>

                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700 required">Tracking Number</label>
                            <input type="text" name="number" class="form-control form-control-solid"
                                placeholder="Enter tracking number" value="{{ $order->tracking_number ?? '' }}"
                                required>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
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
                            <textarea rows="4" name="cancel_reason" id="cancel-reason-text" class="form-control form-control-solid"
                                placeholder="Enter reason for cancellation" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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
                    <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>
                <div class="modal-body py-10 px-lg-17">
                    <div class="mb-10">
                        <label class="form-label fs-6 fw-semibold text-gray-700 required">Delivery Note</label>
                        <textarea rows="4" id="delivery-note-text" class="form-control form-control-solid"
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
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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
                <form id="order-note-form" method="post"
                    action="{{ route('catalog.updateOrderNote', $order->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h2 class="fw-bold">Update Order Instructions</h2>
                        <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </div>
                    </div>
                    <div class="modal-body py-10 px-lg-17">
                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700">Special Instructions</label>
                            <textarea rows="4" name="order_process_note" class="form-control form-control-solid"
                                placeholder="Enter special instructions (e.g., prepare without spices, extra sauce)">{{ $order->order_process_note }}</textarea>
                            <div class="form-text">These notes will be visible to kitchen staff and delivery personnel
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer flex-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                            <i class="ki-outline ki-printer fs-4tx text-primary"></i>
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
                                            <i class="ki-outline ki-document fs-4tx text-success"></i>
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button id="confirmPrintBtn" type="button" class="btn btn-primary">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold text-gray-800"></h5>
                    <div class="d-flex align-items-center position-relative ms-3" style="width: 280px;">
                        <i class="fas fa-search position-absolute ms-3 text-gray-500"></i>
                        <input type="text" id="productSearch" class="form-control form-control-solid ps-9"
                            placeholder="Search products...">
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="row g-4 p-5" id="productList">
                        @foreach ($products as $product)
                            <div class="col-6 col-md-4 col-lg-3 mb-4 product-item">
                                <div
                                    class="card h-100 border-0 shadow-sm product-card position-relative overflow-hidden">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <button class="btn btn-sm btn-icon btn-primary rounded-circle add-btn"
                                            data-bs-toggle="tooltip" title="Add to cart">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    <div class="product-thumb-container position-relative">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->product_name }}"
                                            class="product-thumb card-img-top">
                                    </div>
                                    <div class="card-body text-center p-3">
                                        <h6 class="card-title mb-1 text-truncate fw-semibold text-gray-800"
                                            title="{{ $product->product_name }}">{{ $product->product_name }}</h6>
                                        <div class="d-flex justify-content-center align-items-center mt-2">
                                            <span class="text-primary fw-bold fs-6">{{ $product->price }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div><!-- /row -->
                </div>

                <div class="modal-footer bg-light d-flex justify-content-between">
                    <div class="d-flex align-items-center text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        <span class="small">Click the <span class="fw-bold">+</span> button to add products</span>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Proceed to Checkout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
