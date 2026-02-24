@extends('general.index', ['title' => __('Update Product Categories')])

@section('content')
<!--begin::Container-->
<div class="container-xxl">
    <!--begin::Card-->
    <div class="card">
        <!--begin::Card header-->
        <div class="card-header border-0 pt-5">
            <h3 class="card-title align-items-start flex-column">
                <span class="card-label fw-bold fs-3 mb-1">{{ __('Update Product Categories') }}</span>
                <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Assign product to multiple categories') }}</span>
            </h3>
            <div class="card-toolbar">
                <a href="{{route('catalog.productsCatalog')}}" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2 me-2">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Back to Products
                </a>
            </div>
        </div>
        <!--end::Card header-->

        <!--begin::Card body-->
        <div class="card-body py-4">
            <form id="restorant-apps-form" method="post" autocomplete="off" enctype="multipart/form-data" action="{{route('catalog.productUpdate')}}">
                @csrf
                <input type="hidden" name="check_id" value="{{$CatalogProduct->retailer_id}}">
                <input type="hidden" name="product_id" value="{{$CatalogProduct->id}}">

                <!-- Stock Status -->
                <div class="row mb-10">
                    <div class="col-md-8">
                        <label class="form-label fs-6 fw-semibold text-gray-700">{{ __('Stock Status') }}</label>
                        <div class="d-flex gap-4">
                            <label class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="stock_status" value="1" 
                                    {{ (!isset($CatalogProduct->stock_status) || $CatalogProduct->stock_status == 1) ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold text-gray-700">{{ __('Active') }}</span>
                            </label>
                            <label class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="radio" name="stock_status" value="0" 
                                    {{ isset($CatalogProduct->stock_status) && $CatalogProduct->stock_status == 0 ? 'checked' : '' }}>
                                <span class="form-check-label fw-semibold text-gray-700">{{ __('Inactive') }}</span>
                            </label>
                        </div>
                        <div class="text-muted fs-7 mt-2">{{ __('Inactive products will be hidden from customers') }}</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fs-6 fw-semibold text-gray-700">{{ __('Stock Quantity') }}</label>
                        <input type="number" name="stock_quantity" class="form-control form-control-solid" 
                            value="{{ $CatalogProduct->stock_quantity ?? '' }}" min="0" placeholder="{{ __('Optional') }}">
                    </div>
                </div>

                <!-- Multiple Category Selection -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-10">
                            <label class="form-label fs-6 fw-semibold text-gray-700 required">{{ __('Select Categories') }}</label>
                            <select name="categories[]" id="categories" class="form-select form-select-solid" 
                                    data-control="select2" data-placeholder="Select categories" multiple>
                                @foreach($productcategory as $category)
                                    @php
                                        $selectedRetailerIds = explode(',', $category->retailer_id);
                                        $isSelected = in_array($CatalogProduct->retailer_id, $selectedRetailerIds);
                                    @endphp
                                    <option value="{{ $category->id }}" 
                                        data-retailer_ids="{{ $category->retailer_id }}"
                                        {{ $isSelected ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="text-muted fs-7 mt-2">Hold CTRL or CMD to select multiple categories</div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="d-flex justify-content-end gap-3 pt-10">
                    <button type="submit" class="btn btn-primary">
                        <i class="ki-duotone ki-check-square fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Update Categories
                    </button>
                    <a href="{{route('catalog.productsCatalog')}}" class="btn btn-light">
                        <i class="ki-duotone ki-cross fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
        <!--end::Card body-->
    </div>
    <!--end::Card-->
</div>
<!--end::Container-->
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize select2 for multiple selection
        $('#categories').select2({
            placeholder: "Select categories",
            width: '100%',
            closeOnSelect: false
        });

        // Prepare retailer_ids data before form submission
        $('#restorant-apps-form').submit(function(e) {
            // Get all selected categories' retailer_ids
            const selectedRetailerIds = [];
            $('#categories option:selected').each(function() {
                const ids = $(this).data('retailer_ids').split(',');
                selectedRetailerIds.push(...ids);
            });
            
            // Add hidden field with unique retailer_ids
            $('<input>').attr({
                type: 'hidden',
                name: 'retailer_ids',
                value: [...new Set(selectedRetailerIds)].join(',')
            }).appendTo(this);
        });
    });
</script>
@endpush

<style>
    /* Custom styling for better visual appearance */
    .card {
        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.05);
        border: none;
    }
    
    .card-header {
        border-bottom: 1px solid #eff2f5;
    }
    
    .form-label.required:after {
        content: "*";
        color: #f1416c;
        margin-left: 4px;
    }
    
    /* Style for select2 multiple selections */
    .select2-selection--multiple {
        min-height: 42px;
        padding: 5px;
    }
</style>