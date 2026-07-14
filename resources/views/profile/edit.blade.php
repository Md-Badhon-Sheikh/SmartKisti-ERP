<x-app-layout :title="__('Edit Profile')">
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">{{ __(":name's Profile", ['name' => auth()->user()->name]) }}</h3>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Avatar') }}</label>
                    <div class="col-lg-8">
                        <div class="symbol symbol-100px mb-3">
                            @if (auth()->user()->avatar)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar) }}" class="rounded-circle" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="symbol-label bg-light-primary text-primary fw-bold fs-1">
                                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-light-primary" id="btnUploadAvatar">
                                <i class="fas fa-upload me-1"></i>{{ __('Upload') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-light-info" id="btnCaptureAvatar">
                                <i class="fas fa-camera me-1"></i>{{ __('Capture') }}
                            </button>
                        </div>
                        <input type="file" name="avatar" id="avatar" accept=".png,.jpg,.jpeg" class="d-none">
                        @error('avatar')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Name') }}</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Mobile Number') }}</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="mobile" class="form-control form-control-lg form-control-solid" value="{{ old('mobile', auth()->user()->mobile) }}" required>
                        @error('mobile')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Email') }}</label>
                    <div class="col-lg-8 fv-row">
                        <input type="email" name="email" class="form-control form-control-lg form-control-solid" value="{{ old('email', auth()->user()->email) }}">
                        @error('email')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('New Password') }}</label>
                    <div class="col-lg-8 fv-row">
                        <input type="password" name="password" class="form-control form-control-lg form-control-solid" placeholder="{{ __('Leave blank to keep unchanged') }}">
                        @error('password')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Confirm Password') }}</label>
                    <div class="col-lg-8 fv-row">
                        <input type="password" name="password_confirmation" class="form-control form-control-lg form-control-solid">
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('profile.show') }}" class="btn btn-light btn-active-light-primary me-2">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>

    @include('components.camera-capture-modal')

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var $avatarInput = $('#avatar');
            var $symbol      = $('.symbol.symbol-100px');

            function setPreview(src) {
                var $img = $symbol.find('img');
                if (!$img.length) {
                    $symbol.empty().append('<img src="' + src + '" class="rounded-circle" alt="">');
                } else {
                    $img.attr('src', src);
                }
            }

            function setFileInput(input, file) {
                var dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                input.files = dataTransfer.files;
            }

            $('#btnUploadAvatar').on('click', function () {
                $avatarInput.trigger('click');
            });

            $avatarInput.on('change', function () {
                var file = this.files[0];
                if (!file) return;

                var reader = new FileReader();
                reader.onload = function (e) { setPreview(e.target.result); };
                reader.readAsDataURL(file);
            });

            $('#btnCaptureAvatar').on('click', function () {
                CameraCapture.open(function (file, url) {
                    setFileInput($avatarInput[0], file);
                    setPreview(url);
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
