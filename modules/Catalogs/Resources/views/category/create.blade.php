@extends('general.index', ['title' => __('Categories')])

@section('content')
    <!--begin::Container-->
    <div class="container-xxl">
        <!--begin::Card-->
        <div class="card">
            <!--begin::Card header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-3 mb-1">{{ __('Create New Category') }}</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">{{ __('Organize products into categories') }}</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('catalog.categoryIndex') }}" class="btn btn-sm btn-light-primary">
                        <i class="ki-duotone ki-arrow-left fs-2 me-2">
                            <span class="path1"></span>
                            <span class="path2"></span>
                        </i>
                        {{ __('Back') }}
                    </a>
                </div>
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body py-4">
                <form method="post" autocomplete="off" enctype="multipart/form-data"
                    action="{{ route('catalog.categoryStore') }}">
                    @csrf
                    <div class="row g-5">
                        <!-- Category Name -->
                        <div class="col-md-6">
                            <div class="mb-10">
                                <label
                                    class="form-label fs-6 fw-semibold text-gray-700 required">{{ __('Category Name') }}</label>
                                <div class="input-group input-group-solid">
                                    <span class="input-group-text">
                                        <i class="ki-duotone ki-category fs-2 text-primary">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                        </i>
                                    </span>
                                    <input type="text" name="name" id="name"
                                        class="form-control form-control-solid" placeholder="Enter category name" required>
                                </div>
                            </div>
                        </div>

                        <!-- Products Selection -->
                        @if (count($products) > 0)
                            <div class="col-md-6">
                                <div class="mb-10">
                                    <label class="form-label fs-6 fw-semibold text-gray-700">{{ __('Products') }}</label>
                                    <select name="product[]" id="product" class="form-select form-select-solid" multiple
                                        data-control="select2" data-placeholder="Select products">
                                        @foreach ($products as $product)
                                            <option value="{{ $product->retailer_id }}">{{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="text-muted fs-7 mt-2">Hold CTRL to select multiple products</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-3 pt-10">
                        <button type="submit" class="btn btn-primary">
                            <i class="ki-duotone ki-check-square fs-2 me-2">
                                <span class="path1"></span>
                                <span class="path2"></span>
                            </i>
                            Save Category
                        </button>
                        <a href="{{ route('catalog.categoryIndex') }}" class="btn btn-light">
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
    <script>
        $(document).ready(function() {
            // Initialize select2
            $('#product').select2({
                placeholder: "Select products",
                width: '100%'
            });

            // Form validation can be added here if needed
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

    .input-group-text {
        background-color: #f8f9fa;
    }
</style>
