@extends('frontend.layout.master')

@section('content')
<main class="main">
    <section class="section banner-login position-relative float-start">
        <div class="box-banner-abs">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-xxl-5 col-xl-12 col-lg-12">
                        <div class="box-banner-login">
                            <h2 class="color-brand-1 mb-15 wow animate__animated animate__fadeIn" data-wow-delay=".0s"> Welcome back</h2>
                            <p class="font-md color-grey-500 wow animate__animated animate__fadeIn" data-wow-delay=".2s"> Fill your email address and password to sign in.</p>
                            <div class="line-login mt-25 mb-50"></div>
                            <x-validation-errors class="mb-4" />
                            @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600 text-success">
                                {{ session('status') }}
                            </div>
                            @endif
                            <form method="POST" action="{{ route('login') }}" id="loginform">
                                @csrf
                                <div class="row wow animate__animated animate__fadeIn" data-wow-delay=".4s">
                                    <div class="col-lg-12">
                                        <div class="form-group mb-25">
                                            <input type="email" id="email" name="email" value="{{old('email')}}" required autofocus autocomplete="username" class="form-control icon-user" type="text" placeholder="{{ __('Email') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group mb-25">
                                            <input class="form-control icon-password" id="password" placeholder="{{ __('Password') }}" type="password" name="password" required autocomplete="current-password">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-6 mt-15">
                                        <div class="form-group mb-25">
                                            <label class="cb-container">
                                                <input type="checkbox" name="remember" id="remember_me" checked="checked"><span class="text-small">{{ __('Remember me') }}</span><span class="checkmark"></span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-6 mt-15">
                                        @if(Route::has('password.request'))
                                        <div class="form-group mb-25 text-end"><a class="font-xs color-grey-500" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a></div>
                                        @endif
                                    </div>
                                    <div class="col-lg-12 mb-25">
                                        <button class="btn btn-brand-lg btn-full font-md-bold" type="submit">{{ __('Sign in') }}</button>
                                    </div>
                                    @if (Route::has('register'))
                                    <div class="col-lg-12"><span class="color-grey-500 d-inline-block align-middle font-sm">
                                            {{ __('Don’t have an account?') }}.
                                        </span><a class="d-inline-block align-middle color-success ml-3" href="{{route('register')}}">{{__('Sign up now')}}</a></div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row m-0">
            <div class="col-xxl-5 col-xl-7 col-lg-6"></div>
            <div class="col-xxl-7 col-xl-5 col-lg-6 pr-0">
                <div class="d-none d-xxl-block pl-70">
                    <div class="img-reveal"><img class="w-100 d-block" src="assets/imgs/page/login/banner.png" alt="iori"></div>
                </div>
            </div>
        </div>
    </section>
    <section class="section mt-50">

    </section>
</main>
@endsection