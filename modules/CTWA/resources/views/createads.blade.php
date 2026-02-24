@extends('layouts.app-client')

@section('title', 'FB Automation')

@section('content')
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
  body { background: linear-gradient(135deg,#e0e7ff 0%,#f5f7fa 100%); font-family:Inter, sans-serif; min-height:100vh; }
  .card-custom { background:#fff; border-radius:.75rem; box-shadow:0 .5rem 1rem rgba(0,0,0,.05); padding:1.5rem; }
  .step-indicator { display:flex; justify-content:space-between; border-bottom:2px solid #e9ecef; padding-bottom:.5rem; margin-bottom:1.5rem; }
  .step { cursor:pointer; font-weight:500; color:#6c757d; padding: .25rem .5rem; }
  .step.active { color:#25D366; border-bottom:3px solid #25D366; }
  .form-step { display:none; }
  .form-step.active { display:block; }
  .preview-box { background:#fff; border:1px solid #e9ecef; border-radius:.75rem; padding:1.25rem; }
  .preview-profile { width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid #25D366; margin-right:.75rem; }
  .btn-whatsapp { background:#25D366; color:white; border:none; padding:.5rem 1rem; border-radius:2rem; font-weight:500; }
  .char-count { font-size:.85rem; color:#6c757d; text-align:right; }
  .preview-media { width:100%; max-height:300px; border-radius:.5rem; object-fit:contain; margin-bottom:1rem; }
  .form-control.is-invalid { border-color:#dc3545; }
  .select2-selection--multiple { overflow-y:auto!important; max-height:100px; white-space:normal!important; flex-wrap:wrap!important; }
  .whatsapp-label { font-size:.85rem; color:#25D366; font-weight:600; margin:0.5rem 0; }
  .headline-container { display:flex; justify-content:space-between; align-items:center; }
  .like-btn, .share-btn { cursor:pointer; margin-right:1rem; color:#6c757d; font-size:0.9rem; }
</style>

<div class="container-fluid mt-4">
  <div class="header-body mb-3">
    <h1 class="mb-2" style="text-align:center;">
      <img src="#" alt="" style="height:30px; vertical-align:middle; margin-right:8px;">
      Create CTWA Ad
    </h1>
  </div>
</div>

<div class="container py-5">
  <div class="d-flex flex-md-row gap-4">
    <!-- Left: Form -->
    <div class="col-md-7">
      <div class="card-custom" role="region" aria-label="Ad Creation Form">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0 text-dark">Create CTWA Ad</h4>
        </div>

        <div class="step-indicator" role="navigation" aria-label="Form Steps">
          <div class="step active" data-step="0" tabindex="0">Ad Details</div>
          <div class="step" data-step="1" tabindex="0">Audience</div>
          <div class="step" data-step="2" tabindex="0">Budget</div>
          <div class="step" data-step="3" tabindex="0">Overview</div>
        </div>

        <form id="ctwaAdForm" action="{{ route('ctwa.create') }}" method="post" enctype="multipart/form-data" novalidate>
          @csrf

          <div class="form-group mb-3">
            <label for="pageSelector">Select Page</label>
            <select class="form-control" id="pageSelector" name="page_id" required>
              <option value="">{{ __('Loading pages...') }}</option>
            </select>
          </div>

          <div class="form-group mb-3">
            <label for="adAccountSelector">Select Ad Account</label>
            <select class="form-control" id="adAccountSelector" name="ad_account_id" required>
              <option value="">{{ __('Loading ad accounts...') }}</option>
            </select>
          </div>

          <input type="hidden" name="fb_token" value="{{ $token ?? '' }}">
          <input type="hidden" name="whatsapp_number" value="{{ auth()->user()->whatsapp_sender_id ?? '' }}">
          <input type="hidden" name="geo_targeting" id="geo_targeting" value="[]">

          <!-- STEP 1 -->
          <div class="form-step active" data-step="0">
            <div class="form-group mb-2">
              <label for="adName">Advertisement Name</label>
              <input type="text" class="form-control" id="adName" name="adName" maxlength="50" required>
              <div class="char-count"><span id="adNameCount">0</span>/50</div>
            </div>

            <div class="form-group mb-2">
              <label for="adCaption">Advertisement Caption</label>
              <input type="text" class="form-control" id="adCaption" name="adCaption" maxlength="100" required>
              <div class="char-count"><span id="adCaptionCount">0</span>/100</div>
            </div>

            <div class="form-group mb-2">
              <label for="websiteLink">Website Link</label>
              <input type="url" class="form-control" id="websiteLink" name="websiteLink" placeholder="https://..." required>
            </div>

            <div class="form-group mb-2">
              <label for="headline">Headline</label>
              <input type="text" class="form-control" id="headline" name="headline" maxlength="25" required>
              <div class="char-count"><span id="headlineCount">0</span>/25</div>
            </div>

            <div class="form-group mb-2">
              <label for="whatsappButtonText">WhatsApp Button Text (CTA)</label>
              <input type="text" class="form-control" id="whatsappButtonText" name="whatsapp_button_text" maxlength="20" required>
              <div class="char-count"><span id="whatsappButtonTextCount">0</span>/20</div>
            </div>

            <div class="form-group mb-2">
              <label for="prefilledMessage">Prefilled WhatsApp Message</label>
              <textarea class="form-control" id="prefilledMessage" name="prefilled_message" rows="3" maxlength="200" placeholder="Hi, I'm interested..."></textarea>
              <div class="char-count"><span id="prefilledMessageCount">0</span>/200</div>
            </div>

            <div class="form-group mb-2">
              <label for="mediaType">Media</label>
              <select class="form-control" id="mediaType" name="mediaType">
                <option value="image">Image</option>
                <option value="video">Video</option>
              </select>
              <input type="file" class="form-control mt-2" id="mediaFile" name="mediaFile" accept="image/*,video/*">
            </div>
          </div>

          <!-- STEP 2 - Audience -->
          <div class="form-step" data-step="1">
            <div class="form-group mb-2">
              <label for="ageFrom">Age Range</label>
              <div class="d-flex gap-2">
                <input type="number" class="form-control" id="ageFrom" name="ageFrom" min="13" max="100" value="18" required>
                <input type="number" class="form-control" id="ageTo" name="ageTo" min="13" max="100" value="45" required>
              </div>
            </div>

            <div class="form-group mb-2">
              <label for="gender">Gender</label>
              <select class="form-control" id="gender" name="gender" required>
                <option value="all">All</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>

            <div class="form-group mb-2">
              <label for="country">Target Countries (multiple)</label>
              <select id="country" name="country[]" class="form-control" multiple="multiple" required></select>
            </div>

            <div class="form-group mb-2">
              <label for="targeting">Interests (multiple)</label>
              <select id="targeting" name="targeting[]" class="form-control" multiple="multiple" required></select>
            </div>
          </div>

          <!-- STEP 3 - Budget -->
          <div class="form-step" data-step="2">
            <div class="form-group mb-2">
              <label for="durationPicker">Start Date</label>
              <input type="text" id="durationPicker" class="form-control" name="startDate" required>
            </div>

            <div class="form-group mb-2">
              <label for="durationSlider">Duration (days): <span id="durationDisplay">30</span></label>
              <input type="range" class="form-range" id="durationSlider" name="durationDays" min="1" max="365" value="30">
            </div>

            <div class="form-group mb-2">
              <label for="dailyBudget">Daily Budget (₹)</label>
              <input type="number" step="0.01" min="0" class="form-control" id="dailyBudget" name="dailyBudget" required>
              <small class="form-text text-muted">Minimum ₹83.60 per day</small>
            </div>

            <div class="form-group mb-2">
              <label>Estimated Budget</label>
              <div class="fw-bold" id="estimatedBudget">0</div>
            </div>
          </div>

          <!-- STEP 4 - Overview -->
          <div class="form-step" data-step="3">
            <h5>Overview</h5>
            <p><strong>Name:</strong> <span id="summaryName">Not provided</span></p>
            <p><strong>Caption:</strong> <span id="summaryCaption">Not provided</span></p>
            <p><strong>Headline:</strong> <span id="summaryHeadline">Not provided</span></p>
            <p><strong>Website:</strong> <span id="summaryWebsite">Not provided</span></p>
            <p><strong>CTA:</strong> <span id="summaryCTA">WhatsApp</span></p>
            <p><strong>Prefilled:</strong> <span id="summaryPrefilled">Not provided</span></p>
            <p><strong>Age:</strong> <span id="summaryAge">-</span></p>
            <p><strong>Gender:</strong> <span id="summaryGender">-</span></p>
            <p><strong>Budget:</strong> <span id="summaryBudget">0</span></p>
            <p><strong>Start Date:</strong> <span id="summaryStartDate">-</span></p>
            <p><strong>Duration:</strong> <span id="summaryDuration">-</span></p>
            <p><strong>Countries:</strong> <span id="summaryCountry">-</span></p>
            <p><strong>Interests:</strong> <span id="summaryInterests">-</span></p>
          </div>

          <div class="d-flex justify-content-between mt-3">
            <button type="button" id="prevBtn" class="btn btn-outline-secondary" disabled>Previous</button>
            <div>
              <button type="button" id="clearBtn" class="btn btn-light me-2">Clear</button>
              <button type="button" id="nextBtn" class="btn btn-primary">Next</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Right: Preview -->
    <div class="col-md-5">
      <div class="card-custom" role="region" aria-label="Ad Preview">
        <h5 class="mb-3 text-dark">Ad Preview</h5>
        <div class="preview-box">
          <div class="d-flex align-items-center mb-3">
            <img src="{{ asset('assets/images/no-image.png') }}" alt="Profile" class="preview-profile me-2 preview-profile-img">
            <div><p class="mb-0 fw-semibold page-name">Page name</p></div>
          </div>

          <div class="preview-content">
            <p id="previewCaption" class="mb-2">Enter caption</p>
            <img id="previewImage" class="preview-media" style="display:none;" alt="Ad media">
            <video id="previewVideo" class="preview-media" controls style="display:none;"></video>
            <div class="whatsapp-label">WhatsApp</div>
            <div class="headline-container">
              <p class="fw-semibold mb-0" id="previewHeadline">Enter headline</p>
              <button type="button" class="btn btn-whatsapp" id="previewCTAButton">WhatsApp</button>
            </div>
            <p class="small text-muted mt-2" id="previewPrefilled">Prefilled message will appear here...</p>
            <p class="small text-muted mt-2">Countries: <span id="previewCountry">Not selected</span></p>
            <p class="small text-muted">Interests: <span id="previewInterests">No interests selected</span></p>
          </div>

          <div class="mt-3 d-flex justify-content-around">
            <span class="like-btn"><i class="fas fa-heart"></i> Like</span>
            <span class="share-btn"><i class="fas fa-share"></i> Share</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/create_ads.js') }}"></script>
@endsection
