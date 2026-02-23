@extends('layouts.app', ['title' => __('Create Workflow')])

@section('title')
    <title>{{ __('Create Workflow') }}</title>
@endsection

@section('head')
    @include('work-flows::partials.ui')
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-fat-add"></i></span>
                        {{ __('Create Workflow') }}
                    </h1>
                    <span class="lb-subtitle">{{ __('Choose an app and trigger. You’ll get a webhook URL after creating.') }}</span>
                </div>
                <div class="lb-toolbar">
                    <a href="{{ route('workflows.index') }}" class="lb-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>
        </header>

        <div class="lb-body">
            <section class="lb-section">
                <div class="lb-section-hd">
                    <h3 class="lb-section-title">{{ __('Workflow details') }}</h3>
                    <span class="text-muted small">{{ __('Trigger: when this happens …') }}</span>
                </div>
                <div class="lb-section-bd">
                    <form action="{{ route('workflows.store') }}" method="POST" id="workflow-create-form">
                        @csrf

                        <div class="mb-3 position-relative">
                            <label class="form-label font-weight-bold">{{ __('Name') }}</label>
                            <div class="d-flex align-items-center">
                                <label id="workflownameLabel" class="font-weight-bold mb-0" style="font-size: 1.05rem;">{{ __('Select an app and trigger') }}</label>
                                <input type="text" class="form-control d-none ml-3" id="workflowname" name="workflowname" style="max-width: 520px; border-radius: 12px; height: 44px;">
                                <button type="button" id="editWorkflowName" class="lb-icon-btn ml-2" title="{{ __('Rename') }}">
                                    <i class="ni ni-ruler-pencil"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="app_id" class="form-label">{{ __('Select App') }}</label>
                            <select class="form-control" id="app_id" name="app_id" required style="border-radius: 12px; height: 44px;">
                                <option value="">{{ __('Select an app') }}</option>
                                <option value="webhook">Webhook</option>
                                <option value="indiamart">IndiaMart</option>
                                <option value="99acres">99Acres</option>
                                <option value="housing">Housing.com</option>
                                <option value="justdial">Justdial</option>
                                <option value="tradeindia">TradeIndia</option>
                                <option value="sulekha">Sulekha</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="trigger_event" class="form-label">{{ __('Trigger Event') }}</label>
                            <select class="form-control" id="trigger_event" name="trigger_event" disabled style="border-radius: 12px; height: 44px;">
                                <option value="">{{ __('Select Trigger') }}</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('workflows.index') }}" class="lb-btn-soft mr-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="lb-btn-primary">
                                <i class="ni ni-check-bold"></i> {{ __('Create Workflow') }}
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const app = document.getElementById('app_id');
            const trigger = document.getElementById('trigger_event');
            const label = document.getElementById('workflownameLabel');
            const nameInput = document.getElementById('workflowname');
            const editBtn = document.getElementById('editWorkflowName');
            const form = document.getElementById('workflow-create-form');

            function getTriggerEvents(appId) {
                const events = {
                    webhook: ['Catch Webhook', 'Catch Webhook with Headers', 'Catch Webhook with File Data'],
                    indiamart: ['New Leads'],
                    '99acres': ['New Leads'],
                    housing: ['New Leads'],
                    justdial: ['New Leads'],
                    tradeindia: ['New Leads'],
                    sulekha: ['New Leads']
                };
                return events[appId] || [];
            }

            app.addEventListener('change', function() {
                const appId = app.value;
                const opts = getTriggerEvents(appId);
                trigger.innerHTML = '<option value="">{{ __("Select Trigger") }}</option>';
                trigger.disabled = opts.length === 0;
                opts.forEach(function(t) {
                    const opt = document.createElement('option');
                    opt.value = t.toLowerCase().replace(/\s+/g, '_');
                    opt.textContent = t;
                    trigger.appendChild(opt);
                });
                const appName = app.options[app.selectedIndex].text;
                if (appName) label.textContent = appName;
            });

            trigger.addEventListener('change', function() {
                const appName = app.options[app.selectedIndex].text;
                const triggerName = trigger.options[trigger.selectedIndex].text;
                if (appName && triggerName) label.textContent = appName + ': ' + triggerName;
            });

            form.addEventListener('submit', function() {
                const appName = app.options[app.selectedIndex].text;
                const triggerName = trigger.options[trigger.selectedIndex].text;
                if (appName && triggerName) nameInput.value = appName + ': ' + triggerName;
                else if (label && !nameInput.classList.contains('d-none')) nameInput.value = label.textContent.trim();
            });

            if (editBtn) {
                editBtn.addEventListener('click', function() {
                    nameInput.value = label.textContent;
                    nameInput.classList.remove('d-none');
                    label.classList.add('d-none');
                    nameInput.focus();
                });
            }
            if (nameInput) {
                nameInput.addEventListener('blur', function() {
                    const v = nameInput.value.trim();
                    if (v) label.textContent = v;
                    nameInput.classList.add('d-none');
                    label.classList.remove('d-none');
                });
            }
        });
    </script>
@endpush
