<x-app-layout title="আমার প্রোফাইল">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card p-5">
                <div class="d-flex flex-column align-items-center gap-2">
                    <div class="symbol symbol-100px symbol-lg-100px">
                        @if (auth()->user()->avatar)
                            <img src="{{ \Illuminate\Support\Facades\Storage::url(auth()->user()->avatar) }}" class="rounded-circle" alt="{{ auth()->user()->name }}">
                        @else
                            <div class="symbol-label bg-light-primary text-primary fw-bold fs-1">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h3 class="text-gray-900 fs-2 fw-bolder my-3">{{ auth()->user()->name }}</h3>
                    <h5 class="text-gray-600 fw-semibold mb-0 fs-6">মোবাইলঃ {{ auth()->user()->mobile }}</h5>
                    @if (auth()->user()->email)
                        <h5 class="text-gray-600 fw-semibold mb-0 fs-6">ইমেইলঃ {{ auth()->user()->email }}</h5>
                    @endif
                    <h5 class="text-gray-600 fw-semibold mb-0 fs-6">
                        রোলঃ {{ auth()->user()->getRoleNames()->implode(', ') ?: '—' }}
                    </h5>

                    <div class="d-flex flex-column flex-md-row align-items-center gap-4 pt-4 w-100">
                        <a href="{{ route('profile.edit') }}" class="btn btn-primary fs-6 w-100">প্রোফাইল সম্পাদনা করুন</a>
                        <form method="POST" action="{{ route('logout') }}" class="w-100">
                            @csrf
                            <button type="submit" class="btn btn-danger fs-6 w-100">লগ আউট</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
