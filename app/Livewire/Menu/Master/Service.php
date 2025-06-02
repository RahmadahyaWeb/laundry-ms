<?php

namespace App\Livewire\Menu\Master;

use App\Livewire\BaseComponent;
use App\Models\Service as ModelsService;
use App\Models\ServiceCategory;

class Service extends BaseComponent
{
    public $modalTitle = 'Form Layanan';

    protected array $permissionMap = [
        'save' => ['edit service'],
        'edit' => ['edit service'],
        'delete' => ['delete service']
    ];

    public $editing =  [
        'id' => '',
        'service_category_id' => '',
        'code' => '',
        'name' => '',
        'description' => '',
        'unit_type' => '',
        'price' => '',
        'estimated_days' => '1',
        'is_active' => '1',
    ];

    public $serviceCategoriesGroup;

    public function mount()
    {
        $this->fetchServiceCategories();
    }

    public function rules()
    {
        return [
            'editing.service_category_id' => 'required',
            'editing.name' => 'required',
            'editing.unit_type' => 'required',
            'editing.price' => 'required',
            'editing.estimated_days' => 'required',
            'editing.is_active' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();

        $this->executeSave(function () {
            ModelsService::create([
                'code' => ModelsService::generateServiceCode(),
                'service_category_id' => $this->editing['service_category_id'],
                'name' => $this->editing['name'],
                'description' => $this->editing['description'],
                'unit_type' => $this->editing['unit_type'],
                'price' => $this->editing['price'],
                'estimated_days' => $this->editing['estimated_days'],
                'is_active' => $this->editing['is_active']
            ]);
        });
    }

    public function edit($id)
    {
        $this->editRecord($id, [
            'model' => ModelsService::class,
            'with' => ['category'],
            'map' => function ($data) {
                return [
                    'id' => $data->id,
                    'name' => $data->name,
                    'description' => $data->description,
                    'service_category_id' => $data->service_category_id,
                    'unit_type' => $data->unit_type,
                    'price' => $data->price,
                    'estimated_days' => $data->estimated_days,
                    'is_active' => $data->is_active,
                ];
            }
        ]);
    }

    public function save()
    {
        $this->validate();

        $this->executeSave(function () {
            $service = ModelsService::findOrFail($this->editing['id']);

            $service->update([
                'service_category_id' => $this->editing['service_category_id'],
                'name' => $this->editing['name'],
                'description' => $this->editing['description'],
                'unit_type' => $this->editing['unit_type'],
                'price' => $this->editing['price'],
                'estimated_days' => $this->editing['estimated_days'],
                'is_active' => $this->editing['is_active']
            ]);
        });
    }

    public function fetchServiceCategories()
    {
        $this->serviceCategoriesGroup = ServiceCategory::all();
    }

    public function delete($id)
    {
        $this->executeDelete(function () use ($id) {
            $service = ModelsService::findOrFail($id);
            $service->delete();
        });
    }

    public function render()
    {
        $rows = ModelsService::with(['category'])->paginate();

        $columns = [
            'code' => 'Kode Layanan',
            'category.name' => 'Kategori Layanan',
            'name' => 'Nama Layanan',
            'description' => 'Deskripsi Layanan',
            'unit_type' => 'Satuan',
            'price' => 'Harga Layanan',
            'estimated_days' => 'Estimasi Hari',
            'is_active' => 'Status'
        ];

        $columnFormats = [
            'price' => fn($row) => $this->format_rupiah($row->price),
            'is_active' => function ($row) {
                $label = $row->is_active ? 'Aktif' : 'Nonaktif';
                $color = $row->is_active ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700';

                return '<span class="px-2 py-1 rounded-full text-xs ' . $color . '">' . $label . '</span>';
            },
        ];

        $cellClass = function ($row, $field) {
            $columnsWithClass = [
                'code',
                'name',
                'price'
            ];

            if (in_array($field, $columnsWithClass)) {
                return 'whitespace-nowrap';
            }
            return '';
        };

        // $canDelete = fn($row) => false;
        // $canEdit = fn($row) => false;

        return view('livewire.menu.master.service', compact(
            'rows',
            'columns',
            'columnFormats',
            'cellClass'
        ));
    }
}
