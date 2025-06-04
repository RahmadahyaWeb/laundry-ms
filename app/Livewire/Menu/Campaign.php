<?php

namespace App\Livewire\Menu;

use App\Livewire\BaseComponent;
use App\Models\Campaign as ModelsCampaign;

class Campaign extends BaseComponent
{
    public $modalTitle = 'Form Campaign';

    protected array $permissionMap = [
        'save' => ['edit campaign'],
        'edit' => ['edit campaign'],
        'delete' => ['delete campaign']
    ];

    public $editing =  [
        'id' => '',
        'code' => '',
        'type' => '',
        'value' => '',
        'min_transaction' => '',
        'start_date' => '',
        'end_date' => '',
        'usage_limit' => '1',
        'is_active' => '1',
        'used_count' => '0',
        'is_active' => '1',
        'thumbnail' => ''
    ];

    public function create()
    {
        $this->validate([
            'editing.code' => 'required|unique:campaigns,code',
            'editing.type' => 'required',
            'editing.value' => 'required',
            'editing.usage_limit' => 'required',
            'editing.is_active' => 'required',
            'editing.thumbnail' => 'image|max:2056'
        ]);

        $this->executeSave(function () {
            $thumbnail = null;

            $code = strtoupper(preg_replace('/\s+/', '', $this->editing['code']));

            if ($this->editing['thumbnail']) {
                $thumbnail = $this->editing['thumbnail']->storeAs(
                    'thumbnail',
                    $this->editing['thumbnail']->hashName(),
                    'public'
                );
            }

            ModelsCampaign::create([
                'code' => $code,
                'type' => $this->editing['type'],
                'value' => $this->editing['value'],
                'min_transaction' => $this->editing['min_transaction'],
                'start_date' => $this->editing['start_date'],
                'end_date' => $this->editing['end_date'],
                'usage_limit' => $this->editing['usage_limit'],
                'is_active' => $this->editing['is_active'],
                'thumbnail' => $thumbnail,
            ]);
        });
    }

    public function edit($id)
    {
        $this->editRecord($id, [
            'model' => ModelsCampaign::class,
            'with' => [],
            'map' => function ($data) {
                return [
                    'id' => $data->id,
                    'code' => $data->code,
                    'type' => $data->type,
                    'value' => $data->value,
                    'min_transaction' => $data->min_transaction,
                    'start_date' => $data->start_date,
                    'end_date' => $data->end_date,
                    'usage_limit' => $data->usage_limit,
                    'is_active' => $data->is_active,
                ];
            }
        ]);
    }

    public function save()
    {
        $this->validate([
            'editing.code' => 'required|unique:campaigns,code,' . $this->editing['id'],
            'editing.type' => 'required',
            'editing.value' => 'required',
            'editing.usage_limit' => 'required',
            'editing.is_active' => 'required',
            'editing.thumbnail' => 'image|max:2056'
        ]);

        $this->executeSave(function () {
            $campaign = ModelsCampaign::findOrFail($this->editing['id']);

            $thumbnail = $campaign->thumbnail;

            $code = strtoupper(preg_replace('/\s+/', '', $this->editing['code']));

            if (isset($this->editing['thumbnail']) && $this->editing['thumbnail']) {
                dd('true');
                $thumbnail = $this->editing['thumbnail']->storeAs(
                    'thumbnail',
                    $this->editing['thumbnail']->hashName(),
                    'public'
                );
            }

            $campaign->update([
                'code' => $code,
                'type' => $this->editing['type'],
                'value' => $this->editing['value'],
                'min_transaction' => $this->editing['min_transaction'],
                'start_date' => $this->editing['start_date'],
                'end_date' => $this->editing['end_date'],
                'usage_limit' => $this->editing['usage_limit'],
                'is_active' => $this->editing['is_active'],
                'thumbnail' => $thumbnail,
            ]);
        });
    }

    public function delete($id)
    {
        $this->executeDelete(function () use ($id) {
            $campaign = ModelsCampaign::findOrFail($id);
            $campaign->delete();
        });
    }

    public function render()
    {
        $rows = ModelsCampaign::paginate();

        $columns = [
            'code' => 'Kode',
            'type' => 'Tipe',
            'value' => 'Nilai Voucher',
            'min_transaction' => 'Minimal Transaksi',
            'start_date' => 'Dari Tanggal',
            'end_date' => 'Sampai Tanggal',
            'usage_limit' => 'Batas Pemakaian',
            'used_count' => 'Jumlah Pemakaian',
            'is_active' => 'Status',
            'thumbnail' => 'Thumbnail'
        ];

        $columnFormats = [
            'thumbnail' => 'image',
            'min_transaction' => fn($row) => $this->format_rupiah($row->min_transaction),
            'is_active' => function ($row) {
                $label = $row->is_active ? 'Aktif' : 'Nonaktif';
                $color = $row->is_active ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700';

                return '<span class="px-2 py-1 rounded-full text-xs ' . $color . '">' . $label . '</span>';
            },
            'value' => function ($row) {
                if ($row->type === 'percentage') {
                    return $row->value . '%';
                } elseif ($row->type === 'nominal') {
                    return $this->format_rupiah($row->value);
                }

                return $row->value;
            },
        ];

        $cellClass = function ($row, $field) {
            $columnsWithClass = [
                'code',
                'value',
                'start_date',
                'end_date'
            ];

            if (in_array($field, $columnsWithClass)) {
                return 'whitespace-nowrap';
            }
            return '';
        };

        return view('livewire.menu.campaign', compact(
            'rows',
            'columns',
            'columnFormats',
            'cellClass'
        ));
    }
}
