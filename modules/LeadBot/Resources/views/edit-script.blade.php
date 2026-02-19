{{-- Copied/adapted from WorkFlows builder UI --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        function initFormElements() {
            $('select').select2({ width: '100%' });

            $('.tags-input').each(function() {
                new Tagify(this, {
                    delimiters: ",| ",
                    pattern: /^[a-zA-Z0-9\s\-_]+$/,
                    dropdown: { enabled: 0 }
                });
            });

            $('.groups-selector').select2({
                width: '100%',
                placeholder: "Select groups"
            });
        }

        setTimeout(initFormElements, 100);

        const webhookVariables = @json($mappedDataArray ?? []);
        const whatsappCampaigns = @json($whatsappCampaigns ?? []);
        const groups = @json($groups ?? []);
        const contactFields = @json($contactFields ?? []);
        const agents = @json($agents ?? []);

        $('[data-bs-toggle="tooltip"]').tooltip();

        // Global index for tasks
        let taskIndex = {{ $leadBot->tasks->count() }};

        function generateVariableOptions() {
            let options = '<option value="">-- Select Variable --</option>';
            if (webhookVariables.length > 0) {
                webhookVariables.forEach(item => {
                    options += `<option value="${item.key}">${item.label}</option>`;
                });
            }
            return options;
        }

       
        function renderPreview(value) {
            if (!value) return '';
            return value.replace(/@{{\s*([^}]+)\s*}}/g, function(_, v) {
                const name = (v || '').trim();
                return `<span class="variable-tag">${name}</span>`;
            });
        }

        function updatePhonePreview(index) {
            const input = $(`#phoneInput${index}`);
            const preview = $(`#phonePreview${index}`);
            if (input.length && preview.length) {
                const val = input.val() || '';
                preview.html(renderPreview(val));
            }
        }

        function updateUrlPreview(index) {
            const input = $(`#urlInput${index}`);
            const preview = $(`#urlPreview${index}`);
            if (input.length && preview.length) {
                const val = input.val() || '';
                preview.html(renderPreview(val));
            }
        }

        // Insert variable into an input by id
        function insertVariableIntoInput(inputId, variable) {
            const formattedVar = '@{{ ' + variable + ' }}';
            const $input = $('#' + inputId);
            const current = $input.val() || '';
            $input.val(current + formattedVar);
        }

        // Capture webhook response (LeadBot endpoint)
        $('#captureWebhookResponse, #recaptureWebhookResponse').on('click', function(event) {
            event.preventDefault();
            const btn = $(this);
            btn.prop('disabled', true).text('Processing...');

            let url = "{{ route('lead-bot.webhooks.latest', $leadBot->id) }}";
            const app = ($('#app_id').val() || '').toLowerCase();
            if (['meta','facebook','facebook_leads','fb_leads','fbleads'].includes(app)) {
                url += (url.includes('?') ? '&' : '?') + 'source=crm';
                const leadId = ($('#crmLeadSelect').val() || '').toString();
                if (leadId) url += '&lead_id=' + encodeURIComponent(leadId);
            }

            $.ajax({
                url,
                type: 'GET',
                success: function(response) {
                    btn.prop('disabled', false).text(btn.attr('id') === 'recaptureWebhookResponse' ? 'Re-Capture Webhook Response' : 'Capture Webhook Response');

                    if (Array.isArray(response)) {
                        $('#captureWebhookResponse').hide();
                        $('#recaptureWebhookResponse').show();
                        $('#toggleResponseView').show();

                        let formHtml = `
                            <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px;">
                                <div class="row">`;
                        response.forEach(item => {
                            formHtml += `
                                <div class="col-md-6 mb-2">
                                    <input type="text" class="form-control font-weight-bold" value="${item.label}" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="text" class="form-control font-weight-bold" value="${item.value}" readonly>
                                </div>`;
                        });
                        formHtml += `</div></div>`;

                        $('#webhookResponse').html(formHtml);
                        $('#responseContainer').slideDown();
                        $('#toggleIcon').html('&gt;');

                        Swal.fire({ icon: 'success', title: 'Success!', text: 'Response captured', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: 'No Response', text: response.message || 'No response available' });
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).text('Capture Webhook Response');
                    Swal.fire({ icon: 'error', title: 'Error!', text: xhr.responseJSON?.message || 'Failed to capture response' });
                }
            });
        });

        // Toggle response view
        $('#toggleResponseView').click(function(event) {
            event.preventDefault();
            let responseContainer = $('#responseContainer');
            if (responseContainer.is(':visible')) {
                responseContainer.slideUp();
                $('#toggleIcon').html('&lt;');
            } else {
                responseContainer.slideDown();
                $('#toggleIcon').html('&gt;');
            }
        });

        // Copy webhook url
        $('#copyWebhookUrl').click(function() {
            let webhookUrl = $('#webhookUrl');
            webhookUrl.select();
            document.execCommand("copy");
            $(this).text('✅ Copied').delay(1500).queue(function(next) {
                $(this).text('📋 Copy');
                next();
            });
        });

        // Sortable tasks
        $("#tasks-container").sortable({
            handle: ".drag-handle",
            update: function() {
                $(".task-item").each(function(i) {
                    $(this).find(".task-order").val(i);
                });
            }
        });

        // Add task button inside card footer (+)
        $(document).on('click', '.add-task-btn', function(e) {
            e.preventDefault();
            const variableOptions = generateVariableOptions();
            const idx = taskIndex;

            const newTaskHtml = `
                <div class="task-item col-md-12 mb-10" data-index="${idx}">
                    <div class="card task-card lb-task-card">
                        <div class="card-header lb-task-hd d-flex align-items-center justify-content-between gap-4 py-4">
                            <span class="lb-drag drag-handle me-2" title="Drag"><i class="ni ni-bullet-list-67"></i></span>
                            <div class="flex-grow-1">
                                <label class="task-name-label fw-bold d-block mt-4 mb-2">Task Name</label>
                                <select name="tasks[${idx}][task_type]" class="form-control task-type w-50 mb-2" required>
                                    <option value="">--Select Task--</option>
                                    <option value="create_contact">Create Contact</option>
                                    <option value="call_api">Call API</option>
                                    <option value="send_whatsapp">Send WhatsApp</option>
                                </select>
                                <input type="text" name="tasks[${idx}][task_name]" class="form-control task-name mt-2 w-50 mb-2"
                                    placeholder="Enter Task Name" style="display: none;">
                            </div>
                            <div class="d-flex align-items-center text-nowrap">
                                <button class="lb-icon-btn toggle-task-body me-3" type="button"><span class="toggle-icon">˄</span></button>
                                <div class="dropdown">
                                    <button class="lb-icon-btn three-dots-btn" type="button" data-bs-toggle="dropdown">⋮</button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item rename-task-btn" href="javascript:void(0);">Rename</a></li>
                                        <li><a class="dropdown-item remove-task" href="#">Remove Task</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body lb-task-bd lb-task-body additional-fields"></div>
                        <input type="hidden" name="tasks[${idx}][order]" class="task-order" value="${idx}">
                        <div class="card-footer text-center">
                            <button type="button" class="lb-btn-primary add-task-btn" style="border-radius:999px; width:48px; height:48px; padding:0; display:inline-flex; align-items:center; justify-content:center; font-size:1.4rem;">+</button>
                        </div>
                    </div>
                </div>`;

            $('#tasks-container').append(newTaskHtml);
            taskIndex++;
            $("#tasks-container").sortable("refresh");
            setTimeout(initFormElements, 100);
        });

        // Toggle task body
        $(document).on('click', '.toggle-task-body', function(e) {
            e.preventDefault();
            const card = $(this).closest('.card');
            const body = card.find('.additional-fields');
            body.slideToggle();
            const icon = $(this).find('.toggle-icon');
            icon.text(icon.text() === '˄' ? '˅' : '˄');
        });

        // Remove task
        $(document).on('click', '.remove-task', function(e) {
            e.preventDefault();
            const container = $('#tasks-container');
            if (container.find('.task-item').length <= 1) {
                Swal.fire({ icon: 'warning', title: 'At least one task is required' });
                return;
            }
            $(this).closest('.task-item').remove();
            container.find('.task-item').each(function(i) {
                $(this).find('.task-order').val(i);
            });
        });

        // Rename task: show input
        $(document).on('click', '.rename-task-btn', function(e) {
            e.preventDefault();
            const item = $(this).closest('.task-item');
            item.find('.task-name').toggle().focus();
        });

        // Delete LeadBot confirm
        $('#deleteLeadBotBtn').on('click', function() {
            Swal.fire({
                title: "Delete LeadBot?",
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: "Yes, delete",
                cancelButtonText: "Cancel",
                reverseButtons: true,
                customClass: { confirmButton: 'btn btn-danger', cancelButton: 'btn btn-light' },
                buttonsStyling: false
            }).then((r) => {
                if (r.isConfirmed) document.getElementById('deleteLeadBotForm')?.submit();
            });
        });

        // Trigger events (same pattern as WorkFlows)
        const triggersByApp = {
            meta: ['new_lead'],
            webhook: ['incoming_webhook'],
            indiamart: ['new_enquiry'],
            '99acres': ['new_lead'],
            housing: ['new_lead'],
            justdial: ['new_lead'],
            tradeindia: ['new_lead'],
            sulekha: ['new_lead'],
        };

        function loadTriggerEvents(appId) {
            const triggers = triggersByApp[appId] || [];
            const $select = $('#trigger_event');
            $select.empty();
            $select.append(`<option value="">Select Trigger</option>`);
            triggers.forEach(t => {
                const selected = ("{{ $leadBot->trigger_event }}".toString() === t) ? 'selected' : '';
                $select.append(`<option value="${t}" ${selected}>${t.replace(/_/g,' ')}</option>`);
            });
        }

        if ("{{ $leadBot->app_id }}") {
            loadTriggerEvents("{{ $leadBot->app_id }}");
        }

        $('#app_id').change(function() {
            const appId = $(this).val();
            const appName = $(this).find("option:selected").text();
            loadTriggerEvents(appId);
            $('#workflownameLabel').text(appName).addClass('fw-bold text-dark');
        });

        $('#trigger_event').change(function() {
            const appName = $('#app_id option:selected').text();
            const triggerName = $(this).find("option:selected").text();
            if (appName && triggerName) {
                $('#workflownameLabel').text(`${appName}: ${triggerName}`);
            }
        });

        $('#editWorkflowName').click(function() {
            const labelText = $('#workflownameLabel').text();
            $('#workflowname').val(labelText).removeClass('d-none').focus();
            $('#workflownameLabel').addClass('d-none');
        });

        $('#workflowname').blur(function() {
            const newName = ($(this).val() || '').trim();
            if (newName !== '') {
                $('#workflownameLabel').text(newName);
            }
            $(this).addClass('d-none');
            $('#workflownameLabel').removeClass('d-none');
        });

        function groupsOptions(selectedIds) {
            selectedIds = selectedIds || [];
            return (groups || []).map(g => {
                const sel = selectedIds.includes(g.id) ? 'selected' : '';
                return `<option value="${g.id}" ${sel}>${g.name}</option>`;
            }).join('');
        }

        function agentsOptions(selectedId) {
            return (agents || []).map(a => {
                const sel = (String(selectedId || '') === String(a.id)) ? 'selected' : '';
                return `<option value="${a.id}" ${sel}>${a.name}</option>`;
            }).join('');
        }

        function campaignsOptions(selectedId) {
            return (whatsappCampaigns || []).map(c => {
                const sel = (String(selectedId || '') === String(c.id)) ? 'selected' : '';
                return `<option value="${c.id}" ${sel}>${c.name}</option>`;
            }).join('');
        }

        function contactFieldsOptions(selectedId) {
            const entries = Object.entries(contactFields || {});
            return entries.map(([id, name]) => {
                const sel = (String(selectedId || '') === String(id)) ? 'selected' : '';
                return `<option value="${id}" ${sel}>${name}</option>`;
            }).join('');
        }

        function buildCreateContactFields(index) {
            return `
                <div class="form-group mb-4">
                    <label>Phone</label>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <select class="form-control variable-selector" id="phoneVariableSelector${index}">
                                <option value="">-- Select Variable to Insert --</option>
                                ${generateVariableOptions().replace('<option value="">-- Select Variable --</option>', '')}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="phone${index}">
                                <i class="fas fa-plus-circle me-1"></i> Insert Variable
                            </button>
                        </div>
                    </div>
                    <div class="phone-input-container">
                        <input type="text" class="form-control" name="tasks[${index}][task_config][phone]" id="phone${index}"
                            placeholder='Example: @{{ country_code }}@{{ phone_number }}'>
                        <div class="phone-preview" id="phonePreview${index}">Phone number preview will appear here</div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label>Name</label>
                    <div class="row">
                        <div class="col-md-6">
                            <select class="form-control variable-selector" name="tasks[${index}][task_config][name_variable]">
                                ${generateVariableOptions()}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">OR</span>
                                <input type="text" class="form-control" name="tasks[${index}][task_config][name_static]" placeholder="Static name">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label>Add to Groups</label>
                            <select class="form-control groups-selector" name="tasks[${index}][task_config][add_groups][]" multiple="multiple">
                                ${groupsOptions([])}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label>Remove from Groups</label>
                            <select class="form-control groups-selector" name="tasks[${index}][task_config][remove_groups][]" multiple="multiple">
                                ${groupsOptions([])}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tags</label>
                    <input type="text" class="form-control tags-input" name="tasks[${index}][task_config][tags]" placeholder="Enter tags (comma separated)">
                </div>

                <div class="form-group mb-4 mt-4">
                    <div class="form-check">
                        <input class="form-check-input create-lead-checkbox" type="checkbox" id="createLead${index}" name="tasks[${index}][task_config][create_lead]" value="1">
                        <label class="form-check-label" for="createLead${index}">Create Lead</label>
                    </div>
                </div>

                <div class="form-group mb-4 lead-assignment-container" id="leadAssignmentContainer${index}">
                    <label>Assign Lead To</label>
                    <select class="form-control lead-agent-selector" name="tasks[${index}][task_config][assign_to_user]">
                        <option value="">-- Select Agent/User --</option>
                        ${agentsOptions('')}
                    </select>
                </div>

                <div class="form-group mb-4 mt-4">
                    <div class="form-check">
                        <input class="form-check-input add-custom-fields-checkbox" type="checkbox" id="addCustomFields${index}" name="tasks[${index}][task_config][add_custom_fields]" value="1">
                        <label class="form-check-label" for="addCustomFields${index}">Add Custom Fields</label>
                    </div>
                </div>

                <div class="custom-fields-container" style="display:none;">
                    <div class="custom-field-group mb-3">
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Field Name</strong></div>
                            <div class="col-md-7"><strong>Field Value</strong></div>
                            <div class="col-md-1"></div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary add-custom-field mt-2">Add Custom Field</button>
                </div>
            `;
        }

        function buildSendWhatsappFields(index) {
            return `
                <div class="form-group mb-4">
                    <label>WhatsApp Phone</label>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <select class="form-control variable-selector" id="waPhoneVariableSelector${index}">
                                <option value="">-- Select Variable to Insert --</option>
                                ${generateVariableOptions().replace('<option value="">-- Select Variable --</option>', '')}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="waPhone${index}">
                                <i class="fas fa-plus-circle me-1"></i> Insert Variable
                            </button>
                        </div>
                    </div>
                    <div class="phone-input-container">
                        <input type="text" class="form-control" name="tasks[${index}][task_config][wa_phone]" id="waPhone${index}"
                            placeholder='Example: @{{ country_code }}@{{ phone_number }}'>
                        <div class="phone-preview" id="waPhonePreview${index}">Phone number preview will appear here</div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label>Campaign</label>
                    <select class="form-control" name="tasks[${index}][task_config][campaign_id]">
                        <option value="">-- Select Campaign --</option>
                        ${campaignsOptions('')}
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label>Payload (JSON)</label>
                    <textarea name="tasks[${index}][task_config][wa_payload]" id="payloadWA${index}" class="form-control"
                        placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}' rows="4"></textarea>
                </div>
            `;
        }

        function buildCallApiFields(index) {
            return `
                <div class="form-group mb-4 url-input-container">
                    <label>URL</label>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <select class="form-control variable-selector" id="urlVariableSelector${index}">
                                <option value="">-- Select Variable to Insert --</option>
                                ${generateVariableOptions().replace('<option value="">-- Select Variable --</option>', '')}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="url${index}">
                                <i class="fas fa-plus-circle me-1"></i> Insert Variable
                            </button>
                        </div>
                    </div>
                    <input type="text" class="form-control" name="tasks[${index}][task_config][url]" id="url${index}"
                        placeholder='Example: https://api.example.com/users/@{{user_id}}'>
                    <div class="url-preview" id="urlPreview${index}"></div>
                </div>

                <div class="form-group mb-4">
                    <label>HTTP Method</label>
                    <select class="form-control" name="tasks[${index}][task_config][http_method]">
                        <option value="GET">GET</option>
                        <option value="POST" selected>POST</option>
                        <option value="PUT">PUT</option>
                        <option value="PATCH">PATCH</option>
                        <option value="DELETE">DELETE</option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label>Auth Type</label>
                    <select class="form-control" name="tasks[${index}][task_config][auth_type]">
                        <option value="none" selected>None</option>
                        <option value="basic">Basic</option>
                        <option value="bearer">Bearer</option>
                    </select>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="tasks[${index}][task_config][add_headers]" value="1">
                    <label class="form-check-label">Add Headers</label>
                </div>
                <div class="header-group">
                    <button type="button" class="btn btn-secondary add-header mt-2">Add Header</button>
                </div>

                <div class="form-check mt-4 mb-3">
                    <input class="form-check-input" type="checkbox" name="tasks[${index}][task_config][add_params]" value="1">
                    <label class="form-check-label">Add Params</label>
                </div>
                <div class="param-group">
                    <button type="button" class="btn btn-secondary add-param mt-2">Add Param</button>
                </div>

                <div class="form-group mt-4">
                    <label>Payload (JSON)</label>
                    <textarea name="tasks[${index}][task_config][data]" id="payload${index}" class="form-control"
                        placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}' rows="4"></textarea>
                </div>
            `;
        }

        // On task type change, build fields for new tasks (and allow re-build if empty)
        $(document).on('change', '.task-type', function() {
            const $task = $(this).closest('.task-item');
            const index = $task.data('index');
            const type = $(this).val();
            const $fields = $task.find('.additional-fields');
            if (!$fields.length) return;

            // Only auto-build if empty (avoid wiping existing saved configs)
            if ($fields.children().length > 0) {
                return;
            }

            if (type === 'create_contact') {
                $fields.html(buildCreateContactFields(index));
            } else if (type === 'send_whatsapp') {
                $fields.html(buildSendWhatsappFields(index));
            } else if (type === 'call_api') {
                $fields.html(buildCallApiFields(index));
            } else {
                $fields.empty();
            }

            setTimeout(initFormElements, 50);
        });

        // Add header row
        $(document).on('click', '.add-header', function() {
            const container = $(this).closest('.header-group');
            const taskItem = $(this).closest('.task-item');
            const index = taskItem.data('index');
            const variableOptions = generateVariableOptions();

            const newRow = `
                <div class="header-item row mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="tasks[${index}][task_config][headers_key][]" placeholder="Header key">
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control variable-selector" name="tasks[${index}][task_config][headers_value_variable][]">
                                    ${variableOptions}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">OR</span>
                                    <input type="text" class="form-control" name="tasks[${index}][task_config][headers_value_static][]" placeholder="Static value">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-header">X</button>
                    </div>
                </div>`;

            container.prepend(newRow);
            setTimeout(initFormElements, 50);
        });

        $(document).on('click', '.remove-header', function() {
            $(this).closest('.header-item').remove();
        });

        // Add param row
        $(document).on('click', '.add-param', function() {
            const container = $(this).closest('.param-group');
            const taskItem = $(this).closest('.task-item');
            const index = taskItem.data('index');
            const variableOptions = generateVariableOptions();

            const newRow = `
                <div class="param-item row mb-2">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="tasks[${index}][task_config][params_key][]" placeholder="Parameter key">
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control variable-selector" name="tasks[${index}][task_config][params_value_variable][]">
                                    ${variableOptions}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">OR</span>
                                    <input type="text" class="form-control" name="tasks[${index}][task_config][params_value_static][]" placeholder="Static value">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-param">X</button>
                    </div>
                </div>`;

            container.prepend(newRow);
            setTimeout(initFormElements, 50);
        });

        $(document).on('click', '.remove-param', function() {
            $(this).closest('.param-item').remove();
        });

        // Show/hide custom fields section
        $(document).on('change', '.add-custom-fields-checkbox', function() {
            $(this).closest('.task-item').find('.custom-fields-container').toggle(this.checked);
        });

        // Add new custom field row
        $(document).on('click', '.add-custom-field', function() {
            const container = $(this).closest('.custom-fields-container').find('.custom-field-group');
            const taskItem = $(this).closest('.task-item');
            const index = taskItem.data('index');
            const variableOptions = generateVariableOptions();

            const newRow = `
                <div class="custom-field-item row mb-2">
                    <div class="col-md-4">
                        <select class="form-control custom-field-selector" name="tasks[${index}][task_config][custom_fields][][field_id]">
                            <option value="">-- Select Field --</option>
                            ${contactFieldsOptions('')}
                        </select>
                    </div>
                    <div class="col-md-7">
                        <div class="row">
                            <div class="col-md-6">
                                <select class="form-control variable-selector" name="tasks[${index}][task_config][custom_fields][][value_variable]">
                                    ${variableOptions}
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">OR</span>
                                    <input type="text" class="form-control" name="tasks[${index}][task_config][custom_fields][][value_static]" placeholder="Static value">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger remove-custom-field">X</button>
                    </div>
                </div>`;

            container.append(newRow);
            setTimeout(initFormElements, 50);
        });

        $(document).on('click', '.remove-custom-field', function() {
            $(this).closest('.custom-field-item').remove();
        });

        // Insert variable buttons for phone/url
        $(document).on('click', '.insert-variable-btn', function(e) {
            e.preventDefault();
            const targetId = $(this).data('target');
            if (targetId) {
                insertVariable(targetId);
            }
        });

        // Live previews for both existing + dynamic fields
        $(document).on('input', 'input[id^="phone"], input[id^="waPhone"]', function() {
            updatePhonePreview(this.id);
        });
        $(document).on('input', 'input[id^="url"]', function() {
            updateUrlPreview(this.id);
        });

        // Initialize previews for existing fields
        setTimeout(function() {
            $('input[id^="phone"], input[id^="waPhone"]').each(function() { updatePhonePreview(this.id); });
            $('input[id^="url"]').each(function() { updateUrlPreview(this.id); });
        }, 150);
    });
