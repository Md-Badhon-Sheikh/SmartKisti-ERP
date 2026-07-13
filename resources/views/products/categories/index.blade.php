<x-app-layout :title="__('Categories')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Category List') }}</h3>
                        <button type="button" class="btn btn-primary btn-sm" id="openAddCategoryModal">
                            <i class="fas fa-plus me-1"></i> {{ __('Add Category') }}
                        </button>
                    </div>
                    <div class="card-body">
                        @include('products.categories.partials.category-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Add / Edit Modal --}}
    @include('products.categories.partials.add-category')

    {{-- View Modal --}}
    @include('products.categories.partials.view-category')

</x-app-layout>