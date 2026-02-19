{{-- LeadBot module shared UI styles (Bootstrap/Argon friendly) --}}
<style>
    .lb-scope {
        --lb-pad: 1.5rem;
        --lb-border: #e2e8f0;
        --lb-bg: #f9fafb;
        --lb-text: #0f172a;
        --lb-muted: #64748b;
        --lb-primary: #4f46e5;
        --lb-primary-2: #7c3aed;
        --lb-card-radius: 14px;
        --lb-soft: rgba(79, 70, 229, .14);
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
        padding: 1.6rem var(--lb-pad) 1.25rem;
        background: #fff;
        border-bottom: 1px solid var(--lb-border);
    }
    .lb-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }
    .lb-title {
        font-size: 1.35rem;
        font-weight: 800;
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
        border-radius: 12px;
        background: linear-gradient(135deg, var(--lb-primary) 0%, var(--lb-primary-2) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.05rem;
        flex-shrink: 0;
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.18);
    }
    .lb-subtitle {
        display: block;
        font-size: 0.85rem;
        color: var(--lb-muted);
        margin-top: 0.25rem;
        font-weight: 500;
    }
    .lb-count {
        font-weight: 600;
        color: var(--lb-muted);
        font-size: 0.9rem;
    }

    .lb-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 0.75rem; }
    .lb-search { position: relative; width: 290px; max-width: 100%; }
    .lb-search input {
        padding-left: 2.5rem;
        border-radius: 12px;
        border: 1px solid var(--lb-border);
        font-size: 0.95rem;
        height: 42px;
        background: #fff;
    }
    .lb-search input:focus {
        border-color: var(--lb-primary);
        box-shadow: 0 0 0 4px var(--lb-soft);
    }
    .lb-search .lb-search-ico {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
        pointer-events: none;
    }

    .lb-btn-primary {
        background: linear-gradient(135deg, var(--lb-primary) 0%, var(--lb-primary-2) 100%);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.55rem 1rem;
        font-weight: 700;
        font-size: 0.95rem;
        height: 42px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 12px 30px rgba(79, 70, 229, .18);
    }
    .lb-btn-primary:hover { color: #fff; opacity: .98; transform: translateY(-1px); }
    .lb-btn-soft {
        background: #fff;
        color: #0f172a;
        border: 1px solid var(--lb-border);
        border-radius: 12px;
        padding: 0.55rem 0.95rem;
        font-weight: 700;
        font-size: 0.95rem;
        height: 42px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .lb-btn-soft:hover { background: #f8fafc; border-color: #cbd5e1; }
    .lb-btn-danger {
        background: #fff;
        color: #b91c1c;
        border: 1px solid rgba(185, 28, 28, .25);
        border-radius: 12px;
        padding: 0.55rem 0.95rem;
        font-weight: 800;
        font-size: 0.95rem;
        height: 42px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .lb-btn-danger:hover { background: #fee2e2; border-color: rgba(185, 28, 28, .35); }

    .lb-body { padding: 0; background: var(--lb-bg); min-height: 200px; }
    .lb-flash { padding: 1rem var(--lb-pad) 0; }

    .lb-section {
        margin: 0 var(--lb-pad) 1.25rem;
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--lb-border);
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
        overflow: hidden;
    }
    .lb-section-hd {
        padding: 1rem 1rem;
        border-bottom: 1px solid var(--lb-border);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
    }
    .lb-section-title { margin: 0; font-weight: 800; color: var(--lb-text); font-size: 1.05rem; }
    .lb-section-bd { padding: 1rem 1rem; }

    .lb-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .32rem .6rem;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        border: 1px solid var(--lb-border);
        color: #334155;
        background: #fff;
    }
    .lb-pill.primary { border-color: rgba(79,70,229,.25); background: rgba(79,70,229,.08); color: #3730a3; }
    .lb-pill.info { border-color: rgba(14,165,233,.25); background: rgba(14,165,233,.08); color: #075985; }

    .lb-table thead th {
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: #64748b;
        border-top: 0;
        border-bottom: 1px solid var(--lb-border);
    }
    .lb-table tbody td { vertical-align: middle; border-top: 1px solid #f1f5f9; }
    .lb-truncate { max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .lb-actions { display: flex; justify-content: flex-end; gap: .5rem; }
    .lb-icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid var(--lb-border);
        background: #fff;
        color: #334155;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .15s;
    }
    .lb-icon-btn:hover { background:#f8fafc; border-color:#cbd5e1; color:#0f172a; }
    .lb-icon-btn.danger { color:#b91c1c; border-color: rgba(185,28,28,.22); }
    .lb-icon-btn.danger:hover { background:#fee2e2; border-color: rgba(185,28,28,.34); }

    .lb-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        padding: 1rem var(--lb-pad) 0;
    }
    @media (max-width: 992px) { .lb-stats { grid-template-columns: 1fr; } }
    .lb-stat {
        background: #fff;
        border: 1px solid var(--lb-border);
        border-radius: 14px;
        padding: 1.05rem 1.05rem;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .lb-stat .v { font-size: 1.6rem; font-weight: 900; color: var(--lb-text); line-height: 1.1; }
    .lb-stat .l { font-size: .85rem; color: var(--lb-muted); margin-top: .25rem; font-weight: 700; }

    /* Task builder */
    .lb-task-card { border: 1px solid var(--lb-border); border-radius: 14px; overflow: hidden; }
    .lb-task-card:hover { border-color: #cbd5e1; box-shadow: 0 10px 30px rgba(0,0,0,.05); }
    .lb-task-hd {
        padding: .9rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        background: #fff;
        border-bottom: 1px solid var(--lb-border);
    }
    .lb-task-meta { display: flex; align-items: center; gap: .75rem; min-width: 0; }
    .lb-drag { cursor: grab; color:#94a3b8; }
    .lb-task-title { font-weight: 900; color: var(--lb-text); line-height: 1.2; margin:0; }
    .lb-task-sub { font-size: .82rem; color: var(--lb-muted); }
    .lb-task-bd { padding: 1rem 1rem; background: #fff; }
    .lb-collapse-btn {
        border: 1px solid var(--lb-border);
        background: #fff;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color:#334155;
    }
    .lb-collapse-btn:hover { background:#f8fafc; border-color:#cbd5e1; }
</style>

