<div id="kt_app_sidebar" class="app-sidebar  flex-column border border-1" data-kt-drawer="true"
    data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
    data-kt-drawer-width="250px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    @include('client.partials.sidebar-header')
    <div class="app-sidebar-menu flex-column-fluid mt-4" id="kt_app_sidebar_navs">
        <div id="kt_app_sidebar_navs_wrappers" class="app-sidebar-wrapper hover-scroll-y my-2" data-kt-scroll="true"
            data-kt-scroll-activate="true" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_app_sidebar_header" data-kt-scroll-wrappers="#kt_app_sidebar_navs"
            data-kt-scroll-offset="5px">
            <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false"
                class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary">

                @if (Auth::user()->isImpersonating())
                    <div class="menu-item" data-bs-toggle="tooltip" data-bs-placement="right">
                        <a class="menu-link " href="{{ route('owner.stopImpersonate') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-left fs-2 text-danger"></i>
                            </span>
                            <span class="menu-title text-danger">{{ __('Back Owner') }}</span>
                        </a>
                    </div>
                @endif

                <div class="menu-item" data-bs-toggle="tooltip" data-bs-placement="right">
                    <a class="menu-link @if (Route::currentRouteName() == 'dashboard') active @endif"
                        href="{{ route('dashboard') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-abstract-36 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">{{ __('Dashboard') }}</span>
                    </a>
                </div>
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">
                            {{ __("FUNCTIONS") }}
                        </span>
                    </div>
                </div>
                <div class="menu-item">
                    <a class="menu-link @if (Route::currentRouteName() == 'chat.index') active @endif"
                        href="{{ route('chat.index') }}">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-messages fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                                <span class="path5"></span>
                            </i>
                        </span>
                        <span class="menu-title">{{ __("Conversations") }}</span>
                    </a>
                </div>
                @if (auth()->user()->hasrole(['staff']))
                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'campaigns.index') active @endif"
                            href="{{ route('campaigns.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-add-notepad fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Campaings") }}</span>
                        </a>
                    </div>
                @endif

                @if (!auth()->user()->hasrole(['staff']))
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion @if (Route::currentRouteName() == 'campaigns.index' ||
                                Route::currentRouteName() == 'campaigns.create' ||
                                (Route::currentRouteName() == 'wpbox.api.index' && Request::query('type') == 'api')) show @endif">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-add-notepad fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">
                                {{ __("Campaings") }}
                            </span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion @if (Route::currentRouteName() == 'campaigns.index' ||
                                Route::currentRouteName() == 'campaigns.create' ||
                                (Route::currentRouteName() == 'wpbox.api.index' && Request::query('type') == 'api')) show @endif"
                            @if (Route::currentRouteName() == 'campaigns.index' ||
                                    Route::currentRouteName() == 'campaigns.create' ||
                                    (Route::currentRouteName() == 'wpbox.api.index' && Request::query('type') == 'api')) @else style="display: none; overflow: hidden;" @endif
                            kt-hidden-height="251">
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'campaigns.index' && !Request::is('campaigns/create')) active @endif"
                                    href="{{ route('campaigns.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __("Show Campaigns") }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'campaigns.create') ) active @endif"
                                    href="{{ route('campaigns.create') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __("Create Campaign") }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'wpbox.api.index' && Request::query('type') == 'api') ) active @endif"
                                    href="{{ route('wpbox.api.index', ['type' => 'api']) }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __("API Campaign") }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion @if (Route::currentRouteName() == 'contacts.index' ||
                                Route::currentRouteName() == 'contacts.fields.index' ||
                                Route::currentRouteName() == 'contacts.groups.index') show @endif">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-badge fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">
                                {{ __('Contacts') }}
                            </span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion @if (Route::currentRouteName() == 'contacts.index' ||
                                Route::currentRouteName() == 'contacts.fields.index' ||
                                Route::currentRouteName() == 'contacts.groups.index') show @endif"
                            @if (Route::currentRouteName() == 'contacts.index' ||
                                    Route::currentRouteName() == 'contacts.fields.index' ||
                                    Route::currentRouteName() == 'contacts.groups.index') @else style="display: none; overflow: hidden;" @endif
                            kt-hidden-height="251">
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'contacts.index') active @endif"
                                    href="{{ route('contacts.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __("Contact Management") }}
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'contacts.fields.index') active @endif"
                                    href="{{ route('contacts.fields.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Fields') }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'contacts.groups.index') active @endif"
                                    href="{{ route('contacts.groups.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Lists') }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'agent.index') active @endif"
                            href="{{ route('agent.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-people fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Agents") }}</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'templates.index') active @endif"
                            href="{{ route('templates.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-paintbucket fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __('Templates') }}</span>
                        </a>
                    </div>
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion @if (Route::currentRouteName() == 'flowisebots.index' ||
                                Route::currentRouteName() == 'replies.index' ||
                                Route::currentRouteName() == 'flows.index' ||
                                (Request::is('replies') && Request::query('type') == 'qr')) show @endif">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-technology-2 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">
                                {{ __("Automation") }}
                            </span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion @if (Route::currentRouteName() == 'flowisebots.indexcompany' ||
                                Route::currentRouteName() == 'replies.index' ||
                                Route::currentRouteName() == 'flows.index' ||
                                Route::currentRouteName() == 'whatsapp-flows.index' ||
                                (Request::is('replies') && Request::query('type') == 'qr')) show @endif"
                            @if (Route::currentRouteName() == 'flowisebots.indexcompany' ||
                                    Route::currentRouteName() == 'replies.index' ||
                                    Route::currentRouteName() == 'flows.index' ||
                                    Route::currentRouteName() == 'whatsapp-flows.index' ||
                                    (Request::is('replies') && Request::query('type') == 'qr')) @else style="display: none; overflow: hidden;" @endif
                            kt-hidden-height="251">
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'flowisebots.indexcompany') active @endif"
                                    href="{{ route('flowisebots.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __("AI Bot") }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'replies.index' && !Request::query('type')) active @endif"
                                    href="{{ route('replies.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Template Bot') }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'whatsapp-flows.index' && !Request::query('flows')) active @endif"
                                    href="{{ route('whatsapp-flows.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('WhatsApp Flows') }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Route::currentRouteName() == 'flows.index') active @endif"
                                    href="{{ route('flows.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Flow Bot') }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Request::is('replies') && Request::query('type') == 'qr') active @endif"
                                    href="{{ url('replies?type=qr') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Quick Replies') }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">
                                {{ __("TOOLS") }}
                            </span>
                        </div>
                    </div>
                    <div data-kt-menu-trigger="click"
                        class="menu-item menu-accordion @if (Route::currentRouteName() == 'reminders.reservations.index' ||
                                Route::currentRouteName() == 'reminders.reminders.index' ||
                                Route::currentRouteName() == 'reminders.sources.index') show @endif">
                        <span class="menu-link">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-calendar-8 fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                    <span class="path6"></span>
                                </i>
                            </span>
                            <span class="menu-title">
                                {{ __('Reminders') }}
                            </span>
                            <span class="menu-arrow"></span>
                        </span>
                        <div class="menu-sub menu-sub-accordion @if (Route::currentRouteName() == 'reminders.reservations.index' ||
                                Route::currentRouteName() == 'reminders.reminders.index' ||
                                Route::currentRouteName() == 'reminders.sources.index') show @endif"
                            @if (Route::currentRouteName() == 'reminders.reservations.index' ||
                                    Route::currentRouteName() == 'reminders.reminders.index' ||
                                    Route::currentRouteName() == 'reminders.sources.index') @else style="display: none; overflow: hidden;" @endif
                            kt-hidden-height="251">
                            <div class="menu-item">
                                <a class="menu-link @if (Request::is('reminders/reservations')) active @endif"
                                    href="{{ route('reminders.reservations.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Reservations') }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Request::is('reminders/reminders')) active @endif"
                                    href="{{ route('reminders.reminders.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Reminders') }}
                                    </span>
                                </a>
                            </div>
                            <div class="menu-item">
                                <a class="menu-link @if (Request::is('reminders/sources')) active @endif"
                                    href="{{ route('reminders.sources.index') }}">
                                    <span class="menu-bullet">
                                        <span class="bullet bullet-dot"></span>
                                    </span>
                                    <span class="menu-title">
                                        {{ __('Sources') }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'embedwhatsapp.edit') active @endif"
                            href="{{ route('embedwhatsapp.edit') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-message-minus fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Widget Generator") }}</span>
                        </a>
                    </div>
                    <div class="menu-item pt-5">
                        <div class="menu-content">
                            <span class="menu-heading fw-bold text-uppercase fs-7">
                                {{ __("CONFIGURATION") }}
                            </span>
                        </div>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'available.plans') active @endif"
                            href="{{ route('available.plans') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-credit-cart fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Plans") }}</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'admin.apps.company') active @endif"
                            href="{{ route('admin.apps.company') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-element-plus fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Apps") }}</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'account.profile.api') active @endif"
                            href="{{ route('account.profile.api') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-message-programming fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                    <span class="path4"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Apis") }}</span>
                        </a>
                    </div>
                    {{-- <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'profile.show') active @endif"
                            href="{{ route('profile.show') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-profile-circle fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Account") }}</span>
                        </a>
                    </div>

                    <div class="menu-item">
                        <a class="menu-link @if (Route::currentRouteName() == 'referrals.index') active @endif"
                            href="{{ route('referrals.index') }}">
                            <span class="menu-icon">
                                <i class="ki-duotone ki-dollar fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                    <span class="path3"></span>
                                </i>
                            </span>
                            <span class="menu-title">{{ __("Referrals") }}</span>
                        </a>
                    </div> --}}
                @endif
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">
                            {{ __('HELP') }}
                        </span>
                    </div>
                </div>
                <div class="menu-item">
                    <a href="#" class="menu-link" data-bs-toggle="modal" data-bs-target="#kt_modal_offer_a_deal">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-setting-2 fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">wizard</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="https://support.dotflo.io/es/" target="_blank">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-book fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">{{ __('Documentation') }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="#" target="_blank">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-text-align-justify-center fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </span>
                        <span class="menu-title">{{ __('Open Ticket') }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="#" onclick="window.Intercom('show');">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-security-user fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </span>
                        <span class="menu-title">{{ __('Talk to agent') }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="#">
                        <span class="menu-title"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
