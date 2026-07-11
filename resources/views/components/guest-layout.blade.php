<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'SmartKisti ERP') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.0.0/css/flag-icons.min.css">
    @fonts
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-50 antialiased dark:bg-gray-950">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
        <div class="mb-4 w-full max-w-sm flex justify-end">
            <x-language-selector />
        </div>

        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">SmartKisti ERP</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Furniture &amp; Electronics Sales &amp; Installment Management</p>
        </div>

        <div class="w-full max-w-sm rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            {{ $slot }}
        </div>
    </div>
    @stack('scripts')
</body>
</html>
