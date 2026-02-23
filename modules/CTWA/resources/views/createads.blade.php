@extends('layouts.app', ['title' => __('Create CTWA Ad')])

@section('content')
@include('ctwa::partials.styles')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.9/dist/flatpickr.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

<style>
  .ctwa-create .form-group { margin-bottom: 1.25rem; }
  .ctwa-create .form-group label { font-size: 0.85rem; font-weight: 600; color: var(--ctwa-slate); }
  .ctwa-create .form-control { border: 1px solid var(--ctwa-border); border-radius: 10px; padding: 0.6rem 1rem; }
  .ctwa-create .form-control:focus { border-color: var(--ctwa-primary); box-shadow: 0 0 0 3px rgba(37, 211, 102, 0.15); }
  .char-count { font-size: 0.8rem; color: var(--ctwa-slate-muted); text-align: right; margin-top: 0.25rem; }
  .headline-container { display: flex; justify-content: space-between; align-items: center; }
  .preview-media { width: 100%; max-height: 300px; border-radius: 10px; object-fit: contain; margin-bottom: 1rem; }
  .whatsapp-label { font-size: 0.85rem; color: var(--ctwa-primary); font-weight: 600; margin: 0.5rem 0; }
  .like-btn, .share-btn { cursor: pointer; margin-right: 1rem; color: var(--ctwa-slate-muted); font-size: 0.9rem; }
</style>

<div class="container-fluid mt-5 pt-5 ctwa-wrap">
    <div class="ctwa-page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h1 class="mb-1">Create CTWA Ad</h1>
            <p class="ctwa-subtitle mb-0">Launch a new Click to WhatsApp ad campaign</p>
        </div>
        @include('ctwa::partials.nav')
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ctwa-card p-4 ctwa-create">
                <h5 class="mb-4 fw-600" style="color: var(--ctwa-slate);">Ad Details</h5>
                <div class="ctwa-step-indicator">
                    <div class="ctwa-step active" data-step="1">Ad Details</div>
                    <div class="ctwa-step" data-step="2">Audience</div>
                    <div class="ctwa-step" data-step="3">Budget</div>
                    <div class="ctwa-step" data-step="4">Overview</div>
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
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="ctwa-card p-4">
                <h5 class="mb-3 fw-600" style="color: var(--ctwa-slate);">Ad Preview</h5>
                <div class="ctwa-preview-box">
                    <div class="d-flex align-items-center mb-3">
                        <img src="..." alt="Profile" class="ctwa-preview-profile me-2">
                        <p class="mb-0 fw-semibold page-name">Anantkamal Software Labs</p>
                    </div>
                    <p id="previewCaption" class="mb-2 text-muted small">Enter caption</p>
                    <img id="previewImage" class="preview-media" style="display:none;" alt="Ad media">
                    <video id="previewVideo" class="preview-media" controls style="display:none;"></video>
                    <div class="whatsapp-label">WhatsApp</div>
                    <div class="headline-container">
                        <p class="fw-semibold mb-0" id="previewHeadline">Enter headline</p>
                        <button type="button" class="btn ctwa-btn-whatsapp" id="previewCTAButton">WhatsApp</button>
                    </div>
                    <p class="small text-muted mt-2" id="previewPrefilled">Prefilled message will appear here...</p>
                    <div class="mt-3 d-flex justify-content-around pt-3" style="border-top: 1px solid var(--ctwa-border);">
                        <span class="like-btn"><i class="fas fa-heart"></i> Like</span>
                        <span class="share-btn"><i class="fas fa-share"></i> Share</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $.ajax({
        url: '/meta/pages',
        method: 'GET',
        success: function (response) {
            const selector = $('#pageSelector');
            selector.empty().append(`<option value="">Select a Page</option>`);
            response.pages.forEach(page => {
                selector.append(`<option value="${page.id}" data-token="${page.access_token}">${page.name}</option>`);
            });
        },
        error: function () { alert('Failed to load pages.'); }
    });

    $.ajax({
        url: '/meta/ad-accounts',
        method: 'GET',
        success: function (response) {
            const selector = $('#adAccountSelector');
            selector.empty().append(`<option value="">Select Ad Account</option>`);
            response.forEach(account => {
                selector.append(`<option value="${account.id}">${account.name || 'Ad Account'} (${account.id})</option>`);
            });
        },
        error: function () { alert('Failed to load ad accounts.'); }
    });
});
</script>
@endsection
