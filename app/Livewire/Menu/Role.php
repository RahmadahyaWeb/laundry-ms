<?php

namespace App\Livewire\Menu;

use App\Livewire\BaseComponent;
use Spatie\Permission\Models\Role as ModelsRole;
use Spatie\Permission\Models\Permission;

class Role extends BaseComponent
{
    public $modalTitle = 'Form Hak Akses Pengguna';

    protected array $permissionMap = [
        'save' => ['edit role'],
        'edit' => ['edit role'],
        'delete' => ['delete role']
    ];

    public $editing =  [
        'id' => '',
        'name' => '',
        'permissions' => []
    ];

    public $permissionGroups = [];

    protected function rules()
    {
        return [
            'editing.name' => 'required',
        ];
    }

    public function mount()
    {
        $this->fetchPermissions();
    }

    public function fetchPermissions()
    {
        $grouped = [];

        $permissions = Permission::all();

        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            if (count($parts) >= 2) {
                $action = $parts[0];
                $group = $parts[1];
                $grouped[$group][] = [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'action' => $action
                ];
            }
        }

        $this->permissionGroups = $grouped;
    }

    public function create()
    {
        $this->validate();

        $this->executeSave(function () {
            $role = ModelsRole::firstOrCreate(['name' => $this->editing['name']]);

            $permissions = array_map('intval', $this->editing['permissions']);
            $role->syncPermissions($permissions);
        });
    }

    public function edit($id)
    {
        $this->editRecord($id, [
            'model' => \Spatie\Permission\Models\Role::class,
            'with' => ['permissions'],
            'map' => function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'permissions' => $data->permissions->pluck('id')->toArray(),
                ];
            }
        ]);
    }

    public function save()
    {
        $this->validate();

        $this->executeSave(function () {
            $role = ModelsRole::findOrFail($this->editing['id']);
            $role->name = $this->editing['name'];
            $role->save();

            $permissions = array_map('intval', $this->editing['permissions']);
            $role->syncPermissions($permissions);
        });
    }

    public function delete($id)
    {
        $this->executeDelete(function () use ($id) {
            $role = ModelsRole::findOrFail($id);

            $role->delete();
        });
    }

    public function render()
    {
        $rows = ModelsRole::paginate();

        $columns = [
            'name' => 'Nama Role',
        ];

        $canDelete = function ($row) {
            $protected = ['admin'];
            return !in_array(strtolower($row->name), $protected);
        };

        return view('livewire.menu.role', compact('columns', 'rows', 'canDelete'));
    }
}
