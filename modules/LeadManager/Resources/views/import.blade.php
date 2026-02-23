@extends('layouts.app', ['title' => __('Import Leads')])

@section('title')
    <title>{{ __('Import Leads') }}</title>
@endsection

@section('head')
    @include('work-flows::partials.ui')
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="lb-card">
        <header class="lb-page-header">
            <div class="lb-top">
                <div>
                    <h1 class="lb-title">
                        <span class="lb-icon"><i class="ni ni-archive-2"></i></span>
                        {{ __('Import Leads from CSV') }}
                    </h1>
                    <span class="lb-subtitle">{{ __('Upload a CSV file with columns: name, phone, email (phone required). Duplicates by phone are skipped.') }}</span>
                </div>
                <div class="lb-toolbar">
                    <a href="{{ route('lead-manager.index') }}" class="lb-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back') }}
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

            <section class="lb-section">
                <div class="lb-section-hd">
                    <h3 class="lb-section-title">{{ __('Upload CSV') }}</h3>
                </div>
                <div class="lb-section-bd">
                    <form action="{{ route('lead-manager.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">{{ __('CSV file') }} <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="file" name="file" accept=".csv,.txt" required style="border-radius: 12px;">
                            <div class="text-muted small mt-2">{{ __('Max 2MB. Supported columns: name, phone, mobile, email. Phone/mobile column is required.') }}</div>
                            @error('file')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="source" class="form-label">{{ __('Lead source') }} <span class="text-danger">*</span></label>
                            <select class="form-control" id="source" name="source" required style="border-radius: 12px; height: 44px;">
                                <option value="">{{ __('-- Select source --') }}</option>
                                @foreach (\Modules\LeadManager\Models\Lead::sources() as $k => $v)
                                    <option value="{{ $k }}" {{ old('source') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            <div class="text-muted small mt-2">{{ __('All imported leads will be marked with this source.') }}</div>
                        </div>
                        <div class="mb-4">
                            <label for="stage" class="form-label">{{ __('Stage') }} <span class="text-danger">*</span></label>
                            <select class="form-control" id="stage" name="stage" required style="border-radius: 12px; height: 44px;">
                                @foreach ($stages ?? [] as $k => $v)
                                    <option value="{{ $k }}" {{ old('stage') == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            @error('stage')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('lead-manager.index') }}" class="lb-btn-soft">{{ __('Cancel') }}</a>
                            <button type="submit" class="lb-btn-primary">
                                <i class="ni ni-cloud-upload-94"></i> {{ __('Import') }}
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
