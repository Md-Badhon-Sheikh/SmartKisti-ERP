<x-guest-layout title="কোড ভেরিফাই করুন">
    <h2 class="mb-2 text-center text-lg font-semibold text-gray-900 dark:text-white">ভেরিফিকেশন কোড দিন</h2>
    <p class="mb-6 text-center text-sm text-gray-500 dark:text-gray-400">
        <span dir="ltr">{{ $mobile }}</span> নম্বরে পাঠানো ৬ ডিজিটের কোডটি লিখুন।
    </p>

    @if (session('status'))
        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.otp.verify') }}" class="space-y-4">
        @csrf

        <div>
            <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ভেরিফিকেশন কোড</label>
            <input
                id="otp"
                name="otp"
                type="text"
                inputmode="numeric"
                maxlength="6"
                autofocus
                required
                placeholder="৬ ডিজিটের কোড"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-center text-lg tracking-widest shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >
            @error('otp')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            যাচাই করুন
        </button>
    </form>

    <form method="POST" action="{{ route('password.otp.resend') }}" class="mt-4 text-center">
        @csrf
        <button type="submit" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">
            কোড পাননি? আবার পাঠান
        </button>
    </form>
</x-guest-layout>
