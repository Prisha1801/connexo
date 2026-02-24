<!--begin::Header style="height: 70px;" -->
<div id="kt_app_header" class="app-header" style="background-color: var(--theme-color-header); height: 55px;">
    <!--begin::Header container-->
    <div class="app-container container-fluid d-flex justify-content-between flex-stack" id="kt_app_header_container">
        <!--begin::Sidebar toggle-->
        <div class="d-flex align-items-center d-block justify-content-between flex-wrap gap-3 mb-6 mb-lg-0">
            <div class="d-flex mt-2 mt-lg-0">
                <div class="app-header-logo mt-6 mt-lg-0 text-center d-none align-items-center justify-content-center d-lg-flex"
                    style="width: 80px;">
                    <!--begin::Logo image-->
                    <a href="/dashboard">
                        @php
                            //$app_logo = asset('img/sendinai-icon.svg');
                            $app_logo = asset('backend/Assets/img/logo.png');

                            $partner_id = auth()->user()->created_by;
                            if ($partner_id != 1) {
                                $partner_data = \App\Models\Partner::where('user_id', $partner_id)->first();

                                if ($partner_data) {
                                    if ($partner_data->is_active == 1) {
                                        $app_logo = asset($partner_data->logo);
                                    }
                                }
                            }
                        @endphp
                        <img alt="Logo" src="{{ $app_logo }}" class="h-60px" />
                    </a>
                    <!--end::Logo image-->
                </div>
                <div class="btn btn-icon mt-4 w-35px h-35px me-2 d-block d-lg-none active d-flex align-items-center justify-content-center"
                    id="kt_app_sidebar_mobile_toggle">
                    <i class="ki-outline ki-abstract-14 fs-2 text-white"></i>
                </div>

            </div>
        </div>
        {{-- oculto por el momento
        <div
            id="kt_header_search"
            class="header-search d-flex align-items-center ms-10 ms-lg-0 w-lg-350px"
            data-kt-search-keypress="true"
            data-kt-search-min-length="2"
            data-kt-search-enter="enter"
            data-kt-search-layout="menu"
            data-kt-search-responsive="lg"
            data-kt-menu-trigger="auto"
            data-kt-menu-permanent="true"
            data-kt-menu-placement="bottom-start">

            <form data-kt-search-element="form" class="d-none d-lg-block w-100 position-relative mb-5 mb-lg-0" autocomplete="off">
                <input type="hidden"/>
                <i class="ki-outline ki-magnifier search-icon fs-2 text-gray-500 position-absolute top-50 translate-middle-y ms-5"></i>
                <input type="text" class="search-input form-control form-control form-control-solid form-control-sm  ps-13" name="search" value="" placeholder="Search..." data-kt-search-element="input"/>
                <span class="search-spinner  position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-5" data-kt-search-element="spinner">
                    <span class="spinner-border h-15px w-15px align-middle text-gray-500"></span>
                </span>
                <span class="search-reset  btn btn-flush btn-active-color-primary position-absolute top-50 end-0 translate-middle-y lh-0 d-none me-4" data-kt-search-element="clear">
                    <i class="ki-outline ki-cross fs-2 fs-lg-1 me-0"></i>
                </span>
            </form>

        </div> --}}
        {{-- @isset($title_flow)
            <div class="d-flex align-items-center">
                <h2 class="card-header">{{ $title_flow[0] }}</h2>
            </div>
        @endisset --}}
        <!--Logo Resolucion Telefono -->
        {{-- <div class="d-flex align-items-center d-block d-lg-none ms-n3" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px me-2" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-2"></i>
            </div>
        </div> --}}
        <!--Logo Resolucion Telefono-->

        <!--begin::Navbar-->
        @include('client.layouts.navbars.navbar')
        <!--end::Navbar-->

        <!--begin::Separator-->
        <div class="app-navbar-separator separator d-none d-lg-flex"></div>
        <!--end::Separator-->
    </div>
    <!--end::Header container-->
</div>
<!--end::Header-->



{{-- <!--begin::Header-->
<div id="kt_app_header" class="app-header  d-flex align-items-stretch " style="">
    <!--begin::Header container-->
    <div class="app-container  container-fluid d-flex align-items-stretch justify-content-between " id="kt_app_header_container">
        <!--begin::Header wrapper-->
        <div
            class="app-header-wrapper d-flex flex-grow-1 justify-content-around justify-content-lg-between flex-wrap gap-3 mb-6 mb-lg-0"
            data-kt-swapper="true"
            data-kt-swapper-mode="{default: 'prepend', lg: 'prepend'}"
            data-kt-swapper-parent="{default: '#kt_app_content_container', lg: '#kt_app_header_container'}">
            <!--begin::Page title wrapper-->

            @include('client.layouts.navbars.navbar')

        </div>
        <div class="d-flex d-lg-none flex-grow-1 flex-stack gap-4">
            <!--begin::Sidebar toggle-->
            <button class="btn btn-icon btn-sm btn-active-color-primary ms-n2" id="kt_app_sidebar_mobile_toggle">
                <i class="ki-outline ki-abstract-14 fs-1"></i>	</button>
            <!--end::Sidebar toggle-->
            <!--begin::Logo-->
            <a href="?page=index">
                <img alt="Logo" src="{{ asset('custom/imgs/icono-dark.png') }}" class="h-30px"/>
            </a>
            <!--end::Logo-->
            <!--begin::Sidebar panel toggle-->
            <span></span>
            <!--end::Sidebar panel toggle-->
        </div>
        <!--end::Header wrapper-->

    </div>
    <!--end::Header container-->
</div>
<!--end::Header--> --}}
