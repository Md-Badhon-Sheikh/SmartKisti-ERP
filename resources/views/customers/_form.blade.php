@php
    $customer = $customer ?? null;
    $existingGuarantors = $customer ? $customer->guarantors->map(fn ($g) => [
        'id'        => $g->id,
        'name'      => $g->name,
        'relation'  => $g->relation,
        'mobile'    => $g->mobile,
        'nid'       => $g->nid,
        'address'   => $g->address,
        'photo_url' => $g->photo ? asset($g->photo) : null,
    ]) : collect();
@endphp

<div class="row">
    @if ($customer)
        <div class="col-md-4 mb-6">
            <label class="col-form-label fw-bold fs-6 d-block">{{ __('Customer Code') }}</label>
            <input type="text" class="form-control form-control-solid" value="{{ $customer->customer_code }}" disabled readonly>
        </div>
    @endif

    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Name') }}</label>
        <input type="text" name="name" class="form-control form-control-solid" placeholder="{{ __('Enter Customer Name') }}" value="{{ old('name', $customer?->name) }}" required>
        @error('name')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Area') }}</label>
        <select name="area_id" id="area_id" class="form-select form-select-solid" required>
            <option value="" disabled @selected(! old('area_id', $customer?->area_id))>{{ __('Select') }}</option>
            @foreach ($areas as $area)
                <option value="{{ $area->id }}" @selected(old('area_id', $customer?->area_id) == $area->id)>{{ $area->name }}</option>
            @endforeach
        </select>
        @error('area_id')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Mobile') }}</label>
        <input type="text" name="mobile" class="form-control form-control-solid" placeholder="{{ __('e.g. 01712345678') }}" value="{{ old('mobile', $customer?->mobile) }}" required>
        @error('mobile')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Alternative Mobile') }}</label>
        <input type="text" name="alternative_mobile" class="form-control form-control-solid" placeholder="{{ __('e.g. 01812345678') }}" value="{{ old('alternative_mobile', $customer?->alternative_mobile) }}">
        @error('alternative_mobile')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('NID') }}</label>
        <input type="text" name="nid" class="form-control form-control-solid" placeholder="{{ __('Enter NID Number') }}" value="{{ old('nid', $customer?->nid) }}">
        @error('nid')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Gender') }}</label>
        <select name="gender" id="gender" class="form-select form-select-solid">
            <option value="">{{ __('Select') }}</option>
            <option value="male" @selected(old('gender', $customer?->gender) === 'male')>{{ __('Male') }}</option>
            <option value="female" @selected(old('gender', $customer?->gender) === 'female')>{{ __('Female') }}</option>
            <option value="other" @selected(old('gender', $customer?->gender) === 'other')>{{ __('Other') }}</option>
        </select>
        @error('gender')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Father Name') }}</label>
        <input type="text" name="father_name" class="form-control form-control-solid" placeholder="{{ __('Enter Father Name') }}" value="{{ old('father_name', $customer?->father_name) }}">
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Occupation') }}</label>
        <input type="text" name="occupation" class="form-control form-control-solid" placeholder="{{ __('e.g. Business') }}" value="{{ old('occupation', $customer?->occupation) }}">
    </div>

    <div class="col-md-8 mb-6">
        <label class="col-form-label required fw-bold fs-6">{{ __('Address') }}</label>
        <textarea name="address" class="form-control form-control-solid" rows="2" required>{{ old('address', $customer?->address) }}</textarea>
        @error('address')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8 mb-6">
        <label class="col-form-label fw-bold fs-6">{{ __('Description') }}</label>
        <textarea name="description" class="form-control form-control-solid" rows="2">{{ old('description', $customer?->description) }}</textarea>
        @error('description')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6 d-block">{{ __('Photo') }}</label>
        @if ($customer && $customer->photo)
            <div class="mb-2">
                <img src="{{ asset($customer->photo) }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remove_photo" id="remove_photo" value="1">
                    <label class="form-check-label fs-8" for="remove_photo">{{ __('Remove current photo') }}</label>
                </div>
            </div>
        @endif
        <input type="file" name="photo" id="photo" class="form-control form-control-solid" accept="image/*">
        @error('photo')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6 d-block">{{ __('NID Image') }}</label>
        @if ($customer && $customer->nid_image)
            <div class="mb-2">
                <img src="{{ asset($customer->nid_image) }}" style="width:80px;height:80px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">
                <div class="form-check mt-1">
                    <input class="form-check-input" type="checkbox" name="remove_nid_image" id="remove_nid_image" value="1">
                    <label class="form-check-label fs-8" for="remove_nid_image">{{ __('Remove current NID image') }}</label>
                </div>
            </div>
        @endif
        <input type="file" name="nid_image" id="nid_image" class="form-control form-control-solid" accept="image/*">
        @error('nid_image')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6 d-block">{{ __('Other Documents') }}</label>
        <input type="file" name="other_documents[]" id="other_documents" class="form-control form-control-solid" multiple>
        <div class="form-text">{{ __('You can attach multiple documents (PDF, images, etc).') }}</div>
        @error('other_documents')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
        <div id="documents_list_container" class="d-flex flex-column gap-2 mt-3">
            @if ($customer && $customer->documents->count())
                @foreach ($customer->documents as $document)
                    <div class="d-flex align-items-center gap-2 existing-document" data-document-id="{{ $document->id }}">
                        <a href="{{ asset($document->file_path) }}" target="_blank"><i class="fas fa-paperclip me-1"></i>{{ $document->file_name }}</a>
                        <button type="button" class="btn btn-sm btn-icon btn-light-danger btn-delete-existing-document" data-document-id="{{ $document->id }}" data-customer-id="{{ $customer->id }}" style="width:22px;height:22px;">
                            <i class="fas fa-times fs-8"></i>
                        </button>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @if ($customer)
        <div class="col-md-4 mb-6">
            <label class="col-form-label fw-bold fs-6 d-block">{{ __('Status') }}</label>
            <select name="status" id="status" class="form-select form-select-solid">
                <option value="1" @selected(old('status', $customer->status))>{{ __('Active') }}</option>
                <option value="0" @selected(! old('status', $customer->status))>{{ __('Inactive') }}</option>
            </select>
        </div>
    @endif
