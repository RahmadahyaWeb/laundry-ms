<div>
    <section class="bg-white">
        <div class="gap-16 items-center py-8 px-4 w-full py-8 px-4 mx-auto max-w-screen-xl lg:py-16 lg:px-6">
            <h2 class="mb-8 text-4xl tracking-tight font-extrabold text-emerald-700">
                Form Pemesanan Online
            </h2>

            <livewire:components.alert />

            <div class="grid grid-cols-10 gap-6">
                <div class="col-span-10 md:col-span-7">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <x-form.input id="customer.name" name="customer.name" label="Nama Kamu"
                            wire:model.change="customer.name" />

                        <x-form.input type="text" id="customer_phone" name="customer.phone"
                            label="Nomor Telepon (WA)" wire:model.change="customer.phone" />

                        <x-form.input type="date" id="transaction_date" name="transaction_date"
                            label="Tanggal Transaksi" wire:model.change="transaction_date" disabled />

                        <x-form.input id="notes" name="notes" label="Catatan" wire:model="notes" />

                        <div class="md:col-span-2">
                            <label for="alamat_lengkap" class="block mb-2 text-sm font-medium text-gray-900">
                                Alamat Lengkap
                            </label>

                            <textarea id="alamat_lengkap" rows="4"
                                class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:border-blue-500"></textarea>
                        </div>

                        <div class="grid gap-2">
                            <label class="block text-sm font-medium">Layanan</label>
                            <x-form.checkbox id="delivery_services" label="Layanan antar jemput"
                                value="{{ $delivery_services }}" wire:model.change="delivery_services" />
                        </div>
                    </div>

                    <x-ui.divider title="Form tambah item" class="my-6" />

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <x-form.input id="item_name" name="item_name" label="Nama Item" wire:model="item_name" />

                        <x-form.input type="number" id="qty" name="qty" label="Qty" wire:model="qty" />

                        <x-form.select id="service_category_id" name="service_category_id" label="Kategori"
                            wire:model.change="service_category_id">
                            <option value="">Pilih Kategori</option>

                            @foreach ($categoriesGroup as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.select id="service_id" name="service_id" label="Layanan" wire:model="service_id">
                            <option value="">Pilih Layanan</option>

                            @foreach ($servicesGroup as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </x-form.select>

                        <div class="grid gap-2">
                            <label class="block text-sm font-medium">Layanan Tambahan</label>
                            @foreach ($addonsGroup as $addon)
                                <x-form.checkbox id="addon-{{ $addon->id }}" value="{{ $addon->id }}"
                                    wire:model="addons"
                                    label="{{ $addon->name }} | Rp {{ number_format($addon->price, 0, ',', '.') }}" />
                            @endforeach
                        </div>
                    </div>

                    <x-ui.button color="emerald" class="mt-6 mb-8" :block="true" wire:click="addItem">
                        Tambah item
                    </x-ui.button>

                    <x-ui.trx-table :items="$items" :services-group="$servicesGroup" :addons-group="$addonsGroup" :categories-group="$categoriesGroup" />
                </div>

                <div class="col-span-10 md:col-span-3">
                    <form wire:submit.prevent="applyCampaign">
                        <div class="grid gap-4 mb-4">
                            <x-form.input id="campaignCode" name="campaignCode" label="Kode Voucher"
                                wire:model="campaignCode" />
                            <x-ui.button color="emerald" type="submit" :block="true">Terapkan voucher</x-ui.button>
                        </div>
                    </form>

                    <div class="w-full space-y-6">
                        <div class="flow-root">
                            <div class="-my-3 divide-y divide-gray-200">
                                <dl class="flex items-center justify-between gap-4 py-3">
                                    <dt class="text-base font-normal text-gray-500">Subtotal</dt>
                                    <dd class="text-base font-medium text-gray-900">
                                        {{ number_format($subtotal, 0, ',', '.') }}
                                    </dd>
                                </dl>

                                <dl class="flex items-center justify-between gap-4 py-3">
                                    <dt class="text-base font-normal text-gray-500">
                                        Layanan Antar Jemput
                                    </dt>
                                    <dd class="text-base font-medium text-gray-900">
                                        {{ number_format($deliveryFee, 0, ',', '.') }}
                                    </dd>
                                </dl>

                                <dl class="flex items-center justify-between gap-4 py-3">
                                    <dt class="text-base font-normal text-gray-500">Diskon</dt>
                                    <dd class="text-base font-medium text-gray-900">
                                        {{ number_format($discount, 0, ',', '.') }}
                                    </dd>
                                </dl>

                                <dl class="flex items-center justify-between gap-4 py-3">
                                    <dt class="text-base font-bold text-gray-900">Total</dt>
                                    <dd class="text-base font-bold text-gray-900">
                                        {{ number_format($total, 0, ',', '.') }}</dd>
                                </dl>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <x-ui.button color="emerald" :block="true" wire:click="save">
                                Lanjutkan ke Pembayaran
                            </x-ui.button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('customer_phone');
            const prefix = '+62 ';

            // Atur nilai awal jika kosong
            if (!input.value.startsWith(prefix)) {
                input.value = prefix;
            }

            input.addEventListener('keydown', function(e) {
                const cursorPos = input.selectionStart;

                // Cegah penghapusan prefix
                if ((e.key === 'Backspace' || e.key === 'Delete') && cursorPos <= prefix.length) {
                    e.preventDefault();
                }
            });

            input.addEventListener('input', function(e) {
                // Ambil hanya digit dari input
                let raw = input.value.replace(/\D/g, '');

                // Hilangkan awalan 62 jika user mengetik ulang
                if (raw.startsWith('62')) {
                    raw = raw.slice(2);
                } else if (raw.startsWith('0')) {
                    raw = raw.slice(1);
                }

                // Format ulang menjadi +62 XXX-XXXX-XXXXX
                let formatted = prefix;
                if (raw.length > 0) {
                    formatted += raw.substring(0, 3);
                }
                if (raw.length >= 4) {
                    formatted += '-' + raw.substring(3, 7);
                }
                if (raw.length >= 8) {
                    formatted += '-' + raw.substring(7, 12);
                }

                // Hindari bug "angka muncul sendiri saat hapus"
                if (!formatted.startsWith(prefix)) {
                    formatted = prefix;
                }

                // Set kembali
                input.value = formatted;

                // Geser kursor ke akhir
                input.setSelectionRange(formatted.length, formatted.length);
            });

            input.addEventListener('focus', function() {
                if (!input.value.startsWith(prefix)) {
                    input.value = prefix;
                }
            });
        });
    </script>
@endpush
