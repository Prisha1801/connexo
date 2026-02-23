@extends('layouts.app', ['title' => __('Lead Campaign Summary')])

@section('content')
<div class="container py-5">
    <h2 class="mb-4 fw-bold mt-5">📊 Lead Campaign Summary</h2>

    <div class="row g-4">
        <!-- Ad Info -->
        <div class="col-lg-4">
            <div class="card summary-card h-100">
                <h1 class="card-header bg-light fw-semibold">🎯 Ad Information</h1>
                <div class="card-body">
                    <p><span class="summary-title">Ad Account</span><br><span class="summary-value">{{ $summary['ad']->ad_account ?? 'N/A' }}</span></p>

                    <p><span class="summary-title">Gender</span><br><span class="summary-value">{{ ucfirst($summary['gender'] ?? 'All') }}</span></p>

                    <p><span class="summary-title">Age Group</span><br>
                       <span class="summary-value">
                          {{ isset($summary['age_from'], $summary['age_to']) ? $summary['age_from'] . ' ~ ' . $summary['age_to'] : 'N/A' }}
                       </span>
                    </p>


                    <p><span class="summary-title">Daily Budget</span><br><span class="summary-value">₹ {{ number_format(((float) ($summary['daily_budget'] ?? 0)) / 100, 2) }}</span></p>

                    <p><span class="summary-title">Created</span><br><span class="summary-value">{{ \Carbon\Carbon::parse($summary['ad_created'])->format('M d, Y, h:i A') }}</span></p>

                    <p><span class="summary-title">Stopped</span><br><span class="summary-value">{{ $summary['campaign_stop'] ? \Carbon\Carbon::parse($summary['campaign_stop'])->format('M d, Y, h:i A') : 'N/A' }}</span></p>
                </div>
            </div>
        </div>

        <!-- Metrics -->
        <div class="col-lg-8">
            <div class="row g-4">
                @php
                    $metrics = [
                        ['label' => 'Status', 'value' => $summary['ad_status'] ?? 'N/A', 'icon' => 'bi-flag'],
                        ['label' => 'Budget Used', 'value' => '₹ ' . ($insights['spend'] ?? '0'), 'icon' => 'bi-currency-rupee'],
                        ['label' => 'CPC', 'value' => '₹ ' . ($insights['cpc'] ?? '0'), 'icon' => 'bi-cursor-fill'],
                        ['label' => 'Impressions', 'value' => $insights['impressions'] ?? '0', 'icon' => 'bi-eye'],
                        ['label' => 'Reach', 'value' => $insights['reach'] ?? '0', 'icon' => 'bi-graph-up'],
                        ['label' => 'Clicks', 'value' => $insights['clicks'] ?? '0', 'icon' => 'bi-mouse'],
                        ['label' => 'Unique Clicks', 'value' => $insights['unique_clicks'] ?? '0', 'icon' => 'bi-fingerprint'],
                        ['label' => 'Messages', 'value' => collect($insights['actions'] ?? [])->firstWhere('action_type', 'onsite_web_conversion.messaging_conversation_started')['value'] ?? '0', 'icon' => 'bi-chat-dots-fill'],
                    ];
                @endphp

                @foreach($metrics as $metric)
                <div class="col-6 col-md-3 mt-2">
                    <div class="card summary-card h-100 text-center">
                        <div class="card-body">
                            <div class="text-muted">
                                <i class="bi {{ $metric['icon'] }} metric-icon"></i>{{ $metric['label'] }}
                            </div>
                            <div class="summary-value mt-2">{{ $metric['value'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="card summary-card mt-4">
                <div class="card-body">
                    <div class="section-header">📥 Leads Generated</div>
            
                    <div class="row g-3 align-items-center mb-4">
                        <div class="col-md-4">
                            <select id="preset-range" class="form-select form-select form-control">
                                <option value="7" selected>Last 7 days</option>
                                <option value="14">Last 14 days</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="date" id="start-date" class="form-control"
                                value="{{ now()->subDays(7)->toDateString() }}">
                        </div>
                        <div class="col-md-4">
                            <input type="date" id="end-date" class="form-control" value="{{ now()->toDateString() }}">
                        </div>
                    </div>
            
                    <div class="row g-4 text-center" id="leads-summary">
                        <div class="col-md-4">
                            <div class="lead-box">
                                <div class="summary-title">Total</div>
                                <div class="summary-value">{{ count($leads) }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-box success">
                                <div class="summary-title">New</div>
                                <div class="summary-value">-</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="lead-box warning">
                                <div class="summary-title">Existing</div>
                                <div class="summary-value">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @if($CTWAAdsClickLead->count())
            <div class="card mt-4 col-12">
                <div class="card-header fw-bold">🧾 CTWA Leads</div>
                <div class="mt-3">
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#broadcastModal">
                        📢 Broadcast
                    </button>
                </div>
        
                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="selectAllLeads"></th>
                                <th>#</th>
                                <th>Submitted At</th>
                                <th>WA ID</th>
                                <th>Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($CTWAAdsClickLead as $index => $lead)
                                @php
                                    $url = $lead->source_url;
                                    $platform = 'Unknown';
                                
                                    if (Str::contains($url, 'instagram.com')) {
                                        $platform = 'Instagram';
                                    } elseif (Str::contains($url, ['fb.me', 'facebook.com'])) {
                                        $platform = 'Facebook';
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <input type="checkbox" class="leadCheckbox"
                                               data-lead='@json([
                                                   "wa_id"=>$lead->wa_id,
                                                   "contact_id"=>$lead->contact_id,
                                                   "company_id"=>$lead->company_id
                                               ])'>
                                    </td>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($lead->created_at)->format('M d, Y h:i A') }}</td>
                                    <td>{{ $lead->wa_id }}</td>
                                    <td><a href="{{ $url }}" target="_blank">{{ $platform }}</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>


<!-- Broadcast Modal -->
<div class="modal fade" id="broadcastModal" tabindex="-1" aria-labelledby="broadcastModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="campaignForm" action="{{ url('/campaigns/send') }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="broadcastModalLabel">Create Campaign</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label>Campaign Name</label>
                <input type="text" name="campaign_name" class="form-control" required>
            </div>
            <div class="mb-4">
                <label for="template_id" class="form-label">Template</label>
                <select name="template_id" id="template_id" class="form-control" required>
                    <option value="">Select Template</option>
                    @foreach ($templates as $fblead)
                        <option value="{{ $fblead->id }}">{{ $fblead->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="scheduleToggle">
                <label class="form-check-label" for="scheduleToggle">Schedule Date and Time</label>
            </div>
            <div class="mb-3 d-none" id="scheduleInput">
                <label>Select Date & Time</label>
                <input type="datetime-local" name="scheduled_at" class="form-control">
            </div>
            <input type="hidden" name="selected_leads" id="selectedLeadsInput">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Send Now</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection