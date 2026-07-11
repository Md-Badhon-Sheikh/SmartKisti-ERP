<x-app-layout title="প্রোফাইল সম্পাদনা">
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">{{ auth()->user()->name }}'s Profile</h3>
            </div>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">অ্যাভাটার</label>
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
                        <input type="file" name="avatar" accept=".png,.jpg,.jpeg" class="form-control form-control-solid">
                        @error('avatar')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">নাম</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">মোবাইল নম্বর</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="mobile" class="form-control form-control-lg form-control-solid" value="{{ old('mobile', auth()->user()->mobile) }}" required>
                        @error('mobile')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">ইমেইল</label>
                    <div class="col-lg-8 fv-row">
                        <input type="email" name="email" class="form-control form-control-lg form-control-solid" value="{{ old('email', auth()->user()->email) }}">
                        @error('email')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">নতুন পাসওয়ার্ড</label>
                    <div class="col-lg-8 fv-row">
                        <input type="password" name="password" class="form-control form-control-lg form-control-solid" placeholder="পরিবর্তন করতে না চাইলে খালি রাখুন">
                        @error('password')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">পাসওয়ার্ড নিশ্চিত করুন</label>
                    <div class="col-lg-8 fv-row">
                        <input type="password" name="password_confirmation" class="form-control form-control-lg form-control-solid">
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('profile.show') }}" class="btn btn-light btn-active-light-primary me-2">বাতিল</a>
                <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
</x-app-layout>
