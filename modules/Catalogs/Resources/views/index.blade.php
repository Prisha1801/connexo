@extends('general.index', $setup)

@section('contenttop')
<div class="card-body">
    <div class="row align-items-center">
        <div class="col-12">
            <button type="button" class="btn btn-sm btn-primary rounded-lg px-4" id="syncCatalogIndexBtn">
                <i class="ni ni-refresh mr-2"></i> {{ __('Sync Catalog') }}
            </button>
            <a href="{{ route('catalog.productsCatalog') }}" class="btn btn-sm btn-light-primary rounded-lg px-4 ml-2">
                <i class="ni ni-credit-card mr-2"></i> {{ __('View Products') }}
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
                    <span class="badge badge-success">{{ __('Enabled') }}</span>
                @else
                    <span class="badge badge-danger">{{ __('Disabled') }}</span>
                @endif
            </td>
        </tr>
    @endforeach
@endsection

@section('js')
<style>.spin{animation:spin 1s linear infinite}@keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}</style>
<script>
    document.getElementById('syncCatalogIndexBtn')?.addEventListener('click', function() {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="ni ni-refresh spin mr-2"></i> {{ __('Syncing...') }}';
        fetch("{{ route('catalog.fetchCatalog') }}", {
            method: "POST",
            headers: { "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": "{{ csrf_token() }}" }
        }).then(r => r.json()).then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ni ni-refresh mr-2"></i> {{ __('Sync Catalog') }}';
            alert(data.message || (data.status === 'success' ? 'Synced successfully' : 'Error'));
            if (data.status === 'success') window.location.reload();
        }).catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="ni ni-refresh mr-2"></i> {{ __('Sync Catalog') }}';
            alert('{{ __("Sync failed") }}');
        });
    });
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