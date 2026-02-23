@extends('layouts.app', ['title' => __('Edit Workflow')])

@section('title')
    <title>{{ __('Edit Workflow') }}</title>
@endsection

@section('head')
    @include('work-flows::partials.ui')
    @include('work-flows::edit-css')
    <script>
    window.__workflowFormData = {
        mappedDataArray: @json($mappedDataArray ?? []),
        groups: @json($groups ?? []),
        contactFields: @json($contactFields ?? []),
        whatsappCampaigns: @json($whatsappCampaigns ?? []),
        autoretargetCampaigns: @json($autoretargetCampaigns ?? []),
        agents: @json($agents ?? [])
    };
    </script>
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope workflow-classy">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-diagram-3"></i></span>
                        {{ __('Edit Workflow') }}
                    </h1>
                    <span class="lb-subtitle">
                        <span class="lb-pill primary">{{ ucfirst($workflow->app_id) }}</span>
                        <span class="lb-pill ml-2">#{{ $workflow->id }}</span>
                    </span>
                </div>
                <div class="lb-toolbar">
                    <a href="{{ route('workflows.index') }}" class="lb-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back') }}
                    </a>
                    <button type="submit" form="workflow-form" class="lb-btn-primary">
                        <i class="ni ni-check-bold"></i> {{ __('Save') }}
                    </button>
                    <form action="{{ route('workflows.destroy', $workflow->id) }}" method="POST" class="d-inline m-0" id="workflow-delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="lb-btn-danger" id="deleteWorkflowBtn">
                            <i class="ni ni-fat-remove"></i> {{ __('Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <div class="lb-body">
            @if (session('success'))
                <div class="lb-flash">
                    <div class="alert alert-success mb-0">{{ session('success') }}</div>
                </div>
            @endif

            <form action="{{ route('workflows.update', $workflow->id) }}" method="POST" id="workflow-form" data-workflow-id="{{ $workflow->id }}" data-task-form-url="{{ route('workflows.task-form', ['workflowId' => $workflow->id, 'taskType' => '__TYPE__', 'index' => '__INDEX__']) }}">
                @csrf
                @method('PUT')

                <section class="lb-section">
                    <div class="lb-section-hd">
                        <h3 class="lb-section-title mb-0">{{ __('Workflow details') }}</h3>
                        <span class="text-muted small">{{ __('Trigger: when this happens…') }}</span>
                    </div>
                    <div class="lb-section-bd">
                        <div class="position-relative mb-3">
                            <div class="d-flex align-items-center">
                                <label id="workflownameLabel" class="font-weight-bold mb-0" style="font-size: 1.05rem;">{{ $workflow->name }}</label>
                                <input type="text" class="form-control d-none ml-3" id="workflowname" name="workflowname" value="{{ $workflow->name }}" style="max-width: 520px; border-radius: 12px; height: 44px;">
                                <button type="button" id="editWorkflowName" class="lb-icon-btn ml-2" title="{{ __('Rename') }}">
                                    <i class="ni ni-ruler-pencil"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="app_id" class="form-label">{{ __('Select App') }}</label>
                            <select class="form-control" id="app_id" name="app_id" required style="border-radius: 12px; min-height: 44px;">
                                <option value="" disabled>{{ __('Select an app') }}</option>
                                <option value="webhook" {{ $workflow->app_id == 'webhook' ? 'selected' : '' }}>Webhook
                                </option>
                                <option value="indiamart" {{ $workflow->app_id == 'indiamart' ? 'selected' : '' }}>IndiaMart
                                </option>
                                <option value="99acres" {{ $workflow->app_id == '99acres' ? 'selected' : '' }}>99Acres
                                </option>
                                <option value="housing" {{ $workflow->app_id == 'housing' ? 'selected' : '' }}>Housing.com
                                </option>
                                <option value="justdial" {{ $workflow->app_id == 'justdial' ? 'selected' : '' }}>Justdial
                                </option>
                                <option value="tradeindia" {{ $workflow->app_id == 'tradeindia' ? 'selected' : '' }}>
                                    TradeIndia
                                </option>
                                <option value="sulekha" {{ $workflow->app_id == 'sulekha' ? 'selected' : '' }}>Sulekha
                                </option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="trigger_event" class="form-label">{{ __('Trigger Event') }}</label>
                            <select class="form-control" id="trigger_event" name="trigger_event" style="border-radius: 12px; min-height: 44px;">
                                <option value="">{{ __('Select Trigger') }}</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <label class="form-label">{{ __('Webhook URL') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="text" class="form-control mr-2" id="webhookUrl"
                                    value="{{ url('/api/webhook/' . $workflow->webhook_token) }}" readonly style="border-radius: 12px; flex: 1;">
                                <button type="button" class="lb-icon-btn ml-2" id="copyWebhookUrl" title="{{ __('Copy') }}">
                                    <i class="ni ni-single-copy-04"></i>
                                </button>
                            </div>
                            <button type="button" class="lb-btn-soft mt-3" id="captureWebhookResponse" data-fetch-url="{{ url('workflow-webhooks/' . $workflow->id) }}" style="{{ isset($webhookResponse) && $webhookResponse->mapped_data ? 'display:none' : '' }}">{{ __('Capture Webhook Response') }}</button>
                            <button type="button" class="lb-btn-soft mt-3" id="recaptureWebhookResponse" data-fetch-url="{{ url('workflow-webhooks/' . $workflow->id) }}" style="{{ isset($webhookResponse) && $webhookResponse->mapped_data ? '' : 'display:none' }}">{{ __('Re-Capture Webhook Response') }}</button>
                            <div class="mt-3">
                                <span id="toggleResponseView" class="mt-2"
                                    style="display: {{ isset($webhookResponse) && $webhookResponse->mapped_data ? 'inline-block' : 'none' }}; cursor:pointer; fs-4; font-weight: bold;">
                                    Response Received (<span id="toggleIcon">&gt;</span>)
                                </span>
                                <div id="responseContainer" style="display: block;">
                                    <div id="webhookResponse" class="mt-3"></div>
                                    @if (isset($webhookResponse) && $webhookResponse->mapped_data)
                                        <div id="alreadyExistsWebhookResponse" class="mt-3">
                                            <div
                                                style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px;">
                                                <div class="row">
                                                    @foreach ($webhookResponse->mapped_data as $key => $item)
                                                        <div class="col-md-6 mb-2">
                                                            <input type="text" class="form-control font-weight-bold"
                                                                value="{{ $item['label'] }}" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <input type="text" class="form-control font-weight-bold"
                                                                value="{{ $item['value'] }}" readonly>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="lb-section">
                    <div class="lb-section-hd">
                        <h3 class="lb-section-title mb-0">{{ __('Tasks') }}</h3>
                        <span class="text-muted small">{{ __('Drag and drop to reorder. At least one task is required.') }}</span>
                    </div>
                    <div class="lb-section-bd">
                        <div id="tasks-container" class="row">
                            @if ($workflow->tasks->isEmpty())
                                <!-- Default task-item if no tasks exist -->
                                <div class="task-item col-md-12 mb-3" data-index="0">
                                    <div class="card task-card">
                                        <div class="card-header d-flex align-items-center justify-content-between gap-4 py-4">
                                            <span class="drag-handle mr-3" style="cursor:grab; color:#94a3b8;"><i class="ni ni-bullet-list-67"></i></span>
                                            <!-- LEFT SIDE -->
                                            <div class="flex-grow-1 mb-2">
                                                <label class="task-name-label font-weight-bold d-block mt-2 mb-2">{{ __('Task Name') }}</label>
                                                <select name="tasks[0][task_type]" class="form-control task-type w-50 mb-2" required>
                                                    <option value="">--{{ __('Select Task') }}--</option>
                                                    <option value="create_contact">{{ __('Create Contact') }}</option>
                                                    <option value="call_api">{{ __('Call API') }}</option>
                                                    <option value="send_whatsapp">{{ __('Send WhatsApp') }}</option>
                                                </select>
                                                <input type="text" name="tasks[0][task_name]" class="form-control task-name mt-2 w-50 mb-2" placeholder="{{ __('Enter Task Name') }}" style="display: none;">
                                            </div>
                                            <!-- RIGHT SIDE (Toggle & Three Dots) -->
                                            <div class="d-flex align-items-center text-nowrap">
                                                <button type="button" class="lb-icon-btn toggle-task-body mr-2"><span class="toggle-icon">˄</span></button>
                                                <div class="dropdown">
                                                    <button class="lb-icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">⋮</button>
                                                    <ul class="dropdown-menu dropdown-menu-right">
                                                        <li><a class="dropdown-item rename-task-btn" href="javascript:void(0);"><i class="ni ni-ruler-pencil mr-2"></i> {{ __('Rename') }}</a></li>
                                                        <li><a class="dropdown-item remove-task" href="#"><i class="ni ni-fat-remove mr-2"></i> {{ __('Remove Task') }}</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body additional-fields">
                                            <!-- Additional fields will be loaded here -->
                                        </div>
                                        <input type="hidden" name="tasks[0][order]" class="task-order" value="0">
                                        <div class="card-footer text-center">
                                            <button type="button" class="add-task-btn">+</button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                @foreach ($workflow->tasks as $index => $task)
                                    <div class="task-item col-md-12 mb-3" data-index="{{ $index }}">
                                        <input type="hidden" name="tasks[{{ $index }}][id]" value="{{ $task->id }}">
                                        <div class="card task-card">
                                            <div class="card-header d-flex align-items-center justify-content-between gap-4 py-4">
                                                <span class="drag-handle mr-3" style="cursor:grab; color:#94a3b8;"><i class="ni ni-bullet-list-67"></i></span>
                                                <!-- LEFT SIDE -->
                                                <div class="flex-grow-1">
                                                    <label class="task-name-label font-weight-bold d-block mt-2 mb-2">{{ $task->task_name ?? __('Task Name') }}</label>
                                                    <select name="tasks[{{ $index }}][task_type]" class="form-control task-type w-50 mb-2" required>
                                                        <option value="">--{{ __('Select Task') }}--</option>
                                                        <option value="create_contact" {{ $task->task_type == 'create_contact' ? 'selected' : '' }}>{{ __('Create Contact') }}</option>
                                                        <option value="call_api" {{ $task->task_type == 'call_api' ? 'selected' : '' }}>{{ __('Call API') }}</option>
                                                        <option value="send_whatsapp" {{ $task->task_type == 'send_whatsapp' ? 'selected' : '' }}>{{ __('Send WhatsApp') }}</option>
                                                    </select>
                                                    <input type="text" name="tasks[{{ $index }}][task_name]" class="form-control task-name mt-2 w-50 mb-2" placeholder="{{ __('Enter Task Name') }}" style="display: none;">
                                                </div>
                                                <!-- RIGHT SIDE (Toggle & Three Dots) -->
                                                <div class="d-flex align-items-center text-nowrap">
                                                    <button type="button" class="lb-icon-btn toggle-task-body mr-2"><span class="toggle-icon">˄</span></button>
                                                    <div class="dropdown">
                                                        <button class="lb-icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">⋮</button>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li><a class="dropdown-item rename-task-btn" href="javascript:void(0);"><i class="ni ni-ruler-pencil mr-2"></i> {{ __('Rename') }}</a></li>
                                                            <li><a class="dropdown-item remove-task" href="#"><i class="ni ni-fat-remove mr-2"></i> {{ __('Remove Task') }}</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body additional-fields">
                                                @if ($task->task_type == 'create_contact')
                                                    <!-- Contact Task Fields -->
                                                    <div class="form-group mb-4">
                                                        <label>Phone</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector"
                                                                    id="phoneVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --
                                                                    </option>
                                                                    @if (isset($mappedDataArray) && count($mappedDataArray))
                                                                        @foreach ($mappedDataArray as $item)
                                                                            <option value="{{ $item['key'] }}">
                                                                                {{ $item['label'] }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button"
                                                                    class="btn btn-secondary insert-variable-btn"
                                                                    data-target="phone{{ $index }}">
                                                                    <i class="fas fa-plus-circle me-1"></i> Insert Variable
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="phone-input-container">
                                                            <input type="text" class="form-control"
                                                                name="tasks[{{ $index }}][task_config][phone]"
                                                                id="phone{{ $index }}"
                                                                placeholder='Example: @{{ country_code }}@{{ phone_number }}'
                                                                value="{{ $task->task_config['phone'] ?? '' }}">
                                                            <div class="phone-preview"
                                                                id="phonePreview{{ $index }}">
                                                                Phone number preview will appear here
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-4">
                                                        <label>Name</label>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector"
                                                                    name="tasks[{{ $index }}][task_config][name_variable]">
                                                                    <option value="">-- Select Variable --</option>
                                                                    @if (isset($mappedDataArray) && count($mappedDataArray))
                                                                        @foreach ($mappedDataArray as $item)
                                                                            <option value="{{ $item['key'] }}"
                                                                                {{ isset($task->task_config['name_variable']) && $task->task_config['name_variable'] == $item['key'] ? 'selected' : '' }}>
                                                                                {{ $item['label'] }}
                                                                            </option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="input-group">
                                                                    <span class="input-group-text">OR</span>
                                                                    <input type="text" class="form-control"
                                                                        name="tasks[{{ $index }}][task_config][name_static]"
                                                                        placeholder="Static name"
                                                                        value="{{ $task->task_config['name_static'] ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                                <label>Add to Groups</label>
                                                                <select class="form-control groups-selector"
                                                                    name="tasks[{{ $index }}][task_config][add_groups][]"
                                                                    multiple="multiple">
                                                                    @foreach ($groups as $group)
                                                                        <option value="{{ $group->id }}"
                                                                            {{ isset($task->task_config['add_groups']) && in_array($group->id, $task->task_config['add_groups']) ? 'selected' : '' }}>
                                                                            {{ $group->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mb-4">
                                                                <label>Remove from Groups</label>
                                                                <select class="form-control groups-selector"
                                                                    name="tasks[{{ $index }}][task_config][remove_groups][]"
                                                                    multiple="multiple">
                                                                    @foreach ($groups as $group)
                                                                        <option value="{{ $group->id }}"
                                                                            {{ isset($task->task_config['remove_groups']) && in_array($group->id, $task->task_config['remove_groups']) ? 'selected' : '' }}>
                                                                            {{ $group->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Tags</label>
                                                        <input type="text" class="form-control tags-input"
                                                            name="tasks[{{ $index }}][task_config][tags]"
                                                            placeholder="Enter tags (comma separated)"
                                                            value="{{ $task->task_config['tags'] ?? '' }}">
                                                    </div>

                                                    <!-- Create Lead Section -->
                                                    <!-- Checkbox -->
                                                    <div class="form-group mb-4 mt-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input create-lead-checkbox"
                                                                type="checkbox" id="createLead{{ $index }}"
                                                                name="tasks[{{ $index }}][task_config][create_lead]"
                                                                value="1"
                                                                {{ isset($task->task_config['create_lead']) && $task->task_config['create_lead'] ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="createLead{{ $index }}">
                                                                Create Lead
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <!-- Assignment -->
                                                    <div class="form-group mb-4 lead-assignment-container"
                                                        id="leadAssignmentContainer{{ $index }}">
                                                        <label>Assign Lead To</label>
                                                        <select class="form-control lead-agent-selector"
                                                            name="tasks[{{ $index }}][task_config][assign_to_user]">
                                                            <option value="">-- Select Agent/User --</option>
                                                            @foreach ($agents as $agent)
                                                                <option value="{{ $agent->id }}"
                                                                    {{ isset($task->task_config['assign_to_user']) && $task->task_config['assign_to_user'] == $agent->id ? 'selected' : '' }}>
                                                                    {{ $agent->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <!-- Custom Fields Section -->
                                                    <div class="form-group mb-4 mt-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input add-custom-fields-checkbox"
                                                                type="checkbox" id="addCustomFields{{ $index }}"
                                                                name="tasks[{{ $index }}][task_config][add_custom_fields]"
                                                                value="1"
                                                                {{ isset($task->task_config['add_custom_fields']) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="addCustomFields{{ $index }}">
                                                                Add Custom Fields
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="custom-fields-container"
                                                        style="display: {{ isset($task->task_config['add_custom_fields']) ? 'block' : 'none' }};">
                                                        <div class="custom-field-group mb-3">
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Field Name</strong></div>
                                                                <div class="col-md-7"><strong>Field Value</strong></div>
                                                                <div class="col-md-1"></div>
                                                            </div>

                                                            {{-- Reconstruct custom fields from database format --}}
                                                            @php
                                                                $customFields = [];
                                                                if (isset($task->task_config['custom_fields'])) {
                                                                    $items = $task->task_config['custom_fields'];
                                                                    for ($i = 0; $i < count($items); $i += 3) {
                                                                        $customFields[] = [
                                                                            'field_id' => $items[$i]['field_id'] ?? '',
                                                                            'value_variable' =>
                                                                                $items[$i + 1]['value_variable'] ?? '',
                                                                            'value_static' =>
                                                                                $items[$i + 2]['value_static'] ?? '',
                                                                        ];
                                                                    }
                                                                }
                                                            @endphp

                                                            @foreach ($customFields as $customField)
                                                                <div class="custom-field-item row mb-2">
                                                                    <div class="col-md-4">
                                                                        <select class="form-control custom-field-selector"
                                                                            name="tasks[{{ $index }}][task_config][custom_fields][][field_id]">
                                                                            <option value="">-- Select Field --
                                                                            </option>
                                                                            @foreach ($contactFields as $id => $name)
                                                                                <option value="{{ $id }}"
                                                                                    {{ ($customField['field_id'] ?? '') == $id ? 'selected' : '' }}>
                                                                                    {{ $name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <select
                                                                                    class="form-control variable-selector"
                                                                                    name="tasks[{{ $index }}][task_config][custom_fields][][value_variable]">
                                                                                    <option value="">-- Select
                                                                                        Variable --</option>
                                                                                    @foreach ($mappedDataArray as $item)
                                                                                        <option
                                                                                            value="{{ $item['key'] }}"
                                                                                            {{ ($customField['value_variable'] ?? '') == $item['key'] ? 'selected' : '' }}>
                                                                                            {{ $item['label'] }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-group">
                                                                                    <span
                                                                                        class="input-group-text">OR</span>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="tasks[{{ $index }}][task_config][custom_fields][][value_static]"
                                                                                        placeholder="Static value"
                                                                                        value="{{ $customField['value_static'] ?? '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-custom-field">X</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button"
                                                            class="btn btn-secondary add-custom-field mb-4">+ Add Custom
                                                            Field</button>
                                                    </div>
                                                @elseif($task->task_type == 'send_whatsapp')
                                                    <!-- WhatsApp Task Fields -->
                                                    <div class="form-group mb-4">
                                                        <label>Send to</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector"
                                                                    id="waPhoneVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --
                                                                    </option>
                                                                    @if (isset($mappedDataArray) && count($mappedDataArray))
                                                                        @foreach ($mappedDataArray as $item)
                                                                            <option value="{{ $item['key'] }}">
                                                                                {{ $item['label'] }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button"
                                                                    class="btn btn-secondary insert-variable-btn"
                                                                    data-target="waPhone{{ $index }}">
                                                                    <i class="fas fa-plus-circle me-1"></i> Insert Variable
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="phone-input-container">
                                                            <input type="text" class="form-control"
                                                                name="tasks[{{ $index }}][task_config][wa_phone]"
                                                                id="waPhone{{ $index }}"
                                                                placeholder='Example: @{{ country_code }}@{{ phone_number }}'
                                                                value="{{ $task->task_config['wa_phone'] ?? '' }}">
                                                            <div class="phone-preview"
                                                                id="waPhonePreview{{ $index }}">
                                                                Phone number preview will appear here
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group mb-4">
                                                        <label>Campaign</label>
                                                        <select class="form-control"
                                                            name="tasks[{{ $index }}][task_config][campaign_id]"
                                                            required>
                                                            <option value="">-- Select Campaign --</option>
                                                            @foreach ($whatsappCampaigns as $campaign)
                                                                <option value="{{ $campaign->id }}"
                                                                    {{ isset($task->task_config['campaign_id']) && $task->task_config['campaign_id'] == $campaign->id ? 'selected' : '' }}>
                                                                    {{ $campaign->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="alert alert-info">
                                                            When a contact enters this stage, the selected API campaign will
                                                            be
                                                            triggered. Create new API campaigns using the button below.
                                                            <a href="{{ route('wpbox.api.index', ['type' => 'api']) }}"
                                                                class="btn btn-sm btn-primary" target="_blank">Create API
                                                                Campaign</a>
                                                        </div>
                                                    </div>
                                                    <!-- Payload Field -->
                                                    <!-- PAYLOAD SECTION - FIXED -->
                                                    <div class="form-group mt-4">
                                                        <label>Data to Pass (Payload)</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector"
                                                                    id="payloadWAVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --
                                                                    </option>
                                                                    @foreach ($mappedDataArray as $item)
                                                                        <option value="{{ $item['key'] }}">
                                                                            {{ $item['label'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button"
                                                                    class="btn btn-secondary insert-variable-btn"
                                                                    data-target="payloadWA{{ $index }}"><i
                                                                        class="fas fa-plus-circle me-1"></i> Insert
                                                                    Variable</button>
                                                            </div>
                                                        </div>
                                                        <textarea name="tasks[{{ $index }}][task_config][wa_payload]" id="payloadWA{{ $index }}"
                                                            class="form-control" placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}'
                                                            rows="4">{{ $task->task_config['wa_payload'] ?? '' }}</textarea>
                                                    </div>

                                                    <!-- Autoretarget Option -->
                                                    <div class="mt-5 mb-4">
                                                        <label
                                                            class="form-check form-switch form-check-custom form-check-solid">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="tasks[{{ $index }}][task_config][autoretarget_enabled]"
                                                                id="autoretarget_enabled_{{ $index }}"
                                                                value="1"
                                                                {{ isset($task->task_config['autoretarget_enabled']) && $task->task_config['autoretarget_enabled'] ? 'checked' : '' }} />
                                                            <span
                                                                class="form-check-label fw-semibold text-muted">{{ __('Enable AutoRetarget') }}</span>
                                                        </label>
                                                    </div>

                                                    <div id="autoretarget_section_{{ $index }}"
                                                        style="display: {{ isset($task->task_config['autoretarget_enabled']) && $task->task_config['autoretarget_enabled'] ? 'block' : 'none' }};">
                                                        <div class="mb-5">
                                                            <label for="autoretarget_campaign_id_{{ $index }}"
                                                                class="form-label">{{ __('AutoRetarget Campaign') }}</label>
                                                            <select class="form-select form-select-solid"
                                                                id="autoretarget_campaign_id_{{ $index }}"
                                                                name="tasks[{{ $index }}][task_config][autoretarget_campaign_id]">
                                                                <option value="">
                                                                    {{ __('Select an AutoRetarget Campaign') }}
                                                                </option>
                                                                @foreach ($autoretargetCampaigns as $autoretargetCampaign)
                                                                    <option value="{{ $autoretargetCampaign->id }}"
                                                                        {{ isset($task->task_config['autoretarget_campaign_id']) && $task->task_config['autoretarget_campaign_id'] == $autoretargetCampaign->id ? 'selected' : '' }}>
                                                                        {{ $autoretargetCampaign->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                @elseif($task->task_type == 'send_email')
                                                    <div class="form-group mb-4">
                                                        <label>Email To</label>
                                                        <input type="email"
                                                            name="tasks[{{ $index }}][task_config][to]"
                                                            class="form-control" placeholder="Enter recipient email"
                                                            value="{{ $task->task_config['to'] ?? '' }}" required>
                                                    </div>
                                                    <div class="form-group mb-4">
                                                        <label>Email Subject</label>
                                                        <input type="text"
                                                            name="tasks[{{ $index }}][task_config][subject]"
                                                            class="form-control" placeholder="Enter email subject"
                                                            value="{{ $task->task_config['subject'] ?? '' }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Email Message</label>
                                                        <textarea name="tasks[{{ $index }}][task_config][message]" class="form-control"
                                                            placeholder="Enter email message" required>{{ $task->task_config['message'] ?? '' }}</textarea>
                                                    </div>
                                                @elseif($task->task_type == 'send_sms')
                                                    <div class="form-group mb-4">
                                                        <label>Phone Number</label>
                                                        <input type="text"
                                                            name="tasks[{{ $index }}][task_config][phone]"
                                                            class="form-control" placeholder="Enter phone number"
                                                            value="{{ $task->task_config['phone'] ?? '' }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>SMS Message</label>
                                                        <textarea name="tasks[{{ $index }}][task_config][message]" class="form-control"
                                                            placeholder="Enter SMS message" required>{{ $task->task_config['message'] ?? '' }}</textarea>
                                                    </div>
                                                @elseif($task->task_type == 'call_api')
                                                    <!-- Call API Task Fields -->
                                                    <div class="form-group mb-4">
                                                        <label>API URL</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector"
                                                                    id="urlVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --
                                                                    </option>
                                                                    @if (isset($mappedDataArray) && count($mappedDataArray))
                                                                        @foreach ($mappedDataArray as $item)
                                                                            <option value="{{ $item['key'] }}">
                                                                                {{ $item['label'] }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button"
                                                                    class="btn btn-secondary insert-variable-btn"
                                                                    data-target="url{{ $index }}">
                                                                    <i class="fas fa-plus-circle me-1"></i> Insert Variable
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="url-input-container">
                                                            <input type="text" class="form-control"
                                                                name="tasks[{{ $index }}][task_config][url]"
                                                                id="url{{ $index }}"
                                                                placeholder='Example: https://api.example.com/users/@{{ user_id }}'
                                                                value="{{ $task->task_config['url'] ?? '' }}">
                                                            <div class="url-preview" id="urlPreview{{ $index }}">
                                                                URL preview will appear here
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group mb-4">
                                                        <label>HTTP Method</label>
                                                        <select class="form-control"
                                                            name="tasks[{{ $index }}][task_config][http_method]">
                                                            <option value="GET"
                                                                {{ !isset($task->task_config['http_method']) || $task->task_config['http_method'] == 'GET' ? 'selected' : '' }}>
                                                                GET</option>
                                                            <option value="POST"
                                                                {{ isset($task->task_config['http_method']) && $task->task_config['http_method'] == 'POST' ? 'selected' : '' }}>
                                                                POST</option>
                                                            <option value="PUT"
                                                                {{ isset($task->task_config['http_method']) && $task->task_config['http_method'] == 'PUT' ? 'selected' : '' }}>
                                                                PUT</option>
                                                            <option value="PATCH"
                                                                {{ isset($task->task_config['http_method']) && $task->task_config['http_method'] == 'PATCH' ? 'selected' : '' }}>
                                                                PATCH</option>
                                                            <option value="DELETE"
                                                                {{ isset($task->task_config['http_method']) && $task->task_config['http_method'] == 'DELETE' ? 'selected' : '' }}>
                                                                DELETE</option>
                                                        </select>
                                                    </div>

                                                    <!-- Authentication Section -->
                                                    <div class="form-group mb-4">
                                                        <label>Authentication</label>
                                                        <select class="form-control api-auth-type"
                                                            name="tasks[{{ $index }}][task_config][auth_type]">
                                                            <option value="none"
                                                                {{ isset($task->task_config['auth_type']) && $task->task_config['auth_type'] == 'none' ? 'selected' : '' }}>
                                                                No Authentication</option>
                                                            <option value="basic"
                                                                {{ isset($task->task_config['auth_type']) && $task->task_config['auth_type'] == 'basic' ? 'selected' : '' }}>
                                                                Basic Authentication</option>
                                                            <option value="bearer"
                                                                {{ isset($task->task_config['auth_type']) && $task->task_config['auth_type'] == 'bearer' ? 'selected' : '' }}>
                                                                Bearer Token</option>
                                                        </select>
                                                    </div>

                                                    <!-- Basic Auth Fields -->
                                                    <div class="api-basic-auth"
                                                        style="display: {{ isset($task->task_config['auth_type']) && $task->task_config['auth_type'] == 'basic' ? 'block' : 'none' }};">
                                                        <div class="form-group mb-4">
                                                            <label>Username</label>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <select class="form-control variable-selector"
                                                                        name="tasks[{{ $index }}][task_config][basic_auth_username_variable]">
                                                                        <option value="">-- Select Variable --
                                                                        </option>
                                                                        @foreach ($mappedDataArray as $item)
                                                                            <option value="{{ $item['key'] }}"
                                                                                {{ isset($task->task_config['basic_auth_username_variable']) && $task->task_config['basic_auth_username_variable'] == $item['key'] ? 'selected' : '' }}>
                                                                                {{ $item['label'] }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">OR</span>
                                                                        <input type="text" class="form-control"
                                                                            name="tasks[{{ $index }}][task_config][basic_auth_username_static]"
                                                                            placeholder="Static username"
                                                                            value="{{ $task->task_config['basic_auth_username_static'] ?? '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group mb-4">
                                                            <label>Password</label>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <select class="form-control variable-selector"
                                                                        name="tasks[{{ $index }}][task_config][basic_auth_password_variable]">
                                                                        <option value="">-- Select Variable --
                                                                        </option>
                                                                        @foreach ($mappedDataArray as $item)
                                                                            <option value="{{ $item['key'] }}"
                                                                                {{ isset($task->task_config['basic_auth_password_variable']) && $task->task_config['basic_auth_password_variable'] == $item['key'] ? 'selected' : '' }}>
                                                                                {{ $item['label'] }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">OR</span>
                                                                        <input type="text" class="form-control"
                                                                            name="tasks[{{ $index }}][task_config][basic_auth_password_static]"
                                                                            placeholder="Static password"
                                                                            value="{{ $task->task_config['basic_auth_password_static'] ?? '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Bearer Token Fields -->
                                                    <div class="api-bearer-auth"
                                                        style="display: {{ isset($task->task_config['auth_type']) && $task->task_config['auth_type'] == 'bearer' ? 'block' : 'none' }};">
                                                        <div class="form-group mb-4">
                                                            <label>Token</label>
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <select class="form-control variable-selector"
                                                                        name="tasks[{{ $index }}][task_config][bearer_token_variable]">
                                                                        <option value="">-- Select Variable --
                                                                        </option>
                                                                        @foreach ($mappedDataArray as $item)
                                                                            <option value="{{ $item['key'] }}"
                                                                                {{ isset($task->task_config['bearer_token_variable']) && $task->task_config['bearer_token_variable'] == $item['key'] ? 'selected' : '' }}>
                                                                                {{ $item['label'] }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="input-group">
                                                                        <span class="input-group-text">OR</span>
                                                                        <input type="text" class="form-control"
                                                                            name="tasks[{{ $index }}][task_config][bearer_token_static]"
                                                                            placeholder="Static token"
                                                                            value="{{ $task->task_config['bearer_token_static'] ?? '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Headers Section -->
                                                    <div class="form-group mb-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="addHeadersCheckbox{{ $index }}"
                                                                name="tasks[{{ $index }}][task_config][add_headers]"
                                                                value="1"
                                                                {{ $task->task_config['add_headers'] ?? false ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="addHeadersCheckbox{{ $index }}">Add
                                                                Headers</label>
                                                        </div>
                                                    </div>

                                                    <div class="headers-container"
                                                        style="display: {{ $task->task_config['add_headers'] ?? false ? 'block' : 'none' }};">
                                                        <div class="header-group mb-3">
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Header Key</strong></div>
                                                                <div class="col-md-7"><strong>Header Value</strong></div>
                                                                <div class="col-md-1"></div>
                                                            </div>

                                                            @foreach ($task->task_config['headers_key'] ?? [] as $i => $key)
                                                                <div class="header-item row mb-2">
                                                                    <div class="col-md-4">
                                                                        <input type="text" class="form-control"
                                                                            name="tasks[{{ $index }}][task_config][headers_key][]"
                                                                            placeholder="Header key"
                                                                            value="{{ $key }}">
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <select
                                                                                    class="form-control variable-selector"
                                                                                    name="tasks[{{ $index }}][task_config][headers_value_variable][]">
                                                                                    <option value="">-- Select
                                                                                        Variable --</option>
                                                                                    @foreach ($mappedDataArray as $item)
                                                                                        <option
                                                                                            value="{{ $item['key'] }}"
                                                                                            {{ ($task->task_config['headers_value_variable'][$i] ?? '') == $item['key'] ? 'selected' : '' }}>
                                                                                            {{ $item['label'] }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-group">
                                                                                    <span
                                                                                        class="input-group-text">OR</span>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="tasks[{{ $index }}][task_config][headers_value_static][]"
                                                                                        placeholder="Static value"
                                                                                        value="{{ $task->task_config['headers_value_static'][$i] ?? '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-header">X</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="btn btn-secondary add-header mb-4">+
                                                            Add Header</button>
                                                    </div>

                                                    <!-- Parameters Section -->
                                                    <div class="form-group mb-4 mt-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="addParamsCheckbox{{ $index }}"
                                                                name="tasks[{{ $index }}][task_config][add_params]"
                                                                value="1"
                                                                {{ $task->task_config['add_params'] ?? false ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="addParamsCheckbox{{ $index }}">Set
                                                                Parameters</label>
                                                        </div>
                                                    </div>

                                                    <div class="params-container"
                                                        style="display: {{ $task->task_config['add_params'] ?? false ? 'block' : 'none' }};">
                                                        <div class="param-group mb-3">
                                                            <div class="row mb-2">
                                                                <div class="col-md-4"><strong>Parameter Key</strong></div>
                                                                <div class="col-md-7"><strong>Parameter Value</strong>
                                                                </div>
                                                                <div class="col-md-1"></div>
                                                            </div>

                                                            @foreach ($task->task_config['params_key'] ?? [] as $i => $key)
                                                                <div class="param-item row mb-2">
                                                                    <div class="col-md-4">
                                                                        <input type="text" class="form-control"
                                                                            name="tasks[{{ $index }}][task_config][params_key][]"
                                                                            placeholder="Parameter key"
                                                                            value="{{ $key }}">
                                                                    </div>
                                                                    <div class="col-md-7">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <select
                                                                                    class="form-control variable-selector"
                                                                                    name="tasks[{{ $index }}][task_config][params_value_variable][]">
                                                                                    <option value="">-- Select
                                                                                        Variable --</option>
                                                                                    @foreach ($mappedDataArray as $item)
                                                                                        <option
                                                                                            value="{{ $item['key'] }}"
                                                                                            {{ ($task->task_config['params_value_variable'][$i] ?? '') == $item['key'] ? 'selected' : '' }}>
                                                                                            {{ $item['label'] }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="input-group">
                                                                                    <span
                                                                                        class="input-group-text">OR</span>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="tasks[{{ $index }}][task_config][params_value_static][]"
                                                                                        placeholder="Static value"
                                                                                        value="{{ $task->task_config['params_value_static'][$i] ?? '' }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-1">
                                                                        <button type="button"
                                                                            class="btn btn-danger remove-param">X</button>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <button type="button" class="btn btn-secondary add-param mb-4">+
                                                            Add Parameter</button>
                                                    </div>

                                                    <!-- Payload Section -->
                                                    <div class="form-group">
                                                        <label>Data to Pass (Payload)</label>
                                                        <div class="row mb-2">
                                                            <div class="col-md-6">
                                                                <select class="form-control variable-selector"
                                                                    id="payloadVariableSelector{{ $index }}">
                                                                    <option value="">-- Select Variable to Insert --
                                                                    </option>
                                                                    @foreach ($mappedDataArray as $item)
                                                                        <option value="{{ $item['key'] }}">
                                                                            {{ $item['label'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button"
                                                                    class="btn btn-secondary insert-variable-btn"
                                                                    data-target="payload{{ $index }}"><i
                                                                        class="fas fa-plus-circle me-1"></i> Insert
                                                                    Variable</button>
                                                            </div>
                                                        </div>
                                                        <textarea name="tasks[{{ $index }}][task_config][data]" id="payload{{ $index }}"
                                                            class="form-control" placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}'
                                                            rows="4">{{ $task->task_config['data'] ?? '' }}</textarea>
                                                    </div>
                                                @endif
                                            </div>
                                            <input type="hidden" name="tasks[{{ $index }}][order]"
                                                class="task-order" value="{{ $index }}">
                                            <div class="card-footer text-center">
                                                <button type="button" class="add-task-btn">+</button>
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
@endsection

@push('js')
    @include('work-flows::edit-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteBtn = document.getElementById('deleteWorkflowBtn');
            var deleteForm = document.getElementById('workflow-delete-form');
            if (deleteBtn && deleteForm) {
                deleteBtn.addEventListener('click', function() {
                    Swal.fire({
                        title: "{{ __('Delete Workflow?') }}",
                        text: "{{ __('This action cannot be undone.') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, delete') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                        reverseButtons: true,
                        customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light' },
                        buttonsStyling: false
                    }).then(function(result) { if (result.isConfirmed) deleteForm.submit(); });
                });
            }
        });
    </script>
@endpush

@push('js-late')
<script>
(function() {
    var taskNames = { create_contact: "Task: Create Contact", send_email: "Task: Send Email", send_sms: "Task: Send SMS", call_api: "Task: Call API", send_whatsapp: "Task: Send WhatsApp" };

    function getTaskFormUrl(taskType, index) {
        var formEl = document.getElementById('workflow-form');
        if (!formEl) return null;
        var base = formEl.getAttribute('data-task-form-url');
        if (!base) return null;
        return base.replace('__TYPE__', encodeURIComponent(taskType)).replace('__INDEX__', encodeURIComponent(index));
    }

    function reinitFormElements(container) {
        if (window._workflowInitFormElements && typeof window._workflowInitFormElements === 'function') {
            setTimeout(window._workflowInitFormElements, 80);
        } else if (window.jQuery) {
            try { window.jQuery('select').not('.task-type').select2({ width: '100%' }); } catch(e) {}
        }
    }

    function loadFormInto(selectEl) {
        var taskType = (selectEl.value || '').trim();
        var taskItem = selectEl.closest('.task-item');
        if (!taskItem) return;
        var container = taskItem.querySelector('.additional-fields') || taskItem.querySelector('.card-body');
        if (!container) return;
        var idx = taskItem.getAttribute('data-index') || '0';
        var label = taskItem.querySelector('.task-name-label');
        if (label && taskNames[taskType]) label.textContent = taskNames[taskType];
        document.querySelectorAll('.task-item').forEach(function(item) {
            var body = item.querySelector('.card-body.additional-fields') || item.querySelector('.additional-fields');
            var icon = item.querySelector('.toggle-icon');
            if (item === taskItem) {
                if (body) body.style.display = '';
                if (icon) icon.textContent = '\u02C4';
            } else {
                if (body) body.style.display = 'none';
                if (icon) icon.textContent = '\u02C5';
            }
        });
        if (taskType !== 'create_contact' && taskType !== 'call_api' && taskType !== 'send_whatsapp') {
            container.innerHTML = '';
            return;
        }
        var url = getTaskFormUrl(taskType, idx);
        if (url) {
            container.innerHTML = '<div class="text-muted py-3">Loading form…</div>';
            fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    container.innerHTML = (data && data.html) ? data.html : '';
                    reinitFormElements(container);
                })
                .catch(function() {
                    container.innerHTML = '<div class="text-danger py-2">Could not load form. Please refresh the page.</div>';
                });
        } else {
            var buildFn = window._buildWorkflowTaskFormSync || window._buildWorkflowTaskForm;
            var html = (typeof buildFn === 'function') ? buildFn(taskType, idx) : '';
            container.innerHTML = html || '';
            reinitFormElements(container);
        }
    }

    function onChange(e) {
        var el = e.target;
        if (!el || !el.classList || !el.classList.contains('task-type')) return;
        loadFormInto(el);
    }

    function init() {
        document.querySelectorAll('.task-type').forEach(function(sel) {
            sel.removeEventListener('change', onChange);
            sel.addEventListener('change', onChange);
            try { if (window.jQuery && window.jQuery(sel).data('select2')) window.jQuery(sel).select2('destroy'); } catch(e) {}
        });
        document.querySelectorAll('.task-type').forEach(function(sel) {
            var v = sel.value;
            if (v && (v === 'create_contact' || v === 'call_api' || v === 'send_whatsapp')) {
                var taskItem = sel.closest('.task-item');
                var container = taskItem ? (taskItem.querySelector('.additional-fields') || taskItem.querySelector('.card-body')) : null;
                if (container) {
                    var cnt = container.querySelectorAll('.form-group, .form-check, select').length;
                    if (cnt < 2) loadFormInto(sel);
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { setTimeout(init, 100); });
    } else {
        setTimeout(init, 100);
    }
})();
</script>
@endpush
