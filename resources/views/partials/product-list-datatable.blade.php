<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="products-table">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
            <th>{{ __('#') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Category') }}</th>
            <th>{{ __('Sub Category') }}</th>
            <th>{{ __('Brand') }} / {{ __('Manufacturer') }}</th>
            <th>{{ __('Type') }}</th>
            <th>{{ __('Selling Price') }}</th>
            <th>{{ __('Stock') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end">{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold"></tbody>
</table>

@push('scripts')
<script>
    $(function () {
        smartkistiDataTable('#products-table', {
            ajax: '{{ route('products.data') }}',
            columns: [
                {
                    data: null, name: 'serial', orderable: false, searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                { data: 'name', name: 'name' },
                { data: 'category', name: 'category.name' },
                { data: 'sub_category', name: 'subCategory.name' },
                { data: 'brand_manufacturer', name: 'brand.name', orderable: false, searchable: false },
                { data: 'type', name: 'product_type' },
                { data: 'selling_price', name: 'selling_price' },
                { data: 'stock', name: 'stock' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
            ],
        });
    });
</script>
@endpush
