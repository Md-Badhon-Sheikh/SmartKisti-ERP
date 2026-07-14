<!--begin::Table-->
<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="smsLogListDatatable">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0" style="background: #fff;">
            <th class="min-w-20px  fw-bold text-dark">{{ __('#') }}</th>
            <th class="min-w-120px fw-bold text-dark">{{ __('Customer') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Mobile') }}</th>
            <th class="min-w-70px  fw-bold text-dark">{{ __('Type') }}</th>
            <th class="min-w-200px fw-bold text-dark">{{ __('Message') }}</th>
            <th class="min-w-70px  fw-bold text-dark">{{ __('Status') }}</th>
            <th class="min-w-110px fw-bold text-dark">{{ __('Sent At') }}</th>
            <th class="min-w-110px fw-bold text-dark">{{ __('Created At') }}</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold">
        <!-- DataTables will populate -->
    </tbody>
</table>

@push('scripts')
<script>
$(document).ready(function () {
    $('#smsLogListDatatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{{ route('sms-logs.datatable') }}",
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
            { data: 'customer_name', name: 'customer.name', orderable: false, searchable: false },
            { data: 'mobile',        name: 'mobile' },
            { data: 'sms_type',      name: 'sms_type',       orderable: false, searchable: false },
            { data: 'message',       name: 'message' },
            { data: 'status',        name: 'status',         orderable: false, searchable: false },
            { data: 'sent_at',       name: 'sent_at' },
            { data: 'created_at',    name: 'created_at' }
        ],
        lengthMenu: [[10, 30, 50, -1], [10, 30, 50, "All"]],
        pageLength: 10,
        order: [[7, 'desc']],
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
