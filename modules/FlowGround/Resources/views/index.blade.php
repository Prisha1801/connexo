@extends('layouts.app', ['title' => __('WhatsApp Flow Management')])

@section('head')
@include('flow-ground::partials.ui')
<style>
    .fg-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1.25rem;
        padding: var(--fg-pad);
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
        background: var(--fg-bg);
    }
    @media (max-width: 1200px) { .fg-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 768px) { .fg-stats { grid-template-columns: 1fr; } }
    .fg-stat {
        background: #fff;
        border: 1px solid var(--fg-border);
        border-radius: 12px;
        padding: 1.25rem 1.25rem;
        transition: box-shadow .2s, border-color .2s;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .fg-stat:hover { box-shadow: 0 6px 18px rgba(0,0,0,.08); border-color: #cbd5e1; }
    .fg-stat .v { font-size: 1.75rem; font-weight: 900; color: var(--fg-text); line-height: 1.2; }
    .fg-stat .l { font-size: 0.8125rem; color: var(--fg-muted); margin-top: 0.25rem; font-weight: 700; letter-spacing: .01em; }
    .fg-stat.published .v { color: #059669; }
    .fg-stat.draft .v { color: #d97706; }
    .fg-stat.compact { padding: 0.95rem 1.05rem; }
    .fg-stat.compact .v { font-size: 1.35rem; font-weight: 800; }
    .fg-stat.compact .l { font-size: 0.75rem; font-weight: 700; }
    .fg-stat.deprecated .v { color: #6b7280; }

    /* Status badge: deprecated */
    .fg-badge.deprecated { background: #e5e7eb; color: #374151; }

    /* Bigger action icons (table row actions) */
    .fg-actions { gap: 0.65rem; }
    .fg-kebab-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: 1px solid rgba(148, 163, 184, .35);
        background: #fff;
        color: #475569;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .15s;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .fg-kebab-btn:hover { background:#f8fafc; border-color: rgba(148, 163, 184, .6); color:#0f172a; }
    .fg-kebab-btn i { font-size: 1.15rem; }
    .fg-kebab-dots { font-size: 1.35rem; line-height: 1; margin-top: -2px; }
    .fg-actions .dropdown-menu {
        border-radius: 12px;
        border: 1px solid var(--fg-border);
        box-shadow: 0 18px 48px rgba(0,0,0,.14);
        padding: .5rem;
        min-width: 220px;
    }
    .fg-actions .dropdown-item {
        border-radius: 10px;
        padding: .55rem .75rem;
        font-size: .9rem;
        display: flex;
        align-items: center;
        gap: .6rem;
    }
    .fg-actions .dropdown-item i { font-size: 1.05rem; width: 18px; text-align: center; opacity: .85; }
    .fg-actions .dropdown-item:hover { background:#f0fdfa; color: var(--fg-primary-2); }
    .fg-actions .dropdown-item.text-danger:hover { background:#fee2e2; color:#b91c1c; }
</style>
@endsection

@section('content')
<div class="container-fluid fg-wrap fg-scope">
    <div class="fg-card">
        <header class="fg-page-header">
            <div class="fg-top">
                <div>
                    <h1 class="fg-title">
                        <span class="fg-icon"><i class="ni ni-send"></i></span>
                        {{ __('WhatsApp Flow Management') }}
                        <span class="fg-count">({{ $flows->total() }} {{ __('Flows') }})</span>
                    </h1>
                    <span class="fg-subtitle">{{ __('Create, sync, publish and track submissions for your WhatsApp flows.') }}</span>
                </div>

                <div class="fg-toolbar">
                    <form action="{{ route('flow-ground.index') }}" method="GET" class="d-flex flex-wrap align-items-center" style="gap:.5rem;">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="fg-search">
                            <i class="ni ni-zoom-split-in fg-search-ico"></i>
                            <input type="search" name="search" class="form-control" placeholder="{{ __('Search flows...') }}" value="{{ request('search') }}">
                        </div>
                        <div class="dropdown">
                            <button class="fg-btn-primary dropdown-toggle" type="button" id="flowActionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ni ni-fat-add"></i> {{ __('Flow Actions') }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="flowActionsDropdown" style="border-radius: 12px; border: 1px solid var(--fg-border); box-shadow: 0 12px 42px rgba(0,0,0,.12); padding: .5rem;">
                                <a class="dropdown-item" href="{{ route('flow-ground.create') }}"><i class="ni ni-fat-add mr-2"></i> {{ __('Create New') }}</a>
                                <a class="dropdown-item" href="{{ route('flow-ground.sync') }}"><i class="ni ni-refresh mr-2"></i> {{ __('Sync from Meta') }}</a>
                                <a class="dropdown-item" href="{{ route('flow-ground.view_data') }}"><i class="ni ni-chart-bar-32 mr-2"></i> {{ __('View Flow Data') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </header>

        <div class="fg-body">
            <div class="fg-flash">@include('partials.flash')</div>

            <div class="fg-stats">
                <div class="fg-stat total">
                    <div class="v">{{ $total }}</div>
                    <div class="l">{{ __('Total Flows') }}</div>
                </div>
                <div class="fg-stat published">
                    <div class="v">{{ $published }}</div>
                    <div class="l">{{ __('Published') }}</div>
                </div>
                <div class="fg-stat draft">
                    <div class="v">{{ $draft }}</div>
                    <div class="l">{{ __('Draft') }}</div>
                </div>
                
                <div class="fg-stat compact deprecated">
                    <div class="v">{{ $deprecated }}</div>
                    <div class="l">{{ __('Deprecated') }}</div>
                </div>
                
            </div>

            <section class="fg-section">
                <div class="fg-section-hd">
                    <h3>{{ __('All Flows') }}</h3>
                    <form action="{{ route('flow-ground.index') }}" method="GET" class="d-inline">
                        @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                        <select name="status" class="form-control form-control-sm" style="border-radius:10px; border:1px solid var(--fg-border);" onchange="this.form.submit()">
                            <option value="">{{ __('Active (Draft + Published)') }}</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            <option value="deprecated" {{ request('status') == 'deprecated' ? 'selected' : '' }}>{{ __('Deprecated') }}</option>
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All (including deprecated)') }}</option>
                        </select>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table mb-0 fg-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>{{ __('Flow') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Submissions') }}</th>
                                <th class="text-right" style="width: 90px;">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($flows as $index => $flow)
                            <tr>
                                <td class="text-muted">{{ $flows->firstItem() + $index }}</td>
                                <td>
                                    <div class="flow-cell">
                                        <span class="flow-icon" style="width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#ccfbf1 0%,#99f6e4 100%);color:var(--fg-primary);display:inline-flex;align-items:center;justify-content:center;font-size:1.05rem;flex-shrink:0;"><i class="ni ni-chat-round"></i></span>
                                        <span>
                                            <span class="flow-name" style="font-weight:700;color:var(--fg-text);">{{ $flow->name }}</span>
                                            @if($flow->meta_flow_id)
                                                <span class="flow-id" style="font-size:.75rem;color:#94a3b8;margin-top:.2rem;display:block;">{{ $flow->meta_flow_id }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    @if(isset($flow->status) && $flow->status == 'published')
                                        <span class="fg-badge published">{{ __('Published') }}</span>
                                    @elseif(isset($flow->status) && $flow->status == 'deprecated')
                                        <span class="fg-badge deprecated">{{ __('Deprecated') }}</span>
                                    @else
                                        <span class="fg-badge draft">{{ __('Draft') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $count = isset($dataCounts[$flow->id]) ? (int) $dataCounts[$flow->id] : 0;
                                    @endphp
                                    @if($count > 0)
                                        <a href="{{ route('flow-ground.data', $flow) }}" class="text-decoration-none">
                                            <span class="badge bg-primary" style="border-radius:10px;">{{ $count }}</span>
                                        </a>
                                    @else
                                        <span class="text-muted">0</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="fg-actions justify-content-end">
                                        <div class="dropdown">
                                            <button
                                                type="button"
                                                class="fg-kebab-btn"
                                                title="{{ __('Actions') }}"
                                                aria-haspopup="true"
                                                aria-expanded="false"
                                                data-toggle="dropdown"
                                                data-bs-toggle="dropdown"
                                            >
                                                <span class="fg-kebab-dots" aria-hidden="true">⋮</span>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                @if(isset($flow->status) && $flow->status === 'draft' && !empty($flow->meta_flow_id))
                                                    <a class="dropdown-item" href="{{ route('flow-ground.publish', $flow) }}">
                                                        <i class="ni ni-send"></i> {{ __('Publish') }}
                                                    </a>
                                                @endif
                                                @if(!empty($flow->meta_flow_id))
                                                    <a class="dropdown-item" href="{{ route('flow-ground.preview', $flow) }}" target="_blank" rel="noopener">
                                                        <i class="ni ni-glasses-2"></i> {{ __('Preview') }}
                                                    </a>
                                                @endif
                                                <a class="dropdown-item" href="{{ route('flow-ground.data', $flow) }}">
                                                    <i class="ni ni-chart-bar-32"></i> {{ __('View data') }}
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <button
                                                    type="button"
                                                    class="dropdown-item text-danger js-fg-delete"
                                                    data-delete-url="{{ route('flow-ground.destroy', $flow) }}"
                                                    data-flow-name="{{ $flow->name }}"
                                                    data-meta-id="{{ $flow->meta_flow_id ?? '' }}"
                                                >
                                                    <i class="ni ni-fat-remove"></i> {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="fg-empty">
                                    {{ __('No flows yet.') }} <a href="{{ route('flow-ground.create') }}">{{ __('Create New') }}</a> {{ __('or') }} <a href="{{ route('flow-ground.sync') }}">{{ __('Sync from Meta') }}</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            @if($flows->hasPages())
            <div class="fg-pagination">
                {{ $flows->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Delete confirmation modal (Bootstrap) --}}
<div class="modal fade" id="fgDeleteModal" tabindex="-1" aria-labelledby="fgDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background:#fff;">
                <h5 class="modal-title" id="fgDeleteModalLabel">{{ __('Delete Flow') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                <div style="color: var(--fg-text); font-weight: 600; margin-bottom: .35rem;">
                    <span id="fgDeleteName">{{ __('this flow') }}</span>
                </div>
                <div class="text-muted" style="font-size:.9rem; line-height: 1.45;">
                    {{ __('Draft flows will be deleted on Meta. Published flows cannot be deleted by Meta and will be deprecated instead.') }}
                </div>
                <div id="fgDeleteMeta" class="mt-2" style="display:none;">
                    <span class="badge bg-light text-muted" style="border-radius:10px;">{{ __('Meta ID') }}: <span id="fgDeleteMetaId"></span></span>
                </div>
            </div>
            <div class="modal-footer" style="background:#fff;">
                <button type="button" class="fg-btn-soft" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form id="fgDeleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="fg-btn-primary" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); box-shadow:none;">
                        <i class="ni ni-fat-remove"></i> {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    function qs(sel) { return document.querySelector(sel); }
    function openModal() {
        var el = qs('#fgDeleteModal');
        if (!el) return;
        // Bootstrap 5
        if (window.bootstrap && bootstrap.Modal) {
            bootstrap.Modal.getOrCreateInstance(el).show();
            return;
        }
        // Bootstrap 4 fallback
        if (window.jQuery && jQuery.fn && jQuery.fn.modal) {
            jQuery(el).modal('show');
        }
    }

    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest ? e.target.closest('.js-fg-delete') : null;
        if (!btn) return;

        var url = btn.getAttribute('data-delete-url') || '';
        var name = btn.getAttribute('data-flow-name') || '';
        var metaId = btn.getAttribute('data-meta-id') || '';

        var form = qs('#fgDeleteForm');
        if (form) form.setAttribute('action', url);

        var nameEl = qs('#fgDeleteName');
        if (nameEl) nameEl.textContent = name ? ('"' + name + '"') : '{{ __("this flow") }}';

        var metaWrap = qs('#fgDeleteMeta');
        var metaEl = qs('#fgDeleteMetaId');
        if (metaWrap && metaEl) {
            if (metaId) {
                metaEl.textContent = metaId;
                metaWrap.style.display = '';
            } else {
                metaEl.textContent = '';
                metaWrap.style.display = 'none';
            }
        }

        openModal();
    });
})();
</script>
@endsection
