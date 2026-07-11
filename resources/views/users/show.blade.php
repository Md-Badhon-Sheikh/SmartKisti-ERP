<x-app-layout title="ইউজার বিবরণ">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-5">
                <div class="d-flex flex-column align-items-center gap-2">
                    <div class="symbol symbol-100px symbol-lg-100px">
                        @if ($user->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar) }}" class="rounded-circle" alt="{{ $user->name }}">
                        @else
                            <div class="symbol-label bg-light-primary text-primary fw-bold fs-1">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="text-gray-900 fs-2 fw-bolder my-3">{{ $user->name }}</h3>
                    <h5 class="text-gray-600 fw-semibold mb-0 fs-6">মোবাইলঃ {{ $user->mobile }}</h5>
                    @if ($user->email)
                        <h5 class="text-gray-600 fw-semibold mb-0 fs-6">ইমেইলঃ {{ $user->email }}</h5>
                    @endif
                    <h5 class="text-gray-600 fw-semibold mb-0 fs-6">
                        রোলঃ {{ $user->roles->pluck('name')->implode(', ') ?: '—' }}
                    </h5>

                    <div class="d-flex flex-column flex-md-row align-items-center gap-4 pt-4 w-100">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary fs-6 w-100">সম্পাদনা করুন</a>
                        <a href="{{ route('users.index') }}" class="btn btn-light fs-6 w-100">তালিকায় ফিরুন</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
