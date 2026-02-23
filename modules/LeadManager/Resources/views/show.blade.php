@extends('layouts.app', ['title' => __('Lead View')])

@section('title')
    <title>{{ __('Lead View') }} – {{ $lead->contact->name ?? __('Lead') }}</title>
@endsection

@section('head')
    @include('work-flows::partials.ui')
    <style>
        .lv-card { background: #fff; border: 1px solid var(--lb-border); border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; margin-bottom: 1.5rem; }
        .lv-card-hd { padding: 1rem 1.25rem; border-bottom: 1px solid var(--lb-border); display: flex; align-items: center; gap: 0.75rem; font-weight: 600; font-size: 1rem; color: var(--lb-text); }
        .lv-card-hd .lv-icon { width: 36px; height: 36px; border-radius: 10px; background: var(--lb-soft); color: var(--lb-primary); display: flex; align-items: center; justify-content: center; }
        .lv-detail-row { display: flex; align-items: flex-start; justify-content: space-between; padding: 0.75rem 1.25rem; border-bottom: 1px solid #f1f5f9; }
        .lv-detail-row:last-child { border-bottom: none; }
        .lv-detail-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--lb-muted); margin-bottom: 0.25rem; display: flex; align-items: center; gap: 0.35rem; }
        .lv-detail-value { font-size: 0.95rem; font-weight: 500; color: var(--lb-text); }
        .lv-detail-value a { color: var(--lb-primary); text-decoration: none; }
        .lv-detail-value a:hover { text-decoration: underline; }
        .lv-tag { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.8rem; font-weight: 500; background: #d1fae5; color: #047857; }
        .lv-stage { display: inline-block; padding: 0.25rem 0.6rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; background: #dbeafe; color: #1d4ed8; }
        .lv-btn-edit { color: var(--lb-muted); padding: 0.25rem; border-radius: 8px; }
        .lv-btn-edit:hover { background: var(--lb-soft); color: var(--lb-primary); }
        .lv-btn-primary { background: linear-gradient(135deg, var(--lb-primary) 0%, var(--lb-primary-2) 100%); color: #fff; border: none; border-radius: 10px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.4rem; }
        .lv-btn-primary:hover { color: #fff; opacity: .97; }
        .lv-btn-soft { background: #fff; color: #334155; border: 1px solid var(--lb-border); border-radius: 10px; padding: 0.5rem 1rem; font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.4rem; }
        .lv-btn-soft:hover { background: #f8fafc; border-color: #cbd5e1; color: #0f172a; }
        .lv-note-item { padding: 0.75rem 1rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: var(--lb-text); }
        .lv-note-item:last-child { border-bottom: none; }
        .lv-note-meta { font-size: 0.75rem; color: var(--lb-muted); margin-bottom: 0.25rem; }
        .lv-schedule .form-label { font-size: 0.8rem; font-weight: 600; color: var(--lb-text); }
        .lv-schedule .form-control { border-radius: 10px; border: 1px solid var(--lb-border); }
        .lv-schedule .form-control:focus { border-color: var(--lb-primary); box-shadow: 0 0 0 3px var(--lb-soft); }
    </style>
@endsection

@section('content')
<div class="container-fluid lb-wrap lb-scope">
    <div class="row">
        <div class="col-lg-8">
            {{-- Lead Details --}}
            <div class="lv-card">
                <div class="lv-card-hd">
                    <span class="lv-icon"><i class="ni ni-single-02"></i></span>
                    {{ __('Lead Details') }}
                </div>
                <div class="d-flex flex-wrap justify-content-end gap-2 p-3 border-bottom" style="border-color: var(--lb-border) !important;">
                    <a href="{{ route('lead-manager.edit', $lead) }}" class="lv-btn-soft">
                        <i class="ni ni-ruler-pencil"></i> {{ __('Edit') }}
                    </a>
                    @if($nextLead)
                        <a href="{{ route('lead-manager.show', $nextLead) }}" class="lv-btn-primary">
                            {{ __('Next') }} <i class="ni ni-bold-right"></i>
                        </a>
                    @endif
                    @if($prevLead)
                        <a href="{{ route('lead-manager.show', $prevLead) }}" class="lv-btn-soft">
                            <i class="ni ni-bold-left"></i> {{ __('Back') }}
                        </a>
                    @else
                        <a href="{{ route('lead-manager.index') }}" class="lv-btn-soft">
                            <i class="ni ni-bold-left"></i> {{ __('Back') }}
                        </a>
                    @endif
                </div>
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label"><i class="ni ni-single-02" style="font-size: 0.85rem;"></i> {{ __('Name') }}</div>
                                <div class="lv-detail-value">{{ $lead->contact->name ?? '—' }}</div>
                            </div>
                            <a href="{{ route('lead-manager.edit', $lead) }}" class="lv-btn-edit" title="{{ __('Edit') }}"><i class="ni ni-ruler-pencil"></i></a>
                        </div>
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label"><i class="ni ni-mobile-button" style="font-size: 0.85rem;"></i> {{ __('Phone') }}</div>
                                <div class="lv-detail-value">{{ $lead->contact->phone ?? '—' }}</div>
                            </div>
                            <a href="{{ route('lead-manager.edit', $lead) }}" class="lv-btn-edit"><i class="ni ni-ruler-pencil"></i></a>
                        </div>
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label">{{ __('Source') }}</div>
                                <div class="lv-detail-value">{{ $lead->source_label }}</div>
                            </div>
                        </div>
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label">{{ __('Tags') }}</div>
                                <div class="lv-detail-value">
                                    @if($lead->tags)
                                        @foreach(array_map('trim', explode(',', $lead->tags)) as $tag)
                                            @if($tag)<span class="lv-tag">{{ $tag }}</span> @endif
                                        @endforeach
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('lead-manager.edit', $lead) }}" class="lv-btn-edit"><i class="ni ni-ruler-pencil"></i></a>
                        </div>
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label">{{ __('Groups') }}</div>
                                <div class="lv-detail-value">
                                    @if($lead->contact && $lead->contact->groups->isNotEmpty())
                                        {{ $lead->contact->groups->pluck('name')->join(', ') }}
                                    @else
                                        {{ __('No groups assigned') }}
                                    @endif
                                </div>
                            </div>
                            @if($lead->contact_id)
                                <a href="{{ route('contacts.edit', ['contact' => $lead->contact_id]) }}" class="lv-btn-edit"><i class="ni ni-ruler-pencil"></i></a>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label"><i class="ni ni-collection" style="font-size: 0.85rem;"></i> {{ __('Stage') }}</div>
                                <div class="lv-detail-value"><span class="lv-stage">{{ $lead->stage }}</span></div>
                            </div>
                            <a href="{{ route('lead-manager.edit', $lead) }}" class="lv-btn-edit"><i class="ni ni-ruler-pencil"></i></a>
                        </div>
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label"><i class="ni ni-single-02" style="font-size: 0.85rem;"></i> {{ __('Agent') }}</div>
                                <div class="lv-detail-value">{{ $lead->user->name ?? __('Unassigned') }}</div>
                            </div>
                            <a href="{{ route('lead-manager.edit', $lead) }}" class="lv-btn-edit"><i class="ni ni-ruler-pencil"></i></a>
                        </div>
                        <div class="lv-detail-row">
                            <div>
                                <div class="lv-detail-label"><i class="ni ni-calendar-grid-58" style="font-size: 0.85rem;"></i> {{ __('Next Follow-up') }}</div>
                                <div class="lv-detail-value">{{ $lead->next_follow_up_at ? $lead->next_follow_up_at->format('M d, Y H:i') : __('Not scheduled') }}</div>
                            </div>
                            <a href="{{ route('lead-manager.edit', $lead) }}" class="lv-btn-edit"><i class="ni ni-ruler-pencil"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="lv-card">
                <div class="lv-card-hd">
                    <span class="lv-icon"><i class="ni ni-notes"></i></span>
                    {{ __('Notes') }}
                </div>
                <div class="p-3">
                    @if (session('success'))
                        <div class="alert alert-success mb-3">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('lead-manager.notes.store', $lead) }}" method="POST" class="mb-4">
                        @csrf
                        <label class="form-label">{{ __('Add a note') }}</label>
                        <textarea name="body" class="form-control mb-2" rows="3" placeholder="{{ __('Type your note here...') }}" required style="border-radius: 10px; border: 1px solid var(--lb-border);"></textarea>
                        <button type="submit" class="lv-btn-primary">
                            <i class="ni ni-fat-add"></i> {{ __('Add Note') }}
                        </button>
                    </form>
                    <h6 class="text-muted mb-2">{{ __('Recent Notes') }}</h6>
                    @php
                        $noteChunks = $lead->notes ? array_filter(array_map('trim', explode("\n---\n", $lead->notes))) : [];
                        $noteChunks = array_reverse(array_slice($noteChunks, -20));
                    @endphp
                    @forelse($noteChunks as $chunk)
                        <div class="lv-note-item">
                            @if(preg_match('/^\[([^\]]+)\]\s*(.*)/s', $chunk, $m))
                                <div class="lv-note-meta">{{ $m[1] }}</div>
                                <div>{{ nl2br(e(trim($m[2]))) }}</div>
                            @else
                                <div>{{ nl2br(e($chunk)) }}</div>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted small mb-0">{{ __('No notes yet.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Schedule Follow-up --}}
            <div class="lv-card">
                <div class="lv-card-hd">
                    <span class="lv-icon"><i class="ni ni-calendar-grid-58"></i></span>
                    {{ __('Schedule Follow-up') }}
                </div>
                <div class="p-3 lv-schedule">
                    <form action="{{ route('lead-manager.update', $lead) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="stage" value="{{ $lead->stage }}">
                        <input type="hidden" name="user_id" value="{{ $lead->user_id ?? '' }}">
                        <input type="hidden" name="tags" value="{{ $lead->tags ?? '' }}">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Date and Time') }}</label>
                            <input type="datetime-local" name="next_follow_up_at" class="form-control" value="{{ $lead->next_follow_up_at ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '' }}" placeholder="dd-mm-yyyy --:--">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Notes') }} *</label>
                            <textarea name="schedule_note" class="form-control" rows="3" placeholder="{{ __('Reason or reminder for this follow-up...') }}"></textarea>
                        </div>
                        <button type="submit" class="lv-btn-primary w-100">
                            <i class="ni ni-calendar-grid-58"></i> {{ __('Schedule') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
