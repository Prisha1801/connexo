<!-- Call API Modal -->
<div class="modal fade task-config-modal" id="call_apiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configure API Call Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-4">
                    <label>API URL</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-control variable-selector-modal" name="url_variable">
                                <option value="">-- Select Variable --</option>
                                @foreach ($mappedDataArray as $item)
                                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">OR</span>
                                <input type="url" class="form-control" name="url_static" placeholder="Static URL">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ... rest of API call form fields ... -->
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save-task-config">Save Configuration</button>
            </div>
        </div>
    </div>
</div>