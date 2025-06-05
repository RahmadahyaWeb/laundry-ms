@props([
    'items' => [],
    'servicesGroup' => collect(),
    'addonsGroup' => collect(),
])

<div class="relative overflow-x-auto sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">Item</th>
                <th scope="col" class="px-6 py-3">Qty</th>
                <th scope="col" class="px-6 py-3">Layanan</th>
                <th scope="col" class="px-6 py-3">Layanan Tambahan</th>
                <th scope="col" class="px-6 py-3">Subtotal</th>
                <th scope="col" class="px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $index => $item)
                @php
                    $service = $servicesGroup->firstWhere('id', $item['service_id']);
                    $addonNames = collect($item['addons'] ?? [])
                        ->map(function ($addonId) use ($addonsGroup) {
                            $addon = $addonsGroup->firstWhere('id', $addonId);
                            return $addon?->name;
                        })
                        ->filter()
                        ->implode(', ');
                @endphp
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 border-gray-200">
                    <td class="px-6 py-4">{{ $item['item_name'] }}</td>
                    <td class="px-6 py-4">{{ $item['qty'] }}</td>
                    <td class="px-6 py-4">{{ $service?->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $addonNames ?: '-' }}</td>
                    <td class="px-6 py-4">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        <button type="button" wire:click="removeItem({{ $index }})"
                            class="text-red-600 hover:underline">Hapus</button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data tersedia.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
