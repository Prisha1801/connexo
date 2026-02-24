<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Site') }}</title>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    @yield('head')
    @include('layouts.rtl')

    <link href="{{ asset('metronic56/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('metronic56/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    {{-- <link href="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" /> --}}
    {{-- <script src="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.js"></script> --}}
    <script src="{{ asset('metronic56/assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('metronic56/assets/plugins/global/plugins.bundle.js') }}"></script>

    <link href="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" />
    {{-- <link href="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" /> --}}
    {{-- <link href="{{ asset('Metronic/assets') }}/css/style.bundle.css" rel="stylesheet" type="text/css" /> --}}
    {{-- <link href="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" /> --}}
    {{-- <link rel="canonical" href="https://preview.keenthemes.com/html/metronic/docs/forms/dropzonejs" /> --}}
    {{-- <link href="{{ asset('vendor/argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet"> --}}
    <!--  <link type="text/css" href="{{ asset('vendor/argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">-->
    <link rel="stylesheet" href="{{ asset('vendor') }}/jasny/css/jasny-bootstrap.min.css">
    <!-- Custom CSS defined by admin -->
    <link type="text/css" href="{{ asset('byadmin') }}/back.css" rel="stylesheet">
    <!-- Select2  -->
    {{-- <link type="text/css" href="{{ asset('vendor') }}/select2/select2.min.css" rel="stylesheet"> oculto ya viene con metronic --}}
    <!-- Custom CSS defined by user -->
    {{-- <link type="text/css" href="{{ asset('custom') }}/css/custom.css?id={{ config('version.version') }}"
        rel="stylesheet">
   <link type="text/css" href="{{ asset('custom/css/custom_ceftx.css') }}" rel="stylesheet"> --}}
    <!-- Flags -->
    <link type="text/css" href="{{ asset('vendor') }}/flag-icons/css/flag-icons.min.css" rel="stylesheet" />
    <!-- Bootstap VUE -->
    <link type="text/css" href="{{ asset('vendor') }}/vue/bootstrap-vue.css" rel="stylesheet" />

    {{-- <link href="{{ asset('backend/Assets/fonts/fontawesome/css/all.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="{{ asset('custom/css/telinput.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/intlTelInput.css') }}"> --}}

    <style>
        /* body {
                 font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            color: var(--dark);
            line-height: 1.6;
            padding: 20px;
        } */ 
        :root {
            --theme-color: {{ env('THEME_COLOR', '#1D5C63') }};
            --theme-color-header-light: {{ env('THEME_COLOR_HEADER_LIGHT', '#283d3b') }};
            --theme-color-sidebar-light: {{ env('THEME_COLOR_SIDEBAR_LIGHT', '#283d3b') }};
            --theme-color-header-dark: {{ env('THEME_COLOR_HEADER_DARK', '#252928') }};
            --theme-color-sidebar-dark: {{ env('THEME_COLOR_SIDEBAR_DARK', '#293332') }};
            --theme-link-color: {{ env('THEME_COLOR_HEADER_LIGHT', '#283d3b') }};
            --bs-btn-color: {{ env('THEME_BTN_COLOR', '#283d3b') }};
            --bs-info-active: {{ env('THEME_COLOR_ACTIVE', '#0c382a') }};
            --bs-btn-bg: {{ env('THEME_BTN_BG', '#1D5C63') }};
            --bs-btn-border-color: {{ env('THEME_BTN_BORDER_COLOR', '#1D5C63') }};
            /* --bs-btn-hover-color: {{ env('THEME_BTN_HOVER_COLOR', '#ffffff') }};
            --bs-btn-hover-bg: {{ env('THEME_BTN_HOVER_BG', '#6130c7') }};
            --bs-btn-hover-border-color: {{ env('THEME_BTN_HOVER_BORDER_COLOR', '#5b2ebb') }}; */
            --bs-btn-focus-shadow-rgb: {{ env('THEME_BTN_FOCUS_SHADOW_RGB', '61, 125, 82') }};
            --bs-btn-active-color: {{ env('THEME_BTN_ACTIVE_COLOR', '#ffffff') }};
            --bs-btn-active-bg: {{ env('THEME_BTN_ACTIVE_BG', '#1ABC9C') }};
            --bs-btn-active-border-color: {{ env('THEME_BTN_ACTIVE_BORDER_COLOR', '#1ABC9C') }};
            --bs-btn-active-shadow: {{ env('THEME_BTN_ACTIVE_SHADOW', 'none') }};
            --bs-btn-disabled-color: {{ env('THEME_BTN_DISABLED_COLOR', '#B0C9BC') }};
            --bs-btn-disabled-bg: {{ env('THEME_BTN_DISABLED_BG', '#E8F5E9') }};
            --bs-btn-disabled-border-color: {{ env('THEME_BTN_DISABLED_BORDER_COLOR', '#D0E0D3') }};
            --bs-info-rgb: {{ env('THEME_LINK_COLOR', '#1D5C63') }};
            --bs-primary: {{ env('THEME_BTN_COLOR', '#283d3b') }};
            --bs-primary-rgb: {{ env('THEME_LINK_COLOR', '#1D5C63') }};
            --bs-dark-rgb: {{ env('THEME_COLOR_DARK', '#123D44') }};
            --bs-dark: {{ env('THEME_COLOR_DARK', '#123D44') }};
            --bs-all-theme-highlight: {{ env('THEME_HIGHLIGHT', '#1ABC9C') }};
            --bs-text-primary: {{ env('THEME_HIGHLIGHT', '#1ABC9C') }};
            --bs-pagination-active-bg: {{ env('THEME_HIGHLIGHT', '#1ABC9C') }};
            --bs-component-active-bg: {{ env('THEME_HIGHLIGHT', '#1ABC9C') }};
            --app-sidebar-background-color: {{ env('THEME_HIGHLIGHT', '#1ABC9C') }};
            --border-top-color: {{ env('THEME_HIGHLIGHT', '#1ABC9C') }};
            --bs-text-info: {{ env('THEME_TEXT_COLOR', '#1D3C36') }};
            --bs-primary-active: {{ env('THEME_BTN_COLOR', '#283d3b') }};
            --bs-link-color: {{ env('THEME_COLOR', '#1D5C63') }};
        }
    </style>

    <style>
        :root,
        [data-bs-theme=light] {
            --menu-icon-title-color: {{ env('THEME_ICON_TITLE_LIGHT') }};
            --app-sidebar-background-color: {{ env('THEME_COLOR_SIDEBAR_LIGHT', '#a4ffb9') }};
            --border-top-color: {{ env('THEME_COLOR_SIDEBAR_LIGHT', '#a4ffb9') }};
            --form-field-background-color: #ffffff;
            --bs-dark-inverse-code: #ffffff;
            --bs-secondary: #c7f4e2;
            --fm-modal-bg: #e6e6e6;
            --bs-text-dark: #1E2129;
            --dial-country-dropdown: #fff;
            --theme-menu-active-color: {{ env('THEME_COLOR_MENU_ACTIVE_LIGHT', '#1D5C63') }};
            --theme-icon-color: {{ env('THEME_ICON_COLOR_LIGHT', '#1D5C63') }};
            --theme-icon-color-hover: {{ env('THEME_ICON_COLOR_HOVER_LIGHT', '#3D7D52') }};
            --theme-icon-color-active: {{ env('THEME_ICON_COLOR_ACTIVE_LIGHT', '#FFFFFF') }};
            --theme-color-menu-selected: {{ env('THEME_COLOR_MENU_SELECTED_LIGHT', '#3D7D52') }};

        }

        [data-bs-theme=dark] {
            --menu-icon-title-color: {{ env('THEME_ICON_TITLE_DARK') }};
            --app-sidebar-background-color: {{ env('THEME_COLOR_SIDEBAR_DARK', '#293332') }};
            --border-top-color: {{ env('THEME_COLOR_SIDEBAR_DARK', '#293332') }};
            --form-field-background-color: #15181c;
            --bs-dark-inverse-code: #ffffff;
            --bs-text-dark: #1E2129;
            --bs-secondary: #363843;
            --fm-modal-bg: #363843;
            --dial-country-dropdown: #363843;
            --theme-menu-active-color: {{ env('THEME_COLOR_MENU_ACTIVE_DARK', '#1ABC9C') }};
            --theme-icon-color: {{ env('THEME_ICON_COLOR_DARK', '#A7E9D9') }};
            --theme-icon-color-hover: {{ env('THEME_ICON_COLOR_HOVER_DARK', '#FFFFFF') }};
            --theme-icon-color-active: {{ env('THEME_ICON_COLOR_ACTIVE_DARK', '#FFFFFF') }};
            --theme-color-menu-selected: {{ env('THEME_COLOR_MENU_SELECTED_DARK', '#1ABC9C') }};
            /* --bs-menu-dropdown-bg-color: #1C1D22;
            --bs-menu-dropdown-box-shadow: 0px 0px 30px rgba(0, 0, 0, 0.3); */
        }
    </style>
    <style>
        .bg-light-dark {
            background-color: var(--fm-modal-bg) !important;
        }

        .iti__country-list {
            position: absolute;
            z-index: 2;
            list-style: none;
            text-align: left;
            padding: 0;
            margin: 0 0 0 -1px;
            box-shadow: 1px 1px 4px rgba(0, 0, 0, .2);
            background-color: var(--dial-country-dropdown);
            border: 1px solid #ccc;
            white-space: nowrap;
            max-height: 200px;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
        }

        .iti__search-input {
            width: 100%;
            height: 30px;
            border-width: 0;
            border-radius: 3px;
        }

        .flag-icon {
            border-radius: 50%;
            position: relative;
            left: -18px;
            top: 18px;
            margin-top: 0 !important;
            height: 1.9em !important;
            width: 1.9em !important;
            background-size: cover;
            border: 3px solid #fff;
        }

        .inChatImage {
            width: 380px !important;
        }

        input #phone {
            padding-left: 47px !important;
        }

        .theChatHolder {
            max-height: 85vh !important;
            min-height: 85vh !important;
        }

        .inChatImage {
            width: 380px !important;
        }

        @media (max-width: 768px) {
            .theChatHolder {
                max-height: 67vh !important;
                min-height: 67vh !important;
                margin-top: 0px;
            }

            .inChatImage {
                width: 200px !important;
            }
        }

        @media (min-width: 768) {
            .theChatHolder {
                max-height: 85vh !important;
                min-height: 85vh !important;
            }
        }

        /* .iti__arrow{
            display: none;
        } */
        .modal-backdrop.show {
            backdrop-filter: blur(0.4rem);
            background-color: rgba(0, 0, 0, 0.6);
        }

        [v-cloak] {
            display: none;
        }
    </style>
    </style>
    @stack('topcss')
    @yield('css')
    <script type="text/javascript">
        var filemanager_request = "";
        var imagetopreview = "";
        var STORAGE_TYPE = "{{ env('STORAGE_TYPE') }}";
        var PATH = "{{ url('') . '/' }}";
        var csrf = "{{ csrf_token() }}";
        var FORMAT_DATE = 'dd/mm/yy';
        var FORMAT_DATE = 'dd/mm/yy';
        var FORMAT_DATETIME = ["dd/mm/yy", "hh:mm TT"];
    </script>
</head>

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-sidebar-minimize="on"
    data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true"
    data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true"
    class="app-default">

    @include('client.partials.theme')
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page  flex-column flex-column-fluid" id="kt_app_page">
            @include('client.layouts.header')
            <div class="app-wrapper  flex-column flex-row-fluid" id="kt_app_wrapper">
                @auth()
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endauth
                @include('client.layouts.sidebar')
                <div class="app-main flex-column flex-row-fluid " id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid "> {{-- PADING-8 --}}
                        <div id="kt_app_content" class="app-content flex-column-fluid ">
                            <div id="kt_app_content_container" class="px-4">
                                @include('client.partials.modal_login')
                                @yield('content')
                            </div>
                        </div>
                    </div>
                    @include('client.layouts.footer')
                </div>
            </div>
        </div>
        @stack('modals')
        @include('client.wizard')
    </div>

    @guest()
    @endguest
    @yield('topjs')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarToggle = document.getElementById('kt_app_sidebar_toggle');
            const sidebarLogo = document.querySelector('.app-sidebar-logo');
            const sidebarLogoMinimize = document.querySelector('.sidebar-logo-minimize');

            // sidebarLogo.style.display = 'none';
            // sidebarLogoMinimize.style.display = 'block';
            sidebarToggle.addEventListener('click', function() {
                const isMinimized = sidebarToggle.classList.contains('active');
                if (!isMinimized) {
                    sidebarLogo.style.display = 'none';
                    sidebarLogoMinimize.style.display = 'block';
                } else {
                    sidebarLogo.style.display = 'block';
                    sidebarLogoMinimize.style.display = 'none';
                }
            });
        });
    </script>
    <script>
        var t = "<?php echo 'translations' . App::getLocale(); ?>";
        window.translations = {!! Cache::get('translations' . App::getLocale(), '[]') !!};
    </script>
    <!-- Navtabs -->

    {{-- <script src="{{ asset('vendor') }}/jquery/jquery.min.js" type="text/javascript"></script> --}}
    <script src="{{ asset('vendor/argon') }}/js/popper.min.js" type="text/javascript"></script>
    <script src="{{ asset('vendor/argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
    <!-- Nouslider -->
    <script src="{{ asset('vendor/argon') }}/vendor/nouislider/distribute/nouislider.min.js" type="text/javascript">
    </script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="{{ asset('vendor') }}/jasny/js/jasny-bootstrap.min.js"></script>
    <!-- All in one -->
    <script src="{{ asset('custom') }}/js/js.js?id={{ config('version.version') }}"></script>
    <!-- Notify JS -->
    <script src="{{ asset('custom') }}/js/notify.min.js"></script>
    <!-- Argon JS -->
    <script src="{{ asset('vendor/argon') }}/js/argon.js?v=1.0.0"></script>
    <script>
        var ONESIGNAL_APP_ID = "{{ config('settings.onesignal_app_id') }}";
        var USER_ID = '{{ auth()->user() && auth()->user() ? auth()->user()->id : '' }}';
        var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";

        var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
    </script>
    @if (auth()->user() != null && auth()->user()->hasRole('staff'))
        <script>
            USER_ID = '{{ auth()->user()->company->user_id }}';
        </script>
    @endif
    <!-- OneSignal -->
    @if (strlen(config('settings.onesignal_app_id')) > 4)
        <script src="{{ asset('vendor') }}/OneSignalSDK/OneSignalSDK.js" async=""></script>
        <script src="{{ asset('custom') }}/js/onesignal.js"></script>
    @endif
    @stack('js')
    @yield('js')
    <!-- Pusher -->
    @if (strlen(config('broadcasting.connections.pusher.app_id')) > 2)
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
        @if (config('settings.app_code_name', '') == 'qrpdf')
            <script src="{{ asset('custom') }}/js/pusher.js"></script>
        @endif
    @endif


    <!-- Import Select2 --->
    {{-- <script src="{{ asset('vendor') }}/select2/select2.min.js"></script> oculto ya viene con metronic --}}
    <!-- Custom JS defined by admin -->
    <script src="{{ asset('byadmin') }}/back.js"></script>
    <!-- Import Moment -->

    <script type="text/javascript" src="{{ asset('vendor') }}/moment/moment.min.js"></script>
    <script type="text/javascript" src="{{ asset('vendor') }}/moment/momenttz.min.js"></script>


    {{-- comentado ya metronic lo trae y da problemas con el chat y con la version del bootstrap
    
    <script src="{{ asset('vendor/argon') }}/js/bootstrap.min.js" type="text/javascript"></script> --}}
    <!-- Import Vue -->
    <script src="{{ asset('vendor') }}/vue/vue.js"></script>
    <script src="{{ asset('vendor') }}/vue/bootstrap-vue.min.js"></script>
    <!-- Import AXIOS --->
    <script src="{{ asset('vendor') }}/axios/axios.min.js"></script>
    <?php
    echo file_get_contents(base_path('public/byadmin/back.js'));
    ?>

    <script src="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.js"></script>
    <script src="{{ asset('Metronic/assets') }}/js/widgets.bundle.js"></script>
    <script src="{{ asset('Metronic/assets') }}/js/custom/widgets.js"></script>
    <script src="{{ asset('Metronic/assets') }}/js/custom/apps/chat/chat.js"></script>
    <script src="{{ asset('Metronic/assets') }}/js/custom/utilities/modals/upgrade-plan.js"></script>
    <script src="{{ asset('Metronic/assets') }}/js/custom/utilities/modals/create-campaign.js"></script>
    <script src="{{ asset('Metronic/assets') }}/js/custom/utilities/modals/users-search.js"></script>
    <script src="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.js"></script>
    {{-- <script src="{{ asset('Metronic/assets') }}/js/custom/apps/ecommerce/catalog/products.js"></script> --}}
    {{-- <script src="{{ asset('Metronic/assets') }}/js/custom/apps/ecommerce/catalog/save-product.js"></script> --}}
    <script src="{{ asset('Metronic/assets') }}/js/custom/apexcharts"></script>
    <script src="{{ asset('Metronic/assets') }}/js/custom/popper.min.js" crossorigin="anonymous"></script>

    @include('client.layouts.contactButton')
    <script src="{{ asset('backend/Assets/File_manager/plugins/jquery.lazy/jquery.lazy.min.js') }}"></script>
    <script src="{{ asset('backend/Assets/File_manager/js/file_manager.js') }}"></script>
    
    <script src="{{ asset('backend/Assets/plugins/nicescroll/nicescroll.min.js') }}"></script>
    <script src="{{ asset('backend/Assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <script src="{{ asset('backend/Assets/js/layout.js') }}"></script>
    <script src="{{ asset('backend/Assets/js/core.js') }}"></script>

    <script>
        File_manager.lazy();
    </script>
</body>

</html>
