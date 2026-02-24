@extends('general.index', $setup)

@section('cardbody')
        <!-- Page Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-7">
            <div class="d-flex align-items-center">
                <h1 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">
                    <i class="ki-duotone ki-cart fs-2hx me-4 text-primary">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Orders Management
                </h1>
                <span class="badge badge-light-primary fs-8 fw-bolder ms-4">{{ $setup['items']->total() }}
                    {{ __('Orders') }}</span>
            </div>

            <div class="d-flex align-items-center gap-3">
                @if ($setup['items']->isNotEmpty())
                    <div class="d-flex align-items-center position-relative">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute start-0 ms-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" id="order-search" class="form-control form-control-solid w-250px ps-10"
                            placeholder="Search orders, customers, phone...">
                    </div>
                @endif

                <!-- Advanced Search Button -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#advancedSearchModal">
                    <i class="ki-duotone ki-filter fs-3 me-1">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    <span class="d-none d-md-inline">Advanced Search</span>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        @if ($setup['items']->isNotEmpty())
            <div class="row g-6 mb-7">
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        <span class="fs-2hx fw-bolder text-dark">{{ $stats['totalOrders'] }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('Total Orders') }}</span>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        <span class="fs-2hx fw-bolder text-success">{{ $stats['paidOrders'] }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('Paid Orders') }}</span>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        <span class="fs-2hx fw-bolder text-warning">{{ $stats['pendingOrders'] }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('Pending Orders') }}</span>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        <span class="fs-2hx fw-bolder text-info">₹{{ number_format($stats['totalRevenue'], 2) }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('Total Revenue') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Orders Table Card -->
        <div class="card">
            @if ($setup['items']->isNotEmpty())
                <!-- Card Header -->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder m-0">{{ __('All Orders') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fs-7">{{ __('Filter by status:') }}</span>
                            <select class="form-select form-select-sm w-150px" id="status-filter">
                                <option value="">{{ __('All Orders') }}</option>
                                <option value="order" {{ request('order_status') == 'order' ? 'selected' : '' }}>Pending
                                </option>
                                <option value="accepted" {{ request('order_status') == 'accepted' ? 'selected' : '' }}>
                                    Accepted</option>
                                <option value="dispatched" {{ request('order_status') == 'dispatched' ? 'selected' : '' }}>
                                    Dispatched</option>
                                <option value="delivered" {{ request('order_status') == 'delivered' ? 'selected' : '' }}>
                                    Delivered</option>
                                <option value="canceled" {{ request('order_status') == 'canceled' ? 'selected' : '' }}>
                                    Canceled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Debugging Info -->
                <div class="alert alert-info d-flex align-items-center mx-6 mt-4 mb-0">
                    <i class="ki-duotone ki-information fs-2 me-4">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                    <div class="flex-grow-1">
                        <strong>Current Filters:</strong>
                        Search: "{{ request('search') }}"
                        | Phone: "{{ request('phone') }}"
                        | Order ID: "{{ request('order_id') }}"
                        | Customer: "{{ request('customer_name') }}"
                        | Payment: {{ request('payment_status') ? ucfirst(request('payment_status')) : 'All' }}
                        | Status: {{ request('order_status') ? ucfirst(request('order_status')) : 'All' }}
                    </div>
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-light ms-3">Clear Filters</a>
                </div>

                <!-- Table -->
                <div class="card-body pt-6">
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-dashed gy-4 align-middle fs-6">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-80px">#</th>
                                    <th class="min-w-150px">{{ __('Date & Time') }}</th>
                                    <th class="min-w-120px">{{ __('Order ID') }}</th>
                                    <th class="min-w-200px">{{ __('Customer') }}</th>
                                    <th class="min-w-120px">{{ __('Amount') }}</th>
                                    <th class="min-w-120px">{{ __('Payment') }}</th>
                                    <th class="min-w-120px">{{ __('Status') }}</th>
                                    <th class="min-w-100px text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach ($setup['items'] as $key => $item)
                                    <tr class="order-row" data-order-id="{{ $item->id }}"
                                        data-status="{{ $item->status }}"
                                        data-payment-status="{{ $item->payment_status }}"
                                        data-customer-name="{{ strtolower($item->user_name) }}"
                                        data-order-number="{{ $item->reference_id }}"
                                        data-phone="{{ $item->phone_number }}">
                                        <!-- Serial Number -->
                                        <td>
                                            <span class="text-gray-600 fw-bold">
                                                {{ ($setup['items']->currentPage() - 1) * $setup['items']->perPage() + $key + 1 }}
                                            </span>
                                        </td>

                                        <!-- Date & Time -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px symbol-circle me-4">
                                                    <div class="symbol-label bg-light-primary">
                                                        <i class="ki-duotone ki-calendar-8 fs-2 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="text-dark fw-bold fs-7">{{ $item->created_at->format('d M Y') }}</span>
                                                    <span
                                                        class="text-muted fs-8">{{ $item->created_at->format('h:i A') }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Order ID -->
                                        <td>
                                            <span
                                                class="text-gray-800 fw-bold badge badge-light-dark">#{{ $item->reference_id }}</span>
                                        </td>

                                        <!-- Customer Information -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px symbol-circle me-4">
                                                    <div class="symbol-label bg-light-info">
                                                        <span
                                                            class="fs-4 text-info fw-bold">{{ substr($item->user_name, 0, 1) }}</span>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span
                                                        class="text-dark fw-bold fs-6 mb-1">{{ $item->user_name }}</span>
                                                    <span class="text-muted fs-7">{{ $item->phone_number }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Order Amount -->
                                        <td>
                                            @php
                                                $finalAmount =
                                                    ($item->subtotal_offset ?? 1) != 0
                                                        ? $item->subtotal_value / $item->subtotal_offset
                                                        : 0;
                                                $shipping = $item->shipping_cast ?? 0;
                                                $discountType = $item->discount_type ?? null;
                                                $discountValue = $item->discount ?? 0;
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
                                            <div class="d-flex align-items-center">
                                                <i class="ki-duotone ki-dollar fs-2 me-2 text-warning">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <span
                                                    class="text-gray-800 fw-bold fs-5">₹{{ number_format($total, 2) }}</span>
                                            </div>
                                        </td>

                                        <!-- Payment Status -->
                                        <td>
                                            @php
                                                $paymentStatus = strtolower($item->payment_status);
                                                $paymentMethod = strtolower($item->payment_method);

                                                if (empty($paymentStatus)) {
                                                    $statusLabel = 'Pending';
                                                    $badgeClass = 'warning';
                                                } elseif ($paymentStatus === 'paid' && !empty($paymentMethod)) {
                                                    $statusLabel = 'PAID / ' . strtoupper($paymentMethod);
                                                    $badgeClass = 'success';
                                                } else {
                                                    $statusMap = [
                                                        'failed' => ['label' => 'Failed', 'class' => 'danger'],
                                                        'refunded' => ['label' => 'Refunded', 'class' => 'dark'],
                                                        'unpaid' => ['label' => 'Unpaid', 'class' => 'warning'],
                                                    ];
                                                    $badge = $statusMap[$paymentStatus] ?? [
                                                        'label' => ucfirst($paymentStatus),
                                                        'class' => 'secondary',
                                                    ];
                                                    $statusLabel = $badge['label'];
                                                    $badgeClass = $badge['class'];
                                                }
                                            @endphp
                                            <span
                                                class="badge badge-light-{{ $badgeClass }} py-3 px-3 fw-bold">{{ $statusLabel }}</span>
                                        </td>

                                        <!-- Order Status -->
                                        <td>
                                            @php
                                                $status = $item->status;
                                                $statusMap = [
                                                    'order' => ['label' => 'Pending', 'class' => 'danger'],
                                                    'accepted' => ['label' => 'Accepted', 'class' => 'info'],
                                                    'preparing' => ['label' => 'Preparing', 'class' => 'warning'],
                                                    'ready_to_dispatch' => [
                                                        'label' => 'Ready to Dispatch',
                                                        'class' => 'primary',
                                                    ],
                                                    'dispatched' => ['label' => 'Dispatched', 'class' => 'primary'],
                                                    'delivered' => ['label' => 'Delivered', 'class' => 'success'],
                                                    'canceled' => ['label' => 'Canceled', 'class' => 'dark'],
                                                ];
                                                $badge = $statusMap[$status] ?? [
                                                    'label' => ucfirst($status),
                                                    'class' => 'secondary',
                                                ];
                                            @endphp
                                            <div class="d-flex align-items-center gap-2">
                                                <span
                                                    class="badge badge-light-{{ $badge['class'] }} py-3 px-3 fw-bold status-badge">{{ $badge['label'] }}</span>
                                                <a href="{{ url()->current() . '?order_status=' . $status }}"
                                                    class="btn btn-icon btn-sm btn-light" title="Filter by this status"
                                                    data-bs-toggle="tooltip">
                                                    <i class="ki-duotone ki-filter fs-2"></i>
                                                </a>
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <!-- View Order -->
                                                <a href="{{ route('catalog.itemIndex', $item->id) }}"
                                                    class="btn btn-icon btn-light-primary btn-sm"
                                                    title="View Order Details" data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                    <i class="ki-duotone ki-eye fs-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </a>

                                                <!-- Print Invoice -->
                                                @if (!in_array($item->status, ['canceled']))
                                                    <button type="button" class="btn btn-icon btn-light-success btn-sm"
                                                        data-bs-toggle="modal" data-bs-target="#printInvoiceModal"
                                                        data-order-id="{{ $item->id }}" title="Print Invoice"
                                                        data-bs-toggle="tooltip" data-bs-placement="top">
                                                        <i class="ki-duotone ki-printer fs-4">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if ($setup['items']->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center">
                            <span class="text-muted fs-7">
                                {{ __('Showing') }}
                                <strong>{{ $setup['items']->firstItem() ?? 0 }}</strong>
                                {{ __('to') }}
                                <strong>{{ $setup['items']->lastItem() ?? 0 }}</strong>
                                {{ __('of') }}
                                <strong>{{ $setup['items']->total() }}</strong>
                                {{ __('entries') }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap py-2">
                            {{ $setup['items']->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="card-body">
                    <div class="text-center py-10">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <!-- Icon -->
                            <div class="symbol symbol-100px symbol-circle mb-5">
                                <div class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-cart fs-2hx text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>

                            <!-- Text -->
                            <h3 class="text-dark fw-bolder mb-3">{{ __('No Orders Found') }}</h3>
                            <p class="text-muted fs-5 mb-6 w-lg-400px">
                                {{ __('There are no orders matching your search criteria. Try adjusting your filters or search terms.') }}
                            </p>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3">
                                <a href="{{ url()->current() }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-arrow-left fs-2 me-1"></i>
                                    {{ __('Clear Filters') }}
                                </a>
                                <a href="#" class="btn btn-light" data-bs-toggle="modal"
                                    data-bs-target="#advancedSearchModal">
                                    <i class="ki-duotone ki-filter fs-2 me-1"></i>
                                    {{ __('Advanced Search') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Advanced Search Modal (Keep existing functionality) -->
    <div class="modal fade" id="advancedSearchModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">Advanced Order Search</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ url()->current() }}" id="advancedSearchForm">
                    @foreach (request()->except('phone', 'order_id', 'customer_name', 'payment_status', 'order_status', 'page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <div class="modal-body">
                        <div class="row g-5 mb-5">
                            <!-- Phone Number -->
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <div class="position-relative">
                                    <i class="ki-duotone ki-phone fs-3 position-absolute ms-3 mt-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" name="phone" value="{{ request('phone') }}"
                                        class="form-control form-control-solid ps-10" placeholder="Enter phone number" />
                                </div>
                            </div>

                            <!-- Order ID -->
                            <div class="col-md-6">
                                <label class="form-label">Order ID</label>
                                <div class="position-relative">
                                    <i class="ki-duotone ki-barcode fs-3 position-absolute ms-3 mt-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" name="order_id" value="{{ request('order_id') }}"
                                        class="form-control form-control-solid ps-10" placeholder="Enter order ID" />
                                </div>
                            </div>

                            <!-- Customer Name -->
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <div class="position-relative">
                                    <i class="ki-duotone ki-user fs-3 position-absolute ms-3 mt-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <input type="text" name="customer_name" value="{{ request('customer_name') }}"
                                        class="form-control form-control-solid ps-10" placeholder="Enter customer name" />
                                </div>
                            </div>

                            <!-- Payment Status -->
                            <div class="col-md-6">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" class="form-select form-select-solid">
                                    <option value="">All Payment Statuses</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>
                                        Paid</option>
                                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>
                                        Unpaid</option>
                                    <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>
                                        Failed</option>
                                    <option value="refunded"
                                        {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                            </div>

                            <!-- Order Status -->
                            <div class="col-md-6">
                                <label class="form-label">Order Status</label>
                                <select name="order_status" class="form-select form-select-solid">
                                    <option value="">All Order Statuses</option>
                                    <option value="order" {{ request('order_status') == 'order' ? 'selected' : '' }}>
                                        Pending</option>
                                    <option value="accepted"
                                        {{ request('order_status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="dispatched"
                                        {{ request('order_status') == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                                    <option value="delivered"
                                        {{ request('order_status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="canceled"
                                        {{ request('order_status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Transaction Type</label>
                                <select name="transaction_type" class="form-select form-select-solid">
                                    <option value="">All Types</option>
                                    <option value="upi" {{ request('transaction_type') == 'upi' ? 'selected' : '' }}>
                                        UPI</option>
                                    <option value="cash" {{ request('transaction_type') == 'cash' ? 'selected' : '' }}>
                                        Cash</option>
                                    <option value="bank_transfer"
                                        {{ request('transaction_type') == 'bank_transfer' ? 'selected' : '' }}>Bank
                                        Transfer</option>
                                    <option value="card" {{ request('transaction_type') == 'card' ? 'selected' : '' }}>
                                        Card</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" onclick="resetFilters()">Reset Filters</button>
                        <button type="submit" class="btn btn-primary" id="applyFiltersBtn">Apply Filters</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Print Options Modal (Keep existing functionality) -->
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
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Search functionality
            const searchInput = document.getElementById('order-search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const statusFilterValue = document.getElementById('status-filter') ? document
                        .getElementById('status-filter').value : '';
                    const rows = document.querySelectorAll('.order-row');

                    if (searchTerm.length === 0) {
                        // Show all rows based on status filter
                        rows.forEach(row => {
                            if (statusFilterValue === '' || row.getAttribute('data-status') ===
                                statusFilterValue) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                        return;
                    }

                    rows.forEach(row => {
                        const customerName = row.getAttribute('data-customer-name');
                        const orderNumber = row.getAttribute('data-order-number');
                        const phone = row.getAttribute('data-phone');

                        const matchesSearch = customerName.includes(searchTerm) ||
                            orderNumber.includes(searchTerm) ||
                            phone.includes(searchTerm);

                        const matchesStatus = statusFilterValue === '' ||
                            row.getAttribute('data-status') === statusFilterValue;

                        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
                    });
                });
            }

            // Status filter functionality
            const statusFilter = document.getElementById('status-filter');
            if (statusFilter) {
                statusFilter.addEventListener('change', function() {
                    const status = this.value;
                    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                    const rows = document.querySelectorAll('.order-row');

                    rows.forEach(row => {
                        const customerName = row.getAttribute('data-customer-name');
                        const orderNumber = row.getAttribute('data-order-number');
                        const phone = row.getAttribute('data-phone');

                        const matchesSearch = searchTerm === '' ||
                            customerName.includes(searchTerm) ||
                            orderNumber.includes(searchTerm) ||
                            phone.includes(searchTerm);

                        const matchesStatus = status === '' || row.getAttribute('data-status') ===
                            status;

                        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
                    });
                });
            }

            // Print modal functionality (keep existing)
            const modal = document.getElementById('printInvoiceModal');
            const confirmBtn = document.getElementById('confirmPrintBtn');

            const printOptions = document.querySelectorAll('.print-option');
            printOptions.forEach(option => {
                option.addEventListener('click', function() {
                    printOptions.forEach(opt => {
                        opt.classList.remove('active');
                        opt.classList.add('card-dashed');
                    });
                    this.classList.add('active');
                    this.classList.remove('card-dashed');
                    const value = this.getAttribute('data-value');
                    document.querySelector(`input[name="printType"][value="${value}"]`).checked =
                        true;
                });
            });

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

        function resetFilters() {
            document.querySelectorAll('#advancedSearchForm input[type="text"]').forEach(input => {
                input.value = '';
            });
            document.querySelectorAll('#advancedSearchForm select').forEach(select => {
                select.selectedIndex = 0;
            });
            document.getElementById('advancedSearchForm').submit();
        }
    </script>
@endpush

@push('css')
    <style>
        .card-dashed {
            border: 1px dashed #e4e6ef;
            background: #fafafa;
        }

        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .order-row td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .print-option.active {
            border-color: #009ef7;
            background-color: #f1faff;
        }

        .print-option {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .print-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        @media (max-width: 768px) {
            .d-flex.flex-column.flex-sm-row {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            .d-flex.flex-column.flex-sm-row .btn {
                margin-top: 1rem;
                width: 100%;
            }

            .w-250px {
                width: 100% !important;
            }
        }
    </style>
@endpush

<!-- Keep your existing Pusher JS and other functionality -->
@section('topjs')
    <!-- Pusher JS -->
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>

    <!-- Metronic Modal -->
    <div class="modal fade" id="kt_order_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-650px">
            <div class="modal-content rounded">
                <!-- Modal header -->
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </div>
                </div>

                <!-- Modal body -->
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <!-- Title -->
                    <div class="text-center mb-10">
                        <i class="ki-duotone ki-bell fs-2x text-primary">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <h1 class="fw-bold my-3">New Order Received!</h1>
                    </div>

                    <!-- Order details -->
                    <div class="d-flex flex-column mb-7 fv-row">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px symbol-circle me-5">
                                <span class="symbol-label bg-light-primary text-primary fs-2 fw-bold">
                                    <i class="ki-duotone ki-user fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600 fs-6">Customer</span>
                                <div id="kt_order_customer" class="fw-bold fs-4 text-gray-800"></div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-3"></div>

                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px symbol-circle me-5">
                                <span class="symbol-label bg-light-success text-success fs-2 fw-bold">
                                    <i class="ki-duotone ki-phone fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600 fs-6">Phone number</span>
                                <div id="kt_order_phone_number" class="fw-bold fs-4 text-gray-800"></div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-3"></div>

                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px symbol-circle me-5">
                                <span class="symbol-label bg-light-success text-success fs-2 fw-bold">
                                    <i class="ki-duotone ki-scan-barcode fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                        <span class="path3"></span>
                                        <span class="path4"></span>
                                        <span class="path5"></span>
                                        <span class="path6"></span>
                                        <span class="path7"></span>
                                        <span class="path8"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600 fs-6">Order ID</span>
                                <div id="kt_order_id" class="fw-bold fs-4 text-gray-800"></div>
                            </div>
                        </div>

                        <div class="separator separator-dashed my-3"></div>

                        <div class="d-flex align-items-center mb-8">
                            <div class="symbol symbol-50px symbol-circle me-5">
                                <span class="symbol-label bg-light-warning text-warning fs-2 fw-bold">
                                    <i class="ki-duotone ki-paypal fs-2">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-600 fs-6">Order Total</span>
                                <div id="kt_order_total" class="fw-bold fs-4 text-gray-800"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex flex-center flex-wrap">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                            Close Notification
                        </button>
                        <a href="#" id="kt_order_view_btn" class="btn btn-primary">
                            <i class="ki-duotone ki-eye fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                            </i>
                            View Order Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Pusher (keep existing)
        const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            encrypted: true,
            forceTLS: true
        });

        function playOrderSound() {
            try {
                const audio = new Audio('{{ asset('sounds/order-bell.mp3') }}');
                audio.play().catch(e => console.error('Order sound play failed:', e));
            } catch (e) {
                console.error('Order sound error:', e);
            }
        }

        const channel = pusher.subscribe('orders');
        channel.bind('App\\Events\\NewOrderReceived', function(data) {
            playOrderSound();
            document.getElementById('kt_order_customer').textContent = data.user_name;
            document.getElementById('kt_order_phone_number').textContent = data.phone_number;
            document.getElementById('kt_order_id').textContent = data.reference_id;
            document.getElementById('kt_order_total').textContent = '₹' + data.total;
            document.getElementById('kt_order_view_btn').href = data.url;
            const modal = new bootstrap.Modal(document.getElementById('kt_order_modal'));
            modal.show();
        });
    </script>
@endsection
