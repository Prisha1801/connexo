<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize all form elements after a small delay
        function initFormElements() {
            // Initialize Select2
            $('select').select2({
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
            $('select').select2({
                width: '100%',
            });
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
            $('select').select2({
                width: '100%'
            });
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
            $('select').select2({
                width: '100%',
            });
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
                $(this).find(".card-body").slideUp();
                $(this).find(".toggle-icon").text("˅");
            });
            // Expand the current task
            taskItem.find(".card-body").slideDown();
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

        $(document).on("change", ".task-type", function() {
            let taskItem = $(this).closest(".task-item");
            let selectedValue = $(this).val();
            let taskLabel = $(this).closest(".task-item").find(".task-name-label");

            if (selectedValue in taskNames) {
                taskLabel.text(taskNames[selectedValue]);
            }
            focusTask(taskItem);
        });

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
            let taskLabel = taskInput.siblings(".task-name-label");

            if (taskInput.val().trim() !== "") {
                taskLabel.text(taskInput.val().trim());
            }

            taskInput.hide();
            taskLabel.show();
            focusTask(taskItem);
        });

        $(document).on("click", ".add-task-btn", function() {
            let parentTask = $(this).closest(".task-item");

            let newTaskHTML = `

                       <div class="task-item col-md-12 mb-10" data-index="${taskIndex}">
                                        <div class="card task-card">
                                            <div class="card-header d-flex align-items-center justify-content-between gap-4 py-4">
                                                <i class="ki-duotone ki-abstract-20 drag-handle me-2 fs-2x text-gray-400">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                </i>

   <!-- LEFT SIDE -->
   <div class="flex-grow-1">
       <label class="task-name-label fw-bold d-block mt-4 mb-2">Task Name</label>
       <select name="tasks[${taskIndex}][task_type]" class="form-control task-type w-50 mb-4" required>
           <option value="">--Select Task--</option>
           <option value="create_contact">Create Contact</option>
           <option value="call_api">Call API</option>
           <option value="send_whatsapp">Send WhatsApp</option>
       </select>
       <input type="text" name="tasks[${taskIndex}][task_name]" class="form-control task-name mt-2 w-50 mb-2"
           placeholder="Enter Task Name" style="display: none;">
   </div>
   
   <!-- RIGHT SIDE (Keep in single row) -->
   <div class="d-flex align-items-center text-nowrap">
       <button class="btn btn-light toggle-task-body me-2">
           <span class="toggle-icon">˄</span>
       </button>
   
       <div class="dropdown">
           <button class="btn btn-light three-dots-btn" type="button" data-bs-toggle="dropdown">
               ⋮
           </button>
           <ul class="dropdown-menu">
               <li><a class="dropdown-item rename-task-btn" href="javascript:void(0);">
                   <i class="ki-duotone ki-pencil fs-2"><span class="path1"></span><span class="path2"></span>
                       </i> {{ __('Rename') }}</a></li>
               <li><a class="dropdown-item remove-task" href="#">
                   <i class="ki-duotone ki-trash fs-2"><span class="path1"></span><span class="path2"></span>
                   </i> {{ __('Remove Task') }}</a></li>
           </ul>
       </div>
   </div>
   </div>
   
   <div class="card-body additional-fields">
   <!-- Additional fields go here -->
   </div>
   
   <input type="hidden" name="tasks[${taskIndex}][order]" class="task-order" value="${taskIndex}">
   
   <div class="card-footer text-center">
   <button type="button" class="btn btn-primary add-task-btn"
       style="font-size: 1.5rem; padding: 8px 16px; border-radius: 50%;">
       +
   </button>
   </div>
   </div>
   </div>
   `;

            parentTask.after(newTaskHTML);

            $(".task-item .card-body").slideUp();
            $(".task-item .toggle-icon").text("˅");

            parentTask.next().find(".card-body").slideDown();
            parentTask.next().find(".toggle-icon").text("˄");

            focusTask(parentTask.next());

            taskIndex++;
        });

        $(document).on("click", ".remove-task-btn", function() {
            let currentTask = $(this).closest(".task-item");

            if ($(".task-item").length > 1) {
                currentTask.remove();
            } else {
                alert("You must have at least one task.");
            }
        });

        $(document).on("click", ".toggle-task-body", function(event) {
            event.preventDefault();
            let taskBody = $(this).closest(".task-item").find(".card-body");
            let icon = $(this).find(".toggle-icon");

            if (taskBody.is(":visible")) {
                taskBody.slideUp();
                icon.text("˅");
            } else {
                $(".task-item .card-body").slideUp();
                $(".task-item .toggle-icon").text("˅");

                taskBody.slideDown();
                icon.text("˄");
            }
        });

        $(".task-item:first .card-body").show();
        $(".task-item:first .toggle-icon").text("˄");

        $(document).on('click', '.remove-task', function() {
            if ($('#tasks-container .task-item').length === 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Cannot Delete',
                    text: 'You must have at least one task defined.',
                });
                return;
            }

            let taskCard = $(this).closest('.task-item');
            let hasValue = taskCard.find('input, textarea, select').filter(function() {
                return $(this).val().trim() !== '';
            }).length > 0;

            if (hasValue) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This task contains values. Do you want to delete it?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        taskCard.remove();
                        updateTaskOrder();
                    }
                });
            } else {
                taskCard.remove();
                updateTaskOrder();
            }
        });

        // Delegate change event for .task-type to load additional fields.
        $(document).on('change', '.task-type', function() {
            let taskType = $(this).val();
            let container = $(this).closest('.task-item').find('.additional-fields');
            let index = $(this).closest('.task-item').attr('data-index');
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
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="phone${index}">
                <i class="fas fa-plus-circle me-1"></i> Insert Variable
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
           <button type="button" class="btn btn-secondary insert-variable-btn" data-target="waPhone${index}">
               <i class="fas fa-plus-circle me-1"></i> Insert Variable
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
           <button type="button" class="btn btn-secondary insert-variable-btn" data-target="payloadWA${index}"><i class="fas fa-plus-circle me-1"></i> Insert Variable</button>
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
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="url${index}">
                <i class="fas fa-plus-circle me-1"></i> Insert Variable
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
            <button type="button" class="btn btn-secondary insert-variable-btn" data-target="payload${index}"><i class="fas fa-plus-circle me-1"></i> Insert Variable</button>
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

            container.html(html);
            // Reinitialize form elements after loading new content
            setTimeout(initFormElements, 100);
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

        // Minimize/Maximize Response Table
        $('#toggleResponseView').click(function(event) {
            event.preventDefault();
            let responseContainer = $('#responseContainer');
            let button = $(this);

            if (responseContainer.is(':visible')) {
                responseContainer.slideUp();
                $('#toggleIcon').html('&lt;');
            } else {
                responseContainer.slideDown();
                $('#toggleIcon').html('&gt;');
            }
        });

        $('#captureWebhookResponse').click(function(event) {
            event.preventDefault();
            let workflowId = {{ $workflow->id }};
            let button = $(this);
            button.text('Processing...').prop('disabled', true);

            $.ajax({
                url: `/workflow-webhooks/${workflowId}`,
                type: 'GET',
                success: function(response) {
                    button.text('Capture Webhook Response').prop('disabled', false);

                    if (Array.isArray(response)) {
                        // Convert to Re-capture button
                        $('#captureWebhookResponse').hide();
                        $('#recaptureWebhookResponse').show();

                        // Existing response handling
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

                        // Show success alert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Webhook response captured successfully',
                            timer: 2000
                        });
                    } else {
                        // Show error alert for no response
                        Swal.fire({
                            icon: 'error',
                            title: 'No Response',
                            text: response.message ||
                                'No webhook response available'
                        });
                    }
                },
                error: function(xhr) {
                    button.text('Capture Webhook Response').prop('disabled', false);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: xhr.responseJSON?.message ||
                            'Failed to capture webhook response'
                    });
                }
            });
        });

        $('#recaptureWebhookResponse').on('click', function(event) {
            event.preventDefault();
            let workflowId = {{ $workflow->id }};
            let button = $(this);

            Swal.fire({
                title: 'Re-Capture Webhook Response?',
                text: "This will fetch the latest response and might override existing mappings",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, re-capture!'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.text('Processing...').prop('disabled', true);

                    $.ajax({
                        url: `/workflow-webhooks/${workflowId}`,
                        type: 'GET',
                        success: function(response) {
                            button.text('Re-Capture Webhook Response').prop(
                                'disabled', false);

                            if (Array.isArray(response)) {
                                // Update UI and show success
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

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: 'Response re-captured successfully',
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'No Changes',
                                    text: response.message ||
                                        'No new response available'
                                });
                            }
                        },
                        error: function(xhr) {
                            button.text('Re-Capture Webhook Response').prop(
                                'disabled', false);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: xhr.responseJSON?.message ||
                                    'Failed to re-capture response'
                            });
                        }
                    });
                }
            });
        });
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
