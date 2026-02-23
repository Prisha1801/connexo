{{-- WorkFlows shared UI styles (used by WorkFlows and Lead Manager; Bootstrap/Argon friendly) --}}
<style>
    .lb-scope {
        --lb-pad: 1.5rem;
        --lb-border: #e2e8f0;
        --lb-bg: #f9fafb;
        --lb-text: #111827;
        --lb-muted: #6b7280;
        --lb-primary: #0d9488;
        --lb-primary-2: #0f766e;
        --lb-card-radius: 12px;
        --lb-soft: rgba(13, 148, 136, .12);
    }

    .lb-wrap { padding-top: 5.6rem; padding-bottom: 1.5rem; }
    .lb-card {
        background: #fff;
        border: 1px solid var(--lb-border);
        border-radius: var(--lb-card-radius);
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,.06);
        width: 100%;
    }

    .lb-page-header {
        padding: 1.75rem var(--lb-pad) 1.5rem;
        background: #fff;
        border-bottom: 1px solid var(--lb-border);
    }
    .lb-page-header .lb-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .lb-title {
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--lb-text);
        letter-spacing: -0.02em;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        line-height: 1.2;
    }
    .lb-title .lb-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--lb-primary) 0%, var(--lb-primary-2) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.1rem;
        flex-shrink: 0;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.18);
    }
    .lb-subtitle {
        display: block;
        font-size: 0.8125rem;
        color: var(--lb-muted);
        margin-top: 0.25rem;
        font-weight: 500;
    }

    .lb-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; }
    .lb-search { position: relative; width: 260px; max-width: 100%; }
    .lb-search input {
        padding-left: 2.5rem;
        border-radius: 10px;
        border: 1px solid var(--lb-border);
        font-size: 0.875rem;
        height: 40px;
        background: #fff;
    }
    .lb-search input:focus {
        border-color: var(--lb-primary);
        box-shadow: 0 0 0 3px var(--lb-soft);
    }
    .lb-search .lb-search-ico {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
    }

    .lb-btn-primary {
        background: linear-gradient(135deg, var(--lb-primary) 0%, var(--lb-primary-2) 100%);
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
    .lb-btn-primary:hover { color: #fff; opacity: .97; transform: translateY(-1px); }
    .lb-btn-soft {
        background: #fff;
        color: #334155;
        border: 1px solid var(--lb-border);
        border-radius: 10px;
        padding: 0.5rem 0.9rem;
        font-weight: 600;
        font-size: 0.875rem;
        height: 40px;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }
    .lb-btn-soft:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }

    .lb-body { padding: 0; background: var(--lb-bg); min-height: 200px; }
    .lb-flash { padding: 1rem var(--lb-pad) 0; }

    .lb-stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1.25rem var(--lb-pad);
    }
    .lb-stat {
        flex: 1;
        min-width: 120px;
        background: #fff;
        border-radius: var(--lb-card-radius);
        padding: 1rem 1.25rem;
        border: 1px solid var(--lb-border);
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .lb-stat .v { font-size: 1.5rem; font-weight: 700; color: var(--lb-text); }
    .lb-stat .l { font-size: 0.8125rem; color: var(--lb-muted); margin-top: 0.25rem; }

    .lb-section {
        margin: 0 var(--lb-pad) 1.5rem;
        background: #fff;
        border-radius: var(--lb-card-radius);
        border: 1px solid var(--lb-border);
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .lb-section-hd {
        padding: 1.1rem 1.25rem;
        background: #fff;
        border-bottom: 1px solid var(--lb-border);
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        align-items: center;
        justify-content: space-between;
    }
    .lb-section-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--lb-text);
        letter-spacing: -0.01em;
    }
    .lb-section-bd { padding: 1.25rem; }

    .lb-table thead th {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--lb-muted);
        padding: 0.875rem 1.25rem;
        border-bottom: 1px solid var(--lb-border);
        background: #f8fafc;
    }
    .lb-table tbody td {
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
    }
    .lb-table tbody tr:nth-child(even) { background: #fafbfc; }
    .lb-table tbody tr:hover { background: #f0fdfa !important; }
    .lb-table tbody tr:last-child td { border-bottom: none; }

    .lb-icon-btn {
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
    .lb-icon-btn:hover { background: #f1f5f9; color: #1e293b; }
    .lb-icon-btn.danger:hover { background: #fee2e2; color: #dc2626; }
    .lb-icon-btn.success:hover { background: #d1fae5; color: #059669; }

    .lb-empty {
        text-align: center;
        padding: 3rem 1.25rem;
        color: var(--lb-muted);
        font-size: 0.9375rem;
        line-height: 1.5;
    }
    .lb-empty a { color: var(--lb-primary); font-weight: 700; }

    .nav-tabs .nav-link { color: #64748b; border: none; padding: 0.5rem 1rem; }
    .nav-tabs .nav-link.active { color: var(--lb-primary); font-weight: 600; border-bottom: 2px solid var(--lb-primary); }

    @media (max-width: 768px) {
        .lb-page-header { padding-left: 1rem; padding-right: 1rem; }
        .lb-section { margin-left: 1rem; margin-right: 1rem; }
        .lb-flash { padding-left: 1rem; padding-right: 1rem; }
        .lb-search { width: 100%; }
    }
</style>
