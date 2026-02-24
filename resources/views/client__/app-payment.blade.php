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

    <link href="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <script src="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.js"></script>

    <link href="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('Metronic/assets') }}/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet"
        type="text/css" />
    <link rel="canonical" href="https://preview.keenthemes.com/html/metronic/docs/forms/dropzonejs" />
    <link href="{{ asset('vendor/argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
    <!--  <link type="text/css" href="{{ asset('vendor/argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">-->
    <link rel="stylesheet" href="{{ asset('vendor') }}/jasny/css/jasny-bootstrap.min.css">
    <!-- Custom CSS defined by admin -->
    <link type="text/css" href="{{ asset('byadmin') }}/back.css" rel="stylesheet">
    <!-- Select2  -->
    <link type="text/css" href="{{ asset('vendor') }}/select2/select2.min.css" rel="stylesheet">
    <!-- Custom CSS defined by user --><link type="text/css" href="{{ asset('vendor') }}/flag-icons/css/flag-icons.min.css" rel="stylesheet" />
    <link type="text/css" href="{{ asset('custom') }}/css/custom.css?id={{ config('version.version') }}"
        rel="stylesheet">
    <link type="text/css" href="{{ asset('custom/css/custom_ceftx.css') }}" rel="stylesheet">
    <!-- Flags -->
    <link type="text/css" href="{{ asset('vendor') }}/flag-icons/css/flag-icons.min.css" rel="stylesheet" />
    <!-- Bootstap VUE -->
    <link type="text/css" href="{{ asset('vendor') }}/vue/bootstrap-vue.css" rel="stylesheet" />
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
    </script>
    @stack('topcss')
</head>
{{-- 
<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true"
    data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true"
    data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true"
    data-kt-app-sidebar-push-footer="true" class="app-default"> --}}

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-sidebar-minimize="on" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" class="app-default">

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
                    <div class="d-flex flex-column flex-column-fluid"> {{-- PADING-8 --}}
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-fluid">
                                {{ session('first_login') }}
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
    </div>

    @guest()
    @endguest
    @yield('topjs')

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebarToggle = document.getElementById('kt_app_sidebar_toggle');
            const sidebarLogo = document.querySelector('.app-sidebar-logo');
            const sidebarLogoMinimize = document.querySelector('.sidebar-logo-minimize');
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
    <script src="{{ asset('vendor') }}/jquery/jquery.min.js" type="text/javascript"></script>
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
    <script src="{{ asset('vendor') }}/select2/select2.min.js"></script>
    <!-- Custom JS defined by admin -->
    <script src="{{ asset('byadmin') }}/back.js"></script>
    <!-- Import Moment -->
    <script type="text/javascript" src="{{ asset('vendor') }}/moment/moment.min.js"></script>
    <script type="text/javascript" src="{{ asset('vendor') }}/moment/momenttz.min.js"></script>
    <script src="{{ asset('vendor/argon') }}/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- Import Vue -->
    <script src="{{ asset('vendor') }}/vue/vue.js"></script>
    <script src="{{ asset('vendor') }}/vue/bootstrap-vue.min.js"></script>
    <!-- Import AXIOS --->
    <script src="{{ asset('vendor') }}/axios/axios.min.js"></script>
    <?php
    echo file_get_contents(base_path('public/byadmin/back.js'));
    ?>
    <script>
        var hostUrl = "assets/";
    </script>
    <script src="{{ asset('Metronic/assets') }}/js/scripts.bundle.js"></script>
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

    @include('client.layouts.intercom')
</body>

</html>
