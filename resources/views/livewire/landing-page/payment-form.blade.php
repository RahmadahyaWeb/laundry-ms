<div class="flex flex-col md:flex-row gap-8 py-8 px-4 mx-auto max-w-screen-xl">
    {{-- Bagian Detail Transaksi --}}
    <div class="md:w-2/3 w-full shadow-md p-6 rounded-lg">
        <h1 class="text-xl font-bold mb-4">Detail Transaksi</h1>

        {{-- Info Transaksi --}}
        <div class="mb-6 border-b pb-4 space-y-1 overflow-x-auto">
            <div class="flex">
                <span class="w-48 font-semibold">No Transaksi</span>
                <span>: {{ $transaction->invoice_number }}</span>
            </div>
            <div class="flex">
                <span class="w-48 font-semibold">Tanggal</span>
                <span>: {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d M Y') }}</span>
            </div>
            <div class="flex">
                <span class="w-48 font-semibold">Status Pembayaran</span>
                <span>: {{ ucfirst($transaction->payment_status) }}</span>
            </div>
            <div class="flex">
                <span class="w-48 font-semibold">Status Transaksi</span>
                <span>: {{ ucfirst($transaction->status) }}</span>
            </div>
        </div>

        {{-- Info Pelanggan --}}
        <div class="mb-6 border-b pb-4 space-y-2">
            <div class="grid md:grid-cols-2 gap-x-4 gap-y-2">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Nama</p>
                    <p class="text-sm text-gray-800">{{ $transaction->customer->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">Telepon</p>
                    <p class="text-sm text-gray-800">{{ $transaction->customer->phone }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-sm font-semibold text-gray-700">Alamat</p>
                    <p class="text-sm text-gray-800">{{ $transaction->customer->address }}</p>
                </div>
            </div>
        </div>

        {{-- Detail Layanan --}}
        @php $manualSubtotal = 0; @endphp
        <div class="mb-6 overflow-x-auto">
            <table class="w-full text-sm border min-w-[600px]">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-2 border">Nama Barang</th>
                        <th class="p-2 border">Layanan</th>
                        <th class="p-2 border text-center">Qty</th>
                        <th class="p-2 border text-right">Harga</th>
                        <th class="p-2 border text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transaction->items as $item)
                        @php $manualSubtotal += $item->subtotal; @endphp
                        <tr>
                            <td class="p-2 border">{{ $item->item_name }}</td>
                            <td class="p-2 border">{{ $item->service->name ?? '-' }}</td>
                            <td class="p-2 border text-center">{{ $item->qty }}</td>
                            <td class="p-2 border text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="p-2 border text-right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>

                        {{-- Addon --}}
                        @foreach ($item->addons as $addon)
                            @php $manualSubtotal += $addon->price; @endphp
                            <tr class="bg-gray-50 text-sm text-gray-600">
                                <td class="p-2 border pl-6" colspan="2">+ {{ $addon->name }}</td>
                                <td class="p-2 border text-center">1</td>
                                <td class="p-2 border text-right">Rp{{ number_format($addon->price, 0, ',', '.') }}
                                </td>
                                <td class="p-2 border text-right">Rp{{ number_format($addon->price, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Ringkasan Harga --}}
        @php
            $diskon = $transaction->discount ?? 0;
            $deliveryFee = $transaction->delivery_fee ?? 0;
            $manualTotal = $manualSubtotal - $diskon + $deliveryFee;
        @endphp

        <div class="my-6 flex justify-end">
            <div class="w-full max-w-md space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Subtotal</span>
                    <span class="text-gray-800">Rp{{ number_format($manualSubtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Diskon</span>
                    <span class="text-gray-800">Rp{{ number_format($diskon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-700">Biaya Antar & Jemput</span>
                    <span class="text-gray-800">Rp{{ number_format($deliveryFee, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t pt-2 text-base font-bold">
                    <span>Total</span>
                    <span>Rp{{ number_format($manualTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Pembayaran --}}
    <div class="md:w-1/3 w-full self-start shadow-md p-6 rounded-lg space-y-4">
        <h2 class="text-lg font-bold mb-2">Pembayaran Bisa Dilakukan ke</h2>

        <div class="border border-2 p-4 rounded-md">
            <p class="text-sm text-gray-500">Bank Mandiri</p>
            <p class="font-semibold text-lg tracking-wide">310020314277</p>
            <p class="text-sm text-gray-700">a.n. Muhammad Achyadi Rahmat</p>
        </div>

        <div class="text-sm text-gray-600">
            Harap sertakan bukti transfer melalui WhatsApp setelah pembayaran dilakukan.
        </div>
    </div>
</div>
