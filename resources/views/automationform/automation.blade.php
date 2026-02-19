@extends('layouts.app', ['title' => __('FB Automation')])

@section('content')

<!-- Facebook Lead Automation Section -->
<div class="container-fluid mt-4">
    <div class="header-body mb-3">
        <h1 class="mb-2">
            <img src="{{ asset('assets/imgs/facebook.png') }}" alt="Facebook" style="height: 30px; vertical-align: middle; margin-right: 8px;">
            {{ __('Facebook Lead Ads') }}
        </h1>
        <p class="text-muted">Click the button below to re-authorize your Facebook account and update permissions.</p>
    </div>
</div>

<div class="container mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('automation.reconnect') }}" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-sync-alt mr-2"></i> Connect Facebook
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('facebooklead.index') }}" class="btn btn-secondary btn-lg w-100">
                <i class="fas fa-users mr-2"></i> View Leads
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('automationform.index') }}" class="btn btn-info btn-lg w-100">
                <i class="fas fa-clipboard-list mr-2"></i> Settings
            </a>
        </div>
    </div>
</div>

<!-- Facebook CTWA Automation Section -->
<div class="container-fluid mt-4">
    <div class="header-body mb-3">
        <h1 class="mb-2">
            <img src="{{ asset('assets/imgs/facebook.png') }}" alt="Facebook" style="height: 30px; vertical-align: middle; margin-right: 8px;">
            {{ __('Facebook CTWA Ads') }}
        </h1>
        <p class="text-muted">Click the button below to re-authorize your Facebook account and update permissions.</p>
    </div>
</div>

<div class="container mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('automation.reconnect') }}" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-sync-alt mr-2"></i> Connect Facebook
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('ctwa.index') }}" class="btn btn-secondary btn-lg w-100">
                <i class="fas fa-users mr-2"></i> View CTWA
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('ctwa.create_ads') }}" class="btn btn-info btn-lg w-100">
                <i class="fas fa-clipboard-list mr-2"></i> Create Ads
            </a>
        </div>
    </div>
</div>

<!-- Google Sheet Automation Section -->
<!--<div class="container-fluid mt-4">-->
<!--    <div class="header-body mb-3">-->
<!--        <h1 class="mb-2">-->
<!--        <img src="{{ asset('assets/imgs/sheets.png') }}" alt="Google Sheet" style="height: 30px; vertical-align: middle; margin-right: 8px;">-->
<!--        {{ __('Google Sheet Automation') }}-->
<!--        </h1>-->
<!--        <p class="text-muted">Click the button below to connect your Google account and sync data with Google Sheets.</p>-->
<!--    </div>-->
<!--</div>-->

<!--<div class="container mb-4">-->
<!--    <div class="row g-3">-->
<!--        <div class="col-md-4">-->
<!--            <a href="#" class="btn btn-primary btn-lg w-100">-->
<!--                <i class="fas fa-sync-alt mr-2"></i> Connect Google-->
<!--            </a>-->
<!--        </div>-->
<!--        <div class="col-md-4">-->
<!--            <a href="#" class="btn btn-secondary btn-lg w-100">-->
<!--                <i class="fas fa-table mr-2"></i> View Leads-->
<!--            </a>-->
<!--        </div>-->
<!--        <div class="col-md-4">-->
<!--            <a href="#" class="btn btn-info btn-lg w-100">-->
<!--                <i class="fas fa-cog mr-2"></i> Settings-->
<!--            </a>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!-- LinkedIn Automation Section -->
<!--<div class="container-fluid mt-4">-->
<!--    <div class="header-body mb-3">-->
<!--        <h1 class="mb-2">-->
<!--            <img src="{{ asset('assets/imgs/linkedin.png') }}" alt="LinkedIn" style="height: 30px; vertical-align: middle; margin-right: 8px;">-->
<!--            {{ __('LinkedIn Ads') }}-->
<!--        </h1>-->
<!--        <p class="text-muted">Click the button below to connect your LinkedIn account and sync data with Google Sheets.</p>-->
<!--    </div>-->
<!--</div>-->

<!--<div class="container mb-4">-->
<!--    <div class="row g-3">-->
<!--        <div class="col-md-4">-->
<!--            <a href="#" class="btn btn-primary btn-lg w-100">-->
<!--                <i class="fas fa-sync-alt mr-2"></i> Connect LinkedIn-->
<!--            </a>-->
<!--        </div>-->
<!--        <div class="col-md-4">-->
<!--            <a href="#" class="btn btn-secondary btn-lg w-100">-->
<!--                <i class="fas fa-table mr-2"></i> View Leads-->
<!--            </a>-->
<!--        </div>-->
<!--        <div class="col-md-4">-->
<!--            <a href="#" class="btn btn-info btn-lg w-100">-->
<!--                <i class="fas fa-cog mr-2"></i> Settings-->
<!--            </a>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

@endsection