<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

	<head>

        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Site') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />

        <!-- Icons -->
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

        @yield('head')

        @include('layouts.rtl')

		<link href="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{{ asset('Metronic/assets') }}/css/style.bundle.css" rel="stylesheet" type="text/css" />
		<link href="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />

        <link rel="canonical" href="https://preview.keenthemes.com/html/metronic/docs/forms/dropzonejs"/>

        <link href="{{ asset('vendor/argon') }}/vendor/nucleo/css/nucleo.css" rel="stylesheet">
        <!--  <link type="text/css" href="{{ asset('vendor/argon') }}/css/argon.css?v=1.0.0" rel="stylesheet">-->
        <link rel="stylesheet" href="{{ asset('vendor') }}/jasny/css/jasny-bootstrap.min.css">

        <!-- Custom CSS defined by admin -->
        <link type="text/css" href="{{ asset('byadmin') }}/back.css" rel="stylesheet">

        <!-- Select2  -->
        <link type="text/css" href="{{ asset('vendor') }}/select2/select2.min.css" rel="stylesheet">

        <!-- Custom CSS defined by user -->
        <link type="text/css" href="{{ asset('custom') }}/css/custom.css?id={{ config('version.version')}}" rel="stylesheet">

        <!-- Flags -->
        <link type="text/css" href="{{ asset('vendor') }}/flag-icons/css/flag-icons.min.css" rel="stylesheet" />

        <!-- Bootstap VUE -->
        <link type="text/css" href="{{ asset('vendor') }}/vue/bootstrap-vue.css" rel="stylesheet" />
        <script>// Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }</script>
	</head>
	<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-sidebar-minimize="on" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" class="app-default">
        <!--begin::Theme mode setup on page load-->
		<script>var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-bs-theme-mode")) { themeMode = document.documentElement.getAttribute("data-bs-theme-mode"); } else { if ( localStorage.getItem("data-bs-theme") !== null ) { themeMode = localStorage.getItem("data-bs-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-bs-theme", themeMode); }</script>
		<!--end::Theme mode setup on page load-->
		<!--begin::App-->
		<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
			<!--begin::Page-->
			<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
				<!--begin::Header-->
				@include('client.layouts.header')
				<!--end::Header-->
				<!--begin::Wrapper-->
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">

					<!--begin::Sidebar-->
                    @auth()
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        @include('client.layouts.sidebar')
                    @endauth
					<!--end::Sidebar-->

					<!--begin::Main-->
					<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
						<!--begin::Content wrapper-->
						<div class="d-flex flex-column flex-column-fluid">
							<!--begin::Toolbar-->
                            @include('client.layouts.toolbar')
							<!--end::Toolbar-->
							<!--begin::Content-->
							<div id="kt_app_content" class="app-content flex-column-fluid">
								<!--begin::Content container-->
								<div id="kt_app_content_container" class="app-container container-fluid">

                                    @yield('content')

								</div>
								<!--end::Content container-->
							</div>
							<!--end::Content-->
						</div>
						<!--end::Content wrapper-->
						<!--begin::Footer-->
						@include('client.layouts.footer')
							<!--end::Footer container-->
						</div>
						<!--end::Footer-->
					</div>
					<!--end:::Main-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>
		<!--end::App-->

        @guest()
        @endguest
        @livewireScripts
        <!-- Commented because navtabs includes same script -->
        @yield('topjs')

            <script>
                var t="<?php echo 'translations'.App::getLocale() ?>";
               window.translations = {!! Cache::get('translations'.App::getLocale(),"[]") !!};


            </script>

            <!-- Navtabs -->
            <script src="{{ asset('vendor') }}/jquery/jquery.min.js" type="text/javascript"></script>
            <script src="{{ asset('vendor/argon') }}/js/popper.min.js" type="text/javascript"></script>


            <script src="{{ asset('vendor/argon') }}/vendor/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

            <!-- Nouslider -->
            <script src="{{ asset('vendor/argon') }}/vendor/nouislider/distribute/nouislider.min.js" type="text/javascript"></script>

            <!-- Latest compiled and minified JavaScript -->
            <script src="{{ asset('vendor') }}/jasny/js/jasny-bootstrap.min.js"></script>


            <!-- All in one -->
            <script src="{{ asset('custom') }}/js/js.js?id={{ config('version.version')}}"></script>

            <!-- Notify JS -->
            <script src="{{ asset('custom') }}/js/notify.min.js"></script>

            <!-- Argon JS -->
            <script src="{{ asset('vendor/argon') }}/js/argon.js?v=1.0.0"></script>



            <script>
                var ONESIGNAL_APP_ID = "{{ config('settings.onesignal_app_id') }}";
                var USER_ID = '{{  auth()->user()&&auth()->user()?auth()->user()->id:"" }}';
                var PUSHER_APP_KEY = "{{ config('broadcasting.connections.pusher.key') }}";

                var PUSHER_APP_CLUSTER = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
            </script>
            @if (auth()->user()!=null&&auth()->user()->hasRole('staff'))
                <script>
                    //When staff, use the owner
                    USER_ID = '{{ auth()->user()->company->user_id }}';
                </script>
            @endif


            <!-- OneSignal -->
            @if(strlen( config('settings.onesignal_app_id'))>4)
                <script src="{{ asset('vendor') }}/OneSignalSDK/OneSignalSDK.js" async=""></script>
                <script src="{{ asset('custom') }}/js/onesignal.js"></script>
            @endif

            @stack('js')
            @yield('js')



             <!-- Pusher -->
             @if(strlen(config('broadcasting.connections.pusher.app_id'))>2)
                <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
                @if (config('settings.app_code_name','')=="qrpdf")
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
                echo file_get_contents(base_path('public/byadmin/back.js'))
            ?>

		<!--begin::Javascript-->
		<script>var hostUrl = "assets/";</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)
		<script src="{{ asset('Metronic/assets') }}/plugins/global/plugins.bundle.js"></script>-->
		<script src="{{ asset('Metronic/assets') }}/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Vendors Javascript(used for this page only)-->
		<script src="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.js"></script>

		<!--end::Vendors Javascript-->
		<!--begin::Custom Javascript(used for this page only)-->
		<script src="{{ asset('Metronic/assets') }}/js/widgets.bundle.js"></script>
		<script src="{{ asset('Metronic/assets') }}/js/custom/widgets.js"></script>
		<script src="{{ asset('Metronic/assets') }}/js/custom/apps/chat/chat.js"></script>
		<script src="{{ asset('Metronic/assets') }}/js/custom/utilities/modals/upgrade-plan.js"></script>
		<script src="{{ asset('Metronic/assets') }}/js/custom/utilities/modals/create-campaign.js"></script>
		<script src="{{ asset('Metronic/assets') }}/js/custom/utilities/modals/users-search.js"></script>
        <script src="{{ asset('Metronic/assets') }}/plugins/custom/datatables/datatables.bundle.js"></script>
		<script src="{{ asset('Metronic/assets') }}/js/custom/apps/ecommerce/catalog/products.js"></script>
        <script src="{{ asset('Metronic/assets') }}/js/custom/apps/ecommerce/catalog/save-product.js"></script>

        <script src="{{ asset('Metronic/assets') }}/js/custom/apexcharts"></script>

        <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>

        <!-- Popperjs -->
        <script src="{{ asset('Metronic/assets') }}/js/custom/popper.min.js" crossorigin="anonymous"></script>
        <!-- Stack specifics scripts from view -->
        <!--end::Custom Javascript-->
		<!--end::Javascript-->

        <!-- Modales específicos de la página -->

	</body>
	<!--end::Body-->
</html>
