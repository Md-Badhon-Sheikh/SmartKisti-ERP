<div class="modal fade" id="categoryFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="categoryForm">
                @csrf
                <input type="hidden" name="category_id" id="category_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="categoryFormModalTitle">{{ __('Add Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-5">
                        <label class="form-label required fw-bold">{{ __('Name') }}</label>
                        <input type="text" name="category_name" id="category_name" class="form-control" required>
                        <div class="invalid-feedback" id="category_name_error"></div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label required fw-bold d-block">{{ __('Brand Required') }}</label>
                        <select name="brand_required" id="brand_required" class="form-select" required>
                            <option value="1">{{ __('Yes') }}</option>
                            <option value="0">{{ __('No') }}</option>
                        </select>
                        <div class="invalid-feedback" id="brand_required_error"></div>
                        <div class="form-text">{{ __('Turn this off for categories like Furniture, where products are made in-house and typically have no brand.') }}</div>
                    </div>

                    <div class="mb-5" id="category_status_row" style="display: none;">
                        <label class="form-label fw-bold d-block">{{ __('Status') }}</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="category_status" id="category_status" value="1" checked>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="categoryFormSubmit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    const $modal   = $('#categoryFormModal');
    const $form    = $('#categoryForm');
    const $title   = $('#categoryFormModalTitle');
    const $idInput = $('#category_id');

    $('#brand_required').select2({
        dropdownParent: $modal,
        width: '100%',
    });

    function resetForm() {
        $form[0].reset();
        $idInput.val('');
        $('#brand_required').val('1').trigger('change');
        $('#category_status').prop('checked', true);
        $('#category_status_row').hide();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }

    $('#openAddCategoryModal').on('click', function () {
        resetForm();
        $title.text('{{ __('Add Category') }}');
        $modal.modal('show');
    });

    window.openCategoryEditModal = function (data) {
        resetForm();
        $title.text('{{ __('Edit Category') }}');
        $idInput.val(data.id);
        $('#category_name').val(data.name);
        $('#brand_required').val(data.brand_required ? '1' : '0').trigger('change');
        $('#category_status').prop('checked', !!data.status);
        $('#category_status_row').show();
        $modal.modal('show');
    };

    $modal.on('hidden.bs.modal', function () {
        $('#categoryListDatatable tbody tr').removeClass('table-warning editing-row');
    });

    $form.on('submit', function (e) {
        e.preventDefault();

        const id  = $idInput.val();
        const url = id ? "{{ url('categories') }}/" + id + '/update' : "{{ route('categories.store') }}";

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        $.ajax({
            url: url,
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                toastr.success(response.message || '{{ __('Saved successfully.') }}');
                $modal.modal('hide');
                $('#categoryListDatatable').DataTable().ajax.reload(null, false);
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
