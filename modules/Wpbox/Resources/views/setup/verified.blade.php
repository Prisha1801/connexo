<div class="card shadow max-height-vh-70 overflow-auto overflow-x-hidden">
    <div class="card-header shadow-lg">
        <b>{{ __('Whatsapp Cloud API - Connection Status') }}</b>
    </div>
    <div class="card-body overflow-auto overflow-x-hidden scrollable-div"  >
        
        @if ($company->getConfig('whatsapp_webhook_verified','no')!='yes' || $company->getConfig('whatsapp_settings_done','no')!='yes')
            <div class="alert alert-danger" role="alert">
                <strong>{{ __('Not connected!') }}</strong> {{ __('Please complete all the steps in order to connect to Whatsapp Cloud API')}}
            </div>
            <button onclick="location.reload()" class="btn btn-outline-success" type="button">{{ __('Refresh status')}}</button>

        @else  
            <div class="alert alert-success" role="alert">
                <strong>{{ __('Success!')}}</strong> {{ __('You are now connected to Whatsapp Cloud API. You can start use the system') }}
            </div> 
                
        @endif
        
        
        @php
    $whatsappNumber = \App\Models\Config::where('model_id', auth()->id())
        ->where('key', 'whatsapp_number')
        ->value('value');
@endphp

    
        <div class="container">
            <h3>Set WhatsApp Number</h3>
        
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
        
            <form method="POST" action="{{ route('configs.whatsapp.store') }}">
                @csrf
                <div class="mb-3 text-start">
                    <label for="whatsapp_number" class="form-label">WhatsApp Number</label>
                    <input type="text" 
                           name="whatsapp_number" 
                           id="whatsapp_number" 
                           class="form-control @error('whatsapp_number') is-invalid @enderror" 
                           value="{{ old('whatsapp_number', $whatsappNumber) }}" 
                           placeholder="+919876543210" 
                           required>
                    <small class="form-text text-muted">
                        Enter in <strong>E.164 format</strong> (e.g. <code>+14155552671</code> for US, <code>+919876543210</code> for India).
                    </small>
                    @error('whatsapp_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
        
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>



        
    </div>
</div>