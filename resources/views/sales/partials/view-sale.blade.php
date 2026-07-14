<div class="modal fade" id="saleViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Sale Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="saleViewModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.renderSaleViewModal = function (data) {
        const badge = (cls, label) => '<span class="badge ' + cls + '">' + label + '</span>';

        const row = (label, value) => value
            ? '<div class="row mb-4">'
              + '<label class="col-5 fw-bold text-muted">' + label + '</label>'
              + '<div class="col-7">' + value + '</div>'
              + '</div>'
            : '';

        const esc = (value) => $('<div>').text(value ?? '').html();
        const money = (value) => Number(value ?? 0).toFixed(2);

        const statusBadge = {
            completed: badge('badge-light-success', '{{ __('Completed') }}'),
            cancelled: badge('badge-light-danger', '{{ __('Cancelled') }}'),
            pending: badge('badge-light-warning', '{{ __('Pending') }}'),
        }[data.status] || data.status;

        const typeBadge = data.sale_type === 'installment'
            ? badge('badge-light-warning', '{{ __('Installment') }}')
            : badge('badge-light-success', '{{ __('Cash') }}');

        let itemsTable = '';
        if (data.items && data.items.length) {
            itemsTable = '<div class="table-responsive mb-4">'
                + '<table class="table table-sm table-bordered">'
                + '<thead><tr class="text-muted fs-7 text-uppercase">'
                + '<th>{{ __('Product') }}</th><th class="text-end">{{ __('Qty') }}</th>'
                + '<th class="text-end">{{ __('Unit Price') }}</th><th class="text-end">{{ __('Discount') }}</th>'
                + '<th class="text-end">{{ __('Total') }}</th></tr></thead><tbody>'
                + data.items.map(function (item) {
                    return '<tr>'
                        + '<td>' + esc(item.product_name) + '</td>'
                        + '<td class="text-end">' + item.quantity + '</td>'
                        + '<td class="text-end">' + money(item.unit_price) + '</td>'
                        + '<td class="text-end">' + money(item.discount) + '</td>'
                        + '<td class="text-end">' + money(item.total) + '</td>'
                        + '</tr>';
                }).join('')
                + '</tbody></table></div>';
        }

        let installmentLink = '';
        if (data.installment_plan_id) {
            installmentLink = '<div class="mb-4">'
                + '<a href="{{ url('installments') }}/' + data.installment_plan_id + '" class="btn btn-sm btn-light-primary">'
                + '<i class="fas fa-calendar-alt me-1"></i> {{ __('View Installment Plan') }}'
                + '</a></div>';
        }

        const html = ''
            + row('{{ __('Invoice No') }}', esc(data.invoice_no))
            + row('{{ __('Customer') }}', esc(data.customer_name) + (data.customer_mobile ? ' (' + esc(data.customer_mobile) + ')' : ''))
            + row('{{ __('Sale Date') }}', esc(data.sale_date))
            + row('{{ __('Sale Type') }}', typeBadge)
            + itemsTable
            + row('{{ __('Subtotal') }}', money(data.subtotal))
            + row('{{ __('Discount') }}', money(data.discount))
            + row('{{ __('VAT') }}', money(data.vat))
            + row('{{ __('Delivery Charge') }}', money(data.delivery_charge))
            + row('<strong>{{ __('Grand Total') }}</strong>', '<strong>' + money(data.grand_total) + '</strong>')
            + row('{{ __('Paid Amount') }}', money(data.paid_amount))
            + row('{{ __('Due Amount') }}', money(data.due_amount))
            + row('{{ __('Status') }}', statusBadge)
            + installmentLink
            + row('{{ __('Created By') }}', data.created_by ? esc(data.created_by) : '')
            + row('{{ __('Created At') }}', data.created_at ?? '—');

        $('#saleViewModalBody').html(html);
    };
</script>
@endpush
