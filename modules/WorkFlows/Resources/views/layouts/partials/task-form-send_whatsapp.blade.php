{{-- Inline form for Send WhatsApp task (same fields as send-whatsapp-modal, workflow names) --}}
@php $idx = $index ?? 0; $cfg = $taskConfig ?? []; @endphp
<div class="form-group mb-4">
    <label>Send to</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="waPhoneVariableSelector{{ $idx }}">
                <option value="">-- Select Variable to Insert --</option>
                @foreach ($mappedDataArray ?? [] as $item)
                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="waPhone{{ $idx }}"><i class="fas fa-plus-circle me-1"></i> Insert Variable</button>
        </div>
    </div>
    <div class="phone-input-container">
        <input type="text" class="form-control" name="tasks[{{ $idx }}][task_config][wa_phone]" id="waPhone{{ $idx }}" placeholder="Example: @{{ country_code }}@{{ phone_number }}" value="{{ $cfg['wa_phone'] ?? '' }}">
        <div class="phone-preview" id="waPhonePreview{{ $idx }}">Phone number preview will appear here</div>
    </div>
</div>
<div class="form-group mb-4">
    <label>Campaign</label>
    <select class="form-control" name="tasks[{{ $idx }}][task_config][campaign_id]" required>
        <option value="">-- Select Campaign --</option>
        @foreach ($whatsappCampaigns ?? [] as $campaign)
            <option value="{{ $campaign->id }}" {{ ($cfg['campaign_id'] ?? '') == $campaign->id ? 'selected' : '' }}>{{ $campaign->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-group">
    <div class="alert alert-info">
        When a contact enters this stage, the selected API campaign will be triggered.
        <a href="{{ route('wpbox.api.index', ['type' => 'api']) }}" class="btn btn-sm btn-primary" target="_blank">Create API Campaign</a>
    </div>
</div>
<div class="form-group mt-4">
    <label>Data to Pass (Payload)</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="payloadWAVariableSelector{{ $idx }}">
                <option value="">-- Select Variable to Insert --</option>
                @foreach ($mappedDataArray ?? [] as $item)
                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="payloadWA{{ $idx }}"><i class="fas fa-plus-circle me-1"></i> Insert Variable</button>
        </div>
    </div>
    <textarea name="tasks[{{ $idx }}][task_config][wa_payload]" id="payloadWA{{ $idx }}" class="form-control" placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}' rows="4">{{ $cfg['wa_payload'] ?? '' }}</textarea>
</div>
<div class="mt-5 mb-4">
    <label class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="tasks[{{ $idx }}][task_config][autoretarget_enabled]" id="autoretarget_enabled_{{ $idx }}" value="1" {{ !empty($cfg['autoretarget_enabled']) ? 'checked' : '' }}>
        <span class="form-check-label">{{ __('Enable AutoRetarget') }}</span>
    </label>
</div>
<div id="autoretarget_section_{{ $idx }}" style="display: {{ !empty($cfg['autoretarget_enabled']) ? 'block' : 'none' }};">
    <div class="mb-5">
        <label class="form-label">{{ __('AutoRetarget Campaign') }}</label>
        <select class="form-select" id="autoretarget_campaign_id_{{ $idx }}" name="tasks[{{ $idx }}][task_config][autoretarget_campaign_id]">
            <option value="">{{ __('Select an AutoRetarget Campaign') }}</option>
            @foreach ($autoretargetCampaigns ?? [] as $ac)
                <option value="{{ $ac->id }}" {{ ($cfg['autoretarget_campaign_id'] ?? '') == $ac->id ? 'selected' : '' }}>{{ $ac->name }}</option>
            @endforeach
        </select>
    </div>
</div>
