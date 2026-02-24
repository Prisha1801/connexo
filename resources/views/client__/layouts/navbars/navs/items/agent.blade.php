<div class="app-navbar-item ms-1 ms-md-3">
    <!--begin::Menu- wrapper-->
    <a href="{{ route('agent.index') }}"
        class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-light menu-link w-35px h-35px w-md-40px h-md-40px @if (Route::currentRouteName() == 'agent.index' ||
                Route::currentRouteName() == 'agent.create' ||
                Route::currentRouteName() == 'agent.edit') active @endif"
        data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
        data-kt-menu-placement="bottom-end">
        {{-- <i class="ki-outline ki-calendar fs-1"></i> --}}
        <i class="ki-duotone ki-people fs-2 text-white">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
            <span class="path4"></span>
            <span class="path5"></span>
        </i>
    </a>
</div>
