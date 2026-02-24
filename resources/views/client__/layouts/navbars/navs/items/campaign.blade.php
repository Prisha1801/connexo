    <!--begin::campaign-->
    <div class="app-navbar-item ms-1 ms-md-3">
        <!--begin::Menu- wrapper-->
        <a class="menu-link" href="{{route('campaigns.create')}}">
            <div class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-light w-35px h-35px w-md-40px h-md-40px @if (Route::currentRouteName() == 'campaigns.create') active @endif" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                {{-- <i class="ki-outline ki-calendar fs-1"></i> --}}
                <i class="ki-outline ki-add-notepad fs-3 text-white">
                </i>
            </div>
        </a>
    </div>
    <!--end::campaign-->
