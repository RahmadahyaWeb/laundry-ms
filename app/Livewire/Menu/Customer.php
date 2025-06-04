<?php

namespace App\Livewire\Menu;

use App\Livewire\BaseComponent;
use App\Models\Customer as ModelsCustomer;

class Customer extends BaseComponent
{
    public $modalTitle = 'Form Pengguna';

    protected array $permissionMap = [
        'save' => ['edit customer'],
        'edit' => ['edit customer'],
        'delete' => ['delete customer']
    ];

    public $editing =  [
        'id' => '',
        'name' => '',
        'phone' => '',
        'email' => '',
        'address' => '',
    ];

    public function create()
    {
        $this->validate([
            'editing.name' => 'required',
            'editing.email' => 'email|unique:customers,email',
            'editing.phone' => 'unique:customers,phone',
        ]);

        $this->executeSave(function () {
            ModelsCustomer::create([
                'name' => $this->editing['name'],
                'phone' => $this->editing['phone'],
                'email' => $this->editing['email'],
                'address' => $this->editing['address'],
            ]);
        });
    }

    public function edit($id)
    {
        $this->editRecord($id, [
            'model' => ModelsCustomer::class,
            'with' => [],
            'map' => function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'email' => $data->email,
                    'phone' => $data->phone,
                    'address' => $data->address,
                ];
            }
        ]);
    }

    public function save()
    {
        $this->validate([
            'editing.name' => 'required',
            'editing.email' => 'email|unique:customers,email,' . $this->editing['id'],
            'editing.phone' => 'unique:customers,phone,' . $this->editing['id'],
        ]);

        $this->executeSave(function () {
            $customer = ModelsCustomer::findOrFail($this->editing['id']);

            $customer->update([
                'name' => $this->editing['name'],
                'phone' => $this->editing['phone'],
                'email' => $this->editing['email'],
                'address' => $this->editing['address'],
            ]);
        });
    }

    public function delete($id)
    {
        $this->executeDelete(function () use ($id) {
            $customer = ModelsCustomer::findOrFail($id);
            $customer->delete();
        });
    }

    public function render()
    {
        $rows = ModelsCustomer::paginate();

        $columns = [
            'name' => 'Nama Pelanggan',
            'phone' => 'No Telepon Pelanggan',
            'email' => 'Email Pelanggan',
            'address' => 'Alamat Pelanggan'
        ];

        return view('livewire.menu.customer', compact(
            'rows',
            'columns'
        ));
    }
}
