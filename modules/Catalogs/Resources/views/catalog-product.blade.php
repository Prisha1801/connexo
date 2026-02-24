@extends('general.index', $setup)

@section('contenttop')
<div class="card-body">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <a href="javascript:void(0);" class="btn btn-sm btn-primary" id="syncCatalogBtn">
                <i class="ni ni-refresh"></i> {{ __('Sync Products') }}
            </a>
            <a href="{{ route('catalog.inventoryIndex') }}" class="btn btn-sm btn-light-warning ml-2">
                <i class="ni ni-box-2"></i> {{ __('Inventory') }}
            </a>
        </div>
    </div>
    
    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('catalog.productsCatalog') }}" id="filter-form" class="mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="ni ni-zoom-split-in"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control" 
                            placeholder="{{ __('Search products...') }}" value="{{ request('search') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                    <select name="category" class="form-control">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group mb-0">
                    <select name="stock_status" class="form-control">
                        <option value="">{{ __('All Stock Status') }}</option>
                        <option value="1" {{ request('stock_status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="0" {{ request('stock_status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block rounded-lg">
                    <i class="ni ni-funnel"></i> {{ __('Filter') }}
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('catalog.productsCatalog') }}" class="btn btn-outline-secondary btn-block rounded-lg">
                    <i class="ni ni-button-power"></i> {{ __('Reset') }}
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('custom_table', true)

@section('thead')
    <th>{{ __('Product') }}</th>
    <th>{{ __('Category') }}</th>
    <th>{{ __('Status') }}</th>
    <th>{{ __('Price') }}</th>
    <th>{{ __('crud.actions') }}</th>
@endsection

@section('tbody')
    @foreach ($products as $product)
        @php
            // Get all categories this product belongs to - Fixed SQL injection vulnerability
            $productCategories = Modules\Catalogs\Models\ProductCategory::where('company_id', $company_id)
                ->where(function($query) use ($product) {
                    if ($product->retailer_id) {
                        $query->where('retailer_id', $product->retailer_id)
                              ->orWhereRaw("FIND_IN_SET(?, retailer_id)", [$product->retailer_id]);
                    }
                })
                ->get();
        @endphp
        <tr>
            <td>
                <div class="media align-items-center">
                    <div class="media-left mr-3">
                        @if ($product->image_url)
                            <div class="rounded-circle overflow-hidden" style="width: 48px; height: 48px;">
                                <img alt="Product" src="{{ $product->image_url }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="ni ni-box-2 text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="media-body">
                        <span class="name mb-0 text-sm">{{ $product->product_name }}</span>
                        @if($product->description)
                            <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                        @endif
                        <br><small class="text-muted">ID: {{ $product->retailer_id }}</small>
                    </div>
                </div>
            </td>
            <td>
                @if ($productCategories->count() > 0)
                    @foreach ($productCategories as $category)
                        <span class="badge badge-primary">{{ $category->name }}</span>
                    @endforeach
                @else
                    <span class="badge badge-secondary">{{ __('Uncategorized') }}</span>
                @endif
            </td>
            <td>
                @if(isset($product->stock_status) && $product->stock_status == 0)
                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                @else
                    <span class="badge badge-success">{{ __('Active') }}</span>
                @endif
            </td>
            <td>
                <span class="text-dark font-weight-bold">{{ $product->price ?? 'N/A' }}</span>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-outline-info rounded-lg px-3 view-product-btn" 
                   data-product-id="{{ $product->id }}"
                   data-product-name="{{ $product->product_name }}"
                   data-product-description="{{ $product->description ?? '' }}"
                   data-product-price="{{ $product->price ?? 'N/A' }}"
                   data-product-image="{{ $product->image_url ?? '' }}"
                   data-product-stock="{{ isset($product->stock_status) && $product->stock_status == 0 ? __('Inactive') : __('Active') }}"
                   data-toggle="tooltip" title="{{ __('View') }}">
                    <i class="ni ni-zoom-split-in"></i> {{ __('View') }}
                </button>
                <a href="{{ route('catalog.productEdit', $product->id) }}" 
                   class="btn btn-sm btn-outline-primary rounded-lg px-3" 
                   data-toggle="tooltip" 
                   title="{{ __('Edit') }}">
                    <i class="ni ni-ruler-pencil"></i> {{ __('Edit') }}
                </a>
            </td>
        </tr>
    @endforeach
@endsection

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewProductModalLabel">{{ __('Product Details') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3" id="viewProductImageContainer">
                    <img id="viewProductImage" src="" alt="" class="img-fluid rounded" style="max-height: 200px;">
                </div>
                <table class="table table-sm">
                    <tr><th class="w-40">{{ __('Name') }}</th><td id="viewProductName"></td></tr>
                    <tr><th>{{ __('Description') }}</th><td id="viewProductDescription"></td></tr>
                    <tr><th>{{ __('Price') }}</th><td id="viewProductPrice" class="font-weight-bold"></td></tr>
                    <tr><th>{{ __('Stock Status') }}</th><td id="viewProductStock"></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

@section('js')
<script>
    // View Product Modal
    document.querySelectorAll('.view-product-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var img = document.getElementById('viewProductImage');
            var imgContainer = document.getElementById('viewProductImageContainer');
            img.src = this.dataset.productImage || '';
            img.alt = this.dataset.productName || '';
            imgContainer.style.display = img.src ? 'block' : 'none';
            document.getElementById('viewProductName').textContent = this.dataset.productName || '-';
            document.getElementById('viewProductDescription').textContent = this.dataset.productDescription || '-';
            document.getElementById('viewProductPrice').textContent = this.dataset.productPrice || 'N/A';
            document.getElementById('viewProductStock').innerHTML = '<span class="badge ' + (this.dataset.productStock === 'Inactive' ? 'badge-danger' : 'badge-success') + '">' + this.dataset.productStock + '</span>';
            $('#viewProductModal').modal('show');
        });
    });

    document.getElementById('syncCatalogBtn').addEventListener('click', function() {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="ni ni-refresh spin"></i> {{ __('Syncing...') }}';

        fetch("{{ route('catalog.fetchCatalog') }}", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="ni ni-refresh"></i> {{ __('Sync Products') }}';
                
                if (data.status === 'success') {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Error syncing products');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="ni ni-refresh"></i> {{ __('Sync Products') }}';
                alert('Something went wrong while syncing.');
            });
    });
</script>
<style>
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
