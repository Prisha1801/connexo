@extends('general.index', $setup)

@section('contenttop')
<div class="card-body">
    <div class="row align-items-center">
        <div class="col-12">
            <a href="{{ route('catalog.fetchCatalog') }}" class="btn btn-sm btn-primary rounded-lg px-4">
                <i class="ni ni-refresh mr-2"></i> {{ __('Sync Catalog') }}
            </a>
        </div>
    </div>
</div>
@endsection

@section('custom_table', true)

@section('thead')
    <th>{{ __('Name') }}</th>
    <th>{{ __('Catalogue Id') }}</th>
    <th>{{ __('Product Count') }}</th>
    <th>{{ __('Status') }}</th>
@endsection

@section('tbody')
    @foreach ($catalogs as $catalog)
        @php
            $products_count = App\Models\CatalogProduct::where('catalog_id', $catalog->catalog_id)
                ->where('company_id', $catalog->company_id)
                ->count();
        @endphp
        <tr>
            <td>{{ $catalog->name }}</td>
            <td>{{ $catalog->catalog_id }}</td>
            <td>{{ $products_count }}</td>
            <td>
                @if($catalog->status)
                    <span class="badge badge-danger">{{ __('Disabled') }}</span>
                @else
                    <span class="badge badge-success">{{ __('Enabled') }}</span>
                @endif
            </td>
        </tr>
    @endforeach
@endsection

@section('js')
<script>
    $(document).ready(function() {
        $('.select-catalog').change(function() {
            var url = "{{ route('catalog.index') }}";
            var catalog_id = $(this).val();
            updateURL(catalog_id);
            $.ajax({
                url: url,
                method: "get",
                data: { 'catalog_id': catalog_id },
                success: function(result) {
                    console.log(result);
                    $('.show-product').html(result);
                }
            });
        });
    });
    
    function updateURL(catalog_id = null) {
        var url = new URL(window.location.href);
        if (catalog_id) {
            url.searchParams.set('catalog_id', catalog_id);
        } else {
            url.searchParams.delete('catalog_id');
        }
        history.pushState(null, '', url.toString());
    }
</script>
@endsection