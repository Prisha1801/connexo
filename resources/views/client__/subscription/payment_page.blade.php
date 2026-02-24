@extends('layouts.app-client')
@section('title', __('payment_methods'))
@push('topcss')
    <link href="{{ asset('css/countrySelect.min.css') }}" rel="stylesheet" />
    <link type="text/css" href="{{ asset('vendor') }}/flag-icons/css/flag-icons.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('custom/css/telinput.css') }}">
    <style>
        input.country_selector,
        .country_selector button {
            height: 35px;
            margin: 0;
            padding: 6px 12px;
            border-radius: 2px;
            font-family: inherit;
            font-size: 100%;
            color: inherit;
        }

        input #country_selector,
        input #billing_phone {
            padding-left: 47px !important;
        }

        .iti.iti--allow-dropdown {
            width: 100%;
        }

        /* Loading effect for the submit button */
        .loading_button {
            position: relative;
            /* For positioning spinner */
            cursor: not-allowed !important;
            /* Prevent user interaction */
        }

        .loading_button::after {
            content: "";
            /* Placeholder for spinner */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border: 2px solid #f3f3f3;
            /* Light gray border */
            border-top: 2px solid #354ABF;
            /* Spinner color */
            border-radius: 50%;
            width: 15px;
            /* Size of spinner */
            height: 15px;
            animation: spin 1s linear infinite;
            /* Animation for spinning */
        }

        @keyframes spin {
            from {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            to {
                transform: translate(-50%, -50%) rotate(360deg);
                /* Full spin */
            }
        }
    </style>
@endpush

@section('content')
    @php
        $action =
            $package->is_free == 1 && $package->price == 0
                ? route('upgrade-plan.free', [
                    'trx_id' => $trx_id,
                    'payment_type' => 'free',
                    'package_id' => $package->id,
                ])
                : '';
    @endphp

    <form action="{{ $action }}" class="payment_form" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="d-flex flex-column flex-lg-row">
            @if (session('error'))
                <div role="alert" class="alert alert-danger m-8 mb-0">
                    {{ session('error') }}
                </div>
            @endif
            <div class="flex-lg-row-fluid order-2 order-lg-1 mb-10 mb-lg-0 d-lg-block d-none" id="card-1">
                <div class="card card-flush me-0 me-lg-4">
                    <input type="hidden" name="package_id" value="{{ $package->id }}">
                    <input type="hidden" name="plan_id" value="{{ $package->id }}">
                    <div class="card-header">
                        <div class="card-title">
                            <h3>{{ __('billing_details') }}</h3>
                        </div>
                    </div>
                    <div class="card-body pt-2 pt-lg-4">
                        <div class="d-flex flex-row d-lg-none bg-dark mb-4 rounded p-4">
                            <div class="flex-1 ">
                                <h2 class="text-white fw-light">Resumen de la orden</h2>
                                <span class="fs-3x fw-bold text-white" data-kt-countup-prefix="$"
                                    id="coupon-result-final-pay-movile">
                                    @money($package->price, config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))
                                    <span class="fs-5 text-white"> (1 Artículo)</span>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="mb-0 d-flex justify-content-end align-items-center me-4 d-lg-none h-100">
                                    <button id="returnButton" class="btn btn-secondary my-auto">regresar</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-xl-6">
                                <div class="mb-3">
                                    <label class="form-label text-gray-600 d-none d-lg-block">{{ __('billing_name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control form-control-lg form-control-solid @error('billing_name') is-invalid @enderror"
                                        name="billing_name" placeholder="{{ __('billing_name') }}" required
                                        value="{{ auth()->user()->company->billing_name ? auth()->user()->company->billing_name : auth()->user()->name }}">
                                    @if ($errors->has('billing_name'))
                                        <span class="help-block text-danger">{{ $errors->first('billing_name') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-6">
                                <div class="mb-3">
                                    <label class="form-label text-gray-600 d-none d-lg-block">{{ __('billing_email') }}
                                        <span class="text-danger">*</span></label>
                                    <input type="email"
                                        class="form-control form-control-lg form-control-solid @error('billing_email') is-invalid @enderror"
                                        name="billing_email" placeholder="{{ __('email') }}" required disabled
                                        value="{{ auth()->user()->company->billing_email ? auth()->user()->company->billing_email : auth()->user()->email }}">
                                    @if ($errors->has('billing_email'))
                                        <span class="help-block text-danger">{{ $errors->first('billing_email') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-6">
                                <div class="mb-3">
                                    <label for="billing_phone"
                                        class="form-label d-block  text-gray-600 d-none d-lg-block">{{ __('billing_phone') }}
                                        <span class="text-danger">*</span></label>
                                    <div class="fv-row mb-4 d-flex align-items-center">
                                        <!--begin::Email-->
                                        <span><i class="ki-duotone ki-whatsapp fs-2x mx-4 ki-graph-up text-primary">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i></span>
                                        <input id="billing_phone" name="billing_phone"
                                            class="form-control form-control-lg form-control-solid" type="tel"
                                            value="{{ auth()->user()->company->billing_phone ? auth()->user()->company->billing_phone : auth()->user()->company->phone }}"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="country_code" name="country_code" value="" />
                            <div class="col-md-12 col-xl-6">
                                <div class="mb-3">
                                    <label class="form-label text-gray-600 d-none d-lg-block">{{ __('billing_country') }}
                                        <span class="text-danger">*</span></label>
                                    <div class="form-item">
                                        <input id="country_selector" name="billing_country"
                                            class="form-control form-control-lg form-control-solid" type="text"
                                            value="">
                                        <label for="country_selector" style="display:none;">
                                            {{ __('Select a country here') }}
                                        </label>
                                    </div>
                                    <div class="form-item" style="display:none;">
                                        <input type="text" id="country_selector_code" name="country_selector_code"
                                            data-countrycodeinput="1" readonly="readonly"
                                            placeholder="Selected country code will appear here" />
                                        <label for="country_selector_code">
                                            {{ __('and the selected country code will be updated here') }}
                                        </label>
                                    </div>

                                    @if ($errors->has('billing_country'))
                                        <span class="help-block text-danger">{{ $errors->first('billing_country') }}</span>
                                    @endif
                                </div>
                            </div>


                            <div class="col-md-12 col-xl-12">
                                <div class="mb-3">
                                    <label class="form-label text-gray-600 d-none d-lg-block">{{ __('billing_address') }}
                                        <span class="text-danger"></span></label>
                                    <input type="text"
                                        class="form-control form-control-lg form-control-solid @error('billing_address') is-invalid @enderror"
                                        name="billing_address" value="{{ auth()->user()->company->billing_address }}"
                                        placeholder="{{ __('billing_address') }}">
                                    @if ($errors->has('billing_address'))
                                        <span
                                            class="help-block text-danger">{{ $errors->first('billing_address') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 col-xl-4">
                                <div class="mb-3">
                                    <label class="form-label text-gray-600 d-none d-lg-block">{{ __('billing_city') }}
                                        <span class="text-danger"></span></label>
                                    <input type="text"
                                        class="form-control form-control-lg form-control-solid @error('billing_city') is-invalid @enderror"
                                        name="billing_city" placeholder="{{ __('billing_city') }}"
                                        value="{{ auth()->user()->company->billing_city }}">
                                    @if ($errors->has('billing_city'))
                                        <span class="help-block text-danger">{{ $errors->first('billing_city') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-4">
                                <div class="mb-3">
                                    <label class="form-label text-gray-600 d-none d-lg-block">{{ __('billing_state') }}
                                        <span class="text-danger"></span></label>
                                    <input type="text"
                                        class="form-control form-control-lg form-control-solid @error('billing_state') is-invalid @enderror"
                                        name="billing_state" placeholder="{{ __('billing_state') }}"
                                        value="{{ auth()->user()->company->billing_state }}">
                                    @if ($errors->has('billing_state'))
                                        <span class="help-block text-danger">{{ $errors->first('billing_state') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-6 col-md-12 col-xl-4">
                                <div class="mb-3">
                                    <label class="form-label text-gray-600 d-none d-lg-block">{{ __('billing_zip_code') }}
                                        <span class="text-danger"></span></label>
                                    <input type="text"
                                        class="form-control form-control-lg form-control-solid @error('billing_zipcode') is-invalid @enderror"
                                        name="billing_zipcode" placeholder="{{ __('billing_zip_code') }}"
                                        value="{{ auth()->user()->company->billing_zip_code }}">
                                    @if ($errors->has('billing_zipcode'))
                                        <span
                                            class="help-block text-danger">{{ $errors->first('billing_zipcode') }}</span>
                                    @endif
                                </div>
                            </div>


                        </div>
                        <div class="mb-0 d-lg-none">
                            @if ($package->is_free == 1)
                                <button type="submit"
                                    class="w-100 btn btn-info d-block payment_btns free_btn">{{ __('select_payment_method') }}</button>
                            @else
                                <a href="#"
                                    class="btn btn-info d-block payment_btns disabled_a">{{ __('validate_coupon') }}</a>
                            @endif
                            <div class="div_btns d-none">
                                @if (config('settings.subscription_processor') == 'Stripe')
                                    <button type="submit" onclick="savebilling()"
                                        class="w-100 btn btn-lg btn-lg-primary btn-primary w-10 stripe_btn">{{ __('Process Payment') }}</button>
                                @elseif (config('settings.subscription_processor') == 'Razorpay')
                                    <button type="submit" onclick="savebilling()"
                                        class="w-100 btn btn-lg btn-lg-primary btn-primary w-10 stripe_btn">{{ __('Process Payment') }}</button>
                                @endif
                            </div>
                        </div>
                        <div class="mt-2 text-center mt-4">
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-column flex-lg-row-auto w-100 w-lg-350px w-xl-400px mb-10 order-1 order-lg-2 me-lg-8"
                id="card-2">
                <div class="card card-flush pt-3 mb-0" data-kt-sticky="true" data-kt-sticky-name="subscription-summary"
                    data-kt-sticky-offset="{default: false, lg: '200px'}"
                    data-kt-sticky-width="{lg: '250px', xl: '300px'}" data-kt-sticky-left="auto"
                    data-kt-sticky-top="150px" data-kt-sticky-animation="false" data-kt-sticky-zindex="95"
                    style="">
                    <div class="card-header">
                        <div class="card-title">
                            <h2>{{ __('payment_information') }}</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0 fs-6">
                        <div class="mb-7">
                            <h5 class="mb-3">Customer details</h5>
                            <div class="d-flex align-items-center mb-1">
                                <a href="#" class="fw-bold text-gray-800 text-hover-primary me-2">
                                    {{ auth()->user()->name }}</a>
                                {{-- <span class="badge badge-light-success">Active</span> --}}
                            </div>
                            <a href="#"
                                class="fw-semibold text-gray-600 text-hover-primary">{{ auth()->user()->email }}</a>
                        </div>
                        <div class="separator separator-dashed mb-7"></div>
                        <div class="mb-4 ">
                            <h5 class="mb-3">Plan <span
                                    class="badge badge-light-info fs-5 ms-2">{{ $package->name }}</span></h5>
                            {{-- <span class="badge badge-light-success fs-9 p-2 ">Facturado cada 1 mes(es)</span> --}}
                        </div>

                        <div class="fw-semibold text-gray-600 text-hover-primary">
                            {{ $package->description }}
                        </div>
                        @php
                            $features = explode(',', $package->features);
                        @endphp
                        <div class="fw-semibold text-gray-600 my-4">
                            <ul class="list-unstyled">
                                @foreach ($features as $feature)
                                    <li><i class="ki-solid ki-verify text-dark fs-4 me-4"></i> {{ trim($feature) }}.</li>
                                    <!-- trim para eliminar espacios en blanco -->
                                @endforeach
                            </ul>
                        </div>
                        <div>
                            <table class="table table-borderless">
                                <tr>
                                    <td>{{ __('price') }}</td>
                                    <td class="text-end">
                                        <div class="fs-5 fw-bold" data-kt-countup-prefix="$">
                                            @money($package->price, config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</div>
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>{{ __('total_amount') }}</td>
                                    <td class="text-end">
                                        <div class="fs-5 fw-bold" data-kt-countup-prefix="$"
                                            id="coupon-result-final-pay">
                                            @money($package->price, config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</div>
                                    </td>
                                </tr> --}}
                                @if ($coupon_used <= 6)
                                    <tr class="my-auto">
                                        <td colspan="2">
                                            <div class="input-group">
                                                <input id='coupon_code' type="text"
                                                    class="form-control form-control-solid "
                                                    placeholder="{{ __('coupon_code') }}"
                                                    aria-label="Recipient's username" aria-describedby="basic-addon2" />
                                                <span class="input-group-text border-none" id="basic-addon2"
                                                    style="border: none !important;">
                                                    @if ($coupon_used <= 6)
                                                        <div class="mt-1 text-center border-none">
                                                            <a href="javascript:void(0)"
                                                                class="btn-link btn-sm text-success"
                                                                onclick="applyCoupon()">Apply Coupon</a>
                                                        </div>
                                                    @endif
                                                </span>
                                            </div>
                                            <small id="coupon-result-error" style="color:red"></small>
                                            <small id="coupon-result-success" style="color:green">&nbsp;</small>
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                        <div class="mb-4 d-flex justify-content-between ">
                            <h5 class="mb-3">Total Amount to Pay</h5>
                            <span class="fs-5 fw-bold" data-kt-countup-prefix="$" id="coupon-result-final-pay">
                                @money($package->price, config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))
                            </span>
                        </div>
                        <div class="separator separator-dashed mb-7 d-none d-lg-block"></div>
                        <div class="mb-0 d-none d-lg-block">
                            @if ($package->is_free == 1)
                                <button type="submit"
                                    class="w-100 btn btn-info d-block payment_btns free_btn">{{ __('select_payment_method') }}</button>
                            @endif
                            {{-- @else
                                <a href="#"
                                    class="btn btn-info d-block payment_btns disabled_a">{{ __('validate_coupon') }}</a>
                            @endif --}}
                            
                            <div class="div_btns d-none">
                                @if (config('settings.subscription_processor') == 'Stripe')
                                    <button type="submit" onclick="savebilling()"
                                        class="w-100 btn btn-info w-10 stripe_btn">{{ __('Process Payment') }}</button>
                                @elseif (config('settings.subscription_processor') == 'Razorpay')
                                                                  
                                    <button type="submit" onclick="savebilling()"
                                        class="w-100 btn btn-info d-block payment_btns razorpay_btn">{{ __('Process Payment') }}</button>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="mb-0 d-block d-lg-none">
        <button id="continueButton" class="w-100 btn  btn-lg btn-primary">Continuar</button>
    </div>
    {{-- <section class="options">
        <div class="container-fluid">
            <form
                @if ($package->is_free == 1 && $package->price == 0) action="{{ route('upgrade-plan.free', ['trx_id' => $trx_id, 'payment_type' => 'free', 'package_id' => $package->id]) }}"
            @else
            action="" @endif
                class="payment_form" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <input type="hidden" name="plan_id" value="{{ $package->id }}">
                <div class="row g-xxl-9">
                    <div class="col-xxl-8">
                        <div class="card  card-xxl-stretch mb-5 mb-xxl-10">
                            @if (session('error'))
                                <div role="alert" class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>{{ __('billing_details') }}</h3>
                                </div>
                            </div>
                            <div class="card-body pb-0">
                                <div class="row">
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control form-control-lg form-control-solid @error('billing_name') is-invalid @enderror"
                                                name="billing_name" placeholder="{{ __('billing_name') }}" required
                                                value="{{ auth()->user()->company->billing_name ? auth()->user()->company->billing_name : auth()->user()->name }}">
                                            @if ($errors->has('billing_name'))
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('billing_name') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_email') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="email"
                                                class="form-control form-control-lg form-control-solid @error('billing_email') is-invalid @enderror"
                                                name="billing_email" placeholder="{{ __('email') }}" required disabled
                                                value="{{ auth()->user()->email }}">
                                            @if ($errors->has('billing_email'))
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('billing_email') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_address') }} <span
                                                    class="text-danger"></span></label>
                                            <input type="text"
                                                class="form-control form-control-lg form-control-solid @error('billing_address') is-invalid @enderror"
                                                name="billing_address"
                                                value="{{ auth()->user()->company->billing_address }}"
                                                placeholder="{{ __('billing_address') }}">
                                            @if ($errors->has('billing_address'))
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('billing_address') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_city') }} <span
                                                    class="text-danger"></span></label>
                                            <input type="text"
                                                class="form-control form-control-lg form-control-solid @error('billing_city') is-invalid @enderror"
                                                name="billing_city" placeholder="{{ __('billing_city') }}"
                                                value="{{ auth()->user()->company->billing_city }}">
                                            @if ($errors->has('billing_city'))
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('billing_city') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_state') }} <span
                                                    class="text-danger"></span></label>
                                            <input type="text"
                                                class="form-control form-control-lg form-control-solid @error('billing_state') is-invalid @enderror"
                                                name="billing_state" placeholder="{{ __('billing_state') }}"
                                                value="{{ auth()->user()->company->billing_state }}">
                                            @if ($errors->has('billing_state'))
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('billing_state') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_zip_code') }} <span
                                                    class="text-danger"></span></label>
                                            <input type="text"
                                                class="form-control form-control-lg form-control-solid @error('billing_zipcode') is-invalid @enderror"
                                                name="billing_zipcode" placeholder="{{ __('billing_zip_code') }}"
                                                value="{{ auth()->user()->company->billing_zip_code }}">
                                            @if ($errors->has('billing_zipcode'))
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('billing_zipcode') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_country') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="form-item">
                                                <input id="country_selector" name="billing_country" class="form-control form-control-lg form-control-solid"
                                                    type="text" value="">
                                                <label for="country_selector"
                                                    style="display:none;">{{ __('Select
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            a
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            country here') }}</label>
                                            </div>
                                            <div class="form-item" style="display:none;">
                                                <input type="text" id="country_selector_code"
                                                    name="country_selector_code" data-countrycodeinput="1"
                                                    readonly="readonly"
                                                    placeholder="Selected country code will     appear here" />
                                                <label
                                                    for="country_selector_code">{{ __('and the selected
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            country
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            code
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            will be updated here') }}</label>
                                            </div>

                                            @if ($errors->has('billing_country'))
                                                <span
                                                    class="help-block text-danger">{{ $errors->first('billing_country') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label for="billing_phone"
                                                class="form-label d-block  text-gray-600">{{ __('billing_phone') }} <span
                                                    class="text-danger">*</span></label>
                                            <input id="billing_phone" name="billing_phone" class="form-control form-control-lg form-control-solid"
                                                type="tel"
                                                value="{{ auth()->user()->company->billing_phone ? auth()->user()->company->billing_phone : auth()->user()->company->phone }}"
                                                required>
                                            <small>{{ __('WhatsApp number') }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 text-center mt-4">
                                    @if ($package->is_free == 1)
                                        <button type="submit"
                                            class="w-100 btn btn-info d-block payment_btns free_btn">{{ __('select_payment_method') }}</button>
                                    @else
                                        <a href="#"
                                            class="btn btn-info d-block payment_btns disabled_a">{{ __('validate_coupon') }}</a>
                                    @endif
                                    <div class="div_btns d-none">
                                        @if (config('settings.subscription_processor') == 'Stripe')
                                            <button type="submit"
                                                class="w-100 btn btn-info w-10 stripe_btn">{{ __('Process Payment') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-4">
                        <div class="card  card-xxl-stretch mb-5 mb-xxl-10">
                            <div class="card-header">
                                <div class="card-title">
                                    <h3 class="text-gray-800">{{ __('payment_information') }}</h3>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-self-center">
                                    <div class="flex-grow-1 me-3">
                                        <div class="redious-border p-5 p-sm-30 pt-sm-30">
                                            <table class="table table-borderless">
                                                <tr>
                                                    <td>{{ __('plan_name') }}</td>
                                                    <td class="text-end">
                                                        <h6>{{ $package->name }}</h6>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('price') }}</td>
                                                    <td class="text-end">
                                                        <div class="fs-5 fw-bold" data-kt-countup-prefix="$">
                                                            @money($package->price, config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('total_amount') }}</td>
                                                    <td class="text-end">
                                                        <div class="fs-5 fw-bold" data-kt-countup-prefix="$"
                                                            id="coupon-result-final-pay">
                                                            @money($package->price, config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</div>
                                                    </td>
                                                </tr>
                                                @if ($coupon_used <= 6)
                                                    <tr>
                                                        <td>{{ __('coupon_code') }}</td>
                                                        <td class="text-end">
                                                            <input type="input"
                                                                class="form-control form-control-lg form-control-solid @error('coupon_code') is-invalid @enderror text-end"
                                                                name="coupon_code" id="coupon_code"
                                                                placeholder="{{ __('enter_coupon_code') }}">
                                                            <small id="coupon-result-error" style="color:red"></small>
                                                            <small id="coupon-result-success"
                                                                style="color:green">&nbsp;</small>
                                                        </td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                        @if ($coupon_used <= 6)
                                            <div class="mt-1 text-center mt-1">
                                                <a href="javascript:void(0)" class="btn btn-success d-block"
                                                    onclick="applyCoupon()">Apply
                                                    Coupon</a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    </section> --}}
@endsection
@push('js')
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        const phoneInputField = document.querySelector("#billing_phone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            preferredCountries: ["do", "mx", "ar", "es"], // Lista de paÃ­ses preferidos
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
    </script> --}}
    {{-- <script src="{{ asset('js/intlTelInput.js') }}"></script> --}}
    <script src="{{ asset('js/countrySelect.min.js') }}"></script>

    <script src="{{ asset('custom/js/telinput.js') }}"></script>
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.querySelector("#billing_phone");
            const iti = window.intlTelInput(input, {
                initialCountry: "auto",
                separateDialCode: true,
                geoIpLookup: callback => {
                    fetch("https://ipapi.co/json")
                        .then(res => res.json())
                        .then(data => {
                            const countryCode = data.country_code || "us"; // Valor por defecto
                            callback(countryCode);
                        })
                        .catch(() => callback("us"));
                },
                utilsScript: "{{ asset('custom/js/telinput.js') }}",
            });
            if (input.value) {
                const countryData = iti.getSelectedCountryData();
            }
        });
    </script> --}}
    <script>
        function applyCoupon() {
            document.getElementById("coupon-result-error").style.display = "none";
            document.getElementById("coupon-result-success").style.display = "none";

            const plan_id = '{{ $package->stripe_id }}';
            const amount = '{{ $package->price }}';
            const couponCode = document.getElementById('coupon_code').value;

            fetch('/validate-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        amount,
                        plan_id: plan_id,
                        coupon_code: couponCode
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        // document.getElementById('coupon-result-discount').innerHTML = `$${data.discount_amount.toFixed(2)}`;
                        document.getElementById('coupon-result-final-pay').innerHTML =
                            `$${data.final_amount.toFixed(2)}`;
                        document.getElementById('coupon-result-final-pay-movile').innerHTML =
                            `$${data.final_amount.toFixed(2)}`;


                        document.getElementById("coupon-result-success").style.display = "block";
                        document.getElementById('coupon-result-success').innerHTML = `<p>${data.message}</p>`;
                    } else {
                        document.getElementById("coupon-result-error").style.display = "block";
                        document.getElementById('coupon-result-error').innerHTML = `<p>${data.message}</p>`;
                    }
                })
                .catch(error => {

                    $("#discountBlock").toggleClass("hidden");
                    console.error('Error:', error);
                    document.getElementById('coupon-result-error').innerHTML = `<p>Error applying coupon.</p>`;
                });
        }
    </script>

    <script>
        $('form.payment_form').submit(function(e) {
            console.log('ejecu')
            e.preventDefault();
            var button = $(this).find('button[type="submit"]');
            button.addClass('loading_button');
            var formData = $(this).serializeFormJSON();
            formData['billing_name'] = $('input[name="billing_name"]').val();
            formData['billing_email'] = $('input[name="billing_email"]').val();
            formData['billing_address'] = $('input[name="billing_address"]').val();
            formData['billing_city'] = $('input[name="billing_city"]').val();
            formData['billing_state'] = $('input[name="billing_state"]').val();
            formData['billing_zipcode'] = $('input[name="billing_zipcode"]').val();
            formData['billing_country'] = $('input[name="billing_country"]').val();
            formData['billing_phone'] = $('input[name="billing_phone"]').val();
            formData['country_code'] = $('input[name="country_code"]').val();
            $.ajax({
                type: 'POST',
                url: '/store/billing_data', // URL del controlador
                data: formData,
                dataType: 'json',
                success: function(response) {
                    console.log('almacenado de datos cliente');
                    $('form.payment_form')[0].submit();
                },
                error: function(xhr, status, error) {
                    var errors = xhr.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function(key, value) {
                            alert(value[0]);
                        });
                    }
                },
                complete: function() {
                    button.removeClass('loading_button'); // Remover la clase de carga
                }
            });
        })

        $(document).ready(function() {
            var countryCode = '';
            var input = document.querySelector("#billing_phone");
            const country_code = document.querySelector("#country_code")
            // window.intlTelInput(input, {
            //     autoHideDialCode: false,
            //     autoPlaceholder: "on",
            //     dropdownContainer: document.body,
            //     formatOnDisplay: true,
            //     geoIpLookup: function(callback) {
            //         $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
            //             var countryCode = (resp && resp.country) ? resp.country : "";
            //             callback(countryCode);
            //         });
            //     },
            //     hiddenInput: "full_number",
            //     initialCountry: "auto",
            //     nationalMode: false,
            //     placeholderNumberType: "MOBILE",
            //     preferredCountries: ['us', 'uk', 'ca'],
            //     separateDialCode: false,
            //     utilsScript: "{{ asset('js/utils.js') }}",
            // });
            const iti = window.intlTelInput(input, {
                initialCountry: "auto",
                separateDialCode: true,
                geoIpLookup: callback => {
                    fetch("https://ipapi.co/json")
                        .then(res => res.json())
                        .then(data => {
                            const countryCode = data.country_code ||
                                "us"; // Valor por defecto
                            callback(countryCode);
                        })
                        .catch(() => callback("us"));
                },
                utilsScript: "{{ asset('custom/js/telinput.js') }}",
            });
            if (input.value) {
                const countryData = iti.getSelectedCountryData();
                country_code.value = countryData.dialCode;
            }
            input.addEventListener("countrychange", function() {
                country_code.value = iti.getSelectedCountryData().dialCode;
            });

            var countrySelector = $("#country_selector");
            countrySelector.countrySelect({
                autoDropdown: true,
                initialCountry: "auto",
                formatOnDisplay: true,
                geoIpLookup: function(callback) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(
                        resp) {
                        var countryCode = (resp && resp.country) ? resp.country :
                            "";
                        callback(countryCode);
                    });
                },
            });

            $(document).ready(function() {
                
                let val = 'razorpay';
                let url = '';
                if (val == 'razorpay') {
                    //old
                    url = '{{ route('razor.pay.redirect') }}' + '?trx_id={{ $trx_id }}' +
                        '' +
                        '&payment_type=razorpay';
                }
                $('.payment_form').attr('action', url);
                $('.payment_btns').addClass('d-none');
                $('.div_btns').removeClass('d-none');
                let btn_selector = $('.' + val + '_btn');
                if (val) {
                    if (val == 'offline') {
                        $('#offline_payment').removeClass('d-none');
                    } else {
                        btn_selector.removeClass('d-none');
                        $('#offline_payment').addClass('d-none');
                    }
                }
                $('.client_payment_box').removeClass('active_pg');
                $(this).addClass('active_pg');
            });

        });


        $(document).ready(function() {
            $.fn.serializeFormJSON = function() {
                var o = {};
                var a = this.serializeArray();
                $.each(a, function() {
                    if (o[this.name] !== undefined) {
                        if (!o[this.name].push) {
                            o[this.name] = [o[this.name]];
                        }
                        o[this.name].push(this.value || '');
                    } else {
                        o[this.name] = this.value || '';
                    }
                });
                return o;
            };

            // Form submission handler for the offline form
            $('form.offline_form').submit(function(e) {
                e.preventDefault();
                var button = $(this).find('button[type="submit"]');
                button.addClass('loading_button');

                var formData = $(this).serializeFormJSON();

                formData['billing_name'] = $('input[name="billing_name"]').val();
                formData['billing_email'] = $('input[name="billing_email"]').val();
                formData['billing_address'] = $('input[name="billing_address"]').val();
                formData['billing_city'] = $('input[name="billing_city"]').val();
                formData['billing_state'] = $('input[name="billing_state"]').val();
                formData['billing_zipcode'] = $('input[name="billing_zipcode"]').val();
                formData['billing_country'] = $('input[name="billing_country"]').val();
                formData['billing_phone'] = $('input[name="billing_phone"]').val();

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: formData,
                    beforeSend: function() {
                        $('.loading-btn').addClass('loading');
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status === true) {

                            window.location.href = response.redirect_to;
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        button.removeClass('loading_button');
                        $('.loading-btn').removeClass('loading');
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#continueButton').on('click', function(event) {
                event.preventDefault();
                $('#card-2').addClass('d-none'); 
                $('#continueButton').addClass('d-none');
                $('#card-1').removeClass('d-none'); 
            });
        });

        $(document).ready(function() {
            $('#returnButton').on('click', function(event) {
                event.preventDefault();
                $('#card-2').removeClass('d-none');
                $('#continueButton').removeClass('d-none');
                $('#card-1').addClass('d-none'); 
            });
        });
    </script>
    
@endpush
