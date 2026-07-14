<x-app-layout :title="__('Sales')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Sale List') }}</h3>
                        @hasanyrole('super-admin|admin|manager')
                            <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> {{ __('New Sale') }}
                            </a>
                        @endhasanyrole
                    </div>
                    <div class="card-body">
                        @include('sales.partials.sale-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- View Modal --}}
    @include('sales.partials.view-sale')

</x-app-layout>
