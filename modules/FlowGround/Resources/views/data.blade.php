@extends('layouts.app', ['title' => __('Flow Data')])
@section('head')
@include('flow-ground::partials.ui')
<style>
    .fg-subtitle strong { font-weight: 700; }
    .fg-data-list { padding: var(--fg-pad); padding-top: 1rem; }
    .fg-submission {
        border: 1px solid var(--fg-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,.04);
        margin-bottom: 1rem;
    }
    .fg-submission:last-child { margin-bottom: 0; }
    .fg-submission-hd {
        padding: 0.9rem 1.1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--fg-border);
        cursor: pointer;
    }
    .fg-submission-hd .left { display: flex; align-items: center; gap: .75rem; min-width: 0; }
    .fg-submission-hd .idx {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(13, 148, 136, .10);
        color: var(--fg-primary-2);
        font-weight: 700;
        flex-shrink: 0;
    }
    .fg-submission-hd .meta { min-width: 0; }
    .fg-submission-hd .meta .t { font-weight: 600; color: var(--fg-text); font-size: .9rem; }
    .fg-submission-hd .meta .s { font-size: .78rem; color: var(--fg-muted); margin-top: .15rem; }
    .fg-submission-hd .right { color: #94a3b8; display: inline-flex; align-items: center; gap: .5rem; }
    .fg-submission-hd .chev { transition: transform .15s ease; }
    details[open] .fg-submission-hd .chev { transform: rotate(180deg); }

    .fg-kv { width: 100%; border-collapse: collapse; }
    .fg-kv th, .fg-kv td {
        padding: .75rem 1.1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: top;
        font-size: .875rem;
    }
    .fg-kv tr:last-child th, .fg-kv tr:last-child td { border-bottom: 0; }
    .fg-kv th {
        width: 260px;
        color: var(--fg-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        font-size: .7rem;
        background: #fff;
    }
    .fg-kv td { color: #111827; }
    .fg-kv .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
    .fg-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: #fff;
        border: 1px solid var(--fg-border);
        border-radius: 999px;
        padding: .25rem .6rem;
        font-size: .75rem;
        color: var(--fg-muted);
        font-weight: 600;
        white-space: nowrap;
    }
    .fg-long {
        display: block;
        white-space: pre-wrap;
        word-break: break-word;
        max-height: 240px;
        overflow: auto;
        border: 1px solid rgba(148, 163, 184, .25);
        border-radius: 12px;
        padding: .75rem .9rem;
        background: #0b1220;
        color: #e5e7eb;
    }
</style>
@section('content')
<div class="container-fluid fg-wrap fg-scope">
    <div class="fg-card">
        <header class="fg-page-header">
            <div class="fg-top">
                <div>
                    <h1 class="fg-title">
                        <span class="fg-icon"><i class="ni ni-chart-bar-32"></i></span>
                        {{ $flow->name }}
                    </h1>
                    @if($flow->meta_flow_id)
                        <span class="fg-subtitle">{{ __('Meta ID') }}: <span style="font-weight:800;">{{ $flow->meta_flow_id }}</span></span>
                    @else
                        <span class="fg-subtitle">{{ __('Latest submissions captured for this flow.') }}</span>
                    @endif
                </div>
                <div class="fg-toolbar">
                    <a href="{{ route('flow-ground.index') }}" class="fg-btn-soft">
                        <i class="ni ni-bold-left"></i> {{ __('Back to flows') }}
                    </a>
                </div>
            </div>
        </header>

        <div class="fg-body">
            <div class="fg-flash">@include('partials.flash')</div>

            @if(! $rows->count())
                <section class="fg-section">
                    <div class="fg-empty">
                        <div style="font-weight:900; color: var(--fg-text); font-size: 1.05rem; margin-bottom: .25rem;">
                            {{ __('No data yet for this flow.') }}
                        </div>
                        <div>{{ __('Submissions will appear here once this flow is used.') }}</div>
                    </div>
                </section>
            @else
                <section class="fg-section">
                    <div class="fg-section-hd">
                        <h3>{{ __('Latest submissions') }}</h3>
                        <span class="badge bg-light text-muted" style="border-radius:10px;">{{ $rows->count() }} {{ __('row(s)') }}</span>
                    </div>
                    <div class="fg-data-list">
                        @php
                            /**
                             * Normalize a payload into an array of key => value.
                             * - If payload JSON contains {payload: {...}} use that inner object.
                             */
                            $fgNormalizePayload = function ($payload) {
                                $data = [];
                                if (is_string($payload)) {
                                    $decoded = json_decode($payload, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $data = $decoded;
                                    } else {
                                        $data = ['payload' => $payload];
                                    }
                                } elseif (is_array($payload)) {
                                    $data = $payload;
                                } elseif (is_object($payload)) {
                                    $data = (array) $payload;
                                }
                                if (isset($data['payload']) && is_array($data['payload'])) {
                                    return $data['payload'];
                                }
                                return $data;
                            };

                            $fgPrettyKey = function ($k) {
                                $k = (string) $k;
                                $k = str_replace(['-', '_'], ' ', $k);
                                $k = preg_replace('/\s+/', ' ', $k);
                                $k = trim($k);
                                return $k === '' ? '-' : ucwords($k);
                            };

                            $fgRenderValue = function ($v) {
                                if ($v === null) return '-';
                                if (is_bool($v)) return $v ? 'true' : 'false';
                                if (is_scalar($v)) return (string) $v;
                                return json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            };
                        @endphp

                        @foreach($rows as $i => $row)
                            @php
                                $payloadRaw = $row->payload ?? null;
                                $payload = $fgNormalizePayload($payloadRaw);

                                // If payload is empty, fall back to showing the row fields (excluding huge blobs).
                                if (!is_array($payload) || !count($payload)) {
                                    $arr = (array) $row;
                                    unset($arr['payload'], $arr['response_json'], $arr['meta_flow_json'], $arr['meta_flow_error_details']);
                                    $payload = $arr;
                                }

                                // Remove noisy keys if present
                                $hideKeys = ['id', 'flow_ground_id', 'created_at', 'updated_at'];
                                foreach ($hideKeys as $hk) { if (array_key_exists($hk, $payload)) unset($payload[$hk]); }

                                $created = $row->created_at ?? null;
                                $status = $row->status ?? null;
                                $phone = $row->phone ?? null;
                                $metaId = $row->meta_flow_id ?? null;
                            @endphp

                            <details class="fg-submission" @if($i === 0) open @endif>
                                <summary class="fg-submission-hd">
                                    <div class="left">
                                        <span class="idx">{{ $i + 1 }}</span>
                                        <div class="meta">
                                            <div class="t">{{ __('Submission') }} #{{ $i + 1 }}</div>
                                            <div class="s">
                                                @if($created)
                                                    <span class="fg-pill"><i class="ni ni-time-alarm"></i> {{ $created }}</span>
                                                @endif
                                                @if($phone)
                                                    <span class="fg-pill"><i class="ni ni-mobile-button"></i> <span class="mono">{{ $phone }}</span></span>
                                                @endif
                                                @if($status)
                                                    <span class="fg-pill"><i class="ni ni-tag"></i> {{ (string) $status }}</span>
                                                @endif
                                                @if($metaId)
                                                    <span class="fg-pill"><i class="ni ni-send"></i> <span class="mono">{{ $metaId }}</span></span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="right">
                                        <span class="text-muted small">{{ __('View fields') }}</span>
                                        <i class="ni ni-bold-down chev"></i>
                                    </div>
                                </summary>

                                <div class="table-responsive">
                                    <table class="fg-kv">
                                        <tbody>
                                        @foreach($payload as $k => $v)
                                            @php
                                                $val = $fgRenderValue($v);
                                                $isLong = is_string($val) && mb_strlen($val) > 180;
                                            @endphp
                                            <tr>
                                                <th>{{ $fgPrettyKey($k) }}</th>
                                                <td>
                                                    @if($isLong)
                                                        <code class="fg-long">{{ $val }}</code>
                                                    @else
                                                        <span class="{{ is_string($v) && preg_match('/^[A-Za-z0-9_\-]{16,}$/', (string)$v) ? 'mono' : '' }}">{{ $val }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection