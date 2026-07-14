<div class="modal fade" id="customerViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Customer Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="customerViewModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.renderCustomerViewModal = function (data) {
        const badge = (ok, yesLabel, noLabel) =>
            '<span class="badge ' + (ok ? 'badge-light-success' : 'badge-light-danger') + '">'
            + (ok ? yesLabel : noLabel) + '</span>';

        const row = (label, value) => value
            ? '<div class="row mb-4">'
              + '<label class="col-5 fw-bold text-muted">' + label + '</label>'
              + '<div class="col-7">' + value + '</div>'
              + '</div>'
            : '';

        const esc = (value) => $('<div>').text(value ?? '').html();

        let photoBlock = '';
        if (data.photo_url) {
            photoBlock = '<a href="' + data.photo_url + '" target="_blank">'
                + '<img src="' + data.photo_url + '" style="width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">'
                + '</a>';
        }

        let nidImageBlock = '';
        if (data.nid_image_url) {
            nidImageBlock = '<a href="' + data.nid_image_url + '" target="_blank">'
                + '<img src="' + data.nid_image_url + '" style="width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">'
                + '</a>';
        }

        let topGallery = '';
        if (photoBlock || nidImageBlock) {
            topGallery = '<div class="row mb-4"><div class="col-12 d-flex flex-wrap gap-3">'
                + photoBlock + nidImageBlock
                + '</div></div>';
        }

        let documents = '';
        if (data.documents && data.documents.length) {
            documents = '<div class="row mb-4">'
                + '<label class="col-5 fw-bold text-muted">{{ __('Documents') }}</label>'
                + '<div class="col-7 d-flex flex-column gap-1">'
                + data.documents.map(function (doc) {
                    return '<a href="' + doc.url + '" target="_blank"><i class="fas fa-paperclip me-1"></i>' + esc(doc.name) + '</a>';
                }).join('')
                + '</div></div>';
        }

        let guarantors = '';
        if (data.guarantors && data.guarantors.length) {
            guarantors = '<hr class="my-4">'
                + '<h6 class="fw-bold mb-3">{{ __('Guarantors') }}</h6>'
                + data.guarantors.map(function (g) {
                    return '<div class="d-flex align-items-start gap-3 mb-3 pb-3 border-bottom">'
                        + (g.photo_url ? '<img src="' + g.photo_url + '" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">' : '')
                        + '<div>'
                        + '<div class="fw-bold">' + esc(g.name) + (g.relation ? ' <span class="text-muted fw-normal">(' + esc(g.relation) + ')</span>' : '') + '</div>'
                        + '<div class="text-muted fs-7">' + esc(g.mobile) + (g.nid ? ' • NID: ' + esc(g.nid) : '') + '</div>'
                        + (g.address ? '<div class="text-muted fs-7">' + esc(g.address) + '</div>' : '')
                        + '</div>'
                        + '</div>';
                }).join('');
        }

        const html = ''
            + topGallery
            + row('{{ __('Customer Code') }}', esc(data.customer_code))
            + row('{{ __('Name') }}', esc(data.name))
            + row('{{ __('Mobile') }}', esc(data.mobile))
            + row('{{ __('Alternative Mobile') }}', data.alternative_mobile ? esc(data.alternative_mobile) : '')
            + row('NID', data.nid ? esc(data.nid) : '')
            + row('{{ __('Gender') }}', data.gender ? esc(data.gender.charAt(0).toUpperCase() + data.gender.slice(1)) : '')
            + row('{{ __('Father Name') }}', data.father_name ? esc(data.father_name) : '')
            + row('{{ __('Occupation') }}', data.occupation ? esc(data.occupation) : '')
            + row('{{ __('Area') }}', esc(data.area_name))
            + row('{{ __('Address') }}', esc(data.address))
            + row('{{ __('Description') }}', data.description ? esc(data.description) : '')
            + documents
            + row('{{ __('Status') }}', badge(data.status, '{{ __('Active') }}', '{{ __('Inactive') }}'))
            + row('{{ __('Created By') }}', data.created_by ? esc(data.created_by) : '')
            + row('{{ __('Created At') }}', data.created_at ?? '—')
            + guarantors;

        $('#customerViewModalBody').html(html);
    };
</script>
@endpush
