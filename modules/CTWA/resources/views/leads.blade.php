@extends('layouts.app', ['title' => __('CTWA Leads by Meta ID')])

@section('content')
@include('ctwa::partials.styles')

<div class="container-fluid mt-5 pt-5 ctwa-wrap">
    <div class="ctwa-page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h1 class="mb-1">Leads by Meta ID</h1>
            <p class="ctwa-subtitle mb-0">CTWA leads grouped and filterable by ad source</p>
        </div>
        @include('ctwa::partials.nav')
    </div>

    <div class="ctwa-card">
        <div class="p-4">
            <form method="GET" action="{{ route('ctwa.leads') }}" class="d-flex flex-wrap gap-3 align-items-end mb-4">
                <div style="min-width: 200px;">
                    <label class="ctwa-form-label d-block">Filter by Meta Ad</label>
                    <select name="source_id" class="form-select ctwa-select w-100">
                        <option value="">All Meta IDs</option>
                        @foreach($adsForFilter as $ad)
                            <option value="{{ $ad->ad_id }}" {{ request('source_id') == $ad->ad_id ? 'selected' : '' }}>
                                {{ $ad->ad_name }} ({{ $ad->ad_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width: 240px;">
                    <label class="ctwa-form-label d-block">Search</label>
                    <input type="text" name="search" class="form-control ctwa-input" placeholder="Meta ID, Ad name, WA ID" value="{{ request('search') }}">
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn ctwa-btn-primary">
                        <i class="ni ni-zoom-split-in me-1"></i> Filter
                    </button>
                    <a href="{{ route('ctwa.leads') }}" class="btn ctwa-btn-soft">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table ctwa-table">
                    <thead>
                        <tr>
                            <th>Meta Ad ID</th>
                            <th>Ad Name</th>
                            <th>Contact</th>
                            <th>WA ID</th>
                            <th>Source URL</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            <tr>
                                <td><code class="bg-light px-2 py-1 rounded" style="font-size: 0.85rem;">{{ $lead->source_id ?? '—' }}</code></td>
                                <td>{{ $lead->ad_name ?? '—' }}</td>
                                <td>
                                    @if($lead->contact)
                                        <a href="{{ route('contacts.edit', ['contact' => $lead->contact_id]) }}" class="text-decoration-none">{{ $lead->contact->name ?? $lead->contact->phone ?? '—' }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ $lead->wa_id }}</small></td>
                                <td>
                                    @if($lead->source_url)
                                        <a href="{{ $lead->source_url }}" target="_blank" rel="noopener" class="text-truncate d-inline-block text-decoration-none" style="max-width: 140px;">{{ Str::limit($lead->source_url, 28) }}</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $lead->created_at ? $lead->created_at->format('M d, Y H:i') : '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="ctwa-empty">No leads found. Fetch ads and capture leads via CTWA.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 pt-3 border-top" style="border-color: var(--ctwa-border) !important;">
                {{ $leads->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>
@endsection
