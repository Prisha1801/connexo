@extends('layouts.app-client')
@section('css')
    @include('work-flows::edit-css')
@endsection
@section('content')
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <div class="container-xxl card">
                <form action="{{ route('workflows.update', $workflow->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <!--begin::Card header-->
                    <div class="card-header border-0 pt-6">
                        <h2 class="card-title align-items-start flex-column mb-0">
                            <div class="position-relative">
                                <small class="text-muted">{{ __('Trigger: When this happens …') }}</small>
                                <div class="d-flex align-items-center mt-4">
                                    <label id="workflownameLabel"
                                        class="form-label fw-bold fs-4 flex-grow-1">{{ $workflow->name }}</label>
                                    <input type="text" class="form-control form-control-solid d-none" id="workflowname"
                                        name="workflowname" value="{{ $workflow->name }}">
                                    <span id="editWorkflowName" class="ms-2" style="cursor: pointer;"><i
                                            class="ki-duotone ki-pencil fs-2">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i></span>
                                </div>
                            </div>
                        </h2>
                    </div>
                    <!--end::Card header-->
                    <!--begin::Card body-->
                    <div class="card-body py-4">
                        <div class="mb-3">
                            <label for="app_id" class="form-label">Select App</label>
                            <select class="form-control" id="app_id" name="app_id" required>
                                <option value="" disabled>Select an app</option>
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
                            <label for="trigger_event" class="form-label">Trigger Event</label>
                            <select class="form-control" id="trigger_event" name="trigger_event">
                                <option value="">Select Trigger</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <label for="trigger_event" class="form-label">Webhook URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="webhookUrl"
                                    value="{{ url('/api/webhook/' . $workflow->webhook_token) }}" readonly>
                                <button type="button" class="btn btn-secondary" id="copyWebhookUrl">📋 Copy</button>
                            </div>
                            @if (isset($webhookResponse) && $webhookResponse->mapped_data)
                                <button class="btn btn-secondary mt-4" id="recaptureWebhookResponse">Re-Capture Webhook
                                    Response</button>
                            @else
                                <button class="btn btn-secondary mt-4" id="captureWebhookResponse">Capture Webhook
                                    Response</button>
                            @endif
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
                        <hr>
                        <h3 class="mb-3">Tasks</h3>
                        <p class="mb-3">Drag and drop tasks to arrange execution order. (At least one task is required.)
                        </p>
                        <div id="tasks-container" class="row">
                            @if ($workflow->tasks->isEmpty())
                                <!-- Default task-item if no tasks exist -->
                                <div class="task-item col-md-12 mb-3" data-index="0">
                                    <div class="card">
                                        <div
                                            class="card-header d-flex align-items-center justify-content-between gap-4 py-4">
                                            <i class="ki-duotone ki-abstract-20 drag-handle me-2 fs-2x text-gray-400">
                                                <span class="path1"></span>
                                                <span class="path2"></span>
                                            </i>
                                            <!-- LEFT SIDE -->
                                            <div class="flex-grow-1 mb-2">
                                                <label class="task-name-label fw-bold d-block mt-4 mb-2">Task Name</label>
                                                <select name="tasks[0][task_type]" class="form-control task-type w-50 mb-2"
                                                    required>
                                                    <option value="">--Select Task--</option>
                                                    <option value="create_contact">Create Contact</option>
                                                    {{-- <option value="send_email">Send Email</option> --}}
                                                    {{-- <option value="send_sms">Send SMS</option> --}}
                                                    <option value="call_api">Call API</option>
                                                    <option value="send_whatsapp">Send WhatsApp</option>
                                                </select>
                                                <input type="text" name="tasks[0][task_name]"
                                                    class="form-control task-name mt-2 w-50 mb-2"
                                                    placeholder="Enter Task Name" style="display: none;">
                                            </div>
                                            <!-- RIGHT SIDE (Toggle & Three Dots) -->
                                            <div class="d-flex align-items-center text-nowrap">
                                                <button class="btn btn-light toggle-task-body me-3">
                                                    <span class="toggle-icon">˄</span>
                                                </button>
                                                <div class="dropdown">
                                                    <button class="btn btn-light three-dots-btn" type="button"
                                                        data-bs-toggle="dropdown">
                                                        ⋮
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item rename-task-btn"
                                                                href="javascript:void(0);"> <i
                                                                    class="ki-duotone ki-pencil fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i> {{ __('Rename') }}</a>
                                                        </li>
                                                        <li><a class="dropdown-item remove-task" href="#">
                                                                <i class="ki-duotone ki-trash fs-2">
                                                                    <span class="path1"></span>
                                                                    <span class="path2"></span>
                                                                </i> {{ __('Remove Task') }}
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body additional-fields">
                                            <!-- Additional fields will be loaded here -->
                                        </div>
                                        <input type="hidden" name="tasks[0][order]" class="task-order" value="0">
                                        <div class="card-footer text-center">
                                            <button type="button" class="btn btn-primary add-task-btn"
                                                style="font-size: 1.5rem; padding: 8px 16px; border-radius: 50%;">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                @foreach ($workflow->tasks as $index => $task)
                                    <div class="task-item col-md-12 mb-10" data-index="{{ $index }}">
                                        <input type="hidden" name="tasks[{{ $index }}][id]"
                                            value="{{ $task->id }}">
                                        <div class="card task-card">
                                            <div
                                                class="card-header d-flex align-items-center justify-content-between gap-4 py-4">
                                                <i class="ki-duotone ki-abstract-20 drag-handle me-2 fs-2x text-gray-400">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>
                                                <!-- LEFT SIDE -->
                                                <div class="flex-grow-1">
                                                    <label
                                                        class="task-name-label fw-bold d-block mt-4 mb-2">{{ $task->task_name ?? 'Task Name' }}</label>
                                                    <select name="tasks[{{ $index }}][task_type]"
                                                        class="form-control task-type w-50 mb-2" required>
                                                        <option value="">--Select Task--</option>
                                                        <option value="create_contact"
                                                            {{ $task->task_type == 'create_contact' ? 'selected' : '' }}>
                                                            Create
                                                            Contact
                                                        </option>
                                                        {{-- <option value="send_email"
                                                        {{ $task->task_type == 'send_email' ? 'selected' : '' }}>Send Email
                                                    </option>
                                                    <option value="send_sms"
                                                        {{ $task->task_type == 'send_sms' ? 'selected' : '' }}>
                                                        Send SMS</option> --}}
                                                        <option value="call_api"
                                                            {{ $task->task_type == 'call_api' ? 'selected' : '' }}>
                                                            Call API</option>
                                                        <option value="send_whatsapp"
                                                            {{ $task->task_type == 'send_whatsapp' ? 'selected' : '' }}>
                                                            Send WhatsApp</option>
                                                    </select>
                                                    <input type="text" name="tasks[{{ $index }}][task_name]"
                                                        class="form-control task-name mt-2 w-50 mb-2"
                                                        placeholder="Enter Task Name" style="display: none;">
                                                </div>
                                                <!-- RIGHT SIDE (Toggle & Three Dots) -->
                                                <div class="d-flex align-items-center text-nowrap">
                                                    <button class="btn btn-light toggle-task-body me-3">
                                                        <span class="toggle-icon">˄</span>
                                                    </button>
                                                    <div class="dropdown">
                                                        <button class="btn btn-light three-dots-btn" type="button"
                                                            data-bs-toggle="dropdown">
                                                            ⋮
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li><a class="dropdown-item rename-task-btn"
                                                                    href="javascript:void(0);">
                                                                    <i class="ki-duotone ki-pencil fs-2">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i> {{ __('Rename') }}</a>
                                                            </li>
                                                            <li><a class="dropdown-item remove-task" href="#">
                                                                    <i class="ki-duotone ki-trash fs-2">
                                                                        <span class="path1"></span>
                                                                        <span class="path2"></span>
                                                                    </i> {{ __('Remove Task') }}
                                                                </a>
                                                            </li>
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
                                                <button type="button" class="btn btn-primary add-task-btn"
                                                    style="font-size: 1.5rem; padding: 8px 16px; border-radius: 50%;">
                                                    +
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="submit" class="floating-button" data-bs-toggle="tooltip" data-bs-placement="left"
                            title="Update Workflow">
                            <i class="fas fa-save"></i>
                        </button>
                    </div>
                </form>
            </div>
        @endsection
        @section('js')
            @include('work-flows::edit-script')
        @endsection
