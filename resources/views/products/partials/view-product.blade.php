<div class="modal fade" id="productViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Product Details') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productViewModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    window.renderProductViewModal = function (data) {
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

        let gallery = '';
        if (data.images && data.images.length) {
            gallery = '<div class="row mb-4"><div class="col-12 d-flex flex-wrap gap-3">'
                + data.images.map(function (image) {
                    return '<a href="' + image.url + '" target="_blank">'
                        + '<img src="' + image.url + '" style="width:90px;height:90px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">'
                        + '</a>';
                }).join('')
                + '</div></div>';
        }

        const html = ''
            + gallery
            + row('{{ __('Name') }}', esc(data.name))
            + row('{{ __('Category') }}', esc(data.category_name))
            + row('{{ __('Sub Category') }}', esc(data.sub_category_name))
            + row('{{ __('Ready or Custom Product') }}', badge(data.product_type === 'ready', '{{ __('Ready') }}', '{{ __('Custom') }}'))
            + row('{{ __('Brand') }}', data.brand_name ? esc(data.brand_name) : '')
            + row('{{ __('Model Number') }}', data.model ? esc(data.model) : '')
            + row('{{ __('IMEI / Serial Number') }}', data.imei_serial ? esc(data.imei_serial) : '')
            + row('{{ __('Manufacturer') }}', data.manufacturer_name ? esc(data.manufacturer_name) : '')
            + row('{{ __('Wood Type') }}', data.wood_type_name ? esc(data.wood_type_name) : '')
            + row('{{ __('Color') }}', data.color_name ? esc(data.color_name) : '')
            + row('{{ __('Size') }}', data.size ? esc(data.size) : '')
            + row('{{ __('Polish') }}', data.polish ? esc(data.polish) : '')
            + row('{{ __('Warranty') }}', data.warranty ? esc(data.warranty) : '')
            + row('SKU', data.sku ? esc(data.sku) : '')
            + row('{{ __('Purchase Price') }}', Number(data.purchase_price).toFixed(2))
            + row('{{ __('Selling Price') }}', Number(data.selling_price).toFixed(2))
            + (data.product_type === 'ready' ? row('{{ __('Stock') }}', data.stock) : '')
            + row('{{ __('Status') }}', badge(data.status, '{{ __('Active') }}', '{{ __('Inactive') }}'))
            + row('{{ __('Created At') }}', data.created_at ?? '—');

        $('#productViewModalBody').html(html);
    };
</script>
@endpush
