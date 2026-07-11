@php
    $product = $product ?? null;
@endphp

<div class="row mb-6">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Category') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="category_id" id="product-category" class="form-select form-select-solid form-select-lg" required>
            <option value="" disabled @selected(! old('category_id', $product?->category_id))>{{ __('Select') }}</option>
            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    data-brand-required="{{ $category->brand_required ? 1 : 0 }}"
                    @selected(old('category_id', $product?->category_id) == $category->id)
                >{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Sub Category') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="sub_category_id" id="product-sub-category" class="form-select form-select-solid form-select-lg" required>
            <option value="" disabled @selected(! old('sub_category_id', $product?->sub_category_id))>{{ __('Select') }}</option>
            @foreach ($subCategories as $subCategory)
                <option
                    value="{{ $subCategory->id }}"
                    data-category-id="{{ $subCategory->category_id }}"
                    @selected(old('sub_category_id', $product?->sub_category_id) == $subCategory->id)
                >{{ $subCategory->name }}</option>
            @endforeach
        </select>
        @error('sub_category_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Name') }}</label>
    <div class="col-lg-8 fv-row">
        <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $product?->name) }}" required>
        @error('name')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Ready or Custom Product') }}</label>
    <div class="col-lg-8 fv-row d-flex align-items-center gap-6">
        <div class="form-check form-check-custom form-check-solid">
            <input type="radio" name="product_type" id="product-type-ready" value="ready" class="form-check-input" @checked(old('product_type', $product?->product_type ?? 'ready') === 'ready')>
            <label class="form-check-label" for="product-type-ready">{{ __('Ready Product') }}</label>
        </div>
        <div class="form-check form-check-custom form-check-solid">
            <input type="radio" name="product_type" id="product-type-custom" value="custom" class="form-check-input" @checked(old('product_type', $product?->product_type) === 'custom')>
            <label class="form-check-label" for="product-type-custom">{{ __('Custom Product') }}</label>
        </div>
    </div>
</div>

<div class="row mb-6 stock-section">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Stock') }}</label>
    <div class="col-lg-8 fv-row">
        <input type="number" name="stock" min="0" class="form-control form-control-lg form-control-solid" value="{{ old('stock', $product?->stock ?? 0) }}">
        <div class="form-text">{{ __('Stock only applies to Ready Products. Custom Products are made to order.') }}</div>
        @error('stock')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6 brand-section">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Brand') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="brand_id" class="form-select form-select-solid form-select-lg">
            <option value="" disabled @selected(! old('brand_id', $product?->brand_id))>{{ __('Select') }}</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $product?->brand_id) == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        @error('brand_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6 brand-section">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Model Number') }}</label>
    <div class="col-lg-8 fv-row">
        <input type="text" name="model" class="form-control form-control-lg form-control-solid" value="{{ old('model', $product?->model) }}">
    </div>
</div>

<div class="row mb-6 brand-section">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('IMEI / Serial Number') }}</label>
    <div class="col-lg-8 fv-row">
        <input type="text" name="imei_serial" class="form-control form-control-lg form-control-solid" value="{{ old('imei_serial', $product?->imei_serial) }}">
    </div>
</div>

<div class="row mb-6 furniture-section">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Manufacturer') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="manufacturer_id" class="form-select form-select-solid form-select-lg">
            <option value="" disabled @selected(! old('manufacturer_id', $product?->manufacturer_id))>{{ __('Select') }}</option>
            @foreach ($manufacturers as $manufacturer)
                <option value="{{ $manufacturer->id }}" @selected(old('manufacturer_id', $product?->manufacturer_id) == $manufacturer->id)>{{ $manufacturer->name }}</option>
            @endforeach
        </select>
        @error('manufacturer_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6 furniture-section">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Wood Type') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="wood_type" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach (['Segun', 'Gamari', 'Mahogany', 'Board', 'MDF'] as $option)
                <option value="{{ $option }}" @selected(old('wood_type', $product?->wood_type) === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-6 furniture-section">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Color') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="color" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach (['Brown', 'Black', 'White'] as $option)
                <option value="{{ $option }}" @selected(old('color', $product?->color) === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-6 furniture-section">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Size') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="size" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach (['5 Feet', '6 Feet', '7 Feet'] as $option)
                <option value="{{ $option }}" @selected(old('size', $product?->size) === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-6 furniture-section">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Polish') }}</label>
    <div class="col-lg-8 fv-row">
        <select name="polish" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach (['Matt', 'Glossy'] as $option)
                <option value="{{ $option }}" @selected(old('polish', $product?->polish) === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row mb-6">
    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Warranty') }}</label>
    <div class="col-lg-8 fv-row">
        <input type="text" name="warranty" class="form-control form-control-lg form-control-solid" placeholder="{{ __('e.g. 24 Months') }}" value="{{ old('warranty', $product?->warranty) }}">
    </div>
</div>

<div class="row mb-6">
    <label class="col-lg-4 col-form-label fw-bold fs-6">SKU</label>
    <div class="col-lg-8 fv-row">
        <input type="text" name="sku" class="form-control form-control-lg form-control-solid" value="{{ old('sku', $product?->sku) }}">
        @error('sku')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Purchase Price') }}</label>
    <div class="col-lg-8 fv-row">
        <input type="number" step="0.01" min="0" name="purchase_price" class="form-control form-control-lg form-control-solid" value="{{ old('purchase_price', $product?->purchase_price) }}" required>
        @error('purchase_price')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row mb-6">
    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Selling Price') }}</label>
    <div class="col-lg-8 fv-row">
        <input type="number" step="0.01" min="0" name="selling_price" class="form-control form-control-lg form-control-solid" value="{{ old('selling_price', $product?->selling_price) }}" required>
        @error('selling_price')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

@if ($product)
    <div class="row mb-6">
        <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Status') }}</label>
        <div class="col-lg-8 fv-row">
            <div class="form-check form-switch form-check-custom form-check-solid">
                <input type="checkbox" name="status" class="form-check-input" value="1" @checked(old('status', $product->status))>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var categorySelect = document.getElementById('product-category');
        var subCategorySelect = document.getElementById('product-sub-category');
        var brandSections = document.querySelectorAll('.brand-section');
        var furnitureSections = document.querySelectorAll('.furniture-section');
        var brandField = document.querySelector('[name="brand_id"]');
        var manufacturerField = document.querySelector('[name="manufacturer_id"]');
        var stockSection = document.querySelector('.stock-section');

        function updateCategoryDependentFields() {
            var selected = categorySelect.options[categorySelect.selectedIndex];
            var brandRequired = selected ? selected.dataset.brandRequired === '1' : true;

            brandSections.forEach(function (el) { el.style.display = brandRequired ? '' : 'none'; });
            furnitureSections.forEach(function (el) { el.style.display = brandRequired ? 'none' : ''; });

            if (brandField) brandField.required = brandRequired;
            if (manufacturerField) manufacturerField.required = ! brandRequired;

            var categoryId = categorySelect.value;
            Array.from(subCategorySelect.options).forEach(function (option) {
                if (! option.value) return;
                option.hidden = option.dataset.categoryId !== categoryId;
            });
        }

        function updateStockVisibility() {
            var checked = document.querySelector('[name="product_type"]:checked');
            stockSection.style.display = (checked && checked.value === 'ready') ? '' : 'none';
        }

        categorySelect.addEventListener('change', updateCategoryDependentFields);
        document.querySelectorAll('[name="product_type"]').forEach(function (radio) {
            radio.addEventListener('change', updateStockVisibility);
        });

        updateCategoryDependentFields();
        updateStockVisibility();
    });
</script>
@endpush
