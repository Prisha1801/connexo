@extends('layouts.app-client')
@section('title', __('payment_methods'))
@push('topcss')
    <link href="{{ asset('css/countrySelect.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/intlTelInput.css') }}">
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

        input#country_selector,
        input#billing_phone {
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

    <div class="d-flex flex-column flex-lg-row">
        <div class="flex-lg-row-fluid order-2 order-lg-2 mb-10 mb-lg-0">
            <form class="form" action="#" id="kt_subscriptions_create_new">
                <div class="card card-flush pt-3 mb-5 mb-lg-10">
                    <div class="card-header">
                        <div class="card-title">
                            <h2 class="fw-bold">Customer</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="text-gray-500 fw-semibold fs-5 mb-5">
                            Select or add a customer to a subscription:
                        </div>
                        <div class="d-flex align-items-center p-3 mb-2">
                            <div class="symbol symbol-60px symbol-circle me-3">
                                <img alt="Pic" src="/metronic8/demo38/assets/media/avatars/300-5.jpg">
                            </div>
                            <div class="d-flex flex-column">
                                <a href="#" class="fs-4 fw-bold text-gray-900 text-hover-primary me-2">Sean
                                    Bean</a>
                                <a href="#" class="fw-semibold text-gray-600 text-hover-primary">sean@dellito.com</a>
                            </div>
                        </div>
                        <div class="mb-7 d-none">
                            <a href="#" class="btn btn-info" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_customer_search">Select Customer</a>
                            <span class="fw-bold text-gray-500 mx-2">or</span>
                            <a href="/metronic8/demo38/apps/customers/list.html" class="btn btn-light-primary">Add
                                New Customer</a>
                        </div>
                        <div class="mb-10">
                            <a href="#" class="btn btn-light-primary" data-bs-toggle="modal"
                                data-bs-target="#kt_modal_customer_search">Change Customer</a>
                        </div>
                        <div
                            class="notice d-flex bg-light-primary rounded border-primary border border-dashed rounded-3 p-6">
                            <div class="d-flex flex-stack flex-grow-1 ">
                                <!--begin::Content-->
                                <div class=" fw-semibold">
                                    <h4 class="text-gray-900 fw-bold">This is a very important privacy notice!</h4>
                                    <div class="fs-6 text-gray-700 ">Writing headlines for blog posts is much
                                        science and probably cool audience. <a href="#" class="fw-bold">Learn
                                            more</a>.</div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="card card-flush pt-3 mb-5 mb-lg-10">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="fw-bold">Products</h2>
                                </div>
                                <!--begin::Card title-->

                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <button type="button" class="btn btn-light-primary" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_add_product">Add Product</button>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Table wrapper-->
                                <div class="table-responsive">
                                    <!--begin::Table-->
                                    <div id="kt_subscription_products_table_wrapper"
                                        class="dt-container dt-bootstrap5 dt-empty-footer">
                                        <div id="" class="table-responsive">
                                            <table
                                                class="table align-middle table-row-dashed fs-6 fw-semibold gy-4 dataTable"
                                                id="kt_subscription_products_table" style="width: 100%;">
                                                <colgroup>
                                                    <col data-dt-column="0" style="width: 300px;">
                                                    <col data-dt-column="1" style="width: 100px;">
                                                    <col data-dt-column="2" style="width: 150px;">
                                                    <col data-dt-column="3" style="width: 70px;">
                                                </colgroup>
                                                <thead>
                                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                        <th class="min-w-300px dt-orderable-none" data-dt-column="0"
                                                            rowspan="1" colspan="1"><span
                                                                class="dt-column-title">Product</span><span
                                                                class="dt-column-order"></span></th>
                                                        <th class="min-w-100px dt-orderable-none dt-type-numeric"
                                                            data-dt-column="1" rowspan="1" colspan="1"><span
                                                                class="dt-column-title">Qty</span><span
                                                                class="dt-column-order"></span></th>
                                                        <th class="min-w-150px dt-orderable-none" data-dt-column="2"
                                                            rowspan="1" colspan="1"><span
                                                                class="dt-column-title">Total</span><span
                                                                class="dt-column-order"></span></th>
                                                        <th class="min-w-70px text-end dt-orderable-none" data-dt-column="3"
                                                            rowspan="1" colspan="1"><span
                                                                class="dt-column-title">Remove</span><span
                                                                class="dt-column-order"></span></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="text-gray-600">
                                                    <tr>
                                                        <td>Beginner Plan</td>
                                                        <td class="dt-type-numeric">1</td>
                                                        <td>149.99 / Month</td>
                                                        <td class="text-end">
                                                            <!--begin::Delete-->
                                                            <a href="#"
                                                                class="btn btn-icon btn-flex btn-active-light-primary w-30px h-30px me-3"
                                                                data-bs-toggle="tooltip" data-kt-action="product_remove"
                                                                aria-label="Delete" data-bs-original-title="Delete"
                                                                data-kt-initialized="1">
                                                                <i class="ki-outline ki-trash fs-3"></i> </a>
                                                            <!--end::Delete-->
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Pro Plan</td>
                                                        <td class="dt-type-numeric">1</td>
                                                        <td>499.99 / Month</td>
                                                        <td class="text-end">
                                                            <!--begin::Delete-->
                                                            <a href="#"
                                                                class="btn btn-icon btn-flex btn-active-light-primary w-30px h-30px me-3"
                                                                data-bs-toggle="tooltip" data-kt-action="product_remove"
                                                                aria-label="Delete" data-bs-original-title="Delete"
                                                                data-kt-initialized="1">
                                                                <i class="ki-outline ki-trash fs-3"></i> </a>
                                                            <!--end::Delete-->
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Team Plan</td>
                                                        <td class="dt-type-numeric">1</td>
                                                        <td>999.99 / Month</td>
                                                        <td class="text-end">
                                                            <!--begin::Delete-->
                                                            <a href="#"
                                                                class="btn btn-icon btn-flex btn-active-light-primary w-30px h-30px me-3"
                                                                data-bs-toggle="tooltip" data-kt-action="product_remove"
                                                                aria-label="Delete" data-bs-original-title="Delete"
                                                                data-kt-initialized="1">
                                                                <i class="ki-outline ki-trash fs-3"></i> </a>
                                                            <!--end::Delete-->
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <tfoot></tfoot>
                                            </table>
                                        </div>
                                        <div id="" class="row">
                                            <div id=""
                                                class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start dt-toolbar">
                                            </div>
                                            <div id=""
                                                class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                            </div>
                                        </div>
                                    </div>
                                    <!--end::Table-->
                                </div>
                                <!--end::Table wrapper-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <div class="card card-flush pt-3 mb-5 mb-lg-10" data-kt-subscriptions-form="pricing">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="fw-bold">Payment Method</h2>
                                </div>
                                <!--begin::Card title-->

                                <!--begin::Card toolbar-->
                                <div class="card-toolbar">
                                    <a href="#" class="btn btn-light-primary" data-bs-toggle="modal"
                                        data-bs-target="#kt_modal_new_card">
                                        New Method
                                    </a>
                                </div>
                                <!--end::Card toolbar-->
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Options-->
                                <div id="kt_create_new_payment_method">
                                    <!--begin::Option-->
                                    <div class="py-1">
                                        <!--begin::Header-->
                                        <div class="py-3 d-flex flex-stack flex-wrap">
                                            <!--begin::Toggle-->
                                            <div class="d-flex align-items-center collapsible toggle "
                                                data-bs-toggle="collapse"
                                                data-bs-target="#kt_create_new_payment_method_1">
                                                <!--begin::Arrow-->
                                                <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                                                    <i class="ki-outline ki-minus-square toggle-on text-primary fs-2"></i>
                                                    <i class="ki-outline ki-plus-square toggle-off fs-2"></i>
                                                </div>
                                                <!--end::Arrow-->

                                                <!--begin::Logo-->
                                                <img src="/metronic8/demo38/assets/media/svg/card-logos/mastercard.svg"
                                                    class="w-40px me-3" alt="">
                                                <!--end::Logo-->

                                                <!--begin::Summary-->
                                                <div class="me-3">
                                                    <div class="d-flex align-items-center fw-bold">Mastercard <div
                                                            class="badge badge-light-info ms-5">Primary</div>
                                                    </div>
                                                    <div class="text-muted">Expires Dec 2024</div>
                                                </div>
                                                <!--end::Summary-->
                                            </div>
                                            <!--end::Toggle-->

                                            <!--begin::Input-->
                                            <div class="d-flex my-3 ms-9">
                                                <!--begin::Radio-->
                                                <label class="form-check form-check-custom form-check-solid me-5">
                                                    <input class="form-check-input" type="radio" name="payment_method"
                                                        checked="checked">
                                                </label>
                                                <!--end::Radio-->
                                            </div>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Header-->

                                        <!--begin::Body-->
                                        <div id="kt_create_new_payment_method_1" class="collapse show fs-6 ps-10">
                                            <!--begin::Details-->
                                            <div class="d-flex flex-wrap py-5">
                                                <!--begin::Col-->
                                                <div class="flex-equal me-5">
                                                    <table class="table table-flush fw-semibold gy-1">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Name</td>
                                                                <td class="text-gray-800">Emma Smith</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Number</td>
                                                                <td class="text-gray-800">**** 2116</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Expires</td>
                                                                <td class="text-gray-800">12/2024</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Type</td>
                                                                <td class="text-gray-800">Mastercard credit card</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Issuer</td>
                                                                <td class="text-gray-800">VICBANK</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">ID</td>
                                                                <td class="text-gray-800">id_4325df90sdf8</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="flex-equal ">
                                                    <table class="table table-flush fw-semibold gy-1">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Billing address
                                                                </td>
                                                                <td class="text-gray-800">AU</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Phone</td>
                                                                <td class="text-gray-800">No phone provided</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Email</td>
                                                                <td class="text-gray-800"><a href="#"
                                                                        class="text-gray-900 text-hover-primary">smith@kpmg.com</a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Origin</td>
                                                                <td class="text-gray-800">Australia <div
                                                                        class="symbol symbol-20px symbol-circle ms-2"><img
                                                                            src="/metronic8/demo38/assets/media/flags/australia.svg">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">CVC check</td>
                                                                <td class="text-gray-800">Passed <i
                                                                        class="ki-outline ki-check-circle fs-2 text-success"></i>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Option-->

                                    <div class="separator separator-dashed"></div> <!--begin::Option-->
                                    <div class="py-1">
                                        <!--begin::Header-->
                                        <div class="py-3 d-flex flex-stack flex-wrap">
                                            <!--begin::Toggle-->
                                            <div class="d-flex align-items-center collapsible toggle collapsed"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#kt_create_new_payment_method_2">
                                                <!--begin::Arrow-->
                                                <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                                                    <i class="ki-outline ki-minus-square toggle-on text-primary fs-2"></i>
                                                    <i class="ki-outline ki-plus-square toggle-off fs-2"></i>
                                                </div>
                                                <!--end::Arrow-->

                                                <!--begin::Logo-->
                                                <img src="/metronic8/demo38/assets/media/svg/card-logos/visa.svg"
                                                    class="w-40px me-3" alt="">
                                                <!--end::Logo-->

                                                <!--begin::Summary-->
                                                <div class="me-3">
                                                    <div class="d-flex align-items-center fw-bold">Visa </div>
                                                    <div class="text-muted">Expires Feb 2022</div>
                                                </div>
                                                <!--end::Summary-->
                                            </div>
                                            <!--end::Toggle-->

                                            <!--begin::Input-->
                                            <div class="d-flex my-3 ms-9">
                                                <!--begin::Radio-->
                                                <label class="form-check form-check-custom form-check-solid me-5">
                                                    <input class="form-check-input" type="radio" name="payment_method">
                                                </label>
                                                <!--end::Radio-->
                                            </div>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Header-->

                                        <!--begin::Body-->
                                        <div id="kt_create_new_payment_method_2" class="collapse  fs-6 ps-10">
                                            <!--begin::Details-->
                                            <div class="d-flex flex-wrap py-5">
                                                <!--begin::Col-->
                                                <div class="flex-equal me-5">
                                                    <table class="table table-flush fw-semibold gy-1">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Name</td>
                                                                <td class="text-gray-800">Melody Macy</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Number</td>
                                                                <td class="text-gray-800">**** 4453</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Expires</td>
                                                                <td class="text-gray-800">02/2022</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Type</td>
                                                                <td class="text-gray-800">Visa credit card</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Issuer</td>
                                                                <td class="text-gray-800">ENBANK</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">ID</td>
                                                                <td class="text-gray-800">id_w2r84jdy723</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="flex-equal ">
                                                    <table class="table table-flush fw-semibold gy-1">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Billing address
                                                                </td>
                                                                <td class="text-gray-800">UK</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Phone</td>
                                                                <td class="text-gray-800">No phone provided</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Email</td>
                                                                <td class="text-gray-800"><a href="#"
                                                                        class="text-gray-900 text-hover-primary">melody@altbox.com</a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Origin</td>
                                                                <td class="text-gray-800">United Kingdom <div
                                                                        class="symbol symbol-20px symbol-circle ms-2"><img
                                                                            src="/metronic8/demo38/assets/media/flags/united-kingdom.svg">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">CVC check</td>
                                                                <td class="text-gray-800">Passed <i
                                                                        class="ki-outline ki-check fs-2 text-success"></i>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Option-->

                                    <div class="separator separator-dashed"></div> <!--begin::Option-->
                                    <div class="py-1">
                                        <!--begin::Header-->
                                        <div class="py-3 d-flex flex-stack flex-wrap">
                                            <!--begin::Toggle-->
                                            <div class="d-flex align-items-center collapsible toggle collapsed"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#kt_create_new_payment_method_3">
                                                <!--begin::Arrow-->
                                                <div class="btn btn-sm btn-icon btn-active-color-primary ms-n3 me-2">
                                                    <i class="ki-outline ki-minus-square toggle-on text-primary fs-2"></i>
                                                    <i class="ki-outline ki-plus-square toggle-off fs-2"></i>
                                                </div>
                                                <!--end::Arrow-->

                                                <!--begin::Logo-->
                                                <img src="/metronic8/demo38/assets/media/svg/card-logos/american-express.svg"
                                                    class="w-40px me-3" alt="">
                                                <!--end::Logo-->

                                                <!--begin::Summary-->
                                                <div class="me-3">
                                                    <div class="d-flex align-items-center fw-bold">American Express <div
                                                            class="badge badge-light-danger ms-5">Expired</div>
                                                    </div>
                                                    <div class="text-muted">Expires Aug 2021</div>
                                                </div>
                                                <!--end::Summary-->
                                            </div>
                                            <!--end::Toggle-->

                                            <!--begin::Input-->
                                            <div class="d-flex my-3 ms-9">
                                                <!--begin::Radio-->
                                                <label class="form-check form-check-custom form-check-solid me-5">
                                                    <input class="form-check-input" type="radio" name="payment_method">
                                                </label>
                                                <!--end::Radio-->
                                            </div>
                                            <!--end::Input-->
                                        </div>
                                        <!--end::Header-->

                                        <!--begin::Body-->
                                        <div id="kt_create_new_payment_method_3" class="collapse  fs-6 ps-10">
                                            <!--begin::Details-->
                                            <div class="d-flex flex-wrap py-5">
                                                <!--begin::Col-->
                                                <div class="flex-equal me-5">
                                                    <table class="table table-flush fw-semibold gy-1">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Name</td>
                                                                <td class="text-gray-800">Max Smith</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Number</td>
                                                                <td class="text-gray-800">**** 9773</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Expires</td>
                                                                <td class="text-gray-800">08/2021</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Type</td>
                                                                <td class="text-gray-800">American express credit card</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Issuer</td>
                                                                <td class="text-gray-800">USABANK</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">ID</td>
                                                                <td class="text-gray-800">id_89457jcje63</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::Col-->
                                                <!--begin::Col-->
                                                <div class="flex-equal ">
                                                    <table class="table table-flush fw-semibold gy-1">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Billing address
                                                                </td>
                                                                <td class="text-gray-800">US</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Phone</td>
                                                                <td class="text-gray-800">No phone provided</td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Email</td>
                                                                <td class="text-gray-800"><a href="#"
                                                                        class="text-gray-900 text-hover-primary">max@kt.com</a>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">Origin</td>
                                                                <td class="text-gray-800">United States of America <div
                                                                        class="symbol symbol-20px symbol-circle ms-2"><img
                                                                            src="/metronic8/demo38/assets/media/flags/united-states.svg">
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-muted min-w-125px w-125px">CVC check</td>
                                                                <td class="text-gray-800">Failed <i
                                                                        class="ki-outline ki-cross fs-2 text-danger"></i>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::Col-->
                                            </div>
                                            <!--end::Details-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                    <!--end::Option-->

                                </div>
                                <!--end::Options-->
                            </div>
                            <!--end::Card body-->
                        </div>
                        <div class="card card-flush pt-3 mb-5 mb-lg-10">
                            <!--begin::Card header-->
                            <div class="card-header">
                                <!--begin::Card title-->
                                <div class="card-title">
                                    <h2 class="fw-bold">Advanced Options</h2>
                                </div>
                                <!--begin::Card title-->
                            </div>
                            <!--end::Card header-->

                            <!--begin::Card body-->
                            <div class="card-body pt-0">
                                <!--begin::Custom fields-->
                                <div class="d-flex flex-column mb-15 fv-row">
                                    <!--begin::Label-->
                                    <div class="fs-5 fw-bold form-label mb-3">
                                        Custom fields

                                        <span class="ms-2 cursor-pointer" data-bs-toggle="popover"
                                            data-bs-trigger="hover" data-bs-html="true"
                                            data-bs-content="Add custom fields to the billing invoice."
                                            data-kt-initialized="1">
                                            <i class="ki-outline ki-information fs-7"></i> </span>
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Table wrapper-->
                                    <div class="table-responsive">
                                        <!--begin::Table-->
                                        <div id="kt_create_new_custom_fields_wrapper"
                                            class="dt-container dt-bootstrap5 dt-empty-footer">
                                            <div id="" class="table-responsive">
                                                <table id="kt_create_new_custom_fields"
                                                    class="table align-middle table-row-dashed fw-semibold fs-6 gy-5 dataTable"
                                                    style="width: 100%;">
                                                    <colgroup>
                                                        <col data-dt-column="0" style="width: 252.933px;">
                                                        <col data-dt-column="1" style="width: 263.367px;">
                                                        <col data-dt-column="2" style="width: 66.45px;">
                                                    </colgroup>
                                                    <!--begin::Table head-->
                                                    <thead>
                                                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                                            <th class="pt-0 dt-orderable-none" data-dt-column="0"
                                                                rowspan="1" colspan="1"><span
                                                                    class="dt-column-title">Field Name</span><span
                                                                    class="dt-column-order"></span></th>
                                                            <th class="pt-0 dt-orderable-none" data-dt-column="1"
                                                                rowspan="1" colspan="1"><span
                                                                    class="dt-column-title">Field Value</span><span
                                                                    class="dt-column-order"></span></th>
                                                            <th class="pt-0 text-end dt-orderable-none" data-dt-column="2"
                                                                rowspan="1" colspan="1"><span
                                                                    class="dt-column-title">Remove</span><span
                                                                    class="dt-column-order"></span></th>
                                                        </tr>
                                                    </thead>
                                                    <!--end::Table head-->

                                                    <!--begin::Table body-->
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control form-control-solid" name="null-0"
                                                                    value="">
                                                            </td>
                                                            <td>
                                                                <input type="text"
                                                                    class="form-control form-control-solid" name="null-0"
                                                                    value="">
                                                            </td>
                                                            <td class="text-end">
                                                                <button type="button"
                                                                    class="btn btn-icon btn-flex btn-active-light-primary w-30px h-30px me-3"
                                                                    data-kt-action="field_remove">
                                                                    <i class="ki-outline ki-trash fs-3"></i> </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <!--end::Table body-->
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>
                                            <div id="" class="row">
                                                <div id=""
                                                    class="col-sm-12 col-md-5 d-flex align-items-center justify-content-center justify-content-md-start dt-toolbar">
                                                </div>
                                                <div id=""
                                                    class="col-sm-12 col-md-7 d-flex align-items-center justify-content-center justify-content-md-end">
                                                </div>
                                            </div>
                                        </div>
                                        <!--end:Table-->
                                    </div>
                                    <!--end::Table wrapper-->

                                    <!--begin::Add custom field-->
                                    <button type="button" class="btn btn-light-primary me-auto"
                                        id="kt_create_new_custom_fields_add">Add custom field</button>
                                    <!--end::Add custom field-->
                                </div>
                                <!--end::Custom fields-->

                                <!--begin::Invoice footer-->
                                <div class="d-flex flex-column mb-10 fv-row">
                                    <!--begin::Label-->
                                    <div class="fs-5 fw-bold form-label mb-3">
                                        Invoice footer

                                        <span class="ms-2 cursor-pointer" data-bs-toggle="popover"
                                            data-bs-trigger="hover focus" data-bs-html="true"
                                            data-bs-content="Add an addition invoice footer note."
                                            data-kt-initialized="1">
                                            <i class="ki-outline ki-information fs-7"></i> </span>
                                    </div>
                                    <!--end::Label-->

                                    <textarea class="form-control form-control-solid rounded-3" rows="4"></textarea>
                                </div>
                                <!--end::Invoice footer-->

                                <!--begin::Option-->
                                <div
                                    class="d-flex flex-column mb-5 fv-row rounded-3 p-7 border border-dashed border-gray-300">
                                    <!--begin::Label-->
                                    <div class="fs-5 fw-bold form-label mb-3">
                                        Usage treshold

                                        <span class="ms-2 cursor-pointer" data-bs-toggle="popover"
                                            data-bs-trigger="hover focus" data-bs-html="true" data-bs-delay-hide="1000"
                                            data-bs-content="Thresholds help manage risk by limiting the unpaid usage balance a customer can accrue. Thresholds only measure and bill for metered usage (including discounts but excluding tax). <a href='#'>Learn more</a>."
                                            data-kt-initialized="1">
                                            <i class="ki-outline ki-information fs-7"></i> </span>
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" checked="" value="1">
                                        <span class="form-check-label text-gray-600">
                                            Bill immediately if usage treshold reaches 80%.
                                        </span>
                                    </label>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Option-->

                                <!--begin::Option-->
                                <div class="d-flex flex-column fv-row rounded-3 p-7 border border-dashed border-gray-300">
                                    <!--begin::Label-->
                                    <div class="fs-5 fw-bold form-label mb-3">
                                        Pro-rate billing

                                        <span class="ms-2 cursor-pointer" data-bs-toggle="popover"
                                            data-bs-trigger="hover focus" data-bs-html="true" data-bs-delay-hide="1000"
                                            data-bs-content="Pro-rated billing dynamically calculates the remainder amount leftover per billing cycle that is owed. <a href='#'>Learn more</a>."
                                            data-kt-initialized="1">
                                            <i class="ki-outline ki-information fs-7"></i> </span>
                                    </div>
                                    <!--end::Label-->

                                    <!--begin::Checkbox-->
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1">
                                        <span class="form-check-label text-gray-600">
                                            Allow pro-rated billing when treshold usage is paid before end of billing cycle.
                                        </span>
                                    </label>
                                    <!--end::Checkbox-->
                                </div>
                                <!--end::Option-->
                            </div>
                            <!--end::Card body-->
                        </div> --}}
            </form>
        </div>

        <div class="flex-column flex-lg-row-auto w-100 w-lg-350px w-xl-400px mb-10 order-1 order-lg-1 me-lg-8">
            <!--begin::Card-->
            <div class="card card-flush pt-3 mb-0" data-kt-sticky="true" data-kt-sticky-name="subscription-summary"
                data-kt-sticky-offset="{default: false, lg: '200px'}" data-kt-sticky-width="{lg: '250px', xl: '300px'}"
                data-kt-sticky-left="auto" data-kt-sticky-top="150px" data-kt-sticky-animation="false"
                data-kt-sticky-zindex="95" style="">
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
                                Sean Bean </a>
                            <span class="badge badge-light-success">Active</span>
                        </div>
                        <a href="#" class="fw-semibold text-gray-600 text-hover-primary">sean@dellito.com</a>
                    </div>
                    <div class="separator separator-dashed mb-7"></div>
                    <div class="mb-7">
                        <h5 class="mb-3">Product details</h5>
                        <div class="mb-0">
                            <span class="badge badge-light-info me-2">Basic Bundle</span>
                            <span class="fw-semibold text-gray-600">$149.99 / Year</span>
                        </div>
                    </div>
                    <div class="separator separator-dashed mb-7"></div>
                    <div class="mb-10">

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
                                    <div class="fs-5 fw-bold" data-kt-countup-prefix="$" id="coupon-result-final-pay">
                                        @money($package->price, config('settings.site_currency', 'usd'), config('settings.site_do_currency', true))</div>
                                </td>
                            </tr>
                            @if ($coupon_used <= 6)
                                <tr>
                                    <td colspan="2">{{ __('coupon_code') }}
                                        <div class="input-group mb-5">
                                            <input type="text" class="form-control" placeholder="{{ __('coupon_code') }}"
                                                aria-label="Recipient's username" aria-describedby="basic-addon2" />
                                            <span class="input-group-text" id="basic-addon2">
                                                @if ($coupon_used <= 6)
                                                        <a href="javascript:void(0)" class="btn btn-success btn-sm"
                                                            onclick="applyCoupon()">Apply Coupon</a>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="input-group mb-5">
                                            <span class="input-group-text">
                                                {{ __('coupon_code') }}
                                            </span>
                                            <input type="text" class="form-control"
                                                aria-label="Amount (to the nearest dollar)" />
                                            <span class="input-group-text">
                                                @if ($coupon_used <= 6)
                                                        <a href="javascript:void(0)" class="btn btn-success btn-sm"
                                                            onclick="applyCoupon()">Apply Coupon</a>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="inp-container cursor-pointer rel-position flex-1 inp-focused">

                                            <input name="coupon_code" id="coupon_code" type="text" maxlength=""
                                                placeholder="Código de cupón" class="form-control input-field-radius">

                                            <small id="coupon-result-error" style="color:red"></small>

                                            <small id="coupon-result-success" style="color:green">&nbsp;</small>

                                            @if ($coupon_used <= 6)
                                                <div class="mt-1 text-center mt-1">

                                                    <a href="javascript:void(0)" class="btn btn-success d-block"
                                                        onclick="applyCoupon()">Apply Coupon</a>

                                                </div>
                                            @endif

                                        </div>
                                    </td>
                                </tr>
                            @endif
                            {{-- <tr>
                                            <td>{{ __('discount_amount') }}</td>
                                            <td class="text-end">
                                                <div class="fs-5 fw-bold" data-kt-countup-prefix="$"
                                                    id="coupon-result-discount"></div>
                                            </td>
                                        </tr> --}}
                        </table>


                        <h5 class="mb-3">Payment Details</h5>
                        <!--end::Title-->

                        <!--begin::Details-->
                        <div class="mb-0">
                            <!--begin::Card info-->
                            <div class="fw-semibold text-gray-600 d-flex align-items-center">
                                Mastercard

                                <img src="/metronic8/demo38/assets/media/svg/card-logos/mastercard.svg"
                                    class="w-35px ms-2" alt="">
                            </div>
                            <!--end::Card info-->

                            <!--begin::Card expiry-->
                            <div class="fw-semibold text-gray-600">Expires Dec 2024</div>
                            <!--end::Card expiry-->
                        </div>
                        <!--end::Details-->
                    </div>
                    <!--end::Section-->

                    <!--begin::Actions-->
                    <div class="mb-0">
                        <button type="submit" class="btn btn-info" id="kt_subscriptions_create_button">

                            <!--begin::Indicator label-->
                            <span class="indicator-label">
                                Create Subscription</span>
                            <!--end::Indicator label-->

                            <!--begin::Indicator progress-->
                            <span class="indicator-progress">
                                Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                            <!--end::Indicator progress--> </button>
                    </div>
                    <!--end::Actions-->
                </div>
                <!--end::Card body-->
            </div>
            <!--end::Card-->
        </div>
    </div>


    <section class="options">
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
                    <!--begin::Col-->
                    <div class="col-xxl-8">
                        <!--begin::Billing Details-->
                        <div class="card  card-xxl-stretch mb-5 mb-xxl-10">
                            @if (session('error'))
                                <div role="alert" class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <!--begin::Header-->
                            <div class="card-header">
                                <div class="card-title">
                                    <h3>{{ __('billing_details') }}</h3>
                                </div>
                            </div>
                            <!--end::Header-->
                            <!--begin::Body-->
                            <div class="card-body pb-0">
                                <!--begin::Left Section-->

                                <div class="row">
                                    <div class="col-md-4 col-xl-4">
                                        <div class="mb-3">
                                            <label class="form-label text-gray-600">{{ __('billing_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control @error('billing_name') is-invalid @enderror"
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
                                                class="form-control @error('billing_email') is-invalid @enderror"
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
                                                class="form-control @error('billing_address') is-invalid @enderror"
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
                                                class="form-control @error('billing_city') is-invalid @enderror"
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
                                                class="form-control @error('billing_state') is-invalid @enderror"
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
                                                class="form-control @error('billing_zipcode') is-invalid @enderror"
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
                                                <input id="country_selector" name="billing_country" class="form-control"
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
                                            <input id="billing_phone" name="billing_phone" class="form-control"
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
                                        {{-- <a href="#"
                                            class="btn btn-info d-block payment_btns disabled_a">{{ __('validate_coupon') }}</a> --}}
                                    @endif
                                    <div class="div_btns d-none">
                                        @if (config('settings.subscription_processor') == 'Stripe')
                                            <button type="submit"
                                                class="w-100 btn btn-info w-10 stripe_btn">{{ __('Process Payment') }}</button>
                                        @endif


                                    </div>
                                </div>
                                <!--end::Left Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Earnings-->
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
                                                                class="form-control @error('coupon_code') is-invalid @enderror text-end"
                                                                name="coupon_code" id="coupon_code"
                                                                placeholder="{{ __('enter_coupon_code') }}">
                                                            <small id="coupon-result-error" style="color:red"></small>
                                                            <small id="coupon-result-success"
                                                                style="color:green">&nbsp;</small>
                                                        </td>
                                                    </tr>
                                                @endif
                                                {{-- <tr>
                                                    <td>{{ __('discount_amount') }}</td>
                                                    <td class="text-end">
                                                        <div class="fs-5 fw-bold" data-kt-countup-prefix="$"
                                                            id="coupon-result-discount"></div>
                                                    </td>
                                                </tr> --}}
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

                                    <!--begin::Action-->
                                    <!--end::Action-->
                                </div>
                                <!--end::Left Section-->
                            </div>
                            <!--end::Body-->
                        </div>
                        <!--end::Invoices-->
                    </div>

                </div>
                <!--end::Row-->
                <?php /*   <div class="row">
                    <div class="col-lg-12">
                        @if ($package->is_free == 0)
                            <!--begin::Payment methods-->
                            <div class="card mb-5 mb-xl-10">
                                <!--begin::Card header-->
                                <div class="card-header card-header-stretch pb-0">
                                    <!--begin::Title-->
                                    <div class="card-title">
                                        <h3 class="m-0"></h3>
                                    </div>
                                    <!--end::Title-->

                                    <!--begin::Toolbar-->
                                    <div class="card-toolbar m-0">
                                        <!--begin::Tab nav-->
                                        <ul class="nav nav-stretch nav-line-tabs border-transparent" role="tablist">
                                            <!--begin::Tab item-->
                                            <li class="nav-item" role="presentation">
                                                <a id="kt_billing_creditcard_tab"
                                                    class="nav-link fs-5 fw-bold me-5 active" data-bs-toggle="tab"
                                                    role="tab" href="#kt_billing_creditcard">
                                                    {{ __('credit_debit_card') }}
                                                </a>
                                            </li>
                                            <!--end::Tab item-->

                                            <!--begin::Tab item-->
                                            <!--end::Tab item-->
                                        </ul>
                                        <!--end::Tab nav-->
                                    </div>
                                    <!--end::Toolbar-->
                                </div>
                                <!--end::Card header-->

                                <!--begin::Tab content-->
                                <div id="kt_billing_payment_tab_content" class="card-body tab-content">
                                    <!--begin::Tab panel-->
                                    <div id="kt_billing_creditcard" class="tab-pane fade show active" role="tabpanel"">
                                        <!--begin::Title-->
                                        <h3 class="mb-5"></h3>
                                        <!--end::Title-->

                                        <!--begin::Row-->
                                        <div class="row gx-9 gy-6">
                                            <!--begin::Col-->
                                            @if (config('settings.subscription_processor') == 'Stripe')
                                                <div class="col-xl-3 payment-box client_payment_box" data-method="stripe">
                                                    <!--begin::Card-->
                                                    {{-- <div
                                                        class="notice d-flex bg-light-primary rounded border-primary border border-dashed h-lg-100 p-6">
                                                        <!--begin::Info-->
                                                        <div class="d-flex flex-column py-2">
                                                            <!--begin::Owner-->
                                                            <div class="d-flex align-items-center fs-4 fw-bold mb-5">
                                                                {{ __('Strip') }}
                                                                <span
                                                                    class="badge badge-light-success fs-7 ms-2">{{ __('Primary') }}</span>
                                                            </div>
                                                            <!--end::Owner-->

                                                            <!--begin::Wrapper-->
                                                            <div class="d-flex align-items-center">
                                                                <!--begin::Icon-->
                                                                <img src="{{ asset('Metronic/assets/media/payment-icon/stripe.svg') }}"
                                                                    alt="" class="me-4" />
                                                                <!--end::Icon-->

                                                                <!--begin::Details-->
                                                                <!--end::Details-->
                                                            </div>
                                                            <!--end::Wrapper-->
                                                        </div>
                                                        <!--end::Info-->

                                                        <!--begin::Actions-->
                                                        <!--end::Actions-->
                                                    </div> --}}
                                                    <!--end::Card-->
                                                </div>
                                                <!--end::Col-->
                                            @endif
                                            <!--begin::Col-->
                                            <!--end::Col-->
                                        </div>
                                        <!--end::Row-->
                                    </div>
                                    <!--end::Tab panel-->

                                    <!--begin::Tab panel-->
                                    <!--end::Tab panel-->


                                    <div class="mt-2 text-center mt-4">
                                        @if ($package->is_free == 1)
                                            <button type="submit"
                                                class="w-100 btn btn-info d-block payment_btns free_btn">{{ __('select_payment_method') }}</button>
                                        @else
                                            {{-- <a href="#"
                                                class="btn btn-info d-block payment_btns disabled_a">{{ __('validate_coupon') }}</a> --}}
                                        @endif
                                        <div class="div_btns d-none">
                                            @if (config('settings.subscription_processor') == 'Stripe')
                                                <button type="submit"
                                                    class="w-100 btn btn-info w-10 stripe_btn">{{ __('proceed_stripe_payment') }}</button>
                                            @endif


                                        </div>
                                    </div>

                                </div>
                                <!--end::Tab content-->
                            </div>
                            <!--end::Payment methods-->
                        @endif

                    </div>
                </div>
            </form>
        </div> */
                ?>
    </section>
@endsection
@push('js')
    <script src="{{ asset('js/countrySelect.min.js') }}"></script>
    <script src="{{ asset('js/intlTelInput.js') }}"></script>
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
        $(document).ready(function() {
            var countryCode = '';
            var input = document.querySelector("#billing_phone");
            window.intlTelInput(input, {
                autoHideDialCode: false,
                autoPlaceholder: "on",
                dropdownContainer: document.body,
                formatOnDisplay: true,
                geoIpLookup: function(callback) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "";
                        callback(countryCode);
                    });
                },
                hiddenInput: "full_number",
                initialCountry: "auto",
                nationalMode: false,
                placeholderNumberType: "MOBILE",
                preferredCountries: ['us', 'uk', 'ca'],
                separateDialCode: false,
                utilsScript: "{{ asset('js/utils.js') }}",
            });

            var countrySelector = $("#country_selector");
            countrySelector.countrySelect({
                autoDropdown: true,
                initialCountry: "auto",
                formatOnDisplay: true,
                geoIpLookup: function(callback) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        var countryCode = (resp && resp.country) ? resp.country : "";
                        callback(countryCode);
                    });
                },
            });


            $(document).ready(function() {
                let val = 'stripe';
                let url = '';
                if (val == 'stripe') {
                    url = '{{ route('stripe.redirect') }}' + '?trx_id={{ $trx_id }}' + '' +
                        '&payment_type=stripe';
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

        })


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
@endpush
