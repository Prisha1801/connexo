<!-- Send WhatsApp Modal -->
<div class="modal fade task-config-modal" id="send_whatsappModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configure WhatsApp Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-4">
                    <label>Send WhatsApp to:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-control variable-selector-modal" name="wa_phone_variable">
                                <option value="">-- Select Variable --</option>
                                @foreach ($mappedDataArray as $item)
                                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">OR</span>
                                <input type="text" class="form-control" name="wa_phone_static" placeholder="Static phone number">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label>Campaign</label>
                    <select class="form-control" name="campaign_id" required>
                        <option value="">-- Select Campaign --</option>
                        @foreach ($whatsappCampaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- ... rest of WhatsApp form fields ... -->
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save-task-config">Save Configuration</button>
            </div>
        </div>
    </div>
</div>