<x-app-layout :title="__('Customers')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Customer List') }}</h3>
                        @hasanyrole('super-admin|admin|manager')
                            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> {{ __('Add Customer') }}
                            </a>
                        @endhasanyrole
                    </div>
                    <div class="card-body">
                        @include('customers.partials.customer-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- View Modal --}}
    @include('customers.partials.view-customer')

</x-app-layout>
