@props(['id', 'label', 'value'])

<div class="flex items-center space-x-2">
    <input id="{{ $id }}" type="checkbox" value="{{ $value }}"
        {{ $attributes->merge([
            'class' => 'w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm',
        ]) }}>
    <label for="{{ $id }}" class="text-sm text-gray-700">
        {{ $label }}
    </label>
</div>
