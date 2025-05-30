<?php

namespace App\Livewire\Menu;

use App\Livewire\BaseComponent;
use App\Models\User as ModelsUser;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class User extends BaseComponent
{
    public $modalTitle = 'Form Pengguna';

    protected array $permissionMap = [
        'save' => ['edit user'],
        'edit' => ['edit user'],
        'delete' => ['delete user']
    ];

    public $editing =  [
        'id' => '',
        'name' => '',
        'role' => '',
        'email' => '',
        'password' => '',
    ];

    public $rolesGroup = [];

    public function mount()
    {
        $this->fetchRoles();
    }

    public function fetchRoles()
    {
        $this->rolesGroup = Role::all();
    }

    public function create()
    {
        $this->validate([
            'editing.name' => 'required',
            'editing.role' => 'required',
            'editing.email' => 'required|email|unique:users,email',
            'editing.password' => 'required|min:8'
        ]);

        $this->executeSave(function () {
            $user = ModelsUser::create([
                'name' => $this->editing['name'],
                'email' => $this->editing['email'],
                'password' => Hash::make($this->editing['password']),
            ]);

            $role = Role::find($this->editing['role']);
            $user->assignRole($role->name);
        });
    }

    public function edit($id)
    {
        $this->editRecord($id, [
            'model' => ModelsUser::class,
            'with' => ['roles'],
            'map' => function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'role' => $data->roles->pluck('id')->toArray()[0],
                    'email' => $data->email
                ];
            }
        ]);
    }

    public function save()
    {
        $this->validate([
            'editing.name' => 'required',
            'editing.role' => 'required',
            'editing.email' => 'required|email|unique:users,email, ' . $this->editing['id'],
        ]);

        $this->executeSave(function () {
            $user = ModelsUser::findOrFail($this->editing['id']);

            $user->name = $this->editing['name'];
            $user->save();

            if (!empty($this->editing['role'])) {
                $role = Role::find($this->editing['role']);
                $user->syncRoles([$role->name]);
            }
        });
    }

    public function delete($id)
    {
        $this->executeDelete(function () use ($id) {
            $user = ModelsUser::findOrFail($id);
            $user->delete();
        });
    }

    public function render()
    {
        $rows = ModelsUser::with('roles')->paginate();

        $columns = [
            'name' => 'Nama Pengguna',
            'email' => 'Email',
            'roles.name' => 'Role',
        ];

        return view('livewire.menu.user', compact('rows', 'columns'));
    }
}
