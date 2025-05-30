<div>
    <x-slot name="header">Data Pengguna</x-slot>

    <div class="flex justify-between mt-5">
        <div></div>
        <x-ui.button wire:click="toggleCrudModal('create', 'true')">Tambah Data</x-ui.button>
    </div>

    <x-ui.modal-form :title="$modalTitle" :modalMethod="$modalMethod">
        <x-form.input id="editing.name" label="Nama Pengguna" wire:model="editing.name" />
        <x-form.input type="email" id="editing.email" label="Email Pengguna" wire:model="editing.email" />

        @if ($modalMethod == 'create')
            <x-form.input type="password" id="editing.password" label="Password Pengguna"
                wire:model="editing.password" />
        @endif

        <x-form.select id="editing.role" name="role" label="Role" wire:model="editing.role">
            <option value="">Pilih Role</option>

            @foreach ($rolesGroup as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </x-form.select>
    </x-ui.modal-form>

    <x-ui.table :columns="$columns" :rows="$rows" :columnFormats="$columnFormats" />
</div>
