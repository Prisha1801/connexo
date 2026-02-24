<!doctype html>
<html>
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
    <body>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
            const options = {
                subscription_id: "{{ $subscription['id'] }}",
                key: "{{ env('RAZORPAY_KEY') }}",
                name: "{{ config('app.name') }}",
                description: "Subscription for {{ $package->name }}",
                // image: "https://domain.com/logo.png",
                handler: function (response){
                    // response contains razorpay_payment_id, razorpay_subscription_id, razorpay_signature
                    // We will redirect to server callback to verify and store
                    const params = new URLSearchParams({
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_subscription_id: response.razorpay_subscription_id,
                        razorpay_signature: response.razorpay_signature,
                    });
                    window.location.href = "{{ route('razor.pay.payment.success') }}?" + params.toString();
                },
                prefill: {
                    name: "{{ auth()->user()->name }}",
                    email: "{{ auth()->user()->email }}"
                },
                theme: { color: "#528FF0" }
            };


            const rzp = new Razorpay(options);
            rzp.open();


            // close page when modal closed
            rzp.on('checkout.dismissed', function () {
            window.location.href = "{{ url('/') }}";
            });
        </script>
    </body>
</html>