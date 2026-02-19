@extends('layouts.app', ['title' => __('Edit LeadBot')])

@section('title')
    <title>{{ __('Edit LeadBot') }}</title>
@endsection

@section('head')
    @include('lead-bot::partials.ui')
    @include('lead-bot::edit-css')
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-atom"></i></span>
                        {{ __('Edit LeadBot') }}
                    </h1>
                    <span class="lb-subtitle">
                        <span class="lb-pill primary">{{ $leadBot->app_id === 'meta' ? __('Meta (Facebook Leads)') : ucfirst($leadBot->app_id) }}</span>
                        <span class="lb-pill ml-2">#{{ $leadBot->id }}</span>
                    </span>
                </div>
                <div class="lb-toolbar">
                    <a href="{{ route('lead-bot.index') }}" class="lb-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back') }}
                    </a>
                    <button type="submit" form="leadbot-form" class="lb-btn-primary">
                        <i class="ni ni-check-bold"></i> {{ __('Save') }}
                    </button>
                    <button type="button" class="lb-btn-danger" id="deleteLeadBotBtn">
                        <i class="ni ni-fat-remove"></i> {{ __('Delete') }}
                    </button>
                </div>
            </div>
        </header>

        <div class="lb-body">
            @if (session('success'))
                <div class="lb-flash">
                    <div class="alert alert-success mb-0">{{ session('success') }}</div>
                </div>
            @endif

            <form action="{{ route('lead-bot.update', $leadBot->id) }}" method="POST" id="leadbot-form">
                @csrf
                @method('PUT')

                <section class="lb-section">
                    <div class="lb-section-hd">
                        <h3 class="lb-section-title mb-0">{{ __('Bot details') }}</h3>
                        <span class="text-muted small">{{ __('Trigger: when this happens…') }}</span>
                    </div>
                    <div class="lb-section-bd">
                        <div class="position-relative">
                            <div class="d-flex align-items-center">
                                <label id="workflownameLabel" class="font-weight-bold mb-0" style="font-size: 1.05rem;">
                                    {{ $leadBot->name }}
                                </label>
                                <input type="text" class="form-control d-none ml-3" id="workflowname" name="name"
                                    value="{{ $leadBot->name }}" style="max-width: 520px; border-radius: 12px; height: 44px;">
                                <button type="button" id="editWorkflowName" class="lb-icon-btn ml-2" title="{{ __('Rename') }}">
                                    <i class="ni ni-ruler-pencil"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="app_id" class="form-label">{{ __('Select App') }}</label>
                            <select class="form-control" id="app_id" name="app_id" required>
                                <option value="" disabled>{{ __('Select an app') }}</option>
                                <option value="meta" {{ $leadBot->app_id == 'meta' ? 'selected' : '' }}>{{ __('Meta (Facebook Leads)') }}</option>
                                <option value="webhook" {{ $leadBot->app_id == 'webhook' ? 'selected' : '' }}>Webhook</option>
                                <option value="indiamart" {{ $leadBot->app_id == 'indiamart' ? 'selected' : '' }}>IndiaMart</option>
                                <option value="99acres" {{ $leadBot->app_id == '99acres' ? 'selected' : '' }}>99Acres</option>
                                <option value="housing" {{ $leadBot->app_id == 'housing' ? 'selected' : '' }}>Housing.com</option>
                                <option value="justdial" {{ $leadBot->app_id == 'justdial' ? 'selected' : '' }}>Justdial</option>
                                <option value="tradeindia" {{ $leadBot->app_id == 'tradeindia' ? 'selected' : '' }}>TradeIndia</option>
                                <option value="sulekha" {{ $leadBot->app_id == 'sulekha' ? 'selected' : '' }}>Sulekha</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="trigger_event" class="form-label">{{ __('Trigger Event') }}</label>
                            <select class="form-control" id="trigger_event" name="trigger_event">
                                <option value="">{{ __('Select Trigger') }}</option>
                            </select>
                        </div>

                        @if (strtolower((string) $leadBot->app_id) === 'meta')
                            <div class="mb-3">
                                <label class="form-label">{{ __('CRM Meta Leads') }}</label>
                                <select id="crmLeadSelect" class="form-control">
                                    <option value="">{{ __('Latest lead') }}</option>
                                    @foreach (($crmMetaLeads ?? collect()) as $l)
                                        <option value="{{ $l->id }}">
                                            {{ $l->full_name ?: __('(no name)') }} - {{ $l->phone_number ?: __('(no phone)') }}
                                            @if ($l->campaign_name) — {{ $l->campaign_name }} @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="lb-section">
                    <div class="lb-section-hd">
                        <h3 class="lb-section-title mb-0">{{ __('Webhook') }}</h3>
                        <span class="text-muted small">{{ __('Capture variables from webhook/CRM') }}</span>
                    </div>
                    <div class="lb-section-bd">
                        <div class="form-group mb-2">
                            <label class="form-control-label font-weight-bold">{{ __('Webhook URL') }}</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="webhookUrl"
                                    value="{{ url('/api/lead-bot/webhook/' . $leadBot->webhook_token) }}" readonly
                                    style="border-radius: 12px 0 0 12px; height: 44px;">
                                <div class="input-group-append">
                                    <button type="button" class="lb-btn-soft" id="copyWebhookUrl" style="border-radius: 0;">
                                        <i class="ni ni-single-copy-04"></i> {{ __('Copy') }}
                                    </button>
                                    @if (isset($latestWebhook) && $latestWebhook?->mapped_data)
                                        <button type="button" class="lb-btn-primary" id="recaptureWebhookResponse" style="border-radius: 0 12px 12px 0;">
                                            <i class="ni ni-cloud-download-95"></i> {{ __('Re-Capture') }}
                                        </button>
                                        <button type="button" class="lb-btn-primary d-none" id="captureWebhookResponse"></button>
                                    @else
                                        <button type="button" class="lb-btn-primary" id="captureWebhookResponse" style="border-radius: 0 12px 12px 0;">
                                            <i class="ni ni-cloud-download-95"></i> {{ __('Capture') }}
                                        </button>
                                        <button type="button" class="lb-btn-primary d-none" id="recaptureWebhookResponse"></button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <span id="toggleResponseView"
                                style="display: {{ isset($latestWebhook) && $latestWebhook?->mapped_data ? 'inline-block' : 'none' }}; cursor:pointer; font-weight: 800;">
                                {{ __('Response Received') }} (<span id="toggleIcon">&gt;</span>)
                            </span>
                            <div id="responseContainer" style="display: block;">
                                <div id="webhookResponse" class="mt-3"></div>
                                @if (isset($latestWebhook) && $latestWebhook?->mapped_data)
                                    <div id="alreadyExistsWebhookResponse" class="mt-3">
                                        <div class="border rounded p-3" style="border-radius: 14px; max-height: 300px; overflow-y: auto;">
                                            <div class="row">
                                                @foreach ($latestWebhook->mapped_data as $key => $item)
                                                    <div class="col-md-6 mb-2">
                                                        <input type="text" class="form-control font-weight-bold"
                                                            value="{{ $item['label'] ?? $key }}" readonly>
                                                    </div>
                                                    <div class="col-md-6 mb-2">
                                                        <input type="text" class="form-control font-weight-bold"
                                                            value="{{ is_array($item['value'] ?? null) ? json_encode($item['value']) : ($item['value'] ?? '') }}" readonly>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>

                <section class="lb-section">
                    <div class="lb-section-hd">
                        <div>
                            <h3 class="lb-section-title mb-0">{{ __('Tasks') }}</h3>
                            <div class="text-muted small">{{ __('Drag & drop to reorder. Use + to add more tasks.') }}</div>
                        </div>
                    </div>
                    <div class="lb-section-bd">

                        <div id="tasks-container" class="row">
                            @if ($leadBot->tasks->isEmpty())
                                <div class="task-item col-md-12 mb-3" data-index="0">
                                    <div class="card task-card lb-task-card">
                                        <div class="card-header lb-task-hd d-flex align-items-center justify-content-between gap-4 py-4">
                                            <span class="lb-drag drag-handle me-2" title="{{ __('Drag') }}"><i class="ni ni-bullet-list-67"></i></span>
                                            <div class="flex-grow-1 mb-2">
                                                <label class="task-name-label fw-bold d-block mt-4 mb-2">{{ __('Task Name') }}</label>
                                                <select name="tasks[0][task_type]" class="form-control task-type w-50 mb-2" required>
                                                    <option value="">--Select Task--</option>
                                                    <option value="create_contact">Create Contact</option>
                                                    <option value="call_api">Call API</option>
                                                    <option value="send_whatsapp">Send WhatsApp</option>
                                                </select>
                                                <input type="text" name="tasks[0][task_name]" class="form-control task-name mt-2 w-50 mb-2"
                                                    placeholder="Enter Task Name" style="display: none;">
                                            </div>
                                            <div class="d-flex align-items-center text-nowrap">
                                                <button class="lb-icon-btn toggle-task-body me-3" type="button"><span class="toggle-icon">˄</span></button>
                                                <div class="dropdown">
                                                    <button class="lb-icon-btn three-dots-btn" type="button" data-bs-toggle="dropdown">⋮</button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item rename-task-btn" href="javascript:void(0);">Rename</a></li>
                                                        <li><a class="dropdown-item remove-task" href="#">{{ __('Remove Task') }}</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body lb-task-bd lb-task-body additional-fields"></div>
                                        <input type="hidden" name="tasks[0][order]" class="task-order" value="0">
                                        <div class="card-footer text-center">
                                            <button type="button" class="lb-btn-primary add-task-btn" style="border-radius: 999px; width: 48px; height: 48px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem;">+</button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                @foreach ($leadBot->tasks as $index => $task)
                                    @php
                                        $cfg = $task->task_config ?? [];
                                        if (is_string($cfg)) {
                                            $cfg = json_decode($cfg, true) ?: [];
                                        }
                                        if (!is_array($cfg)) $cfg = [];
                                    @endphp
                                    <div class="task-item col-md-12 mb-10" data-index="{{ $index }}">
                                        <input type="hidden" name="tasks[{{ $index }}][id]" value="{{ $task->id }}">
                                        <div class="card task-card lb-task-card">
                                            <div class="card-header lb-task-hd d-flex align-items-center justify-content-between gap-4 py-4">
                                                <span class="lb-drag drag-handle me-2" title="{{ __('Drag') }}"><i class="ni ni-bullet-list-67"></i></span>
                                                <div class="flex-grow-1">
                                                    <label class="task-name-label fw-bold d-block mt-4 mb-2">{{ $task->task_name ?? 'Task Name' }}</label>
                                                    <select name="tasks[{{ $index }}][task_type]" class="form-control task-type w-50 mb-2" required>
                                                        <option value="">--Select Task--</option>
                                                        <option value="create_contact" {{ $task->task_type == 'create_contact' ? 'selected' : '' }}>Create Contact</option>
                                                        <option value="call_api" {{ $task->task_type == 'call_api' ? 'selected' : '' }}>Call API</option>
                                                        <option value="send_whatsapp" {{ $task->task_type == 'send_whatsapp' ? 'selected' : '' }}>Send WhatsApp</option>
                                                    </select>
                                                    <input type="text" name="tasks[{{ $index }}][task_name]" class="form-control task-name mt-2 w-50 mb-2"
                                                        placeholder="Enter Task Name" style="display: none;">
                                                </div>
                                                <div class="d-flex align-items-center text-nowrap">
                                                    <button class="lb-icon-btn toggle-task-body me-3" type="button"><span class="toggle-icon">˄</span></button>
                                                    <div class="dropdown">
                                                        <button class="lb-icon-btn three-dots-btn" type="button" data-bs-toggle="dropdown">⋮</button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item rename-task-btn" href="javascript:void(0);">{{ __('Rename') }}</a></li>
                                                            <li><a class="dropdown-item remove-task" href="#">{{ __('Remove Task') }}</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card-body lb-task-bd lb-task-body additional-fields">
                                                @if ($task->task_type == 'create_contact')
                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Phone') }}</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector" id="phoneVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --</option>
                                                                    @foreach ($mappedDataArray as $item)
                                                                        <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button" class="btn btn-secondary insert-variable-btn"
                                                                    data-target="phone{{ $index }}">
                                                                    <i class="fas fa-plus-circle me-1"></i> {{ __('Insert Variable') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="phone-input-container">
                                                            <input type="text" class="form-control" id="phone{{ $index }}"
                                                                name="tasks[{{ $index }}][task_config][phone]"
                                                                placeholder='Example: @{{country_code}}@{{phone_number}}'
                                                                value="{{ $cfg['phone'] ?? '' }}">
                                                            <div class="phone-preview" id="phonePreview{{ $index }}"></div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Name') }}</label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector"
                                                                    name="tasks[{{ $index }}][task_config][name_variable]">
                                                                    <option value="">-- Select Variable --</option>
                                                                    @foreach ($mappedDataArray as $item)
                                                                        <option value="{{ $item['key'] }}" {{ ($cfg['name_variable'] ?? '') == $item['key'] ? 'selected' : '' }}>
                                                                            {{ $item['label'] }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="input-group">
                                                                    <span class="input-group-text">OR</span>
                                                                    <input type="text" class="form-control"
                                                                        name="tasks[{{ $index }}][task_config][name_static]"
                                                                        value="{{ $cfg['name_static'] ?? '' }}"
                                                                        placeholder="{{ __('Static name') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Add to Groups') }}</label>
                                                        <select class="form-control groups-selector" multiple
                                                            name="tasks[{{ $index }}][task_config][add_groups][]">
                                                            @foreach ($groups as $group)
                                                                <option value="{{ $group->id }}" {{ in_array($group->id, $cfg['add_groups'] ?? [], true) ? 'selected' : '' }}>
                                                                    {{ $group->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Remove from Groups') }}</label>
                                                        <select class="form-control groups-selector" multiple
                                                            name="tasks[{{ $index }}][task_config][remove_groups][]">
                                                            @foreach ($groups as $group)
                                                                <option value="{{ $group->id }}" {{ in_array($group->id, $cfg['remove_groups'] ?? [], true) ? 'selected' : '' }}>
                                                                    {{ $group->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Tags') }}</label>
                                                        <input type="text" class="form-control tags-input"
                                                            name="tasks[{{ $index }}][task_config][tags]"
                                                            placeholder="Enter tags (comma separated)"
                                                            value="{{ $cfg['tags'] ?? '' }}">
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input"
                                                                name="tasks[{{ $index }}][task_config][create_lead]"
                                                                value="1"
                                                                {{ !empty($cfg['create_lead']) ? 'checked' : '' }}>
                                                            <label class="form-check-label">{{ __('Create Lead') }}</label>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Assign Lead To') }}</label>
                                                        <select class="form-control"
                                                            name="tasks[{{ $index }}][task_config][assign_to_user]">
                                                            <option value="">{{ __('-- Select Agent/User --') }}</option>
                                                            @foreach (($agents ?? []) as $agent)
                                                                <option value="{{ $agent->id }}" {{ (string) ($cfg['assign_to_user'] ?? '') === (string) $agent->id ? 'selected' : '' }}>
                                                                    {{ $agent->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group mb-2">
                                                        <div class="form-check">
                                                            <input type="checkbox" class="form-check-input add-custom-fields-checkbox"
                                                                name="tasks[{{ $index }}][task_config][add_custom_fields]"
                                                                value="1" {{ !empty($cfg['add_custom_fields']) ? 'checked' : '' }}>
                                                            <label class="form-check-label">{{ __('Add Custom Fields') }}</label>
                                                        </div>
                                                    </div>

                                                    <div class="custom-fields-container" style="display: {{ !empty($cfg['add_custom_fields']) ? 'block' : 'none' }};">
                                                        <div class="custom-field-group mb-3">
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>{{ __('Field Name') }}</strong></div>
                                                                <div class="col-md-7"><strong>{{ __('Field Value') }}</strong></div>
                                                                <div class="col-md-1"></div>
                                                            </div>

                                                            @foreach (($cfg['custom_fields'] ?? []) as $row)
                                                                @php $row = is_array($row) ? $row : []; @endphp
                                                                <div class="custom-field-item row mb-2">
                                                                    <div class="col-md-4">
                                                                        <select class="form-control custom-field-selector"
                                                                            name="tasks[{{ $index }}][task_config][custom_fields][][field_id]">
                                                                            <option value="">{{ __('-- Select Field --') }}</option>
                                                                            @foreach ($contactFields as $fid => $fname)
                                                                                <option value="{{ $fid }}" {{ (string) ($row['field_id'] ?? '') === (string) $fid ? 'selected' : '' }}>
                                                                                    {{ $fname }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <select class="form-control variable-selector"
                                                                                    name="tasks[{{ $index }}][task_config][custom_fields][][value_variable]">
                                                                                    <option value="">{{ __('-- Select Variable --') }}</option>
                                                                                    @foreach ($mappedDataArray as $item)
                                                                                        <option value="{{ $item['key'] }}" {{ (string) ($row['value_variable'] ?? '') === (string) $item['key'] ? 'selected' : '' }}>
                                                                                            {{ $item['label'] }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-text">OR</span>
                                                                                    <input type="text" class="form-control"
                                                                                        name="tasks[{{ $index }}][task_config][custom_fields][][value_static]"
                                                                                        placeholder="{{ __('Static value') }}"
                                                                                        value="{{ $row['value_static'] ?? '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button" class="btn btn-danger remove-custom-field">X</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>

                                                        <button type="button" class="btn btn-secondary add-custom-field mt-2">{{ __('Add Custom Field') }}</button>
                                                    </div>
                                                @elseif ($task->task_type == 'send_whatsapp')
                                                    <div class="form-group mb-4">
                                                        <label>{{ __('WhatsApp Phone') }}</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector" id="waPhoneVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --</option>
                                                                    @foreach ($mappedDataArray as $item)
                                                                        <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button" class="btn btn-secondary insert-variable-btn"
                                                                    data-target="waPhone{{ $index }}">
                                                                    <i class="fas fa-plus-circle me-1"></i> {{ __('Insert Variable') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="phone-input-container">
                                                            <input type="text" class="form-control" id="waPhone{{ $index }}"
                                                                name="tasks[{{ $index }}][task_config][wa_phone]"
                                                                placeholder='Example: @{{country_code}}@{{phone_number}}'
                                                                value="{{ $cfg['wa_phone'] ?? '' }}">
                                                            <div class="phone-preview" id="waPhonePreview{{ $index }}"></div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Campaign') }}</label>
                                                        <select class="form-control" name="tasks[{{ $index }}][task_config][campaign_id]">
                                                            <option value="">{{ __('-- Select Campaign --') }}</option>
                                                            @foreach ($whatsappCampaigns as $c)
                                                                <option value="{{ $c->id }}" {{ (string) ($cfg['campaign_id'] ?? '') === (string) $c->id ? 'selected' : '' }}>
                                                                    {{ $c->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <div class="mt-2">
                                                            <a href="{{ route('wpbox.api.index', ['type' => 'api']) }}" class="btn btn-sm btn-primary" target="_blank">{{ __('Create API Campaign') }}</a>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Payload (JSON)') }}</label>
                                                        <textarea name="tasks[{{ $index }}][task_config][wa_payload]" id="payloadWA{{ $index }}" class="form-control"
                                                            placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}' rows="4">{{ $cfg['wa_payload'] ?? '' }}</textarea>
                                                    </div>
                                                @elseif ($task->task_type == 'call_api')
                                                    <div class="form-group mb-4 url-input-container">
                                                        <label>{{ __('URL') }}</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector" id="urlVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --</option>
                                                                    @foreach ($mappedDataArray as $item)
                                                                        <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button" class="btn btn-secondary insert-variable-btn"
                                                                    data-target="url{{ $index }}">
                                                                    <i class="fas fa-plus-circle me-1"></i> {{ __('Insert Variable') }}
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <input type="text" class="form-control" id="url{{ $index }}"
                                                            name="tasks[{{ $index }}][task_config][url]"
                                                            placeholder='Example: https://api.example.com/users/@{{user_id}}'
                                                            value="{{ $cfg['url'] ?? '' }}">
                                                        <div class="url-preview" id="urlPreview{{ $index }}"></div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('HTTP Method') }}</label>
                                                        <select class="form-control" name="tasks[{{ $index }}][task_config][http_method]">
                                                            @foreach (['GET','POST','PUT','PATCH','DELETE'] as $m)
                                                                <option value="{{ $m }}" {{ (strtoupper($cfg['http_method'] ?? 'POST') === $m) ? 'selected' : '' }}>{{ $m }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>{{ __('Auth Type') }}</label>
                                                        <select class="form-control" name="tasks[{{ $index }}][task_config][auth_type]">
                                                            <option value="none" {{ ($cfg['auth_type'] ?? 'none') === 'none' ? 'selected' : '' }}>None</option>
                                                            <option value="basic" {{ ($cfg['auth_type'] ?? '') === 'basic' ? 'selected' : '' }}>Basic</option>
                                                            <option value="bearer" {{ ($cfg['auth_type'] ?? '') === 'bearer' ? 'selected' : '' }}>Bearer</option>
                                                        </select>
                                                    </div>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="tasks[{{ $index }}][task_config][add_headers]" value="1"
                                                            {{ !empty($cfg['add_headers']) ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ __('Add Headers') }}</label>
                                                    </div>
                                                    <div class="header-group">
                                                        @php
                                                            $hKeys = is_array($cfg['headers_key'] ?? null) ? $cfg['headers_key'] : [];
                                                            $hVars = is_array($cfg['headers_value_variable'] ?? null) ? $cfg['headers_value_variable'] : [];
                                                            $hStatics = is_array($cfg['headers_value_static'] ?? null) ? $cfg['headers_value_static'] : [];
                                                            $hCount = max(count($hKeys), count($hVars), count($hStatics));
                                                        @endphp

                                                        @if ($hCount > 0)
                                                            @for ($hi = 0; $hi < $hCount; $hi++)
                                                                <div class="header-item row mb-2">
                                                                    <div class="col-md-4">
                                                                        <input type="text" class="form-control"
                                                                            name="tasks[{{ $index }}][task_config][headers_key][]"
                                                                            placeholder="Header key"
                                                                            value="{{ $hKeys[$hi] ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <select class="form-control variable-selector"
                                                                                    name="tasks[{{ $index }}][task_config][headers_value_variable][]">
                                                                                    <option value="">{{ __('-- Select Variable --') }}</option>
                                                                                    @foreach ($mappedDataArray as $item)
                                                                                        <option value="{{ $item['key'] }}" {{ (string) ($hVars[$hi] ?? '') === (string) $item['key'] ? 'selected' : '' }}>
                                                                                            {{ $item['label'] }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-text">OR</span>
                                                                                    <input type="text" class="form-control"
                                                                                        name="tasks[{{ $index }}][task_config][headers_value_static][]"
                                                                                        placeholder="Static value"
                                                                                        value="{{ $hStatics[$hi] ?? '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button" class="btn btn-danger remove-header">X</button>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        @else
                                                            <div class="header-item row mb-2">
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control"
                                                                        name="tasks[{{ $index }}][task_config][headers_key][]" placeholder="Header key">
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <select class="form-control variable-selector"
                                                                                name="tasks[{{ $index }}][task_config][headers_value_variable][]">
                                                                                <option value="">{{ __('-- Select Variable --') }}</option>
                                                                                @foreach ($mappedDataArray as $item)
                                                                                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <span class="input-group-text">OR</span>
                                                                                <input type="text" class="form-control"
                                                                                    name="tasks[{{ $index }}][task_config][headers_value_static][]" placeholder="Static value">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <button type="button" class="btn btn-danger remove-header">X</button>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <button type="button" class="btn btn-secondary add-header mt-2">{{ __('Add Header') }}</button>
                                                    </div>

                                                    <div class="form-check mt-4 mb-3">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="tasks[{{ $index }}][task_config][add_params]" value="1"
                                                            {{ !empty($cfg['add_params']) ? 'checked' : '' }}>
                                                        <label class="form-check-label">{{ __('Add Params') }}</label>
                                                    </div>
                                                    <div class="param-group">
                                                        @php
                                                            $pKeys = is_array($cfg['params_key'] ?? null) ? $cfg['params_key'] : [];
                                                            $pVars = is_array($cfg['params_value_variable'] ?? null) ? $cfg['params_value_variable'] : [];
                                                            $pStatics = is_array($cfg['params_value_static'] ?? null) ? $cfg['params_value_static'] : [];
                                                            $pCount = max(count($pKeys), count($pVars), count($pStatics));
                                                        @endphp

                                                        @if ($pCount > 0)
                                                            @for ($pi = 0; $pi < $pCount; $pi++)
                                                                <div class="param-item row mb-2">
                                                                    <div class="col-md-4">
                                                                        <input type="text" class="form-control"
                                                                            name="tasks[{{ $index }}][task_config][params_key][]"
                                                                            placeholder="Parameter key"
                                                                            value="{{ $pKeys[$pi] ?? '' }}">
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <select class="form-control variable-selector"
                                                                                    name="tasks[{{ $index }}][task_config][params_value_variable][]">
                                                                                    <option value="">{{ __('-- Select Variable --') }}</option>
                                                                                    @foreach ($mappedDataArray as $item)
                                                                                        <option value="{{ $item['key'] }}" {{ (string) ($pVars[$pi] ?? '') === (string) $item['key'] ? 'selected' : '' }}>
                                                                                            {{ $item['label'] }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-group">
                                                                                    <span class="input-group-text">OR</span>
                                                                                    <input type="text" class="form-control"
                                                                                        name="tasks[{{ $index }}][task_config][params_value_static][]"
                                                                                        placeholder="Static value"
                                                                                        value="{{ $pStatics[$pi] ?? '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button" class="btn btn-danger remove-param">X</button>
                                                                    </div>
                                                                </div>
                                                            @endfor
                                                        @else
                                                            <div class="param-item row mb-2">
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control"
                                                                        name="tasks[{{ $index }}][task_config][params_key][]" placeholder="Parameter key">
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <select class="form-control variable-selector"
                                                                                name="tasks[{{ $index }}][task_config][params_value_variable][]">
                                                                                <option value="">{{ __('-- Select Variable --') }}</option>
                                                                                @foreach ($mappedDataArray as $item)
                                                                                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="input-group">
                                                                                <span class="input-group-text">OR</span>
                                                                                <input type="text" class="form-control"
                                                                                    name="tasks[{{ $index }}][task_config][params_value_static][]" placeholder="Static value">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-1">
                                                                    <button type="button" class="btn btn-danger remove-param">X</button>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <button type="button" class="btn btn-secondary add-param mt-2">{{ __('Add Param') }}</button>
                                                    </div>

                                                    <div class="form-group mt-4">
                                                        <label>{{ __('Payload (JSON)') }}</label>
                                                        <textarea name="tasks[{{ $index }}][task_config][data]" id="payload{{ $index }}" class="form-control"
                                                            placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}' rows="4">{{ $cfg['data'] ?? '' }}</textarea>
                                                    </div>
                                                @endif
                                            </div>

                                            <input type="hidden" name="tasks[{{ $index }}][order]" class="task-order" value="{{ $index }}">
                                            <div class="card-footer text-center">
                                                <button type="button" class="lb-btn-primary add-task-btn" style="border-radius: 999px; width: 48px; height: 48px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 1.4rem;">+</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                    </div>
                </section>
            </form>
        </div>
    </div>
</div>

<form action="{{ route('lead-bot.destroy', $leadBot->id) }}" method="POST" id="deleteLeadBotForm" class="d-none">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('js')
    @include('lead-bot::edit-script')
@endsection
