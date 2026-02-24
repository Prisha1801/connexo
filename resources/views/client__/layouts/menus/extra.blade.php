{{-- @php
    $ExtraMenus = auth()->user()->getExtraMenus();
@endphp
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Chat')
        <div class="menu-item">
            <a class="menu-link @if (Route::currentRouteName() == 'chat.index') active @endif"
                href="{{ route($menu['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-messages fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
            </a>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Campaigns')
        <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-add-notepad fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link" href="{{ route($menu['menus']['0']['route']) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['0']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route($menu['menus']['1']['route']) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['1']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('wpbox.api.index', ['type' => 'api']) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['2']['name']) }}</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Reminders')
        <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-calendar-8 fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link" href="{{ route($menu['menus']['0']['route']) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['0']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route($menu['menus']['1']['route']) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['1']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route($menu['menus']['2']['route']) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['2']['name']) }}</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Agents')
        <div class="menu-item" data-bs-toggle="tooltip" data-bs-placement="right">
            <a class="menu-link @if (Route::currentRouteName() == 'agent.index') active @endif"
                href="{{ route($menu['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-people fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
            </a>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Web Widget')
        <div class="menu-item" data-bs-toggle="tooltip" data-bs-placement="right">
            <a class="menu-link @if (Route::currentRouteName() == 'widget.index') active @endif"
                href="{{ route($menu['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                <span class="menu-icon">
                    <i class="ki-duotone ki-message-minus fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
            </a>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Contacts')

        <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
            <span class="menu-link @if (Route::currentRouteName() == 'contacts.index') active @endif">
                <span class="menu-icon">
                    <i class="ki-duotone ki-badge fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route('contacts.index')}}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __('Contact Management') }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('contacts.groups.index') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __('Groups') }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route('contacts.fields.index')}}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __('Fields') }}</span>
                    </a>
                </div>

            </div>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Templates')

        <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-paintbucket fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route($menu['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['1']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route($menu['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['0']['name']) }}</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Bots')
        <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-technology-2 fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __('Automation') }}</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('flowisebots.index') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ 'AI bots' }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="/replies">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ 'Template bots' }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="{{ route('flows.index') }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ 'Flow Bots' }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link" href="/replies?type=qr">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ 'Quick Replies' }}</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Ecommerce')
        <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <i class="ki-duotone ki-basket-ok fs-2" >
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion">
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route($menu['menus']['0']['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['0']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route($menu['menus']['1']['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['1']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route($menu['menus']['2']['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['2']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route($menu['menus']['3']['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['3']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route($menu['menus']['4']['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['4']['name']) }}</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link"
                        href="{{ route($menu['menus']['5']['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">{{ __($menu['menus']['5']['name']) }}</span>
                    </a>
                </div>
            </div>
        </div>
    @endif
@endforeach
@foreach ($ExtraMenus as $menu)
    @if ($menu['name'] == 'Referrals')
        <div class="menu-item" data-bs-toggle="tooltip" data-bs-placement="right">
            <a class="menu-link @if (Route::currentRouteName() == 'referrals.index') active @endif"
                href="{{ route($menu['route'], isset($menu['params']) ? $menu['params'] : []) }}">
                <span class="menu-icon">
                    <i class="bi bi-person-lines-fill fs-2" style="color: #00394f !important">
                        <span class="path1"></span>
                        <span class="path2"></span>
                        <span class="path3"></span>
                        <span class="path4"></span>
                        <span class="path5"></span>
                    </i>
                </span>
                <span class="menu-title">{{ __($menu['name']) }}</span>
            </a>
        </div>
    @endif
@endforeach --}}
