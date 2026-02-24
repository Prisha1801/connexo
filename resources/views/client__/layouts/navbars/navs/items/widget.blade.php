<!--begin::widget-->
<div class="app-navbar-item ms-1 ms-md-3">
    <!--begin::Menu- wrapper-->
    <a class="menu-link" href="{{ route('embedwhatsapp.edit') }}">
        <div class="btn btn-icon btn-custom btn-color-gray-600 btn-active-color-light w-35px h-35px w-md-40px h-md-40px @if (Route::currentRouteName() == 'embedwhatsapp.edit') active @endif"
            data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end">
            {{-- <i class="ki-outline ki-calendar fs-1"></i> --}}
            <i class="ki-duotone ki-abstract-36 fs-2 text-white"><span class="path1"></span><span
                    class="path2"></span></i>
        </div>
    </a>
</div>
<!--end::widget-->
