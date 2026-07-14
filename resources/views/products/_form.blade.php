@php
    $product = $product ?? null;
@endphp

<div class="row">
    <div class="col-md-6 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Category') }}</label>
        <select name="category_id" id="category_id" class="form-select form-select-solid form-select-lg" required>
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

    <div class="col-md-6 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Sub Category') }}</label>
        <select name="sub_category_id" id="sub_category_id" class="form-select form-select-solid form-select-lg" required>
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

<div class="mb-6">
    <label class="col-form-label required fw-bold fs-6">{{ __('Name') }}</label>
    <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $product?->name) }}" required>
    @error('name')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-6">
    <label class="col-form-label required fw-bold fs-6">{{ __('Ready or Custom Product') }}</label>
    <div class="d-flex align-items-center gap-6">
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

<div class="mb-6 stock-section">
    <label class="col-form-label fw-bold fs-6">{{ __('Stock') }}</label>
    <input type="number" name="stock" min="0" class="form-control form-control-lg form-control-solid" value="{{ old('stock', $product?->stock ?? 0) }}">
    <div class="form-text">{{ __('Stock only applies to Ready Products. Custom Products are made to order.') }}</div>
    @error('stock')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="row brand-section">
    <div class="col-md-6 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Brand') }}</label>
        <select name="brand_id" id="brand_id" class="form-select form-select-solid form-select-lg">
            <option value="" disabled @selected(! old('brand_id', $product?->brand_id))>{{ __('Select') }}</option>
            @foreach ($brands as $brand)
                <option value="{{ $brand->id }}" @selected(old('brand_id', $product?->brand_id) == $brand->id)>{{ $brand->name }}</option>
            @endforeach
        </select>
        @error('brand_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Model Number') }}</label>
        <input type="text" name="model" class="form-control form-control-lg form-control-solid" value="{{ old('model', $product?->model) }}">
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('IMEI / Serial Number') }}</label>
        <input type="text" name="imei_serial" class="form-control form-control-lg form-control-solid" value="{{ old('imei_serial', $product?->imei_serial) }}">
    </div>
</div>

