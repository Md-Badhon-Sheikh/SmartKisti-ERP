<x-guest-layout :title="__('Reset Password')">
    <h2 class="mb-2 text-center text-lg font-semibold text-gray-900 dark:text-white">{{ __('Forgot your password?') }}</h2>
    <p class="mb-6 text-center text-sm text-gray-500 dark:text-gray-400">
        {{ __('Enter your registered mobile number, we will send you a verification code.') }}
    </p>

    <form method="POST" action="{{ route('password.otp.send') }}" class="space-y-4">
        @csrf

        <div>
            <label for="mobile" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mobile Number') }}</label>
            <input
                id="mobile"
                name="mobile"
                type="text"
                value="{{ old('mobile') }}"
                required
                autofocus
                placeholder="01XXXXXXXXX"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >
            @error('mobile')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            {{ __('Send Code') }}
        </button>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('login') }}" class="text-indigo-600 hover:underline dark:text-indigo-400">{{ __('Back to Login') }}</a>
        </p>
    </form>
</x-guest-layout>
