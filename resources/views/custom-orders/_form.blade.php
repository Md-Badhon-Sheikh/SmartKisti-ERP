@php
    $order = $order ?? null;

    $existingItemsForJs = [];
    if ($order) {
        foreach ($order->items as $item) {
            $existingItemsForJs[] = [
                'product_type' => $item->product_type,
                'wood_type' => $item->wood_type,
                'size' => $item->size,
                'color' => $item->color,
                'glass_type' => $item->glass_type,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'description' => $item->description,
                'design_image_url' => $item->design_image ? asset($item->design_image) : null,
                'design_image_path' => $item->design_image,
            ];
        }
    }

    $woodTypesForJs = [];
    foreach ($woodTypes as $woodType) {
        $woodTypesForJs[] = ['code' => $woodType['code'], 'name' => app()->getLocale() === 'bn' ? $woodType['bn_name'] : $woodType['en_name']];
    }

    $colorsForJs = [];
    foreach ($colors as $color) {
        $colorsForJs[] = ['code' => $color['code'], 'name' => app()->getLocale() === 'bn' ? $color['bn_name'] : $color['en_name']];
    }

    $glassTypesForJs = [];
    foreach ($glassTypes as $glassType) {
        $glassTypesForJs[] = ['code' => $glassType['code'], 'name' => app()->getLocale() === 'bn' ? $glassType['bn_name'] : $glassType['en_name']];
    }
@endphp

<div class="row">
    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Customer') }}</label>
        <select name="customer_id" id="customer_id" class="form-select form-select-solid" required>
            <option value="" disabled @selected(! old('customer_id', $order?->customer_id))>{{ __('Select') }}</option>
            @foreach ($customers as $customer)
                <option value="{{ $customer->id }}" @selected(old('customer_id', $order?->customer_id) == $customer->id)>{{ $customer->name }} ({{ $customer->mobile }})</option>
            @endforeach
        </select>
        @error('customer_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Order Type') }}</label>
        <select name="order_type" id="order_type" class="form-select form-select-solid" required>
            <option value="cash" @selected(old('order_type', $order?->order_type ?? 'cash') === 'cash')>{{ __('Cash') }}</option>
            <option value="installment" @selected(old('order_type', $order?->order_type) === 'installment')>{{ __('Installment') }}</option>
        </select>
        @error('order_type')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Order Date') }}</label>
        <input type="date" name="order_date" class="form-control form-control-solid" value="{{ old('order_date', $order?->order_date?->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
        @error('order_date')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Expected Delivery Date') }}</label>
        <input type="date" name="delivery_date" class="form-control form-control-solid" value="{{ old('delivery_date', $order?->delivery_date?->format('Y-m-d')) }}">
        @error('delivery_date')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Advance Amount') }}</label>
        <input type="number" step="0.01" min="0" name="advance_amount" id="advance_amount" class="form-control form-control-solid" value="{{ old('advance_amount', $order?->advance_amount ?? 0) }}">
        @error('advance_amount')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="mb-6">
    <label class="col-form-label fw-bold fs-6">{{ __('Remarks') }}</label>
    <textarea name="remarks" class="form-control form-control-solid" rows="2">{{ old('remarks', $order?->remarks) }}</textarea>
</div>

<hr class="my-6">

<h5 class="fw-bold mb-4">{{ __('Custom Furniture Items') }}</h5>

<div id="itemsContainer"></div>
@error('items')
    <div class="text-danger fs-7 mb-3">{{ $message }}</div>
@enderror

<button type="button" class="btn btn-sm btn-light-primary mb-6" id="addItemRow">
    <i class="fas fa-plus me-1"></i> {{ __('Add Item') }}
</button>

