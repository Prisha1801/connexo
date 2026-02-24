@extends('general.index', $setup)

@section('contenttop')
<div class="card-body">
    <div class="row mb-3">
        <div class="col-12">
            <a href="javascript:void(0);" class="btn btn-sm btn-primary" id="syncCatalogBtn">
                <i class="ni ni-refresh"></i> {{ __('Sync Products') }}
            </a>
        </div>
    </div>
    
    <!-- Search and Filter Form -->
    <form method="GET" action="{{ route('catalog.productsCatalog') }}" id="filter-form" class="mb-3">
        <div class="row g-3">
            <div class="col-md-4">
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
            <div class="col-md-4">
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
                    @if ($product->image_url)
                        <a href="#" class="avatar rounded-circle mr-3">
                            <img alt="Product" src="{{ $product->image_url }}">
                        </a>
                    @endif
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
                <span class="badge badge-success">{{ __('Active') }}</span>
            </td>
            <td>
                <span class="text-dark font-weight-bold">{{ $product->price ?? 'N/A' }}</span>
            </td>
            <td>
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

@section('js')
<script>
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
