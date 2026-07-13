<div class="modal fade" id="subCategoryFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="subCategoryForm">
                @csrf
                <input type="hidden" name="sub_category_id" id="sub_category_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="subCategoryFormModalTitle">{{ __('Add Sub Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-5">
                        <label class="form-label required fw-bold d-block">{{ __('Category') }}</label>
                        <select name="category_id" id="category_id" class="form-select" required>
                            <option value="" disabled selected>{{ __('Select') }}</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback" id="category_id_error"></div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label required fw-bold">{{ __('Name') }}</label>
                        <input type="text" name="sub_category_name" id="sub_category_name" class="form-control" required>
                        <div class="invalid-feedback" id="sub_category_name_error"></div>
                    </div>

                    <div class="mb-5" id="sub_category_status_row" style="display: none;">
                        <label class="form-label fw-bold d-block">{{ __('Status') }}</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="sub_category_status" id="sub_category_status" value="1" checked>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="subCategoryFormSubmit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    const $modal   = $('#subCategoryFormModal');
    const $form    = $('#subCategoryForm');
    const $title   = $('#subCategoryFormModalTitle');
    const $idInput = $('#sub_category_id');

    $('#category_id').select2({
        dropdownParent: $modal,
        width: '100%',
    });

    function resetForm() {
        $form[0].reset();
        $idInput.val('');
        $('#category_id').val('').trigger('change');
        $('#sub_category_status').prop('checked', true);
        $('#sub_category_status_row').hide();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }

    $('#openAddSubCategoryModal').on('click', function () {
        resetForm();
        $title.text('{{ __('Add Sub Category') }}');
        $modal.modal('show');
    });

    window.openSubCategoryEditModal = function (data) {
        resetForm();
        $title.text('{{ __('Edit Sub Category') }}');
        $idInput.val(data.id);
        $('#category_id').val(data.category_id).trigger('change');
        $('#sub_category_name').val(data.name);
        $('#sub_category_status').prop('checked', !!data.status);
        $('#sub_category_status_row').show();
        $modal.modal('show');
    };

    $modal.on('hidden.bs.modal', function () {
        $('#subCategoryListDatatable tbody tr').removeClass('table-warning editing-row');
    });

    $form.on('submit', function (e) {
        e.preventDefault();

        const id  = $idInput.val();
        const url = id ? "{{ url('sub-categories') }}/" + id + '/update' : "{{ route('sub-categories.store') }}";

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        $.ajax({
            url: url,
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                toastr.success(response.message || '{{ __('Saved successfully.') }}');
                $modal.modal('hide');
                $('#subCategoryListDatatable').DataTable().ajax.reload(null, false);
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
