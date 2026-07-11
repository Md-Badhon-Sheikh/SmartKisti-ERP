<table class="table erp-datatable align-middle table-bordered fs-6 gy-5 m-auto display responsive" id="users-table">
    <thead>
        <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0">
            <th>{{ __('#') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Mobile') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Role') }}</th>
            <th class="text-end">{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody class="text-gray-600 fw-bold"></tbody>
</table>

@push('scripts')
<script>
    $(function () {
        smartkistiDataTable('#users-table', {
            ajax: '{{ route('users.data') }}',
            columns: [
                {
                    data: null, name: 'serial', orderable: false, searchable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                { data: 'name', name: 'name' },
                { data: 'mobile', name: 'mobile' },
                { data: 'email', name: 'email', defaultContent: '—' },
                { data: 'role', name: 'role', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' },
            ],
        });

        $('#users-table').on('submit', '.delete-form', function (e) {
            e.preventDefault();
            var form = this;
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

        $('#users-table').on('submit', '.promote-form', function (e) {
            e.preventDefault();
            var form = this;
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
</script>
@endpush
