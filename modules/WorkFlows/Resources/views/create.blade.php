@extends('layouts.app-client')

@section('content')
    <!--begin::Container-->
    <div id="kt_app_content_container" class="px-4">
        <!--begin::Card-->
        <div class="container-xxl card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-6">
                <h2 class="card-title align-items-start flex-column mb-0">
                    <span class="card-label fw-bold fs-3 mb-1">Create Workflow</span>
                </h2>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body py-4">
                <form action="{{ route('workflows.store') }}" method="POST">
                    @csrf

                    <!--begin::Input group-->
                    <div class="mb-10 position-relative">
                        <small class="text-muted fs-7 fw-semibold">Trigger : When this happens …</small>
                        <div class="d-flex align-items-center mt-1">
                            <label id="workflownameLabel" class="fs-5 fw-bold text-gray-900 pe-3">Select an app and trigger</label>
                            <input type="text" class="form-control form-control-solid d-none" id="workflowname" name="workflowname">
                            <span id="editWorkflowName" class="svg-icon svg-icon-2 cursor-pointer">
                                <i class="ki-duotone ki-pencil fs-2">
                                    <span class="path1"></span>
                                    <span class="path2"></span>
                                </i>
                            </span>
                        </div>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="mb-10">
                        <label for="app_id" class="form-label fs-6 fw-bold text-gray-700">Select App</label>
                        <select class="form-select form-select-solid" data-control="select2" id="app_id" name="app_id" required data-placeholder="Select an app">
                            <option value="">Select an app</option>
                            <option value="webhook">Webhook</option>
                            <option value="indiamart">IndiaMart</option>
                            <option value="99acres">99Acres</option>
                            <option value="housing">Housing.com</option>
                            <option value="justdial">Justdial</option>
                            <option value="tradeindia">TradeIndia</option>
                            <option value="sulekha">Sulekha</option>
                        </select>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Input group-->
                    <div class="mb-10">
                        <label for="trigger_event" class="form-label fs-6 fw-bold text-gray-700">Trigger Event</label>
                        <select class="form-select form-select-solid" data-control="select2" id="trigger_event" name="trigger_event" disabled data-placeholder="Select Trigger">
                            <option value="" selected>Select Trigger</option>
                        </select>
                    </div>
                    <!--end::Input group-->

                    <!--begin::Actions-->
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">
                            <span class="svg-icon svg-icon-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C10.7 16.1 11.3 16.1 11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L10.3 12.9C9.9 12.5 9.3 12.5 8.9 12.9C8.5 13.3 8.5 13.9 8.9 14.3L10.3 15.7C10.7 16.1 11.3 16.1 11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6Z" fill="currentColor"/>
                                    <path d="M4 22C4 17.6 7.6 14 12 14C16.4 14 20 17.6 20 22C20 22.6 19.6 23 19 23C18.4 23 18 22.6 18 22C18 19.1 15.3 16.5 12 16.5C8.7 16.5 6 19.1 6 22C6 22.6 5.6 23 5 23C4.4 23 4 22.6 4 22Z" fill="currentColor"/>
                                </svg>
                            </span>
                            Create Workflow
                        </button>
                    </div>
                    <!--end::Actions-->
                </form>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::Card-->
    </div>
    <!--end::Container-->

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#app_id, #trigger_event').select2();

            let hasWebhookUrl = false;

            $('#app_id').change(function() {
                let appId = $(this).val();
                let appName = $(this).find("option:selected").text();

                let triggers = getTriggerEvents(appId);
                $("#trigger_event").html('<option value="">Select Trigger</option>').prop("disabled", false);
                
                triggers.forEach(trigger => {
                    $("#trigger_event").append(
                        `<option value="${trigger.toLowerCase().replace(/\s+/g, '_')}">${trigger}</option>`
                    );
                });

                $('#workflownameLabel').text(appName).addClass('fw-bold text-gray-900');
                $('#trigger_event').select2();
            });

            $('#trigger_event').change(function() {
                let appName = $('#app_id option:selected').text();
                let triggerName = $(this).find("option:selected").text();
                
                if (appName && triggerName) {
                    $('#workflownameLabel').text(`${appName}: ${triggerName}`);
                }
            });

            $('form').submit(function() {
                let appName = $('#app_id option:selected').text();
                let triggerName = $('#trigger_event option:selected').text();
                
                if (appName && triggerName) {
                    $('#workflowname').val(`${appName}: ${triggerName}`);
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
        });
    </script>
@endsection