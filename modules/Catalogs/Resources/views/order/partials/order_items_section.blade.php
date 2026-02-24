@php
    // Inherit freezeOperations logic from parent view
    $freezeOperations =
        in_array($order->status, ['delivered', 'dispatched', 'preparing', 'ready_to_dispatch', 'canceled']) ||
        in_array($order->payment_status, ['Paid', 'Refunded', 'Failed']);
@endphp

<!-- Order Items Card -->
<div class="card mb-5">
    <div class="card-header border-0 pt-5">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-3 mb-1">{{ __('Order Items') }}</span>
            <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Products in this order') }}</span>
        </h3>
        <div class="card-toolbar">
            <button type="button" class="btn btn-sm btn-danger manual-cancel-order-btn"
                data-payment-status="{{ $order->payment_status }}" @if ($freezeOperations) disabled @endif>
                <i class="ki-duotone ki-truck fs-2 me-2">
                    <span class="path1"></span>
                    <span class="path2"></span>
                </i>
                {{ __('Cancel Order') }}
            </button>
            <button type="button" class="btn btn-sm btn-primary ms-2 add-item-btn" id="add-item-btn"
                @if ($freezeOperations) disabled @endif>
                <i class="ki-duotone ki-plus fs-2 me-1"></i> Add New Item
            </button>
            <button type="button" class="btn btn-sm btn-light-primary ms-2" data-bs-toggle="tooltip"
                data-bs-placement="top" title="Print order receipt" onclick="printBasicReceipt('{{ $order->id }}')"
                @if ($freezeOperations) disabled @endif>
                <i class="ki-duotone ki-printer">
                    <span class="path1"></span>
                    <span class="path2"></span>
                    <span class="path3"></span>
                    <span class="path4"></span>
                    <span class="path5"></span>
                </i>
            </button>
        </div>
    </div>
    <div class="card-body pt-0">
        <div class="table-responsive">

            <div id="order-items-container">
                <table class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                            <th class="min-w-250px">Product</th>
                            <th class="text-center min-w-80px">Qty</th>
                            <th class="text-center min-w-100px">Price</th>
                            <th class="text-center min-w-100px">Total</th>
                        </tr>
                    </thead>
                    <tbody class="fw-semibold text-gray-600">
                        @foreach ($setup['items'] as $key => $item)
                            @php
                                $catalogproduct = App\Models\CatalogProduct::where(
                                    'retailer_id',
                                    $item->retailer_id,
                                )->first();
                                $category = Modules\Catalogs\Models\ProductCategory::whereRaw(
                                    "FIND_IN_SET('{$item->retailer_id}', retailer_id)",
                                )->first();
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center position-relative">
                                        <div class="symbol symbol-50px me-5 position-relative">
                                            <button
                                                class="btn btn-icon btn-sm btn-danger position-absolute top-0 start-0 translate-middle delete-item-btn"
                                                data-item-id="{{ $item->id }}" data-item-name="{{ $item->name }}"
                                                style="z-index: 10; width: 22px; height: 22px; padding: 0; border-radius: 50%;"
                                                @if ($freezeOperations) disabled @endif>
                                                <i class="ki-duotone ki-cross-circle fs-6">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </button>
                                            @if ($catalogproduct && $catalogproduct->image_url)
                                                <img src="{{ $catalogproduct->image_url }}"
                                                    alt="{{ $catalogproduct->product_name }}"
                                                    class="h-20 align-self-center">
                                            @else
                                                <span class="symbol-label bg-light-primary">
                                                    <i class="ki-duotone ki-bag fs-2x text-primary">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </span>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span
                                                class="text-gray-800 fw-bold fs-7">{{ $catalogproduct->product_name ?? 'Product not found' }}</span>
                                            <span class="text-muted fs-7">SKU: {{ $item->retailer_id }}</span>
                                            @if ($category)
                                                <span
                                                    class="badge badge-light-info mt-1 fs-7">{{ $category->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="quantity-controls">
                                        <button
                                            class="btn btn-icon btn-sm btn-light btn-outline btn-circle quantity-btn"
                                            data-action="decrease" data-item-id="{{ $item->id }}"
                                            style="width: 24px; height: 24px; padding: 0;"
                                            @if ($freezeOperations) disabled @endif>
                                            <i class="ki-duotone ki-minus fs-5"></i>
                                        </button>
                                        <span class="item-quantity mx-2">{{ $item->quantity }}</span>
                                        <button
                                            class="btn btn-icon btn-sm btn-light btn-outline btn-circle quantity-btn"
                                            data-action="increase" data-item-id="{{ $item->id }}"
                                            style="width: 24px; height: 24px; padding: 0;"
                                            @if ($freezeOperations) disabled @endif>
                                            <i class="ki-duotone ki-plus fs-5"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="text-center">₹{{ $item->amount_value / $item->amount_offset }}</td>
                                <td class="text-center">
                                    ₹{{ ($item->amount_value / $item->amount_offset) * $item->quantity }}</td>
                            </tr>
                        @endforeach

                        <!-- Shipping Row -->
                        <tr class="border-top border-gray-200">
                            <td colspan="2"></td>
                            <td class="text-end fw-bold">Shipping:</td>
                            <td class="text-center text-danger fw-bold" id="order-shipping-amount">
                                ₹{{ number_format($order->shipping_cast, 2) }}
                            </td>
                        </tr>

                        <!-- Discount Row -->
                        @if ($order->discount)
                            <tr id="order-discount-row">
                                <td colspan="2"></td>
                                <td class="text-end fw-bold">Discount:</td>
                                <td class="text-center text-success fw-bold" id="order-discount-display">
                                    {{ $order->discount_type === 'percent' ? $order->discount . '%' : '₹' . number_format($order->discount, 2) }}
                                </td>
                            </tr>
                        @endif

                        <!-- Total Row -->
                        <tr>
                            <td colspan="2"></td>
                            <td class="text-end fw-bold">Total:</td>
                            <td class="text-center text-danger fw-bold" id="order-total-amount">
                                @php
                                    $finalAmount =
                                        ($order->subtotal_offset ?? 1) != 0
                                            ? $order->subtotal_value / $order->subtotal_offset
                                            : 0;
                                    $shipping = $order->shipping_cast ?? 0;
                                    $discountType = $order->discount_type ?? null;
                                    $discountValue = $order->discount ?? 0;
                                    $discountAmount = 0;

                                    if ($discountType && $discountValue > 0) {
                                        if ($discountType === 'percent') {
                                            $discountAmount = ($finalAmount * $discountValue) / 100;
                                        } else {
                                            $discountAmount = min($discountValue, $finalAmount);
                                        }
                                    }

                                    $total = $finalAmount - $discountAmount + $shipping;
                                @endphp
                                ₹{{ number_format($total, 2) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-primary send-updated-cart-btn"
                @if ($freezeOperations) disabled @endif>
                <span class="indicator-label">Update Order & Send WhatsApp</span>
                <span class="indicator-progress d-none">
                    <span class="spinner-border spinner-border-sm align-middle me-2"></span>
                    Sending...
                </span>
            </button>
        </div>
    </div>

    <!-- Add hidden element for subtotal -->
    <div id="order-subtotal-amount" style="display: none;">
        ₹{{ number_format($order->subtotal_value / $order->subtotal_offset, 2) }}
    </div>
</div>
@include('Catalogs::order.partials.adjust_section')
