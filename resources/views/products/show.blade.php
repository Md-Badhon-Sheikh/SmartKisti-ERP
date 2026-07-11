<x-app-layout :title="__('Product Details')">
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">{{ $product->name }}</h3>
            </div>
            <div class="card-toolbar">
                <span class="badge badge-light-{{ $product->status ? 'success' : 'danger' }}">
                    {{ $product->status ? __('Active') : __('Inactive') }}
                </span>
            </div>
        </div>
        <div class="card-body border-top p-9">
            <div class="row mb-4">
                <label class="col-lg-4 fw-bold text-muted">{{ __('Category') }}</label>
                <div class="col-lg-8">{{ $product->category->name }}</div>
            </div>
            <div class="row mb-4">
                <label class="col-lg-4 fw-bold text-muted">{{ __('Sub Category') }}</label>
                <div class="col-lg-8">{{ $product->subCategory->name }}</div>
            </div>
            <div class="row mb-4">
                <label class="col-lg-4 fw-bold text-muted">{{ __('Ready or Custom Product') }}</label>
                <div class="col-lg-8">
                    <span class="badge badge-light-{{ $product->product_type === 'ready' ? 'success' : 'warning' }}">
                        {{ $product->product_type === 'ready' ? __('Ready') : __('Custom') }}
                    </span>
                </div>
            </div>

            @if ($product->brand)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Brand') }}</label>
                    <div class="col-lg-8">{{ $product->brand->name }}</div>
                </div>
            @endif
            @if ($product->model)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Model Number') }}</label>
                    <div class="col-lg-8">{{ $product->model }}</div>
                </div>
            @endif
            @if ($product->imei_serial)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('IMEI / Serial Number') }}</label>
                    <div class="col-lg-8">{{ $product->imei_serial }}</div>
                </div>
            @endif

            @if ($product->manufacturer)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Manufacturer') }}</label>
                    <div class="col-lg-8">{{ $product->manufacturer->name }}</div>
                </div>
            @endif
            @if ($product->wood_type)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Wood Type') }}</label>
                    <div class="col-lg-8">{{ $product->wood_type }}</div>
                </div>
            @endif
            @if ($product->color)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Color') }}</label>
                    <div class="col-lg-8">{{ $product->color }}</div>
                </div>
            @endif
            @if ($product->size)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Size') }}</label>
                    <div class="col-lg-8">{{ $product->size }}</div>
                </div>
            @endif
            @if ($product->polish)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Polish') }}</label>
                    <div class="col-lg-8">{{ $product->polish }}</div>
                </div>
            @endif
            @if ($product->warranty)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Warranty') }}</label>
                    <div class="col-lg-8">{{ $product->warranty }}</div>
                </div>
            @endif
            @if ($product->sku)
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">SKU</label>
                    <div class="col-lg-8">{{ $product->sku }}</div>
                </div>
            @endif

            <div class="row mb-4">
                <label class="col-lg-4 fw-bold text-muted">{{ __('Purchase Price') }}</label>
                <div class="col-lg-8">{{ number_format($product->purchase_price, 2) }}</div>
            </div>
            <div class="row mb-4">
                <label class="col-lg-4 fw-bold text-muted">{{ __('Selling Price') }}</label>
                <div class="col-lg-8">{{ number_format($product->selling_price, 2) }}</div>
            </div>
            @if ($product->product_type === 'ready')
                <div class="row mb-4">
                    <label class="col-lg-4 fw-bold text-muted">{{ __('Stock') }}</label>
                    <div class="col-lg-8">{{ $product->stock }}</div>
                </div>
            @endif
        </div>
        <div class="card-footer d-flex justify-content-end py-6 px-9">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary me-2">{{ __('Edit') }}</a>
            <a href="{{ route('products.index') }}" class="btn btn-light">{{ __('Back to List') }}</a>
        </div>
    </div>
</x-app-layout>
