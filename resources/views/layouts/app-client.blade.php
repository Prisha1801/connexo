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

    <!-- Metronic CSS -->
    <link href="{{ asset('metronic56/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('metronic56/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    
    <!-- DataTables CSS -->
    <link href="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('vendor') }}/jasny/css/jasny-bootstrap.min.css">
    <link type="text/css" href="{{ asset('byadmin') }}/back.css" rel="stylesheet">
    
    <!-- Flags -->
    <link type="text/css" href="{{ asset('vendor') }}/flag-icons/css/flag-icons.min.css" rel="stylesheet" />
    
    <!-- Bootstrap VUE -->
    <link type="text/css" href="{{ asset('vendor') }}/vue/bootstrap-vue.css" rel="stylesheet" />
    
    @stack('topcss')
    @yield('css')
    
    <script type="text/javascript">
        var filemanager_request = "";
        var imagetopreview = "";
        var STORAGE_TYPE = "{{ env('STORAGE_TYPE') }}";
        var PATH = "{{ url('') . '/' }}";
        var csrf = "{{ csrf_token() }}";
        var FORMAT_DATE = 'dd/mm/yy';
        var FORMAT_DATETIME = ["dd/mm/yy", "hh:mm TT"];
    </script>
    
    <!-- Metronic JS -->
    <script src="{{ asset('metronic56/assets/js/scripts.bundle.js') }}"></script>
    <script src="{{ asset('metronic56/assets/plugins/global/plugins.bundle.js') }}"></script>
</head>

<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-sidebar-minimize="on"
    data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true"
    data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true"
    class="app-default">

    @include('client.partials.theme')
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            @include('client.layouts.header')
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                @auth()
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endauth
                @include('client.layouts.sidebar')
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_content" class="app-content flex-column-fluid">
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

    @yield('topjs')
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Initialize any required scripts here
        });
    </script>
    
    @stack('scripts')
    @yield('js')
</body>
</html>
