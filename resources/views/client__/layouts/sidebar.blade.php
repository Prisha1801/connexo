{{-- Dynamic Module Menus --}}
@foreach(auth()->user()->getExtraMenus() as $menu)

    @if(isset($menu['isGroup']) && $menu['isGroup'])

        {{-- Group Menu --}}
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
             data-kt-menu-placement="right-start"
             class="menu-item py-2
             @if(
                request()->routeIs($menu['route']) ||
                (isset($menu['menus']) && collect($menu['menus'])->pluck('route')->contains(Route::currentRouteName()))
             ) show here @endif">

            {{-- Main menu icon --}}
            <span class="menu-link menu-center">
                <span class="menu-icon-title">
                    <span class="menu-icon me-0 mt-4" style="color: var(--theme-icon-color);">
                        <i class="{{ $menu['icon'] }} fs-2"></i>
                    </span>
                    <span class="menu-icon-title mt-1">{{ __($menu['name']) }}</span>
                </span>
            </span>

            {{-- Submenu --}}
            <div class="bg-dark menu-sub menu-sub-dropdown menu-sub-indention px-2 py-4 w-250px mh-75 overflow-auto">

                <div class="menu-item">
                    <div class="menu-content">
                        <span class="menu-section text-white fs-5 fw-bolder ps-1 py-1">
                            {{ __($menu['name']) }}
                        </span>
                    </div>
                </div>

                @foreach($menu['menus'] as $submenu)

                    <div class="menu-item">

                        <a class="menu-link link-wb-active
                           @if(request()->routeIs($submenu['route'])) active @endif"

                           href="{{ route($submenu['route'],
                                isset($submenu['params']) && is_array($submenu['params'])
                                ? $submenu['params'] : []) }}">

                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>

                            <span class="menu-title">
                                {{ __($submenu['name']) }}
                            </span>

                        </a>

                    </div>

                @endforeach

            </div>

        </div>

    @else

        {{-- Single Menu --}}
        <a href="{{ route($menu['route']) }}"
           class="menu-item py-2 @if(request()->routeIs($menu['route'])) here @endif">

            <span class="menu-link menu-center">
                <span class="menu-icon-title">

                    <span class="menu-icon me-0 mt-4">
                        <i class="{{ $menu['icon'] }} fs-2"></i>
                    </span>

                    <span class="menu-icon-title mt-1">
                        {{ __($menu['name']) }}
                    </span>

                </span>
            </span>

        </a>

    @endif

@endforeach
