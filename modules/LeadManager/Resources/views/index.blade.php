@extends('layouts.app', ['title' => __('Lead Manager')])

@section('title')
    <title>{{ __('Lead Manager') }}</title>
@endsection

@section('head')
    @include('work-flows::partials.ui')
    <style>
        .lm-stat-total { border-left: 4px solid #94a3b8; }
        .lm-stat-won { border-left: 4px solid #22c55e; }
        .lm-stat-active { border-left: 4px solid #eab308; }
        .lm-stat-lost { border-left: 4px solid #ec4899; }
        .lm-stat-won .v { color: #22c55e; }
        .lm-stat-active .v { color: #eab308; }
        .lm-stat-lost .v { color: #ec4899; }
        .lm-kanban { display: grid; grid-auto-flow: column; grid-auto-columns: 300px; gap: 1.25rem; overflow-x: auto; padding: 1.25rem; min-height: 420px; background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 50%, #f1f5f9 100%); border-radius: 16px; justify-content: start; }
        .lm-kanban::-webkit-scrollbar { height: 10px; }
        .lm-kanban::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 5px; }
        .lm-kanban::-webkit-scrollbar-thumb { background: linear-gradient(90deg, #0d9488, #14b8a6); border-radius: 5px; }
        .lm-kanban::-webkit-scrollbar-thumb:hover { background: #0f766e; }
        .lm-kanban-col { width: 300px; min-width: 300px; background: #fff; border-radius: 14px; display: flex; flex-direction: column; max-height: calc(100vh - 320px); box-shadow: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -2px rgba(0,0,0,.06); border: 1px solid rgba(13,148,136,.08); transition: box-shadow .2s, transform .2s; }
        .lm-kanban-col:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,.08), 0 4px 6px -4px rgba(0,0,0,.06); }
        .lm-kanban-col-hd { padding: 1rem 1.25rem; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.02em; color: #fff; background: linear-gradient(135deg, #0f766e 0%, #115e59 50%, #134e4a 100%); border-radius: 14px 14px 0 0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(15,118,110,.2); }
        .lm-kanban-col-hd .badge { background: rgba(255,255,255,.95); color: #0f766e; min-width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 700; box-shadow: 0 1px 3px rgba(0,0,0,.12); }
        .lm-kanban-col-bd { flex: 1; overflow-y: auto; padding: 1rem; min-height: 100px; display: flex; flex-direction: column; gap: 0.75rem; }
        .lm-kanban-col-bd::-webkit-scrollbar { width: 6px; }
        .lm-kanban-col-bd::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
        .lm-kanban-col-bd::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        .lm-kanban-col.drag-over { background: linear-gradient(180deg, #f0fdfa 0%, #fff 100%); box-shadow: 0 0 0 3px rgba(13,148,136,.25); border-color: #14b8a6; transform: scale(1.01); }
        .lm-kanban-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.125rem; cursor: grab; box-shadow: 0 1px 3px rgba(0,0,0,.05); transition: all .2s ease; }
        .lm-kanban-card:hover { box-shadow: 0 8px 20px -4px rgba(0,0,0,.1); border-color: #cbd5e1; transform: translateY(-2px); }
        .lm-kanban-card.dragging { opacity: 0.7; cursor: grabbing; transform: rotate(2deg); box-shadow: 0 12px 24px -8px rgba(0,0,0,.2); }
        .lm-kanban-card .card-row { display: flex; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.5rem; font-size: 0.9rem; }
        .lm-kanban-card .card-row:last-of-type { margin-bottom: 0; }
        .lm-kanban-card .card-row i { color: #0d9488; font-size: 1rem; margin-top: 1px; flex-shrink: 0; }
        .lm-kanban-card .card-name { font-weight: 600; color: #1e293b; font-size: 0.95rem; }
        .lm-kanban-card .card-meta { color: #64748b; line-height: 1.4; }
        .lm-kanban-card .card-actions { margin-top: 0.75rem; padding-top: 0.6rem; border-top: 1px solid #f1f5f9; display: flex; gap: 0.35rem; justify-content: flex-end; }
        .lm-kanban-card .card-actions .btn-view { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff; border: none; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 4px rgba(139,92,246,.3); transition: transform .15s, box-shadow .2s; }
        .lm-kanban-card .card-actions .btn-view:hover { transform: scale(1.08); box-shadow: 0 4px 8px rgba(139,92,246,.35); color: #fff; }
        .lm-kanban-card .card-actions .btn-edit { width: 34px; height: 34px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #fff; border: none; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 2px 4px rgba(16,185,129,.3); transition: transform .15s, box-shadow .2s; }
        .lm-kanban-card .card-actions .btn-edit:hover { transform: scale(1.08); box-shadow: 0 4px 8px rgba(16,185,129,.35); color: #fff; }
    </style>
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-single-02"></i></span>
                        {{ __('Lead Manager') }}
                    </h1>
                    <span class="lb-subtitle">{{ $leads->total() }} {{ __('Leads') }}</span>
                </div>
                <div class="lb-toolbar d-flex flex-wrap align-items-center gap-2">
                    <form method="GET" action="{{ route('lead-manager.index') }}" class="d-flex align-items-center" style="max-width: 260px;">
                        @foreach (request()->except('search') as $k => $v)
                            @if (is_array($v))
                                @foreach ($v as $vv)
                                    <input type="hidden" name="{{ $k }}[]" value="{{ $vv }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endif
                        @endforeach
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search leads…') }}" class="form-control" style="height: 40px; border-radius: 10px 0 0 10px;">
                        <button type="submit" class="lb-btn-soft" style="height: 40px; border-radius: 0 10px 10px 0;"><i class="ni ni-zoom-split-in"></i></button>
                    </form>
                    <button type="button" class="lb-btn-soft" id="kanban-btn">
                        <i class="ni ni-diagram-3"></i> {{ __('Kanban') }}
                    </button>
                    <div class="dropdown">
                        <button class="lb-btn-primary dropdown-toggle" type="button" id="addLeadDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ni ni-fat-add"></i> {{ __('Add Lead') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="addLeadDropdown">
                            <li><a class="dropdown-item" href="{{ route('lead-manager.create') }}"><i class="ni ni-single-02 me-2"></i>{{ __('Add Lead manually') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('lead-manager.import') }}"><i class="ni ni-archive-2 me-2"></i>{{ __('Import Leads') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="lb-body">
            @if (session('success'))
                <div class="lb-flash">
                    <div class="alert alert-success mb-0">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="lb-flash">
                    <div class="alert alert-danger mb-0">{{ session('error') }}</div>
                </div>
            @endif

            <div class="lb-stats">
                <div class="lb-stat lm-stat-total">
                    <div class="v">{{ (int) ($stats['total'] ?? 0) }}</div>
                    <div class="l">{{ __('Total Leads') }}</div>
                </div>
                <div class="lb-stat lm-stat-won">
                    <div class="v">{{ (int) ($stats['won'] ?? 0) }}</div>
                    <div class="l">{{ __('Won Leads') }}</div>
                </div>
                <div class="lb-stat lm-stat-active">
                    <div class="v">{{ (int) ($stats['active'] ?? 0) }}</div>
                    <div class="l">{{ __('Active Leads') }}</div>
                </div>
                <div class="lb-stat lm-stat-lost">
                    <div class="v">{{ (int) ($stats['lost'] ?? 0) }}</div>
                    <div class="l">{{ __('Lost Leads') }}</div>
                </div>
            </div>

            <section class="lb-section" id="leads-section">
                <div class="lb-section-hd flex-wrap" id="table-section-hd">
                    <h3 class="lb-section-title mb-0">{{ __('All Leads') }}</h3>
                    <form method="GET" action="{{ route('lead-manager.index') }}" class="d-flex flex-wrap align-items-center gap-2" id="filter-form">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="source" class="form-control" style="height: 38px; border-radius: 10px; min-width: 140px;">
                            <option value="">{{ __('All Sources') }}</option>
                            @foreach ($sources ?? [] as $k => $v)
                                <option value="{{ $k }}" {{ request('source') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <select name="stage" class="form-control" style="height: 38px; border-radius: 10px; min-width: 180px;">
                            <option value="">{{ __('Filter by stage: All Stages') }}</option>
                            @foreach ($stages ?? [] as $k => $v)
                                <option value="{{ $k }}" {{ request('stage') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <select name="assigned" class="form-control" style="height: 38px; border-radius: 10px; min-width: 130px;">
                            <option value="">{{ __('All Agents') }}</option>
                            <option value="unassigned" {{ request('assigned') === 'unassigned' ? 'selected' : '' }}>{{ __('Unassigned') }}</option>
                            @foreach ($agents ?? [] as $a)
                                <option value="{{ $a->id }}" {{ request('assigned') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control" style="height: 38px; border-radius: 10px; width: 140px;" placeholder="dd-mm-yyyy">
                        <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control" style="height: 38px; border-radius: 10px; width: 140px;" placeholder="dd-mm-yyyy">
                        <button type="submit" class="lb-btn-soft" style="height: 38px;">{{ __('Filter') }}</button>
                        <button type="button" class="lb-btn-soft" style="height: 38px;" id="reset-filters">{{ __('Reset') }}</button>
                    </form>
                </div>
                <div class="lb-section-hd flex-wrap d-none" id="kanban-section-hd" style="border-bottom: none; padding: 1.25rem 1.5rem; background: #fff;">
                    <h3 class="lb-section-title mb-0" style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="ni ni-diagram-3" style="color: #0f766e;"></i> {{ __('Lead Pipeline - Kanban View') }}
                    </h3>
                    <button type="button" class="btn btn-primary" id="back-to-table-btn" style="height: 40px; background: #8b5cf6; border-color: #8b5cf6; border-radius: 8px;">
                        <i class="ni ni-bullet-list-67 me-1"></i> {{ __('List View') }}
                    </button>
                </div>

                <div id="table-view">
                @if ($leads->isNotEmpty())
                    <div class="lb-section-bd p-0">
                        <div class="table-responsive">
                            <table class="table lb-table mb-0">
                                <thead>
                                    <tr>
                                        <th style="width:40px"><input type="checkbox" id="select-all-leads" title="{{ __('Select all') }}"></th>
                                        <th>{{ __('LEAD') }}</th>
                                        <th>{{ __('CONTACT INFO') }}</th>
                                        <th>{{ __('SOURCE') }}</th>
                                        <th>{{ __('STAGE') }}</th>
                                        <th>{{ __('AGENT') }}</th>
                                        <th>{{ __('NEXT FOLLOW-UP') }}</th>
                                        <th>{{ __('CREATED') }}</th>
                                        <th style="width:120px" class="text-end">{{ __('ACTIONS') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <form action="{{ route('lead-manager.assign') }}" method="POST" id="assign-form">
                                        @csrf
                                        <input type="hidden" name="user_id" id="assign-user-id">
                                        @foreach ($leads as $i => $lead)
                                            <tr>
                                                <td>
                                                    @if (!$lead->isClosed())
                                                        <input type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" class="lead-checkbox">
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><a href="{{ route('lead-manager.show', $lead) }}" class="text-decoration-none text-dark">{{ $lead->contact?->name ?? '—' }}</a></div>
                                                    @if ($lead->tags)
                                                        <div class="small text-muted">
                                                            @foreach (array_filter(array_map('trim', explode(',', $lead->tags))) as $tag)
                                                                <span class="lb-pill me-1" style="font-size: 0.7rem;">{{ $tag }}</span>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div>{{ $lead->contact?->phone ?? '—' }}</div>
                                                    <div class="small text-muted">{{ $lead->contact?->email ?: __('No email') }}</div>
                                                </td>
                                                <td><span class="lb-pill primary">{{ $lead->source_label }}</span></td>
                                                <td><span class="lb-pill info">{{ $lead->stage }}</span></td>
                                                <td>{{ $lead->user->name ?? __('Unassigned') }}</td>
                                                <td class="small">{{ $lead->next_follow_up_at ? $lead->next_follow_up_at->format('M d, Y') : __('Not scheduled') }}</td>
                                                <td class="small">{{ $lead->created_at->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('lead-manager.show', $lead) }}" class="lb-icon-btn" title="{{ __('View') }}"><i class="ni ni-single-02"></i></a>
                                                    <a href="{{ route('lead-manager.edit', $lead) }}" class="lb-icon-btn" title="{{ __('Edit') }}"><i class="ni ni-ruler-pencil"></i></a>
                                                    <form action="{{ route('lead-manager.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this lead?') }}');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="lb-icon-btn danger" title="{{ __('Delete') }}"><i class="ni ni-fat-remove"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </form>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="lb-pagination d-flex justify-content-between align-items-center flex-wrap p-3">
                        <div class="lb-count">
                            {{ __('Showing') }} {{ $leads->firstItem() ?? 0 }}–{{ $leads->lastItem() ?? 0 }} {{ __('of') }} {{ $leads->total() }}
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <select id="assign-agent" class="form-control" style="height: 38px; width: 160px; border-radius: 10px;">
                                <option value="">{{ __('Assign selected to…') }}</option>
                                @foreach ($agents ?? [] as $a)
                                    <option value="{{ $a->id }}">{{ $a->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="do-assign" class="lb-btn-soft btn-sm">{{ __('Assign') }}</button>
                            @if ($leads->hasPages())
                                <div>{{ $leads->links() }}</div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="lb-section-bd">
                        <div class="lb-empty">
                            <div class="lb-empty-icon"><i class="ni ni-single-02"></i></div>
                            <h3 class="mb-2" style="color: var(--lb-text); font-size: 1.1rem;">{{ __('No leads yet') }}</h3>
                            <p class="mb-4">{{ __('Add a lead manually or import from CSV. Leads from CRM, Facebook, WhatsApp Campaign, Instagram & more will appear here.') }}</p>
                            <a href="{{ route('lead-manager.create') }}" class="lb-btn-primary mr-2">
                                <i class="ni ni-fat-add"></i> {{ __('Add Lead') }}
                            </a>
                            <a href="{{ route('lead-manager.import') }}" class="lb-btn-soft">
                                <i class="ni ni-archive-2"></i> {{ __('Import Leads') }}
                            </a>
                        </div>
                    </div>
                @endif
                </div>

                <div id="kanban-view" class="d-none p-3">
                    <div id="kanban-board"></div>
                    <div id="kanban-loading" class="text-center py-5 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>{{ __('Loading…') }}</div>
                    <div id="kanban-empty" class="lb-empty d-none">
                        <div class="lb-empty-icon"><i class="ni ni-single-02"></i></div>
                        <h3 class="mb-2" style="color: var(--lb-text); font-size: 1.1rem;">{{ __('No leads yet') }}</h3>
                        <p class="mb-4">{{ __('Add a lead to get started.') }}</p>
                        <a href="{{ route('lead-manager.create') }}" class="lb-btn-primary"><i class="ni ni-fat-add"></i> {{ __('Add Lead') }}</a>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var selectAll = document.getElementById('select-all-leads');
    var checkboxes = document.querySelectorAll('.lead-checkbox');
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            checkboxes.forEach(function(cb) { cb.checked = selectAll.checked; });
        });
    }
    var assignAgent = document.getElementById('assign-agent');
    var assignForm = document.getElementById('assign-form');
    var doAssign = document.getElementById('do-assign');
    if (doAssign && assignForm && assignAgent) {
        doAssign.addEventListener('click', function() {
            var uid = assignAgent.value;
            if (!uid) { alert('{{ __("Select an agent") }}'); return; }
            var checked = document.querySelectorAll('.lead-checkbox:checked');
            if (checked.length === 0) { alert('{{ __("Select at least one lead") }}'); return; }
            document.getElementById('assign-user-id').value = uid;
            assignForm.submit();
        });
    }
    var resetBtn = document.getElementById('reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            window.location.href = '{{ route("lead-manager.index") }}';
        });
    }

    var kanbanBtn = document.getElementById('kanban-btn');
    var backToTableBtn = document.getElementById('back-to-table-btn');
    var tableView = document.getElementById('table-view');
    var kanbanView = document.getElementById('kanban-view');
    var tableHd = document.getElementById('table-section-hd');
    var kanbanHd = document.getElementById('kanban-section-hd');
    var kanbanBoard = document.getElementById('kanban-board');
    var kanbanLoading = document.getElementById('kanban-loading');
    var kanbanEmpty = document.getElementById('kanban-empty');

    function buildQueryString() {
        var form = document.getElementById('filter-form');
        if (!form) return '';
        var fd = new FormData(form);
        var params = [];
        fd.forEach(function(v, k) { if (v) params.push(k + '=' + encodeURIComponent(v)); });
        return params.length ? '?' + params.join('&') : '';
    }

    function initKanbanDragDrop() {
        var cols = kanbanBoard.querySelectorAll('.lm-kanban-col');
        var cards = kanbanBoard.querySelectorAll('.lm-kanban-card');
        var draggedCard = null;

        cards.forEach(function(card) {
            card.ondragstart = function(e) {
                draggedCard = card;
                card.classList.add('dragging');
                e.dataTransfer.setData('text/plain', card.dataset.leadId);
                e.dataTransfer.effectAllowed = 'move';
            };
            card.ondragend = function() {
                card.classList.remove('dragging');
                cols.forEach(function(c) { c.classList.remove('drag-over'); });
                draggedCard = null;
            };
        });

        cols.forEach(function(col) {
            var bd = col.querySelector('.lm-kanban-col-bd');
            var stage = col.dataset.stage;
            bd.ondragover = function(e) {
                e.preventDefault();
                e.dataTransfer.dropEffect = 'move';
                col.classList.add('drag-over');
            };
            bd.ondragleave = function() {
                col.classList.remove('drag-over');
            };
            bd.ondrop = function(e) {
                e.preventDefault();
                col.classList.remove('drag-over');
                var leadId = e.dataTransfer.getData('text/plain');
                if (!leadId || !draggedCard || stage === draggedCard.dataset.stage) return;

                var formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('_method', 'PATCH');
                formData.append('stage', stage);

                fetch('{{ route("lead-manager.update-stage", ["lead" => "__ID__"]) }}'.replace('__ID__', leadId), {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(function(r) {
                    if (!r.ok) return r.text().then(function(t) { throw new Error(t || r.status); });
                    return r.json();
                })
                .then(function(data) {
                    if (data.success) {
                        var oldStage = draggedCard.dataset.stage;
                        draggedCard.dataset.stage = stage;
                        bd.appendChild(draggedCard);
                        var badge = col.querySelector('.lm-kanban-col-hd .badge');
                        if (badge) badge.textContent = bd.querySelectorAll('.lm-kanban-card').length;
                        var oldCol = kanbanBoard.querySelector('.lm-kanban-col[data-stage="' + oldStage + '"]');
                        if (oldCol) {
                            var obadge = oldCol.querySelector('.lm-kanban-col-hd .badge');
                            if (obadge) obadge.textContent = oldCol.querySelector('.lm-kanban-col-bd').querySelectorAll('.lm-kanban-card').length;
                        }
                    }
                })
                .catch(function(err) {
                    console.error('Stage update error:', err);
                    alert('{{ __("Failed to update. Please try again.") }}');
                });
            };
        });
    }

    kanbanBtn.addEventListener('click', function() {
        tableView.classList.add('d-none');
        tableHd.classList.add('d-none');
        kanbanView.classList.remove('d-none');
        kanbanHd.classList.remove('d-none');
        kanbanBoard.innerHTML = '';
        kanbanBoard.classList.add('d-none');
        kanbanLoading.classList.remove('d-none');
        kanbanEmpty.classList.add('d-none');

        fetch('{{ route("lead-manager.kanban-data") }}' + buildQueryString(), {
            headers: { 'Accept': 'application/json' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            kanbanLoading.classList.add('d-none');
            if (data.error) {
                kanbanEmpty.classList.remove('d-none');
                return;
            }
            var total = 0;
            var html = '';
            (data.stages || []).forEach(function(stageName) {
                var leads = (data.leadsByStage || {})[stageName] || [];
                total += leads.length;
                html += '<div class="lm-kanban-col" data-stage="' + (stageName || '').replace(/"/g, '&quot;') + '">';
                html += '<div class="lm-kanban-col-hd"><span>' + (stageName || '—') + '</span><span class="badge">' + leads.length + '</span></div>';
                html += '<div class="lm-kanban-col-bd" data-stage="' + (stageName || '').replace(/"/g, '&quot;') + '">';
                leads.forEach(function(lead) {
                    html += '<div class="lm-kanban-card" draggable="true" data-lead-id="' + lead.id + '" data-stage="' + (lead.stage || '').replace(/"/g, '&quot;') + '">';
                    html += '<div class="card-row"><i class="ni ni-single-02"></i><span class="card-name">' + (lead.name || '—').replace(/</g, '&lt;') + '</span></div>';
                    html += '<div class="card-row"><i class="ni ni-mobile-button"></i><span class="card-meta">' + (lead.phone || '—').replace(/</g, '&lt;') + '</span></div>';
                    if (lead.source_label) html += '<div class="card-row"><i class="ni ni-tag"></i><span class="card-meta">' + (lead.source_label || '').replace(/</g, '&lt;') + '</span></div>';
                    html += '<div class="card-actions">';
                    html += '<a href="' + (lead.show_url || lead.edit_url || '#') + '" class="btn-view" title="{{ __("View") }}"><i class="ni ni-single-02"></i></a>';
                    html += '<a href="' + (lead.edit_url || '#') + '" class="btn-edit" title="{{ __("Edit") }}"><i class="ni ni-ruler-pencil"></i></a>';
                    html += '</div></div>';
                });
                html += '</div></div>';
            });
            kanbanBoard.innerHTML = html;
            kanbanBoard.className = 'lm-kanban';
            kanbanBoard.classList.remove('d-none');
            if (total === 0 && (data.stages || []).length === 0) kanbanEmpty.classList.remove('d-none');
            initKanbanDragDrop();
        })
        .catch(function() {
            kanbanLoading.classList.add('d-none');
            kanbanEmpty.classList.remove('d-none');
        });
    });

    backToTableBtn.addEventListener('click', function() {
        kanbanView.classList.add('d-none');
        kanbanHd.classList.add('d-none');
        tableView.classList.remove('d-none');
        tableHd.classList.remove('d-none');
    });
});
</script>
@endpush
