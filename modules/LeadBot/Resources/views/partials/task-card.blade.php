@php
    $idx = $index;
    $taskId = $task['id'] ?? null;
    $taskType = $task['task_type'] ?? '';
    $taskName = $task['task_name'] ?? '';
    $taskOrder = $task['task_order'] ?? ($task['order'] ?? 0);
    $cfg = $task['task_config'] ?? [];
    if (is_string($cfg)) {
        $cfg = json_decode($cfg, true) ?: [];
    }
    if (!is_array($cfg)) {
        $cfg = [];
    }
@endphp

<div class="leadbot-task mb-6" data-index="{{ $idx }}">
    <div class="lb-task-card">
        <div class="lb-task-hd">
            <div class="lb-task-meta">
                <span class="lb-drag" title="{{ __('Drag to reorder') }}"><i class="ni ni-bullet-list-67"></i></span>
                <div class="text-truncate">
                    <div class="lb-task-title text-truncate">
                        {{ $taskName ?: __('Task') }}
                        @if ($taskType)
                            <span class="lb-pill ml-2">{{ $taskType }}</span>
                        @endif
                    </div>
                    <div class="lb-task-sub">{{ __('Configure what happens after webhook') }}</div>
                </div>
            </div>

            <div class="d-flex align-items-center" style="gap:.5rem;">
                <button type="button" class="lb-collapse-btn toggle-task-btn" title="{{ __('Collapse/expand') }}">
                    <i class="ni ni-bold-down"></i>
                </button>
                <button type="button" class="lb-btn-danger remove-task-btn">
                    <i class="ni ni-fat-remove"></i> {{ __('Remove') }}
                </button>
            </div>
        </div>

        <div class="lb-task-bd lb-task-body">
            @if ($taskId)
                <input type="hidden" name="tasks[{{ $idx }}][id]" value="{{ $taskId }}">
            @endif
            <input type="hidden" class="task-order-input" name="tasks[{{ $idx }}][task_order]" value="{{ (int) $taskOrder }}">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">{{ __('Task Type') }}</label>
                        <select name="tasks[{{ $idx }}][task_type]" class="form-control task-type-select" required
                            style="border-radius: 12px; height: 44px;">
                        <option value="">{{ __('Select task') }}</option>
                        <option value="create_contact" @selected($taskType === 'create_contact')>{{ __('Create Contact') }}</option>
                        <option value="send_whatsapp" @selected($taskType === 'send_whatsapp')>{{ __('Send WhatsApp') }}</option>
                        <option value="call_api" @selected($taskType === 'call_api')>{{ __('Call API') }}</option>
                    </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">{{ __('Task Name (optional)') }}</label>
                        <input type="text" name="tasks[{{ $idx }}][task_name]" class="form-control"
                            value="{{ $taskName }}" placeholder="{{ __('Example: Create/Update contact') }}"
                            style="border-radius: 12px; height: 44px;">
                    </div>
                </div>
            </div>

            {{-- CREATE CONTACT --}}
            <div class="mt-4" data-task-section="create_contact" style="display:none">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('Phone') }}</label>
                            <div class="row">
                            <div class="col-md-6">
                                <select class="form-control lb-var-select"
                                    name="tasks[{{ $idx }}][task_config][phone_variable]">
                                    <option value="">{{ __('Variable') }}</option>
                                    @foreach ($mappedDataArray ?? [] as $v)
                                        <option value="{{ $v['key'] }}" @selected(($cfg['phone_variable'] ?? '') === $v['key'])>
                                            {{ $v['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    name="tasks[{{ $idx }}][task_config][phone_static]"
                                    value="{{ $cfg['phone_static'] ?? '' }}" placeholder="{{ __('Static phone') }}"
                                    style="border-radius: 12px; height: 44px;">
                            </div>
                        </div>
                        <div class="text-muted small mt-2">{{ __('Either choose a variable OR enter a static phone.') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('Name') }}</label>
                            <div class="row">
                            <div class="col-md-6">
                                <select class="form-control lb-var-select"
                                    name="tasks[{{ $idx }}][task_config][name_variable]">
                                    <option value="">{{ __('Variable') }}</option>
                                    @foreach ($mappedDataArray ?? [] as $v)
                                        <option value="{{ $v['key'] }}" @selected(($cfg['name_variable'] ?? '') === $v['key'])>
                                            {{ $v['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    name="tasks[{{ $idx }}][task_config][name_static]"
                                    value="{{ $cfg['name_static'] ?? '' }}" placeholder="{{ __('Static name') }}"
                                    style="border-radius: 12px; height: 44px;">
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('Add to groups') }}</label>
                            <select class="form-control" multiple name="tasks[{{ $idx }}][task_config][add_groups][]"
                                style="border-radius: 12px;">
                            @foreach ($groups as $g)
                                <option value="{{ $g->id }}" @selected(in_array($g->id, $cfg['add_groups'] ?? [], true))>
                                    {{ $g->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('Remove from groups') }}</label>
                            <select class="form-control" multiple name="tasks[{{ $idx }}][task_config][remove_groups][]"
                                style="border-radius: 12px;">
                            @foreach ($groups as $g)
                                <option value="{{ $g->id }}" @selected(in_array($g->id, $cfg['remove_groups'] ?? [], true))>
                                    {{ $g->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="mb-0" style="font-weight: 800;">{{ __('Custom fields (optional)') }}</h4>
                        <button type="button" class="lb-btn-soft add-custom-field-row">
                            <i class="ni ni-fat-add"></i> {{ __('Add Field') }}
                        </button>
                    </div>
                    <div class="custom-fields-rows">
                        @foreach (($cfg['custom_fields'] ?? []) as $row)
                            @php
                                $row = is_array($row) ? $row : [];
                            @endphp
                            <div class="row g-3 align-items-end custom-field-row mb-3">
                                <div class="col-md-4">
                                    <label class="form-control-label font-weight-bold">{{ __('Field') }}</label>
                                    <select class="form-control"
                                        name="tasks[{{ $idx }}][task_config][custom_fields][][field_id]">
                                        <option value="">{{ __('Select field') }}</option>
                                        @foreach ($contactFields as $fid => $fname)
                                            <option value="{{ $fid }}" @selected((int) ($row['field_id'] ?? 0) === (int) $fid)>{{ $fname }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-control-label font-weight-bold">{{ __('Variable') }}</label>
                                    <select class="form-control lb-var-select"
                                        name="tasks[{{ $idx }}][task_config][custom_fields][][value_variable]">
                                        <option value="">{{ __('Select variable') }}</option>
                                        @foreach ($mappedDataArray ?? [] as $v)
                                            <option value="{{ $v['key'] }}" @selected(($row['value_variable'] ?? '') === $v['key'])>
                                                {{ $v['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-control-label font-weight-bold">{{ __('Static') }}</label>
                                    <input type="text" class="form-control"
                                        name="tasks[{{ $idx }}][task_config][custom_fields][][value_static]"
                                        value="{{ $row['value_static'] ?? '' }}" placeholder="{{ __('Optional') }}"
                                        style="border-radius: 12px; height: 44px;">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="lb-icon-btn danger w-100 remove-row" style="border-radius: 12px;">×</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- SEND WHATSAPP --}}
            <div class="mt-4" data-task-section="send_whatsapp" style="display:none">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('API Campaign') }}</label>
                            <select class="form-control" name="tasks[{{ $idx }}][task_config][campaign_id]" style="border-radius: 12px; height: 44px;">
                            <option value="">{{ __('Select campaign') }}</option>
                            @foreach ($whatsappCampaigns as $c)
                                <option value="{{ $c->id }}" @selected((int) ($cfg['campaign_id'] ?? 0) === (int) $c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="text-muted small mt-2">{{ __('Uses your existing WhatsApp API campaigns (Wpbox).') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('WhatsApp Phone') }}</label>
                            <div class="row">
                            <div class="col-md-6">
                                <select class="form-control lb-var-select"
                                    name="tasks[{{ $idx }}][task_config][wa_phone_variable]">
                                    <option value="">{{ __('Variable') }}</option>
                                    @foreach ($mappedDataArray ?? [] as $v)
                                        <option value="{{ $v['key'] }}" @selected(($cfg['wa_phone_variable'] ?? '') === $v['key'])>
                                            {{ $v['label'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control"
                                    name="tasks[{{ $idx }}][task_config][wa_phone_static]"
                                    value="{{ $cfg['wa_phone_static'] ?? '' }}" placeholder="{{ __('Static phone') }}"
                                    style="border-radius: 12px; height: 44px;">
                            </div>
                        </div>
                        <div class="text-muted small mt-2">{{ __('Template variables can use webhook values at runtime.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CALL API --}}
            <div class="mt-4" data-task-section="call_api" style="display:none">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('Method') }}</label>
                            <select class="form-control" name="tasks[{{ $idx }}][task_config][method]" style="border-radius: 12px; height: 44px;">
                            @foreach (['GET','POST','PUT','PATCH','DELETE'] as $m)
                                <option value="{{ $m }}" @selected(($cfg['method'] ?? 'POST') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('URL') }}</label>
                            <input type="text" class="form-control"
                            name="tasks[{{ $idx }}][task_config][url]" value="{{ $cfg['url'] ?? '' }}"
                            placeholder="https://api.example.com/leads" style="border-radius: 12px; height: 44px;">
                            <div class="text-muted small mt-2">
                                {{ __('You can use templates like') }} <code>@{{body.phone}}</code>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="mb-0" style="font-weight:800;">{{ __('Headers (optional)') }}</h4>
                        <button type="button" class="lb-btn-soft add-header-row">
                            <i class="ni ni-fat-add"></i> {{ __('Add Header') }}
                        </button>
                    </div>
                    <div class="headers-rows">
                        @foreach (($cfg['headers'] ?? []) as $row)
                            @php $row = is_array($row) ? $row : []; @endphp
                            <div class="row g-3 align-items-end kv-row mb-3">
                                <div class="col-md-4">
                                    <label class="form-control-label font-weight-bold">{{ __('Key') }}</label>
                                    <input type="text" class="form-control"
                                        name="tasks[{{ $idx }}][task_config][headers][][key]" value="{{ $row['key'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-control-label font-weight-bold">{{ __('Variable') }}</label>
                                    <select class="form-control lb-var-select"
                                        name="tasks[{{ $idx }}][task_config][headers][][value_variable]">
                                        <option value="">{{ __('Select variable') }}</option>
                                        @foreach ($mappedDataArray ?? [] as $v)
                                            <option value="{{ $v['key'] }}" @selected(($row['value_variable'] ?? '') === $v['key'])>
                                                {{ $v['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-control-label font-weight-bold">{{ __('Static') }}</label>
                                    <input type="text" class="form-control"
                                        name="tasks[{{ $idx }}][task_config][headers][][value_static]"
                                        value="{{ $row['value_static'] ?? '' }}">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="lb-icon-btn danger w-100 remove-row" style="border-radius: 12px;">×</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h4 class="mb-0" style="font-weight:800;">{{ __('Query params (optional)') }}</h4>
                        <button type="button" class="lb-btn-soft add-query-row">
                            <i class="ni ni-fat-add"></i> {{ __('Add Param') }}
                        </button>
                    </div>
                    <div class="query-rows">
                        @foreach (($cfg['query'] ?? []) as $row)
                            @php $row = is_array($row) ? $row : []; @endphp
                            <div class="row g-3 align-items-end kv-row mb-3">
                                <div class="col-md-4">
                                    <label class="form-control-label font-weight-bold">{{ __('Key') }}</label>
                                    <input type="text" class="form-control"
                                        name="tasks[{{ $idx }}][task_config][query][][key]" value="{{ $row['key'] ?? '' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-control-label font-weight-bold">{{ __('Variable') }}</label>
                                    <select class="form-control lb-var-select"
                                        name="tasks[{{ $idx }}][task_config][query][][value_variable]">
                                        <option value="">{{ __('Select variable') }}</option>
                                        @foreach ($mappedDataArray ?? [] as $v)
                                            <option value="{{ $v['key'] }}" @selected(($row['value_variable'] ?? '') === $v['key'])>
                                                {{ $v['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-control-label font-weight-bold">{{ __('Static') }}</label>
                                    <input type="text" class="form-control"
                                        name="tasks[{{ $idx }}][task_config][query][][value_static]"
                                        value="{{ $row['value_static'] ?? '' }}">
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="lb-icon-btn danger w-100 remove-row" style="border-radius: 12px;">×</button>
                                </div>
                            </div>
                        @endforeach
                    </div>  
                </div>

                <div class="mt-4">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">{{ __('Body (optional)') }}</label>
                        <textarea class="form-control" rows="6" name="tasks[{{ $idx }}][task_config][body]"
                            placeholder='{"phone":"@{{body.phone}}"}'
                            style="border-radius: 12px;">{{ $cfg['body'] ?? '' }}</textarea>
                        <div class="text-muted small mt-2">{{ __('If this is JSON, it will be sent as JSON automatically.') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

