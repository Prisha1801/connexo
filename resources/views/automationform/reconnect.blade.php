    @extends('layouts.app', ['title' => __('Re-Connect')])
    
    @section('content')
    
    <!-- Page Header -->
    <div class="header pb-5 pt-5 pt-md-8">
        <div class="container-fluid">
            <div class="header-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h1 class="mb-2">🔄 {{ __('Facebook Re-Connect') }}</h1>
                        <p class="text-muted">Click the button below to re-authorize your Facebook account and update permissions.</p>
                    </div>
                    <div class="mb-3">
                        <a href="{{ route('automation.fb_automation') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reconnect Form Card -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-facebook text-primary" style="font-size: 4rem;"></i>
    
                        @if (!empty($fb_long_lived_token) && !empty($whatsapp_sender_id))
                            <span class="badge bg-success fs-6 mt-3">
                                ✅ Connected
                            </span>
                        @else
                            <span class="badge bg-danger fs-6 mt-3">
                                ❌ Not Connected
                            </span>
                        @endif
    
                        <h4 class="mt-4 mb-4">Re-connect your Facebook account</h4>
    
                        <form action="{{ route('automation.reconnect.form') }}" method="POST">
                            @csrf
    
                            <div class="mb-3 text-start">
                                <label for="client_id" class="form-label">Facebook App ID</label>
                                <input type="text" name="client_id" class="form-control" value="{{ old('client_id', $client_id) }}" required>
                            </div>
    
                            <div class="mb-3 text-start">
                                <label for="client_secret" class="form-label">Facebook App Secret</label>
                                <input type="text" name="client_secret" class="form-control" value="{{ old('client_secret', $client_secret) }}" required>
                            </div>
    
                            <!--<div class="mb-3 text-start">-->
                            <!--    <label for="fb_exchange_token" class="form-label">Permanent access token</label>-->
                            <!--    <input type="text" name="fb_exchange_token" class="form-control" value="{{ old('fb_long_lived_token', $fb_long_lived_token) }}" required>-->
                            <!--</div>-->
    
                            <div class="mb-4 text-start">
                                <label for="whatsapp_sender_id" class="form-label">WhatsApp Phone Number ID</label>
                                <input type="text" name="whatsapp_sender_id" class="form-control" value="{{ old('whatsapp_sender_id', $whatsapp_sender_id) }}" required>
                            </div>
                            <div class="mb-3 text-start">
                                <label for="fb_exchange_token" class="form-label">Permanent access token</label>
                                <input type="text" name="fb_exchange_token" class="form-control" value="{{ old('fb_long_lived_token', $fb_long_lived_token) }}" required>
                            </div>
    
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-box-arrow-up-right me-2"></i> Re-Connect with Facebook
                            </button>
                        </form>
                        @if ($ctwa_webhook_url)
                            <hr>
                            <h4>Webhook Details</h4>
                            <p><strong>Webhook URL:</strong> <code>{{ $ctwa_webhook_url }}</code></p>
                            <p><strong>Webhook Token:</strong> <code>{{ $ctwa_webhook_token }}</code></p>
                        @endif
    
                        @if (session('success'))
                            <div class="alert alert-success mt-4">{{ session('success') }}</div>
                        @endif
    
                        @if ($errors->any())
                            <div class="alert alert-danger mt-4 text-start">
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @endsection