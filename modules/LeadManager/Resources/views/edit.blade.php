@extends('layouts.app', ['title' => __('Edit Lead')])

@section('title')
    <title>{{ __('Edit Lead') }}</title>
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
                        <span class="lb-icon"><i class="ni ni-single-02"></i></span>
                        {{ __('Edit Lead') }}
                    </h1>
                    <span class="lb-subtitle">
                        <span class="lb-pill primary">{{ $lead->contact->name ?? '—' }}</span>
                        <span class="lb-pill ml-2">{{ $lead->contact->phone ?? '—' }}</span>
                        <span class="lb-pill info ml-2">{{ $lead->source_label }}</span>
                    </span>
                </div>
                <div class="lb-toolbar">
                    <a href="{{ route('lead-manager.index') }}" class="lb-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back') }}
                    </a>
                    <form action="{{ route('lead-manager.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Delete this lead?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="lb-btn-danger">{{ __('Delete') }}</button>
                    </form>
                </div>
            </div>
        </header>

        <div class="lb-body">
            @if (session('success'))
                <div class="lb-flash">
                    <div class="alert alert-success mb-0">{{ session('success') }}</div>
                </div>
            @endif
            @if (session('error'))
                <div class="lb-flash">
                    <div class="alert alert-danger mb-0">{{ session('error') }}</div>
                </div>
            @endif

            <section class="lb-section">
                <div class="lb-section-hd">
                    <h3 class="lb-section-title">{{ __('Lead info') }}</h3>
                    <span class="text-muted small">{{ __('Contact: :name (:phone)', ['name' => $lead->contact->name ?? '—', 'phone' => $lead->contact->phone ?? '—']) }}</span>
                </div>
                <div class="lb-section-bd">
                    <form action="{{ route('lead-manager.update', $lead) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="stage" class="form-label">{{ __('Stage') }} <span class="text-danger">*</span></label>
                            <select class="form-control" id="stage" name="stage" required style="border-radius: 12px; height: 44px;">
                                @foreach ($stages ?? \Modules\LeadManager\Models\Lead::stages() as $k => $v)
                                    <option value="{{ $k }}" {{ old('stage', $lead->stage) == $k ? 'selected' : '' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                            @error('stage')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if (!$lead->isWon())
                        <div class="mb-3">
                            <label class="form-label">{{ __('Qualified') }}</label>
                            <div>
                                <label class="d-inline-flex align-items-center me-3">
                                    <input type="radio" name="qualified" value="1" {{ old('qualified', $lead->qualified) == 1 ? 'checked' : '' }} class="me-2">
                                    {{ __('Yes') }}
                                </label>
                                <label class="d-inline-flex align-items-center me-3">
                                    <input type="radio" name="qualified" value="0" {{ old('qualified', $lead->qualified) === 0 || old('qualified') === '0' ? 'checked' : ($lead->qualified === null ? 'checked' : '') }} class="me-2">
                                    {{ __('No / Pending') }}
                                </label>
                            </div>
                        </div>

                        <div class="mb-3" id="lost-reason-group" style="{{ $lead->stage === \Modules\LeadManager\Models\Lead::STAGE_LOST ? '' : 'display:none' }}">
                            <label for="lost_reason" class="form-label">{{ __('Lost reason') }}</label>
                            <input type="text" class="form-control" id="lost_reason" name="lost_reason" value="{{ old('lost_reason', $lead->lost_reason) }}" placeholder="{{ __('e.g. Not interested, Budget…') }}" style="border-radius: 12px; height: 44px;">
                            @error('lost_reason')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif

                        <div class="mb-3">
                            <label for="tags" class="form-label">{{ __('Tags') }}</label>
                            <input type="text" class="form-control" id="tags" name="tags" value="{{ old('tags', $lead->tags) }}" placeholder="{{ __('Comma separated tags') }}" style="border-radius: 12px; height: 44px;">
                        </div>

                        <div class="mb-3">
                            <label for="next_follow_up_at" class="form-label">{{ __('Next follow-up') }}</label>
                            <input type="datetime-local" class="form-control" id="next_follow_up_at" name="next_follow_up_at" value="{{ old('next_follow_up_at', $lead->next_follow_up_at ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '') }}" style="border-radius: 12px; height: 44px;">
                        </div>

                        <div class="mb-3">
                            <label for="user_id" class="form-label">{{ __('Assigned to') }}</label>
                            <select class="form-control" id="user_id" name="user_id" style="border-radius: 12px; height: 44px;">
                                <option value="">{{ __('Unassigned') }}</option>
                                @foreach ($agents ?? [] as $a)
                                    <option value="{{ $a->id }}" {{ old('user_id', $lead->user_id) == $a->id ? 'selected' : '' }}>{{ $a->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">{{ __('Notes') }}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" placeholder="{{ __('Internal notes…') }}" style="border-radius: 12px;">{{ old('notes', $lead->notes) }}</textarea>
                            @error('notes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('lead-manager.index') }}" class="lb-btn-soft mr-2">{{ __('Cancel') }}</a>
                            <button type="submit" class="lb-btn-primary">
                                <i class="ni ni-check-bold"></i> {{ __('Save') }}
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
    var stage = document.getElementById('stage');
    var lostReason = document.getElementById('lost-reason-group');
    var lostStage = '{{ \Modules\LeadManager\Models\Lead::STAGE_LOST }}';
    function toggle() {
        lostReason.style.display = stage.value === lostStage ? 'block' : 'none';
    }
    stage.addEventListener('change', toggle);
    toggle();
});
</script>
@endpush
