<div class="modal fade" id="areaFormModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="areaForm">
                @csrf
                <input type="hidden" name="area_id" id="area_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="areaFormModalTitle">{{ __('Add Area') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-5">
                        <label class="form-label required fw-bold">{{ __('Name') }}</label>
                        <input type="text" name="area_name" id="area_name" class="form-control" required>
                        <div class="invalid-feedback" id="area_name_error"></div>
                    </div>

                    <div class="mb-5" id="area_status_row" style="display: none;">
                        <label class="form-label fw-bold d-block">{{ __('Status') }}</label>
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" name="area_status" id="area_status" value="1" checked>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary" id="areaFormSubmit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(function () {
    const $modal   = $('#areaFormModal');
    const $form    = $('#areaForm');
    const $title   = $('#areaFormModalTitle');
    const $idInput = $('#area_id');

    function resetForm() {
        $form[0].reset();
        $idInput.val('');
        $('#area_status').prop('checked', true);
        $('#area_status_row').hide();
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');
    }

    $('#openAddAreaModal').on('click', function () {
        resetForm();
        $title.text('{{ __('Add Area') }}');
        $modal.modal('show');
    });

    window.openAreaEditModal = function (data) {
        resetForm();
        $title.text('{{ __('Edit Area') }}');
        $idInput.val(data.id);
        $('#area_name').val(data.name);
        $('#area_status').prop('checked', !!data.status);
        $('#area_status_row').show();
        $modal.modal('show');
    };

    $modal.on('hidden.bs.modal', function () {
        $('#areaListDatatable tbody tr').removeClass('table-warning editing-row');
    });

    $form.on('submit', function (e) {
        e.preventDefault();

        const id  = $idInput.val();
        const url = id ? "{{ url('areas') }}/" + id + '/update' : "{{ route('areas.store') }}";

        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.invalid-feedback').text('');

        $.ajax({
            url: url,
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                toastr.success(response.message || '{{ __('Saved successfully.') }}');
                $modal.modal('hide');
                $('#areaListDatatable').DataTable().ajax.reload(null, false);
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
