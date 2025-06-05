<div>
    <x-slot name="header">Form Transaksi</x-slot>

    <div class="grid grid-cols-1 md:grid-cols-10 md:gap-16 gap-6">
        <div class="col-span-7">
            <div class="grid gap-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-form.select id="customer_id" name="customer_id" label="Pelanggan" wire:model.change="customer_id">
                        <option value="">Pilih Pelanggan</option>

                        @foreach ($customersGroup as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </x-form.select>

                    <x-form.input type="date" id="transaction_date" name="transaction_date" label="Tanggal Transaksi"
                        wire:model.change="transaction_date" />

                    <x-form.input type="date" id="due_date" name="due_date" label="Tanggal Selesai"
                        wire:model.change="due_date" />

                    <x-form.select id="sales_id" name="sales_id" label="Sales" wire:model.change="sales_id">
                        <option value="">Pilih Sales</option>

                        @foreach ($salesGroup as $sales)
                            <option value="{{ $sales->id }}">{{ $sales->name }}</option>
                        @endforeach
                    </x-form.select>

                    <div class="col-span-2">
                        <x-form.input id="notes" name="notes" label="Catatan" wire:model="notes" />
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-sm font-medium">Layanan</label>
                        <x-form.checkbox id="delivery_services" label="Layanan antar jemput"
                            value="{{ $delivery_services }}" wire:model.change="delivery_services" />
                    </div>
                </div>

                @if ($customer_id && $transaction_date && $sales_id)
                    <x-ui.divider title="Form tambah item" />

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-form.input id="item_name" name="item_name" label="Nama Item" wire:model="item_name" />

                        <x-form.input type="number" id="qty" name="qty" label="Qty" wire:model="qty" />

                        <x-form.select id="service_id" name="service_id" label="Layanan" wire:model="service_id">
                            <option value="">Pilih Layanan</option>

                            @foreach ($servicesGroup as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <div class="grid gap-2">
                        <label class="block text-sm font-medium">Layanan Tambahan</label>
                        @foreach ($addonsGroup as $addon)
                            <x-form.checkbox id="addon-{{ $addon->id }}" value="{{ $addon->id }}"
                                wire:model="addons"
                                label="{{ $addon->name }} | Rp {{ number_format($addon->price, 0, ',', '.') }}" />
                        @endforeach
                    </div>

                    <x-ui.button :block="true" wire:click="addItem">Tambah Item</x-ui.button>
                @endif
            </div>

            <x-ui.divider title="Daftar item" class="my-6" />

            <x-ui.trx-table :items="$items" :services-group="$servicesGroup" :addons-group="$addonsGroup" />
        </div>

        <div class="col-span-7 md:col-span-3">
            <form wire:submit.prevent="applyCampaign">
                <div class="grid gap-4 mb-4">
                    <x-form.input id="campaignCode" name="campaignCode" label="Kode Voucher"
                        wire:model="campaignCode" />
                    <x-ui.button type="submit" :block="true">Terapkan Voucher</x-ui.button>
                </div>
            </form>

            <div class="w-full space-y-6">
                <div class="flow-root">
                    <div class="-my-3 divide-y divide-gray-200 dark:divide-gray-800">
                        <dl class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Subtotal</dt>
                            <dd class="text-base font-medium text-gray-900 dark:text-white">
                                {{ number_format($subtotal, 0, ',', '.') }}
                            </dd>
                        </dl>

                        <dl class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Diskon</dt>
                            <dd class="text-base font-medium text-gray-900 dark:text-white">
                                {{ number_format($discount, 0, ',', '.') }}
                            </dd>
                        </dl>

                        <dl class="flex items-center justify-between gap-4 py-3">
                            <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                            <dd class="text-base font-bold text-gray-900 dark:text-white">
                                {{ number_format($total, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="space-y-3">
                    <x-ui.button :block="true" wire:click="save">Simpan</x-ui.button>
                </div>
            </div>
        </div>
    </div>
</div>
