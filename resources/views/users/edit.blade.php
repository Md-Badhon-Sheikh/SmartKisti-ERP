<x-app-layout title="ইউজার সম্পাদনা">
    <div class="card mb-5 mb-xl-10">
        <div class="card-header border-0 pt-6">
            <div class="card-title m-0">
                <h3 class="fw-bolder m-0">{{ $user->name }}'s Account</h3>
            </div>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body border-top p-9">
                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">নাম</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="name" class="form-control form-control-lg form-control-solid" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">মোবাইল নম্বর</label>
                    <div class="col-lg-8 fv-row">
                        <input type="text" name="mobile" class="form-control form-control-lg form-control-solid" value="{{ old('mobile', $user->mobile) }}" required>
                        @error('mobile')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label fw-bold fs-6">ইমেইল</label>
                    <div class="col-lg-8 fv-row">
                        <input type="email" name="email" class="form-control form-control-lg form-control-solid" value="{{ old('email', $user->email) }}">
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

                <div class="row mb-6">
                    <label class="col-lg-4 col-form-label required fw-bold fs-6">রোল নির্বাচন করুন</label>
                    <div class="col-lg-8 fv-row">
                        <select name="role" class="form-select form-select-solid form-select-lg" required @disabled($user->hasRole('super-admin'))>
                            @foreach ($roles as $role)
                                <option value="{{ $role }}" @selected(old('role', $user->roles->first()?->name) === $role)>{{ $role }}</option>
                            @endforeach
                            @if ($user->hasRole('super-admin'))
                                <option value="super-admin" selected>super-admin</option>
                            @endif
                        </select>
                        @if ($user->hasRole('super-admin'))
                            <input type="hidden" name="role" value="super-admin">
                            <div class="form-text">Super Admin-এর রোল এখান থেকে পরিবর্তন করা যাবে না।</div>
                        @endif
                        @error('role')
                            <div class="text-danger fs-7 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-end py-6 px-9">
                <a href="{{ route('users.index') }}" class="btn btn-light btn-active-light-primary me-2">বাতিল</a>
                <button type="submit" class="btn btn-primary">সংরক্ষণ করুন</button>
            </div>
        </form>
    </div>
</x-app-layout>
