@extends('general.index', ['title' => __('Catalogue Template')])

@section('content')
    <div class="container-xxl">
        <div class="card card-custom gutter-b">
            <div class="card-header card-header-custom border-0 py-6">
                <div class="card-title">
                    <h3 class="fw-bold m-0 text-gray-800">{{ __('Catalogue Template') }}</h3>
                    {{-- <div class="text-muted pt-2 fs-6">{{ __('Manage your message templates efficiently') }}</div> --}}
                </div>

                <div class="card-toolbar">
                    <div class="d-flex align-items-center gap-4">
                        <!-- Search Form -->
                        <form method="GET" class="d-flex align-items-center position-relative">
                            <i class="ki-duotone ki-magnifier fs-3 position-absolute ms-4 text-gray-500">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            <input type="text" name="name" class="form-control form-control-solid ps-12 w-250px"
                                placeholder="Search templates..." value="{{ request('name') }}"
                                onkeydown="if(event.key === 'Enter') this.form.submit()">
                            @foreach (request()->except('name', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>

                        <!-- Add Template Button -->
                        <a href="{{ route('catalog.catalogsTemplatesCreate') }}" class="btn btn-primary px-6">
                            <i class="ki-duotone ki-plus fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('Create New') }}
                        </a>

                        <a href="{{ route('catalog.carouselTemplatesCreate') }}" class="btn btn-light-primary px-6">
                            <i class="ki-duotone ki-plus fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            {{ __('Carousel') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-0">
                @if (session('success'))
                    <div
                        class="alert alert-dismissible bg-light-success border border-success d-flex flex-column flex-sm-row p-5 mb-10">
                        <i class="ki-duotone ki-check fs-2hx text-success me-4 mb-5 mb-sm-0">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-success">{{ __('Success') }}</h4>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button type="button"
                            class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                            data-bs-dismiss="alert">
                            <i class="ki-duotone ki-cross fs-1 text-success">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </button>
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="alert alert-dismissible bg-light-danger border border-danger d-flex flex-column flex-sm-row p-5 mb-10">
                        <i class="ki-duotone ki-cross-circle fs-2hx text-danger me-4 mb-5 mb-sm-0">
                            <span class="path1"></span>
                            <span class="path2"></span>
                            <span class="path3"></span>
                        </i>
                        <div class="d-flex flex-column">
                            <h4 class="mb-1 text-danger">{{ __('Error') }}</h4>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button type="button"
                            class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                            data-bs-dismiss="alert">
                            <i class="ki-duotone ki-cross fs-1 text-danger">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                        </button>
                    </div>
                @endif
                @if (count($setup['items']))
                    <div class="table-responsive">
                        <table class="table table-hover table-row-bordered align-middle gy-5">
                            <thead class="border-bottom border-gray-200 fs-7 fw-bolder">
                                <tr>
                                    <th class="min-w-200px">
                                        <a href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'name_asc' ? 'name_desc' : 'name_asc', 'page' => 1]) }}"
                                            class="text-gray-600 text-hover-primary d-flex align-items-center">
                                            {{ __('Template Name') }}
                                            @if (request('sort') == 'name_asc')
                                                <i class="ki-duotone ki-arrow-up fs-2 ms-2"></i>
                                            @elseif(request('sort') == 'name_desc')
                                                <i class="ki-duotone ki-arrow-down fs-2 ms-2"></i>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="min-w-100px">{{ __('Status') }}</th>
                                    <th class="min-w-100px">{{ __('Category') }}</th>
                                    <th class="min-w-100px">{{ __('Language') }}</th>
                                    {{-- <th class="min-w-150px">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => request('sort') == 'date_asc' ? 'date_desc' : 'date_asc', 'page' => 1]) }}"
                                        class="text-gray-600 text-hover-primary d-flex align-items-center">
                                        {{ __('Created Date') }}
                                        @if (request('sort') == 'date_asc')
                                            <i class="ki-duotone ki-arrow-up fs-2 ms-2"></i>
                                        @elseif(request('sort') == 'date_desc' || !request('sort'))
                                            <i class="ki-duotone ki-arrow-down fs-2 ms-2"></i>
                                        @endif
                                    </a>
                                </th> --}}
                                    <th class="text-end min-w-120px">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="fs-6">
                                @foreach ($setup['items'] as $template)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-45px me-4">
                                                    <span class="symbol-label bg-light-primary">
                                                        <i class="ki-duotone ki-message-text-2 fs-2 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-dark fw-bold d-block">{{ $template->name }}</span>
                                                    <span
                                                        class="text-muted fs-7">{{ Str::limit($template->description, 40) }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge py-3 px-4 fs-7 badge-light-{{ $template->status == 'APPROVED' ? 'success' : ($template->status == 'PENDING' ? 'warning' : 'danger') }}">
                                                <span
                                                    class="bullet bullet-vertical me-2 bg-{{ $template->status == 'APPROVED' ? 'success' : ($template->status == 'PENDING' ? 'warning' : 'danger') }}"></span>
                                                {{ $template->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info py-2 px-3">{{ $template->category }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge badge-light-primary py-2 px-3">{{ strtoupper($template->language) }}</span>
                                        </td>
                                        {{-- <td>
                                        <span
                                            class="text-gray-700 fw-semibold d-block">{{ $template->created_at->format('M d, Y') }}</span>
                                        <span class="text-muted fs-8">{{ $template->created_at->format('h:i A') }}</span>
                                    </td> --}}
                                        <td class="text-end text-nowrap">
                                            <div class="d-flex justify-content-end gap-2">
                                                {{-- <a href="#"
                                                class="btn btn-icon btn-light btn-active-light-primary btn-sm"
                                                data-bs-toggle="tooltip" title="Edit">
                                                <i class="ki-duotone ki-pencil fs-2 text-primary">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                            </a> --}}

                                                <a href="#templatePreviewModal"
                                                    class="btn btn-icon btn-light btn-active-light-primary btn-sm"
                                                    data-id="{{ $template->id }}"
                                                    data-components='@json($template->components)' data-bs-toggle="modal"
                                                    onclick="showTemplatePreview(this)" title="Preview">
                                                    <i class="ki-duotone ki-eye fs-2 text-info">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </a>
                                                <a type="submitt"
                                                    href="{{ Route('campaigns.create', ['templates_type' => 1]) }}"
                                                    class="btn btn-success btn-sm">{{ __('Send Catalog') }}</a>
                                                <button
                                                    class="btn btn-icon btn-light btn-active-light-danger btn-sm delete-btn"
                                                    data-id="{{ $template->id }}"
                                                    data-url="{{ route('templates.destroy', $template->id) }}"
                                                    data-bs-toggle="tooltip" title="Delete">
                                                    <i class="ki-duotone ki-trash fs-2 text-danger">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                        <span class="path3"></span>
                                                    </i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($setup['items']->hasPages())
                        <div class="d-flex flex-stack flex-wrap pt-10">
                            <div class="fs-6 fw-semibold text-gray-700">
                                Showing {{ $templates->firstItem() }} to {{ $templates->lastItem() }} of
                                {{ $templates->total() }} entries
                            </div>
                            <ul class="pagination">
                                {{-- Previous Page Link --}}
                                @if ($templates->onFirstPage())
                                    <li class="page-item previous disabled">
                                        <a href="#" class="page-link">
                                            <i class="ki-duotone ki-arrow-left fs-2"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item previous">
                                        <a href="{{ $templates->previousPageUrl() }}" class="page-link">
                                            <i class="ki-duotone ki-arrow-left fs-2"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Pagination Elements --}}
                                @foreach ($templates->getUrlRange(1, $templates->lastPage()) as $page => $url)
                                    @if ($page == $templates->currentPage())
                                        <li class="page-item active">
                                            <a href="#" class="page-link">{{ $page }}</a>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                        </li>
                                    @endif
                                @endforeach

                                {{-- Next Page Link --}}
                                @if ($templates->hasMorePages())
                                    <li class="page-item next">
                                        <a href="{{ $templates->nextPageUrl() }}" class="page-link">
                                            <i class="ki-duotone ki-arrow-right fs-2"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item next disabled">
                                        <a href="#" class="page-link">
                                            <i class="ki-duotone ki-arrow-right fs-2"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    @endif
                @else
                    <!--begin::Empty-->
                    <div class="text-center py-10">
                        <!--begin::Icon-->
                        <span class="svg-icon svg-icon-4x opacity-75">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3"
                                    d="M21.25 18.525L13.05 21.025C12.35 21.325 11.65 21.325 10.95 21.025L2.75 18.525C1.75 18.125 1.75 16.725 2.75 16.325L4.04999 15.825L10.25 18.325C10.85 18.525 11.45 18.625 12.05 18.625C12.65 18.625 13.25 18.525 13.85 18.325L20.05 15.825L21.35 16.325C22.35 16.725 22.35 18.125 21.25 18.525ZM13.05 16.425L21.25 13.925C22.25 13.525 22.25 12.125 21.25 11.725L13.05 9.22502C12.35 8.92502 11.65 8.92502 10.95 9.22502L2.75 11.725C1.75 12.125 1.75 13.525 2.75 13.925L4.04999 14.425L10.25 11.925C10.85 11.725 11.45 11.625 12.05 11.625C12.65 11.625 13.25 11.725 13.85 11.925L20.05 14.425L21.35 13.925C22.35 13.525 22.35 12.125 21.25 11.725L13.05 9.22502C12.35 8.92502 11.65 8.92502 10.95 9.22502L2.75 11.725C1.75 12.125 1.75 13.525 2.75 13.925L10.95 16.425C11.65 16.725 12.35 16.725 13.05 16.425Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M11.05 11.025L2.84998 8.52502C1.84998 8.12502 1.85 6.72502 2.85 6.32502L11.05 3.82502C11.75 3.52502 12.45 3.52502 13.15 3.82502L21.35 6.32502C22.35 6.72502 22.35 8.12502 21.35 8.52502L13.15 11.025C12.45 11.325 11.75 11.325 11.05 11.025Z"
                                    fill="currentColor"></path>
                            </svg>
                        </span>
                        <!--end::Icon-->
                        <!--begin::Message-->
                        <div class="mt-4">
                            <h3 class="fw-bold text-gray-600">{{ __('No records found') }}</h3>
                            <span class="text-muted">{{ __('There are no template to display here.') }}</span>
                        </div>
                        <!--end::Message-->
                    </div>
                    <!--end::Empty-->
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="templatePreviewModal" tabindex="-1" aria-labelledby="templatePreviewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-3 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="templatePreviewModalLabel">{{ __('Template Preview') }}</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-light-primary" data-bs-dismiss="modal">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                    </button>
                </div>

                <!-- Make modal body scrollable -->
                <div class="modal-body p-0" style="max-height: 80vh; overflow-y: auto;">
                    <!-- WhatsApp-style background container -->
                    <div class="d-flex justify-content-center align-items-center py-10"
                        style="min-height: 500px; background: url('{{ asset('uploads/default/dotflo/bg.png') }}'); background-size: cover; background-position: center;">

                        <!-- Mobile-sized preview card -->
                        <div id="previewElement" class="card shadow-lg"
                            style="width: 360px; border-radius: 12px; overflow: hidden;">

                            <!-- Chat-like message container -->
                            <div class="card-body py-4 px-3" style="background-color: #ffffff;">
                                <div id="templatePreviewBody"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('topjs')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showTemplatePreview(button) {
            let componentsRaw = button.dataset.components;

            // First decode the JSON string from the attribute
            let components;
            try {
                components = JSON.parse(componentsRaw); // Might still be a string
                if (typeof components === 'string') {
                    components = JSON.parse(components); // Parse again if still string
                }
            } catch (e) {
                console.error("Invalid components data", e);
                return;
            }

            const container = document.getElementById('templatePreviewBody');
            container.innerHTML = '';

            components.forEach(comp => {
                if (comp.type === 'HEADER') {
                    if (comp.format === 'TEXT') {
                        container.innerHTML += `<h4 class="fw-bold mb-3">${comp.text}</h4>`;
                    } else if (['IMAGE', 'VIDEO', 'DOCUMENT'].includes(comp.format)) {
                        const url = comp.example?.header_handle?.[0];
                        if (url) {
                            if (comp.format === 'IMAGE') {
                                container.innerHTML += `<img src="${url}" class="img-fluid rounded mb-3" />`;
                            } else if (comp.format === 'VIDEO') {
                                container.innerHTML +=
                                    `<video controls class="w-100 rounded mb-3"><source src="${url}"></video>`;
                            } else if (comp.format === 'DOCUMENT') {
                                container.innerHTML +=
                                    `<a href="${url}" target="_blank" class="btn btn-light-info mb-3 w-100 mb-2">View Document</a>`;
                            }
                        }
                    }
                }

                if (comp.type === 'BODY') {
                    container.innerHTML += `<p class="fs-5 mb-3">${comp.text.replace(/\n/g, '<br>')}</p>`;
                }

                if (comp.type === 'FOOTER') {
                    container.innerHTML += `<p class="text-muted small mb-3">${comp.text}</p>`;
                }

                if (comp.type === 'BUTTONS') {
                    const btnGroup = document.createElement('div');
                    btnGroup.className = 'd-flex flex-wrap gap-2 mt-3';

                    comp.buttons.forEach(btn => {
                        let html = '';
                        if (btn.type === 'PHONE_NUMBER') {
                            html =
                                `<a href="tel:${btn.phone_number}" class="btn btn-sm btn-light-primary w-100">${btn.text}</a>`;
                        } else if (btn.type === 'URL') {
                            html =
                                `<a href="${btn.url}" target="_blank" class="btn btn-sm btn-light-primary w-100">${btn.text}</a>`;
                        } else if (btn.type === 'QUICK_REPLY') {
                            html =
                                `<button class="btn btn-sm btn-light-primary w-100">${btn.text}</button>`;
                        } else if (btn.type === 'FLOW') {
                            html =
                                `<button class="btn btn-sm btn-light-primary w-100">${btn.text}</button>`;
                        }
                        btnGroup.innerHTML += html;
                    });

                    container.appendChild(btnGroup);
                }
            });
        }
    </script>
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Delete button functionality
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const templateId = this.dataset.id;
                    const templateName = this.closest('tr').querySelector('.text-dark').textContent;

                    Swal.fire({
                        title: 'Delete Template?',
                        html: `<div class="text-center">
                                    <i class="ki-duotone ki-question fs-2hx text-danger mb-3">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                    <p class="fs-5">Are you sure you want to delete<br><b>${templateName}</b>?</p>
                                    <p class="text-muted fs-7">This action cannot be undone</p>
                                </div>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel',
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'btn btn-danger px-6',
                            cancelButton: 'btn btn-light px-6'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait while we delete the template',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => Swal.showLoading()
                            });

                            // AJAX request
                            axios.delete(this.dataset.url, {
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Template has been deleted.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                })
                                .catch(error => {
                                    console.error(error);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Failed!',
                                        text: error.response?.data?.message ||
                                            'Could not delete template'
                                    });
                                });
                        }
                    });
                });
            });
        });
    </script>
@endsection

@section('css')
    <style>
        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.05);
        }

        .card-header-custom {
            background: linear-gradient(90deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 1px solid #ebedf3;
            padding: 1.5rem 2rem;
            border-radius: 10px 10px 0 0 !important;
        }

        .table-hover tbody tr {
            transition: all 0.2s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            transform: translateX(2px);
        }

        .badge-light-success {
            background-color: rgba(25, 187, 135, 0.1);
            color: #19bb87;
        }

        .badge-light-warning {
            background-color: rgba(255, 184, 34, 0.1);
            color: #ffb822;
        }

        .badge-light-danger {
            background-color: rgba(246, 78, 96, 0.1);
            color: #f64e60;
        }

        .badge-light-info {
            background-color: rgba(54, 153, 255, 0.1);
            color: #3699ff;
        }

        .badge-light-primary {
            background-color: rgba(95, 102, 255, 0.1);
            color: #5f66ff;
        }

        .pagination .page-item .page-link {
            border-radius: 6px;
            margin: 0 3px;
            border: none;
        }

        /* .pagination .page-item.active .page-link {
                                                    background-color: #3699ff;
                                                    border-color: #3699ff;
                                                } */

        .pagination .page-item .page-link i {
            vertical-align: middle;
        }

        .bullet {
            display: inline-block;
            width: 4px;
            /* height: 12px; */
            border-radius: 2px;
            vertical-align: middle;
        }

        .bullet-vertical {
            height: 24px;
        }

        .btn-icon {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }
    </style>
@endsection
