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

    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();

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

        // Function to freeze UI for free window expiration
        function freezeUIForFreeWindow() {
            // Disable all interactive elements except status select
            $('.btn-edit-shipping').prop('disabled', true);
            $('.btn-dispatch-order').prop('disabled', true);
            $('.manual-cancel-order-btn').prop('disabled', true);
            $('[data-bs-target="#shippingModal"]').prop('disabled', true);
            $('[data-bs-target="#dispatchModal"]').prop('disabled', true);
            $('[data-bs-target="#contactModal"]').prop('disabled', true);
            $('[data-bs-target="#orderNoteModal"]').prop('disabled', true);
            $('#resendAddressFormBtn').prop('disabled', true);
            $('#apply-discount-btn').prop('disabled', true);
            $('#resend-payment-link').prop('disabled', true);
        }

        // Function to unfreeze UI when free window is active
        function unfreezeUIForFreeWindow() {
            // Only enable if order is not in terminal state
            if (orderStatus !== 'delivered' && orderStatus !== 'canceled') {
                $('.btn-edit-shipping').prop('disabled', false);
                $('.btn-dispatch-order').prop('disabled', false);
                $('.manual-cancel-order-btn').prop('disabled', false);
                $('[data-bs-target="#shippingModal"]').prop('disabled', false);
                $('[data-bs-target="#paymentModal"]').prop('disabled', false);
                $('[data-bs-target="#dispatchModal"]').prop('disabled', false);
                $('[data-bs-target="#contactModal"]').prop('disabled', false);
                $('[data-bs-target="#orderNoteModal"]').prop('disabled', false);
                $('#resendAddressFormBtn').prop('disabled', false);
                $('#apply-discount-btn').prop('disabled', false);
                $('#resend-payment-link').prop('disabled', false);
            }
        }

        // Function to update UI state based on free window status
        function updateUIState() {
            if (isFreeWindowActive) {
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
        @if ($order->status == 'delivered')
            $('#order-status-select').prop('disabled', true);
            $('.btn-edit-shipping').prop('disabled', true);
            $('.btn-dispatch-order').hide();
            $('.order-status-badge').html('Delivered <i class="fas fa-lock ms-2"></i>');
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
            $('[data-bs-target="#shippingModal"]').prop('disabled', true);
            $('[data-bs-target="#paymentModal"]').prop('disabled', true);
            $('[data-bs-target="#dispatchModal"]').prop('disabled', true);
            $('[data-bs-target="#contactModal"]').prop('disabled', true);
            $('[data-bs-target="#orderNoteModal"]').prop('disabled', true);
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
        $('[name="shipping_amount"], [name="discount_value"], [name="discount_type"]').on('input change',
            function() {
                calculateTotal();
            });

        // Calculate total function
        function calculateTotal() {
            shipping = parseFloat($('[name="shipping_amount"]').val()) || 0;
            discountType = $('[name="discount_type"]').val();
            discountValue = parseFloat($('[name="discount_value"]').val()) || 0;

            // Calculate discount amount
            let discountAmount = 0;
            if (discountType && discountValue > 0) {
                if (discountType === 'percent') {
                    discountAmount = (subtotal * discountValue) / 100;
                } else {
                    discountAmount = Math.min(discountValue, subtotal);
                }
            }

            // Calculate new total
            const newTotal = (subtotal - discountAmount) + shipping;

            // Update preview
            $('#preview-shipping').text('₹' + shipping.toFixed(2));
            $('#preview-discount').text('-' + (discountType ?
                (discountType === 'percent' ? discountValue + '%' : '₹' + discountAmount.toFixed(2)) :
                '₹0.00'));
            $('#preview-total').text('₹' + newTotal.toFixed(2));
        }

        // Initialize calculation
        calculateTotal();

        // Apply discount form submission
        $('#apply-discount-form').submit(function(e) {
            e.preventDefault();
            const form = $(this);
            const btn = form.find('#apply-discount-btn');

            btn.attr('data-kt-indicator', 'on');
            btn.prop('disabled', true);

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        // Parse amounts as floats
                        const shipping = parseFloat(response.shipping);
                        const discountValue = parseFloat(response.discount_value);
                        const finalAmount = parseFloat(response.final_amount);

                        // Update order summary
                        $('#order-shipping-amount').text('₹' + shipping.toFixed(2));

                        // Properly handle discount visibility
                        if (discountValue > 0) {
                            $('#order-discount-row').show();

                            // Format discount display based on type
                            if (response.discount_type === 'percent') {
                                $('#order-discount-display').text(response.discount_value +
                                    '%');
                            } else {
                                $('#order-discount-display').text('₹' + parseFloat(response
                                    .discount).toFixed(2));
                            }
                        } else {
                            // Only hide if discount is 0 or empty

                            $('#order-discount-display').text('0.00%');
                            //$('#order-discount-row').hide();
                        }

                        $('#order-total-amount').text('₹' + finalAmount.toFixed(2));

                        // Enable resend button if not frozen
                        if (!{{ $freezePricing ? 'true' : 'false' }}) {
                            $('#resend-payment-link').prop('disabled', false);
                        }

                        // Show success toast
                        Swal.fire({
                            icon: 'success',
                            title: 'Pricing Updated!',
                            text: 'Shipping and discount applied successfully',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Failed to update pricing',
                            'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Server error',
                        'error');
                },
                complete: function() {
                    btn.removeAttr('data-kt-indicator');
                    btn.prop('disabled', false);
                }
            });
        });

        // Resend Payment Link with confirmation
        $('#resend-payment-link').click(function(e) {
            e.preventDefault(); // Prevent form submission
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
                                    'Failed to resend link',
                                    'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message ||
                                'Server error',
                                'error');
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

        // Textarea input handler to clear button selection
        $('#cancel-reason-text').on('input', function() {
            $('.reason-btn').removeClass('active');

            // If user clears textarea, clear selection
            if ($(this).val().trim() === '') {
                $('.reason-btn').removeClass('active');
            }
        });

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

            if (shippingMethod === 'Local Pickup') {
                // Hide and disable validation for address fields
                addressFields.hide();
                addressFields.find('input, textarea').removeAttr('required');
            } else {
                // Show and require address fields
                addressFields.show();
                addressFields.find('input, textarea').attr('required', 'required');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInput = document.getElementById('productSearch');
            const productItems = document.querySelectorAll('.product-item');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                productItems.forEach(item => {
                    const title = item.querySelector('.card-title').textContent
                        .toLowerCase();
                    if (title.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Add button functionality
            const addButtons = document.querySelectorAll('.add-btn');
            addButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const card = this.closest('.product-card');
                    const productName = card.querySelector('.card-title').textContent;

                    // Create and show notification
                    const notification = document.createElement('div');
                    notification.className =
                        'position-fixed bottom-0 end-0 m-3 p-3 bg-success text-white rounded shadow';
                    notification.style.zIndex = '1050';
                    notification.style.transition = 'transform 0.3s ease';
                    notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <span>Added <strong>${productName}</strong> to cart!</span>
                </div>
            `;

                    document.body.appendChild(notification);

                    // Animate button
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    this.classList.remove('btn-primary');
                    this.classList.add('btn-success');

                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-plus"></i>';
                        this.classList.remove('btn-success');
                        this.classList.add('btn-primary');
                    }, 2000);

                    // Remove notification after delay
                    setTimeout(() => {
                        notification.style.transform = 'translateY(100px)';
                        setTimeout(() => {
                            notification.remove();
                        }, 300);
                    }, 3000);
                });
            });

            // Initialize Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll(
                '[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

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
