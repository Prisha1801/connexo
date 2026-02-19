<!-- Create Contact Modal -->
<div class="modal fade task-config-modal" id="create_contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Configure Create Contact Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mb-4">
                    <label>Phone</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-control variable-selector-modal" name="phone_variable">
                                <option value="">-- Select Variable --</option>
                                @foreach ($mappedDataArray as $item)
                                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">OR</span>
                                <input type="text" class="form-control" name="phone_static" placeholder="Static phone number">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label>Name</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-control variable-selector-modal" name="name_variable">
                                <option value="">-- Select Variable --</option>
                                @foreach ($mappedDataArray as $item)
                                    <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">OR</span>
                                <input type="text" class="form-control" name="name_static" placeholder="Static name">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label>Add to Groups</label>
                            <select class="form-control groups-selector" 
                                    name="add_groups[]" 
                                    multiple="multiple">
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label>Remove from Groups</label>
                            <select class="form-control groups-selector" 
                                    name="remove_groups[]" 
                                    multiple="multiple">
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mb-4">
                    <label>Tags</label>
                    <input type="text" class="form-control tags-input" 
                           name="tags" 
                           placeholder="Enter tags (comma separated)">
                </div>
                
                <!-- Custom Fields Section -->
                <div class="form-group mb-4">
                    <div class="form-check">
                        <input class="form-check-input add-custom-fields-checkbox" 
                               type="checkbox" 
                               name="add_custom_fields" 
                               value="1">
                        <label class="form-check-label">
                            Add Custom Fields
                        </label>
                    </div>
                </div>
                
                <div class="custom-fields-container" style="display: none;">
                    <div class="custom-field-group mb-3">
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Field Name</strong></div>
                            <div class="col-md-7"><strong>Field Value</strong></div>
                            <div class="col-md-1"></div>
                        </div>
                        <div class="custom-field-item row mb-2">
                            <div class="col-md-4">
                                <select class="form-control custom-field-selector" 
                                        name="custom_fields[0][field_id]">
                                    <option value="">-- Select Field --</option>
                                    @foreach ($contactFields as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-7">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select class="form-control variable-selector-modal" 
                                                name="custom_fields[0][value_variable]">
                                            <option value="">-- Select Variable --</option>
                                            @foreach ($mappedDataArray as $item)
                                                <option value="{{ $item['key'] }}">{{ $item['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text">OR</span>
                                            <input type="text" class="form-control" 
                                                   name="custom_fields[0][value_static]" 
                                                   placeholder="Static value">
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary save-task-config">Save Configuration</button>
            </div>
        </div>
    </div>
</div>