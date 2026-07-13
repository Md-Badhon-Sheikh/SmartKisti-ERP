<div class="modal fade" id="categoryViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Category Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="categoryViewModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.renderCategoryViewModal = function (data) {
        const badge = (ok, yesLabel, noLabel) =>
            '<span class="badge ' + (ok ? 'badge-light-success' : 'badge-light-danger') + '">'
            + (ok ? yesLabel : noLabel) + '</span>';

        const html = ''
            + '<div class="row mb-4">'
            +   '<label class="col-5 fw-bold text-muted">{{ __('Name') }}</label>'
            +   '<div class="col-7">' + $('<div>').text(data.name).html() + '</div>'
            + '</div>'
            + '<div class="row mb-4">'
            +   '<label class="col-5 fw-bold text-muted">{{ __('Brand Required') }}</label>'
            +   '<div class="col-7">' + badge(data.brand_required, '{{ __('Yes') }}', '{{ __('No') }}') + '</div>'
            + '</div>'
            + '<div class="row mb-4">'
            +   '<label class="col-5 fw-bold text-muted">{{ __('Status') }}</label>'
            +   '<div class="col-7">' + badge(data.status, '{{ __('Active') }}', '{{ __('Inactive') }}') + '</div>'
            + '</div>'
            + '<div class="row mb-4">'
            +   '<label class="col-5 fw-bold text-muted">{{ __('Products') }}</label>'
            +   '<div class="col-7">' + data.products_count + '</div>'
            + '</div>'
            + '<div class="row">'
            +   '<label class="col-5 fw-bold text-muted">{{ __('Created At') }}</label>'
            +   '<div class="col-7">' + (data.created_at ?? '—') + '</div>'
            + '</div>';

        $('#categoryViewModalBody').html(html);
    };
</script>
@endpush
