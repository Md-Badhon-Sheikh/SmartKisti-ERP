<!--begin::Table-->
<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="salesReportDatatable">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0" style="background: #fff;">
            <th class="min-w-20px  fw-bold text-dark">{{ __('#') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Invoice No') }}</th>
            <th class="min-w-90px  fw-bold text-dark">{{ __('Sale Date') }}</th>
            <th class="min-w-120px fw-bold text-dark">{{ __('Customer') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Customer Code') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Mobile') }}</th>
            <th class="min-w-100px fw-bold text-dark">{{ __('Area') }}</th>
            <th class="min-w-160px fw-bold text-dark">{{ __('Products') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Type') }}</th>
            <th class="min-w-90px  fw-bold text-dark">{{ __('Grand Total') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Paid') }}</th>
            <th class="min-w-80px  fw-bold text-dark">{{ __('Due') }}</th>
            <th class="min-w-90px  fw-bold text-dark">{{ __('Down Payment') }}</th>
            <th class="min-w-60px  fw-bold text-dark">{{ __('Status') }}</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold">
        <!-- DataTables will populate -->
    </tbody>
</table>

@push('scripts')
<script>
$(document).ready(function () {

    function formatAmount(value) {
        var number = parseFloat(value) || 0;
        return number.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function currentFilters() {
        return {
            category_id: $('#filterCategory').val(),
            sub_category_id: $('#filterSubCategory').val(),
            area_id: $('#filterArea').val(),
            sale_type: $('#filterSaleType').val(),
            installment_status: $('#filterInstallmentStatus').val(),
            start_date: $('#filterStartDate').val(),
            end_date: $('#filterEndDate').val(),
            search: $('#filterSearch').val()
        };
    }

    // ── Select2 (Category → Sub Category cascading, matches products form pattern) ──
    function subCategoryMatcher(params, data) {
        if (!data.element) return data;

        var categoryId = $('#filterCategory').val();
        var optCategoryId = $(data.element).data('category-id');

        if (categoryId && String(optCategoryId) !== String(categoryId)) {
            return null;
        }

        if ($.trim(params.term) === '') return data;

        if (data.text && data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
            return data;
        }

        return null;
    }

    $('#filterCategory').select2({ width: '100%' });
    $('#filterSubCategory').select2({ width: '100%', matcher: subCategoryMatcher });
    $('#filterArea').select2({ width: '100%' });
    $('#filterSaleType').select2({ width: '100%' });
    $('#filterInstallmentStatus').select2({ width: '100%' });

    $('#filterCategory').on('change', function () {
        $('#filterSubCategory').val('').trigger('change');
    });

    $('#filterSaleType').on('change', function () {
        var isInstallment = $(this).val() === 'installment';
        $('#filterInstallmentStatus').prop('disabled', !isInstallment).val('').trigger('change');
    });

    // ── DataTable ─────────────────────────────────────────────
    var table = $('#salesReportDatatable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('sales-report.datatable') }}",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, currentFilters());
            }
        },
        columns: [
            {
                data: null,
                name: 'serial',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'invoice_no', name: 'invoice_no' },
            { data: 'sale_date', name: 'sale_date' },
            { data: 'customer_name', name: 'customer.name', orderable: false, searchable: false },
            { data: 'customer_code', name: 'customer.customer_code', orderable: false, searchable: false },
            { data: 'mobile', name: 'customer.mobile', orderable: false, searchable: false },
            { data: 'area_name', name: 'customer.area.name', orderable: false, searchable: false },
            { data: 'products', name: 'products', orderable: false, searchable: false },
            { data: 'sale_type', name: 'sale_type' },
            { data: 'grand_total', name: 'grand_total' },
            { data: 'paid_amount', name: 'paid_amount' },
            { data: 'due_amount', name: 'due_amount' },
            { data: 'down_payment', name: 'down_payment', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false, searchable: false }
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
        },
        drawCallback: function (settings) {
            var summary = settings.json && settings.json.summary;

            if (!summary) return;

            $('#summaryTotalSales').text(formatAmount(summary.total_sales_amount));
            $('#summaryTotalPaid').text(formatAmount(summary.total_paid_amount));
            $('#summaryTotalDue').text(formatAmount(summary.total_due_amount));
            $('#summaryTotalDownPayment').text(formatAmount(summary.total_down_payment));
            $('#summaryTotalRecords').text(summary.total_records);
        }
    });

    $('#btnApplyFilters').on('click', function () {
        table.ajax.reload();
    });

    $('#filterSearch').on('keypress', function (e) {
        if (e.which === 13) {
            e.preventDefault();
            table.ajax.reload();
        }
    });

    $('#btnResetFilters').on('click', function () {
        $('#filterCategory, #filterSubCategory, #filterArea, #filterSaleType').val('').trigger('change');
        $('#filterInstallmentStatus').val('').prop('disabled', true).trigger('change');
        $('#filterStartDate, #filterEndDate, #filterSearch').val('');
        table.ajax.reload();
    });

    // ── Print / Export (open with the currently applied filters) ──
    $('#btnPrintReport').on('click', function () {
        window.open("{{ route('sales-report.print') }}?" + $.param(currentFilters()), '_blank');
    });

    $('#btnExportExcel').on('click', function () {
        window.location.href = "{{ route('sales-report.export') }}?" + $.param(currentFilters());
    });

});
</script>
@endpush
