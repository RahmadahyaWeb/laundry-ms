<?php

namespace App\Livewire\Menu\Master;

use App\Livewire\BaseComponent;
use App\Models\ServiceCategory as ModelsServiceCategory;

class ServiceCategory extends BaseComponent
{
    public $modalTitle = 'Form Kategori Layanan';

    protected array $permissionMap = [
        'save' => ['edit service-category'],
        'edit' => ['edit service-category'],
        'delete' => ['delete service-category']
    ];

    public $editing =  [
        'id' => '',
        'code' => '',
        'name' => '',
        'description' => '',
    ];

    public function rules()
    {
        return [
            'editing.name' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();

        $this->executeSave(function () {
            ModelsServiceCategory::create([
                'code' => ModelsServiceCategory::generateServiceCode(),
                'name' => strtolower($this->editing['name']),
                'description' => $this->editing['description']
            ]);
        });
    }


    public function edit($id)
    {
        $this->editRecord($id, [
            'model' => ModelsServiceCategory::class,
            'with' => [],
            'map' => function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'description' => $data->description
                ];
            }
        ]);
    }

    public function save()
    {
        $this->validate();

        $this->executeSave(function () {
            $serviceCategory = ModelsServiceCategory::findOrFail($this->editing['id']);

            $serviceCategory->update([
                'name' => strtolower($this->editing['name']),
                'description' => $this->editing['description']
            ]);
        });
    }

    public function delete($id)
    {
        $this->executeDelete(function () use ($id) {
            $serviceCategory = ModelsServiceCategory::findOrFail($id);
            $serviceCategory->delete();
        });
    }

    public function render()
    {
        $rows = ModelsServiceCategory::paginate();

        $columns = [
            'code' => 'Kode Kategori Layanan',
            'name' => 'Nama Kategori Layanan',
            'description' => 'Deskripsi Kategori Layanan'
        ];

        return view('livewire.menu.master.service-category', compact(
            'rows',
            'columns'
        ));
    }
}
