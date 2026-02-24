@extends('layouts.app-client')

@section('title')
    {{ __('Plans') }}
    <x-button-links />
@endsection

@section('content')
    <!--begin::Main-->
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <!--begin::Content wrapper-->
        <div class="d-flex flex-column flex-column-fluid">
            <!--begin::Toolbar-->
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            {{ __('Subscription Plans') }}
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Plans</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--end::Toolbar-->

            <!--begin::Content-->
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <!--begin::Alerts-->
                    @if (session('status'))
                        <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                            <i class="ki-duotone ki-shield-tick fs-2hx text-success me-4"><span class="path1"></span><span class="path2"></span></i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-success">{{ __('Success') }}</h4>
                                <span>{{ session('status') }}</span>
                            </div>
                            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                <i class="ki-duotone ki-cross fs-1 text-success"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
                            <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4"><span class="path1"></span><span class="path2"></span></i>
                            <div class="d-flex flex-column">
                                <h4 class="mb-1 text-danger">{{ __('Error') }}</h4>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                                <i class="ki-duotone ki-cross fs-1 text-danger"><span class="path1"></span><span class="path2"></span></i>
                            </button>
                        </div>
                    @endif
                    <!--end::Alerts-->

                    @if ($currentPlan)
                        <!--begin::Current Plan-->
                        <div class="card mb-10">
                            <div class="card-body">
                                @include('plans.info', ['planAttribute' => $planAttribute, 'showLinkToPlans' => false])
                            </div>
                        </div>
                        <!--end::Current Plan-->
                    @endif

                    <!--begin::Pricing Card-->
                    <div class="card">
                        <!--begin::Card body-->
                        <div class="card-body p-lg-20">
                            <!--begin::Heading-->
                            <div class="text-center mb-15">
                                <h1 class="fs-2hx fw-bold mb-5">{{ __('Choose Your Perfect Plan') }}</h1>
                                <div class="text-gray-500 fw-semibold fs-5">{{ __('Select the plan that works best for your business') }}</div>
                            </div>
                            <!--end::Heading-->

                            <!--begin::Pricing toggle-->
                            <div class="d-flex justify-content-center mb-15">
                                <div class="nav-group nav-group-outline mx-auto" data-kt-buttons="true" data-kt-buttons-target="[data-kt-plan]">
                                    <button class="btn btn-color-gray-500 btn-active btn-active-primary px-6 py-3 me-2 active" data-kt-plan="month">
                                        <span class="fs-1 fw-bold">{{ __('Monthly') }}</span>
                                    </button>
                                    <button class="btn btn-color-gray-500 btn-active btn-active-primary px-6 py-3" data-kt-plan="annual">
                                        <span class="fs-1 fw-bold">{{ __('Annual') }}</span>
                                    </button>
                                </div>
                            </div>
                            <!--end::Pricing toggle-->

                            <!--begin::Monthly plans-->
                            <div id="monthly-plans" class="row g-10">
                                @foreach ($plans as $index => $plan)
                                    @if ($plan['status'] == 1 && $plan['period'] == 1)
                                        @if (!(config('settings.forceUserToPay', false) && intval(config('settings.free_pricing_id')) . '' == $plan['id'] . ''))
                                            <!--begin::Col-->
                                            <div class="col-xl-4">
                                                <!--begin::Pricing card-->
                                                <div class="card h-100 @if($index % 2 == 0) border border-4 border-primary @endif">
                                                    <!--begin::Card header-->
                                                    <div class="card-header flex-column pt-9 pb-7 @if($index % 2 == 0) bg-primary @else bg-body @endif">
                                                        <!--begin::Title-->
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-bold fs-3 @if($index % 2 == 0) text-white @else text-gray-800 @endif">{{ $plan['name'] }}</span>
                                                            @if ($currentPlan && $plan['id'] . '' == $currentPlan->id . '')
                                                                <span class="badge badge-light-success fs-7 py-2 px-3">{{ __('Current Plan') }}</span>
                                                            @endif
                                                        </div>
                                                        <!--end::Title-->

                                                        <!--begin::Price-->
                                                        <div class="d-flex align-items-center">
                                                            <span class="fs-2hx fw-bold @if($index % 2 == 0) text-white @else text-gray-800 @endif me-2 lh-1 ls-n2">@money($plan['price'], config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</span>
                                                            <span class="fs-6 fw-semibold @if($index % 2 == 0) text-white @else text-gray-800 @endif opacity-75">/ {{ __('month') }}</span>
                                                        </div>
                                                        <!--end::Price-->

                                                        <!--begin::Description-->
                                                        <div class="fw-semibold fs-6 @if($index % 2 == 0) text-white @else text-gray-600 @endif opacity-75 mt-5">
                                                            {{ $plan['description'] }}
                                                        </div>
                                                        <!--end::Description-->
                                                    </div>
                                                    <!--end::Card header-->

                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-0">
                                                        <!--begin::Features-->
                                                        <div class="mb-10 mt-4">
                                                            @foreach (explode(',', $plan['features']) as $feature)
                                                                <div class="d-flex align-items-center mb-5">
                                                                    <span class="fw-semibold fs-6 @if($index % 2 == 0) text-gray-800 @else text-gray-600 @endif flex-grow-1 pe-3">
                                                                        <i class="ki-duotone ki-check-circle fs-2 @if($index % 2 == 0) text-primary @else text-gray-400 @endif me-3"><span class="path1"></span><span class="path2"></span></i>
                                                                        {{ __(trim($feature)) }}
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <!--end::Features-->

                                                        <!--begin::Actions-->
                                                        <div class="d-flex flex-column">
                                                            @if (!($currentPlan && $plan['id'] . '' == $currentPlan->id . ''))
                                                                @if (strlen($plan['stripe_id']) > 2 && config('settings.subscription_processor') == 'Stripe')
                                                                    @if ($plan['trial_days'] > 2 && $plan['trial_status'] == 1 && $trial_used != 1)
                                                                        <a href="javascript:void(0);" data-plan-id="{{ $plan['id'] }}" class="btn btn-outline btn-outline-dashed btn-outline-default btn-active-light-primary mb-5 freeTrialButton">
                                                                            {{ __('Start Free Trial') }}
                                                                        </a>
                                                                    @endif
                                                                    <a href="{{ route('upgrade.plan', $plan['id']) }}" class="btn @if($index % 2 == 0) btn-primary @else btn-light-primary @endif">
                                                                        {{ __('Get Started') }}
                                                                        <i class="ki-duotone ki-arrow-right fs-2 ms-1 me-0"><span class="path1"></span><span class="path2"></span></i>
                                                                    </a>
                                                                @endif
                                                                @if ($plan['price'] > 0 && (config('settings.subscription_processor') == 'Local' || config('settings.subscription_processor') == 'local'))
                                                                    <button data-bs-toggle="modal" data-bs-target="#paymentModal{{ $plan['id'] }}" class="btn @if($index % 2 == 0) btn-primary @else btn-light-primary @endif">
                                                                        {{ __('Get Started') }}
                                                                        <i class="ki-duotone ki-arrow-right fs-2 ms-1 me-0"><span class="path1"></span><span class="path2"></span></i>
                                                                    </button>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <!--end::Actions-->
                                                    </div>
                                                    <!--end::Card body-->
                                                </div>
                                                <!--end::Pricing card-->
                                            </div>
                                            <!--end::Col-->
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            <!--end::Monthly plans-->

                            <!--begin::Annual plans-->
                            <div id="annual-plans" class="row g-10" style="display: none;">
                                @foreach ($plans as $index => $plan)
                                    @if ($plan['status'] == 1 && $plan['period'] == 2) <!-- Changed from != 1 to == 2 for clarity -->
                                        @if (!(config('settings.forceUserToPay', false) && intval(config('settings.free_pricing_id')) . '' == $plan['id'] . ''))
                                            <!--begin::Col-->
                                            <div class="col-xl-4">
                                                <!--begin::Pricing card-->
                                                <div class="card h-100 @if($index % 2 != 0) border border-4 border-primary @endif">
                                                    <!--begin::Card header-->
                                                    <div class="card-header flex-column pt-9 pb-7 @if($index % 2 != 0) bg-primary @else bg-body @endif">
                                                        <!--begin::Title-->
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="fw-bold fs-3 @if($index % 2 != 0) text-white @else text-gray-800 @endif">{{ $plan['name'] }}</span>
                                                            @if ($currentPlan && $plan['id'] . '' == $currentPlan->id . '')
                                                                <span class="badge badge-light-success fs-7 py-2 px-3">{{ __('Current Plan') }}</span>
                                                            @endif
                                                        </div>
                                                        <!--end::Title-->

                                                        <!--begin::Price-->
                                                        <div class="d-flex align-items-center">
                                                            <span class="fs-2hx fw-bold @if($index % 2 != 0) text-white @else text-gray-800 @endif me-2 lh-1 ls-n2">@money($plan['price'], config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</span>
                                                            <span class="fs-6 fw-semibold @if($index % 2 != 0) text-white @else text-gray-800 @endif opacity-75">/ {{ __('year') }}</span>
                                                        </div>
                                                        <!--end::Price-->

                                                        <!--begin::Description-->
                                                        <div class="fw-semibold fs-6 @if($index % 2 != 0) text-white @else text-gray-600 @endif opacity-75 mt-5">
                                                            {{ $plan['description'] }}
                                                        </div>
                                                        <!--end::Description-->
                                                    </div>
                                                    <!--end::Card header-->

                                                    <!--begin::Card body-->
                                                    <div class="card-body pt-0">
                                                        <!--begin::Features-->
                                                        <div class="mb-10 mt-4">
                                                            @foreach (explode(',', $plan['features']) as $feature)
                                                                <div class="d-flex align-items-center mb-5">
                                                                    <span class="fw-semibold fs-6 @if($index % 2 != 0) text-gray-800 @else text-gray-600 @endif flex-grow-1 pe-3">
                                                                        <i class="ki-duotone ki-check-circle fs-2 @if($index % 2 != 0) text-primary @else text-gray-400 @endif me-3"><span class="path1"></span><span class="path2"></span></i>
                                                                        {{ __(trim($feature)) }}
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <!--end::Features-->

                                                        <!--begin::Actions-->
                                                        <div class="d-flex flex-column">
                                                            @if (!($currentPlan && $plan['id'] . '' == $currentPlan->id . ''))
                                                                @if (strlen($plan['stripe_id']) > 2 && config('settings.subscription_processor') == 'Stripe')
                                                                    <a href="{{ route('upgrade.plan', $plan['id']) }}" class="btn @if($index % 2 != 0) btn-primary @else btn-light-primary @endif">
                                                                        {{ __('Get Started') }}
                                                                        <i class="ki-duotone ki-arrow-right fs-2 ms-1 me-0"><span class="path1"></span><span class="path2"></span></i>
                                                                    </a>
                                                                @endif
                                                                @if ($plan['price'] > 0 && (config('settings.subscription_processor') == 'Local' || config('settings.subscription_processor') == 'local'))
                                                                    <button data-bs-toggle="modal" data-bs-target="#paymentModal{{ $plan['id'] }}" class="btn @if($index % 2 != 0) btn-primary @else btn-light-primary @endif">
                                                                        {{ __('Get Started') }}
                                                                        <i class="ki-duotone ki-arrow-right fs-2 ms-1 me-0"><span class="path1"></span><span class="path2"></span></i>
                                                                    </button>
                                                                @endif
                                                                @if (strlen($plan['razorpay_id']) > 2 && config('settings.subscription_processor') == 'Razorpay')
                                                                    <a href="{{ route('upgrade.plan', $plan['id']) }}" class="btn @if($index % 2 != 0) btn-primary @else btn-light-primary @endif">
                                                                        {{ __('Get Started') }}
                                                                        <i class="ki-duotone ki-arrow-right fs-2 ms-1 me-0"><span class="path1"></span><span class="path2"></span></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                        <!--end::Actions-->
                                                    </div>
                                                    <!--end::Card body-->
                                                </div>
                                                <!--end::Pricing card-->
                                            </div>
                                            <!--end::Col-->
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                            <!--end::Annual plans-->
                        </div>
                        <!--end::Card body-->
                    </div>
                    <!--end::Pricing Card-->
                </div>
            </div>
            <!--end::Content-->
        </div>
        <!--end::Content wrapper-->
    </div>
    <!--end::Main-->

    <!-- Payment Modals -->
    @foreach ($plans as $plan)
        @if ($plan['price'] > 0 && (config('settings.subscription_processor') == 'Local' || config('settings.subscription_processor') == 'local'))
            <!--begin::Modal - Payment-->
            <div class="modal fade" id="paymentModal{{ $plan['id'] }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered mw-650px">
                    <div class="modal-content">
                        <!--begin::Modal header-->
                        <div class="modal-header">
                            <h2 class="fw-bold">{{ $plan['name'] }} {{ __('Plan') }}</h2>
                            <div class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal">
                                <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                            </div>
                        </div>
                        <!--end::Modal header-->

                        <!--begin::Modal body-->
                        <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                            <!--begin::Details-->
                            <div class="mb-9">
                                <!--begin::Plan-->
                                <div class="d-flex align-items-center border-bottom border-gray-300 border-bottom-dashed py-5">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        <div class="symbol symbol-50px me-5">
                                            <span class="symbol-label bg-light-primary">
                                                <i class="ki-duotone ki-abstract-28 fs-2x text-primary"><span class="path1"></span><span class="path2"></span></i>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column">
                                            <span class="fs-5 fw-bold text-gray-800">{{ $plan['name'] }}</span>
                                            <span class="fs-6 text-gray-500">{{ $plan['description'] }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column w-100px text-end">
                                        <span class="fw-bold fs-3 text-gray-800">@money($plan['price'], config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</span>
                                        <span class="text-gray-500">{{ $plan['period'] == 1 ? __('per month') : __('per year') }}</span>
                                    </div>
                                </div>
                                <!--end::Plan-->

                                <!--begin::Payment info-->
                                <div class="pt-7">
                                    <h4 class="fw-bold text-gray-800 mb-4">{{ __('Payment Information') }}</h4>
                                    <div class="alert alert-primary d-flex align-items-center p-5 mb-10">
                                        <i class="ki-duotone ki-information-5 fs-2hx text-primary me-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                                        <div class="d-flex flex-column">
                                            <h4 class="mb-1 text-primary">{{ __('Important') }}</h4>
                                            <span>{{ config('settings.local_transfer_info') }}</span>
                                            <span class="mt-3">{{ config('settings.local_transfer_account') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Payment info-->
                            </div>
                            <!--end::Details-->

                            <!--begin::Actions-->
                            <div class="text-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                <a href="{{ route('upgrade.plan', $plan['id']) }}" class="btn btn-primary">
                                    <span class="indicator-label">{{ __('I Have Made Payment') }}</span>
                                </a>
                            </div>
                            <!--end::Actions-->
                        </div>
                        <!--end::Modal body-->
                    </div>
                </div>
            </div>
            <!--end::Modal - Payment-->
        @endif
    @endforeach
@endsection

@section('js')

    <script>
        document.querySelectorAll('.freeTrialButton').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();

                const planId = this.getAttribute('data-plan-id');
                console.log(planId);
                fetch("{{ route('subscription.validate.trial') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            plan_id: planId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href =
                                `{{ route('subscription.trial.forward', '') }}/${planId}`;
                        } else {
                            alert(data.message || 'Something went wrong. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while processing your request.');
                    });
            });
        });
    </script>
    <script type="text/javascript">
        $(".btn-sub-actions").on('click', function() {
            var action = $(this).attr('data-action');

            $('#action').val(action);
            document.getElementById('form-subscription-actions').submit();
        });

        function showLocalPayment(plan_name, plan_id) {
            alert(plan_name);
        }

        var plans = <?php echo json_encode($plans); ?>;
        var user = <?php echo json_encode(auth()->user()); ?>;
        var payment_processor = <?php echo json_encode(config('settings.subscription_processor')); ?>;
    </script>

    @if (config('settings.subscription_processor') == 'Stripe')
        <!-- Stripe -->
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            "use strict";
            var STRIPE_KEY = "{{ config('settings.stripe_key') }}";
            var ENABLE_STRIPE = "{{ config('settings.subscription_processor') == 'Stripe' }}";
            if (ENABLE_STRIPE) {
                js.initStripe(STRIPE_KEY, "stripe-payment-form");
            }

            function validateOrderFormSubmit() {
                return true;
            }

            function showStripeCheckout(plan_id, plan_name) {
                $('#plan_id').val(plan_id);
                $('#plan_name').html(plan_name);
                $('#stripe-payment-form-holder').show();
            }
        </script>
    @else
        @if (!(config('settings.subscription_processor') == 'Local'))
            <!-- Payment Processors JS Modules -->
            @include($subscription_processor . '-subscribe::subscribe')
        @endif
    @endif
    <script>
        $(document).ready(function() {
            const $monthlyDiv = $('#monthly-plans');
            const $annualDiv = $('#annual-plans');

            $('.nav-group button').on('click', function() {
                const plan = $(this).data('kt-plan');

                if (plan === 'month') {
                    $monthlyDiv.show();
                    $annualDiv.hide();
                } else if (plan === 'annual') {
                    $monthlyDiv.hide();
                    $annualDiv.show();
                }

                $('.nav-group button').removeClass('active');
                $(this).addClass('active');
            });
        });
    </script>






@endsection
