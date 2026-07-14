<x-app-layout :title="__('Deliveries')">

    <div class="container-fluid py-4">

        <div class="row g-4">

            <div class="col-12">
                <div class="card shadow-sm border">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h3 class="card-title fw-bold mb-0">{{ __('Deliveries') }}</h3>
                    </div>
                    <div class="card-body">
                        @include('deliveries.partials.delivery-list-datatable')
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-app-layout>
