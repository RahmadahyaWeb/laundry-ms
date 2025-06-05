<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <x-sidebar />

    <div class="p-4 sm:ml-64">
        <div class="p-4 mt-14">
            {{-- Slot Breadcrumb --}}
            <x-ui.breadcrumbs :items="$breadcrumbs" />

            {{-- Slot Header --}}
            @isset($header)
                <h2 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-4xl">
                    {{ $header }}
                </h2>

                <livewire:components.alert />
            @endisset

            {{-- Slot Content --}}
            {{ $slot }}
        </div>
    </div>

    {{-- <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script> --}}

    @stack('scripts')
</body>

</html>
