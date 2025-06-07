@props([
    'type' => 'button',
    'block' => false,
    'color' => 'blue', // default ke 'blue'
])

@php
    $colors = [
        'blue' => 'text-white bg-blue-700 hover:bg-blue-800 focus:ring-blue-300',
        'red' => 'text-white bg-red-700 hover:bg-red-800 focus:ring-red-300',
        'green' => 'text-white bg-green-700 hover:bg-green-800 focus:ring-green-300',
        'gray' => 'text-white bg-gray-700 hover:bg-gray-800 focus:ring-gray-300',
        'yellow' => 'text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-300',
        'emerald' => 'text-white bg-emerald-700 hover:bg-emerald-800 focus:ring-emerald-300',
    ];

    $baseClass = $colors[$color] ?? $colors['blue'];
    $baseClass .= ' focus:ring-4 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2';

    if ($block) {
        $baseClass .= ' w-full';
    }
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $baseClass]) }}>
    {{ $slot }}
</button>
