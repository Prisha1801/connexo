@extends('layouts.app', ['title' => isset($edit) && $edit ? __('Edit Flow') : __('Create Flow')])

@section('head')
@include('flow-ground::partials.ui')
<style>
    /* First form section: clean underline inputs like screenshot */
    .fg-first-section { margin: var(--fg-pad) var(--fg-pad) 1rem; padding: 1.25rem 1.25rem 0.25rem; }
    .fg-first-section .control-label { font-size: .82rem; font-weight: 800; color: var(--fg-text); }
    .fg-first-section .form-control {
        border: 0;
        border-bottom: 1px solid #cbd5e1;
        border-radius: 0;
        padding-left: 0;
        padding-right: 0;
        height: 42px;
        background: transparent;
        box-shadow: none !important;
    }
    .fg-first-section .form-control::placeholder { color: #94a3b8; }
    .fg-first-section .form-control:focus {
        border-bottom-color: var(--fg-primary);
        box-shadow: 0 8px 0 -7px rgba(13,148,136,.35) !important;
    }
    .fg-first-section .input-group { border: 0; }
    .fg-first-section .ni-notification-70 { color: #94a3b8; }

    /* Select2: make it look like a dropdown */
    .fg-first-section .select2-container { width: 100% !important; }
    .fg-first-section .select2-container--default .select2-selection--single,
    .fg-first-section .select2-container--default .select2-selection--multiple {
        border: 0;
        border-bottom: 1px solid #cbd5e1;
        border-radius: 0;
        height: 42px;
        background: transparent;
        display: flex;
        align-items: center;
        padding-left: 0;
    }
    .fg-first-section .select2-container--default .select2-selection--single .select2-selection__rendered,
    .fg-first-section .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding-left: 0;
        color: #334155;
        font-size: .875rem;
        line-height: 42px;
    }
    .fg-first-section .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: 0;
    }
    /* dropdown is appended to body; use custom dropdown class to avoid affecting other Select2 */
    .fg-s2-dropdown.select2-dropdown {
        border: 1px solid var(--fg-border) !important;
        border-radius: 12px !important;
        box-shadow: 0 18px 48px rgba(0,0,0,.14) !important;
        overflow: hidden;
    }
    .fg-s2-dropdown .select2-results__option--highlighted.select2-results__option--selectable {
        background: #f0fdfa !important;
        color: var(--fg-primary-2) !important;
    }
    .fg-first-section .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        height: auto;
        padding: 0.25rem 0;
    }
    .fg-first-section .select2-container--default .select2-selection--multiple .select2-selection__choice {
        border: 1px solid rgba(13, 148, 136, .2);
        background: rgba(13, 148, 136, .08);
        color: #0f766e;
        border-radius: 999px;
        padding: 2px 10px;
        margin-top: 6px;
        font-weight: 600;
        font-size: .75rem;
    }
    .fg-first-section .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #0f766e;
        margin-right: 6px;
        border: 0 !important;
        background: transparent !important;
        padding: 0 !important;
        float: none !important;
        line-height: 1;
        opacity: .85;
    }
    .fg-first-section .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        opacity: 1;
        color: #dc2626;
    }
    /* add dropdown caret on multi-select */
    .fg-first-section .select2-container--default .select2-selection--multiple { position: relative; padding-right: 18px; }
    .fg-first-section .select2-container--default .select2-selection--multiple:after {
        content: "▾";
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-55%);
        color: #94a3b8;
        font-size: 0.95rem;
        pointer-events: none;
    }

    .fg-page { max-width: 1280px; margin: 0 auto; }
    .fg-header { margin-bottom: 1.75rem; }
    .fg-header h1 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0; letter-spacing: -0.02em; }
    .fg-banner { background: linear-gradient(135deg, #f0fdfa 0%, #ccfbf1 100%); border: 1px solid #99f6e4; border-radius: 14px; padding: 1.25rem 1.5rem; margin-bottom: 1.75rem; }
    .fg-banner h6 { font-size: 0.9375rem; font-weight: 600; color: #0f766e; margin: 0 0 0.35rem 0; }
    .fg-banner p { font-size: 0.8125rem; color: #0d9488; margin: 0; line-height: 1.5; opacity: .95; }
    .fg-banner a { color: #0f766e; font-weight: 600; }
    .fg-banner a:hover { text-decoration: underline; }
    .fg-form-section { margin-bottom: 1.75rem; }
    .fg-form-section label { font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
    .fg-form-section .form-control { border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.6rem 0.875rem; font-size: 0.875rem; }
    .fg-form-section .form-control:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12); }
    .fg-builder { display: grid; grid-template-columns: 280px 1fr 320px; gap: 0; min-height: 560px; background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    @media (max-width: 1200px) { .fg-builder { grid-template-columns: 260px 1fr; } .fg-preview-column { display: none; } .fg-config-panel { position: fixed; right: 0; top: 0; bottom: 0; width: 100%; max-width: 320px; z-index: 1000; box-shadow: -4px 0 20px rgba(0,0,0,.12); } }
    @media (max-width: 768px) { .fg-builder { grid-template-columns: 1fr; } }
    .fg-sidebar { background: #f8fafc; border-right: 1px solid #e2e8f0; padding: 1rem; overflow-y: auto; display: flex; flex-direction: column; }
    .fg-sidebar-groups { flex: 1; }
    .fg-sidebar-group { margin-bottom: 1.25rem; }
    .fg-sidebar-group:last-child { margin-bottom: 0; }
    .fg-sidebar-footer { margin-top: auto; padding-top: 1rem; border-top: 1px solid #e2e8f0; display: flex; flex-direction: column; gap: 0.5rem; }
    .fg-sidebar-title { font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; margin-bottom: 0.6rem; padding-bottom: 0.35rem; border-bottom: 1px solid #e2e8f0; }
    .fg-field-btn { display: flex; align-items: center; gap: 0.65rem; width: 100%; padding: 0.6rem 0.75rem; margin-bottom: 0.35rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.8125rem; color: #334155; cursor: pointer; transition: all .15s; text-align: left; }
    .fg-field-btn:hover { border-color: #0d9488; background: #f0fdfa; color: #0f766e; }
    .fg-field-btn .icon { width: 28px; height: 28px; border-radius: 6px; background: #f0fdfa; color: #0d9488; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; }
    .fg-field-btn.fg-btn-screen { background: #e0f2fe; border-color: #7dd3fc; }
    .fg-field-btn.fg-btn-screen:hover { background: #bae6fd; border-color: #0d9488; }
    .fg-field-btn.fg-btn-screen .icon { background: #0ea5e9; color: #fff; }
    .fg-canvas-area { padding: 1.25rem; overflow-y: auto; display: flex; flex-direction: column; }
    .fg-screen-tabs { display: flex; flex-wrap: wrap; gap: 0.35rem; margin-bottom: 0.75rem; }
    .fg-screen-tab { padding: 0.5rem 0.875rem; font-size: 0.8125rem; font-weight: 500; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #64748b; cursor: pointer; transition: all .15s; }
    .fg-screen-tab:hover { border-color: #0d9488; color: #0f766e; }
    .fg-screen-tab.active { background: #0d9488; border-color: #0d9488; color: #fff; }
    .fg-screen-tab .tab-remove { margin-left: 0.35rem; opacity: 0.7; }
    .fg-screen-tab .tab-remove:hover { opacity: 1; }
    .fg-canvas-title { font-size: 0.8125rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; }
    .fg-canvas { flex: 1; min-height: 320px; background: #fafafa; border: 2px dashed #e2e8f0; border-radius: 12px; padding: 1rem; }
    .fg-screen { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,.04); height: 100%; display: flex; flex-direction: column; }
    .fg-screen-header { padding: 0.75rem 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; font-size: 0.875rem; font-weight: 600; color: #475569; }
    .fg-screen-body { padding: 1rem; min-height: 200px; flex: 1; }
    .fg-block-row { display: flex; align-items: center; justify-content: space-between; padding: 0.65rem 0.875rem; margin-bottom: 0.4rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; transition: border-color .15s; }
    .fg-block-row:hover { border-color: #cbd5e1; }
    .fg-block-row .block-info { display: flex; align-items: center; gap: 0.5rem; }
    .fg-block-row .block-info .block-badge { font-size: 0.65rem; font-weight: 600; text-transform: uppercase; padding: 0.2rem 0.4rem; border-radius: 4px; background: #f1f5f9; color: #64748b; }
    .fg-block-row .block-info .block-badge.content { background: #dbeafe; color: #1d4ed8; }
    .fg-block-row .block-info .block-badge.form { background: #d1fae5; color: #047857; }
    .fg-block-row .block-info strong { font-size: 0.875rem; color: #1e293b; }
    .fg-block-row .block-info .block-preview { font-size: 0.75rem; color: #94a3b8; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .fg-block-row .field-actions { display: flex; gap: 2px; align-items: center; }
    .fg-block-row .btn-icon { width: 30px; height: 30px; border: none; background: transparent; border-radius: 6px; color: #64748b; display: inline-flex; align-items: center; justify-content: center; transition: all .15s; font-size: 0.9rem; }
    .fg-block-row .btn-icon:hover { background: #f1f5f9; color: #475569; }
    .fg-block-row .btn-icon.btn-remove:hover { background: #fee2e2; color: #dc2626; }
    .fg-block-row .btn-icon.btn-edit:hover { background: #e0f2fe; color: #0284c7; }
    .fg-block-row .btn-icon.btn-copy:hover { background: #f0fdfa; color: #0d9488; }
    .fg-block-row.selected { border-color: #0d9488; background: #f0fdfa; box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2); }
    .fg-block-row .block-info { cursor: pointer; flex: 1; }
    .fg-center-column { display: flex; flex-direction: column; min-width: 0; border-right: 1px solid #e2e8f0; }
    .fg-config-panel { background: #fff; border-top: 1px solid #e2e8f0; padding: 1rem; overflow-y: auto; display: none; flex-direction: column; flex: 0 0 auto; max-height: 360px; }
    .fg-config-panel.visible { display: flex; }
    .fg-config-panel-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #e2e8f0; }
    .fg-config-panel-header h6 { margin: 0; font-size: 1rem; font-weight: 600; color: #1e293b; }
    .fg-config-panel-actions { display: flex; gap: 0.25rem; }
    .fg-config-panel-actions .btn-icon { width: 32px; height: 32px; border: none; background: #f1f5f9; border-radius: 8px; color: #64748b; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all .15s; }
    .fg-config-panel-actions .btn-icon:hover { background: #e2e8f0; color: #475569; }
    .fg-config-panel-actions .btn-icon.btn-remove:hover { background: #fee2e2; color: #dc2626; }
    .fg-config-field { margin-bottom: 1rem; }
    .fg-config-field label { display: block; font-size: 0.75rem; font-weight: 600; color: #64748b; margin-bottom: 0.35rem; text-transform: uppercase; letter-spacing: 0.04em; }
    .fg-config-field input[type="text"], .fg-config-field input[type="number"], .fg-config-field select, .fg-config-field textarea { width: 100%; padding: 0.5rem 0.65rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; }
    .fg-config-field label.checkbox-wrap { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0; text-transform: none; font-weight: 500; }
    .fg-config-field label.checkbox-wrap input { width: auto; }
    .fg-config-field input:focus, .fg-config-field select:focus, .fg-config-field textarea:focus { border-color: #0d9488; outline: none; box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.15); }
    .fg-config-field textarea { min-height: 80px; resize: vertical; }
    .fg-config-field .checkbox-wrap { display: flex; align-items: center; gap: 0.5rem; }
    .fg-config-panel-close { margin-top: auto; padding-top: 1rem; }
    .fg-config-panel-close .btn-close { width: 100%; padding: 0.6rem 1rem; background: #0d9488; color: #fff; border: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; }
    .fg-config-panel-close .btn-close:hover { background: #0f766e; }
    .fg-hint { font-size: 0.8125rem; color: #94a3b8; margin-top: 0.5rem; }
    .fg-footer { margin-top: 1.75rem; display: flex; align-items: center; gap: 0.75rem; }
    .fg-btn { border-radius: 10px; padding: 0.6rem 1.25rem; font-size: 0.875rem; font-weight: 600; border: none; cursor: pointer; transition: all .15s; }
    .fg-btn-clear { background: #f1f5f9; color: #64748b; }
    .fg-btn-clear:hover { background: #e2e8f0; color: #475569; }
    .fg-btn-save { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); color: #fff; box-shadow: 0 2px 8px rgba(13, 148, 136, 0.25); }
    .fg-btn-save:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3); }
    .fg-form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem 1.5rem; margin-bottom: 1.75rem; }
    .fg-form-row .field-wrap { display: flex; flex-direction: column; }
    .fg-form-row .field-wrap label { display: flex; align-items: center; gap: 0.35rem; font-size: 0.8125rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }
    .fg-form-row .field-wrap label .info-icon { width: 14px; height: 14px; border-radius: 50%; background: #94a3b8; color: #fff; font-size: 0.65rem; display: inline-flex; align-items: center; justify-content: center; cursor: help; }
    .fg-form-row .field-wrap .form-control { border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.6rem 0.875rem; font-size: 0.875rem; }
    .fg-form-row .field-wrap .form-control:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12); }
    @media (max-width: 768px) { .fg-form-row { grid-template-columns: 1fr; } }
    .fg-preview-section { margin-top: 1.75rem; }
    .fg-preview-section h3 { font-size: 1rem; font-weight: 600; color: #475569; margin: 0 0 0.75rem 0; }
    .fg-preview-frame { max-width: 400px; margin: 0 auto; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; }
    .fg-preview-frame-header { padding: 0.75rem 1rem; background: #f0fdfa; border-bottom: 1px solid #e2e8f0; font-size: 0.8125rem; font-weight: 600; color: #0f766e; }
    .fg-preview-body { padding: 1.25rem; min-height: 200px; }
    .fg-preview-body .preview-heading { margin: 0 0 0.5rem 0; color: #1e293b; font-weight: 600; }
    .fg-preview-body .preview-heading.h1 { font-size: 1.25rem; }
    .fg-preview-body .preview-heading.h2 { font-size: 1.1rem; }
    .fg-preview-body .preview-heading.h3 { font-size: 1rem; }
    .fg-preview-body .preview-text { margin: 0 0 0.75rem 0; font-size: 0.875rem; color: #475569; line-height: 1.5; }
    .fg-preview-body .preview-field { margin-bottom: 1rem; }
    .fg-preview-body .preview-field label { display: block; font-size: 0.8125rem; font-weight: 600; color: #334155; margin-bottom: 0.35rem; }
    .fg-preview-body .preview-field input, .fg-preview-body .preview-field select, .fg-preview-body .preview-field textarea { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 0.875rem; }
    .fg-preview-body .preview-field textarea { min-height: 80px; resize: vertical; }
    .fg-preview-body .preview-field .required-dot { color: #dc2626; margin-left: 2px; }
    .fg-preview-body .preview-options { display: flex; flex-direction: column; gap: 0.35rem; }
    .fg-preview-body .preview-options label { font-weight: 500; color: #475569; }
    .fg-preview-body .preview-options-inline { flex-direction: row; flex-wrap: wrap; gap: 0.75rem 1.25rem; }
    .fg-preview-body .preview-help { display: block; font-size: 0.75rem; color: #64748b; margin-bottom: 0.35rem; }
    .fg-preview-body .preview-input { cursor: pointer; }
    .fg-preview-empty { color: #94a3b8; font-size: 0.875rem; text-align: center; padding: 2rem 1rem; }
    .fg-preview-column { background: #fafafa; border-left: 1px solid #e2e8f0; padding: 1rem; overflow-y: auto; display: flex; flex-direction: column; }
    .fg-preview-column .fg-preview-section { margin-top: 0; }
    .fg-preview-column .fg-preview-frame { max-width: 100%; margin: 0; }
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
                        {{ isset($edit) && $edit ? __('Edit Flow') : __('Create Flow') }}
                    </h1>
                    <span class="fg-subtitle">{{ __('Configure screens and form fields. Keep labels clear and short for best conversion.') }}</span>
                </div>
                <div class="fg-toolbar">
                    <a href="{{ route('flow-ground.index') }}" class="fg-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>
        </header>

        <div class="fg-body">
            <div class="fg-flash">@include('partials.flash')</div>

            <form action="{{ isset($flow) ? route('flow-ground.update', $flow) : route('flow-ground.store') }}" method="POST" id="flow-builder-form" class="form form-vertical" enctype="multipart/form-data">
                @csrf
                @if(isset($flow)) @method('PUT') @endif

                <section class="fg-section fg-first-section">
                    <div class="row">
                        <div class="col-6 col-md-6">
                            <div class="form-group mt-2 mt-sm-2 ms-sm-0 mt-md-2 ms-md-0 mt-lg-2 ms-lg-0 mt-xl-2 ms-xl-0 w-100">
                                <label class="control-label required">{{ __('Identifier') }}</label>
                                <a href="#" tabindex="-1" data-bs-placement="top" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ __('A label to recognise this flow later.') }}"><i class="ni ni-notification-70 ms-1"></i></a>
                                <div class="input-group">
                                    <input type="text" name="name" id="fg_flow_name" class="form-control" required placeholder="{{ __('e.g. Booking flow') }}" value="{{ old('name', isset($flow) ? $flow->name : '') }}">
                                </div>
                                @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="form-group mt-2 mt-sm-2 ms-sm-0 mt-md-2 ms-md-0 mt-lg-2 ms-lg-0 mt-xl-2 ms-xl-0 w-100">
                                <label class="control-label required">{{ __('Display title') }}</label>
                                <a href="#" tabindex="-1" data-bs-placement="top" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ __('Title shown to users for this flow.') }}"><i class="ni ni-notification-70 ms-1"></i></a>
                                <input type="text" name="flow_title" id="fg_form_title" class="form-control" placeholder="{{ __('e.g. Book a slot') }}" value="{{ old('flow_title', isset($flow) ? ($flow->flow_title ?? $flow->name ?? '') : '') }}">
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="form-group mt-2 mt-sm-2 ms-sm-0 mt-md-2 ms-md-0 mt-lg-2 ms-lg-0 mt-xl-2 ms-xl-0 w-100">
                                <label class="control-label required">{{ __('Screen key') }}</label>
                                <a href="#" tabindex="-1" data-bs-placement="top" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ __('Letters and underscores only; no spaces or numbers.') }}"><i class="ni ni-notification-70 ms-1"></i></a>
                                <div class="input-group">
                                    <input type="text" id="screen_unique_name" class="form-control" required placeholder="{{ __('e.g. main_screen') }}" value="" oninput="fgSanitizeScreenKey(event)">
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-6">
                            <div class="form-group mt-2 mt-sm-2 ms-sm-0 mt-md-2 ms-md-0 mt-lg-2 ms-lg-0 mt-xl-2 ms-xl-0 w-100">
                                <label class="control-label required">{{ __('Grouping') }}</label>
                                <a href="#" tabindex="-1" data-bs-placement="top" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-content="{{ __('Pick one or more groupings for the flow.') }}"><i class="ni ni-notification-70 ms-1"></i></a>
                                <div>
                                    <select id="fg_flow_tags" name="categories[]" class="form-control select2" multiple="multiple" style="width:100% !important;">
                                        <option value="SIGN_UP">SIGN_UP</option>
                                        <option value="SIGN_IN">SIGN_IN</option>
                                        <option value="APPOINTMENT_BOOKING">APPOINTMENT_BOOKING</option>
                                        <option value="LEAD_GENERATION">LEAD_GENERATION</option>
                                        <option value="CONTACT_US">CONTACT_US</option>
                                        <option value="CUSTOMER_SUPPORT">CUSTOMER_SUPPORT</option>
                                        <option value="SURVEY">SURVEY</option>
                                        <option value="OTHER">OTHER</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="row px-2">
                    <div class="col-md-12">
                <div class="fg-builder">
                <div class="fg-sidebar">
                    <div class="fg-sidebar-groups">
                        <div class="fg-sidebar-group">
                            <div class="fg-sidebar-title">{{ __('Screens') }}</div>
                            <button type="button" class="fg-field-btn fg-btn-screen" id="btn-add-screen"><span class="icon">+</span> {{ __('Add screen') }}</button>
                        </div>
                        <div class="fg-sidebar-group">
                            <div class="fg-sidebar-title">{{ __('Content') }}</div>
                            <button type="button" class="fg-field-btn" data-type="header" data-label="Header" data-kind="content"><span class="icon">H</span> Header</button>
                            <button type="button" class="fg-field-btn" data-type="subheading" data-label="Subheading" data-kind="content"><span class="icon">S</span> Subheading</button>
                            <button type="button" class="fg-field-btn" data-type="body" data-label="Body text" data-kind="content"><span class="icon">¶</span> Body text</button>
                            <button type="button" class="fg-field-btn" data-type="caption" data-label="Caption" data-kind="content"><span class="icon">C</span> Caption</button>
                            <button type="button" class="fg-field-btn" data-type="footer" data-label="Footer" data-kind="content"><span class="icon">F</span> Footer</button>
                        </div>
                        <div class="fg-sidebar-group">
                            <div class="fg-sidebar-title">{{ __('Form fields') }}</div>
                            <button type="button" class="fg-field-btn" data-type="text_field" data-label="Text field" data-kind="form"><span class="icon">T</span> Text field</button>
                            <button type="button" class="fg-field-btn" data-type="text_area" data-label="Text area" data-kind="form"><span class="icon">¶</span> Text area</button>
                            <button type="button" class="fg-field-btn" data-type="checkbox_group" data-label="Checkbox group" data-kind="form"><span class="icon">☑</span> Checkbox group</button>
                            <button type="button" class="fg-field-btn" data-type="radio_group" data-label="Radio group" data-kind="form"><span class="icon">○</span> Radio group</button>
                            <button type="button" class="fg-field-btn" data-type="select" data-label="Dropdown" data-kind="form"><span class="icon">▾</span> Dropdown</button>
                            <button type="button" class="fg-field-btn" data-type="date_field" data-label="Date" data-kind="form"><span class="icon">📅</span> Date</button>
                        </div>
                    </div>
                    <div class="fg-sidebar-footer">
                        <button type="button" class="fg-btn fg-btn-clear w-100" id="btn-clear">{{ __('Clear') }}</button>
                        <button type="submit" class="fg-btn fg-btn-save w-100" id="btn-save-sidebar">{{ __('Save') }}</button>
                    </div>
                </div>
                <div class="fg-center-column">
                    <div class="fg-canvas-area">
                        <div class="fg-screen-tabs" id="screen-tabs"></div>
                        <div class="fg-canvas-title">{{ __('Screen content') }}</div>
                        <div class="fg-canvas" id="flow-canvas">
                            <div class="fg-screen" id="screen-panel">
                                <div class="fg-screen-header" id="screen-panel-title">Main</div>
                                <div class="fg-screen-body" id="screen-blocks-container"></div>
                            </div>
                        </div>
                        <p class="fg-hint">{{ __('Add items from the left. Use edit, copy, up/down and delete on each field.') }}</p>
                    </div>
                    <div class="fg-config-panel" id="config-panel">
                        <div class="fg-config-panel-header">
                            <h6 id="config-panel-title">{{ __('Options') }}</h6>
                            <div class="fg-config-panel-actions">
                                <button type="button" class="btn-icon btn-move-up" id="config-move-up" title="{{ __('Move up') }}"><i class="ni ni-bold-up"></i></button>
                                <button type="button" class="btn-icon btn-move-down" id="config-move-down" title="{{ __('Move down') }}"><i class="ni ni-bold-down"></i></button>
                                <button type="button" class="btn-icon btn-remove" id="config-remove" title="{{ __('Remove') }}"><i class="ni ni-fat-remove"></i></button>
                            </div>
                        </div>
                        <div id="config-panel-fields"></div>
                        <div class="fg-config-panel-close">
                            <button type="button" class="btn-close" id="config-close">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
                <div class="fg-preview-column">
                    <div class="fg-preview-section">
                        <h3>{{ __('Live preview') }}</h3>
                        <p class="fg-hint" style="margin-bottom: 0.5rem;">{{ __('How this screen will look.') }}</p>
                        <div class="fg-preview-frame">
                            <div class="fg-preview-frame-header" id="preview-screen-title">Main</div>
                            <div class="fg-preview-body" id="preview-body"></div>
                        </div>
                    </div>
                </div>
                </div>
                    </div>
                </div>

                <input type="hidden" name="flow_json" id="flow_json_input" value="">

                <div class="fg-footer">
                    <button type="submit" class="fg-btn fg-btn-save">{{ __('Save flow') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function fgSanitizeScreenKey(ev) {
    var el = ev && ev.target ? ev.target : document.getElementById('screen_unique_name');
    if (!el) return;
    var start = el.selectionStart;
    var val = el.value;
    var out = val.replace(/[^A-Za-z_]/g, '');
    if (out !== val) {
        el.value = out;
        var pos = Math.max(0, start - (val.length - out.length));
        el.setSelectionRange(pos, pos);
    }
}
(function() {
    var form = document.getElementById('flow-builder-form');
    var container = document.getElementById('screen-blocks-container');
    var input = document.getElementById('flow_json_input');
    var tabsEl = document.getElementById('screen-tabs');
    var panelTitle = document.getElementById('screen-panel-title');
    var screens = [{ id: 'main', title: 'Main', unique_name: 'main', blocks: [] }];
    var activeScreenId = 'main';

    function uid() { return Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 9); }

    function getActiveScreen() {
        var s = screens.find(function(x) { return x.id === activeScreenId; });
        return s || screens[0];
    }

    function getBlocksForScreen(screenId) {
        var s = screens.find(function(x) { return x.id === screenId; });
        return s ? s.blocks : [];
    }

    function setBlocksForScreen(screenId, blocks) {
        var s = screens.find(function(x) { return x.id === screenId; });
        if (s) s.blocks = blocks;
    }

    function renderTabs() {
        tabsEl.innerHTML = '';
        screens.forEach(function(s) {
            var tab = document.createElement('button');
            tab.type = 'button';
            tab.className = 'fg-screen-tab' + (s.id === activeScreenId ? ' active' : '');
            tab.textContent = s.title;
            tab.setAttribute('data-screen-id', s.id);
            if (screens.length > 1) {
                var rm = document.createElement('span');
                rm.className = 'tab-remove';
                rm.innerHTML = '×';
                rm.title = '{{ __("Remove screen") }}';
                rm.onclick = function(e) { e.stopPropagation(); removeScreen(s.id); };
                tab.appendChild(rm);
            }
            tab.onclick = function() { switchScreen(s.id); };
            tabsEl.appendChild(tab);
        });
    }

    function switchScreen(screenId) {
        persistScreenKeyFromInput();
        activeScreenId = screenId;
        var s = getActiveScreen();
        panelTitle.textContent = s.title;
        renderBlocks(s.blocks);
        renderTabs();
        updatePreview();
        updateScreenUniqueName();
    }

    function removeScreen(screenId) {
        if (screens.length <= 1) return;
        if (!confirm('{{ __("Remove this screen and all its content?") }}')) return;
        screens = screens.filter(function(x) { return x.id !== screenId; });
        if (activeScreenId === screenId) {
            activeScreenId = screens[0].id;
            switchScreen(activeScreenId);
        } else {
            renderTabs();
        }
        serialize();
    }

    function addScreen() {
        persistScreenKeyFromInput();
        var n = screens.length + 1;
        var id = 'screen_' + uid();
        var t = 'Screen_' + n;
        screens.push({ id: id, title: 'Screen ' + n, unique_name: t, blocks: [] });
        activeScreenId = id;
        panelTitle.textContent = 'Screen ' + n;
        renderBlocks([]);
        renderTabs();
        updateScreenUniqueName();
        serialize();
    }

    var configPanel = document.getElementById('config-panel');
    var configPanelTitle = document.getElementById('config-panel-title');
    var configPanelFields = document.getElementById('config-panel-fields');
    var selectedRow = null;

    function getDefaultConfig(type, label, kind) {
        var c = { label: label || type, kind: kind };
        if (kind === 'content') {
            c.content = '';
            c.type = (type === 'header' ? 'h1' : 'p');
            c.class = '';
        } else {
            c.name = (label || type).toLowerCase().replace(/\s+/g, '_');
            c.placeholder = '';
            c.required = false;
            c.options = '';
            c.help_text = '';
            c.inline = false;
        }
        return c;
    }

    function getBlockConfig(row) {
        var data = row.getAttribute('data-config');
        if (data) try { return JSON.parse(data); } catch (e) {}
        return getDefaultConfig(
            row.getAttribute('data-type'),
            row.getAttribute('data-label'),
            row.getAttribute('data-kind')
        );
    }

    function setBlockConfig(row, config) {
        row.setAttribute('data-config', JSON.stringify(config));
        row.setAttribute('data-label', config.label || row.getAttribute('data-type'));
        var preview = config.content || config.label || config.name || config.placeholder || '';
        if (preview && preview.length > 50) preview = preview.substring(0, 50) + '…';
        row.setAttribute('data-preview', preview);
        var blockInfo = row.querySelector('.block-info');
        var strongEl = blockInfo ? blockInfo.querySelector('strong') : null;
        var previewEl = row.querySelector('.block-preview');
        if (strongEl) strongEl.textContent = config.label || row.getAttribute('data-type');
        if (previewEl) previewEl.textContent = preview;
        else if (preview && blockInfo) {
            var span = document.createElement('span');
            span.className = 'block-preview';
            span.textContent = preview;
            strongEl.parentNode.insertBefore(span, strongEl.nextSibling);
        }
    }

    function buildConfigForm(blockType, kind, config) {
        var html = '';
        if (kind === 'content') {
            html += '<div class="fg-config-field"><label>{{ __("Label") }}</label><input type="text" id="cfg-label" value="' + (config.label || '').replace(/"/g, '&quot;') + '"></div>';
            if (['header','subheading'].indexOf(blockType) >= 0) {
                html += '<div class="fg-config-field"><label>{{ __("Type") }}</label><select id="cfg-type"><option value="h1"' + (config.type === 'h1' ? ' selected' : '') + '>h1</option><option value="h2"' + (config.type === 'h2' ? ' selected' : '') + '>h2</option><option value="h3"' + (config.type === 'h3' ? ' selected' : '') + '>h3</option></select></div>';
            }
            html += '<div class="fg-config-field"><label>{{ __("Class") }}</label><input type="text" id="cfg-class" placeholder="space separated classes" value="' + (config.class || '').replace(/"/g, '&quot;') + '"></div>';
            html += '<div class="fg-config-field"><label>{{ __("Text / Content") }}</label><textarea id="cfg-content" placeholder="{{ __("Enter text to display") }}">' + (config.content || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</textarea></div>';
        } else {
            html += '<div class="fg-config-field"><label>{{ __("Label") }}</label><input type="text" id="cfg-label" value="' + (config.label || '').replace(/"/g, '&quot;') + '"></div>';
            html += '<div class="fg-config-field"><label>{{ __("Name") }}</label><input type="text" id="cfg-name" placeholder="field_name" value="' + (config.name || '').replace(/"/g, '&quot;') + '"></div>';
            html += '<div class="fg-config-field"><label>{{ __("Placeholder") }}</label><input type="text" id="cfg-placeholder" value="' + (config.placeholder || '').replace(/"/g, '&quot;') + '"></div>';
            html += '<div class="fg-config-field"><label>{{ __("Help text") }}</label><input type="text" id="cfg-help_text" placeholder="{{ __("Optional hint below the label") }}" value="' + (config.help_text || '').replace(/"/g, '&quot;') + '"></div>';
            html += '<div class="fg-config-field"><label class="checkbox-wrap"><input type="checkbox" id="cfg-required"' + (config.required ? ' checked' : '') + '> {{ __("Required") }}</label></div>';
            if (['select','radio_group','checkbox_group'].indexOf(blockType) >= 0) {
                var optValues = [];
                if (Array.isArray(config.options)) {
                    optValues = config.options.slice();
                } else if (config.options) {
                    optValues = String(config.options).split(',').map(function (s) { return s.trim(); }).filter(Boolean);
                }
                if (!optValues.length) {
                    optValues = ['Option 1', 'Option 2', 'Option 3'];
                }
                html += '<div class="fg-config-field"><label>{{ __("Options") }}</label>';
                html += '<div id="cfg-options-list">';
                optValues.forEach(function (opt, idx) {
                    var safe = String(opt).replace(/"/g, '&quot;');
                    html += '<div class="fg-option-row d-flex align-items-center mb-1">'
                        + '<input type="text" class="form-control form-control-sm fg-opt-label me-2" value="' + safe + '">'
                        + '<button type="button" class="btn btn-sm btn-light fg-opt-remove"' + (idx === 0 ? ' style="visibility:hidden"' : '') + '>&times;</button>'
                        + '</div>';
                });
                html += '</div>';
                html += '<button type="button" class="btn btn-sm btn-light mt-2" id="cfg-add-option">{{ __("Add option") }}</button>';
                html += '</div>';
                if (['radio_group','checkbox_group'].indexOf(blockType) >= 0) {
                    html += '<div class="fg-config-field"><label class="checkbox-wrap"><input type="checkbox" id="cfg-inline"' + (config.inline ? ' checked' : '') + '> {{ __("Display options in one row") }}</label></div>';
                }
            }
        }
        return html;
    }

    function readOptionsFromForm() {
        var list = document.getElementById('cfg-options-list');
        if (list) {
            var out = [];
            list.querySelectorAll('.fg-opt-label').forEach(function (inp) {
                var v = (inp.value || '').trim();
                if (v) out.push(v);
            });
            return out;
        }
        var optEl = document.getElementById('cfg-options');
        if (optEl && optEl.value) {
            return optEl.value.split(',').map(function (s) { return s.trim(); }).filter(Boolean);
        }
        return [];
    }

    function getBlocksForPreviewWithFormOverlay() {
        var blocks = getBlocksForScreen(activeScreenId);
        if (!selectedRow || !configPanelFields.contains(document.activeElement)) return blocks;
        var idx = -1;
        container.querySelectorAll('.fg-block-row').forEach(function(r, i) { if (r === selectedRow) idx = i; });
        if (idx < 0) return blocks;
        var overlay = { id: blocks[idx].id, type: blocks[idx].type, kind: blocks[idx].kind, label: blocks[idx].label };
        var labelEl = document.getElementById('cfg-label');
        if (labelEl) overlay.label = labelEl.value || overlay.label;
        if (blocks[idx].kind === 'content') {
            overlay.content = (document.getElementById('cfg-content') || {}).value;
            overlay.type = (document.getElementById('cfg-type') || {}).value || blocks[idx].type;
            overlay.class = (document.getElementById('cfg-class') || {}).value;
        } else {
            overlay.name = (document.getElementById('cfg-name') || {}).value;
            overlay.placeholder = (document.getElementById('cfg-placeholder') || {}).value;
            overlay.help_text = (document.getElementById('cfg-help_text') || {}).value;
            overlay.required = (document.getElementById('cfg-required') || {}).checked;
            overlay.inline = (document.getElementById('cfg-inline') || {}).checked;
            overlay.options = readOptionsFromForm();
        }
        var out = blocks.slice();
        out[idx] = Object.assign({}, blocks[idx], overlay);
        return out;
    }

    function openConfigPanel(row) {
        selectedRow = row;
        container.querySelectorAll('.fg-block-row').forEach(function(r) { r.classList.remove('selected'); });
        row.classList.add('selected');
        var type = row.getAttribute('data-type');
        var kind = row.getAttribute('data-kind') || 'form';
        var config = getBlockConfig(row);
        configPanelTitle.textContent = row.getAttribute('data-label') || type;
        configPanelFields.innerHTML = buildConfigForm(type, kind, config);
        configPanel.classList.add('visible');
        configPanelFields.querySelectorAll('input, select, textarea').forEach(function(el) {
            el.addEventListener('input', livePreview);
            el.addEventListener('change', livePreview);
        });
        var addOpt = document.getElementById('cfg-add-option');
        if (addOpt) {
            addOpt.addEventListener('click', function () {
                var list = document.getElementById('cfg-options-list');
                if (!list) return;
                var idx = list.querySelectorAll('.fg-option-row').length + 1;
                var row = document.createElement('div');
                row.className = 'fg-option-row d-flex align-items-center mb-1';
                row.innerHTML = '<input type="text" class="form-control form-control-sm fg-opt-label me-2" value="Option ' + idx + '">'
                    + '<button type="button" class="btn btn-sm btn-light fg-opt-remove">&times;</button>';
                list.appendChild(row);
                var inp = row.querySelector('.fg-opt-label');
                var rm = row.querySelector('.fg-opt-remove');
                if (inp) {
                    inp.addEventListener('input', livePreview);
                    inp.addEventListener('change', livePreview);
                    inp.focus();
                }
                if (rm) {
                    rm.addEventListener('click', function () {
                        row.remove();
                        livePreview();
                    });
                }
                list.querySelectorAll('.fg-opt-remove').forEach(function(btn, i) {
                    btn.style.visibility = (i === 0 ? 'hidden' : 'visible');
                });
                livePreview();
            });
            document.querySelectorAll('.fg-opt-remove').forEach(function(btn, i) {
                btn.addEventListener('click', function () {
                    var row = this.closest('.fg-option-row');
                    if (row) row.remove();
                    document.querySelectorAll('.fg-opt-remove').forEach(function(b, j) {
                        b.style.visibility = (j === 0 ? 'hidden' : 'visible');
                    });
                    livePreview();
                });
            });
        }
    }
    function livePreview() { updatePreviewWithBlocks(getBlocksForPreviewWithFormOverlay()); }

    function closeConfigPanel() {
        if (selectedRow) {
            applyConfigToRow(selectedRow);
            selectedRow.classList.remove('selected');
        }
        selectedRow = null;
        configPanel.classList.remove('visible');
        syncBlocksFromDOM();
        serialize();
        updatePreview();
    }

    function applyConfigToRow(row) {
        var kind = row.getAttribute('data-kind') || 'form';
        var config = getBlockConfig(row);
        var labelEl = document.getElementById('cfg-label');
        if (labelEl) config.label = labelEl.value || config.label;
        if (kind === 'content') {
            var typeEl = document.getElementById('cfg-type');
            if (typeEl) config.type = typeEl.value;
            var classEl = document.getElementById('cfg-class');
            if (classEl) config.class = classEl.value;
            var contentEl = document.getElementById('cfg-content');
            if (contentEl) config.content = contentEl.value;
        } else {
            var nameEl = document.getElementById('cfg-name');
            if (nameEl) config.name = nameEl.value || (config.label || '').toLowerCase().replace(/\s+/g, '_');
            var phEl = document.getElementById('cfg-placeholder');
            if (phEl) config.placeholder = phEl.value;
            var helpEl = document.getElementById('cfg-help_text');
            if (helpEl) config.help_text = helpEl.value;
            var reqEl = document.getElementById('cfg-required');
            if (reqEl) config.required = reqEl.checked;
            var inlineEl = document.getElementById('cfg-inline');
            if (inlineEl) config.inline = inlineEl.checked;
            config.options = readOptionsFromForm();
        }
        setBlockConfig(row, config);
    }

    function renderBlocks(blocks) {
        container.innerHTML = '';
        (blocks || []).forEach(function(b) {
            var config = b.config || getDefaultConfig(b.type, b.label, b.kind || 'form');
            if (b.label) config.label = b.label;
            if (b.content !== undefined) config.content = b.content;
            if (b.name !== undefined) config.name = b.name;
            if (b.placeholder !== undefined) config.placeholder = b.placeholder;
            if (b.required !== undefined) config.required = b.required;
            if (b.options !== undefined) config.options = b.options;
            if (b.help_text !== undefined) config.help_text = b.help_text;
            if (b.inline !== undefined) config.inline = b.inline;
            if (b.type && b.type.match(/^h[123]$/)) config.type = b.type;
            if (b.class !== undefined) config.class = b.class;
            appendBlockRow(b.id, b.type, b.kind || 'form', config);
        });
    }

    function appendBlockRow(blockId, type, kind, config) {
        if (!config) config = getDefaultConfig(type, type, kind);
        var label = config.label || type;
        var preview = config.content || config.name || config.placeholder || '';
        if (preview && preview.length > 50) preview = preview.substring(0, 50) + '…';
        var row = document.createElement('div');
        row.className = 'fg-block-row';
        row.setAttribute('data-block-id', blockId);
        row.setAttribute('data-type', type);
        row.setAttribute('data-label', label);
        row.setAttribute('data-kind', kind);
        row.setAttribute('data-config', JSON.stringify(config));
        row.setAttribute('data-preview', preview);
        var badgeClass = kind === 'content' ? 'content' : 'form';
        row.innerHTML = '<div class="block-info">' +
            '<span class="block-badge ' + badgeClass + '">' + (kind === 'content' ? '{{ __("Content") }}' : '{{ __("Form") }}') + '</span>' +
            '<strong>' + label + '</strong>' +
            (preview ? '<span class="block-preview">' + (preview.length > 40 ? preview.substring(0, 40) + '…' : preview) + '</span>' : '') +
            '</div>' +
            '<div class="field-actions">' +
            '<button type="button" class="btn-icon btn-edit" title="{{ __("Edit") }}"><i class="ni ni-settings"></i></button>' +
            '<button type="button" class="btn-icon btn-copy" title="{{ __("Copy") }}"><i class="ni ni-collection"></i></button>' +
            '<button type="button" class="btn-icon btn-move-up" title="{{ __("Move up") }}"><i class="ni ni-bold-up"></i></button>' +
            '<button type="button" class="btn-icon btn-move-down" title="{{ __("Move down") }}"><i class="ni ni-bold-down"></i></button>' +
            '<button type="button" class="btn-icon btn-remove" title="{{ __("Delete") }}"><i class="ni ni-fat-remove"></i></button>' +
            '</div>';
        row.querySelector('.block-info').onclick = function(e) { if (!e.target.closest('button')) openConfigPanel(row); };
        row.querySelector('.btn-edit').onclick = function(e) { e.stopPropagation(); openConfigPanel(row); };
        row.querySelector('.btn-copy').onclick = function(e) { e.stopPropagation(); duplicateBlock(row); };
        row.querySelector('.btn-remove').onclick = function(e) { e.stopPropagation(); row.remove(); closeConfigPanel(); syncBlocksFromDOM(); serialize(); updatePreview(); };
        row.querySelector('.btn-move-up').onclick = function(e) { e.stopPropagation();
            var prev = row.previousElementSibling;
            if (prev) { container.insertBefore(row, prev); syncBlocksFromDOM(); serialize(); updatePreview(); }
        };
        row.querySelector('.btn-move-down').onclick = function(e) { e.stopPropagation();
            var next = row.nextElementSibling;
            if (next) { container.insertBefore(next, row); syncBlocksFromDOM(); serialize(); updatePreview(); }
        };
        container.appendChild(row);
    }

    function duplicateBlock(row) {
        var type = row.getAttribute('data-type');
        var kind = row.getAttribute('data-kind') || 'form';
        var config = getBlockConfig(row);
        var newId = uid();
        appendBlockRow(newId, type, kind, config);
        var newRow = container.lastElementChild;
        var next = row.nextElementSibling;
        if (next) container.insertBefore(newRow, next);
        else container.appendChild(newRow);
        syncBlocksFromDOM();
        serialize();
        updatePreview();
    }

    function syncBlocksFromDOM() {
        var blocks = [];
        container.querySelectorAll('.fg-block-row').forEach(function(row) {
            var config = getBlockConfig(row);
            blocks.push(Object.assign({
                id: row.getAttribute('data-block-id'),
                type: row.getAttribute('data-type'),
                kind: row.getAttribute('data-kind') || 'form',
                label: config.label
            }, config));
        });
        setBlocksForScreen(activeScreenId, blocks);
    }

    function escapeHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function renderBlockPreview(block) {
        var kind = block.kind || 'form';
        var config = block;
        var bid = escapeHtml(block.id || '');
        if (kind === 'content') {
            var tag = (config.type && config.type.match(/^h[123]$/)) ? config.type : 'p';
            var text = config.content || config.label || '';
            var cls = (config.class || '').trim() ? ' class="' + escapeHtml(config.class) + '"' : '';
            if (tag === 'p' || tag === 'h1' || tag === 'h2' || tag === 'h3') {
                return '<div class="preview-' + (tag === 'p' ? 'text' : 'heading') + ' ' + (tag !== 'p' ? tag : '') + '"' + cls + '>' + escapeHtml(text || '') + '</div>';
            }
            return '<div class="preview-text"' + cls + '>' + escapeHtml(text) + '</div>';
        }
        var label = config.label || block.type || '';
        var required = config.required ? ' <span class="required-dot">*</span>' : '';
        var ph = escapeHtml(config.placeholder || '');
        var helpText = config.help_text ? escapeHtml(config.help_text) : '';
        var inlineClass = config.inline ? ' preview-options-inline' : '';
        var rawOpts = config.options;
        var opts = Array.isArray(rawOpts) ? rawOpts : (rawOpts ? String(rawOpts).split(',').map(function(s) { return s.trim(); }) : []);
        var hadNoOptions = !rawOpts || (Array.isArray(rawOpts) && !rawOpts.length) || (typeof rawOpts === 'string' && !rawOpts.trim());
        // If no options configured yet, show sensible defaults like Meta's builder
        if (!opts || !opts.length) {
            if (block.type === 'radio_group' || block.type === 'checkbox_group') {
                opts = ['Option 1', 'Option 2'];
            } else if (block.type === 'select') {
                opts = ['Option 1'];
            }
        }
        var fname = 'fg_pv_' + bid;
        switch (block.type) {
            case 'text_field':
                return '<div class="preview-field"><label>' + escapeHtml(label) + required + '</label>' + (helpText ? '<span class="preview-help">' + helpText + '</span>' : '') + '<input type="text" name="' + fname + '" placeholder="' + ph + '" class="preview-input"></div>';
            case 'text_area':
                return '<div class="preview-field"><label>' + escapeHtml(label) + required + '</label>' + (helpText ? '<span class="preview-help">' + helpText + '</span>' : '') + '<textarea name="' + fname + '" placeholder="' + ph + '" class="preview-input"></textarea></div>';
            case 'select':
                var optHtml = opts.length ? opts.map(function(o) { return '<option value="' + escapeHtml(o) + '">' + escapeHtml(o) + '</option>'; }).join('') : '';
                return '<div class="preview-field"><label>' + escapeHtml(label) + required + '</label>' + (helpText ? '<span class="preview-help">' + helpText + '</span>' : '') + '<select name="' + fname + '" class="preview-input"><option value="">—</option>' + optHtml + '</select></div>';
            case 'radio_group':
                var radioHtml = opts.length ? opts.map(function(o, i) { return '<label class="preview-opt"><input type="radio" name="' + fname + '" value="' + escapeHtml(o) + '" class="preview-input"> ' + escapeHtml(o) + '</label>'; }).join('') : '<span class="preview-text">' + escapeHtml(label) + ' (no options)</span>';
                if (hadNoOptions) {
                    radioHtml += '<div class="preview-add-option text-muted small">+ Add option</div>';
                }
                return '<div class="preview-field"><label>' + escapeHtml(label) + required + '</label>' + (helpText ? '<span class="preview-help">' + helpText + '</span>' : '') + '<div class="preview-options' + inlineClass + '">' + radioHtml + '</div></div>';
            case 'checkbox_group':
                var cbHtml = opts.length ? opts.map(function(o, i) { return '<label class="preview-opt"><input type="checkbox" name="' + fname + '[]" value="' + escapeHtml(o) + '" class="preview-input"> ' + escapeHtml(o) + '</label>'; }).join('') : '<span class="preview-text">' + escapeHtml(label) + ' (no options)</span>';
                if (hadNoOptions) {
                    cbHtml += '<div class="preview-add-option text-muted small">+ Add option</div>';
                }
                return '<div class="preview-field"><label>' + escapeHtml(label) + required + '</label>' + (helpText ? '<span class="preview-help">' + helpText + '</span>' : '') + '<div class="preview-options' + inlineClass + '">' + cbHtml + '</div></div>';
            case 'date_field':
                return '<div class="preview-field"><label>' + escapeHtml(label) + required + '</label>' + (helpText ? '<span class="preview-help">' + helpText + '</span>' : '') + '<input type="date" name="' + fname + '" class="preview-input"></div>';
            default:
                return '<div class="preview-field"><label>' + escapeHtml(label) + '</label>' + (helpText ? '<span class="preview-help">' + helpText + '</span>' : '') + '<input type="text" name="' + fname + '" placeholder="' + ph + '" class="preview-input"></div>';
        }
    }

    function updatePreviewWithBlocks(blocks) {
        var el = document.getElementById('preview-body');
        var titleEl = document.getElementById('preview-screen-title');
        if (!el) return;
        if (titleEl) titleEl.textContent = getActiveScreen().title;
        if (!blocks || !blocks.length) {
            el.innerHTML = '<div class="fg-preview-empty">{{ __("Add content and form fields from the left menu. Preview will update here.") }}</div>';
            return;
        }
        el.innerHTML = blocks.map(renderBlockPreview).join('');
    }

    function updatePreview() {
        updatePreviewWithBlocks(getBlocksForPreviewWithFormOverlay());
    }

    var screenKeyInput = document.getElementById('screen_unique_name');
    function enforceScreenKeyFormat(ev) {
        var el = ev && ev.target ? ev.target : screenKeyInput;
        if (!el) return;
        var cur = el.value;
        var out = cur.replace(/[^A-Za-z_]/g, '');
        if (out !== cur) el.value = out;
    }
    function updateScreenUniqueName() {
        var s = getActiveScreen();
        if (!screenKeyInput) return;
        screenKeyInput.value = s.unique_name || s.title.replace(/[^A-Za-z_]/g, '_') || '';
    }
    function persistScreenKeyFromInput() {
        var s = getActiveScreen();
        if (screenKeyInput) s.unique_name = screenKeyInput.value.replace(/[^A-Za-z_]/g, '') || s.title.replace(/[^A-Za-z_]/g, '_');
    }

    function addBlock(type, label, kind) {
        var id = uid();
        kind = kind || (['header','subheading','body','caption','footer'].indexOf(type) >= 0 ? 'content' : 'form');
        var config = getDefaultConfig(type, label, kind);
        appendBlockRow(id, type, kind, config);
        syncBlocksFromDOM();
        serialize();
        updatePreview();
        var lastRow = container.querySelector('.fg-block-row:last-child');
        if (lastRow) openConfigPanel(lastRow);
    }

    document.getElementById('config-close').onclick = closeConfigPanel;
    document.getElementById('config-remove').onclick = function() {
        if (selectedRow) {
            selectedRow.remove();
            selectedRow = null;
            configPanel.classList.remove('visible');
            syncBlocksFromDOM();
            serialize();
            updatePreview();
        }
    };
    document.getElementById('config-move-up').onclick = function() {
        if (selectedRow && selectedRow.previousElementSibling) {
            container.insertBefore(selectedRow, selectedRow.previousElementSibling);
            syncBlocksFromDOM(); serialize(); updatePreview();
        }
    };
    document.getElementById('config-move-down').onclick = function() {
        if (selectedRow && selectedRow.nextElementSibling) {
            container.insertBefore(selectedRow.nextElementSibling, selectedRow);
            syncBlocksFromDOM(); serialize(); updatePreview();
        }
    };

    function serialize() {
        persistScreenKeyFromInput();
        syncBlocksFromDOM();
        var flowTitleEl = form.querySelector('[name="flow_title"]');
        var tagsEl = document.getElementById('fg_flow_tags');
        var categories = [];
        if (tagsEl) {
            if (typeof jQuery !== 'undefined' && jQuery(tagsEl).data('select2')) categories = jQuery(tagsEl).val() || [];
            else for (var i = 0; i < tagsEl.options.length; i++) if (tagsEl.options[i].selected) categories.push(tagsEl.options[i].value);
        }
        var payload = {
            version: '4.0',
            flow_title: flowTitleEl ? flowTitleEl.value : '',
            categories: categories,
            screens: screens.map(function(s) {
                return { id: s.id, title: s.title, unique_name: s.unique_name || s.title.replace(/[^A-Za-z_]/g, '_'), blocks: s.blocks };
            })
        };
        input.value = JSON.stringify(payload);
    }

    document.getElementById('btn-add-screen').onclick = addScreen;

    document.querySelectorAll('.fg-field-btn[data-type]').forEach(function(btn) {
        btn.onclick = function() {
            addBlock(
                this.getAttribute('data-type'),
                this.getAttribute('data-label'),
                this.getAttribute('data-kind') || 'form'
            );
        };
    });

    document.getElementById('btn-clear').onclick = function() {
        if (confirm('{{ __("Clear all blocks from this screen?") }}')) {
            selectedRow = null;
            configPanel.classList.remove('visible');
            setBlocksForScreen(activeScreenId, []);
            renderBlocks([]);
            serialize();
            updatePreview();
        }
    };

    form.onsubmit = function() {
        var previewBody = document.getElementById('preview-body');
        if (previewBody) previewBody.querySelectorAll('input, select, textarea').forEach(function(el) { el.removeAttribute('name'); });
        serialize();
    };

    var defaultFlow = { version: '4.0', flow_title: '', categories: [], screens: [{ id: 'main', title: 'Main', unique_name: 'main', blocks: [] }] };
    var initial = @json($flowJson ?? null);
    if (!initial || !initial.screens) { initial = defaultFlow; }
    var flowTitleInput = form.querySelector('[name="flow_title"]');
    if (flowTitleInput && initial.flow_title) flowTitleInput.value = initial.flow_title;
    var initialCategories = (initial && initial.categories && initial.categories.length) ? initial.categories : [];
    if (initial.screens && initial.screens.length) {
        screens = initial.screens.map(function(s) {
            var blocks = s.blocks || [];
            if (s.fields && !s.blocks) {
                blocks = s.fields.map(function(f) {
                    return { id: f.id || uid(), type: f.type || 'text_field', label: f.label || f.type, kind: 'form', name: f.name || '' };
                });
            }
            return { id: s.id || 'main', title: s.title || 'Main', unique_name: s.unique_name || (s.title || 'Main').replace(/[^A-Za-z_]/g, '_'), blocks: blocks };
        });
        activeScreenId = screens[0].id;
    }
    panelTitle.textContent = getActiveScreen().title;
    renderBlocks(getActiveScreen().blocks);
    renderTabs();
    serialize();
    updatePreview();
    updateScreenUniqueName();

    if (screenKeyInput) {
        screenKeyInput.addEventListener('input', function(e) { enforceScreenKeyFormat(e); persistScreenKeyFromInput(); });
        screenKeyInput.addEventListener('blur', persistScreenKeyFromInput);
    }
    function fgInitEnhancements() {
        var did = false;

        try {
            if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
                var popoverEls = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
                popoverEls.forEach(function(el) {
                    if (!el.__fgPopover) {
                        new bootstrap.Popover(el);
                        el.__fgPopover = true;
                        did = true;
                    }
                });
            }
        } catch (e) {}

        try {
            var tagsEl = document.getElementById('fg_flow_tags');
            if (tagsEl && typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.select2) {
                var $el = jQuery(tagsEl);
                if (!$el.data('select2')) {
                    $el.select2({
                        placeholder: '{{ __("Pick groupings") }}',
                        allowClear: true,
                        closeOnSelect: false,
                        width: '100%',
                        dropdownCssClass: 'fg-s2-dropdown'
                    });
                    did = true;
                }
                if (initialCategories.length) $el.val(initialCategories).trigger('change');
            } else if (tagsEl && initialCategories.length) {
                initialCategories.forEach(function(v) {
                    var opt = tagsEl.querySelector('option[value="' + v + '"]');
                    if (opt) opt.selected = true;
                });
            }
        } catch (e) {}

        return did;
    }

    // libs (jQuery/Bootstrap) load after content in this app; retry briefly until ready
    fgInitEnhancements();
    var fgTry = 0;
    var fgTimer = setInterval(function() {
        fgTry++;
        var ok = fgInitEnhancements();
        if (ok || fgTry > 20) clearInterval(fgTimer);
    }, 250);
})();
</script>
@endsection
