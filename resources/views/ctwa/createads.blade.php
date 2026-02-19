@extends('layouts.app', ['title' => __('FB Automation')])

@section('content')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
  body {
    background: linear-gradient(135deg, #e0e7ff 0%, #f5f7fa 100%);
    font-family: 'Inter', sans-serif;
    min-height: 100vh;
  }
  .dark-mode { background: #2a2e3b; color: #e0e0e0; }
  .card-custom { background: #fff; border-radius: 0.75rem; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.05); padding: 1.5rem; }
  .dark-mode .card-custom { background: #343a40; color: #e0e0e0; }
  .step-indicator { display:flex; justify-content:space-between; border-bottom:2px solid #e9ecef; padding-bottom:0.5rem; margin-bottom:1.5rem; }
  .step { cursor:pointer; font-weight:500; color:#6c757d; }
  .step.active { color:#25D366; border-bottom:3px solid #25D366; }
  .form-step { display:none; }
  .form-step.active { display:block; }
  .preview-box { background:#fff; border:1px solid #e9ecef; border-radius:0.75rem; padding:1.25rem; }
  .dark-mode .preview-box { background:#2a2e3b; border-color:#3a3f4a; }
  .preview-profile { width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid #25D366; margin-right:0.75rem; }
  .btn-whatsapp { background:#25D366; color:white; border:none; padding:0.5rem 1rem; border-radius:2rem; font-weight:500; }
  .btn-whatsapp:hover { background:#20c35a; }
  .char-count { font-size:0.85rem; color:#6c757d; text-align:right; }
  .headline-container { display:flex; justify-content:space-between; align-items:center; }
  .form-control.is-invalid { border-color:#dc3545; }
  .invalid-feedback { font-size:0.875rem; }
  .preview-media { width:100%; max-height:300px; border-radius:0.5rem; object-fit:contain; margin-bottom:1rem; }
  .whatsapp-label { font-size:0.85rem; color:#25D366; font-weight:600; margin:0.5rem 0; }
  .timestamp { font-size:0.75rem; color:#adb5bd; margin-top:0.5rem; }
  .like-btn, .share-btn { cursor:pointer; margin-right:1rem; color:#6c757d; font-size:0.9rem; }
  .like-btn:hover, .share-btn:hover { color:#25D366; }
  .form-multiselect { height:auto; min-height:100px; max-height:200px; overflow-y:auto; }
  .select2-selection--multiple { overflow-y:auto!important; max-height:100px; white-space:normal!important; flex-wrap:wrap!important; }
  .select2-container--default .select2-selection--multiple .select2-selection__rendered { display:flex!important; flex-wrap:wrap; gap:4px; padding:4px; }
  .select2-container--default .select2-selection--multiple { min-height:38px; border:1px solid #ced4da; border-radius:0.375rem; }
</style>

<div class="container-fluid mt-4">
    <div class="header-body mb-3">
        <h1 class="mb-2">
            <img src="#" alt="" style="height:30px; vertical-align:middle; margin-right:8px;">
        </h1>
        <p class="text-muted"></p>
    </div>
</div>

<div class="container py-5">
  <div class="d-flex flex-md-row gap-4">
      
    <!-- Left Side: Form Section -->
    <div class="col-md-7">
      <div class="card-custom" role="region" aria-label="Ad Creation Form">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="mb-0 text-dark">Create CTWA Ad</h4>
        </div>
        <div class="step-indicator" role="navigation" aria-label="Form Steps">
          <div class="step active" data-step="1">Ad Details</div>
          <div class="step" data-step="2">Audience</div>
          <div class="step" data-step="3">Budget</div>
          <div class="step" data-step="4">Overview</div>
        </div>

        <form id="ctwaAdForm" action="{{ route('ctwa.create') }}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
              <label for="pageSelector">Select Page</label>
              <select class="form-control" id="pageSelector" name="page_id" required>
                  <option value="">Loading pages...</option>
              </select>
          </div>
          <div class="form-group">
              <label for="adAccountSelector">Select Ad Account</label>
              <select class="form-control" id="adAccountSelector" name="ad_account_id" required>
                  <option value="">Loading ad accounts...</option>
              </select>
          </div>

          <input type="hidden" name="fb_token" value="{{ auth()->user()->fb_long_lived_token }}">
          <input type="hidden" name="whatsapp_number" value="{{ auth()->user()->whatsapp_sender_id }}">

          <!-- Step 1 -->
          <div class="form-step active" data-step="1">
            <div class="form-group">
              <label for="adName">Advertisement Name</label>
              <input type="text" class="form-control" id="adName" name="adName" maxlength="50" required>
              <span class="char-count"><span id="adNameCount">0</span>/50</span>
            </div>
            <div class="form-group">
              <label for="adCaption">Advertisement Caption</label>
              <input type="text" class="form-control" id="adCaption" name="adCaption" maxlength="100" required>
              <span class="char-count"><span id="adCaptionCount">0</span>/100</span>
            </div>
            <div class="form-group">
              <label for="websiteLink">Website Link</label>
              <input type="url" class="form-control" id="websiteLink" name="websiteLink" placeholder="https://..." required>
            </div>
            <div class="form-group">
              <label for="headline">Headline</label>
              <input type="text" class="form-control" id="headline" name="headline" maxlength="25" required>
              <span class="char-count"><span id="headlineCount">0</span>/25</span>
            </div>
            <div class="form-group">
              <label for="whatsappButtonText">WhatsApp Button Text (CTA)</label>
              <input type="text" class="form-control" id="whatsappButtonText" name="whatsapp_button_text" maxlength="20" required>
              <span class="char-count"><span id="whatsappButtonTextCount">0</span>/20</span>
            </div>
            <div class="form-group">
              <label for="prefilledMessage">Prefilled WhatsApp Message</label>
              <textarea class="form-control" id="prefilledMessage" name="prefilled_message" rows="3" maxlength="200" placeholder="Hi, I'm interested in your services..."></textarea>
              <span class="char-count"><span id="prefilledMessageCount">0</span>/200</span>
            </div>
            <div class="form-group">
              <label for="mediaType">Media</label>
              <select class="form-control" id="mediaType" name="mediaType">
                <option value="image">Image</option>
                <option value="video">Video</option>
              </select>
              <input type="file" class="form-control mt-2" id="mediaFile" name="mediaFile" accept="image/*,video/*">
            </div>
          </div>

          <!-- Other steps omitted for brevity (same as your code) -->

        </form>
      </div>
    </div>

    <!-- Right Side: Preview Section -->
    <div class="col-md-5">
      <div class="card-custom" role="region" aria-label="Ad Preview">
        <h5 class="mb-3 text-dark">Ad Preview</h5>
        <div class="preview-box">
         <div class="d-flex align-items-center mb-3">
          <img src="..." alt="Profile" class="preview-profile me-2">
          <div><p class="mb-0 fw-semibold page-name">Anantkamal Software Labs</p></div>
        </div>
          <div class="preview-content">
            <p id="previewCaption" class="mb-2">Enter caption</p>
            <img id="previewImage" class="preview-media" style="display:none;" alt="Ad media">
            <video id="previewVideo" class="preview-media" controls style="display:none;"></video>
            <div class="whatsapp-label">WhatsApp</div>
            <div class="headline-container">
              <p class="fw-semibold mb-0" id="previewHeadline">Enter headline</p>
              <button class="btn btn-whatsapp" id="previewCTAButton">WhatsApp</button>
            </div>
            <p class="small text-muted mt-2" id="previewPrefilled">Prefilled message will appear here...</p>
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

<!-- ✅ Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Load Pages
    $.ajax({
        url: '/meta/pages',
        method: 'GET',
        success: function (response) {
            const selector = $('#pageSelector');
            selector.empty().append(`<option value="">Select a Page</option>`);
            response.pages.forEach(page => {
                selector.append(
                    `<option value="${page.id}" data-token="${page.access_token}">${page.name}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load pages.');
        }
    });

    // Load Ad Accounts
    $.ajax({
        url: '/meta/ad-accounts',
        method: 'GET',
        success: function (response) {
            const selector = $('#adAccountSelector');
            selector.empty().append(`<option value="">Select Ad Account</option>`);
            response.forEach(account => {
                selector.append(
                    `<option value="${account.id}">${account.name || 'Ad Account'} (${account.id})</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load ad accounts.');
        }
    });
});
</script>
@endsection