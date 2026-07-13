<div class="modal fade" id="brandFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="brandForm">
                @csrf
                <input type="hidden" name="brand_id" id="brand_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="brandFormModalTitle">{{ __('Add Brand') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-5">
                        <label class="form-label required fw-bold">{{ __('Name') }}</label>
                        <input type="text" name="brand_name" id="brand_name" class="form-control" required>
                        <div class="invalid-feedback" id="brand_name_error"></div>
                    </div>

                    <div class="mb-5" id="brand_status_row" style="display: none;">
                        <label class="form-label fw-bold d-block">{{ __('Status') }}</label>
                        <select name="brand_status" id="brand_status" class="form-select">
                            <option value="1">{{ __('Active') }}</option>
                            <option value="0">{{ __('Inactive') }}</option>
                        </select>
                        <div class="invalid-feedback" id="brand_status_error"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="brandFormSubmit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    const $modal   = $('#brandFormModal');
    const $form    = $('#brandForm');
    const $title   = $('#brandFormModalTitle');
    const $idInput = $('#brand_id');

    $('#brand_status').select2({
        dropdownParent: $modal,
        width: '100%',
    });

    function resetForm() {
        $form[0].reset();
        $idInput.val('');
        $('#brand_status').val('1').trigger('change');
        $('#brand_status_row').hide();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }

    $('#openAddBrandModal').on('click', function () {
        resetForm();
        $title.text('{{ __('Add Brand') }}');
        $modal.modal('show');
    });

    window.openBrandEditModal = function (data) {
        resetForm();
        $title.text('{{ __('Edit Brand') }}');
        $idInput.val(data.id);
        $('#brand_name').val(data.name);
        $('#brand_status').val(data.status ? '1' : '0').trigger('change');
        $('#brand_status_row').show();
        $modal.modal('show');
    };

    $modal.on('hidden.bs.modal', function () {
        $('#brandListDatatable tbody tr').removeClass('table-warning editing-row');
    });

    $form.on('submit', function (e) {
        e.preventDefault();

        const id  = $idInput.val();
        const url = id ? "{{ url('brands') }}/" + id + '/update' : "{{ route('brands.store') }}";

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        $.ajax({
            url: url,
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                toastr.success(response.message || '{{ __('Saved successfully.') }}');
                $modal.modal('hide');
                $('#brandListDatatable').DataTable().ajax.reload(null, false);
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors || {};
                    $.each(errors, function (field, messages) {
                        $('#' + field).addClass('is-invalid');
                        $('#' + field + '_error').text(messages[0]);
                    });
                } else {
                    toastr.error(xhr.responseJSON?.message || '{{ __('Something went wrong.') }}');
                }
            },
        });
    });
});
</script>
@endpush
