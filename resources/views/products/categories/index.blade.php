<x-app-layout :title="__('Categories')">
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <h2 class="card-label">{{ __('Categories') }}</h2>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('New Category') }}
                </a>
            </div>
        </div>
        <div class="card-body pt-0" style="overflow-x: auto;">
            @include('partials.category-list-datatable')
        </div>
    </div>

    <x-delete-confirm-script />

</x-app-layout>