</div>

<div class="mt-6">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0">{{ __('Guarantors') }}</h5>
        <button type="button" class="btn btn-sm btn-light-primary" id="addGuarantorRow">
            <i class="fas fa-plus me-1"></i>{{ __('Add Guarantor') }}
        </button>
    </div>
    <div id="guarantors_container"></div>
    <div id="removed_guarantor_ids_container"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#area_id').select2({ width: '100%' });
        $('#gender').select2({ width: '100%' });
        $('#status').select2({ width: '100%' });

        var guarantorIndex = 0;
        var existingGuarantors = @json($existingGuarantors);

        function escAttr(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function buildGuarantorRow(index, data) {
            data = data || {};

            var photoPreview = data.photo_url
                ? '<img src="' + data.photo_url + '" style="width:60px;height:60px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;">'
                : '';

            return ''
                + '<div class="border rounded p-4 mb-3 guarantor-row" data-index="' + index + '">'
                +   '<input type="hidden" name="guarantors[' + index + '][id]" value="' + (data.id || '') + '">'
                +   '<div class="row">'
                +     '<div class="col-md-3 mb-3">'
                +       '<label class="form-label fw-bold">{{ __('Name') }}</label>'
                +       '<input type="text" name="guarantors[' + index + '][name]" class="form-control" value="' + escAttr(data.name) + '">'
                +     '</div>'
                +     '<div class="col-md-3 mb-3">'
                +       '<label class="form-label fw-bold">{{ __('Relation') }}</label>'
                +       '<input type="text" name="guarantors[' + index + '][relation]" class="form-control" value="' + escAttr(data.relation) + '">'
                +     '</div>'
                +     '<div class="col-md-3 mb-3">'
                +       '<label class="form-label fw-bold">{{ __('Mobile') }}</label>'
                +       '<input type="text" name="guarantors[' + index + '][mobile]" class="form-control" value="' + escAttr(data.mobile) + '">'
                +     '</div>'
                +     '<div class="col-md-3 mb-3">'
                +       '<label class="form-label fw-bold">NID</label>'
                +       '<input type="text" name="guarantors[' + index + '][nid]" class="form-control" value="' + escAttr(data.nid) + '">'
                +     '</div>'
                +     '<div class="col-md-6 mb-3">'
                +       '<label class="form-label fw-bold">{{ __('Address') }}</label>'
                +       '<input type="text" name="guarantors[' + index + '][address]" class="form-control" value="' + escAttr(data.address) + '">'
                +     '</div>'
                +     '<div class="col-md-4 mb-3">'
                +       '<label class="form-label fw-bold d-block">{{ __('Photo') }}</label>'
                +       '<div class="d-flex align-items-center gap-2">'
                +         photoPreview
                +         '<input type="file" name="guarantors[' + index + '][photo]" class="form-control" accept="image/*">'
                +       '</div>'
                +     '</div>'
                +     '<div class="col-md-2 mb-3 d-flex align-items-end">'
                +       '<button type="button" class="btn btn-light-danger btn-sm remove-guarantor-row">'
                +         '<i class="fas fa-trash me-1"></i>{{ __('Remove') }}'
                +       '</button>'
                +     '</div>'
                +   '</div>'
                + '</div>';
        }

        existingGuarantors.forEach(function (guarantor) {
            $('#guarantors_container').append(buildGuarantorRow(guarantorIndex++, guarantor));
        });

        $('#addGuarantorRow').on('click', function () {
            $('#guarantors_container').append(buildGuarantorRow(guarantorIndex++));
        });

        $(document).on('click', '.remove-guarantor-row', function () {
            var $row = $(this).closest('.guarantor-row');
            var existingId = $row.find('input[name$="[id]"]').val();

            if (existingId) {
                $('#removed_guarantor_ids_container').append(
                    '<input type="hidden" name="removed_guarantor_ids[]" value="' + existingId + '">'
                );
            }

            $row.remove();
        });

        $(document).on('click', '.btn-delete-existing-document', function () {
            var documentId = $(this).data('document-id');
            var customerId = $(this).data('customer-id');
            var $wrapper = $(this).closest('.existing-document');

            $.ajax({
                url: '{{ url('customers') }}/' + customerId + '/documents/' + documentId + '/delete',
                type: 'POST',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    toastr.success(response.message || '{{ __('Document deleted successfully.') }}');
                    $wrapper.remove();
                },
                error: function (xhr) {
                    toastr.error(xhr.responseJSON?.message || '{{ __('Delete failed.') }}');
                }
            });
        });
    });
</script>
@endpush
