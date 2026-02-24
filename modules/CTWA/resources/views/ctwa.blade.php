@extends('layouts.app', ['title' => __('CTWA Ads Dashboard')])

@section('content')
@include('ctwa::partials.styles')

<div class="container-fluid mt-5 pt-5 ctwa-wrap">
    <div class="ctwa-page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h1 class="mb-1">CTWA Ads Dashboard</h1>
            <p class="ctwa-subtitle mb-0">Monitor, manage, and create CTWA ads seamlessly.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ url()->previous() ?? route('dashboard') }}" class="btn btn-sm ctwa-btn-soft">{{ __('Back') }}</a>
        </div>
    </div>

    <div class="ctwa-card mb-4">
        <div class="p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-3 mb-3">
                <form method="GET" action="{{ route('ctwa.index') }}" class="flex-grow-1">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0"><i class="ni ni-zoom-split-in text-muted"></i></span>
                        <input type="text" name="search" class="form-control ctwa-input border-0"
                               placeholder="Search by ad name" value="{{ request('search') }}">
                    </div>
                </form>
                <div class="d-flex gap-2">
                    <a href="{{ route('ctwa.fetch_store_ads') }}" class="btn ctwa-btn-soft">
                        <i class="fas fa-download me-2"></i>{{ __('Fetch Ads') }}
                    </a>
                    <a href="{{ route('ctwa.create_ads') }}" class="btn ctwa-btn-primary">
                        <i class="ni ni-fat-add me-2"></i>{{ __('Create CTWA Ad') }}
                    </a>
                </div>
            </div>

            @php
                $totals = $finalTotals ?? [
                    'impressions' => 0, 'reach' => 0, 'spend' => 0,
                    'chats' => 0, 'leads' => 0, 'clicks' => 0,
                ];
            @endphp
            <div class="row g-3 mb-3">
                <div class="col-6 col-md-2">
                    <div class="ctwa-stat-card">
                        <div>
                            <div class="stat-label">{{ __('Impressions') }}</div>
                            <div class="stat-value">{{ number_format($totals['impressions']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="ctwa-stat-card">
                        <div>
                            <div class="stat-label">{{ __('Reach') }}</div>
                            <div class="stat-value">{{ number_format($totals['reach']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="ctwa-stat-card">
                        <div>
                            <div class="stat-label">{{ __('Spend') }}</div>
                            <div class="stat-value">₹{{ number_format($totals['spend'], 2) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="ctwa-stat-card">
                        <div>
                            <div class="stat-label">{{ __('Clicks') }}</div>
                            <div class="stat-value">{{ number_format($totals['clicks']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="ctwa-stat-card">
                        <div>
                            <div class="stat-label">{{ __('Chats') }}</div>
                            <div class="stat-value">{{ number_format($totals['chats']) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <div class="ctwa-stat-card">
                        <div>
                            <div class="stat-label">{{ __('Leads') }}</div>
                            <div class="stat-value">{{ number_format($totals['leads']) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive mt-3">
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
