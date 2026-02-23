<script>
(function() {
    var d = window.__workflowFormData || {};
    var webhookVariables = d.mappedDataArray || [], groups = d.groups || [], contactFields = d.contactFields || [];
    var whatsappCampaigns = d.whatsappCampaigns || [], autoretargetCampaigns = d.autoretargetCampaigns || [], agents = d.agents || [];
    function genVarOpts() {
        var o = '<option value="">-- Select Variable --</option>';
        webhookVariables.forEach(function(i) { o += '<option value="' + (i.key||'') + '">' + (i.label||'') + '</option>'; });
        return o;
    }
    function genAutoretarget() {
        var o = '<option value="">-- Select an AutoRetarget Campaign --</option>';
        autoretargetCampaigns.forEach(function(c) { o += '<option value="' + c.id + '">' + (c.name||'') + '</option>'; });
        return o;
    }
    window._buildWorkflowTaskFormSync = function(taskType, index) {
        var html = '', idx = index || '0', vopts = genVarOpts();
        if (taskType === 'create_contact') {
            var gopts = groups.map(function(g) { return '<option value="' + g.id + '">' + (g.name||'') + '</option>'; }).join('');
            var cfopts = Object.keys(contactFields).map(function(id) { return '<option value="' + id + '">' + (contactFields[id]||'') + '</option>'; }).join('');
            html = '<div class="form-group mb-4"><label>Phone</label><div class="row mb-2"><div class="col-md-6"><select class="form-control variable-selector" id="phoneVariableSelector' + idx + '">' + vopts + '</select></div><div class="col-md-6"><button type="button" class="insert-btn insert-variable-btn" data-target="phone' + idx + '"><i class="ni ni-fat-add mr-1"></i> Insert Variable</button></div></div><div class="phone-input-container"><input type="text" name="tasks[' + idx + '][task_config][phone]" id="phone' + idx + '" class="form-control" placeholder="Example: @{{country_code}}@{{phone_number}}"><div class="phone-preview" id="phonePreview' + idx + '">Phone number preview will appear here</div></div></div>';
            html += '<div class="form-group mb-4"><label>Name</label><div class="row"><div class="col-md-6"><select class="form-control variable-selector" name="tasks[' + idx + '][task_config][name_variable]">' + vopts + '</select></div><div class="col-md-6"><div class="input-group"><span class="input-group-text">OR</span><input type="text" class="form-control" name="tasks[' + idx + '][task_config][name_static]" placeholder="Static name"></div></div></div></div>';
            html += '<div class="row"><div class="col-md-6"><div class="form-group mb-4"><label>Add to Groups</label><select class="form-control groups-selector" name="tasks[' + idx + '][task_config][add_groups][]" multiple="multiple">' + gopts + '</select></div></div><div class="col-md-6"><div class="form-group mb-4"><label>Remove from Groups</label><select class="form-control groups-selector" name="tasks[' + idx + '][task_config][remove_groups][]" multiple="multiple">' + gopts + '</select></div></div></div>';
            html += '<div class="form-group mb-4"><label>Tags</label><input type="text" class="form-control tags-input" name="tasks[' + idx + '][task_config][tags]" placeholder="Enter tags (comma separated)"></div>';
            html += '<div class="form-group mb-4"><div class="form-check"><input class="form-check-input create-lead-checkbox" type="checkbox" id="createLead' + idx + '" name="tasks[' + idx + '][task_config][create_lead]" value="1"><label class="form-check-label" for="createLead' + idx + '">Create Lead</label></div></div>';
            html += '<div class="form-group mb-4"><label>Assign Lead To</label><select class="form-control lead-agent-selector" name="tasks[' + idx + '][task_config][assign_to_user]"><option value="">-- Select Agent/User --</option>';
            agents.forEach(function(a) { html += '<option value="' + a.id + '">' + (a.name||'') + '</option>'; });
            html += '</select></div>';
        } else if (taskType === 'send_whatsapp') {
            var campopts = '<option value="">-- Select Campaign --</option>';
            whatsappCampaigns.forEach(function(c) { campopts += '<option value="' + c.id + '">' + (c.name||'') + '</option>'; });
            html = '<div class="form-group mb-4"><label>Send WhatsApp on:</label><div class="row mb-2"><div class="col-md-6"><select class="form-control variable-selector" id="waPhoneVariableSelector' + idx + '">' + vopts + '</select></div><div class="col-md-6"><button type="button" class="insert-btn insert-variable-btn" data-target="waPhone' + idx + '"><i class="ni ni-fat-add mr-1"></i> Insert Variable</button></div></div><div class="phone-input-container"><input type="text" name="tasks[' + idx + '][task_config][wa_phone]" id="waPhone' + idx + '" class="form-control" placeholder="Example: @{{country_code}}@{{phone_number}}"><div class="phone-preview" id="waPhonePreview' + idx + '">Phone number preview will appear here</div></div></div>';
            html += '<div class="form-group mb-4"><label>Campaign</label><select class="form-control" name="tasks[' + idx + '][task_config][campaign_id]" required>' + campopts + '</select></div>';
            html += '<div class="form-group mt-4"><label>Data to Pass (Payload)</label><div class="row mb-2"><div class="col-md-6"><select class="form-control variable-selector" id="payloadWAVariableSelector' + idx + '">' + vopts.replace('-- Select Variable --','-- Select Variable to Insert --') + '</select></div><div class="col-md-6"><button type="button" class="insert-btn insert-variable-btn" data-target="payloadWA' + idx + '"><i class="ni ni-fat-add mr-1"></i> Insert Variable</button></div></div><textarea name="tasks[' + idx + '][task_config][wa_payload]" id="payloadWA' + idx + '" class="form-control" rows="4"></textarea></div>';
            html += '<div class="mt-5 mb-4"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="tasks[' + idx + '][task_config][autoretarget_enabled]" id="autoretarget_enabled_' + idx + '" value="1"><span class="form-check-label">Enable AutoRetarget</span></label></div><div id="autoretarget_section_' + idx + '" style="display:none;"><div class="mb-5"><label>AutoRetarget Campaign</label><select class="form-select" id="autoretarget_campaign_id_' + idx + '" name="tasks[' + idx + '][task_config][autoretarget_campaign_id]">' + genAutoretarget() + '</select></div></div>';
        } else if (taskType === 'call_api') {
            html = '<div class="form-group mb-4"><label>API URL</label><div class="row mb-2"><div class="col-md-6"><select class="form-control variable-selector" id="urlVariableSelector' + idx + '">' + vopts.replace('-- Select Variable --','-- Select Variable to Insert --') + '</select></div><div class="col-md-6"><button type="button" class="insert-btn insert-variable-btn" data-target="url' + idx + '"><i class="ni ni-fat-add mr-1"></i> Insert Variable</button></div></div><div class="url-input-container"><input type="text" class="form-control" name="tasks[' + idx + '][task_config][url]" id="url' + idx + '" placeholder="https://api.example.com"><div class="url-preview" id="urlPreview' + idx + '">URL preview</div></div></div>';
            html += '<div class="form-group mb-4"><label>HTTP Method</label><select class="form-control" name="tasks[' + idx + '][task_config][http_method]"><option value="GET">GET</option><option value="POST" selected>POST</option><option value="PUT">PUT</option><option value="PATCH">PATCH</option><option value="DELETE">DELETE</option></select></div>';
            html += '<div class="form-group mb-4"><label>Authentication</label><select class="form-control api-auth-type" name="tasks[' + idx + '][task_config][auth_type]"><option value="none">No Authentication</option><option value="basic">Basic</option><option value="bearer">Bearer Token</option></select></div>';
            html += '<div class="form-group mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="addHeadersCheckbox' + idx + '" name="tasks[' + idx + '][task_config][add_headers]" value="1"><label class="form-check-label">Add Headers</label></div></div>';
            html += '<div class="form-group mb-4"><div class="form-check"><input class="form-check-input" type="checkbox" id="addParamsCheckbox' + idx + '" name="tasks[' + idx + '][task_config][add_params]" value="1"><label class="form-check-label">Set Parameters</label></div></div>';
            html += '<div class="form-group"><label>Data to Pass (Payload)</label><textarea name="tasks[' + idx + '][task_config][data]" id="payload' + idx + '" class="form-control" rows="4"></textarea></div>';
        }
        return html;
    };
})();
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize all form elements after a small delay
        function initFormElements() {
            // Initialize Select2 - exclude task-type so native change fires and form loads
            $('select').not('.task-type').select2({
                width: '100%',
            });

            // Initialize Tagify for tags input
            $('.tags-input').each(function() {
                new Tagify(this, {
                    delimiters: ",| ",
                    pattern: /^[a-zA-Z0-9\s\-_]+$/,
                    dropdown: {
                        enabled: 0
                    }
                });
            });

            // Initialize groups selectors
            $('.groups-selector').select2({
                width: '100%',
                placeholder: "Select groups"
            });
        }

        // Toggle autoretarget section visibility
        $(document).on('change', '[id^="autoretarget_enabled_"]', function() {
            const index = $(this).closest('.task-item').data('index');
            if ($(this).is(':checked')) {
                $(`#autoretarget_section_${index}`).show();
            } else {
                $(`#autoretarget_section_${index}`).hide();
            }
        });

        // Call initialization after a small delay
        setTimeout(initFormElements, 100);

        // Get mapped data from PHP
        const webhookVariables = @json($mappedDataArray ?? []);
        const whatsappCampaigns = @json($whatsappCampaigns);
        const groups = @json($groups ?? []);
        const contactFields = @json($contactFields ?? []);
        const autoretargetCampaigns = @json($autoretargetCampaigns ?? []);
        const agents = @json($agents);

        $('[data-bs-toggle="tooltip"]').tooltip();
        let hasWebhookUrl = {{ $workflow->app_id == 'webhook' ? 'true' : 'false' }};
        // Global index for tasks
        let taskIndex = {{ $workflow->tasks->count() }};
        window._workflowTaskIndex = taskIndex;

        // Function to generate variable options
        function generateVariableOptions() {
            let options = '<option value="">-- Select Variable --</option>';
            if (webhookVariables.length > 0) {
                webhookVariables.forEach(item => {
                    options += `<option value="${item.key}">${item.label}</option>`;
                });
            }
            return options;
        }

        // Add new header row
        $(document).on('click', '.add-header', function() {
            const container = $(this).prev('.header-group');
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

            container.append(newRow);
            $('select').not('.task-type').select2({ width: '100%' });
        });

        // Show/hide custom fields section
        $(document).on('change', '.add-custom-fields-checkbox', function() {
            $(this).closest('.form-group').next('.custom-fields-container').toggle(this.checked);
        });

        // Add new custom field
        $(document).on('click', '.add-custom-field', function() {
            const container = $(this).prev('.custom-field-group');
            const taskItem = $(this).closest('.task-item');
            const index = taskItem.data('index');
            const variableOptions = generateVariableOptions();

            // Generate contact field options
            const contactFieldOptions = Object.entries(contactFields).map(([id, name]) =>
                `<option value="${id}">${name}</option>`
            ).join('');

            const newRow = `
<div class="custom-field-item row mb-2">
    <div class="col-md-4">
        <select class="form-control custom-field-selector" 
                name="tasks[${index}][task_config][custom_fields][][field_id]">
            <option value="">-- Select Field --</option>
            ${contactFieldOptions}
        </select>
    </div>
    <div class="col-md-7">
        <div class="row">
            <div class="col-md-6">
                <select class="form-control variable-selector" 
                        name="tasks[${index}][task_config][custom_fields][][value_variable]">
                    ${variableOptions}
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">OR</span>
                    <input type="text" class="form-control" 
                           name="tasks[${index}][task_config][custom_fields][][value_static]" 
                           placeholder="Static value">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-danger remove-custom-field">X</button>
    </div>
</div>`;

            container.append(newRow);
            $('select').not('.task-type').select2({ width: '100%' });
        });

        // Remove custom field
        $(document).on('click', '.remove-custom-field', function() {
            $(this).closest('.custom-field-item').remove();
        });

        // Add parameter row
        $(document).on('click', '.add-param', function() {
            const container = $(this).prev('.param-group');
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

            container.append(newRow);
            $('select').not('.task-type').select2({ width: '100%' });
        });

        // Remove header row with protection for last item
        $(document).on('click', '.remove-header', function() {
            const headerContainer = $(this).closest('.header-group');
            const headerItems = headerContainer.find('.header-item');

            if (headerItems.length > 1) {
                $(this).closest('.header-item').remove();
            }
        });

        // Remove parameter row with protection for last item
        $(document).on('click', '.remove-param', function() {
            const paramContainer = $(this).closest('.param-group');
            const paramItems = paramContainer.find('.param-item');

            if (paramItems.length > 1) {
                $(this).closest('.param-item').remove();
            }
        });

        $('#responseContainer').hide();
        $('#toggleIcon').html('&lt;');

        function focusTask(taskItem) {
            // Collapse all other tasks
            $(".task-item").not(taskItem).each(function() {
                $(this).find(".card-body.additional-fields").slideUp();
                $(this).find(".toggle-icon").text("˅");
            });
            // Expand the current task
            taskItem.find(".card-body.additional-fields").slideDown();
            taskItem.find(".toggle-icon").text("˄");
        }

        $(".app-icon").click(function() {
            let appId = $(this).data("app-id");

            // Highlight the selected app
            $(".app-icon").removeClass("border border-primary");
            $(this).addClass("border border-primary");

            // Fetch trigger events dynamically
            let triggers = getTriggerEvents(appId);
            $("#trigger_event").html('<option value="">Select Trigger</option>').prop("disabled",
                false);

            triggers.forEach(trigger => {
                $("#trigger_event").append(`<option value="${trigger}">${trigger}</option>`);
            });

            // If Webhook, generate a sample webhook URL
            if (appId === "webhook") {
                let webhookUrl = "https://yourdomain.com/webhook/catch";
                $("#webhook_url").val(webhookUrl);
            } else {
                $("#webhook_url").val("");
            }
        });

        // Function to generate autoretarget campaign options
        function generateAutoretargetCampaignOptions() {
            let options = '<option value="">-- Select an AutoRetarget Campaign --</option>';
            if (autoretargetCampaigns.length > 0) {
                autoretargetCampaigns.forEach(campaign => {
                    options += `<option value="${campaign.id}">${campaign.name}</option>`;
                });
            }
            return options;
        }

        function getTriggerEvents(appId) {
            let events = {
                webhook: ["Catch Webhook", "Catch Webhook with Headers", "Catch Webhook with File Data"],
                indiamart: ["New Leads"],
                "99acres": ["New Leads"],
                housing: ["New Leads"],
                justdial: ["New Leads"],
                tradeindia: ["New Leads"],
                sulekha: ["New Leads"]
            };

            return events[appId] || [];
        }

        function updateTaskOrder() {
            $('#tasks-container .task-item').each(function(index) {
                $(this).attr('data-index', index);
                $(this).find('.task-order').val(index);
                $(this).find('select.task-type').attr('name', 'tasks[' + index + '][task_type]');
                $(this).find('.additional-fields').find('input, textarea, select').each(function() {
                    let name = $(this).attr('name');
                    if (name) {
                        let newName = name.replace(/tasks\[\d+\]/, 'tasks[' + index + ']');
                        $(this).attr('name', newName);
                    }
                });
                $(this).find('.task-order').attr('name', 'tasks[' + index + '][order]');
            });
        }

        $("#tasks-container").sortable({
            items: '.task-item',
            handle: '.drag-handle',
            update: function(event, ui) {
                updateTaskOrder();
            }
        });

        const taskNames = {
            "create_contact": "Task: Create Contact",
            "send_email": "Task: Send Email",
            "send_sms": "Task: Send SMS",
            "call_api": "Task: Call API",
            "send_whatsapp": "Task: Send WhatsApp"
        };

        // Expose for vanilla task-type handler (survives jQuery reload)
        window._workflowTaskNames = taskNames;
        window._workflowFocusTask = function(el) { if (window.jQuery && el) focusTask($(el)); };
        window._workflowInitFormElements = function() { initFormElements(); };

        $(document).on("click", ".rename-task-btn", function() {
            let taskItem = $(this).closest(".task-item");
            let taskLabel = taskItem.find(".task-name-label");
            let taskInput = taskItem.find(".task-name");

            taskInput.val(taskLabel.text().trim());
            taskLabel.hide();
            taskInput.show().focus();
            focusTask(taskItem);
        });

        $(document).on("blur", ".task-name", function() {
            let taskInput = $(this);
            let taskItem = taskInput.closest(".task-item");
            let taskLabel = taskItem.find(".task-name-label");

            if (taskInput.val().trim() !== "") {
                taskLabel.text(taskInput.val().trim());
            }

            taskInput.hide();
            taskLabel.show();
        });

        {{-- Add-task handler moved to vanilla JS at end of file (jQuery may be reloaded by layout) --}}

        $(document).on("click", ".remove-task-btn", function() {
            let currentTask = $(this).closest(".task-item");

            if ($(".task-item").length > 1) {
                currentTask.remove();
            } else {
                alert("You must have at least one task.");
            }
        });

        {{-- Toggle handler moved to vanilla JS at end of file for reliability (jQuery may be reloaded by layout) --}}

        $(".task-item:first .card-body.additional-fields").show();
        $(".task-item:first .toggle-icon").text("˄");

        {{-- Remove-task handler moved to vanilla JS at end of file (jQuery may be reloaded by layout) --}}

        // Build form HTML for given task type - extracted for reuse by vanilla handler
        function buildTaskFormHtml(taskType, index) {
            let html = '';
            if (taskType === 'create_contact') {
                // Generate group options
                const groupOptions = groups.map(group =>
                    `<option value="${group.id}">${group.name}</option>`
                ).join('');

                // Generate contact field options
                const contactFieldOptions = Object.entries(contactFields).map(([id, name]) =>
                    `<option value="${id}">${name}</option>`
                ).join('');

                html = `
 <div class="form-group mb-4">
    <label>Phone</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="phoneVariableSelector${index}">
                ${generateVariableOptions()}
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="insert-btn insert-variable-btn" data-target="phone${index}">
                <i class="ni ni-fat-add mr-1"></i> Insert Variable
            </button>
        </div>
    </div>
    <div class="phone-input-container">
        <input type="text" name="tasks[${index}][task_config][phone]" id="phone${index}" class="form-control" 
               placeholder='Example: @{{country_code}}@{{phone_number}}'>
        <div class="phone-preview" id="phonePreview${index}">
            Phone number preview will appear here
        </div>
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
            <select class="form-control groups-selector" 
                    name="tasks[${index}][task_config][add_groups][]" 
                    multiple="multiple">
                ${groupOptions}
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group mb-4">
            <label>Remove from Groups</label>
            <select class="form-control groups-selector" 
                    name="tasks[${index}][task_config][remove_groups][]" 
                    multiple="multiple">
                ${groupOptions}
            </select>
        </div>
    </div>
</div>
<div class="form-group mb-4">
    <label>Tags</label>
    <input type="text" class="form-control tags-input" 
           name="tasks[${index}][task_config][tags]" 
           placeholder="Enter tags (comma separated)">
</div>

<div class="form-group mb-4">
    <div class="form-check">
        <input class="form-check-input create-lead-checkbox" 
               type="checkbox" 
               id="createLead${index}" 
               name="tasks[${index}][task_config][create_lead]" 
               value="1">
        <label class="form-check-label" for="createLead${index}">
            Create Lead
        </label>
    </div>
</div>

<div class="form-group mb-4">
    <label>Assign Lead To</label>
    <select class="form-control lead-agent-selector" 
            name="tasks[${index}][task_config][assign_to_user]">
        <option value="">-- Select Agent/User --</option>
        ${agents.map(agent => `<option value="${agent.id}">${agent.name}</option>`).join('')}
    </select>
</div>


<!-- Custom Fields Section -->
<div class="form-group mb-4">
    <div class="form-check">
        <input class="form-check-input add-custom-fields-checkbox" 
               type="checkbox" 
               id="addCustomFields${index}" 
               name="tasks[${index}][task_config][add_custom_fields]" 
               value="1">
        <label class="form-check-label" for="addCustomFields${index}">
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
                        name="tasks[${index}][task_config][custom_fields][][field_id]">
                    <option value="">-- Select Field --</option>
                    ${contactFieldOptions}
                </select>
            </div>
            <div class="col-md-7">
                <div class="row">
                    <div class="col-md-6">
                        <select class="form-control variable-selector" 
                                name="tasks[${index}][task_config][custom_fields][][value_variable]">
                            ${generateVariableOptions()}
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">OR</span>
                            <input type="text" class="form-control" 
                                   name="tasks[${index}][task_config][custom_fields][][value_static]" 
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
`;
            } else if (taskType === 'send_whatsapp') {
                let options = '<option value="">-- Select Campaign --</option>';
                whatsappCampaigns.forEach(campaign => {
                    options += `<option value="${campaign.id}">${campaign.name}</option>`;
                });

                // Generate variable options for payload section
                const variableOptions = generateVariableOptions();
                // Generate autoretarget campaign options
                const autoretargetOptions = generateAutoretargetCampaignOptions();

                html = `
 <div class="form-group mb-4">
   <label>Send WhatsApp on:</label>
   <div class="row mb-2">
       <div class="col-md-6">
           <select class="form-control variable-selector" id="waPhoneVariableSelector${index}">
               ${generateVariableOptions()}
           </select>
       </div>
       <div class="col-md-6">
           <button type="button" class="insert-btn insert-variable-btn" data-target="waPhone${index}">
               <i class="ni ni-fat-add mr-1"></i> Insert Variable
           </button>
       </div>
   </div>
   <div class="phone-input-container">
       <input type="text" name="tasks[${index}][task_config][wa_phone]" id="waPhone${index}" class="form-control" 
              placeholder='Example: @{{country_code}}@{{phone_number}}'>
       <div class="phone-preview" id="waPhonePreview${index}">
           Phone number preview will appear here
       </div>
   </div>
   </div>
   <div class="form-group mb-4">
   <label>Campaign</label>
   <select class="form-control" name="tasks[${index}][task_config][campaign_id]" required>
       ${options}
   </select>
   </div>
   <div class="form-group">
   <div class="alert alert-info">
       When a contact enters this stage, the selected API campaign will be triggered. Create new API campaigns using the button below.

   <a href="{{ route('wpbox.api.index', ['type' => 'api']) }}" class="btn btn-sm btn-primary" target="_blank">Create API Campaign</a>
   </div>
   </div>
   <!-- Payload Section -->
   <div class="form-group mt-4">
   <label>Data to Pass (Payload)</label>
   <div class="row mb-2">
       <div class="col-md-6">
           <select class="form-control variable-selector" id="payloadWAVariableSelector${index}">
               <option value="">-- Select Variable to Insert --</option>
               ${variableOptions.replace('<option value="">-- Select Variable --</option>', '')}
           </select>
       </div>
       <div class="col-md-6">
           <button type="button" class="insert-btn insert-variable-btn" data-target="payloadWA${index}"><i class="ni ni-fat-add mr-1"></i> Insert Variable</button>
       </div>
   </div>
   <textarea name="tasks[${index}][task_config][wa_payload]" 
             id="payloadWA${index}" 
             class="form-control" 
             placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}'
             rows="4"></textarea>
   </div>
    <!-- Autoretarget Option -->
   <div class="mt-5 mb-4">
       <label class="form-check form-switch form-check-custom form-check-solid">
           <input class="form-check-input" type="checkbox"
               name="tasks[${index}][task_config][autoretarget_enabled]" 
               id="autoretarget_enabled_${index}" value="1">
           <span class="form-check-label fw-semibold text-muted">{{ __('Enable AutoRetarget') }}</span>
       </label>
   </div>

   <div id="autoretarget_section_${index}" style="display: none;">
       <div class="mb-5">
           <label for="autoretarget_campaign_id_${index}"
               class="form-label">{{ __('AutoRetarget Campaign') }}</label>
           <select class="form-select form-select-solid"
               id="autoretarget_campaign_id_${index}" 
               name="tasks[${index}][task_config][autoretarget_campaign_id]">
               ${autoretargetOptions}
           </select>
       </div>
   </div>
   `;
            } else if (taskType === 'send_email') {
                html = `
   <div class="form-group mb-4">
   <label>Email To</label>
   <input type="email" name="tasks[${index}][task_config][to]" class="form-control" placeholder="Enter recipient email" required>
   </div>
   <div class="form-group mb-4">
   <label>Email Subject</label>
   <input type="text" name="tasks[${index}][task_config][subject]" class="form-control" placeholder="Enter email subject" required>
   </div>
   <div class="form-group">
   <label>Email Message</label>
   <textarea name="tasks[${index}][task_config][message]" class="form-control" placeholder="Enter email message" required></textarea>
   </div>
   `;
            } else if (taskType === 'send_sms') {
                html = `
   <div class="form-group mb-4">
   <label>Phone Number</label>
   <input type="text" name="tasks[${index}][task_config][phone]" class="form-control" placeholder="Enter phone number" required>
   </div>
   <div class="form-group mb-4">
   <label>SMS Message</label>
   <textarea name="tasks[${index}][task_config][message]" class="form-control" placeholder="Enter SMS message" required></textarea>
   </div>
   `;
            } else if (taskType === 'call_api') {
    // Generate variable options
    const variableOptions = generateVariableOptions();

    html = `
<div class="form-group mb-4">
    <label>API URL</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="urlVariableSelector${index}">
                <option value="">-- Select Variable to Insert --</option>
                ${variableOptions.replace('<option value="">-- Select Variable --</option>', '')}
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="insert-btn insert-variable-btn" data-target="url${index}">
                <i class="ni ni-fat-add mr-1"></i> Insert Variable
            </button>
        </div>
    </div>
    <div class="url-input-container">
        <input type="text" class="form-control" name="tasks[${index}][task_config][url]" id="url${index}" 
               placeholder='Example: https://api.example.com/users/@{{user_id}}'>
        <div class="url-preview" id="urlPreview${index}">URL preview will appear here</div>
    </div>
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

<!-- Authentication Section -->
<div class="form-group mb-4">
    <label>Authentication</label>
    <select class="form-control api-auth-type" name="tasks[${index}][task_config][auth_type]">
        <option value="none">No Authentication</option>
        <option value="basic">Basic Authentication</option>
        <option value="bearer">Bearer Token</option>
    </select>
</div>

<!-- Basic Auth Fields (hidden by default) -->
<div class="api-basic-auth" style="display: none;">
    <div class="form-group mb-4">
        <label>Username</label>
        <div class="row">
            <div class="col-md-6">
                <select class="form-control variable-selector" name="tasks[${index}][task_config][basic_auth_username_variable]">
                    ${variableOptions}
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">OR</span>
                    <input type="text" class="form-control" name="tasks[${index}][task_config][basic_auth_username_static]" placeholder="Static username">
                </div>
            </div>
        </div>
    </div>
    <div class="form-group mb-4">
        <label>Password</label>
        <div class="row">
            <div class="col-md-6">
                <select class="form-control variable-selector" name="tasks[${index}][task_config][basic_auth_password_variable]">
                    ${variableOptions}
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">OR</span>
                    <input type="text" class="form-control" name="tasks[${index}][task_config][basic_auth_password_static]" placeholder="Static password">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bearer Token Fields (hidden by default) -->
<div class="api-bearer-auth" style="display: none;">
    <div class="form-group mb-4">
        <label>Token</label>
        <div class="row">
            <div class="col-md-6">
                <select class="form-control variable-selector" name="tasks[${index}][task_config][bearer_token_variable]">
                    ${variableOptions}
                </select>
            </div>
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">OR</span>
                    <input type="text" class="form-control" name="tasks[${index}][task_config][bearer_token_static]" placeholder="Static token">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group mb-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="addHeadersCheckbox${index}" name="tasks[${index}][task_config][add_headers]" value="1">
        <label class="form-check-label" for="addHeadersCheckbox${index}">Add Headers</label>
    </div>
</div>

<!-- Headers Section -->
<div class="headers-container" style="display: none;">
    <div class="header-group mb-3">
        <div class="row mb-2">
            <div class="col-md-4"><strong>Header Key</strong></div>
            <div class="col-md-7"><strong>Header Value</strong></div>
            <div class="col-md-1"></div>
        </div>
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
        </div>
    </div>
    <button type="button" class="btn btn-secondary add-header mb-4">+ Add Header</button>
</div>

<!-- Parameters Section -->
<div class="form-group mb-4 mt-4">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="addParamsCheckbox${index}" name="tasks[${index}][task_config][add_params]" value="1">
        <label class="form-check-label" for="addParamsCheckbox${index}">Set Parameters</label>
    </div>
</div>

<!-- Parameters Section -->
<div class="params-container" style="display: none;">
    <div class="param-group mb-3">
        <div class="row mb-2">
            <div class="col-md-4"><strong>Parameter Key</strong></div>
            <div class="col-md-7"><strong>Parameter Value</strong></div>
            <div class="col-md-1"></div>
        </div>
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
        </div>
    </div>
    <button type="button" class="btn btn-secondary add-param mb-4">+ Add Parameter</button>
</div>

<!-- Payload Section -->
<div class="form-group">
    <label>Data to Pass (Payload)</label>
    <div class="row mb-2">
        <div class="col-md-6">
            <select class="form-control variable-selector" id="payloadVariableSelector${index}">
                <option value="">-- Select Variable to Insert --</option>
                ${variableOptions.replace('<option value="">-- Select Variable --</option>', '')}
            </select>
        </div>
        <div class="col-md-6">
            <button type="button" class="insert-btn insert-variable-btn" data-target="payload${index}"><i class="ni ni-fat-add mr-1"></i> Insert Variable</button>
        </div>
    </div>
    <textarea name="tasks[${index}][task_config][data]" 
        id="payload${index}" 
        class="form-control" 
        placeholder='Example: {"name": "@{{ name }}", "phone": "@{{ phone }}"}'
        rows="4"></textarea>
</div>
`;
} else {
                html = '';
            }
            return html;
        }
        window._buildWorkflowTaskForm = buildTaskFormHtml;

        // jQuery task-type handler - primary, uses closure's buildTaskFormHtml
        $(document).on('change select2:select', '.task-type', function() {
            var taskType = $(this).val();
            var taskItem = $(this).closest('.task-item');
            var container = taskItem.find('.additional-fields');
            var index = taskItem.attr('data-index') || '0';
            var taskLabel = taskItem.find('.task-name-label');
            if (taskType && taskNames[taskType]) taskLabel.text(taskNames[taskType]);
            focusTask(taskItem);
            container.html(buildTaskFormHtml(taskType, index));
            setTimeout(initFormElements, 100);
        });

        // Trigger form load for any task that already has a selection (e.g. server-rendered but form missing)
        $('.task-type').each(function() {
            var v = $(this).val();
            if (v && (v === 'create_contact' || v === 'call_api' || v === 'send_whatsapp')) {
                var taskItem = $(this).closest('.task-item');
                var container = taskItem.find('.additional-fields');
                var hasForm = container.find('.form-group, .form-check, input[type="text"], select').length > 1;
                if (!hasForm) {
                    var index = taskItem.attr('data-index') || '0';
                    container.html(buildTaskFormHtml(v, index));
                    setTimeout(initFormElements, 100);
                }
            }
        });

        $(document).on('click', '.insert-variable-btn', function() {
            const targetId = $(this).data('target');
            insertVariable(targetId);
        });

        // On form submission, check that at least one task exists.
        $('form').submit(function(e) {
            if ($('#tasks-container .task-item').length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'You must define at least one task.',
                });
                e.preventDefault();
            }
        });

        function loadTriggerEvents(appId) {
            let triggers = {
                webhook: ["Catch Webhook", "Catch Webhook with Headers", "Catch Webhook with File Data"],
                indiamart: ["New Leads"],
                "99acres": ["New Leads"],
                housing: ["New Leads"],
                justdial: ["New Leads"],
                tradeindia: ["New Leads"],
                sulekha: ["New Leads"]
            };

            $("#trigger_event").html('<option value="">Select Trigger</option>');
            if (triggers[appId]) {
                triggers[appId].forEach(trigger => {
                    let triggerValue = trigger.toLowerCase().replace(/\s+/g, '_');
                    $("#trigger_event").append(
                        `<option value="${triggerValue}" ${"{{ $workflow->trigger_event }}" === triggerValue ? 'selected' : ''}>${trigger}</option>`
                    );
                });
            }
        }

        if ("{{ $workflow->app_id }}") {
            loadTriggerEvents("{{ $workflow->app_id }}");
        }

        $('#app_id').change(function() {
            let appId = $(this).val();
            let appName = $(this).find("option:selected").text();
            loadTriggerEvents(appId);
            $('#workflownameLabel').text(appName).addClass('fw-bold text-dark');
            if (!hasWebhookUrl) {
                $('#webhook_url_container').addClass('d-none');
            }
        });

        $('#trigger_event').change(function() {
            let appName = $('#app_id option:selected').text();
            let triggerName = $(this).find("option:selected").text();
            if (appName && triggerName) {
                $('#workflownameLabel').text(`${appName}: ${triggerName}`);
            }
        });
        
        $('#editWorkflowName').click(function() {
            let labelText = $('#workflownameLabel').text();
            $('#workflowname').val(labelText).removeClass('d-none').focus();
            $('#workflownameLabel').addClass('d-none');
        });

        $('#workflowname').blur(function() {
            let newName = $(this).val().trim();
            if (newName !== '') {
                $('#workflownameLabel').text(newName);
            }
            $(this).addClass('d-none');
            $('#workflownameLabel').removeClass('d-none');
        });

        $('#copyWebhookUrl').click(function() {
            let webhookUrl = $('#webhookUrl');
            webhookUrl.select();
            document.execCommand("copy");
            $(this).text('✅ Copied').delay(1500).queue(function(next) {
                $(this).text('📋 Copy');
                next();
            });
        });

        // Webhook capture/recapture/toggle moved to vanilla JS IIFE ( survives jQuery reload )
    });
