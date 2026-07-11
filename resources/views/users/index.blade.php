<x-app-layout :title="__('All Users')">
    <div class="card">
        <div class="card-header border-0 pt-6">
            <div class="card-toolbar">
                <h2 class="card-label">{{ __('All Users') }}</h2>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> {{ __('New User') }}
                </a>
            </div>
        </div>
        <div class="card-body pt-0" style="overflow-x: auto;">
            @include('partials.user-list-datatable')
        </div>
    </div>
</x-app-layout>
