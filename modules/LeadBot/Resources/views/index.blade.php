@extends('layouts.app', ['title' => __('LeadBot Automation')])

@section('title')
    <title>{{ __('LeadBot Automation') }}</title>
@endsection

@section('head')
    @include('lead-bot::partials.ui')
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-atom"></i></span>
                        {{ __('LeadBot Automation') }}
                    </h1>
                    <span class="lb-subtitle">{{ __('Receive webhooks and run tasks (contacts, WhatsApp, APIs) in order.') }}</span>
                </div>

                <div class="lb-toolbar">
                    @if ($bots->isNotEmpty())
                        <div class="lb-search">
                            <span class="lb-search-ico"><i class="ni ni-zoom-split-in"></i></span>
                            <input type="text" id="table-search" placeholder="{{ __('Search bots…') }}">
                        </div>
                    @endif
                    <a href="{{ route('lead-bot.create') }}" class="lb-btn-primary">
                        <i class="ni ni-fat-add"></i> {{ __('Create LeadBot') }}
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

            <div class="lb-stats">
                <div class="lb-stat">
                    <div class="v">{{ (int) ($totalBots ?? $bots->total()) }}</div>
                    <div class="l">{{ __('Total bots') }}</div>
                </div>
                <div class="lb-stat">
                    <div class="v">{{ (int) ($uniqueApps ?? 0) }}</div>
                    <div class="l">{{ __('Connected apps') }}</div>
                </div>
                <div class="lb-stat">
                    <div class="v">{{ (int) $bots->total() }}</div>
                    <div class="l">{{ __('Bots in workspace') }}</div>
                </div>
            </div>

            <section class="lb-section">
                <div class="lb-section-hd">
                    <h3 class="lb-section-title">{{ __('All LeadBots') }}</h3>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small">{{ __('Filter by app') }}</span>
                        <select class="form-control" id="app-filter" style="height: 42px; border-radius: 12px;">
                            <option value="">{{ __('All') }}</option>
                            @foreach ($bots->pluck('app_id')->unique() as $app)
                                <option value="{{ $app }}">{{ ucfirst($app) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($bots->isNotEmpty())
                    <div class="lb-section-bd p-0">
                        <div class="table-responsive">
                            <table class="table lb-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:70px">#</th>
                                        <th>{{ __('Bot') }}</th>
                                        <th style="width:160px">{{ __('App') }}</th>
                                        <th style="width:220px">{{ __('Trigger') }}</th>
                                        <th>{{ __('Webhook') }}</th>
                                        <th style="width:160px" class="text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bots as $index => $bot)
                                        <tr class="bot-row" data-app="{{ $bot->app_id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="mr-3"
                                                        style="width:40px;height:40px;border-radius:12px;background:rgba(79,70,229,.10);display:flex;align-items:center;justify-content:center;color:#3730a3;">
                                                        <i class="ni ni-atom"></i>
                                                    </div>
                                                    <div class="d-flex flex-column">
                                                        <span class="font-weight-bold text-dark">{{ $bot->name }}</span>
                                                        <span class="text-muted small">#{{ $bot->id }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="lb-pill primary">{{ ucfirst($bot->app_id) }}</span></td>
                                            <td><span class="lb-pill info">{{ str_replace('_', ' ', ucfirst($bot->trigger_event)) }}</span></td>
                                            <td>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="lb-truncate text-muted"
                                                        title="{{ url("/api/lead-bot/webhook/{$bot->webhook_token}") }}">
                                                        {{ url("/api/lead-bot/webhook/{$bot->webhook_token}") }}
                                                    </span>
                                                    <button type="button" class="lb-icon-btn copy-webhook"
                                                        data-clipboard-text="{{ url("/api/lead-bot/webhook/{$bot->webhook_token}") }}"
                                                        title="{{ __('Copy') }}">
                                                        <i class="ni ni-single-copy-04"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-right">
                                                <div class="lb-actions">
                                                    <a class="lb-icon-btn" href="{{ route('lead-bot.edit', $bot->id) }}" title="{{ __('Edit') }}">
                                                        <i class="ni ni-ruler-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('lead-bot.destroy', $bot->id) }}" method="POST" class="delete-form m-0">
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

                    @if ($bots->hasPages())
                        <div class="lb-section-bd d-flex justify-content-between align-items-center flex-wrap">
                            <div class="lb-count">
                                {{ __('Showing') }} {{ $bots->firstItem() ?? 0 }}–{{ $bots->lastItem() ?? 0 }} {{ __('of') }} {{ $bots->total() }}
                            </div>
                            <div>{{ $bots->links() }}</div>
                        </div>
                    @endif
                @else
                    <div class="lb-section-bd">
                        <div class="text-center py-5">
                            <div class="mb-3" style="font-size: 42px; color: #4f46e5;"><i class="ni ni-atom"></i></div>
                            <h3 class="mb-2">{{ __('No LeadBots yet') }}</h3>
                            <p class="text-muted mb-4">{{ __('Create a LeadBot to receive a webhook and run tasks.') }}</p>
                            <a href="{{ route('lead-bot.create') }}" class="lb-btn-primary">
                                <i class="ni ni-fat-add"></i> {{ __('Create LeadBot') }}
                            </a>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const appFilter = document.getElementById('app-filter');
            const searchInput = document.getElementById('table-search');
            const rows = document.querySelectorAll('.bot-row');

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

            document.querySelectorAll('.delete-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: "{{ __('Delete LeadBot?') }}",
                        text: "{{ __('This action cannot be undone.') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, delete') }}",
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