</script>
<script>
function insertVariable(targetId) {
    // Check if this is a phone input field
    if (targetId.startsWith('phone') || targetId.startsWith('waPhone')) {
        const index = targetId.replace('phone', '').replace('waPhone', '');
        const selectorId = targetId.startsWith('waPhone') ? 
            `waPhoneVariableSelector${index}` : `phoneVariableSelector${index}`;
        
        const selector = document.getElementById(selectorId);
        if (!selector) return;
        
        const variable = selector.value;
        if (!variable) return;

        const input = document.getElementById(targetId);
        if (!input) return;

        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;

        // Create properly formatted variable
        const formattedVar = `@{{${variable}}}`;

        // Insert at cursor position
        input.value = text.substring(0, start) + formattedVar + text.substring(end);

        // Position cursor after inserted variable
        const newPos = start + formattedVar.length;
        input.selectionStart = newPos;
        input.selectionEnd = newPos;
        input.focus();
        
        // Update preview for phone fields
        updatePhonePreview(targetId);
        
        // Reset selector
        selector.value = "";
    }
    // Check if this is an API URL field
    else if (targetId.startsWith('url')) {
        const index = targetId.replace('url', '');
        const selectorId = `urlVariableSelector${index}`;
        const selector = document.getElementById(selectorId);
        if (!selector) return;
        
        const variable = selector.value;
        if (!variable) return;

        const input = document.getElementById(targetId);
        if (!input) return;

        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;

        // Create properly formatted variable
        const formattedVar = `@{{${variable}}}`;

        // Insert at cursor position
        input.value = text.substring(0, start) + formattedVar + text.substring(end);

        // Position cursor after inserted variable
        const newPos = start + formattedVar.length;
        input.selectionStart = newPos;
        input.selectionEnd = newPos;
        input.focus();
        
        // Update preview for URL fields
        updateUrlPreview(targetId);
        
        // Reset selector
        selector.value = "";
    }
    // Check if this is a WA payload
    else if (targetId.startsWith('payloadWA')) {
        const index = targetId.replace('payloadWA', '');
        const selectorId = `payloadWAVariableSelector${index}`;
        const selector = document.getElementById(selectorId);
        const variable = selector.value;
        if (!variable) return;

        const textarea = document.getElementById(targetId);
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        // Create properly formatted variable
        const formattedVar = '@{{ ' + variable + ' }}';

        // Insert at cursor position
        textarea.value = text.substring(0, start) + formattedVar + text.substring(end);

        // Position cursor after inserted variable
        const newPos = start + formattedVar.length;
        textarea.selectionStart = newPos;
        textarea.selectionEnd = newPos;
        textarea.focus();
    }
    // Handle regular payloads
    else if (targetId.startsWith('payload')) {
        const selectorId = `payloadVariableSelector${targetId.replace('payload', '')}`;
        const selector = document.getElementById(selectorId);
        const variable = selector.value;
        if (!variable) return;

        const textarea = document.getElementById(targetId);
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        // Create properly formatted variable
        const formattedVar = '@{{ ' + variable + ' }}';

        // Insert at cursor position
        textarea.value = text.substring(0, start) + formattedVar + text.substring(end);

        // Position cursor after inserted variable
        const newPos = start + formattedVar.length;
        textarea.selectionStart = newPos;
        textarea.selectionEnd = newPos;
        textarea.focus();
    }
}

