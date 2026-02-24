@extends('frontend.layout.master')
@section('head')
<link rel="stylesheet" href="{{ asset('vendor/IntlTelInput/intlTelInput.css')}}">
@endsection
@section('content')
<main class="main">
  <section class="section box-page-register">
    <div class="container">
      <div class="row">
        <div class="col-lg-5">
          <div class="box-steps-small">
            <div class="item-number hover-up active wow animate__animated animate__fadeInLeft" data-wow-delay=".0s">
              <div class="num-ele">1</div>
              <div class="info-num">
                <h5 class="color-brand-1 mb-15">Quick & Easy Sign-Up</h5>
                <p class="font-md color-grey-500">Start your journey in just a few clicks by filling out our simple
                  registration form with your basic details.</p>
              </div>
            </div>
            <div class="item-number hover-up wow animate__animated animate__fadeInLeft" data-wow-delay=".1s">
              <div class="num-ele">2</div>
              <div class="info-num">
                <h5 class="color-brand-1 mb-15">Customized Solutions</h5>
                <p class="font-md color-grey-500">Gain access to services and tools tailored to enhance your WhatsApp
                  Business API experience.</p>
              </div>
            </div>
            <div class="item-number hover-up wow animate__animated animate__fadeInLeft" data-wow-delay=".2s">
              <div class="num-ele">3</div>
              <div class="info-num">
                <h5 class="color-brand-1 mb-15">Support at Your Fingertips</h5>
                <p class="font-md color-grey-500">Access dedicated customer support to help you make the most of your
                  WhatsApp API integrations.</p>
              </div>
            </div>
            <div class="item-number hover-up wow animate__animated animate__fadeInLeft" data-wow-delay=".3s">
              <div class="num-ele">4</div>
              <div class="info-num">
                <h5 class="color-brand-1 mb-15">Business Transformation</h5>
                <p class="font-md color-grey-500">Register now to leverage the full power of Anantkamalwademo's
                  solutions for business growth and communication.</p>
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
            <h2 class="color-brand-1 mb-15 wow animate__animated animate__fadeIn" data-wow-delay=".0s">Create an account
            </h2>
            <p class="font-md color-grey-500 wow animate__animated animate__fadeIn" data-wow-delay=".0s">Create an
              account today and start using our platform</p>
            <div class="line-register mt-25 mb-50"></div>
            <x-validation-errors class="mb-4" />
            @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 text-success">
          {{ session('status') }}
        </div>
      @endif
            <form method="POST" action="{{ route('register') }}">
              @csrf
              <div class="row wow animate__animated animate__fadeIn" data-wow-delay=".0s">
                <div class="col-lg-6 col-sm-6">
                  <div class="form-group mb-25">
                    <input id="name" name="name" value="{{old('name')}}" required autofocus autocomplete="name"
                      class="form-control icon-name" type="text" placeholder="{{__('Your name')}}">
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                  <div class="form-group mb-25">
                    <input id="phone" name="phone" value="{{old('phone')}}" required autocomplete="phone"
                      class="form-control icon-phone" type="text" placeholder="{{__('Whatsapp No')}}">
                    <input type="hidden" id="country_code" name="country_code" value="" />
                  </div>
                </div>
                <div class="col-lg-12 col-sm-12">
                  <div class="form-group mb-25">
                    <input type="email" name="email" value="{{old('email')}}" required autocomplete="username"
                      class="form-control icon-email" placeholder="{{__('Email')}}">
                  </div>
                </div>

                <div class="col-lg-6 col-sm-6">
                  <div class="form-group mb-25">
                    <input id="password" type="password" name="password" class="form-control icon-password"
                      placeholder="{{__('Password')}}" required autocomplete="new-password">
                  </div>
                </div>
                <div class="col-lg-6 col-sm-6">
                  <div class="form-group mb-25">
                    <input class="form-control icon-password" placeholder="{{__('Re-password')}}" type="password"
                      name="password_confirmation" required autocomplete="new-password">
                  </div>
                </div>
                <div class="col-lg-12">

                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                  <div class="form-group mb-25"><a class="btn btn-border-80 btn-full" href="#"><img
                        class="d-inline-block align-middle mr-5" src="assets/imgs/page/register/google.svg"
                        alt="iori">Google</a></div>
                </div>
                <div class="col-lg-3 col-md-3 col-sm-6 col-6">
                  <div class="form-group mb-25"><a class="btn btn-border-80 btn-full" href="#"><img
                        class="d-inline-block align-middle mr-5" src="assets/imgs/page/register/fb.svg"
                        alt="iori">Facebook</a></div>
                </div>
                <div class="col-lg-12 mt-15">
                  @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="form-group mb-25">
                    <label class="cb-container">
                      <input type="checkbox" name="terms" checked="checked" required>
                      <span class="text-small">
                      {!! __('I have read and agree to the :term-conditions and the :privacy-policy of this website.', [
            'term-conditions' => '<a target="_blank" href="' . route('terms-conditions') . '" class="d-inline-block color-success">' . __('Terms & Conditions') . '</a>',
            'privacy-policy' => '<a target="_blank" href="' . route('privacy-policy') . '" class="d-inline-block color-success">' . __('Privacy Policy') . '</a>',
            ]) !!}
                      I have read and agree to the Terms & Conditions and the Privacy Policy of this website.
                      </span><span class="checkmark"></span>
                    </label>
                    </div>
          @endif
                  <div class="form-group mb-25">
                    <label class="cb-container">
                      <input type="checkbox"><span class="text-small">I want to receive design inspiration and product
                        updates. (No spam. You can opt-out anytime.)</span><span class="checkmark"></span>
                    </label>
                  </div>
                </div>
              </div>
              <div class="row align-items-center mt-30 wow animate__animated animate__fadeIn" data-wow-delay=".0s">
                <div class="col-xl-5 col-lg-5 col-md-5 col-sm-6 col-6">
                  <div class="form-group">
                    <button class="btn btn-brand-lg btn-full font-md-bold" type="submit">{{__('Sign up now')}}</button>
                  </div>
                </div>
                <div class="col-xl-7 col-lg-7 col-md-7 col-sm-6 col-6"><span
                    class="d-inline-block align-middle font-sm color-grey-500">Already have an account?</span><a
                    class="d-inline-block align-middle color-success ml-3" href="{{route('login')}}"> Sign In</a></div>
              </div>
            </form>
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

<script src="{{ asset('vendor/IntlTelInput/intlTelInput.min.js')}}"></script>
<script>
  const input = document.querySelector("#phone");
  const country_code = document.querySelector("#country_code");
  const iti = window.intlTelInput(input, {
    initialCountry: "auto",
    separateDialCode: true,
    geoIpLookup: callback => {
      fetch("https://ipapi.co/json")
        .then(res => res.json())
        .then(data => callback(data.country_code))
        .catch(() => callback("us"));
    },
    utilsScript: "{{ asset('vendor/IntlTelInput/utils.js')}}",
  });
  input.addEventListener("countrychange", function () {
    // do something with iti.getSelectedCountryData()
    country_code.value = iti.getSelectedCountryData().dialCode;
  });
</script>
@endsection