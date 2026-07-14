<x-app-layout :title="__('Areas')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Area List') }}</h3>
                        <button type="button" class="btn btn-primary btn-sm" id="openAddAreaModal">
                            <i class="fas fa-plus me-1"></i> {{ __('Add Area') }}
                        </button>
                    </div>
                    <div class="card-body">
                        @include('customers.areas.partials.area-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- Add / Edit Modal --}}
    @include('customers.areas.partials.add-area')

    {{-- View Modal --}}
    @include('customers.areas.partials.view-area')

</x-app-layout>
