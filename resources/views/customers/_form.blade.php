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
    $photoPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23b5b5c3'%3E%3Ccircle cx='12' cy='8' r='4'/%3E%3Cpath d='M4 20c0-4 3.5-7 8-7s8 3 8 7'/%3E%3C/svg%3E";
@endphp

<div class="d-flex flex-column align-items-center mb-8">
    <div class="position-relative" style="width:130px;height:130px;">
        <img id="customer_photo_preview"
             src="{{ $customer && $customer->photo ? asset($customer->photo) : $photoPlaceholder }}"
             style="width:130px;height:130px;object-fit:cover;border-radius:50%;border:2px solid #e4e6ef;background:#f5f5f5;">
    </div>

    <div class="d-flex gap-2 mt-3">
        <button type="button" class="btn btn-sm btn-light-primary" id="btnUploadPhoto">
            <i class="fas fa-upload me-1"></i>{{ __('Upload') }}
        </button>
        <button type="button" class="btn btn-sm btn-light-info" id="btnCapturePhoto">
            <i class="fas fa-camera me-1"></i>{{ __('Capture') }}
        </button>
    </div>

    @if ($customer && $customer->photo)
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" name="remove_photo" id="remove_photo" value="1">
            <label class="form-check-label fs-8" for="remove_photo">{{ __('Remove current photo') }}</label>
        </div>
    @endif

    <input type="file" name="photo" id="photo" class="d-none" accept="image/*">
    @error('photo')
        <div class="text-danger fs-7 mt-1">{{ $message }}</div>
    @enderror
</div>

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
        <label class="col-form-label fw-bold fs-6 d-block">{{ __('NID Image') }}</label>
        <div class="d-flex align-items-center gap-3 mb-2">
            <img id="nid_image_preview"
                 src="{{ $customer && $customer->nid_image ? asset($customer->nid_image) : $photoPlaceholder }}"
                 style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;background:#f5f5f5;">
            <div class="d-flex flex-column gap-2">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-light-primary" id="btnUploadNidImage">
                        <i class="fas fa-upload me-1"></i>{{ __('Upload') }}
                    </button>
                    <button type="button" class="btn btn-sm btn-light-info" id="btnCaptureNidImage">
                        <i class="fas fa-camera me-1"></i>{{ __('Capture') }}
                    </button>
                </div>
                @if ($customer && $customer->nid_image)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_nid_image" id="remove_nid_image" value="1">
                        <label class="form-check-label fs-8" for="remove_nid_image">{{ __('Remove current NID image') }}</label>
                    </div>
                @endif
            </div>
        </div>
        <input type="file" name="nid_image" id="nid_image" class="d-none" accept="image/*">
        @error('nid_image')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-6">
        <label class="col-form-label fw-bold fs-6 d-block">{{ __('Other Documents') }}</label>
        <div class="d-flex gap-2 mb-2">
            <button type="button" class="btn btn-sm btn-light-primary" id="btnUploadDocuments">
                <i class="fas fa-upload me-1"></i>{{ __('Upload') }}
            </button>
            <button type="button" class="btn btn-sm btn-light-info" id="btnCaptureDocument">
                <i class="fas fa-camera me-1"></i>{{ __('Capture') }}
            </button>
        </div>
        <input type="file" name="other_documents[]" id="other_documents" class="d-none" multiple>
        <div class="form-text">{{ __('You can attach multiple documents (PDF, images, etc).') }}</div>
        @error('other_documents')
            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
        @enderror
        <div id="new_documents_preview" class="d-flex flex-column gap-2 mt-2"></div>
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

