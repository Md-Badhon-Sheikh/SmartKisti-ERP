@once
    @push('scripts')
    <script>
        $(function () {
            $(document).on('submit', '.delete-form', function (e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: '{{ __('Are you sure?') }}',
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
    </script>
    @endpush
@endonce
