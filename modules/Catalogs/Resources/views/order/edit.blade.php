@extends('general.index', $setup)

@section('cardbody')
<div class="container-fluid">
    <div class="card shadow-sm border-0">
        <div class="card-header border-0 pt-6 bg-light-primary">
            <div class="card-title">
                <div class="d-flex align-items-center">
                    <div class="symbol symbol-45px symbol-circle me-4">
                        <span class="symbol-label bg-primary">
                            <i class="ki-duotone ki-geolocation fs-2 text-white"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                    </div>
                    <div>
                        <h3 class="fw-bolder mb-0">{{ __('Delivery Address') }}</h3>
                        <span class="text-muted fs-7">{{ __('Update shipping address for order') }} #{{ $order->reference_id ?? '' }}</span>
                    </div>
                </div>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('catalog.itemIndex', $order->id) }}" class="btn btn-sm btn-light">
                    <i class="ki-duotone ki-arrow-left fs-2 me-1"><span class="path1"></span><span class="path2"></span></i>
                    {{ __('Back to Order') }}
                </a>
            </div>
        </div>
        <div class="card-body py-6">
            <form method="post" action="{{ route('catalog.orderUpdate', $order->id) }}">
                @csrf
                <div class="row g-6">
                    <div class="col-12">
                        <label class="form-label fw-semibold required">{{ __('Full Address') }}</label>
                        <textarea name="address" rows="4" class="form-control form-control-solid" placeholder="{{ __('Enter full address') }}">{{ $order->address }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('State') }}</label>
                        <input type="text" name="state" class="form-control form-control-solid" placeholder="{{ __('State') }}" value="{{ $order->state }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('City') }}</label>
                        <input type="text" name="city" class="form-control form-control-solid" placeholder="{{ __('City') }}" value="{{ $order->city }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Pin Code') }}</label>
                        <input type="text" name="pin_code" class="form-control form-control-solid" placeholder="{{ __('Pin Code') }}" value="{{ $order->pin_code }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Building Name') }}</label>
                        <input type="text" name="building_name" class="form-control form-control-solid" placeholder="{{ __('Building Name') }}" value="{{ $order->building_name }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('House Number') }}</label>
                        <input type="text" name="house_number" class="form-control form-control-solid" placeholder="{{ __('House Number') }}" value="{{ $order->house_number }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Landmark / Area') }}</label>
                        <input type="text" name="landmark_area" class="form-control form-control-solid" placeholder="{{ __('Landmark Area') }}" value="{{ $order->landmark_area }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">{{ __('Tower Number') }}</label>
                        <input type="text" name="tower_number" class="form-control form-control-solid" placeholder="{{ __('Tower Number') }}" value="{{ $order->tower_number }}">
                    </div>
                </div>
                <div class="separator my-6"></div>
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-primary px-6">
                        <i class="ki-duotone ki-check fs-2 me-2"><span class="path1"></span><span class="path2"></span></i>
                        {{ __('Update Address') }}
                    </button>
                    <a href="{{ route('catalog.orderIndex') }}" class="btn btn-light px-6">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
