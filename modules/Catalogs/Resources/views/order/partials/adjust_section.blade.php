<!-- Discount & Shipping Section -->
@php
    $freezePricing = $order->payment_status === 'Paid' || $order->status === 'delivered';
@endphp

<div class="card mt-4 mb-4">
    <div class="card-header">
        <h3 class="card-title align-items-start flex-column">
            <span class="card-label fw-bold fs-4">Adjust Pricing</span>
            <span class="text-muted mt-1 fs-7">Modify shipping, discounts, and resend payment</span>
        </h3>
    </div>
    <div class="card-body">
        <!-- Removed form tag and added data attributes -->
        <div id="adjust-section" data-order-id="{{ $order->id }}">
            <div class="row mb-6">
                <!-- Shipping Amount -->
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2 required">Shipping Amount</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent">₹</span>
                        <input type="number" name="shipping_amount" class="form-control form-control-solid"
                            placeholder="0.00" step="0.01" min="0" value="{{ $order->shipping_cast }}"
                            required @if ($freezePricing) disabled @endif>
                    </div>
                </div>

                <!-- Discount Type -->
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Discount Type</label>
                    <select name="discount_type" class="form-select form-select-solid" data-control="select2"
                        @if ($freezePricing) disabled @endif>
                        <option value="">Select Type</option>
                        <option value="percent" {{ $order->discount_type == 'percent' ? 'selected' : '' }}>
                            Percentage</option>
                        <option value="fixed" {{ $order->discount_type == 'fixed' ? 'selected' : '' }}>
                            Fixed Amount</option>
                    </select>
                </div>

                <!-- Discount Value -->
                <div class="col-md-4 fv-row">
                    <label class="fs-6 fw-semibold mb-2">Discount Value</label>
                    <div class="input-group">
                        <input type="number" name="discount_value" class="form-control form-control-solid"
                            placeholder="0.00" step="0.01" min="0" value="{{ $order->discount ?? 0 }}"
                            @if ($freezePricing) disabled @endif>
                        <span class="input-group-text bg-transparent" id="discount-symbol">
                            {{ $order->discount_type == 'percent' ? '%' : '₹' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Calculation Preview -->
            <div class="row bg-light-info rounded p-5 mb-5" id="discount-preview">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-2">
                        <span class="fw-bold me-4">Subtotal:</span>
                        <span
                            id="preview-subtotal">₹{{ number_format($order->subtotal_value / $order->subtotal_offset, 2) }}</span>

                        <input type="number" name="subtotal_value" class="form-control form-control-solid d-none"
                            placeholder="0.00" step="0.01" min="0"
                            value="{{ $order->subtotal_value / $order->subtotal_offset }}">
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="fw-bold me-4">Shipping:</span>
                        <span id="preview-shipping">₹{{ number_format($order->shipping_cast, 2) }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <span class="fw-bold me-4">Discount:</span>
                        <span id="preview-discount"
                            class="text-success">-₹{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-end mb-2">
                        <span class="fw-bold fs-4 me-4">New Total:</span>
                        <span class="fw-bold fs-3 text-danger" id="preview-total">
                            ₹{{ number_format($order->final_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end gap-3">
                <button type="button" class="btn btn-primary" id="apply-discount-btn"
                    @if ($freezePricing) disabled @endif>
                    <span class="indicator-label">Update Pricing</span>
                    <span class="indicator-progress">Processing...
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                    </span>
                </button>

                <button id="resend-payment-link" class="btn btn-success" data-order-id="{{ $order->id }}"
                    @if (!$order->final_amount || $freezePricing) disabled @endif>
                    <i class="ki-duotone ki-send fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Resend Payment Link
                </button>
            </div>
        </div>
    </div>
