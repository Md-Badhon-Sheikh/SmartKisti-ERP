<div class="row g-3 mb-4" id="salesReportFilters">
    <div class="col-md-2">
        <label class="form-label fw-bold fs-7">{{ __('Category') }}</label>
        <select id="filterCategory" class="form-select form-select-solid">
            <option value="">{{ __('All') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold fs-7">{{ __('Sub Category') }}</label>
        <select id="filterSubCategory" class="form-select form-select-solid">
            <option value="">{{ __('All') }}</option>
            @foreach ($subCategories as $subCategory)
                <option value="{{ $subCategory->id }}" data-category-id="{{ $subCategory->category_id }}">{{ $subCategory->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold fs-7">{{ __('Area') }}</label>
        <select id="filterArea" class="form-select form-select-solid">
            <option value="">{{ __('All') }}</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}">{{ $area->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold fs-7">{{ __('Sale Type') }}</label>
        <select id="filterSaleType" class="form-select form-select-solid">
            <option value="">{{ __('All') }}</option>
            <option value="cash">{{ __('Cash') }}</option>
            <option value="installment">{{ __('Installment') }}</option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold fs-7">{{ __('Installment Status') }}</label>
        <select id="filterInstallmentStatus" class="form-select form-select-solid" disabled>
            <option value="">{{ __('All') }}</option>
            <option value="due">{{ __('Due') }}</option>
            <option value="paid">{{ __('Paid') }}</option>
        </select>
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold fs-7">{{ __('Start Date') }}</label>
        <input type="date" id="filterStartDate" class="form-control form-control-solid">
    </div>

    <div class="col-md-2">
        <label class="form-label fw-bold fs-7">{{ __('End Date') }}</label>
        <input type="date" id="filterEndDate" class="form-control form-control-solid">
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold fs-7">{{ __('Search') }}</label>
        <input type="text" id="filterSearch" class="form-control form-control-solid"
               placeholder="{{ __('Customer, Mobile, Area, Product, Code, Invoice, Receipt No...') }}">
    </div>

    <div class="col-md-3 d-flex align-items-end gap-2">
        <button type="button" id="btnApplyFilters" class="btn btn-primary flex-fill">
            <i class="fas fa-filter me-1"></i>{{ __('Apply Filters') }}
        </button>
        <button type="button" id="btnResetFilters" class="btn btn-light flex-fill">
            <i class="fas fa-rotate-left me-1"></i>{{ __('Reset') }}
        </button>
    </div>
</div>
