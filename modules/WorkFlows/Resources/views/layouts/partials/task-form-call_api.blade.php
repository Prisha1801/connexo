{{-- Inline form for Call API task (same fields as call-api-modal, workflow names) --}}
@php $idx = $index ?? 0; $cfg = $taskConfig ?? []; @endphp
<div class="form-group mb-4">
    <label>API URL</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="urlVariableSelector{{ $idx }}">
                <option value="">-- Select Variable to Insert --</option>
                @foreach ($mappedDataArray ?? [] as $item)
                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="url{{ $idx }}"><i class="fas fa-plus-circle me-1"></i> Insert Variable</button>
        </div>
    </div>
    <div class="url-input-container">
        <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][url]" id="url{{ $idx }}" placeholder="Example: https://api.example.com/users/@{{ user_id }}" value="{{ $cfg['url'] ?? '' }}">
        <div class="url-preview" id="urlPreview{{ $idx }}">URL preview will appear here</div>
    </div>
</div>
<div class="form-group mb-4">
    <label>HTTP Method</label>
    <select class="form-control" name="tasks[{{ $idx }}][task_config][http_method]">
        <option value="GET" {{ ($cfg['http_method'] ?? 'POST') == 'GET' ? 'selected' : '' }}>GET</option>
        <option value="POST" {{ ($cfg['http_method'] ?? 'POST') == 'POST' ? 'selected' : '' }}>POST</option>
        <option value="PUT" {{ ($cfg['http_method'] ?? '') == 'PUT' ? 'selected' : '' }}>PUT</option>
        <option value="PATCH" {{ ($cfg['http_method'] ?? '') == 'PATCH' ? 'selected' : '' }}>PATCH</option>
        <option value="DELETE" {{ ($cfg['http_method'] ?? '') == 'DELETE' ? 'selected' : '' }}>DELETE</option>
    </select>
</div>
<div class="form-group mb-4">
    <label>Authentication</label>
    <select class="form-control api-auth-type" name="tasks[{{ $idx }}][task_config][auth_type]">
        <option value="none" {{ ($cfg['auth_type'] ?? 'none') == 'none' ? 'selected' : '' }}>No Authentication</option>
        <option value="basic" {{ ($cfg['auth_type'] ?? '') == 'basic' ? 'selected' : '' }}>Basic Authentication</option>
        <option value="bearer" {{ ($cfg['auth_type'] ?? '') == 'bearer' ? 'selected' : '' }}>Bearer Token</option>
    </select>
</div>
<div class="api-basic-auth" style="display: {{ ($cfg['auth_type'] ?? '') == 'basic' ? 'block' : 'none' }};">
    <div class="form-group mb-4">
        <label>Username</label>
        <div class="row">
            <div class="col-md-6">
                <select class="form-control variable-selector" name="tasks[{{ $idx }}][task_config][basic_auth_username_variable]">
                    <option value="">-- Select Variable --</option>
                    @foreach ($mappedDataArray ?? [] as $item)
                        <option value="{{ $item['key'] }}" {{ ($cfg['basic_auth_username_variable'] ?? '') == $item['key'] ? 'selected' : '' }}>{{ $item['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">OR</span>
                    <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][basic_auth_username_static]" placeholder="Static username" value="{{ $cfg['basic_auth_username_static'] ?? '' }}">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group mb-4">
        <label>Password</label>
        <div class="row">
            <div class="col-md-6">
                <select class="form-control variable-selector" name="tasks[{{ $idx }}][task_config][basic_auth_password_variable]">
                    <option value="">-- Select Variable --</option>
                    @foreach ($mappedDataArray ?? [] as $item)
                        <option value="{{ $item['key'] }}" {{ ($cfg['basic_auth_password_variable'] ?? '') == $item['key'] ? 'selected' : '' }}>{{ $item['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">OR</span>
                    <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][basic_auth_password_static]" placeholder="Static password" value="{{ $cfg['basic_auth_password_static'] ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="api-bearer-auth" style="display: {{ ($cfg['auth_type'] ?? '') == 'bearer' ? 'block' : 'none' }};">
    <div class="form-group mb-4">
        <label>Token</label>
        <div class="row">
            <div class="col-md-6">
                <select class="form-control variable-selector" name="tasks[{{ $idx }}][task_config][bearer_token_variable]">
                    <option value="">-- Select Variable --</option>
                    @foreach ($mappedDataArray ?? [] as $item)
                        <option value="{{ $item['key'] }}" {{ ($cfg['bearer_token_variable'] ?? '') == $item['key'] ? 'selected' : '' }}>{{ $item['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">OR</span>
                    <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][bearer_token_static]" placeholder="Static token" value="{{ $cfg['bearer_token_static'] ?? '' }}">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="form-group mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="addHeadersCheckbox{{ $idx }}" name="tasks[{{ $idx }}][task_config][add_headers]" value="1" {{ !empty($cfg['add_headers']) ? 'checked' : '' }}>
        <label class="form-check-label" for="addHeadersCheckbox{{ $idx }}">Add Headers</label>
    </div>
</div>
<div class="headers-container" style="display: {{ !empty($cfg['add_headers']) ? 'block' : 'none' }};">
    <div class="header-group mb-3">
        <div class="header-item row mb-2">
            <div class="col-md-4"><input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][headers_key][]" placeholder="Header key"></div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control variable-selector" name="tasks[{{ $idx }}][task_config][headers_value_variable][]">
                            <option value="">-- Select Variable --</option>
                            @foreach ($mappedDataArray ?? [] as $item)
                                <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">OR</span>
                            <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][headers_value_static][]" placeholder="Static value">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"><button type="button" class="btn btn-danger remove-header">X</button></div>
        </div>
    </div>
    <button type="button" class="btn btn-secondary add-header mb-4">+ Add Header</button>
</div>
<div class="form-group mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="addParamsCheckbox{{ $idx }}" name="tasks[{{ $idx }}][task_config][add_params]" value="1" {{ !empty($cfg['add_params']) ? 'checked' : '' }}>
        <label class="form-check-label" for="addParamsCheckbox{{ $idx }}">Set Parameters</label>
    </div>
</div>
<div class="params-container" style="display: {{ !empty($cfg['add_params']) ? 'block' : 'none' }};">
    <div class="param-group mb-3">
        <div class="param-item row mb-2">
            <div class="col-md-4"><input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][params_key][]" placeholder="Parameter key"></div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control variable-selector" name="tasks[{{ $idx }}][task_config][params_value_variable][]">
                            <option value="">-- Select Variable --</option>
                            @foreach ($mappedDataArray ?? [] as $item)
                                <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">OR</span>
                            <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][params_value_static][]" placeholder="Static value">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-1"><button type="button" class="btn btn-danger remove-param">X</button></div>
        </div>
    </div>
    <button type="button" class="btn btn-secondary add-param mb-4">+ Add Parameter</button>
</div>
<div class="form-group">
    <label>Data to Pass (Payload)</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="payloadVariableSelector{{ $idx }}">
                <option value="">-- Select Variable to Insert --</option>
                @foreach ($mappedDataArray ?? [] as $item)
                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="payload{{ $idx }}"><i class="fas fa-plus-circle me-1"></i> Insert Variable</button>
        </div>
    </div>
    <textarea name="tasks[{{ $idx }}][task_config][data]" id="payload{{ $idx }}" class="form-control" placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}' rows="4">{{ $cfg['data'] ?? '' }}</textarea>
</div>
