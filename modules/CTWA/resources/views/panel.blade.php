@extends('layouts.app', ['title' => __('CTWA Panel')])

@section('content')
@include('ctwa::partials.styles')

<div class="container-fluid mt-5 pt-5 ctwa-wrap">
    <div class="ctwa-page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h1 class="mb-1">CTWA Panel</h1>
            <p class="ctwa-subtitle mb-0">Manage your Click to WhatsApp Ads and leads</p>
        </div>
        <nav class="ctwa-nav">
            <a href="{{ route('ctwa.panel') }}" class="{{ request()->routeIs('ctwa.panel') ? 'active' : '' }}"><i class="ni ni-app me-1"></i> Panel</a>
            <a href="{{ route('ctwa.index') }}" class="{{ request()->routeIs('ctwa.index') ? 'active' : '' }}"><i class="ni ni-chart-bar-32 me-1"></i> Ads</a>
            <a href="{{ route('ctwa.leads') }}" class="{{ request()->routeIs('ctwa.leads') ? 'active' : '' }}"><i class="ni ni-single-02 me-1"></i> Leads</a>
            <a href="{{ route('ctwa.create_ads') }}" class="{{ request()->routeIs('ctwa.create_ads') ? 'active' : '' }}"><i class="ni ni-fat-add me-1"></i> Create Ads</a>
        </nav>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">{{ __('Total Ads') }}</div>
                <div class="stat-value">{{ number_format($adsCount ?? 0) }}</div>
                <small class="text-muted">{{ __('From Meta') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">{{ __('Total Impressions') }}</div>
                <div class="stat-value">{{ number_format($insights['impressions'] ?? 0) }}</div>
                <small class="text-muted">{{ __('Last 30 days, from Meta') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">{{ __('Total Spend') }}</div>
                <div class="stat-value">₹{{ number_format($insights['spend'] ?? 0, 2) }}</div>
                <small class="text-muted">{{ __('Last 30 days, from Meta') }}</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="ctwa-stat-card">
                <div class="stat-label">{{ __('Total Leads') }}</div>
                <div class="stat-value">{{ number_format($leadsCount ?? 0) }}</div>
                <small class="text-muted">{{ __('Stored from Meta webhooks') }}</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('ctwa.index') }}" class="ctwa-panel-card card">
                <div class="card-body d-flex align-items-center gap-4">
                    <div class="panel-icon flex-shrink-0"><i class="ni ni-chart-bar-32"></i></div>
                    <div>
                        <div class="panel-label">Ads Dashboard</div>
                        <div class="panel-title">View & Manage Ads</div>
                        <div class="panel-desc">Monitor impressions, spend & performance (from Meta)</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('ctwa.leads') }}" class="ctwa-panel-card card">
                <div class="card-body d-flex align-items-center gap-4">
                    <div class="panel-icon flex-shrink-0"><i class="ni ni-single-02"></i></div>
                    <div>
                        <div class="panel-label">Leads by Meta ID</div>
                        <div class="panel-title">CTWA Leads</div>
                        <div class="panel-desc">Leads grouped by ad source (from Meta webhooks)</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-sm-6 col-lg-4">
            <a href="{{ route('ctwa.create_ads') }}" class="ctwa-panel-card card">
                <div class="card-body d-flex align-items-center gap-4">
                    <div class="panel-icon flex-shrink-0"><i class="ni ni-fat-add"></i></div>
                    <div>
                        <div class="panel-label">Create New Ad</div>
                        <div class="panel-title">Launch CTWA Ad</div>
                        <div class="panel-desc">Create a new Click to WhatsApp campaign (via Meta API)</div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
