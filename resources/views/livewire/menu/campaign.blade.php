<div>
    <x-slot name="header">Data Campaign</x-slot>

    <div class="flex justify-between mt-5">
        <div></div>
        <x-ui.button wire:click="toggleCrudModal('create', 'true')">Tambah Data</x-ui.button>
    </div>

    <x-ui.modal-form :title="$modalTitle" :modalMethod="$modalMethod">
        <x-form.input id="editing.code" label="Kode" wire:model="editing.code" />

        <x-form.select id="editing.type" name="type" label="Tipe" wire:model="editing.type">
            <option value="">Pilih Tipe</option>
            <option value="percentage">Persen</option>
            <option value="nominal">Nominal</option>
        </x-form.select>

        <x-form.input type="number" id="editing.value" label="Nilai Voucher" wire:model="editing.value" />

        <x-form.input type="number" id="editing.min_transaction" label="Minimal Transaksi"
            wire:model="editing.min_transaction" />

        <x-form.input type="number" id="editing.usage_limit" label="Limit Pemakaian"
            wire:model="editing.usage_limit" />

        <div class="grid grid-cols-2 gap-3">
            <x-form.input type="date" id="editing.start_date" label="Dari Tanggal" wire:model="editing.start_date" />

            <x-form.input type="date" id="editing.end_date" label="Sampai Tanggal" wire:model="editing.end_date" />
        </div>

        <x-form.select id="editing.is_active" name="is_active" label="Status" wire:model="editing.is_active">
            <option value="">Pilih Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </x-form.select>

        <x-form.input type="file" id="editing.thumbnail" label="Thumbnail" wire:model="editing.thumbnail" />

        <div wire:loading wire:target="editing.thumbnail">Uploading...</div>

    </x-ui.modal-form>

    <x-ui.table :columns="$columns" :rows="$rows" :columnFormats="$columnFormats" :cellClass="$cellClass" />
</div>
