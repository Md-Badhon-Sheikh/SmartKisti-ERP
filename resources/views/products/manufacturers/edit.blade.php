<x-app-layout :title="__('Edit Manufacturer')">
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">{{ $manufacturer->name }}</h3>
            </div>
        </div>

        <form action="{{ route('manufacturers.update', $manufacturer) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Name') }}</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $manufacturer->name) }}" required>
                        @error('name')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">{{ __('Type') }}</label>
                    <div class="col-lg-8 fv-row">
                        <select name="type" class="form-select form-select-solid form-select-lg" required>
                            <option value="own_factory" @selected(old('type', $manufacturer->type) === 'own_factory')>{{ __('Own Factory') }}</option>
                            <option value="local_carpenter" @selected(old('type', $manufacturer->type) === 'local_carpenter')>{{ __('Local Carpenter') }}</option>
                            <option value="outside_factory" @selected(old('type', $manufacturer->type) === 'outside_factory')>{{ __('Outside Factory') }}</option>
                        </select>
                        @error('type')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">{{ __('Status') }}</label>
                    <div class="col-lg-8 fv-row">
                        <div class="form-check form-switch form-check-custom form-check-solid">
                            <input type="checkbox" name="status" class="form-check-input" value="1" @checked(old('status', $manufacturer->status))>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('manufacturers.index') }}" class="btn btn-light btn-active-light-primary me-2">{{ __('Cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
