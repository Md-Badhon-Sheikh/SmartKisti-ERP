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
            <table class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Mobile') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Role') }}</th>
                        <th class="text-end">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->email ?? '—' }}</td>
                            <td>
                                @foreach ($user->roles as $role)
                                    <span class="badge badge-light-primary">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-end">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-icon btn-light-success me-2" title="{{ __('View') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-icon btn-light-primary me-2" title="{{ __('Edit') }}">
                                    <i class="fas fa-pen"></i>
                                </a>

                                @role('super-admin')
                                    @unless ($user->hasRole('super-admin'))
                                        <form action="{{ route('users.promote-super-admin', $user) }}" method="POST" class="d-inline promote-form">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-icon btn-light-warning me-2" title="{{ __('Make Super Admin') }}">
                                                <i class="fas fa-crown"></i>
                                            </button>
                                        </form>
                                    @endunless
                                @endrole

                                @if ($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-icon btn-light-danger" title="{{ __('Delete') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $users->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '{{ __('Are you sure?') }}',
                        text: '{{ __('This user will be deleted!') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('Yes, delete it') }}',
                        cancelButtonText: '{{ __('Cancel') }}',
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });

            document.querySelectorAll('.promote-form').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    Swal.fire({
                        title: '{{ __('Are you sure?') }}',
                        text: '{{ __('This user will be made a Super Admin!') }}',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '{{ __('Yes, confirm') }}',
                        cancelButtonText: '{{ __('Cancel') }}',
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
