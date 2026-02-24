@extends('layouts.app-client')

@section('content')
    @yield('customheading')

    <div class="col-12">
        @include('partials.flash-client')
    </div>

    @isset($title)
        <div class="container-xxl card card-flush mb-10">
            <div
                class="card-header align-items-center py-5 gap-4 flex-wrap flex-md-nowrap bg-light rounded-top shadow-sm border-bottom mb-4">
                <!-- Title Block -->
                <div class="card-title flex-grow-1">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
                        <h3 class="mb-0 d-flex align-items-center text-dark fw-bold fs-2">
                            <i class="ki-duotone ki-document fs-2x me-2 text-primary"></i>
                            {{ __($title) }}
                        </h3>
                        @isset($subtitle)
                            <span class="text-muted fs-6">{{ $subtitle }}</span>
                        @endisset
                    </div>
                </div>

                <!-- Toolbar for Desktop -->
                <div class="card-toolbar d-none d-lg-flex gap-2 flex-wrap justify-content-end">
                    @isset($action_link)
                        <a href="{{ $action_link }}" class="btn btn-sm btn-primary">
                            {!! $action_icon !!} {{ __($action_name) }}
                        </a>
                    @endisset
                    @isset($action_link2)
                        <a href="{{ $action_link2 }}" class="btn btn-sm btn-light-info">
                            {!! $action_icon2 !!} {{ __($action_name2) }}
                        </a>
                    @endisset
                    @isset($action_link3)
                        <a href="{{ $action_link3 }}" class="btn btn-sm btn-light-warning">
                            {!! $action_icon3 !!} {{ __($action_name3) }}
                        </a>
                    @endisset
                    @isset($action_link4)
                        <a href="{{ $action_link4 }}" class="btn btn-sm btn-light-danger">
                            {!! $action_icon4 !!} {{ __($action_name4) }}
                        </a>
                    @endisset
                    @isset($action_link5)
                        <a target="_blank" href="{{ $action_link5 }}" class="btn btn-sm btn-light-dark">
                            {!! $action_icon5 !!} {{ __($action_name5) }}
                        </a>
                    @endisset
                    @isset($usefilter)
                        <button id="show-hide-filters" class="btn btn-icon btn-outline btn-outline-secondary" type="button">
                            <i class="ki-duotone ki-setting-3 fs-2"></i>
                        </button>
                    @endisset
                </div>

                <!-- Toolbar Dropdown for Mobile -->
                <div class="card-toolbar d-flex d-lg-none justify-content-end">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-icon btn-light-primary" data-bs-toggle="dropdown">
                            <i class="ki-duotone ki-category fs-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                                <span class="path3"></span>
                                <span class="path4"></span>
                            </i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end px-3 py-2 w-200px">
                            @isset($action_link)
                                <a href="{{ $action_link }}" class="dropdown-item">
                                    {!! $action_icon !!} {{ __($action_name) }}
                                </a>
                            @endisset
                            @isset($action_link2)
                                <a href="{{ $action_link2 }}" class="dropdown-item">
                                    {!! $action_icon2 !!} {{ __($action_name2) }}
                                </a>
                            @endisset
                            @isset($action_link3)
                                <a href="{{ $action_link3 }}" class="dropdown-item">
                                    {!! $action_icon3 !!} {{ __($action_name3) }}
                                </a>
                            @endisset
                            @isset($action_link4)
                                <a href="{{ $action_link4 }}" class="dropdown-item">
                                    {!! $action_icon4 !!} {{ __($action_name4) }}
                                </a>
                            @endisset
                            @isset($action_link5)
                                <a target="_blank" href="{{ $action_link5 }}" class="dropdown-item">
                                    {!! $action_icon5 !!} {{ __($action_name5) }}
                                </a>
                            @endisset
                            @isset($usefilter)
                                <a href="javascript:;" id="show-hide-filters-mobile" class="dropdown-item">
                                    <i class="ki-duotone ki-setting-3 fs-4 me-2 text-secondary"></i>{{ __('Filters') }}
                                </a>
                            @endisset
                        </div>
                    </div>
                </div>
            </div>


            @isset($usefilter)
                <div class="card-body py-0">
                    @include('general.filters-client')
                </div>
            @endisset

            @yield('contenttop')

            <div class="card-body pt-0">
                @if (isset($iscontent))
                    @yield('cardbody')
                @else
                    @if (isset($items) && is_countable($items) && count($items) > 0)
                        <div class="mb-5">
                            <div class="input-group w-md-300px">
                                <span class="input-group-text">
                                    <i class="ki-duotone ki-magnifier fs-2"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control form-control-solid"
                                    placeholder="{{ __('Search...') }}">
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="dataTable">
                                <thead>
                                    <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                                        @if (isset($custom_table))
                                            @yield('thead')
                                        @else
                                            @if (isset($fields))
                                                @foreach ($fields as $field)
                                                    <th>{{ __($field['name']) }}</th>
                                                @endforeach
                                                <th class="text-center">{{ __('crud.actions') }}</th>
                                            @else
                                                @yield('thead')
                                            @endif
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @yield('tbody')
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endisset
@endsection

@section('topjs')
    <script>
        $(document).ready(function() {
            var table = $('#dataTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: false
            });

            $('#searchInput').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endsection
