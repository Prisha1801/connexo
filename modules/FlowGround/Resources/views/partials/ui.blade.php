{{-- FlowGround module shared UI styles (Bootstrap/Argon friendly) --}}
<style>
    /* Scoped variables to avoid CSS mixing with other modules */
    .fg-scope {
        --fg-pad: 1.5rem;
        --fg-border: #e2e8f0;
        --fg-bg: #f9fafb;
        --fg-text: #111827;
        --fg-muted: #6b7280;
        --fg-primary: #0d9488;
        --fg-primary-2: #0f766e;
        --fg-card-radius: 12px;
        --fg-soft: rgba(13, 148, 136, .12);
    }

    .fg-wrap { padding-top: 5.6rem; padding-bottom: 1.5rem; }
    .fg-card {
        background: #fff;
        border: 1px solid var(--fg-border);
        border-radius: var(--fg-card-radius);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        width: 100%;
    }

    .fg-page-header {
        padding: 1.75rem var(--fg-pad) 1.5rem;
        background: #fff;
        border-bottom: 1px solid var(--fg-border);
    }
    .fg-page-header .fg-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .fg-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--fg-text);
        letter-spacing: -0.02em;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        line-height: 1.2;
    }
    .fg-title .fg-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--fg-primary) 0%, var(--fg-primary-2) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.18);
    }
    .fg-subtitle {
        display: block;
        font-size: 0.8125rem;
        color: var(--fg-muted);
        margin-top: 0.25rem;
        font-weight: 500;
    }
    .fg-count {
        font-weight: 500;
        color: var(--fg-muted);
        font-size: 0.875rem;
    }

    .fg-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; }
    .fg-search { position: relative; width: 260px; max-width: 100%; }
    .fg-search input {
        padding-left: 2.5rem;
        border-radius: 10px;
        border: 1px solid var(--fg-border);
        font-size: 0.875rem;
        height: 40px;
        background: #fff;
    }
    .fg-search input:focus {
        border-color: var(--fg-primary);
        box-shadow: 0 0 0 3px var(--fg-soft);
    }
    .fg-search .fg-search-ico {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
    }

    .fg-btn-primary {
        background: linear-gradient(135deg, var(--fg-primary) 0%, var(--fg-primary-2) 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.875rem;
        height: 40px;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        box-shadow: 0 8px 24px rgba(13, 148, 136, .18);
    }
    .fg-btn-primary:hover { color: #fff; opacity: .97; transform: translateY(-1px); }
    .fg-btn-soft {
        background: #fff;
        color: #334155;
        border: 1px solid var(--fg-border);
        border-radius: 10px;
        padding: 0.5rem 0.9rem;
        font-weight: 600;
        font-size: 0.875rem;
        height: 40px;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .fg-btn-soft:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

    .fg-body { padding: 0; background: var(--fg-bg); min-height: 200px; }
    .fg-flash { padding: 1rem var(--fg-pad) 0; }

    .fg-section {
        margin: 0 var(--fg-pad) 1.5rem;
        background: #fff;
        border-radius: 12px;
        border: 1px solid var(--fg-border);
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .fg-section-hd {
        padding: 1.1rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid var(--fg-border);
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: center;
        justify-content: space-between;
    }
    .fg-section-hd h3 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--fg-text);
        letter-spacing: -0.01em;
    }

    .fg-table thead th {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--fg-muted);
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--fg-border);
        background: #f8fafc;
    }
    .fg-table tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .fg-table tbody tr:nth-child(even) { background: #fafbfc; }
    .fg-table tbody tr:hover { background: #f0fdfa !important; }
    .fg-table tbody tr:last-child td { border-bottom: none; }

    .fg-badge {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding: 0.35rem 0.7rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
    }
    .fg-badge.published { background: #d1fae5; color: #047857; }
    .fg-badge.draft { background: #ffedd5; color: #c2410c; }

    .fg-actions { display: inline-flex; align-items: center; gap: 0.5rem; }
    .fg-icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid transparent;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        transition: all .15s;
        cursor: pointer;
    }
    .fg-icon-btn:hover { background: #f1f5f9; color: #1e293b; }
    .fg-icon-btn.danger:hover { background: #fee2e2; color: #dc2626; }
    .fg-icon-btn.success:hover { background: #d1fae5; color: #059669; }
    .fg-icon-btn.info:hover { background: #e0f2fe; color: #0284c7; }

    .fg-empty {
        text-align: center;
        padding: 3rem 1.25rem;
        color: var(--fg-muted);
        font-size: 0.9375rem;
        line-height: 1.5;
    }
    .fg-empty a { color: var(--fg-primary); font-weight: 700; }

    .fg-pagination {
        padding: 1rem var(--fg-pad);
        background: #fff;
        border-top: 1px solid var(--fg-border);
    }

    @media (max-width: 768px) {
        .fg-page-header { padding-left: 1rem; padding-right: 1rem; }
        .fg-section { margin-left: 1rem; margin-right: 1rem; }
        .fg-flash { padding-left: 1rem; padding-right: 1rem; }
        .fg-search { width: 100%; }
    }
</style>
