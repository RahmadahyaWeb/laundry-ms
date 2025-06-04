@props([
    'columns',
    'rows',
    'canDelete' => null,
    'canEdit' => null,
    'columnFormats' => [],
    'actions' => [],
    'cellClass' => null,
])

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                @foreach ($columns as $field => $label)
                    <th scope="col" class="px-6 py-3">{{ $label }}</th>
                @endforeach
                <th scope="col" class="px-6 py-3"><span class="sr-only">Edit</span></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="bg-white border-b border-gray-200">
                    @foreach ($columns as $field => $label)
                        @php
                            $value = data_get($row, $field, '');

                            $format = $columnFormats[$field] ?? null;

                            if (is_callable($format)) {
                                $rendered = $format($row);
                            } elseif ($format === 'image') {
                                $rendered = $value
                                    ? '<a href="' .
                                        asset('storage/' . $value) .
                                        '" target="_blank">
                                            <img src="' .
                                        asset('storage/' . $value) .
                                        '" alt="Image" class="w-16 h-16 object-cover rounded">
                                       </a>'
                                    : '-';
                            } elseif ($format === 'badge') {
                                $rendered =
                                    '<span class="px-2 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">' .
                                    e($value) .
                                    '</span>';
                            } else {
                                $rendered = e($value);
                            }
                        @endphp

                        @if ($loop->first)
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 {{ $cellClass ? call_user_func($cellClass, $row, $field) : '' }}">
                                {!! $rendered !!}
                            </th>
                        @else
                            <td class="px-6 py-4 {{ $cellClass ? call_user_func($cellClass, $row, $field) : '' }}">
                                {!! $rendered !!}
                            </td>
                        @endif
                    @endforeach

                    <td class="px-6 py-4">
                        <div class="flex gap-3 justify-end whitespace-nowrap">
                            @if (is_null($canEdit) || call_user_func($canEdit, $row))
                                <a href="#" class="font-medium text-blue-600 hover:underline"
                                    wire:click="edit({{ $row->id }})">
                                    Edit
                                </a>
                            @endif

                            @if (!empty($actions))
                                @foreach ($actions as $action)
                                    @php
                                        $label = $action['label'] ?? 'Action';
                                        $route = $action['route'] ?? null;
                                        $method = $action['method'] ?? 'GET';
                                        $target = $action['target'] ?? '_self';
                                        $confirmMessage = $action['confirmMessage'] ?? '';
                                        $textColor = $action['textColor'] ?? 'indigo';
                                        $can = $action['can'] ?? fn() => true;

                                        $shouldShow = is_callable($can) ? $can($row) : true;

                                        $href = is_callable($route)
                                            ? $route($row)
                                            : (is_string($route)
                                                ? str_replace('{id}', $row->id, $route)
                                                : '#');
                                    @endphp

                                    @if ($shouldShow)
                                        @if ($method === 'GET')
                                            <a href="{{ $href }}" target="{{ $target }}"
                                                class="font-medium text-{{ $textColor }}-600 hover:underline">
                                                {{ $label }}
                                            </a>
                                        @else
                                            <a href="#"
                                                class="font-medium text-{{ $textColor }}-600 hover:underline"
                                                wire:click="{{ $method . '(' . $row->id . ')' }}"
                                                wire:confirm="{{ $confirmMessage }}">
                                                {{ $label }}
                                            </a>
                                        @endif
                                    @endif
                                @endforeach
                            @endif

                            @if (is_null($canDelete) || call_user_func($canDelete, $row))
                                <a href="#" class="font-medium text-red-600 hover:underline"
                                    wire:click="delete({{ $row->id }})" wire:confirm="Yakin ingin hapus data?">
                                    Delete
                                </a>
                            @endif
                        </div>

                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($rows->links())
    <div class="py-4">
        {{ $rows->links() }}
    </div>
@endif
