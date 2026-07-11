<x-app-layout :title="__('Edit Category')">
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">{{ $category->name }}</h3>
            </div>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Name') }}</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $category->name) }}" required>
                        @error('name')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Brand Required') }}</label>
                    <div class="col-lg-8 fv-row">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input type="checkbox" name="brand_required" class="form-check-input" value="1" @checked(old('brand_required', $category->brand_required))>
                        </div>
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Status') }}</label>
                    <div class="col-lg-8 fv-row">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input type="checkbox" name="status" class="form-check-input" value="1" @checked(old('status', $category->status))>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('categories.index') }}" class="btn btn-light btn-active-light-primary me-2">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
