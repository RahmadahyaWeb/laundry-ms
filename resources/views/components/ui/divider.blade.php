@props(['title' => '', 'class' => ''])

<div {{ $attributes->merge(['class' => 'flex items-center ' . $class]) }}>
    <div class="flex-grow border-t border-gray-300"></div>
    <span class="mx-4 text-sm text-gray-500 font-semibold">{{ $title }}</span>
    <div class="flex-grow border-t border-gray-300"></div>
</div>
