<!--begin::Table-->
<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="customOrderListDatatable">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0" style="background: #fff;">
            <th class="min-w-20px  fw-bold text-dark">{{ __('#') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Order No') }}</th>
            <th class="min-w-120px fw-bold text-dark">{{ __('Customer') }}</th>
            <th class="min-w-90px  fw-bold text-dark">{{ __('Order Date') }}</th>
            <th class="min-w-90px  fw-bold text-dark">{{ __('Delivery Date') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Type') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Estimated Price') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Remaining') }}</th>
            <th class="min-w-70px  fw-bold text-dark">{{ __('Status') }}</th>
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

    const BASE_URL = "{{ url('custom-orders') }}";

    $('#customOrderListDatatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{{ route('custom-orders.datatable') }}",
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
            { data: 'order_no',          name: 'order_no' },
            { data: 'customer_name',     name: 'customer.name',  orderable: false, searchable: false },
            { data: 'order_date',        name: 'order_date' },
            { data: 'delivery_date',     name: 'delivery_date' },
            { data: 'order_type',        name: 'order_type' },
            { data: 'estimated_price',   name: 'estimated_price' },
            { data: 'remaining_amount',  name: 'remaining_amount' },
            { data: 'status',            name: 'status',         orderable: false, searchable: false },
            { data: 'action',            name: 'action',         orderable: false, searchable: false, className: 'text-end' }
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

    // ── Delete (POST only) ────────────────────────────────────
    $(document).on('click', '.btn-delete', function (e) {
        e.preventDefault();

        const id = $(this).data('id');

        function performDelete() {
            $.ajax({
                url:  BASE_URL + '/' + id + '/delete',
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },

                success: function (response) {
                    toastr.success(response.message || '{{ __('Custom order deleted.') }}');
                    $('#customOrderListDatatable').DataTable().ajax.reload(null, false);
                },

                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || '{{ __('Delete failed.') }}');
                }
            });
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title:              "{{ __('Are you sure?') }}",
                text:               "{{ __('This action cannot be undone.') }}",
                icon:               'warning',
                showCancelButton:   true,
                confirmButtonText:  "{{ __('Yes, delete it!') }}",
                cancelButtonText:   "{{ __('Cancel') }}",
                confirmButtonColor: '#d33',
            }).then(result => {
                if (result.isConfirmed) performDelete();
            });
        } else {
            if (confirm('{{ __('Are you sure?') }}')) performDelete();
        }
    });

});
</script>
@endpush
