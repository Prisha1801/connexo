@extends('layouts.app', ['title' => __('Workflows Management')])

@section('title')
    <title>{{ __('Workflows Management') }}</title>
@endsection

@section('head')
    @include('work-flows::partials.ui')
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-diagram-3"></i></span>
                        {{ __('Workflows Management') }}
                    </h1>
                    <span class="lb-subtitle">{{ __('Automate processes and connect apps with webhooks and tasks.') }}</span>
                </div>

                <div class="lb-toolbar">
                    @if ($workflows->isNotEmpty())
                        <div class="lb-search">
                            <span class="lb-search-ico"><i class="ni ni-zoom-split-in"></i></span>
                            <input type="text" id="table-search" placeholder="{{ __('Search workflows…') }}">
                        </div>
                    @endif
                    <a href="{{ route('workflows.create') }}" class="lb-btn-primary">
                        <i class="ni ni-fat-add"></i> {{ __('Create Workflow') }}
                    </a>
                </div>
            </div>
        </header>

        <div class="lb-body">
            @if (session('success'))
                <div class="lb-flash">
                    <div class="alert alert-success mb-0">{{ session('success') }}</div>
                </div>
            @endif

            @php
                $uniqueApps = $workflows->pluck('app_id')->unique()->count();
            @endphp
            <div class="lb-stats">
                <div class="lb-stat">
                    <div class="v">{{ (int) $workflows->total() }}</div>
                    <div class="l">{{ __('Total Workflows') }}</div>
                </div>
                <div class="lb-stat">
                    <div class="v">{{ (int) $uniqueApps }}</div>
                    <div class="l">{{ __('Connected Apps') }}</div>
                </div>
                <div class="lb-stat">
                    <div class="v">{{ (int) $workflows->total() }}</div>
                    <div class="l">{{ __('Workflows in workspace') }}</div>
                </div>
            </div>

            <section class="lb-section">
                <div class="lb-section-hd">
                    <h3 class="lb-section-title">{{ __('All Workflows') }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">{{ __('Filter by app') }}</span>
                        <select class="form-control" id="app-filter" style="height: 42px; border-radius: 12px;">
                            <option value="">{{ __('All Apps') }}</option>
                            @foreach ($workflows->pluck('app_id')->unique() as $app)
                                <option value="{{ $app }}">{{ ucfirst($app) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($workflows->isNotEmpty())
                    <div class="lb-section-bd p-0">
                        <div class="table-responsive">
                            <table class="table lb-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:70px">#</th>
                                        <th>{{ __('Workflow') }}</th>
                                        <th style="width:160px">{{ __('App') }}</th>
                                        <th style="width:220px">{{ __('Trigger Event') }}</th>
                                        <th>{{ __('Webhook URL') }}</th>
                                        <th style="width:160px" class="text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($workflows as $index => $workflow)
                                        <tr class="workflow-row" data-app="{{ $workflow->app_id }}" data-workflow-id="{{ $workflow->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3"
                                                        style="width:40px;height:40px;border-radius:12px;background:rgba(13,148,136,.12);display:flex;align-items:center;justify-content:center;color:#0f766e;">
                                                        <i class="ni ni-diagram-3"></i>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="font-weight-bold text-dark wf-name">{{ $workflow->name }}</span>
                                                        <span class="text-muted small">#{{ $workflow->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="lb-pill primary">{{ ucfirst($workflow->app_id) }}</span></td>
                                            <td><span class="lb-pill info">{{ str_replace('_', ' ', ucfirst($workflow->trigger_event)) }}</span></td>
                                            <td>
                                                @if ($workflow->webhook_token)
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <span class="lb-truncate text-muted"
                                                            title="{{ url("/api/webhook/{$workflow->webhook_token}") }}">
                                                            {{ url("/api/webhook/{$workflow->webhook_token}") }}
                                                        </span>
                                                        <button type="button" class="lb-icon-btn copy-webhook"
                                                            data-clipboard-text="{{ url("/api/webhook/{$workflow->webhook_token}") }}"
                                                            title="{{ __('Copy') }}">
                                                            <i class="ni ni-single-copy-04"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <div class="lb-actions">
                                                    <a class="lb-icon-btn" href="{{ route('workflows.edit', $workflow->id) }}" title="{{ __('Edit') }}">
                                                        <i class="ni ni-ruler-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('workflows.destroy', $workflow->id) }}" method="POST" class="delete-form m-0">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="lb-icon-btn danger delete-btn" title="{{ __('Delete') }}">
                                                            <i class="ni ni-fat-remove"></i>
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

                    @if ($workflows->hasPages())
                        <div class="lb-pagination d-flex justify-content-between align-items-center flex-wrap">
                            <div class="lb-count">
                                {{ __('Showing') }} {{ $workflows->firstItem() ?? 0 }}–{{ $workflows->lastItem() ?? 0 }} {{ __('of') }} {{ $workflows->total() }}
                            </div>
                            <div>{{ $workflows->links() }}</div>
                        </div>
                    @endif
                @else
                    <div class="lb-section-bd">
                        <div class="lb-empty">
                            <div class="lb-empty-icon"><i class="ni ni-diagram-3"></i></div>
                            <h3 class="mb-2" style="color: var(--lb-text); font-size: 1.1rem;">{{ __('No Workflows Found') }}</h3>
                            <p class="mb-4">{{ __('You haven\'t created any workflows yet. Create one to automate processes and connect apps.') }}</p>
                            <a href="{{ route('workflows.create') }}" class="lb-btn-primary">
                                <i class="ni ni-fat-add"></i> {{ __('Create Workflow') }}
                            </a>
                            <div class="mt-4">
                                <a href="#" class="text-muted small" data-bs-toggle="modal" data-bs-target="#helpModal">
                                    <i class="ni ni-info"></i> {{ __('Learn more about workflows') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        </div>
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
                            <i class="ni ni-check-bold text-success me-3"></i>
                            <span>{{ __('Automate repetitive tasks') }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="ni ni-check-bold text-success me-3"></i>
                            <span>{{ __('Connect different applications') }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="ni ni-check-bold text-success me-3"></i>
                            <span>{{ __('Trigger actions based on events') }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="ni ni-check-bold text-success me-3"></i>
                            <span>{{ __('Save time and reduce manual work') }}</span>
                        </li>
                    </ul>
                </div>
                <div class="mb-4">
                    <h4 class="text-dark mb-3">{{ __('Workflow Components') }}</h4>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center mb-2">
                            <span class="lb-pill primary me-3">{{ __('App') }}</span>
                            <span>{{ __('The application that triggers the workflow') }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <span class="lb-pill info me-3">{{ __('Trigger Event') }}</span>
                            <span>{{ __('The specific event that starts the workflow') }}</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <span class="lb-pill me-3">{{ __('Webhook URL') }}</span>
                            <span>{{ __('The endpoint that receives the trigger event') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="lb-btn-soft" data-bs-dismiss="modal">{{ __('Close') }}</button>
                <a href="{{ route('workflows.create') }}" class="lb-btn-primary">{{ __('Create Workflow') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appFilter = document.getElementById('app-filter');
            const searchInput = document.getElementById('table-search');
            const rows = document.querySelectorAll('.workflow-row');

            function applyFilters() {
                const app = appFilter ? appFilter.value : '';
                const term = searchInput ? (searchInput.value || '').toLowerCase().trim() : '';

                rows.forEach(row => {
                    const matchesApp = app === '' || row.getAttribute('data-app') === app;
                    if (!matchesApp) {
                        row.style.display = 'none';
                        return;
                    }
                    if (term === '') {
                        row.style.display = '';
                        return;
                    }
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(term) ? '' : 'none';
                });
            }

            if (appFilter) appFilter.addEventListener('change', applyFilters);
            if (searchInput) searchInput.addEventListener('input', applyFilters);

            document.querySelectorAll('.copy-webhook').forEach(button => {
                button.addEventListener('click', async () => {
                    const text = button.getAttribute('data-clipboard-text');
                    try {
                        await navigator.clipboard.writeText(text);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: "{{ __('Webhook URL copied') }}",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                    } catch (e) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: "{{ __('Copy failed') }}",
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                    }
                });
            });

            const isStaff = @json(auth()->user()->hasRole('staff') ?? false);

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');

                    if (isStaff) {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("Permission Denied") }}',
                            text: '{{ __("You do not have rights to delete this workflow.") }}',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                        return;
                    }

                    const nameEl = this.closest('tr').querySelector('.wf-name');
                    const workflowName = nameEl ? nameEl.textContent : '';

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
                        if (result.isConfirmed) form.submit();
                    });
                });
            });
        });
    </script>
@endpush
