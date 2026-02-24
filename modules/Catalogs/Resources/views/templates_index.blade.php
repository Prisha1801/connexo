@extends('general.index', $setup)

@section('contenttop')
<div class="card-body">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div>
            <h5 class="mb-1">{{ __('Catalog Templates') }}</h5>
            <p class="mb-0 text-muted">{{ __('Manage WhatsApp catalog and carousel templates used for your orders.') }}</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('catalog.catalogsTemplatesCreate') }}" class="btn btn-sm btn-primary mr-2">
                <i class="ni ni-fat-add mr-1"></i> {{ __('New Template') }}
            </a>
            <a href="{{ route('catalog.carouselTemplatesCreate') }}" class="btn btn-sm btn-outline-primary mr-2">
                <i class="ni ni-image mr-1"></i> {{ __('Carousel Template') }}
            </a>
            <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank" class="btn btn-sm btn-light">
                <i class="ni ni-world mr-1"></i> {{ __('Open WhatsApp Manager') }}
            </a>
        </div>
    </div>
</div>
@endsection

@section('custom_table', true)

@section('thead')
    <th>{{ __('Name') }}</th>
    <th>{{ __('Category') }}</th>
    <th>{{ __('Status') }}</th>
    <th>{{ __('Last updated') }}</th>
    <th class="text-right">{{ __('crud.actions') }}</th>
@endsection

@section('tbody')
    @foreach ($setup['items'] as $template)
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <span class="badge badge-primary rounded-circle" style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                            <i class="ni ni-email-83"></i>
                        </span>
                    </div>
                    <div>
                        <div class="font-weight-bold">{{ $template->name ?? '-' }}</div>
                        @if(!empty($template->language))
                            <div class="text-muted small">{{ strtoupper($template->language) }}</div>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                <span class="badge badge-light">{{ $template->category ?? __('General') }}</span>
            </td>
            <td>
                @if(isset($template->status) && in_array($template->status, ['APPROVED','approved']))
                    <span class="badge badge-success">{{ __('Approved') }}</span>
                @elseif(isset($template->status) && in_array($template->status, ['REJECTED','rejected']))
                    <span class="badge badge-danger">{{ __('Rejected') }}</span>
                @else
                    <span class="badge badge-warning">{{ ucfirst($template->status ?? __('Pending')) }}</span>
                @endif
            </td>
            <td>
                <span class="text-muted small">
                    {{ optional($template->updated_at ?? $template->created_at)->format('d M Y, H:i') ?? '-' }}
                </span>
            </td>
            <td class="text-right">
                <a href="{{ route('catalog.catalogsTemplatesCreate', ['id' => $template->id ?? null]) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="ni ni-ruler-pencil"></i> {{ __('Edit') }}
                </a>
            </td>
        </tr>
    @endforeach
@endsection

@section('js')
@push('css')
<style>
    .table tbody tr { transition: background 0.2s; }
    .table tbody tr:hover { background-color: #f8fafc; }
</style>
@endpush
@endsection

