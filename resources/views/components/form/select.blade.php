@props([
    'id' => 'select',
    'label' => null,
    'name' => null,
    'required' => false,
])

<div>
    @if ($label)
        <label for="{{ $id }}"
            class="block mb-2 text-sm font-medium
                @error($id) text-red-700 @else text-gray-900 @enderror">
            {{ $label }}
        </label>
    @endif

    <select id="{{ $id }}" name="{{ $name ?? $id }}" @if ($required) required @endif
        {{ $attributes->merge([
            'class' =>
                'block w-full p-2.5 text-sm rounded-lg ' .
                ($errors->has($id)
                    ? 'bg-red-50 border border-red-500 text-red-900 focus:ring-red-500 focus:border-red-500'
                    : 'bg-gray-50 border border-gray-300 text-gray-900 focus:ring-blue-500 focus:border-blue-500'),
        ]) }}>
        {{ $slot }}
    </select>

    @error($id)
        <p class="mt-2 text-sm text-red-600">
            {{ $message }}
        </p>
    @enderror
</div>
