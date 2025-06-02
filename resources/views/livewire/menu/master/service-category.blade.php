<div>
    <x-slot name="header">Data Kategori Layanan</x-slot>

    <div class="flex justify-between mt-5">
        <div></div>
        <x-ui.button wire:click="toggleCrudModal('create', 'true')">Tambah Data</x-ui.button>
    </div>

    <x-ui.modal-form :title="$modalTitle" :modalMethod="$modalMethod">
        <x-form.input id="editing.name" label="Nama Kategori Layanan" wire:model="editing.name" />
        <x-form.input id="editing.description" label="Deskripsi Kategori Layanan" wire:model="editing.description" />
    </x-ui.modal-form>

    <x-ui.table :columns="$columns" :rows="$rows" />
</div>
