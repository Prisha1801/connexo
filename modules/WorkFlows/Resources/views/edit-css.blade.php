<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .floating-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #007bff;
        color: #fff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease-in-out;
    }

    .floating-button:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    .variable-selector {
        margin-bottom: 10px;
    }

    .select2-container--default .select2-selection--single {
        background-color: #fff;
        border: 1px solid #eedbdb;
        border-radius: 10px;
        font-size: small;
        font-weight: 500;
        height: 45px;
        /* Increased height */
        padding: 6px 12px;
        /* Added some padding */
        line-height: 26px;
        /* Aligns text vertically */
    }

    .select2-selection select2-selection--multiple {
        background-color: #fff;
        border: 1px solid #eedbdb;
        border-radius: 10px;
        font-size: small;
        font-weight: 500;
        height: 45px;
        /* Increased height */
        padding: 6px 12px;
        /* Added some padding */
        line-height: 26px;
        /* Aligns text vertically */
    }

    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #f1fbf3;
        cursor: default;
    }

    /* Optional: Ensure the arrow aligns properly */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
        right: 10px;
    }
</style>
<style>
    .phone-input-container {
        position: relative;
    }

    .variable-tag {
        background-color: #e3f2fd;
        border: 1px solid #bbdefb;
        border-radius: 4px;
        padding: 2px 6px;
        margin: 2px;
        display: inline-block;
        color: #1565c0;
        font-weight: 500;
    }

    .phone-preview {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 8px 12px;
        margin-top: 8px;
        border: 1px dashed #dee2e6;
        min-height: 38px;
    }

    .insert-btn {
        background-color: #4a6cf7;
        color: white;
        border: none;
        transition: all 0.3s;
    }

    .insert-btn:hover {
        background-color: #3b5be3;
        transform: translateY(-2px);
    }

    .url-input-container {
        position: relative;
    }

    .url-preview {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 8px 12px;
        margin-top: 8px;
        border: 1px dashed #dee2e6;
        min-height: 38px;
        font-family: monospace;
    }

    .url-preview .variable-tag { font-family: inherit; }

    /* LeadBot-style (teal) in lb-scope */
    .lb-scope .form-control {
        border-radius: 12px;
        min-height: 44px;
        border-color: var(--lb-border);
    }
    .lb-scope .form-control:focus {
        border-color: var(--lb-primary);
        box-shadow: 0 0 0 3px var(--lb-soft);
    }
    .lb-scope .task-card {
        border: 1px solid var(--lb-border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
    }
    .lb-scope .task-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 10px 30px rgba(0,0,0,.05);
    }
    .lb-scope .task-card .card-footer {
        background: #fff;
        border-top: 1px solid var(--lb-border);
    }
    .lb-scope .task-card .add-task-btn {
        background: linear-gradient(135deg, var(--lb-primary) 0%, var(--lb-primary-2) 100%);
        color: #fff;
        border: none;
        border-radius: 50%;
        font-size: 1.5rem;
        width: 48px;
        height: 48px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.2);
    }
    .lb-scope .task-card .add-task-btn:hover { color: #fff; opacity: .95; }
    .lb-scope .dropdown-menu {
        border-radius: 12px;
        border: 1px solid var(--lb-border);
        box-shadow: 0 20px 50px rgba(0,0,0,.12);
    }
    .lb-scope .variable-tag {
        background: #d1fae5;
        border: 1px solid #a7f3d0;
        color: #047857;
        border-radius: 6px;
        padding: 2px 8px;
        font-weight: 600;
        font-size: .75rem;
    }
    .lb-scope .insert-btn, .lb-scope .insert-variable-btn {
        background: linear-gradient(135deg, var(--lb-primary) 0%, var(--lb-primary-2) 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.4rem 0.75rem;
        font-weight: 600;
        font-size: 0.8125rem;
    }
    .lb-scope .insert-btn:hover, .lb-scope .insert-variable-btn:hover { color: #fff; opacity: .95; }

    /* Classy workflow design – refined, attractive */
    .workflow-classy {
        --lb-pad: 1.5rem;
        --lb-border: #e5e7eb;
        --lb-bg: #fafbfc;
        --lb-text: #1f2937;
        --lb-muted: #6b7280;
        --lb-primary: #6366f1;
        --lb-primary-2: #4f46e5;
        --lb-accent: #8b5cf6;
        --lb-soft: rgba(99, 102, 241, .12);
        --lb-card-radius: 10px;
    }
    .workflow-classy .lb-body {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    }
    .workflow-classy .lb-card {
        border-radius: 12px;
        border: 1px solid rgba(226, 232, 240, .8);
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 4px 12px rgba(0,0,0,.03);
    }
    .workflow-classy .lb-page-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-bottom: 1px solid rgba(226, 232, 240, .6);
    }
    .workflow-classy .lb-title .lb-icon {
        border-radius: 10px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        box-shadow: 0 4px 14px rgba(99, 102, 241, .35);
    }
    .workflow-classy .lb-section {
        border-radius: 12px;
        border: 1px solid rgba(226, 232, 240, .8);
        box-shadow: 0 1px 2px rgba(0,0,0,.03);
        background: #fff;
    }
    .workflow-classy .lb-section-hd {
        background: linear-gradient(180deg, #fafbfc 0%, #fff 100%);
        border-bottom: 1px solid #f1f5f9;
    }
    .workflow-classy .form-control,
    .workflow-classy .lb-scope .form-control {
        border-radius: 8px;
        border-color: #e2e8f0;
        transition: border-color .2s, box-shadow .2s;
    }
    .workflow-classy .form-control:focus,
    .workflow-classy .lb-scope .form-control:focus {
        border-color: #818cf8;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
    }
    .workflow-classy .lb-btn-primary {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(99, 102, 241, .3);
    }
    .workflow-classy .lb-btn-primary:hover {
        box-shadow: 0 4px 12px rgba(99, 102, 241, .4);
        transform: translateY(-1px);
    }
    .workflow-classy .lb-btn-soft {
        border-radius: 8px;
    }
    .workflow-classy .lb-btn-danger {
        border-radius: 8px;
    }
    .workflow-classy .task-card,
    .workflow-classy .lb-scope .task-card {
        border-radius: 12px;
        border: 1px solid rgba(226, 232, 240, .9);
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: box-shadow .25s, border-color .25s;
    }
    .workflow-classy .task-card:hover {
        border-color: rgba(129, 140, 248, .4);
        box-shadow: 0 4px 16px rgba(99, 102, 241, .08);
    }
    .workflow-classy .task-card .card-header {
        background: linear-gradient(180deg, #fafbfc 0%, #fff 100%);
        border-bottom: 1px solid #f1f5f9;
    }
    .workflow-classy .task-card .card-body {
        background: #fff;
    }
    .workflow-classy .task-card .card-footer {
        background: linear-gradient(0deg, #f8fafc 0%, #fff 100%);
        border-top: 1px solid #f1f5f9;
    }
    .workflow-classy .add-task-btn,
    .workflow-classy .task-card .add-task-btn {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        border-radius: 12px !important;
        width: 48px !important;
        height: 48px !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, .35) !important;
        transition: transform .2s, box-shadow .2s !important;
    }
    .workflow-classy .add-task-btn:hover,
    .workflow-classy .task-card .add-task-btn:hover {
        transform: scale(1.05) !important;
        box-shadow: 0 6px 20px rgba(99, 102, 241, .45) !important;
    }
    .workflow-classy .lb-icon-btn {
        border-radius: 8px;
        transition: background .2s, color .2s;
    }
    .workflow-classy .lb-icon-btn:hover {
        background: rgba(99, 102, 241, .08) !important;
        color: #4f46e5 !important;
    }
    .workflow-classy .lb-pill.primary {
        background: rgba(99, 102, 241, .15);
        color: #4338ca;
    }
    .workflow-classy .lb-pill.info {
        background: rgba(139, 92, 246, .12);
        color: #6d28d9;
    }
    .workflow-classy .dropdown-menu {
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0,0,0,.12);
        border: 1px solid #f1f5f9;
    }
    .workflow-classy .insert-btn,
    .workflow-classy .insert-variable-btn,
    .workflow-classy .lb-scope .insert-btn,
    .workflow-classy .lb-scope .insert-variable-btn {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(99, 102, 241, .25);
    }
    .workflow-classy .insert-btn:hover,
    .workflow-classy .insert-variable-btn:hover {
        box-shadow: 0 4px 12px rgba(99, 102, 241, .35);
    }
</style>