// Helper function to update phone preview
function updatePhonePreview(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const previewId = inputId + 'Preview';
    const preview = document.getElementById(previewId);
    if (!preview) return;
    
    let value = input.value;
    
    // Replace variables with sample values for preview
    value = value.replace(/@{{(\w+)}}/g, '<span class="variable-tag">$1</span>');
    
    preview.innerHTML = value || 'Phone number preview will appear here';
}

// Helper function to update URL preview
function updateUrlPreview(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const previewId = inputId + 'Preview';
    const preview = document.getElementById(previewId);
    if (!preview) return;
    
    let value = input.value;
    
    // Replace variables with sample values for preview
    value = value.replace(/@{{(\w+)}}/g, '<span class="variable-tag">$1</span>');
    
    preview.innerHTML = value || 'URL preview will appear here';
}
    // Show/hide auth sections based on auth type
    $(document).on('change', '.api-auth-type', function() {
        const authType = $(this).val();
        const container = $(this).closest('.form-group').parent();

        // Hide all auth sections
        container.find('.api-basic-auth, .api-bearer-auth').hide();

        // Show only the selected auth type
        if (authType === 'basic') {
            container.find('.api-basic-auth').show();
        } else if (authType === 'bearer') {
            container.find('.api-bearer-auth').show();
        }
    });

    // Show/hide headers section
    $(document).on('change', '[id^="addHeadersCheckbox"]', function() {
        $(this).closest('.form-group').next('.headers-container').toggle(this.checked);
    });

    // Show/hide params section
    $(document).on('change', '[id^="addParamsCheckbox"]', function() {
        $(this).closest('.form-group').next('.params-container').toggle(this.checked);
    });
