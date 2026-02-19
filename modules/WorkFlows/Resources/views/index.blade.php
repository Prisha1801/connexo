@extends('layouts.app-client')

@section('title', __('Workflows Management'))

@section('content')
    <div class="container-xxl">
        <!-- Page Header -->
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center mb-7">
            <div class="d-flex align-items-center">
                <h1 class="d-flex align-items-center text-dark fw-bolder my-1 fs-3">
                    <i class="ki-duotone ki-setting-3 fs-2hx me-4 text-primary">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    {{ __('Workflows Management') }}
                </h1>
                <span class="badge badge-light-primary fs-8 fw-bolder ms-4">{{ $workflows->total() }}
                    {{ __('Workflows') }}</span>
            </div>

            <div class="d-flex align-items-center gap-3">
                @if ($workflows->isNotEmpty())
                    <div class="d-flex align-items-center position-relative">
                        <i class="ki-duotone ki-magnifier fs-3 position-absolute start-0 ms-3">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        <input type="text" id="table-search" class="form-control form-control-solid w-250px ps-10"
                            placeholder="{{ __('Search workflows...') }}">
                    </div>
                @endif

                <a href="{{ route('workflows.create') }}" class="btn btn-primary">
                    <i class="ki-duotone ki-plus fs-2"></i>
                    {{ __('Create Workflow') }}
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        @if ($workflows->isNotEmpty())
            <div class="row g-6 mb-7">
                <div class="col-xl-6 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        <span class="fs-2hx fw-bolder text-dark">{{ $workflows->total() }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('Total Workflows') }}</span>
                    </div>
                </div>
                <div class="col-xl-6 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        @php
                            $uniqueApps = $workflows->pluck('app_id')->unique()->count();
                        @endphp
                        <span class="fs-2hx fw-bolder text-success">{{ $uniqueApps }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('Connected Apps') }}</span>
                    </div>
                </div>
                {{-- <div class="col-xl-3 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        @php
                            $activeWebhooks = $workflows->where('webhook_token', '!=', null)->count();
                        @endphp
                        <span class="fs-2hx fw-bolder text-warning">{{ $activeWebhooks }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('Active Webhooks') }}</span>
                    </div>
                </div> --}}
                {{-- <div class="col-xl-3 col-sm-6">
                    <div class="card card-dashed h-xl-100 flex-center flex-column py-4">
                        @php
                            $noWebhooks = $workflows->where('webhook_token', null)->count();
                        @endphp
                        <span class="fs-2hx fw-bolder text-danger">{{ $noWebhooks }}</span>
                        <span class="text-gray-600 fw-semibold">{{ __('No Webhook') }}</span>
                    </div>
                </div> --}}
            </div>
        @endif

        <!-- Workflows Table Card -->
        <div class="card">
            @if ($workflows->isNotEmpty())
                <!-- Card Header -->
                <div class="card-header border-0 pt-6">
                    <div class="card-title">
                        <h3 class="fw-bolder m-0">{{ __('All Workflows') }}</h3>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted fs-7">{{ __('Filter by app:') }}</span>
                            <select class="form-select form-select-sm w-150px" id="app-filter">
                                <option value="">{{ __('All Apps') }}</option>
                                @foreach ($workflows->pluck('app_id')->unique() as $app)
                                    <option value="{{ $app }}">{{ ucfirst($app) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                    <div class="alert alert-dismissible bg-light-success d-flex flex-column flex-sm-row p-5 m-5 mb-0">
                        <div class="d-flex flex-column pe-0 pe-sm-10">
                            <span class="fw-semibold">{{ session('success') }}</span>
                        </div>
                        <button type="button"
                            class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto"
                            data-bs-dismiss="alert">
                            <i class="ki-duotone ki-cross fs-1 text-success"></i>
                        </button>
                    </div>
                @endif

                <!-- Table -->
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table table-row-bordered table-row-dashed gy-4 align-middle fs-6">
                            <thead>
                                <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                                    <th class="min-w-50px">#</th>
                                    <th class="min-w-200px">{{ __('Workflow') }}</th>
                                    <th class="min-w-150px">{{ __('App') }}</th>
                                    <th class="min-w-200px">{{ __('Trigger Event') }}</th>
                                    <th class="min-w-300px">{{ __('Webhook URL') }}</th>
                                    <th class="min-w-150px text-end">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-semibold">
                                @foreach ($workflows as $index => $workflow)
                                    <tr data-workflow-id="{{ $workflow->id }}" class="workflow-row"
                                        data-app="{{ $workflow->app_id }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-50px symbol-circle me-5">
                                                    <div class="symbol-label bg-light-primary">
                                                        <i class="ki-duotone ki-setting-3 fs-3 text-primary">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                        </i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6 mb-1">{{ $workflow->name }}</span>
                                                    {{-- <span class="text-muted fs-7">ID: {{ $workflow->id }}</span> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-primary fs-8">{{ ucfirst($workflow->app_id) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-light-info fs-8">{{ str_replace('_', ' ', ucfirst($workflow->trigger_event)) }}</span>
                                        </td>
                                        <td>
                                            @if ($workflow->webhook_token)
                                                <div class="d-flex align-items-center">
                                                    <span class="text-truncate me-2" style="max-width: 200px"
                                                        data-bs-toggle="tooltip"
                                                        title="{{ url("/api/webhook/{$workflow->webhook_token}") }}">
                                                        {{ url("/api/webhook/{$workflow->webhook_token}") }}
                                                    </span>
                                                    <button type="button"
                                                        class="btn btn-icon btn-active-light-primary btn-sm copy-webhook"
                                                        data-clipboard-text="{{ url("/api/webhook/{$workflow->webhook_token}") }}"
                                                        data-bs-toggle="tooltip" title="{{ __('Copy to clipboard') }}">
                                                        <i class="ki-duotone ki-copy fs-2"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2 align-items-center">
                                                <!-- Edit Button -->
                                                <a href="{{ route('workflows.edit', $workflow->id) }}"
                                                    class="btn btn-icon btn-light-primary btn-sm"
                                                    title="{{ __('Edit Workflow') }}" data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                    <i class="ki-duotone ki-pencil fs-4">
                                                        <span class="path1"></span>
                                                        <span class="path2"></span>
                                                    </i>
                                                </a>

                                                <!-- Delete Button -->
                                                <form action="{{ route('workflows.destroy', $workflow->id) }}" method="POST"
                                                    class="delete-form d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm delete-btn"
                                                        title="{{ __('Delete Workflow') }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
                                                        <i class="ki-duotone ki-trash fs-4">
                                                            <span class="path1"></span>
                                                            <span class="path2"></span>
                                                            <span class="path3"></span>
                                                        </i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                @if ($workflows->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                        <div class="d-flex align-items-center">
                            <span class="text-muted fs-7">
                                {{ __('Showing') }}
                                <strong>{{ $workflows->firstItem() ?? 0 }}</strong>
                                {{ __('to') }}
                                <strong>{{ $workflows->lastItem() ?? 0 }}</strong>
                                {{ __('of') }}
                                <strong>{{ $workflows->total() }}</strong>
                                {{ __('entries') }}
                            </span>
                        </div>
                        <div class="d-flex flex-wrap py-2">
                            {{ $workflows->links() }}
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="card-body">
                    <div class="text-center py-10">
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <!-- Icon -->
                            <div class="symbol symbol-100px symbol-circle mb-5">
                                <div class="symbol-label bg-light-primary">
                                    <i class="ki-duotone ki-setting-3 fs-2hx text-primary">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>
                                </div>
                            </div>

                            <!-- Text -->
                            <h3 class="text-dark fw-bolder mb-3">{{ __('No Workflows Found') }}</h3>
                            <p class="text-muted fs-5 mb-6 w-lg-400px">
                                {{ __('You haven\'t created any workflows yet. Workflows help you automate processes and connect different apps together.') }}
                            </p>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3">
                                <a href="{{ route('workflows.create') }}" class="btn btn-primary">
                                    <i class="ki-duotone ki-plus fs-2"></i>
                                    {{ __('Create Your First Workflow') }}
                                </a>
                                <a href="#" class="btn btn-light" data-bs-toggle="modal"
                                    data-bs-target="#helpModal">
                                    <i class="ki-duotone ki-information fs-2"></i>
                                    {{ __('Learn More') }}
                                </a>
                            </div>

                            <!-- Additional Help -->
                            <div class="mt-10">
                                <div class="d-flex align-items-center text-muted fs-7">
                                    <i class="ki-duotone ki-information fs-3 me-2"></i>
                                    {{ __('Workflows automate repetitive tasks and connect your favorite apps to work together seamlessly.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title">{{ __('About Workflows') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-5">
                        <h4 class="text-dark mb-3">{{ __('What can workflows do?') }}</h4>
                        <ul class="list-unstyled">
                            <li class="d-flex align-items-center mb-2">
                                <i class="ki-duotone ki-check-circle fs-3 text-success me-3"></i>
                                <span>{{ __('Automate repetitive tasks') }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="ki-duotone ki-check-circle fs-3 text-success me-3"></i>
                                <span>{{ __('Connect different applications') }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="ki-duotone ki-check-circle fs-3 text-success me-3"></i>
                                <span>{{ __('Trigger actions based on events') }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <i class="ki-duotone ki-check-circle fs-3 text-success me-3"></i>
                                <span>{{ __('Save time and reduce manual work') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="mb-4">
                        <h4 class="text-dark mb-3">{{ __('Workflow Components') }}</h4>
                        <ul class="list-unstyled">
                            <li class="d-flex align-items-center mb-2">
                                <span class="badge badge-light-primary me-3">{{ __('App') }}</span>
                                <span>{{ __('The application that triggers the workflow') }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <span class="badge badge-light-info me-3">{{ __('Trigger Event') }}</span>
                                <span>{{ __('The specific event that starts the workflow') }}</span>
                            </li>
                            <li class="d-flex align-items-center mb-2">
                                <span class="badge badge-light-success me-3">{{ __('Webhook URL') }}</span>
                                <span>{{ __('The endpoint that receives the trigger event') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <a href="{{ route('workflows.create') }}"
                        class="btn btn-primary">{{ __('Create Workflow') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // App filter functionality
            const appFilter = document.getElementById('app-filter');
            if (appFilter) {
                appFilter.addEventListener('change', function() {
                    const app = this.value;
                    const rows = document.querySelectorAll('.workflow-row');

                    rows.forEach(row => {
                        if (app === '' || row.getAttribute('data-app') === app) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });
            }

            // Search functionality
            const searchInput = document.getElementById('table-search');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();
                    const appFilterValue = appFilter ? appFilter.value : '';
                    const rows = document.querySelectorAll('.workflow-row');

                    if (searchTerm.length === 0) {
                        // Show all rows based on app filter
                        rows.forEach(row => {
                            if (appFilterValue === '' || row.getAttribute('data-app') === appFilterValue) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                        return;
                    }

                    rows.forEach(row => {
                        const name = row.querySelector('td:nth-child(2) span.text-dark').textContent
                            .toLowerCase();
                        const app = row.querySelector('td:nth-child(3) span').textContent
                            .toLowerCase();
                        const trigger = row.querySelector('td:nth-child(4) span').textContent
                            .toLowerCase();
                        const id = row.querySelector('td:nth-child(2) span.text-muted').textContent
                            .toLowerCase();

                        const matchesSearch = name.includes(searchTerm) ||
                            app.includes(searchTerm) ||
                            trigger.includes(searchTerm) ||
                            id.includes(searchTerm);

                        const matchesApp = appFilterValue === '' ||
                            row.getAttribute('data-app') === appFilterValue;

                        row.style.display = (matchesSearch && matchesApp) ? '' : 'none';
                    });
                });
            }

            // Delete functionality with permission check
            const isStaff = @json(auth()->user()->hasRole('staff'));
            
            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    // Permission check
                    if (isStaff) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Permission Denied',
                            text: 'You do not have rights to delete this workflow.',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        return;
                    }

                    const workflowName = this.closest('tr').querySelector('span.text-dark').textContent;

                    Swal.fire({
                        title: "{{ __('Are you sure?') }}",
                        html: `{{ __('You are about to delete the workflow') }} <strong>"${workflowName}"</strong>. {{ __('This action cannot be undone.') }}`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, delete it!') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                        reverseButtons: true,
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-light'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            // Clipboard functionality with SweetAlert toasts
            document.querySelectorAll('.copy-webhook').forEach(button => {
                button.addEventListener('click', async (e) => {
                    e.preventDefault();
                    const text = button.getAttribute('data-clipboard-text');

                    try {
                        await navigator.clipboard.writeText(text);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: "{{ __('Webhook URL copied to clipboard!') }}",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: '#1bc5bd',
                            iconColor: 'white',
                            color: 'white'
                        });
                    } catch (err) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: "{{ __('Failed to copy to clipboard') }}",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                });
            });
        });
    </script>
@endpush

@push('css')
    <style>
        .workflow-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #f3f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #3699ff;
            font-size: 1.25rem;
        }

        .card-dashed {
            border: 1px dashed #e4e6ef;
            background: #fafafa;
        }

        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .workflow-row td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .badge-light-primary {
            background-color: #f1faff;
            color: #3699ff;
        }

        .badge-light-info {
            background-color: #f8f5ff;
            color: #8950fc;
        }

        @media (max-width: 768px) {
            .d-flex.flex-column.flex-sm-row {
                flex-direction: column !important;
                align-items: flex-start !important;
            }

            .d-flex.flex-column.flex-sm-row .btn {
                margin-top: 1rem;
                width: 100%;
            }

            .w-250px {
                width: 100% !important;
            }

            .workflow-row .d-flex.gap-2 {
                flex-direction: column;
                gap: 0.5rem !important;
            }

            .workflow-row .d-flex.gap-2 .btn {
                margin: 0;
            }

            #app-filter {
                width: 100% !important;
                margin-top: 1rem;
            }
            
            .table-responsive {
                font-size: 0.875rem;
            }
        }
    </style>
@endpush