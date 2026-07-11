<x-guest-layout title="নতুন পাসওয়ার্ড">
    <h2 class="mb-6 text-center text-lg font-semibold text-gray-900 dark:text-white">নতুন পাসওয়ার্ড সেট করুন</h2>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">নতুন পাসওয়ার্ড</label>
            <input
                id="password"
                name="password"
                type="password"
                required
                autofocus
                autocomplete="new-password"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >
            @error('password')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">পাসওয়ার্ড নিশ্চিত করুন</label>
            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
            >
        </div>

        <button
            type="submit"
            class="w-full rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
        >
            পাসওয়ার্ড পরিবর্তন করুন
        </button>
    </form>
</x-guest-layout>
