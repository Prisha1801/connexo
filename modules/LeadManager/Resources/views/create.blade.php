@extends('layouts.app', ['title' => __('Add New Lead')])

@section('title')
    <title>{{ __('Add New Lead') }}</title>
@endsection

@section('head')
    @include('work-flows::partials.ui')
    <style>
        .lm-add-form .form-control { border-radius: 12px; }
        .lm-add-form .form-label { font-weight: 600; color: #334155; margin-bottom: 0.4rem; }
        .lm-add-form .form-hint { font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem; }
    </style>
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-single-02"></i></span>
                        {{ __('Add New Lead') }}
                    </h1>
                    <span class="lb-subtitle">{{ __('Capture leads from CRM, Facebook, WhatsApp Campaign, Instagram & more.') }}</span>
                </div>
                <div class="lb-toolbar">
                    <a href="{{ route('lead-manager.index') }}" class="lb-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </header>

        <div class="lb-body">
            @if (session('error'))
                <div class="lb-flash">
                    <div class="alert alert-danger mb-0">{{ session('error') }}</div>
                </div>
            @endif

            <section class="lb-section lm-add-form">
                <div class="lb-section-bd p-4">
                    <form action="{{ route('lead-manager.store') }}" method="POST" id="add-lead-form">
                        @csrf

                        <div class="row">
                            {{-- Left column --}}
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="phone" class="form-label">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="{{ __('Enter phone number') }}" required style="height: 44px;">
                                    <div class="form-hint">{{ __('Country code will be automatically added.') }}</div>
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="source" class="form-label">{{ __('Source') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="source" name="source" required style="height: 44px;">
                                        <option value="">{{ __('Select or add a source') }}</option>
                                        @foreach ($sources ?? [] as $k => $v)
                                            <option value="{{ $k }}" {{ old('source') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                        @endforeach
                                        <option value="__custom__">{{ __('+ Add new source') }}</option>
                                    </select>
                                    <div class="form-hint mt-2" id="source-custom-wrap" style="display: none;">
                                        <input type="text" class="form-control" id="source_custom" placeholder="{{ __('Type to add a new source') }}" style="height: 38px;">
                                    </div>
                                    @error('source')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="user_id" class="form-label">{{ __('Assign Agent') }}</label>
                                    <select class="form-control" id="user_id" name="user_id" style="height: 44px;">
                                        <option value="">{{ __('Unassigned') }}</option>
                                        @foreach ($agents ?? [] as $a)
                                            <option value="{{ $a->id }}" {{ old('user_id') == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="groups" class="form-label">{{ __('Groups') }}</label>
                                    <select class="form-control" id="groups" name="groups[]" multiple style="min-height: 44px;">
                                        @foreach ($groups ?? [] as $g)
                                            <option value="{{ $g->id }}" {{ in_array($g->id, old('groups', [])) ? 'selected' : '' }}>{{ $g->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="form-hint">{{ __('Select groups') }}</div>
                                </div>
                            </div>

                            {{-- Right column --}}
                            <div class="col-lg-6">
                                <div class="mb-4">
                                    <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="{{ __('Enter client name') }}" required style="height: 44px;">
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="stage" class="form-label">{{ __('Stage') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="stage" name="stage" required style="height: 44px;">
                                        @foreach ($stages ?? [] as $k => $v)
                                            <option value="{{ $k }}" {{ old('stage', array_key_first($stages ?? [])) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <label for="tags" class="form-label">{{ __('Tags') }}</label>
                                    <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags') }}" placeholder="{{ __('Select or add tags (comma separated)') }}" style="height: 44px;">
                                </div>
                            </div>
                        </div>

                        {{-- Additional Information --}}
                        <div class="border-top pt-4 mt-4">
                            <h5 class="mb-3" style="font-weight: 600; color: #334155;">{{ __('Additional Information') }}</h5>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label">{{ __('Email') }}</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter email') }}" style="height: 44px;">
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="location" class="form-label">{{ __('Location') }}</label>
                                    <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" placeholder="{{ __('Enter location') }}" style="height: 44px;">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="form-label">{{ __('Notes') }}</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="{{ __('Optional notes…') }}" style="border-radius: 12px;">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 pt-3">
                            <a href="{{ route('lead-manager.index') }}" class="lb-btn-soft">× {{ __('Cancel') }}</a>
                            <button type="submit" class="lb-btn-primary">
                                <i class="ni ni-fat-add"></i> {{ __('Create Lead') }}
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var source = document.getElementById('source');
    var sourceCustom = document.getElementById('source_custom');
    var sourceCustomWrap = document.getElementById('source-custom-wrap');
    var form = document.getElementById('add-lead-form');

    source.addEventListener('change', function() {
        if (source.value === '__custom__') {
            sourceCustomWrap.style.display = 'block';
            source.removeAttribute('required');
        } else {
            sourceCustomWrap.style.display = 'none';
            source.setAttribute('required', 'required');
        }
    });
    if (source.value === '__custom__') sourceCustomWrap.style.display = 'block';

    form.addEventListener('submit', function(e) {
        if (source.value === '__custom__') {
            var val = sourceCustom.value.trim();
            if (!val) {
                e.preventDefault();
                alert('{{ __("Please enter a source name") }}');
                return false;
            }
            var hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'source';
            hidden.value = val;
            form.appendChild(hidden);
            source.removeAttribute('name');
        }
    });
});
</script>
@endpush
