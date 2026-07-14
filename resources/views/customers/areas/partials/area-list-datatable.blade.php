<!--begin::Table-->
<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="areaListDatatable">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0" style="background: #fff;">
            <th class="min-w-20px  fw-bold text-dark">{{ __('#') }}</th>
            <th class="min-w-120px fw-bold text-dark">{{ __('Name') }}</th>
            <th class="min-w-60px  fw-bold text-dark">{{ __('Customers') }}</th>
            <th class="min-w-50px  fw-bold text-dark">{{ __('Status') }}</th>
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

    const BASE_URL = "{{ url('areas') }}";

    var table = $('#areaListDatatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url:  "{{ route('areas.datatable') }}",
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
            { data: 'name',                  name: 'name' },
            { data: 'customers_count_badge', name: 'customers_count', orderable: false, searchable: false },
            { data: 'status',                name: 'status',          orderable: false, searchable: false },
            { data: 'action',                name: 'action',          orderable: false, searchable: false, className: 'text-end' }
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

    // ── View ──────────────────────────────────────────────────
    $(document).on('click', '.btn-view', function (e) {
        e.preventDefault();

        const id = $(this).data('id');

        $.ajax({
            url:      BASE_URL + '/' + id,
            type:     'GET',
            dataType: 'json',

            beforeSend: function () {
                $('#areaViewModal').modal('show');
                $('#areaViewModalBody').html(
                    '<div class="d-flex justify-content-center py-5">' +
                    '<div class="spinner-border text-primary"></div>' +
                    '</div>'
                );
            },

            success: function (response) {
                if (!response?.data) {
                    toastr.error('{{ __('Invalid response from server.') }}');
                    return;
                }
                window.renderAreaViewModal(response.data);
            },

            error: function (xhr) {
                $('#areaViewModalBody').html(
                    '<div class="text-center text-danger py-5">' +
                    '<i class="fas fa-exclamation-circle fs-2 mb-3"></i>' +
                    '<p>{{ __('Failed to load data.') }}</p>' +
                    '</div>'
                );
            }
        });
    });

    // ── Edit ──────────────────────────────────────────────────
    $(document).on('click', '.btn-edit', function (e) {
        e.preventDefault();

        const id = $(this).data('id');

        $('#areaListDatatable tbody tr').removeClass('table-warning editing-row');
        $(this).closest('tr').addClass('table-warning editing-row');

        $.ajax({
            url:      BASE_URL + '/' + id,
            type:     'GET',
            dataType: 'json',

            success: function (response) {
                if (!response?.data) {
                    toastr.error('{{ __('Invalid response from server.') }}');
                    return;
                }
                window.openAreaEditModal(response.data);
            },

            error: function (xhr) {
                toastr.error(xhr.responseJSON?.message || '{{ __('Failed to fetch area data.') }}');
            }
        });
    });

    // ── Status toggle ─────────────────────────────────────────
    $(document).on('click', '.status-toggle', function () {
        const id      = $(this).data('id');
        const $toggle = $(this);

        $.ajax({
            url:  BASE_URL + '/' + id + '/toggle',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content') },

            success: function (res) {
                toastr.success(res.message || "{{ __('Status updated.') }}");
                $('#areaListDatatable').DataTable().ajax.reload(null, false);
            },

            error: function (xhr) {
                $toggle.prop('checked', !$toggle.prop('checked'));
                toastr.error(xhr.responseJSON?.message || "{{ __('Failed to update status.') }}");
            }
        });
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
                    toastr.success(response.message || '{{ __('Area deleted.') }}');
                    $('#areaListDatatable').DataTable().ajax.reload(null, false);
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
