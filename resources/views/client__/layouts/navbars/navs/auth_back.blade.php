<div class="app-navbar flex-lg-grow-1" id="kt_app_header_navbar">
    <div class="app-navbar-item d-flex align-items-stretch flex-lg-grow-1">
    </div>
    @if (auth()->user()->hasRole('owner') || auth()->user()->hasRole('staff'))
        @if (auth()->user()->hasRole('owner'))
            <?php $urlToVendor = auth()->user()->companies()->get()->first()->getLinkAttribute(); ?>
        @endif
        @if (auth()->user()->hasRole('staff'))
            <?php $urlToVendor = auth()->user()->company->getLinkAttribute(); ?>
        @endif
        @if (config('settings.show_company_page', true))
            <a href="{{ $urlToVendor }}" target="_blank" class="nav-link" role="button">
                <i class="ni ni-shop"></i>
                <span class="nav-link-inner--text bold">{{ __(config('settings.vendor_entity_name')) }}</span>
            </a>
        @endif
    @endif
    <?php
    $availableLanguagesENV = config('settings.front_languages');
    $exploded = explode(',', $availableLanguagesENV);
    $availableLanguages = [];
    for ($i = 0; $i < count($exploded); $i += 2) {
        $availableLanguages[$exploded[$i]] = $exploded[$i + 1];
    }
    $locale = isset($locale) ? $locale : (Cookie::get('lang') ? Cookie::get('lang') : config('settings.app_locale'));
    ?>
    @if (!auth()->user()->hasrole(['staff']) && auth()->user()->type == 3)
        @include('client.layouts.navbars.navs.items.partner-dashboard')
    @endif
    @include('client.layouts.navbars.navs.items.help')
    @if (!auth()->user()->hasrole(['staff']))
        @include('client.layouts.navbars.navs.items.campaign')
        @include('client.layouts.navbars.navs.items.contact')
    @endif
    {{-- @include('client.layouts.navbars.navs.items.notifications') --}}
    @php
        if (auth()->user()->profile_photo_path != '') {
            // Si el avatar existe, se utiliza
            $avatar = auth()->user()->profile_photo_path;
        } else {
            // Si no existe, se utiliza el avatar por defecto
            $avatar = asset('Metronic/assets/media/avatars/blank.png');
        }
    @endphp
    <div class="app-navbar-item ms-1 ms-md-3" id="kt_header_user_menu_toggle">
        <div class="cursor-pointer symbol symbol-40px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <img src="{{ $avatar }}" alt="user" />
        </div>

        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-350px"
            data-kt-menu="true">
            <div class="menu-item px-3">
                <div class="menu-content d-flex align-items-center px-3">
                    <div class="symbol symbol-50px me-5">
                        <img alt="Logo" src="{{ $avatar }}" />
                    </div>
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-5">{{ auth()->user()->name }}</div>
                        <a href="#"
                            class="fw-semibold text-muted text-hover-primary fs-7">{{ auth()->user()->email }}</a>
                    </div>
                </div>
            </div>
            <div class="separator my-2"></div>
            <div class="menu-item px-5">
                <a href="{{ route('account.profile.show') }}" class="menu-link px-5">{{ __('My profile') }}</a>
            </div>
            @if (isset($availableLanguages) && count($availableLanguages) > 1 && isset($locale))
                <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                    data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                    <a href="#" class="menu-link px-5">
                        @foreach ($availableLanguages as $short => $lang)
                            @if (strtolower($short) == strtolower($locale))
                                <span class="menu-title position-relative">{{ __('Language') }}
                                    <span
                                        class="fs-8 rounded bg-light px-3 py-2 position-absolute translate-middle-y top-50 end-0">{{ __($lang) }}
                                        @if ($short == 'EN')
                                            <img class="w-15px h-15px rounded-1 ms-2"
                                                src="{{ asset('Metronic/assets') }}/media/flags/united-states.svg"
                                                alt="" />
                                        @else
                                            <img class="w-15px h-15px rounded-1 ms-2"
                                                src="{{ asset('Metronic/assets') }}/media/flags/spain.svg"
                                                alt="" />
                                        @endif
                                    </span>
                                </span>
                            @endif
                        @endforeach
                    </a>
                    <div class="menu-sub menu-sub-dropdown w-175px py-4">
                        @foreach ($availableLanguages as $short => $lang)
                            <div class="menu-item px-3">
                                <a href="{{ route('home', $short) }}" class="menu-link d-flex px-5">
                                    <span class="symbol symbol-20px me-4">
                                        @if ($short == 'EN')
                                            <img class="rounded-1"
                                                src="{{ asset('Metronic/assets') }}/media/flags/united-states.svg"
                                                alt="" />
                                        @else
                                            <img class="rounded-1"
                                                src="{{ asset('Metronic/assets') }}/media/flags/spain.svg"
                                                alt="" />
                                        @endif
                                    </span>{{ __($lang) }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                <a href="#" class="menu-link px-5">
                    <span class="menu-title position-relative">{{ __('Mode') }}
                        <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                            <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                            <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                        </span></span>
                </a>
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                    data-kt-menu="true" data-kt-element="theme-mode-menu">
                    <div class="menu-item px-3 my-0">
                        <a id="light-mode" href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                            data-kt-value="light">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-night-day fs-2"></i>
                            </span>
                            <span class="menu-title">Light</span>
                        </a>
                    </div>
                    <div class="menu-item px-3 my-0">
                        <a id="dark-mode" href="#" class="menu-link px-3 py-2" data-kt-element="mode"
                            data-kt-value="dark">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-moon fs-2"></i>
                            </span>
                            <span class="menu-title">Dark</span>
                        </a>
                    </div>
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-screen fs-2"></i>
                            </span>
                            <span class="menu-title">System</span>
                        </a>
                    </div>
                </div>
            </div>
            @if (config('settings.app_code_name', '') == 'wpbox' && auth()->user()->hasRole('owner'))
                <div class="menu-item px-5">
                    <a href="{{ route('whatsapp.setup') }}"
                        class="menu-link px-5">{{ __('Whatsapp Cloud API Setup') }}</a>
                </div>
            @endif
            @if (!auth()->user()->hasrole(['staff']))
                <div class="menu-item px-5">
                    <a href="{{ route('account.profile.billing') }}"
                        class="menu-link px-5">{{ __('Billing Settings') }}</a>
                </div>
                <div class="menu-item px-5">
                    {{-- <a href="{{ route('referrals.index') }}" class="menu-link px-5">{{ __('Referrals') }}</a> --}}
                </div>
            @endif
            <div class="separator my-2 mt-6"></div>
            <div class="menu-item px-5">
                <div class="d-flex m-4">
                    <div class="flex-1 d-flex justify-content-center">
                        <div>
                            <img src="https://img.icons8.com/color/48/qr-code--v1.png"
                                alt="QR">
                        </div>
                    </div>
                    <div class="flex-grow-1 d-flex flex-stack ms-4">
                        <div class=" fw-semibold">
                            <h4 class="text-gray-900 fs-6 fw-bold">Download the mobile app</h4>
                            <div class="fs-9 text-gray-700 ">
                                Respond to your customers from your mobile device, and never miss
                                a single business deal.
                            </div>
                            <div class="ms--4 mt-2 d-flex justify-content-center">
                                <a href="#" target="_blank" class="me-2">
                                    <i class="ki-duotone ki-apple fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                                <a href="#" target="_blank" class="">
                                    <i class="ki-duotone ki-google-play fs-1">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="separator my-2"></div>
            <div class="menu-item px-5">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="menu-link px-5">{{ __('Logout') }}</a>
            </div>
        </div>
    </div>
</div>
