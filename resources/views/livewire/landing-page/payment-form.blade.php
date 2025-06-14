<div class="flex gap-8 py-8 px-4 mx-auto max-w-screen-xl">
    <div class="w-2/3 shadow-md p-6 rounded-lg">
        <h1 class="text-xl font-bold mb-4">Detail Transaksi</h1>

        {{-- Info Transaksi --}}
        <div class="mb-6 border-b pb-4 space-y-1">
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
        <div class="mb-6 border-b pb-4 space-y-1">
            <div class="flex">
                <span class="w-48 font-semibold">Nama</span>
                <span>: {{ $transaction->customer->name }}</span>
            </div>
            <div class="flex">
                <span class="w-48 font-semibold">Telepon</span>
                <span>: {{ $transaction->customer->phone }}</span>
            </div>
            <div class="flex">
                <span class="w-48 font-semibold">Alamat</span>
                <span>: {{ $transaction->customer->address }}</span>
            </div>
        </div>

        {{-- Detail Layanan --}}
        <div class="mb-6">
            <table class="w-full text-sm border">
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
                        <tr>
                            <td class="p-2 border">{{ $item->item_name }}</td>
                            <td class="p-2 border">{{ $item->service->name ?? '-' }}</td>
                            <td class="p-2 border text-center">{{ $item->qty }}</td>
                            <td class="p-2 border text-right">Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                            </td>
                            <td class="p-2 border text-right">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>

                        {{-- Addon per item --}}
                        @foreach ($item->addons as $addon)
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
        <div class="my-4 flex justify-end">
            <div class="space-y-1">
                <div class="flex">
                    <span class="w-60 font-semibold">Subtotal</span>
                    <span>: Rp{{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex">
                    <span class="w-60 font-semibold">Diskon</span>
                    <span>: Rp{{ number_format($transaction->discount, 0, ',', '.') }}</span>
                </div>
                <div class="flex">
                    <span class="w-60 font-semibold">Biaya Antar & Jemput</span>
                    <span>: Rp{{ number_format($transaction->delivery_fee, 0, ',', '.') }}</span>
                </div>
                <div class="flex text-lg font-bold">
                    <span class="w-60">Total</span>
                    <span>: Rp{{ number_format($transaction->total_price, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="w-1/3 self-start shadow-md p-6 rounded-lg space-y-4">
        <h2 class="text-lg font-bold mb-2">Pembayaran Bisa Dilakukan ke</h2>

        <div class="border border-2 p-4 rounded-md">
            <p class="text-sm text-gray-500">Bank Mandiri</p>
            <p class="font-semibold text-lg tracking-wide">
                310020314277
            </p>
            <p class="text-sm text-gray-700">a.n. Muhammad Achyadi Rahmat</p>
        </div>

        <div class="text-sm text-gray-600">
            Harap sertakan bukti transfer melalui WhatsApp setelah pembayaran dilakukan.
        </div>
    </div>
</div>
