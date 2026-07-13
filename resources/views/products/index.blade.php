<x-app-layout :title="__('Products')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Product List') }}</h3>
                        @hasanyrole('super-admin|admin|manager')
                            <a href="{{ route('products.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Product') }}
                            </a>
                        @endhasanyrole
                    </div>
                    <div class="card-body">
                        @include('products.partials.product-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- View Modal --}}
    @include('products.partials.view-product')

</x-app-layout>
