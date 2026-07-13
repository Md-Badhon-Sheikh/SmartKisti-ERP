<x-app-layout :title="__('Brands')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Brand List') }}</h3>
                        <button type="button" class="btn btn-primary btn-sm" id="openAddBrandModal">
                            <i class="fas fa-plus me-1"></i> {{ __('Add Brand') }}
                        </button>
                    </div>
                    <div class="card-body">
                        @include('products.brands.partials.brand-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Add / Edit Modal --}}
    @include('products.brands.partials.add-brand')

    {{-- View Modal --}}
    @include('products.brands.partials.view-brand')

</x-app-layout>