<div class="row justify-content-end">
    <div class="col-md-5">
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('Estimated Price') }}</label>
            <div class="col-6"><input type="text" id="estimatedPriceDisplay" class="form-control form-control-solid text-end" readonly value="0.00"></div>
        </div>
        <div class="row mb-3">
            <label class="col-6 col-form-label fw-bold fs-6">{{ __('Remaining Amount') }}</label>
            <div class="col-6"><input type="text" id="remainingAmountDisplay" class="form-control form-control-solid text-end" readonly value="0.00"></div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const WOOD_TYPES = @json($woodTypesForJs);
    const COLORS = @json($colorsForJs);
    const GLASS_TYPES = @json($glassTypesForJs);
    const EXISTING_ITEMS = @json($existingItemsForJs);

    let rowIndex = 0;
    const $container = $('#itemsContainer');

    function options(list, selected, placeholder) {
        let html = '<option value="">' + placeholder + '</option>';
        list.forEach(function (o) {
            html += '<option value="' + o.code + '"' + (o.code === selected ? ' selected' : '') + '>' + o.name + '</option>';
        });
        return html;
    }

    function addItemRow(item) {
        const index = rowIndex++;
        item = item || {};

        const $card = $('<div class="card mb-4 item-row"></div>');
        $card.html(
            '<div class="card-body">' +
            '<div class="row">' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Product Type') }}</label>' +
                    '<input type="text" name="items[' + index + '][product_type]" class="form-control item-product-type" placeholder="{{ __('e.g. Bed, Wardrobe') }}" value="' + (item.product_type || '') + '" required>' +
                '</div>' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Wood Type') }}</label>' +
                    '<select name="items[' + index + '][wood_type]" class="form-select item-select-wood">' + options(WOOD_TYPES, item.wood_type, '{{ __('Select') }}') + '</select>' +
                '</div>' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Size') }}</label>' +
                    '<input type="text" name="items[' + index + '][size]" class="form-control" placeholder="{{ __('e.g. 7 Feet') }}" value="' + (item.size || '') + '">' +
                '</div>' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Color') }}</label>' +
                    '<select name="items[' + index + '][color]" class="form-select item-select-color">' + options(COLORS, item.color, '{{ __('Select') }}') + '</select>' +
                '</div>' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Glass Type') }}</label>' +
                    '<select name="items[' + index + '][glass_type]" class="form-select item-select-glass">' + options(GLASS_TYPES, item.glass_type, '{{ __('Select') }}') + '</select>' +
                '</div>' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Quantity') }}</label>' +
                    '<input type="number" name="items[' + index + '][quantity]" class="form-control" min="1" value="' + (item.quantity || 1) + '" required>' +
                '</div>' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Price') }}</label>' +
                    '<input type="number" step="0.01" min="0" name="items[' + index + '][price]" class="form-control item-price" value="' + (item.price || '') + '" required>' +
                '</div>' +
                '<div class="col-md-3 mb-4">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Design Image') }}</label>' +
                    '<input type="file" name="items[' + index + '][design_image]" class="form-control" accept="image/*">' +
                    '<input type="hidden" name="items[' + index + '][existing_design_image]" value="' + (item.design_image_path || '') + '">' +
                    (item.design_image_url ? '<img src="' + item.design_image_url + '" class="mt-2" style="width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">' : '') +
                '</div>' +
                '<div class="col-md-9 mb-2">' +
                    '<label class="fw-bold fs-7 d-block">{{ __('Description') }}</label>' +
                    '<textarea name="items[' + index + '][description]" class="form-control" rows="1">' + (item.description || '') + '</textarea>' +
                '</div>' +
                '<div class="col-md-3 mb-2 d-flex align-items-end justify-content-end">' +
                    '<button type="button" class="btn btn-sm btn-light-danger btn-remove-item"><i class="fas fa-trash me-1"></i>{{ __('Remove') }}</button>' +
                '</div>' +
            '</div>' +
            '</div>'
        );

        $container.append($card);
        $card.find('.item-select-wood').select2({ width: '100%' });
        $card.find('.item-select-color').select2({ width: '100%' });
        $card.find('.item-select-glass').select2({ width: '100%' });

        recalculate();
    }

    $('#addItemRow').on('click', function () {
        addItemRow(null);
    });

    $container.on('click', '.btn-remove-item', function () {
        if ($container.find('.item-row').length <= 1) {
            toastr.error('{{ __('At least one item is required.') }}');
            return;
        }
        $(this).closest('.item-row').remove();
        recalculate();
    });

    $container.on('input', '.item-price', recalculate);
    $('#advance_amount').on('input', recalculate);

    function recalculate() {
        let estimatedPrice = 0;
        $container.find('.item-price').each(function () {
            estimatedPrice += parseFloat($(this).val()) || 0;
        });

        const advanceAmount = parseFloat($('#advance_amount').val()) || 0;
        const remaining = Math.max(estimatedPrice - advanceAmount, 0);

        $('#estimatedPriceDisplay').val(estimatedPrice.toFixed(2));
        $('#remainingAmountDisplay').val(remaining.toFixed(2));
    }

    $('#customer_id').select2({ width: '100%' });
    $('#order_type').select2({ width: '100%' });

    if (EXISTING_ITEMS.length) {
        EXISTING_ITEMS.forEach(function (item) { addItemRow(item); });
    } else {
        addItemRow(null);
    }
});
</script>
@endpush
