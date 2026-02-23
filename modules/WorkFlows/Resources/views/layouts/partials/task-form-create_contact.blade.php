{{-- Inline form for Create Contact task (same fields as create-contact-modal, workflow names) --}}
@php $idx = $index ?? 0; $cfg = $taskConfig ?? []; @endphp
<div class="form-group mb-4">
    <label>Phone</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="phoneVariableSelector{{ $idx }}">
                <option value="">-- Select Variable to Insert --</option>
                @foreach ($mappedDataArray ?? [] as $item)
                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="phone{{ $idx }}"><i class="fas fa-plus-circle me-1"></i> Insert Variable</button>
        </div>
    </div>
    <div class="phone-input-container">
        <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][phone]" id="phone{{ $idx }}" placeholder="Example: @{{ country_code }}@{{ phone_number }}" value="{{ $cfg['phone'] ?? '' }}">
        <div class="phone-preview" id="phonePreview{{ $idx }}">Phone number preview will appear here</div>
    </div>
</div>
<div class="form-group mb-4">
    <label>Name</label>
    <div class="row">
        <div class="col-md-6">
            <select class="form-control variable-selector" name="tasks[{{ $idx }}][task_config][name_variable]">
                <option value="">-- Select Variable --</option>
                @foreach ($mappedDataArray ?? [] as $item)
                    <option value="{{ $item['key'] }}" {{ ($cfg['name_variable'] ?? '') == $item['key'] ? 'selected' : '' }}>{{ $item['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">OR</span>
                <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][name_static]" placeholder="Static name" value="{{ $cfg['name_static'] ?? '' }}">
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-4">
            <label>Add to Groups</label>
            <select class="form-control groups-selector" name="tasks[{{ $idx }}][task_config][add_groups][]" multiple="multiple">
                @foreach ($groups ?? [] as $group)
                    <option value="{{ $group->id }}" {{ in_array($group->id, $cfg['add_groups'] ?? []) ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-4">
            <label>Remove from Groups</label>
            <select class="form-control groups-selector" name="tasks[{{ $idx }}][task_config][remove_groups][]" multiple="multiple">
                @foreach ($groups ?? [] as $group)
                    <option value="{{ $group->id }}" {{ in_array($group->id, $cfg['remove_groups'] ?? []) ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="form-group mb-4">
    <label>Tags</label>
    <input type="text" class="form-control tags-input" name="tasks[{{ $idx }}][task_config][tags]" placeholder="Enter tags (comma separated)" value="{{ $cfg['tags'] ?? '' }}">
</div>
<div class="form-group mb-4">
    <div class="form-check">
        <input class="form-check-input create-lead-checkbox" type="checkbox" id="createLead{{ $idx }}" name="tasks[{{ $idx }}][task_config][create_lead]" value="1" {{ !empty($cfg['create_lead']) ? 'checked' : '' }}>
        <label class="form-check-label" for="createLead{{ $idx }}">Create Lead</label>
    </div>
</div>
<div class="form-group mb-4">
    <label>Assign Lead To</label>
    <select class="form-control lead-agent-selector" name="tasks[{{ $idx }}][task_config][assign_to_user]">
        <option value="">-- Select Agent/User --</option>
        @foreach ($agents ?? [] as $agent)
            <option value="{{ $agent->id }}" {{ ($cfg['assign_to_user'] ?? '') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group mb-4">
    <div class="form-check">
        <input class="form-check-input add-custom-fields-checkbox" type="checkbox" name="tasks[{{ $idx }}][task_config][add_custom_fields]" value="1" {{ !empty($cfg['add_custom_fields']) ? 'checked' : '' }}>
        <label class="form-check-label">Add Custom Fields</label>
    </div>
</div>
<div class="custom-fields-container" style="display: none;">
    <div class="custom-field-group mb-3">
        <div class="custom-field-item row mb-2">
            <div class="col-md-4">
                <select class="form-control custom-field-selector" name="tasks[{{ $idx }}][task_config][custom_fields][][field_id]">
                    <option value="">-- Select Field --</option>
                    @foreach ($contactFields ?? [] as $fid => $fname)
                        <option value="{{ $fid }}">{{ $fname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control variable-selector" name="tasks[{{ $idx }}][task_config][custom_fields][][value_variable]">
                            <option value="">-- Select Variable --</option>
                            @foreach ($mappedDataArray ?? [] as $item)
                                <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">OR</span>
                            <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][custom_fields][][value_static]" placeholder="Static value">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-danger remove-custom-field">X</button>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-secondary add-custom-field mb-4">+ Add Custom Field</button>
</div>
