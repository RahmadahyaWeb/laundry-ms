<?php

namespace App\Livewire\Menu;

use App\Livewire\BaseComponent;
use App\Models\Transaction as ModelsTransaction;
use Illuminate\Support\Facades\Crypt;

class Transaction extends BaseComponent
{
    public $modalTitle = 'Form Edit Transaksi';

    protected array $permissionMap = [
        'save' => ['edit trx'],
        'edit' => ['edit trx'],
        'delete' => ['delete trx']
    ];

    public $editing =  [
        'id' => '',
        'status' => '',
        'payment_status' => '',
    ];

    public function edit($id)
    {
        $this->editRecord($id, [
            'model' => ModelsTransaction::class,
            'with' => [],
            'map' => function ($data) {
                return [
                    'id' => $data->id,
                    'status' => $data->status,
                    'payment_status' => $data->payment_status
                ];
            }
        ]);
    }

    public function save()
    {
        $this->executeSave(function () {
            $transaction = ModelsTransaction::findOrFail($this->editing['id']);

            $transaction->update([
                'status' => $this->editing['status'],
                'payment_status' => $this->editing['payment_status']
            ]);
        });
    }

    public function cancelTrx($id)
    {
        ModelsTransaction::where('id', $id)
            ->update([
                'status' => 'batal'
            ]);

        $this->showAlert('Transaksi berhasil dibatal');
    }

    public function render()
    {
        $rows = ModelsTransaction::with('customer', 'user')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('invoice_number', 'desc')
            ->paginate();

        $columns = [
            'invoice_number' => 'No Transaksi',
            'customer.name' => 'Nama Pelanggan',
            'user.name' => 'Sales',
            'transaction_date' => 'Tanggal Transaksi',
            'due_date' => 'Tanggal Selesai',
            'status' => 'Status',
            'discount' => 'Diskon',
            'total_price' => 'Total',
            'payment_status' => 'Status Pembayaran'
        ];

        $columnFormats = [
            'discount' => fn($row) => $this->format_rupiah($row->discount),
            'total_price' => fn($row) => $this->format_rupiah($row->total_price),
            'status' => function ($row) {
                $color = '';

                if ($row->status == 'proses') {
                    $color = 'bg-yellow-100 text-yellow-700';
                } elseif ($row->status == 'selesai') {
                    $color = 'bg-blue-100 text-blue-700';
                } elseif ($row->status == 'batal') {
                    $color = 'bg-red-100 text-red-700';
                }

                return '<span class="px-2 py-1 rounded-full text-xs ' . $color . '">' . $row->status . '</span>';
            },
        ];

        $cellClass = function ($row, $field) {
            $columnsWithClass = [
                'invoice.number',
                'discount',
                'total_price',
                'transaction_date',
                'due_date'
            ];

            if (in_array($field, $columnsWithClass)) {
                return 'whitespace-nowrap';
            }
            return '';
        };

        $actions = [
            [
                'label' => 'Detail',
                'route' => fn($row) => route('landing-page.payment-form', [
                    'invoice_number' => Crypt::encryptString($row->invoice_number)
                ]),
                'target' => '_blank',
                'can' => fn($row) => true,
            ],
            [
                'label' => 'Batal',
                'method' => 'cancelTrx',
                'confirmMessage' => 'Yakin ingin batal transaksi?',
                'textColor' => 'red',
                'can' => fn($row) => $row->status != 'batal'
            ],
        ];

        $canEdit = fn($row) => $row->status == 'batal' ? false : true;
        $canDelete = fn($row) => false;

        return view('livewire.menu.transaction', compact(
            'rows',
            'columns',
            'columnFormats',
            'canEdit',
            'canDelete',
            'cellClass',
            'actions',
        ));
    }
}
