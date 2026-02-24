@extends('layouts.app-client')
@section('title', __('subscription_info'))
@section('content')
    <div class="app-main flex-column flex-row-fluid " id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar  pt-7 pt-lg-10 ">
                <div id="kt_app_toolbar_container" class="app-container  container-fluid d-flex align-items-stretch">
                    <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                        <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                            <h1
                                class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0">
                                {{ __('subscription_info') }}
                            </h1>
                            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                <li class="breadcrumb-item text-muted">
                                    <a href="/" class="text-muted text-hover-primary">
                                        {{ __('dashboard') }} </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                </li>
                                <li class="breadcrumb-item text-muted">
                                    {{ __('plan_details') }} </li>
                            </ul>
                        </div>
                        <div class="d-flex align-items-center gap-2 gap-lg-3">
                            {{-- <a href="javascript:void(0)"
                                onclick="delete_row('{{ route('enable.recurring', $active_subscription->id) }}')"
                                data-toggle="tooltip"
                                class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold">
                                {{ __('stop_recurring') }}
                            </a>  --}}
                            <a href="{{ route('whatsapp.setup') }}" data-toggle="tooltip"
                                class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold">
                                {{ __('Whatsapp Cloud API Setup') }}
                            </a>
                            @if ($active_subscription && $active_subscription->status == 1 && $active_subscription->payment_method == 'stripe')
                                <a href="javascript:void(0)"
                                    onclick="delete_row('{{ route('cancel.subscription', $active_subscription->id) }}')"
                                    data-toggle="tooltip"
                                    class="btn btn-flex btn-outline btn-color-gray-700 btn-active-color-primary bg-body h-40px fs-7 fw-bold">
                                    {{ __('cancel_subscription') }}
                                </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content  flex-column-fluid ">
                <div id="kt_app_content_container" class="app-container  container-fluid ">
                    <div class="card mb-6">
                        <div class="card-body pt-9 pb-0">
                            <div class="d-flex flex-wrap flex-sm-nowrap">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                        <div class="d-flex flex-column">
                                            <div class="d-flex align-items-center mb-2">
                                                @php
                                                    $tril_message = null;
                                                    $tril_status = 0;
                                                    $currentDate = new DateTime();

                                                    if (
                                                        !is_null($active_subscription) &&
                                                        $active_subscription->trial_expire_date
                                                    ) {
                                                        $expireDate = new DateTime(
                                                            $active_subscription->trial_expire_date,
                                                        );
                                                        $tril_status = $currentDate <= $expireDate ? 1 : 0;
                                                        $tril_message = $tril_status ? __('Trial Plan ') : '';
                                                    }
                                                @endphp

                                                <a href="#"
                                                    class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{{ $tril_message }}{{ @$active_subscription->plan->name }}</a>
                                                <a href="#">
                                                    <i class="ki-duotone ki-verify fs-1 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </a>
                                            </div>
                                            <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2">
                                                @if ($tril_status != 0)
                                                    <a href="#"
                                                        class="d-flex align-items-center text-gray-600 text-hover-primary me-5 mb-2">
                                                        <i class="ki-duotone ki-dollar fs-2 me-2">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                        {{ __('Trial Expire') }}
                                                        :
                                                        {{ Carbon\Carbon::parse($active_subscription->trial_expire_date)->format('Y-m-d') }}
                                                    </a>
                                                @endif
                                                <a href="#"
                                                    class="d-flex align-items-center text-gray-600 text-hover-primary me-5 mb-2">
                                                    <i class="ki-duotone ki-cheque fs-2 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                        <span class="path5"></span>
                                                        <span class="path6"></span>
                                                        <span class="path7"></span>
                                                    </i>
                                                    {{ __('next_billing') }}
                                                    :
                                                    {{ !is_null($active_subscription) && $active_subscription->expire_date
                                                        ? \Carbon\Carbon::parse($active_subscription->expire_date)->format('Y-m-d')
                                                        : '-' }}

                                                </a>
                                                <a href="#"
                                                    class="d-flex align-items-center text-gray-600 text-hover-primary me-5 mb-2">
                                                    <i class="ki-duotone ki-wallet fs-2 me-2">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                        <span class="path4"></span>
                                                    </i>
                                                    {{ __('payment_method') }}
                                                    :
                                                    {{ !is_null($active_subscription) && $active_subscription->payment_method
                                                        ? $active_subscription->payment_method
                                                        : '-' }}
                                                </a>
                                            </div>
                                        </div>
                                        <div class="d-flex my-4">
                                            <a href="{{ route('available.plans') }}"
                                                class="btn btn-sm btn-primary me-3">{{ __('change_plan') }}</a>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-wrap flex-stack mb-6">
                                        <div class="d-flex flex-column flex-grow-1">
                                            <div class="d-flex flex-wrap justify-content-between justify-content-sm-start">
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-2 mb-4 me-sm-6 mb-sm-3 p-md-3 p-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-duotone ki-wallet fs-2 me-2 text-success">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                        </i>
                                                        <div class="fs-2 fw-bold counted">
                                                            ${{ $active_subscription->price ?? __('N/A') }}</div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-500">{{ __('price') }}</div>
                                                </div>
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-2 mb-4 me-sm-6 mb-sm-3 p-md-3 p-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-duotone ki-people fs-2 me-2 text-danger">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                        <div class="fs-2 fw-bold">
                                                            @if ($active_subscription->team_limit === 0)
                                                                <i class="fa-solid fa-infinity fs-2 text-gray-900"></i>
                                                            @else
                                                                {{ $active_subscription->team_limit }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-500">{{ __('team_limit') }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-2 mb-4 me-sm-6 mb-sm-3 p-md-3 p-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-duotone ki-add-notepad fs-2  me-2 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                        </i>
                                                        <div class="fs-2 fw-bold">
                                                            @if ($active_subscription->campaign_limit === 0)
                                                                <i class="fa-solid fa-infinity fs-2 text-gray-900"></i>
                                                            @else
                                                                {{ $active_subscription->campaign_limit }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-500">{{ __('campaign_limit') }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-2 mb-4 me-sm-6 mb-sm-3 p-md-3 p-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-duotone ki-badge fs-2  me-2 text-success">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                        <div class="fs-2 fw-bold">
                                                            @if ($active_subscription->contact_limit === 0)
                                                                <i class="fa-solid fa-infinity fs-2 text-gray-900"></i>
                                                            @else
                                                                {{ $active_subscription->contact_limit }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-500">{{ __('contact_limit') }}
                                                    </div>
                                                </div>
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-2 mb-4 me-sm-6 mb-sm-3 p-md-3 p-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-duotone ki-messages fs-2  me-2 text-danger">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                            <span class="path4"></span>
                                                            <span class="path5"></span>
                                                        </i>
                                                        <div class="fs-2 fw-bold">
                                                            @if ($active_subscription->conversation_remaining === 0)
                                                                <i class="fa-solid fa-infinity fs-2 text-gray-900"></i>
                                                            @else
                                                                {{ $active_subscription->conversation_remaining }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-500">
                                                        {{ __('conversation_limit') }}</div>
                                                </div>
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-2 mb-4 me-sm-6 mb-sm-3 p-md-3 p-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-duotone ki-technology-2 fs-2  me-2 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <div class="fs-2 fw-bold">
                                                            @if ($active_subscription->max_flow_builder === 0)
                                                                <i class="fa-solid fa-infinity fs-2 text-gray-900"></i>
                                                            @else
                                                                {{ $active_subscription->max_flow_builder }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-500">
                                                        {{ __('max_flow_builder') }}</div>
                                                </div>
                                                <div
                                                    class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-2 mb-4 me-sm-6 mb-sm-3 p-md-3 p-0">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ki-duotone ki-abstract-26 fs-2 me-2 text-success">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                        <div class="fs-2 fw-bold">
                                                            @if ($active_subscription->max_bot_reply === 0)
                                                                <i class="fa-solid fa-infinity fs-2 text-gray-900"></i>
                                                            @else
                                                                {{ $active_subscription->max_bot_reply }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="fw-semibold fs-6 text-gray-500">{{ __('max_bot_reply') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card ">
                        <div class="card-header card-header-stretch border-bottom border-gray-200">
                            <div class="card-title">
                                <h3 class="fw-bold m-0">{{ __('Billing History') }}</h3>
                            </div>
                            <div class="card-toolbar m-0">
                                <ul class="nav nav-stretch nav-line-tabs border-transparent" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a id="kt_billing_alltime_tab" class="nav-link fs-5 fw-semibold"
                                            data-bs-toggle="tab" role="tab" href="#kt_billing_all">
                                            {{ __('All Time') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content">
                            <div id="kt_billing_months" class="card-body p-0 tab-pane fade show active" role="tabpanel"
                                aria-labelledby="kt_billing_months">
                                <div class="table-responsive">
                                    <table class="table table-row-bordered align-middle gy-4 gs-9">
                                        <thead
                                            class="border-bottom border-gray-200 fs-6 text-gray-600 fw-bold bg-light bg-opacity-75">
                                            <tr>
                                                <td class="min-w-150px">{{ __('Date') }}</td>
                                                <td class="min-w-250px">{{ __('Description') }}</td>
                                                <td class="min-w-150px">{{ __('Amount') }}</td>
                                                <td class="min-w-150px">{{ __('Invoice') }}</td>
                                                <td></td>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">


                                            @forelse ($billingHistory as $invoice)
                                                <tr>
                                                    <td>{{ $invoice['date'] }}</td>
                                                    <td><a href="#">{{ $invoice['description'] }}</a></td>
                                                    <td>{{ $invoice['amount'] }}</td>
                                                    <td>
                                                        <a href="{{ $invoice['invoice_pdf'] }}"
                                                            class="btn btn-sm btn-light btn-active-light-primary">
                                                            <i class="ki-duotone ki-exit-up fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            PDF
                                                        </a>
                                                    </td>
                                                    <td class="text-right">
                                                        <a href="{{ $invoice['invoice_pdf'] }}" target="_blank"
                                                            class="btn btn-sm btn-light btn-active-light-primary">
                                                            <i class="ki-duotone ki-exit-up fs-2">
                                                                <span class="path1"></span>
                                                                <span class="path2"></span>
                                                            </i>
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">Empty.</td>
                                                </tr>
                                            @endforelse

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('common.delete-script')
    @endsection
