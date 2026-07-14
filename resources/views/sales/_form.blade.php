@php
    $sale = $sale ?? null;

    $existingItemsForJs = [];
    if ($sale) {
        foreach ($sale->items as $item) {
            $existingItemsForJs[] = [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'discount' => (float) $item->discount,
            ];
        }
    }

    $productsForJs = [];
    foreach ($products as $product) {
        $productsForJs[] = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->selling_price,
        ];
    }
@endphp

<div class="row">
    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Customer') }}</label>
        <select name="customer_id" id="customer_id" class="form-select form-select-solid" required>
            <option value="" disabled @selected(! old('customer_id', $sale?->customer_id))>{{ __('Select') }}</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $sale?->customer_id) == $customer->id)>{{ $customer->name }} ({{ $customer->mobile }})</option>
            @endforeach
        </select>
        @error('customer_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Sale Type') }}</label>
        <select name="sale_type" id="sale_type" class="form-select form-select-solid" required>
            <option value="cash" @selected(old('sale_type', $sale?->sale_type ?? 'cash') === 'cash')>{{ __('Cash') }}</option>
            <option value="installment" @selected(old('sale_type', $sale?->sale_type) === 'installment')>{{ __('Installment') }}</option>
        </select>
        @error('sale_type')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Sale Date') }}</label>
        <input type="date" name="sale_date" class="form-control form-control-solid" value="{{ old('sale_date', $sale?->sale_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
        @error('sale_date')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-6 installment-section">
    <label class="col-form-label required fw-bold fs-6">{{ __('Installment Month') }}</label>
    <input type="number" name="installment_month" id="installment_month" min="1" class="form-control form-control-solid" style="max-width: 200px;" value="{{ old('installment_month', $sale?->installmentPlan?->installment_month) }}">
    <div class="form-text">{{ __('Paid Amount below will be treated as the Down Payment; the remaining balance is split evenly across these months.') }}</div>
    @error('installment_month')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
</div>

<hr class="my-6">

<h5 class="fw-bold mb-4">{{ __('Products') }}</h5>

<div class="table-responsive mb-3">
    <table class="table table-bordered align-middle" id="itemsTable">
        <thead>
            <tr class="text-muted fs-7 text-uppercase">
                <th style="min-width: 220px;">{{ __('Product') }}</th>
                <th style="width: 100px;">{{ __('Qty') }}</th>
                <th style="width: 140px;">{{ __('Unit Price') }}</th>
                <th style="width: 120px;">{{ __('Discount') }}</th>
                <th style="width: 140px;" class="text-end">{{ __('Total') }}</th>
                <th style="width: 50px;"></th>
            </tr>
        </thead>
        <tbody id="itemsTableBody"></tbody>
    </table>
</div>
@error('items')
    <div class="text-danger fs-7 mb-3">{{ $message }}</div>
@enderror

<button type="button" class="btn btn-sm btn-light-primary mb-6" id="addItemRow">
    <i class="fas fa-plus me-1"></i> {{ __('Add Product') }}
</button>

