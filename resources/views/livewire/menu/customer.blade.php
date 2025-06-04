<div>
    <x-slot name="header">Data Pelanggan</x-slot>

    <div class="flex justify-between mt-5">
        <div></div>
        <x-ui.button wire:click="toggleCrudModal('create', 'true')">Tambah Data</x-ui.button>
    </div>

    <x-ui.modal-form :title="$modalTitle" :modalMethod="$modalMethod">
        <x-form.input id="editing.name" label="Nama Pelanggan" wire:model="editing.name" />
        <x-form.input id="editing.phone" label="No Telepon Pelanggan" wire:model="editing.phone" />
        <x-form.input type="email" id="editing.email" label="Email Pelanggan" wire:model="editing.email" />
        <x-form.input id="editing.address" label="Alamat Pelanggan" wire:model="editing.address" />
    </x-ui.modal-form>

    <x-ui.table :columns="$columns" :rows="$rows" />
</div>
