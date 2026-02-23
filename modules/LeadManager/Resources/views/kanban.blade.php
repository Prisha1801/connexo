@extends('layouts.app', ['title' => __('Lead Manager - Kanban')])

@section('title')
    <title>{{ __('Kanban') }}</title>
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
        .lm-kanban { display: grid; grid-auto-flow: column; grid-auto-columns: 300px; gap: 1.25rem; overflow-x: auto; padding: 1.25rem; min-height: 420px; background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 50%, #f1f5f9 100%); border-radius: 16px; margin: 0 -0.25rem; justify-content: start; }
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
        .lm-kanban-card .card-name { font-weight: 600; color: #1e293b; margin-bottom: 0.35rem; font-size: 0.95rem; }
        .lm-kanban-card .card-meta { font-size: 0.8rem; color: #64748b; line-height: 1.4; }
        .lm-kanban-card .card-actions { margin-top: 0.75rem; padding-top: 0.6rem; border-top: 1px solid #f1f5f9; display: flex; gap: 0.35rem; }
        .lm-kanban-card .card-actions .lb-icon-btn { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; transition: transform .15s; }
        .lm-kanban-card .card-actions .lb-icon-btn:hover { transform: scale(1.1); }
        .lm-kanban-card .card-actions .btn-view { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: #fff; border: none; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .lm-kanban-card .card-actions .btn-view:hover { color: #fff; transform: scale(1.1); }
        .nav-tabs .nav-link { color: #64748b; border: none; padding: 0.5rem 1rem; }
        .nav-tabs .nav-link.active { color: var(--lb-primary); font-weight: 600; border-bottom: 2px solid var(--lb-primary); }
    </style>
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-diagram-3"></i></span>
                        {{ __('Lead Manager') }}
                    </h1>
                    <span class="lb-subtitle">{{ $leadsByStage->flatten()->count() }} {{ __('Leads') }}</span>
                </div>
                <div class="lb-toolbar d-flex flex-wrap align-items-center gap-2">
                    <ul class="nav nav-tabs border-0" style="margin-bottom: -1px;">
                        <li class="nav-item"><a class="nav-link" href="{{ route('lead-manager.index') }}">{{ __('Table') }}</a></li>
                        <li class="nav-item"><a class="nav-link active" href="{{ route('lead-manager.kanban') }}">{{ __('Kanban') }}</a></li>
                    </ul>
                    <form method="GET" action="{{ route('lead-manager.kanban') }}" class="d-flex align-items-center" style="max-width: 260px;">
                        @foreach (request()->except('search') as $k => $v)
                            @if (!is_array($v))<input type="hidden" name="{{ $k }}" value="{{ $v }}">@endif
                        @endforeach
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search leads…') }}" class="form-control" style="height: 40px; border-radius: 10px 0 0 10px;">
                        <button type="submit" class="lb-btn-soft" style="height: 40px; border-radius: 0 10px 10px 0;"><i class="ni ni-zoom-split-in"></i></button>
                    </form>
                    <button type="button" class="lb-btn-soft" data-bs-toggle="modal" data-bs-target="#addStageModal">
                        <i class="ni ni-fat-add"></i> {{ __('Add Stage') }}
                    </button>
                    <div class="dropdown">
                        <button class="lb-btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="ni ni-fat-add"></i> {{ __('Add Lead') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('lead-manager.create') }}"><i class="ni ni-single-02 me-2"></i>{{ __('Add Lead manually') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('lead-manager.import') }}"><i class="ni ni-archive-2 me-2"></i>{{ __('Import Leads') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </header>

        <div class="lb-body">
            @if (session('success'))
                <div class="lb-flash"><div class="alert alert-success mb-0">{{ session('success') }}</div></div>
            @endif
            @if (session('error'))
                <div class="lb-flash"><div class="alert alert-danger mb-0">{{ session('error') }}</div></div>
            @endif

            <div class="lb-stats">
                <div class="lb-stat lm-stat-total"><div class="v">{{ (int) ($stats['total'] ?? 0) }}</div><div class="l">{{ __('Total Leads') }}</div></div>
                <div class="lb-stat lm-stat-won"><div class="v">{{ (int) ($stats['won'] ?? 0) }}</div><div class="l">{{ __('Won') }}</div></div>
                <div class="lb-stat lm-stat-active"><div class="v">{{ (int) ($stats['active'] ?? 0) }}</div><div class="l">{{ __('Active') }}</div></div>
                <div class="lb-stat lm-stat-lost"><div class="v">{{ (int) ($stats['lost'] ?? 0) }}</div><div class="l">{{ __('Lost') }}</div></div>
            </div>

            <section class="lb-section">
                <div class="lb-section-hd flex-wrap">
                    <h3 class="lb-section-title mb-0">{{ __('Kanban View') }}</h3>
                    <form method="GET" action="{{ route('lead-manager.kanban') }}" class="d-flex flex-wrap align-items-center gap-2">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="source" class="form-control" style="height: 38px; border-radius: 10px; min-width: 140px;">
                            <option value="">{{ __('All Sources') }}</option>
                            @foreach ($sources ?? [] as $k => $v)   
                                <option value="{{ $k }}" {{ request('source') == $k ? 'selected' : '' }}>{{ $v }}</option>
                            @endforeach
                        </select>
                        <select name="assigned" class="form-control" style="height: 38px; border-radius: 10px; min-width: 130px;">
                            <option value="">{{ __('All Agents') }}</option>
                            <option value="unassigned" {{ request('assigned') === 'unassigned' ? 'selected' : '' }}>{{ __('Unassigned') }}</option>
                            @foreach ($agents ?? [] as $a)
                                <option value="{{ $a->id }}" {{ request('assigned') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="lb-btn-soft" style="height: 38px;">{{ __('Filter') }}</button>
                        <a href="{{ route('lead-manager.kanban') }}" class="lb-btn-soft" style="height: 38px;">{{ __('Reset') }}</a>
                    </form>
                </div>
                <div class="lb-section-bd p-3">
                    @if ($leadsByStage->flatten()->isNotEmpty() || $stages->isNotEmpty())
                    <div class="lm-kanban" id="kanban-board">
                        @foreach ($stages as $stageModel)
                            @php $stageKey = $stageModel->name; $stageLeads = $leadsByStage->get($stageKey, collect()); @endphp
                            <div class="lm-kanban-col" data-stage="{{ $stageKey }}">
                                <div class="lm-kanban-col-hd">
                                    <span>{{ $stageKey }}</span>
                                    <span class="badge bg-secondary">{{ $stageLeads->count() }}</span>
                                </div>
                                <div class="lm-kanban-col-bd" data-stage="{{ $stageKey }}">
                                    @foreach ($stageLeads as $lead)
                                        <div class="lm-kanban-card" draggable="true" data-lead-id="{{ $lead->id }}" data-stage="{{ $lead->stage }}">
                                            <div class="card-name"><a href="{{ route('lead-manager.show', $lead) }}" class="text-decoration-none text-dark">{{ $lead->contact?->name ?? '—' }}</a></div>
                                            <div class="card-meta">{{ $lead->contact?->phone ?? '—' }} · {{ $lead->source_label }}</div>
                                            @if ($lead->user)
                                                <div class="card-meta">{{ __('Assigned to') }} {{ $lead->user->name }}</div>
                                            @endif
                                            <div class="card-actions">
                                                <a href="{{ route('lead-manager.show', $lead) }}" class="lb-icon-btn btn-sm btn-view" title="{{ __('View') }}"><i class="ni ni-single-02"></i></a>
                                                <a href="{{ route('lead-manager.edit', $lead) }}" class="lb-icon-btn btn-sm" title="{{ __('Edit') }}"><i class="ni ni-ruler-pencil"></i></a>
                                                <form action="{{ route('lead-manager.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this lead?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="lb-icon-btn danger btn-sm" title="{{ __('Delete') }}"><i class="ni ni-fat-remove"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                    <div class="lb-empty">
                        <div class="lb-empty-icon"><i class="ni ni-single-02"></i></div>
                        <h3 class="mb-2" style="color: var(--lb-text); font-size: 1.1rem;">{{ __('No leads yet') }}</h3>
                        <p class="mb-4">{{ __('Add a lead or create stages to get started.') }}</p>
                        <a href="{{ route('lead-manager.create') }}" class="lb-btn-primary mr-2"><i class="ni ni-fat-add"></i> {{ __('Add Lead') }}</a>
                        <button type="button" class="lb-btn-soft" data-bs-toggle="modal" data-bs-target="#addStageModal"><i class="ni ni-fat-add"></i> {{ __('Add Stage') }}</button>
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>

<div class="modal fade" id="addStageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Add Stage') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('lead-manager.stages.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stage_name" class="form-label">{{ __('Stage name') }}</label>
                        <input type="text" class="form-control" id="stage_name" name="name" required placeholder="{{ __('e.g. Follow-up') }}" style="border-radius: 12px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="lb-btn-soft" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="lb-btn-primary"><i class="ni ni-check-bold"></i> {{ __('Create Stage') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var kanban = document.getElementById('kanban-board');
    if (!kanban) return;
    var cols = kanban.querySelectorAll('.lm-kanban-col');
    var cards = kanban.querySelectorAll('.lm-kanban-card');
    var draggedCard = null;

    cards.forEach(function(card) {
        card.addEventListener('dragstart', function(e) {
            draggedCard = card;
            card.classList.add('dragging');
            e.dataTransfer.setData('text/plain', card.dataset.leadId);
            e.dataTransfer.effectAllowed = 'move';
        });
        card.addEventListener('dragend', function() {
            card.classList.remove('dragging');
            cols.forEach(function(c) { c.classList.remove('drag-over'); });
            draggedCard = null;
        });
    });

    cols.forEach(function(col) {
        var bd = col.querySelector('.lm-kanban-col-bd');
        var stage = col.dataset.stage;
        bd.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            col.classList.add('drag-over');
        });
        bd.addEventListener('dragleave', function() {
            col.classList.remove('drag-over');
        });
        bd.addEventListener('drop', function(e) {
            e.preventDefault();
            col.classList.remove('drag-over');
            var leadId = e.dataTransfer.getData('text/plain');
            if (!leadId || !draggedCard) return;
            if (stage === draggedCard.dataset.stage) return;

            fetch('{{ url("lead-manager") }}/' + leadId + '/stage', {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ stage: stage })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var oldStage = draggedCard.dataset.stage;
                    draggedCard.dataset.stage = stage;
                    bd.appendChild(draggedCard);
                    var badge = col.querySelector('.lm-kanban-col-hd .badge');
                    if (badge) badge.textContent = bd.querySelectorAll('.lm-kanban-card').length;
                    var oldCol = kanban.querySelector('.lm-kanban-col[data-stage="' + oldStage + '"]');
                    if (oldCol) {
                        var obadge = oldCol.querySelector('.lm-kanban-col-hd .badge');
                        if (obadge) obadge.textContent = oldCol.querySelector('.lm-kanban-col-bd').querySelectorAll('.lm-kanban-card').length;
                    }
                }
            })
            .catch(function() { location.reload(); });
        });
    });
});
</script>
@endpush
