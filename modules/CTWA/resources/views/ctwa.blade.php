@extends('layouts.app', ['title' => __('CTWA Ads Dashboard')])

@section('content')
@include('ctwa::partials.styles')

<div class="container-fluid mt-5 pt-5 ctwa-wrap">
    <div class="ctwa-page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h1 class="mb-1">Ads Dashboard</h1>
            <p class="ctwa-subtitle mb-0">Monitor, manage, and create CTWA ads seamlessly</p>
        </div>
        @include('ctwa::partials.nav')
    </div>

    <div class="row g-4 mb-4">
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">Impressions</div>
                <div class="stat-value">755,083</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">Spend</div>
                <div class="stat-value">₹18,153</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">Leads</div>
                <div class="stat-value">78</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">Reach</div>
                <div class="stat-value">662,152</div>
            </div>
        </div>
    </div>

    <div class="ctwa-card">
        <div class="p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <form method="GET" action="{{ route('ctwa.index') }}" class="d-flex" style="max-width: 320px;">
                    <input type="text" name="search" class="form-control ctwa-input me-2" placeholder="Search by Ad name" value="{{ request('search') }}">
                    <button type="submit" class="btn ctwa-btn-soft"><i class="ni ni-zoom-split-in"></i></button>
                </form>
                <a href="{{ route('ctwa.fetch_store_ads') }}" class="btn ctwa-btn-primary">
                    <i class="fas fa-download me-2"></i>Fetch Ads
                </a>
            </div>

            <div class="table-responsive">
                <table class="table ctwa-table">
                    <thead>
                        <tr>
                            <th>Ad Name</th>
                            <th>Campaign</th>
                            <th>Status</th>
                            <th>Ad Account</th>
                            <th>Date</th>
                            <th style="width: 80px;">Leads</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ads as $ad)
                            <tr class="clickable-row" data-id="{{ $ad->ad_id }}">
                                <td><strong>{{ $ad->ad_name }}</strong></td>
                                <td>{{ $ad->campaign_name }}</td>
                                <td>
                                    @php
                                        $statusClass = match(strtolower($ad->status)) {
                                            'active' => 'bg-success',
                                            'paused' => 'bg-warning',
                                            'deleted', 'disapproved' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge ctwa-badge {{ $statusClass }} text-white">{{ $ad->status }}</span>
                                </td>
                                <td><small class="text-muted">{{ $ad->ad_account }}</small></td>
                                <td>{{ \Carbon\Carbon::parse($ad->ad_created_at)->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('ctwa.leads', ['source_id' => $ad->ad_id]) }}" class="btn btn-sm ctwa-btn-soft" title="View leads">
                                        <i class="ni ni-single-02"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-3 border-top" style="border-color: var(--ctwa-border) !important;">
                {{ $ads->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adDetailsModal" tabindex="-1" aria-labelledby="adDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content ctwa-card">
            <div class="modal-body p-0" id="adDetailsContent"></div>
        </div>
    </div>
</div>
@endsection
