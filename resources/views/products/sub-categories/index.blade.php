<x-app-layout :title="__('Sub Categories')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Sub Category List') }}</h3>
                        <button type="button" class="btn btn-primary btn-sm" id="openAddSubCategoryModal">
                            <i class="fas fa-plus me-1"></i> {{ __('Add Sub Category') }}
                        </button>
                    </div>
                    <div class="card-body">
                        @include('products.sub-categories.partials.sub-category-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Add / Edit Modal --}}
    @include('products.sub-categories.partials.add-sub-category')

    {{-- View Modal --}}
    @include('products.sub-categories.partials.view-sub-category')

</x-app-layout>