@include('components.camera-capture-modal')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#area_id').select2({ width: '100%' });
        $('#gender').select2({ width: '100%' });
        $('#status').select2({ width: '100%' });

        function setFileInput(input, file) {
            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;
        }

        (function () {
            var $photoInput   = $('#photo');
            var $photoPreview = $('#customer_photo_preview');
            var $removePhoto  = $('#remove_photo');

            $('#btnUploadPhoto').on('click', function () {
                $photoInput.trigger('click');
            });

            $photoInput.on('change', function () {
                var file = this.files[0];
                if (!file) return;

                var reader = new FileReader();
                reader.onload = function (e) {
                    $photoPreview.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);

                if ($removePhoto.length) $removePhoto.prop('checked', false);
            });

            $('#btnCapturePhoto').on('click', function () {
                CameraCapture.open(function (file, url) {
                    setFileInput($photoInput[0], file);
                    $photoPreview.attr('src', url);
                    if ($removePhoto.length) $removePhoto.prop('checked', false);
                });
            });
        })();

        (function () {
            var $nidInput   = $('#nid_image');
            var $nidPreview = $('#nid_image_preview');
            var $removeNid  = $('#remove_nid_image');

            $('#btnUploadNidImage').on('click', function () {
                $nidInput.trigger('click');
            });

            $nidInput.on('change', function () {
                var file = this.files[0];
                if (!file) return;

                var reader = new FileReader();
                reader.onload = function (e) {
                    $nidPreview.attr('src', e.target.result);
                };
                reader.readAsDataURL(file);

                if ($removeNid.length) $removeNid.prop('checked', false);
            });

            $('#btnCaptureNidImage').on('click', function () {
                CameraCapture.open(function (file, url) {
                    setFileInput($nidInput[0], file);
                    $nidPreview.attr('src', url);
                    if ($removeNid.length) $removeNid.prop('checked', false);
                });
            });
        })();

        (function () {
            var pendingDocuments = [];
            var $documentsInput  = $('#other_documents');
            var $preview         = $('#new_documents_preview');

            function renderPreview() {
                $preview.empty();
                pendingDocuments.forEach(function (file, index) {
                    $preview.append(
                        '<div class="d-flex align-items-center gap-2 pending-document">' +
                        '<i class="fas fa-paperclip me-1"></i><span>' + $('<div>').text(file.name).html() + '</span>' +
                        '<button type="button" class="btn btn-sm btn-icon btn-light-danger btn-remove-pending-document" data-index="' + index + '" style="width:22px;height:22px;">' +
                        '<i class="fas fa-times fs-8"></i></button>' +
                        '</div>'
                    );
                });
            }

            function syncInput() {
                var dataTransfer = new DataTransfer();
                pendingDocuments.forEach(function (file) { dataTransfer.items.add(file); });
                $documentsInput[0].files = dataTransfer.files;
            }

            $('#btnUploadDocuments').on('click', function () {
                $documentsInput.trigger('click');
            });

            $documentsInput.on('change', function () {
                Array.prototype.forEach.call(this.files, function (file) {
                    pendingDocuments.push(file);
                });
                syncInput();
                renderPreview();
            });

            $('#btnCaptureDocument').on('click', function () {
                CameraCapture.open(function (file) {
                    pendingDocuments.push(file);
                    syncInput();
                    renderPreview();
                });
            });

            $(document).on('click', '.btn-remove-pending-document', function () {
                var index = $(this).data('index');
                pendingDocuments.splice(index, 1);
                syncInput();
                renderPreview();
            });
        })();

        var guarantorIndex = 0;
        var existingGuarantors = @json($existingGuarantors);
        var photoPlaceholder = @json($photoPlaceholder);

        function escAttr(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function buildGuarantorRow(index, data) {
            data = data || {};

            var photoSrc = data.photo_url || photoPlaceholder;

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
                +         '<img class="guarantor-photo-preview" src="' + photoSrc + '" style="width:50px;height:50px;object-fit:cover;border-radius:6px;border:1px solid #e4e6ef;background:#f5f5f5;">'
                +         '<button type="button" class="btn btn-sm btn-light-primary btn-guarantor-upload"><i class="fas fa-upload"></i></button>'
                +         '<button type="button" class="btn btn-sm btn-light-info btn-guarantor-capture"><i class="fas fa-camera"></i></button>'
                +         '<input type="file" name="guarantors[' + index + '][photo]" class="d-none guarantor-photo-input" accept="image/*">'
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

        $(document).on('click', '.btn-guarantor-upload', function () {
            $(this).closest('.guarantor-row').find('.guarantor-photo-input').trigger('click');
        });

        $(document).on('change', '.guarantor-photo-input', function () {
            var file = this.files[0];
            if (!file) return;

            var $preview = $(this).closest('.guarantor-row').find('.guarantor-photo-preview');
            var reader = new FileReader();
            reader.onload = function (e) { $preview.attr('src', e.target.result); };
            reader.readAsDataURL(file);
        });

        $(document).on('click', '.btn-guarantor-capture', function () {
            var $row     = $(this).closest('.guarantor-row');
            var $input   = $row.find('.guarantor-photo-input');
            var $preview = $row.find('.guarantor-photo-preview');

            CameraCapture.open(function (file, url) {
                setFileInput($input[0], file);
                $preview.attr('src', url);
            });
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
