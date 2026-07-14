<x-app-layout :title="__('Custom Orders')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Custom Order List') }}</h3>
                        @hasanyrole('super-admin|admin|manager')
                            <a href="{{ route('custom-orders.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> {{ __('New Custom Order') }}
                            </a>
                        @endhasanyrole
                    </div>
                    <div class="card-body">
                        @include('custom-orders.partials.custom-order-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
