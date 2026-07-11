<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="categories-table">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
            <th>{{ __('#') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Brand Required') }}</th>
            <th>{{ __('Products') }}</th>
            <th>{{ __('Status') }}</th>
            <th class="text-end">{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold"></tbody>
</table>

@push('scripts')
<script>
    $(function () {
        smartkistiDataTable('#categories-table', {
            ajax: '{{ route('categories.data') }}',
            columns: [
                {
                    data: null, name: 'serial', orderable: false, searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                { data: 'name', name: 'name' },
                { data: 'brand_required', name: 'brand_required' },
                { data: 'products_count', name: 'products_count', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
            ],
        });
    });
</script>
@endpush