<div class="row justify-content-end">
    <div class="col-md-5">
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('Subtotal') }}</label>
            <div class="col-6"><input type="text" id="subtotalDisplay" class="form-control form-control-solid text-end" readonly value="0.00"></div>
        </div>
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('Discount') }}</label>
            <div class="col-6"><input type="number" step="0.01" min="0" name="discount" id="discount" class="form-control form-control-solid text-end" value="{{ old('discount', $sale?->discount ?? 0) }}"></div>
        </div>
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('VAT') }}</label>
            <div class="col-6"><input type="number" step="0.01" min="0" name="vat" id="vat" class="form-control form-control-solid text-end" value="{{ old('vat', $sale?->vat ?? 0) }}"></div>
        </div>
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('Delivery Charge') }}</label>
            <div class="col-6"><input type="number" step="0.01" min="0" name="delivery_charge" id="delivery_charge" class="form-control form-control-solid text-end" value="{{ old('delivery_charge', $sale?->delivery_charge ?? 0) }}"></div>
        </div>
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('Grand Total') }}</label>
            <div class="col-6"><input type="text" id="grandTotalDisplay" class="form-control form-control-solid text-end fw-bold" readonly value="0.00"></div>
        </div>
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6" id="paidAmountLabel">{{ __('Paid Amount') }}</label>
            <div class="col-6"><input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" class="form-control form-control-solid text-end" value="{{ old('paid_amount', $sale?->paid_amount ?? 0) }}"></div>
        </div>
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('Due Amount') }}</label>
            <div class="col-6"><input type="text" id="dueAmountDisplay" class="form-control form-control-solid text-end" readonly value="0.00"></div>
        </div>

        @if ($sale)
            <div class="row mb-3">
                <label class="col-6 col-form-label fw-bold fs-6">{{ __('Status') }}</label>
                <div class="col-6">
                    <select name="status" id="status" class="form-select form-select-solid">
                        <option value="pending" @selected(old('status', $sale->status) === 'pending')>{{ __('Pending') }}</option>
                        <option value="completed" @selected(old('status', $sale->status) === 'completed')>{{ __('Completed') }}</option>
                        <option value="cancelled" @selected(old('status', $sale->status) === 'cancelled')>{{ __('Cancelled') }}</option>
                    </select>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const PRODUCTS = @json($productsForJs);
    const EXISTING_ITEMS = @json($existingItemsForJs);

    let rowIndex = 0;
    const $tbody = $('#itemsTableBody');

    function productOptions(selectedId) {
        let html = '<option value="" disabled' + (selectedId ? '' : ' selected') + '>{{ __('Select') }}</option>';
        PRODUCTS.forEach(function (p) {
            html += '<option value="' + p.id + '" data-price="' + p.price + '"' + (String(p.id) === String(selectedId) ? ' selected' : '') + '>' + p.name + '</option>';
        });
        return html;
    }

    function addItemRow(item) {
        const index = rowIndex++;
        const qty = item ? item.quantity : 1;
        const price = item ? item.unit_price : '';
        const discount = item ? item.discount : 0;

        const $row = $('<tr class="item-row"></tr>');
        $row.html(
            '<td><select name="items[' + index + '][product_id]" class="form-select form-select-solid item-product" required>' + productOptions(item?.product_id) + '</select></td>' +
            '<td><input type="number" name="items[' + index + '][quantity]" class="form-control item-qty" min="1" value="' + qty + '" required></td>' +
            '<td><input type="number" step="0.01" name="items[' + index + '][unit_price]" class="form-control item-price" min="0" value="' + price + '" required></td>' +
            '<td><input type="number" step="0.01" name="items[' + index + '][discount]" class="form-control item-discount" min="0" value="' + discount + '"></td>' +
            '<td class="item-total text-end fw-bold">0.00</td>' +
            '<td class="text-center"><button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-item"><i class="fas fa-trash"></i></button></td>'
        );

        $tbody.append($row);
        $row.find('.item-product').select2({ width: '100%', dropdownParent: $row.closest('.card, body') });

        recalculate();
    }

    $('#addItemRow').on('click', function () {
        addItemRow(null);
    });

    $tbody.on('click', '.btn-remove-item', function () {
        if ($tbody.find('tr').length <= 1) {
            toastr.error('{{ __('At least one product is required.') }}');
            return;
        }
        $(this).closest('tr').remove();
        recalculate();
    });

    $tbody.on('change', '.item-product', function () {
        const price = $(this).find('option:selected').data('price');
        $(this).closest('tr').find('.item-price').val(price ?? 0);
        recalculate();
    });

    $tbody.on('input', '.item-qty, .item-price, .item-discount', recalculate);
    $('#discount, #vat, #delivery_charge, #paid_amount').on('input', recalculate);

    function recalculate() {
        let subtotal = 0;

        $tbody.find('.item-row').each(function () {
            const qty = parseFloat($(this).find('.item-qty').val()) || 0;
            const price = parseFloat($(this).find('.item-price').val()) || 0;
            const discount = parseFloat($(this).find('.item-discount').val()) || 0;
            const total = Math.max((qty * price) - discount, 0);

            $(this).find('.item-total').text(total.toFixed(2));
            subtotal += total;
        });

        const discount = parseFloat($('#discount').val()) || 0;
        const vat = parseFloat($('#vat').val()) || 0;
        const deliveryCharge = parseFloat($('#delivery_charge').val()) || 0;
        const grandTotal = Math.max(subtotal - discount + vat + deliveryCharge, 0);
        const paidAmount = parseFloat($('#paid_amount').val()) || 0;
        const dueAmount = Math.max(grandTotal - paidAmount, 0);

        $('#subtotalDisplay').val(subtotal.toFixed(2));
        $('#grandTotalDisplay').val(grandTotal.toFixed(2));
        $('#dueAmountDisplay').val(dueAmount.toFixed(2));
    }

    function updateSaleTypeVisibility() {
        const isInstallment = $('#sale_type').val() === 'installment';
        $('.installment-section').toggle(isInstallment);
        $('#installment_month').prop('required', isInstallment);
        $('#paidAmountLabel').text(isInstallment ? '{{ __('Down Payment') }}' : '{{ __('Paid Amount') }}');
    }

    $('#customer_id').select2({ width: '100%' });
    $('#sale_type').select2({ width: '100%' });
    $('#status').select2({ width: '100%' });
    $('#sale_type').on('change', updateSaleTypeVisibility);
    updateSaleTypeVisibility();

    if (EXISTING_ITEMS.length) {
        EXISTING_ITEMS.forEach(function (item) { addItemRow(item); });
    } else {
        addItemRow(null);
    }
});
</script>
@endpush
