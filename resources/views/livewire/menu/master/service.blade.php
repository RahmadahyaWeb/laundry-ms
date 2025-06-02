<div>
    <x-slot name="header">Data Layanan</x-slot>

    <div class="flex justify-between mt-5">
        <div></div>
        <x-ui.button wire:click="toggleCrudModal('create', 'true')">Tambah Data</x-ui.button>
    </div>

    <x-ui.modal-form :title="$modalTitle" :modalMethod="$modalMethod">
        <x-form.input id="editing.name" label="Nama Kategori Layanan" wire:model="editing.name" />

        <x-form.select id="editing.service_category_id" name="service_category_id" label="Kategori Layanan"
            wire:model="editing.service_category_id">
            <option value="">Pilih Kategori</option>

            @foreach ($serviceCategoriesGroup as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </x-form.select>

        <x-form.select id="editing.unit_type" name="unit_type" label="Satuan" wire:model="editing.unit_type">
            <option value="">Pilih Satuan</option>
            <option value="pcs">pcs</option>
            <option value="kg">kilogram</option>
        </x-form.select>

        <x-form.input type="number" id="editing.estimated_days" label="Estimasi Hari"
            wire:model="editing.estimated_days" />

        <x-form.input type="number" id="editing.price" label="Harga Layanan" wire:model="editing.price" />

        <x-form.input id="editing.description" label="Deskripsi Kategori Layanan" wire:model="editing.description" />

        <x-form.select id="editing.is_active" name="is_active" label="Status" wire:model="editing.is_active">
            <option value="">Pilih Status</option>
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
        </x-form.select>

    </x-ui.modal-form>

    <x-ui.table :columns="$columns" :rows="$rows" :columnFormats="$columnFormats" :cellClass="$cellClass" />
</div>
