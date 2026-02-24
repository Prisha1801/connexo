@extends('general.index-client', $setup)

@section('title')
    {{ __($setup['title']) }}
    <x-button-links />
@endsection

@section('cardbody')
    <div class="card-body">
        <!--begin::Form-->
        <form id="kt_form" action="{{ $setup['action'] }}" method="POST" enctype="multipart/form-data" class="form">
            @csrf
            @isset($setup['isupdate'])
                @method('PUT')
            @endisset

            <!--begin::Main form container-->
            <div class="d-flex flex-column gap-7 gap-lg-10">
                <!--begin::Fields section-->
                <div class="card card-flush py-4">
                    <!--begin::Card body-->
                    <div class="card-body pt-0">
                        @isset($setup['inrow'])
                            <div class="row g-5">
                            @endisset

                            @include('partials.fields', ['fields' => $fields])

                            @isset($setup['inrow'])
                            </div>
                        @endisset
                    </div>
                    <!--end::Card body-->
                </div>
                <!--end::Fields section-->

                <!--begin::Actions-->
                <div class="d-flex justify-content-start">
                    <div class="card-footer d-flex justify-content-start">
                        @if (isset($setup['isupdate']))
                            <button type="submit" class="btn btn-primary">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen055.svg-->
                                <span class="svg-icon svg-icon-3 ms-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M6 8.725C6 8.15 6.4 7.725 7 7.725H14L18 11.725V12.275L14 16.275H7C6.4 16.275 6 15.85 6 15.275C6 14.7 6.4 14.275 7 14.275H12V11.725H7C6.4 11.725 6 11.3 6 10.725V8.725Z"
                                            fill="currentColor" />
                                        <path opacity="0.3"
                                            d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM11 7.725H7C6.4 7.725 6 8.15 6 8.725V10.725C6 11.3 6.4 11.725 7 11.725H11V7.725ZM7 14.275H11V11.725H7C6.4 11.725 6 12.15 6 12.725V15.275C6 15.85 6.4 16.275 7 16.275H12L18 12.275V11.725L12 7.725H7C6.4 7.725 6 8.15 6 8.725V10.725C6 11.3 6.4 11.725 7 11.725H11V14.275H7Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                {{ __('Update record') }}
                            </button>
                        @else
                            <button type="submit" class="btn btn-primary">
                                <!--begin::Svg Icon | path: icons/duotune/general/gen055.svg-->
                                <span class="svg-icon svg-icon-3 ms-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3"
                                            d="M3 13V11C3 10.4 3.4 10 4 10H20C20.6 10 21 10.4 21 11V13C21 13.6 20.6 14 20 14H4C3.4 14 3 13.6 3 13Z"
                                            fill="currentColor" />
                                        <path
                                            d="M13 21H11C10.4 21 10 20.6 10 20V4C10 3.4 10.4 3 11 3H13C13.6 3 14 3.4 14 4V20C14 20.6 13.6 21 13 21Z"
                                            fill="currentColor" />
                                    </svg>
                                </span>
                                <!--end::Svg Icon-->
                                {{ __('Create record') }}
                            </button>
                        @endif
                    </div>
                </div>
                <!--end::Actions-->
            </div>
            <!--end::Main form container-->
        </form>
        <!--end::Form-->
    </div>
@endsection