<div class="row furniture-section">
    <div class="col-md-6 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Manufacturer') }}</label>
        <select name="manufacturer_code" id="manufacturer_code" class="form-select form-select-solid form-select-lg">
            <option value="" disabled @selected(! old('manufacturer_code', $product?->manufacturer_code))>{{ __('Select') }}</option>
            @foreach ($manufacturers as $manufacturer)
                <option value="{{ $manufacturer['code'] }}" @selected(old('manufacturer_code', $product?->manufacturer_code) == $manufacturer['code'])>{{ app()->getLocale() === 'bn' ? $manufacturer['bn_name'] : $manufacturer['en_name'] }}</option>
            @endforeach
        </select>
        @error('manufacturer_code')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Wood Type') }}</label>
        <select name="wood_type" id="wood_type" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach ($woodTypes as $woodType)
                <option value="{{ $woodType['code'] }}" @selected(old('wood_type', $product?->wood_type) === $woodType['code'])>{{ app()->getLocale() === 'bn' ? $woodType['bn_name'] : $woodType['en_name'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Color') }}</label>
        <select name="color" id="color" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach ($colors as $color)
                <option value="{{ $color['code'] }}" @selected(old('color', $product?->color) === $color['code'])>{{ app()->getLocale() === 'bn' ? $color['bn_name'] : $color['en_name'] }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Size') }}</label>
        <select name="size" id="size" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach (['5 Feet', '6 Feet', '7 Feet'] as $option)
                <option value="{{ $option }}" @selected(old('size', $product?->size) === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Polish') }}</label>
        <select name="polish" id="polish" class="form-select form-select-solid form-select-lg">
            <option value="">{{ __('Select') }}</option>
            @foreach (['Matt', 'Glossy'] as $option)
                <option value="{{ $option }}" @selected(old('polish', $product?->polish) === $option)>{{ $option }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Warranty') }}</label>
        <input type="text" name="warranty" class="form-control form-control-lg form-control-solid" placeholder="{{ __('e.g. 24 Months') }}" value="{{ old('warranty', $product?->warranty) }}">
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label fw-bold fs-6">SKU</label>
        <input type="text" name="sku" class="form-control form-control-lg form-control-solid" value="{{ old('sku', $product?->sku) }}">
        @error('sku')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Purchase Price') }}</label>
        <input type="number" step="0.01" min="0" name="purchase_price" class="form-control form-control-lg form-control-solid" value="{{ old('purchase_price', $product?->purchase_price) }}" required>
        @error('purchase_price')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Selling Price') }}</label>
        <input type="number" step="0.01" min="0" name="selling_price" class="form-control form-control-lg form-control-solid" value="{{ old('selling_price', $product?->selling_price) }}" required>
        @error('selling_price')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

@if ($product && $product->images->count())
    <div class="mb-6">
        <label class="col-form-label fw-bold fs-6 d-block">{{ __('Existing Images') }}</label>
        <div id="existing_images_container" class="d-flex flex-wrap gap-3">
            @foreach ($product->images as $image)
                <div class="position-relative existing-image" data-image-id="{{ $image->id }}">
                    <img src="{{ asset($image->image_path) }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">
                    <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete-existing-image position-absolute top-0 start-100 translate-middle rounded-circle" data-image-id="{{ $image->id }}" data-product-id="{{ $product->id }}" style="width:22px;height:22px;">
                        <i class="fas fa-times fs-8"></i>
                    </button>
                </div>
            @endforeach
        </div>
    </div>
@endif

<div class="mb-6">
    <label class="col-form-label fw-bold fs-6 d-block">{{ __('Images') }}</label>
    <input type="file" name="images[]" id="images" class="form-control form-control-lg form-control-solid" multiple accept="image/*">
    <div class="form-text">{{ __('You can select multiple images.') }}</div>
    @error('images')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
    <div id="new_images_preview" class="d-flex flex-wrap gap-3 mt-3"></div>
</div>

@if ($product)
    <div class="mb-6">
        <label class="col-form-label fw-bold fs-6 d-block">{{ __('Status') }}</label>
        <select name="status" id="status" class="form-select form-select-solid form-select-lg">
            <option value="1" @selected(old('status', $product->status))>{{ __('Active') }}</option>
            <option value="0" @selected(! old('status', $product->status))>{{ __('Inactive') }}</option>
        </select>
    </div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var categorySelect = document.getElementById('category_id');
        var subCategorySelect = document.getElementById('sub_category_id');
        var brandSections = document.querySelectorAll('.brand-section');
        var furnitureSections = document.querySelectorAll('.furniture-section');
        var brandField = document.querySelector('[name="brand_id"]');
        var manufacturerField = document.querySelector('[name="manufacturer_code"]');
        var stockSection = document.querySelector('.stock-section');

        function subCategoryMatcher(params, data) {
            if (!data.element) {
                return data;
            }

            var categoryId = $(categorySelect).val();
            var optCategoryId = $(data.element).data('category-id');

            if (categoryId && String(optCategoryId) !== String(categoryId)) {
                return null;
            }

            if ($.trim(params.term) === '') {
                return data;
            }

            if (data.text && data.text.toUpperCase().indexOf(params.term.toUpperCase()) > -1) {
                return data;
            }

            return null;
        }

        $('#category_id').select2({ width: '100%' });
        $('#sub_category_id').select2({ width: '100%', matcher: subCategoryMatcher });
        $('#brand_id').select2({ width: '100%' });
        $('#manufacturer_code').select2({ width: '100%' });
        $('#wood_type').select2({ width: '100%' });
        $('#color').select2({ width: '100%' });
        $('#size').select2({ width: '100%' });
        $('#polish').select2({ width: '100%' });
        $('#status').select2({ width: '100%' });

        function updateCategoryDependentFields() {
            var selected = categorySelect.options[categorySelect.selectedIndex];
            var brandRequired = selected ? selected.dataset.brandRequired === '1' : true;

            brandSections.forEach(function (el) { el.style.display = brandRequired ? '' : 'none'; });
            furnitureSections.forEach(function (el) { el.style.display = brandRequired ? 'none' : ''; });

            if (brandField) brandField.required = brandRequired;
            if (manufacturerField) manufacturerField.required = ! brandRequired;
        }

        function updateStockVisibility() {
            var checked = document.querySelector('[name="product_type"]:checked');
            stockSection.style.display = (checked && checked.value === 'ready') ? '' : 'none';
        }

        $(categorySelect).on('change', updateCategoryDependentFields);
        document.querySelectorAll('[name="product_type"]').forEach(function (radio) {
            radio.addEventListener('change', updateStockVisibility);
        });

        updateCategoryDependentFields();
        updateStockVisibility();

        $('#images').on('change', function () {
            var $preview = $('#new_images_preview').empty();
            var files = this.files;

            for (var i = 0; i < files.length; i++) {
                (function (file) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $preview.append(
                            '<div class="position-relative">' +
                            '<img src="' + e.target.result + '" style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">' +
                            '</div>'
                        );
                    };
                    reader.readAsDataURL(file);
                })(files[i]);
            }
        });

        $(document).on('click', '.btn-delete-existing-image', function () {
            var imageId = $(this).data('image-id');
            var productId = $(this).data('product-id');
            var $wrapper = $(this).closest('.existing-image');

            $.ajax({
                url: '{{ url('products') }}/' + productId + '/images/' + imageId + '/delete',
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    toastr.success(response.message || '{{ __('Image deleted successfully.') }}');
                    $wrapper.remove();
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || '{{ __('Delete failed.') }}');
                }
            });
        });
    });
</script>
@endpush
