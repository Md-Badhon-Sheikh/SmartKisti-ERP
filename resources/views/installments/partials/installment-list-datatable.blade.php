<!--begin::Table-->
<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="installmentListDatatable">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0" style="background: #fff;">
            <th class="min-w-20px  fw-bold text-dark">{{ __('#') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Invoice No') }}</th>
            <th class="min-w-120px fw-bold text-dark">{{ __('Customer') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Product Total') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Monthly') }}</th>
            <th class="min-w-90px  fw-bold text-dark">{{ __('Next Payment') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Remaining Due') }}</th>
            <th class="min-w-60px  fw-bold text-dark">{{ __('Status') }}</th>
            <th class="text-end min-w-30px fw-bold text-dark">{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold">
        <!-- DataTables will populate -->
    </tbody>
</table>

@push('scripts')
<script>
$(document).ready(function () {
    $('#installmentListDatatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{{ route('installments.datatable') }}",
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
            { data: 'invoice_no',        name: 'sale.invoice_no', orderable: false, searchable: false },
            { data: 'customer_name',     name: 'customer.name',   orderable: false, searchable: false },
            { data: 'product_total',     name: 'product_total' },
            { data: 'monthly_amount',    name: 'monthly_amount' },
            { data: 'next_payment_date', name: 'next_payment_date' },
            { data: 'remaining_due',     name: 'remaining_due' },
            { data: 'status',            name: 'status',          orderable: false, searchable: false },
            { data: 'action',            name: 'action',          orderable: false, searchable: false, className: 'text-end' }
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
