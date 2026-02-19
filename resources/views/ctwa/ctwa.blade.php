@extends('layouts.app', ['title' => __('FB Automation')])

@section('content')
<style>
    .card-custom {
      border: none;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      background: linear-gradient(145deg, #ffffff, #f8f9fa);
      border-radius: 12px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      animation: fadeIn 0.5s ease-in;
    }
    .card-custom:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    .card-custom h6 {
      color: #6c757d;
      font-size: 0.9rem;
      text-transform: uppercase;
    }
    .card-custom h4 {
      color: #1a1d29;
      font-weight: 600;
    }
  </style>
<!-- Facebook CTWA Automation Section -->
<div class="container-fluid mt-5 pt-5">
    <div class="header-body mb-3">
        <h1 class="mb-2">
            Facebook CTWA Ads Dashboard
        </h1>
        <p class="text-muted">Monitor, manage, and create CTWA ads seamlessly.</p>
    </div>
</div>

<div class="d-flex">
    <!-- Main Content -->
    <div class="main-content flex-grow-1 container-fluid py-4">
        <div class="row">
            <div class="col-lg-12">
                <!-- Ad Stats -->
                <div class="row g-3 mt-4">
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-custom text-center p-4">
                            <h6>Impressions</h6>
                            <h4>755,083</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-custom text-center p-4">
                            <h6>Spend</h6>
                            <h4>₹18,153.34</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-custom text-center p-4">
                            <h6>Leads</h6>
                            <h4>78</h4>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3">
                        <div class="card card-custom text-center p-4">
                            <h6>Reach</h6>
                            <h4>662,152</h4>
                        </div>
                    </div>
                </div>

                <!-- Ad List Table -->
                <div class="card card-custom mt-4 p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                       <form method="GET" action="{{ route('ctwa.index') }}" class="d-flex w-50 mb-3" id="searchForm">
                            <input type="text" name="search" class="form-control me-2" id="customSearch"
                                   placeholder="Search by Ad name" value="{{ request('search') }}">
                        </form>

                        <div>
                            <!--<button class="btn btn-success me-2" data-bs-toggle="tooltip" title="Sync ad data">-->
                            <!--    <i class="fas fa-sync-alt"></i> Sync-->
                            <!--</button>-->
                            <a href="{{ route('ctwa.fetch_store_ads') }}" class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>Fetch Ads
                            </a>
                            <!--<a href="{{ route('ctwa.create_ads') }}" class="btn btn-outline-primary" id="fetchAds" data-bs-toggle="tooltip" title="Fetch latest ads"><i class="fas fa-download me-2"></i>Create Ads</a>-->
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ad Name</th>
                                    <th>Campaign</th>
                                    <th>Status</th>
                                    <th>Ad Account</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ads as $ad)
                                    <tr class="clickable-row" data-id="{{ $ad->ad_id }}">
                                        <td>{{ $ad->ad_name }}</td>
                                        <td>{{ $ad->campaign_name }}</td>
                                        <td>
                                            @php
                                                $statusClass = match(strtolower($ad->status)) {
                                                    'active' => 'success',
                                                    'paused' => 'warning',
                                                    'deleted' => 'danger',
                                                    'disapproved' => 'danger',
                                                    'pending_review' => 'secondary',
                                                    default => 'secondary',
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }} text-uppercase">{{ $ad->status }}</span>
                                        </td>
                                        <td>{{ $ad->ad_account }}</td>
                                        <td>{{ \Carbon\Carbon::parse($ad->ad_created_at)->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $ads->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="adDetailsModal" tabindex="-1" aria-labelledby="adDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-body p-0" id="adDetailsContent"></div>
        </div>
    </div>
</div>

@endsection