</script>
<script>
    function insertVariable(targetId) {
        // Phone inputs
        if (targetId.startsWith('phone') || targetId.startsWith('waPhone')) {
            const index = targetId.replace('phone', '').replace('waPhone', '');
            const selectorId = targetId.startsWith('waPhone') ? `waPhoneVariableSelector${index}` : `phoneVariableSelector${index}`;
            const selector = document.getElementById(selectorId);
            if (!selector) return;
            const variable = selector.value;
            if (!variable) return;

            const input = document.getElementById(targetId);
            if (!input) return;

            const start = input.selectionStart ?? input.value.length;
            const end = input.selectionEnd ?? input.value.length;
            const text = input.value || '';
            const formattedVar = `@{{${variable}}}`;

            input.value = text.substring(0, start) + formattedVar + text.substring(end);
            const newPos = start + formattedVar.length;
            input.selectionStart = newPos;
            input.selectionEnd = newPos;
            input.focus();

            updatePhonePreview(targetId);
            selector.value = "";
        }
        // URL inputs
        else if (targetId.startsWith('url')) {
            const index = targetId.replace('url', '');
            const selectorId = `urlVariableSelector${index}`;
            const selector = document.getElementById(selectorId);
            if (!selector) return;
            const variable = selector.value;
            if (!variable) return;

            const input = document.getElementById(targetId);
            if (!input) return;

            const start = input.selectionStart ?? input.value.length;
            const end = input.selectionEnd ?? input.value.length;
            const text = input.value || '';
            const formattedVar = `@{{${variable}}}`;

            input.value = text.substring(0, start) + formattedVar + text.substring(end);
            const newPos = start + formattedVar.length;
            input.selectionStart = newPos;
            input.selectionEnd = newPos;
            input.focus();

            updateUrlPreview(targetId);
            selector.value = "";
        }
    }

    function updatePhonePreview(targetId) {
        const input = document.getElementById(targetId);
        if (!input) return;
        const index = targetId.replace('phone', '').replace('waPhone', '');
        const previewId = targetId.startsWith('waPhone') ? `waPhonePreview${index}` : `phonePreview${index}`;
        const preview = document.getElementById(previewId);
        if (!preview) return;
        const value = input.value || '';
        preview.innerHTML = (value || '').replace(/@{{\s*([^}]+)\s*}}/g, function(_, v) {
            const name = (v || '').trim();
            return `<span class="variable-tag">${name}</span>`;
        });
    }

    function updateUrlPreview(targetId) {
        const input = document.getElementById(targetId);
        if (!input) return;
        const index = targetId.replace('url', '');
        const preview = document.getElementById(`urlPreview${index}`);
        if (!preview) return;
        const value = input.value || '';
        preview.innerHTML = (value || '').replace(/@{{\s*([^}]+)\s*}}/g, function(_, v) {
            const name = (v || '').trim();
            return `<span class="variable-tag">${name}</span>`;
        });
    }
</script>

