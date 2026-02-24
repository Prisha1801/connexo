@extends('general.index', $setup)

@section('contenttop')
<div class="card-body">
    <div class="row mb-3">
        <div class="col-12">
            @if(isset($setup['items']) && $setup['items']->isNotEmpty())
                @php
                    $totalProducts = 0;
                    $categoriesWithProducts = 0;
                    $emptyCategories = 0;

                    foreach ($setup['items'] as $category) {
                        $matchedProducts = $setup['products']->filter(function ($product) use ($category) {
                            if (!$category->retailer_id) return false;
                            $retailerIds = explode(',', $category->retailer_id);
                            return in_array($product->retailer_id, $retailerIds);
                        });
                        $productCount = $matchedProducts->count();
                        $totalProducts += $productCount;

                        if ($productCount > 0) {
                            $categoriesWithProducts++;
                        } else {
                            $emptyCategories++;
                        }
                    }
                @endphp
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-gradient-primary text-white mb-0 rounded-lg shadow-sm">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-white">{{ $setup['items']->total() }}</h2>
                                    <p class="mb-0">{{ __('Total Categories') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-success text-white mb-0 rounded-lg shadow-sm">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-white">{{ $categoriesWithProducts }}</h2>
                                    <p class="mb-0">{{ __('With Products') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-warning text-white mb-0 rounded-lg shadow-sm">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-white">{{ $emptyCategories }}</h2>
                                    <p class="mb-0">{{ __('Empty Categories') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-gradient-info text-white mb-0 rounded-lg shadow-sm">
                            <div class="card-body">
                                <div class="text-center">
                                    <h2 class="text-white">{{ $totalProducts }}</h2>
                                    <p class="mb-0">{{ __('Total Products') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('custom_table', true)

@section('thead')
    <th>{{ __('Category') }}</th>
    <th>{{ __('Products Count') }}</th>
    <th>{{ __('crud.actions') }}</th>
@endsection

@section('tbody')
    @if(isset($setup['items']) && $setup['items']->isNotEmpty())
        @foreach ($setup['items'] as $item)
            @php
                $matchedProducts = $setup['products']->filter(function ($product) use ($item) {
                    if (!$item->retailer_id) return false;
                    $retailerIds = explode(',', $item->retailer_id);
                    return in_array($product->retailer_id, $retailerIds);
                });
                $productCount = $matchedProducts->count();
            @endphp
            <tr>
                <td>
                    <div class="media align-items-center">
                        <div class="media-body">
                            <span class="name mb-0 text-sm font-weight-bold">{{ $item->name }}</span>
                        </div>
                    </div>
                </td>
                <td>
                    @if ($productCount > 0)
                        <span class="badge badge-success">{{ $productCount }} {{ __('Products') }}</span>
                    @else
                        <span class="badge badge-secondary">{{ __('No Products') }}</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('catalog.categoryEdit', $item->id) }}" 
                       class="btn btn-sm btn-outline-primary rounded-lg" 
                       data-toggle="tooltip" 
                       title="{{ __('Edit') }}">
                        <i class="ni ni-ruler-pencil"></i>
                    </a>
                    <button type="button"
                        class="btn btn-sm btn-outline-danger delete-category-btn rounded-lg"
                        data-category-id="{{ $item->id }}"
                        data-category-name="{{ $item->name }}"
                        data-toggle="tooltip"
                        title="{{ __('Delete') }}">
                        <i class="ni ni-fat-remove"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    @endif
@endsection

@section('js')
<script>
    $(document).ready(function() {
        // Delete category functionality
        $('.delete-category-btn').click(function() {
            var categoryId = $(this).data('category-id');
            var categoryName = $(this).data('category-name');
            
            if (confirm('{{ __("Are you sure you want to delete") }} "' + categoryName + '"?')) {
                $.ajax({
                    url: "{{ route('catalog.categoryDelete', ['id' => ':id']) }}".replace(':id', categoryId),
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function() {
                        alert('{{ __("Error deleting category") }}');
                    }
                });
            }
        });
    });
</script>
@endsection
