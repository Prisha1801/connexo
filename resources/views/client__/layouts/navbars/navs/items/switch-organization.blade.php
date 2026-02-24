<div class="app-navbar-item ms-1 ms-md-3">
    <!--begin::Menu- wrapper-->
    <a href="{{route('admin.organizations.manage')}}"  class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-light menu-link w-35px h-35px w-md-40px h-md-40px @if (Route::currentRouteName() == 'admin.organizations.manage' || Route::currentRouteName() == 'admin.organizations.edit') active @endif"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end">
        {{-- <i class="ki-outline ki-calendar fs-1"></i> --}}
        <i class="ki-outline ki-switch fs-3 text-white">
        </i>
    </a>
</div>
