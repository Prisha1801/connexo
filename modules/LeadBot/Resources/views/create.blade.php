@extends('layouts.app', ['title' => __('Create LeadBot')])

@section('title')
    <title>{{ __('Create LeadBot') }}</title>
@endsection

@section('head')
    @include('lead-bot::partials.ui')
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-fat-add"></i></span>
                        {{ __('Create LeadBot') }}
                    </h1>
                    <span class="lb-subtitle">{{ __('Choose a source and trigger. You’ll get a webhook URL after creating.') }}</span>
                </div>
                <div class="lb-toolbar">
                    <a href="{{ route('lead-bot.index') }}" class="lb-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>
        </header>

        <div class="lb-body">
            <section class="lb-section">
                <div class="lb-section-hd">
                    <h3 class="lb-section-title">{{ __('Bot details') }}</h3>
                    <span class="text-muted small">{{ __('Tip: use a name that describes what it does.') }}</span>
                </div>
                <div class="lb-section-bd">
                    <form action="{{ route('lead-bot.store') }}" method="POST" id="leadbot-create-form">
                        @csrf

                        <div class="form-group">
                            <label class="form-control-label font-weight-bold">{{ __('Bot Name') }}</label>
                            <input type="text" class="form-control" name="name" id="name"
                                placeholder="{{ __('Example: Webhook → Create Contact → WhatsApp') }}" required
                                value="{{ old('name') }}" style="border-radius: 12px; height: 44px;">
                            @error('name')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_id" class="form-control-label font-weight-bold">{{ __('App') }}</label>
                                    <select class="form-control" id="app_id" name="app_id" required
                                        style="border-radius: 12px; height: 44px;">
                                        <option value="">{{ __('Select an app') }}</option>
                                        <option value="meta">{{ __('Meta (Facebook Leads)') }}</option>
                                        <option value="webhook">{{ __('Webhook') }}</option>
                                        <option value="indiamart">{{ __('IndiaMart') }}</option>
                                        <option value="99acres">{{ __('99Acres') }}</option>
                                        <option value="housing">{{ __('Housing.com') }}</option>
                                        <option value="justdial">{{ __('Justdial') }}</option>
                                        <option value="tradeindia">{{ __('TradeIndia') }}</option>
                                        <option value="sulekha">{{ __('Sulekha') }}</option>
                                    </select>
                                    @error('app_id')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="trigger_event"
                                        class="form-control-label font-weight-bold">{{ __('Trigger') }}</label>
                                    <select class="form-control" id="trigger_event" name="trigger_event" required disabled
                                        style="border-radius: 12px; height: 44px;">
                                        <option value="">{{ __('Select trigger') }}</option>
                                    </select>
                                    @error('trigger_event')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('lead-bot.index') }}" class="lb-btn-soft mr-2">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="lb-btn-primary">
                                <i class="ni ni-check-bold"></i> {{ __('Create') }}
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

            function triggersFor(appId) {
                const events = {
                    meta: ["New Lead"],
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

            app.addEventListener('change', function() {
                const appId = app.value;
                const list = triggersFor(appId);
                trigger.innerHTML = '<option value="">{{ __('Select trigger') }}</option>';
                trigger.disabled = list.length === 0;
                list.forEach(t => {
                    const opt = document.createElement('option');
                    opt.value = t.toLowerCase().replace(/\s+/g, '_');
                    opt.textContent = t;
                    trigger.appendChild(opt);
                });
            });
        });
    </script>
@endpush

