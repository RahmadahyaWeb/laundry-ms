<div>
    <x-slot name="header">List Transaksi</x-slot>

    <x-ui.modal-form :title="$modalTitle" :modalMethod="$modalMethod">
        <x-form.select id="editing.status" name="status" label="Status" wire:model="editing.status">
            <option value="">Pilih Status</option>
            <option value="proses">proses</option>
            <option value="batal">batal</option>
            <option value="selesai">selesai</option>
        </x-form.select>

        <x-form.select id="editing.payment_status" name="Status Pembayaran" label="payment_status"
            wire:model="editing.payment_status">
            <option value="">Pilih Status</option>
            <option value="belum bayar">belum bayar</option>
            <option value="proses">proses</option>
            <option value="lunas">lunas</option>
        </x-form.select>
    </x-ui.modal-form>

    <x-ui.table :rows="$rows" :columns="$columns" :columnFormats="$columnFormats" :canEdit="$canEdit" :cellClass="$cellClass"
        :canDelete="$canDelete" :actions="$actions" />
</div>
