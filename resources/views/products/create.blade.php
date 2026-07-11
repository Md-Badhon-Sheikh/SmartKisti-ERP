<x-app-layout :title="__('New Product')">
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">{{ __('New Product') }}</h3>
            </div>
        </div>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="card-body border-top p-9">
                @include('products._form')
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('products.index') }}" class="btn btn-light btn-active-light-primary me-2">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
