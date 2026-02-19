@extends('frontend.layout.master')
@section('head')
<!-- <link rel="stylesheet" href="{{ asset('vendor/IntlTelInput/intlTelInput.css')}}"> -->
@endsection
@section('content')
<main class="main">
    <section class="section box-page-register">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="box-steps-small">
                        <div class="item-number hover-up  wow animate__animated animate__fadeInLeft" data-wow-delay=".0s">
                            <div class="num-ele">1</div>
                            <div class="info-num">
                                <h5 class="color-brand-1 mb-15">Register</h5>
                                <p class="font-md color-grey-500">All you need is your name, email and a strong password, Or use your social media accounts.</p>
                            </div>
                        </div>
                        <div class="item-number hover-up active wow animate__animated animate__fadeInLeft" data-wow-delay=".1s">
                            <div class="num-ele">2</div>
                            <div class="info-num">
                                <h5 class="color-brand-1 mb-15">Activate</h5>
                                <p class="font-md color-grey-500">Use the code sent to your email to activate your account.</p>
                            </div>
                        </div>
                        <div class="item-number hover-up wow animate__animated animate__fadeInLeft" data-wow-delay=".2s">
                            <div class="num-ele">3</div>
                            <div class="info-num">
                                <h5 class="color-brand-1 mb-15">Open a trading account</h5>
                                <p class="font-md color-grey-500">Create a real or demo trading account on our platform. No credit card required.</p>
                            </div>
                        </div>
                        <div class="item-number hover-up wow animate__animated animate__fadeInLeft" data-wow-delay=".3s">
                            <div class="num-ele">4</div>
                            <div class="info-num">
                                <h5 class="color-brand-1 mb-15">Connect with investors</h5>
                                <p class="font-md color-grey-500">With a real-time analysis system you will become a professional investor.</p>
                            </div>
                        </div>
                        <div class="item-number hover-up wow animate__animated animate__fadeInLeft" data-wow-delay=".4s">
                            <div class="num-ele">5</div>
                            <div class="info-num">
                                <h5 class="color-brand-1 mb-15">Almost done</h5>
                                <p class="font-md color-grey-500">Start your amazing journey on our platform.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="box-register">
                        <h2 class="color-brand-1 mb-15 wow animate__animated animate__fadeIn" data-wow-delay=".0s">OTP Verification</h2>
                        <p class="font-md color-grey-500 wow animate__animated animate__fadeIn" data-wow-delay=".0s">Check your whatsapp we have sent you OTP at {{isset(auth()->user()->phone)?auth()->user()->phone:''}}</p>
                        <div class="line-register mt-25 mb-50"></div>
                        <x-validation-errors class="mb-4" />
                        @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 text-success">
                            {{ session('status') }}
                        </div>
                        @endif
                        <form method="POST" action="{{ route('otp.verify') }}">
                            @csrf
                            <div class="row wow animate__animated animate__fadeIn" data-wow-delay=".0s">
                                <div class="col-lg-10 col-sm-10">
                                    <div class="form-group mb-25">
                                        <input type="number" id="otp" name="otp" value="" autofocus class="form-control icon-name" placeholder="{{__('Enter OTP')}}">
                                    </div>
                                </div>
                                <div class="col-lg-10 col-sm-10 alertMessage">
                                    
                                </div>
                            </div>
                            <div class="row align-items-center mt-30 wow animate__animated animate__fadeIn" data-wow-delay=".0s">
                                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-6 col-6">
                                    <div class="form-group">
                                        <button class="btn btn-brand-lg btn-full font-md-bold" onclick="verifyOtp()" type="button" id="verify-otp-button">
                                            {{__('Verify')}}
                                            <span id="verify-loader" style="display:none;">⏳</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-xl-7 col-lg-7 col-md-7 col-sm-6 col-6">
                                    <span class="d-inline-block align-middle font-sm color-grey-500" id="otp-text">
                                        Resend OTP in
                                    </span>
                                    <span id="countdown" class="color-success">1:30</span>
                                    <button id="resend-otp-button" class="d-inline-block btn btn-brand-lg ml-3 resend-otp d-none" onclick="resendOtp()" type="button">
                                        {{__('Resend OTP')}}
                                        <span id="resend-loader" style="display:none;">⏳</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
    <section class="section mt-50">
    </section>
</main>
@endsection

@section('post_js')

<script>
    let timer = "{{config('otp.otp_timer')}}"; // Timer in seconds 
    const countdownElement = document.getElementById('countdown');
    const resendButton = document.getElementById('resend-otp-button');
    const otpText = document.getElementById('otp-text');

    const verifyButton = document.getElementById('verify-otp-button');
    const resendLoader = document.getElementById('resend-loader');
    const verifyLoader = document.getElementById('verify-loader');

    function startTimer() {
        let countdown = setInterval(() => {
            timer--;
            let minutes = Math.floor(timer / 60);
            let seconds = timer % 60;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            countdownElement.innerText = minutes + ':' + seconds;
            if (timer <= 0) {
                clearInterval(countdown);
                countdownElement.style.display = 'none';
                otpText.setAttribute('style', 'display:none ! important');
                resendButton.classList.remove('d-none');
            }
        }, 1000);
    }

    startTimer();


    function resendOtp() {
        resendLoader.style.display = 'inline'; // Show loader
        resendButton.classList.add('disabled');
        fetch('{{route("resend.otp")}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                resendLoader.style.display = 'none';
                if (data.message) {
                    $(".alertMessage").html('<div class="alert alert-success alert-dismissible fade show" role="alert">'+data.message+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>')
                    timer = "{{config('otp.otp_timer')}}"; // Reset the timer 
                    resendButton.classList.add('d-none');
                    otpText.setAttribute('style', 'display:inline');
                    countdownElement.style.display = 'inline'; // Show countdown again
                    startTimer(); // Restart the timer
                } else if (data.error) {
                    $(".alertMessage").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+data.error+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>')
                }
                resendButton.classList.remove('disabled');
            })
            .catch(error => {
                console.error('Error:', error);
                resendLoader.style.display = 'none'; // Hide loader on error
                resendButton.classList.remove('disabled');
            });
    }

    function verifyOtp() {
        const otp = document.getElementById('otp').value;

        if (!otp || otp.length !== 6) {
            $(".alertMessage").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">Please enter a valid 6-digit OTP.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>')
            return;
        }
        verifyButton.classList.add('disabled');

        verifyLoader.style.display = 'inline'; // Show loader

        fetch('{{ route("verify-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    otp: otp
                })
            })
            .then(response => response.json())
            .then(data => {
                verifyButton.classList.remove('disabled');
                verifyLoader.style.display = 'none'; // Hide loader
                if (data.message) {
                    $(".alertMessage").html('<div class="alert alert-success alert-dismissible fade show" role="alert">'+data.message+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>')
                    window.location.href = '{{route("home") }}';
                } else if (data.error) {
                    $(".alertMessage").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+data.error+'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>')
                    
                }
            })
            .catch(error => {
                verifyButton.classList.remove('disabled');
                console.error('Error:', error);
                verifyLoader.style.display = 'none'; // Hide loader on error
            });
    }
</script>
@endsection