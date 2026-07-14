<!--begin::Table-->
<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="deliveryListDatatable">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0" style="background: #fff;">
            <th class="min-w-20px  fw-bold text-dark">{{ __('#') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Reference') }}</th>
            <th class="min-w-120px fw-bold text-dark">{{ __('Customer') }}</th>
            <th class="min-w-90px  fw-bold text-dark">{{ __('Delivery Date') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Delivery Charge') }}</th>
            <th class="min-w-70px  fw-bold text-dark">{{ __('Status') }}</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold">
        <!-- DataTables will populate -->
    </tbody>
</table>

@push('scripts')
<script>
$(document).ready(function () {
    $('#deliveryListDatatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{{ route('deliveries.datatable') }}",
            type: 'GET'
        },
        columns: [
            {
                data:       null,
                name:       'serial',
                orderable:  false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'reference',        name: 'reference',        orderable: false, searchable: false },
            { data: 'customer_name',    name: 'customer_name',     orderable: false, searchable: false },
            { data: 'delivery_date',    name: 'delivery_date' },
            { data: 'delivery_charge',  name: 'delivery_charge' },
            { data: 'delivery_status',  name: 'delivery_status',  orderable: false, searchable: false }
        ],
        lengthMenu: [[10, 30, 50, -1], [10, 30, 50, "All"]],
        pageLength: 10,
        dom: "<'row'<'col-sm-4'l><'col-sm-4 d-flex justify-content-center'B><'col-sm-4'f>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        buttons: [
            { extend: 'colvis', columns: ':not(:first-child)' }
        ],
        language: {
            search: '<div class="input-group">' +
                    '<span class="input-group-text"><i class="fas fa-search"></i></span>' +
                    '_INPUT_' +
                    '</div>'
        },
        columnDefs: [
            { targets: '_all', searchable: true, orderable: true }
        ],
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: ''
            }
        }
    });
});
</script>
@endpush
