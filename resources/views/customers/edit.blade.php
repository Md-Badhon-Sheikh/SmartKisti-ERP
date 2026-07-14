<x-app-layout :title="__('Edit Customer')">
    <div class="card my-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0 d-flex align-items-center justify-content-between w-100">
                <h3 class="fw-bolder m-0">{{ $customer->name }}</h3>
                  <div>
                    <a href="javascript:void(0)" onclick="history.back()" class="btn btn-light-primary border btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>{{ __('Back') }}</a>
                </div>
            </div>
        </div>

        <form action="{{ route('customers.update', $customer) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body border-top p-9">
                @include('customers._form')
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('customers.index') }}" class="btn btn-light btn-active-light-primary me-2">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
