@extends('general.index', $setup)

@section('contenttop')
<div class="card-body">
    <form method="GET" action="{{ route('catalog.inventoryIndex') }}" class="mb-3">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="ni ni-zoom-split-in"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control" 
                        placeholder="{{ __('Search products...') }}" value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="stock_status" class="form-control">
                    <option value="">{{ __('All Stock Status') }}</option>
                    <option value="1" {{ request('stock_status') === '1' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="0" {{ request('stock_status') === '0' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-block"><i class="ni ni-funnel"></i> {{ __('Filter') }}</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('catalog.inventoryIndex') }}" class="btn btn-outline-secondary btn-block">{{ __('Reset') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection

@section('custom_table', true)

@section('thead')
    <th>{{ __('Product') }}</th>
    <th>{{ __('Stock Status') }}</th>
    <th>{{ __('Quantity') }}</th>
    <th>{{ __('Price') }}</th>
    <th>{{ __('crud.actions') }}</th>
@endsection

@section('tbody')
    @foreach ($products as $product)
        <tr>
            <td>
                <div class="media align-items-center">
                    @if ($product->image_url)
                        <a href="#" class="avatar rounded-circle mr-3">
                            <img alt="Product" src="{{ $product->image_url }}" style="width:40px;height:40px;object-fit:cover;">
                        </a>
                    @endif
                    <div class="media-body">
                        <span class="name mb-0 text-sm font-weight-bold">{{ $product->product_name }}</span>
                        <br><small class="text-muted">ID: {{ $product->retailer_id }}</small>
                    </div>
                </div>
            </td>
            <td>
                @if(isset($product->stock_status) && $product->stock_status == 0)
                    <span class="badge badge-danger">{{ __('Inactive') }}</span>
                @else
                    <span class="badge badge-success">{{ __('Active') }}</span>
                @endif
            </td>
            <td>
                <span class="font-weight-bold">{{ $product->stock_quantity ?? '-' }}</span>
            </td>
            <td>{{ $product->price ?? 'N/A' }}</td>
            <td>
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#adjustModal" 
                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}"
                        data-current-qty="{{ $product->stock_quantity ?? 0 }}" data-type="in">
                        <i class="ni ni-fat-add"></i> {{ __('Stock In') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#adjustModal"
                        data-product-id="{{ $product->id }}" data-product-name="{{ $product->product_name }}"
                        data-current-qty="{{ $product->stock_quantity ?? 0 }}" data-type="out">
                        <i class="ni ni-fat-delete"></i> {{ __('Stock Out') }}
                    </button>
                </div>
                <a href="{{ route('catalog.productEdit', $product->id) }}" class="btn btn-sm btn-outline-primary ml-1">
                    <i class="ni ni-ruler-pencil"></i>
                </a>
            </td>
        </tr>
    @endforeach
@endsection

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('catalog.inventoryAdjust') }}">
            @csrf
            <input type="hidden" name="product_id" id="adj_product_id">
            <input type="hidden" name="type" id="adj_type">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adjustModalTitle">{{ __('Adjust Stock') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>{{ __('Product') }}:</strong> <span id="adj_product_name"></span></p>
                    <p><strong>{{ __('Current Quantity') }}:</strong> <span id="adj_current_qty"></span></p>
                    <div class="form-group">
                        <label>{{ __('Adjustment Quantity') }}</label>
                        <input type="number" name="adjustment" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('js')
<script>
    $('#adjustModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var productId = button.data('product-id');
        var productName = button.data('product-name');
        var currentQty = button.data('current-qty');
        var type = button.data('type');
        var modal = $(this);
        modal.find('#adj_product_id').val(productId);
        modal.find('#adj_type').val(type);
        modal.find('#adj_product_name').text(productName);
        modal.find('#adj_current_qty').text(currentQty);
        modal.find('#adjustModalTitle').text(type === 'in' ? '{{ __('Stock In') }}' : '{{ __('Stock Out') }}');
    });
</script>
@endsection