</script>
{{-- Vanilla JS fallback for toggle and add-task buttons - works even if jQuery is reloaded after @stack('js') --}}
<script>
(function() {
    var initialTaskCount = {{ $workflow->tasks->count() }};

    function getNextTaskIndex() {
        var idx = window._workflowTaskIndex;
        if (typeof idx === 'number') return idx;
        var items = document.querySelectorAll('#tasks-container .task-item');
        return items.length;
    }

    function updateTaskOrderVanilla() {
        var container = document.getElementById('tasks-container');
        if (!container) return;
        var items = container.querySelectorAll('.task-item');
        items.forEach(function(item, index) {
            item.setAttribute('data-index', index);
            var orderInput = item.querySelector('.task-order');
            if (orderInput) {
                orderInput.value = index;
                orderInput.setAttribute('name', 'tasks[' + index + '][order]');
            }
            var taskTypeSelect = item.querySelector('select.task-type');
            if (taskTypeSelect) taskTypeSelect.setAttribute('name', 'tasks[' + index + '][task_type]');
            var additionalFields = item.querySelector('.additional-fields');
            if (additionalFields) {
                additionalFields.querySelectorAll('input, textarea, select').forEach(function(el) {
                    var name = el.getAttribute('name');
                    if (name) el.setAttribute('name', name.replace(/tasks\[\d+\]/, 'tasks[' + index + ']'));
                });
            }
        });
        window._workflowTaskIndex = items.length;
    }

    function handleAddTaskClick(e) {
        var btn = e.target.closest('.add-task-btn');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        var parentTask = btn.closest('.task-item');
        if (!parentTask) return;
        var taskIndex = getNextTaskIndex();
        var newTaskHTML = '<div class="task-item col-md-12 mb-3" data-index="' + taskIndex + '">' +
            '<div class="card task-card">' +
            '<div class="card-header d-flex align-items-center justify-content-between gap-4 py-4">' +
            '<span class="drag-handle mr-3" style="cursor:grab; color:#94a3b8;"><i class="ni ni-bullet-list-67"></i></span>' +
            '<div class="flex-grow-1">' +
            '<label class="task-name-label font-weight-bold d-block mt-2 mb-2">Task Name</label>' +
            '<select name="tasks[' + taskIndex + '][task_type]" class="form-control task-type w-50 mb-2" required>' +
            '<option value="">--Select Task--</option>' +
            '<option value="create_contact">Create Contact</option>' +
            '<option value="call_api">Call API</option>' +
            '<option value="send_whatsapp">Send WhatsApp</option>' +
            '</select>' +
            '<input type="text" name="tasks[' + taskIndex + '][task_name]" class="form-control task-name mt-2 w-50 mb-2" placeholder="Enter Task Name" style="display: none;">' +
            '</div>' +
            '<div class="d-flex align-items-center text-nowrap">' +
            '<button type="button" class="lb-icon-btn toggle-task-body mr-2"><span class="toggle-icon">\u02C5</span></button>' +
            '<div class="dropdown">' +
            '<button class="lb-icon-btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">\u22EE</button>' +
            '<ul class="dropdown-menu dropdown-menu-right">' +
            '<li><a class="dropdown-item rename-task-btn" href="javascript:void(0);"><i class="ni ni-ruler-pencil mr-2"></i> Rename</a></li>' +
            '<li><a class="dropdown-item remove-task" href="#"><i class="ni ni-fat-remove mr-2"></i> Remove Task</a></li>' +
            '</ul></div></div></div>' +
            '<div class="card-body additional-fields" style="display:none;"><input type="hidden" name="tasks[' + taskIndex + '][task_config][]" value=""></div>' +
            '<input type="hidden" name="tasks[' + taskIndex + '][order]" class="task-order" value="' + taskIndex + '">' +
            '<div class="card-footer text-center">' +
            '<button type="button" class="add-task-btn">+</button>' +
            '</div></div></div>';
        var div = document.createElement('div');
        div.innerHTML = newTaskHTML.trim();
        var newTask = div.firstChild;
        parentTask.parentNode.insertBefore(newTask, parentTask.nextSibling);
        document.querySelectorAll('.task-item .card-body.additional-fields').forEach(function(el) { el.style.display = 'none'; });
        document.querySelectorAll('.task-item .toggle-icon').forEach(function(el) { el.textContent = '\u02C5'; });
        updateTaskOrderVanilla();
        if (window.jQuery && window.jQuery.fn.sortable) {
            try { window.jQuery('#tasks-container').sortable('refresh'); } catch (err) {}
        }
        if (window.jQuery) {
            try {
                window.jQuery(newTask).find('select').not('.task-type').select2({ width: '100%' });
            } catch (err) {}
        }
    }

    function handleToggleClick(e) {
        var btn = e.target.closest('.toggle-task-body');
        if (!btn) return;
        e.preventDefault();
        e.stopPropagation();
        var taskItem = btn.closest('.task-item');
        if (!taskItem) return;
        var taskBody = taskItem.querySelector('.card-body.additional-fields');
        if (!taskBody) return;
        var icon = btn.querySelector('.toggle-icon');
        var isVisible = taskBody.offsetParent !== null && getComputedStyle(taskBody).display !== 'none';
        if (isVisible) {
            taskBody.style.display = 'none';
            if (icon) icon.textContent = '\u02C5';
        } else {
            document.querySelectorAll('.task-item .card-body.additional-fields').forEach(function(el) { el.style.display = 'none'; });
            document.querySelectorAll('.task-item .toggle-icon').forEach(function(el) { el.textContent = '\u02C5'; });
            taskBody.style.display = 'block';
            if (icon) icon.textContent = '\u02C4';
        }
    }

    function handleTaskTypeChange(e) {
        var select = e.target;
        if (!select || !select.classList || !select.classList.contains('task-type')) return;
        var taskItem = select.closest('.task-item');
        if (!taskItem) return;
        var container = taskItem.querySelector('.additional-fields');
        if (!container) return;
        var taskType = select.value || '';
        var index = taskItem.getAttribute('data-index') || '0';
        var taskLabel = taskItem.querySelector('.task-name-label');
        var taskNames = window._workflowTaskNames;
        if (taskType && taskNames && taskNames[taskType] && taskLabel) {
            taskLabel.textContent = taskNames[taskType];
        }
        document.querySelectorAll('.task-item').forEach(function(item) {
            var body = item.querySelector('.card-body.additional-fields');
            var icon = item.querySelector('.toggle-icon');
            if (item === taskItem) {
                if (body) body.style.display = 'block';
                if (icon) icon.textContent = '\u02C4';
            } else {
                if (body) body.style.display = 'none';
                if (icon) icon.textContent = '\u02C5';
            }
        });
        var buildFn = window._buildWorkflowTaskForm;
        var html = (buildFn && typeof buildFn === 'function') ? buildFn(taskType, index) : '';
        container.innerHTML = html;
        var initFn = window._workflowInitFormElements;
        if (initFn && typeof initFn === 'function') {
            setTimeout(initFn, 100);
        }
    }

    function handleRemoveTaskClick(e) {
        var link = e.target.closest('.remove-task');
        if (!link) return;
        e.preventDefault();
        e.stopPropagation();
        var container = document.getElementById('tasks-container');
        if (!container) return;
        var tasks = container.querySelectorAll('.task-item');
        if (tasks.length <= 1) {
            if (window.Swal) {
                window.Swal.fire({ icon: 'warning', title: 'Cannot Delete', text: 'You must have at least one task defined.' });
            } else {
                alert('You must have at least one task defined.');
            }
            return;
        }
        var taskCard = link.closest('.task-item');
        if (!taskCard) return;
        var hasValue = false;
        taskCard.querySelectorAll('input, textarea, select').forEach(function(el) {
            var v = (el.value || '').trim();
            if (el.type === 'checkbox' && el.checked) v = '1';
            if (v !== '') hasValue = true;
        });
        function doRemove() {
            taskCard.remove();
            updateTaskOrderVanilla();
            if (window.jQuery && window.jQuery.fn.sortable) {
                try { window.jQuery('#tasks-container').sortable('refresh'); } catch (err) {}
            }
        }
        if (hasValue && window.Swal) {
            window.Swal.fire({
                title: 'Are you sure?',
                text: 'This task contains values. Do you want to delete it?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) { if (result.isConfirmed) doRemove(); });
        } else if (hasValue && confirm('This task contains values. Do you want to delete it?')) {
            doRemove();
        } else if (!hasValue) {
            doRemove();
        }
    }

    function init() {
        document.removeEventListener('click', handleToggleClick);
        document.removeEventListener('click', handleAddTaskClick);
        document.removeEventListener('click', handleRemoveTaskClick);
        document.removeEventListener('change', handleTaskTypeChange);
        document.addEventListener('click', handleToggleClick);
        document.addEventListener('click', handleAddTaskClick);
        document.addEventListener('click', handleRemoveTaskClick);
        document.addEventListener('change', handleTaskTypeChange);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
{{-- Vanilla JS webhook capture/recapture/toggle - survives jQuery reload --}}
<script>
(function() {
    function toggleResponseView(e) {
        var el = e.target.closest('#toggleResponseView');
        if (!el) return;
        e.preventDefault();
        var container = document.getElementById('responseContainer');
        var icon = document.getElementById('toggleIcon');
        if (!container || !icon) return;
        var isVisible = container.offsetParent !== null && getComputedStyle(container).display !== 'none';
        if (isVisible) {
            container.style.display = 'none';
            icon.innerHTML = '&lt;';
        } else {
            container.style.display = 'block';
            icon.innerHTML = '&gt;';
        }
    }

    function renderWebhookResponse(data) {
        var wrapper = document.getElementById('webhookResponse');
        if (!wrapper) return;
        var existing = document.getElementById('alreadyExistsWebhookResponse');
        if (existing) existing.style.display = 'none';
        var html = '<div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; border-radius: 5px; padding: 10px;"><div class="row">';
        data.forEach(function(item) {
            var lbl = (item.label || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;');
            var val = (item.value || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/"/g,'&quot;');
            html += '<div class="col-md-6 mb-2"><input type="text" class="form-control font-weight-bold" value="' + lbl + '" readonly></div>';
            html += '<div class="col-md-6 mb-2"><input type="text" class="form-control font-weight-bold" value="' + val + '" readonly></div>';
        });
        html += '</div></div>';
        wrapper.innerHTML = html;
    }

    function onCaptureSuccess(button) {
        var captureBtn = document.getElementById('captureWebhookResponse');
        var recaptureBtn = document.getElementById('recaptureWebhookResponse');
        var toggleSpan = document.getElementById('toggleResponseView');
        var container = document.getElementById('responseContainer');
        var icon = document.getElementById('toggleIcon');
        if (captureBtn) captureBtn.style.display = 'none';
        if (recaptureBtn) recaptureBtn.style.display = '';
        if (toggleSpan) toggleSpan.style.display = 'inline-block';
        if (container) container.style.display = 'block';
        if (icon) icon.innerHTML = '&gt;';
        if (button) { button.textContent = 'Capture Webhook Response'; button.disabled = false; }
    }

    function onRecaptureSuccess(button) {
        if (button) { button.textContent = 'Re-Capture Webhook Response'; button.disabled = false; }
    }

    function handleCaptureClick(e) {
        var btn = e.target.closest('#captureWebhookResponse');
        if (!btn) return;
        e.preventDefault();
        var url = btn.getAttribute('data-fetch-url');
        if (!url) return;
        btn.textContent = 'Processing...';
        btn.disabled = true;
        fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.ok ? r.json() : r.json().then(function(j) { throw j; }); })
            .then(function(response) {
                if (Array.isArray(response)) {
                    renderWebhookResponse(response);
                    onCaptureSuccess(btn);
                    if (window.Swal) window.Swal.fire({ icon: 'success', title: 'Success!', text: 'Webhook response captured successfully', timer: 2000 });
                } else {
                    btn.textContent = 'Capture Webhook Response';
                    btn.disabled = false;
                    if (window.Swal) window.Swal.fire({ icon: 'error', title: 'No Response', text: response.message || 'No webhook response available' });
                }
            })
            .catch(function(err) {
                btn.textContent = 'Capture Webhook Response';
                btn.disabled = false;
                var msg = (err && err.message) ? err.message : 'Failed to capture webhook response';
                if (window.Swal) window.Swal.fire({ icon: 'error', title: 'Error!', text: msg });
            });
    }

    function handleRecaptureClick(e) {
        var btn = e.target.closest('#recaptureWebhookResponse');
        if (!btn) return;
        e.preventDefault();
        var url = btn.getAttribute('data-fetch-url');
        if (!url) return;
        if (window.Swal) {
            window.Swal.fire({
                title: 'Re-Capture Webhook Response?',
                text: 'This will fetch the latest response and might override existing mappings',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, re-capture!'
            }).then(function(result) {
                if (result.isConfirmed) doRecapture(btn, url);
            });
        } else {
            if (confirm('Re-capture webhook response?')) doRecapture(btn, url);
        }
    }

    function doRecapture(btn, url) {
        btn.textContent = 'Processing...';
        btn.disabled = true;
        fetch(url, { method: 'GET', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.ok ? r.json() : r.json().then(function(j) { throw j; }); })
            .then(function(response) {
                if (Array.isArray(response)) {
                    renderWebhookResponse(response);
                    onRecaptureSuccess(btn);
                    if (window.Swal) window.Swal.fire({ icon: 'success', title: 'Updated!', text: 'Response re-captured successfully', timer: 1500 });
                } else {
                    onRecaptureSuccess(btn);
                    if (window.Swal) window.Swal.fire({ icon: 'error', title: 'No Changes', text: response.message || 'No new response available' });
                }
            })
            .catch(function(err) {
                onRecaptureSuccess(btn);
                var msg = (err && err.message) ? err.message : 'Failed to re-capture response';
                if (window.Swal) window.Swal.fire({ icon: 'error', title: 'Error!', text: msg });
            });
    }

    function init() {
        var toggleEl = document.getElementById('toggleResponseView');
        if (toggleEl) {
            toggleEl.removeEventListener('click', toggleResponseView);
            toggleEl.addEventListener('click', toggleResponseView);
        }
        document.removeEventListener('click', handleCaptureClick);
        document.removeEventListener('click', handleRecaptureClick);
        document.addEventListener('click', handleCaptureClick);
        document.addEventListener('click', handleRecaptureClick);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
