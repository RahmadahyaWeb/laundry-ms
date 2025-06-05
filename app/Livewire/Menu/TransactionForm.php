<?php

namespace App\Livewire\Menu;

use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionForm extends BaseComponent
{
    public $customer_id, $transaction_date, $due_date, $notes, $sales_id, $delivery_services, $deliveryFee;

    public $item_name, $qty = 1, $service_id, $addons = [];

    public $campaignCode, $campaign;

    public $items = [];

    public $customersGroup, $servicesGroup, $addonsGroup, $salesGroup;

    public $subtotal = 0;
    public $total = 0;
    public $discount = 0;

    public function mount()
    {
        $this->transaction_date = Carbon::today()->format('Y-m-d');

        $this->fetchCustomers();
        $this->fetchServices();
        $this->fetchAddons();
        $this->fetchSales();
    }

    public function fetchCustomers()
    {
        $this->customersGroup = Customer::all();
    }

    public function fetchServices()
    {
        $this->servicesGroup = Service::join('service_categories', 'service_categories.id', '=', 'services.service_category_id')
            ->select([
                'services.id',
                'services.name',
                'services.price'
            ])
            ->where('service_categories.name', '!=', 'addons')
            ->where('services.is_active', 1)
            ->get();
    }

    public function fetchAddons()
    {
        $this->addonsGroup = Service::join('service_categories', 'service_categories.id', '=', 'services.service_category_id')
            ->select([
                'services.id',
                'services.name',
                'services.price'
            ])
            ->where('service_categories.name', '=', 'addons')
            ->where('services.is_active', 1)
            ->get();;
    }

    public function fetchSales()
    {
        $this->salesGroup = User::role('sales')->get();
    }

    public function updatedDeliveryServices()
    {
        $this->calculateTotal();
    }

    public function addItem()
    {
        $this->validate([
            'item_name' => 'required|string',
            'qty' => 'required|integer|min:1',
            'service_id' => 'required|exists:services,id',
        ]);

        $this->items[] = [
            'item_name' => $this->item_name,
            'qty' => $this->qty,
            'service_id' => $this->service_id,
            'addons' => $this->addons
        ];

        $this->calculateTotal();

        $this->reset(['item_name', 'qty', 'service_id', 'addons']);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->subtotal = 0;
        $updatedItems = [];

        foreach ($this->items as $item) {
            $service = $this->servicesGroup->firstWhere('id', $item['service_id']);
            $itemSubtotal = 0;

            if ($service) {
                $itemSubtotal += $service->price * $item['qty'];
            }

            if (!empty($item['addons'])) {
                foreach ($item['addons'] as $addonId) {
                    $addon = $this->addonsGroup->firstWhere('id', $addonId);
                    if ($addon) {
                        $itemSubtotal += $addon->price;
                    }
                }
            }

            $item['subtotal'] = $itemSubtotal; // tambahkan ke item
            $this->subtotal += $itemSubtotal;

            $updatedItems[] = $item; // simpan ke array baru
        }

        $this->items = $updatedItems; // overwrite array lama

        $this->deliveryFee = $this->delivery_services ? 15000 : 0;

        $this->total = $this->subtotal - $this->discount + $this->deliveryFee;
    }

    public function applyCampaign()
    {
        if (!$this->subtotal || !$this->total) {
            $this->addError('campaignCode', 'Tidak ada item di form transaksi.');
            return;
        }

        $code = trim($this->campaignCode);
        $campaign = Campaign::where('code', $code)->first();

        if (!$campaign) {
            $this->discount = 0;
            $this->total = $this->subtotal;
            $this->showAlert('Kode voucher tidak ditemukan.', 'danger', 'Error');
            $this->campaign = null;
            return;
        }

        if (!$campaign->isValid()) {
            $this->discount = 0;
            $this->total = $this->subtotal;
            $this->showAlert($campaign->getErrorMessage(), 'danger', 'Error');
            $this->campaign = null;
            return;
        }

        $this->campaign = $campaign;
        if ($campaign->type === 'percentage') {
            $this->discount = round($this->subtotal * ($campaign->value / 100), 2);
        } else {
            $this->discount = min($campaign->value, $this->subtotal);
        }

        $this->total = $this->subtotal - $this->discount;
        $this->showAlert('Kode voucher berhasil diterapkan.');
    }

    public function save()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'transaction_date' => 'required|date',
            'due_date' => 'required|date',
            'sales_id' => 'required'
        ]);

        if (empty($this->items)) {
            $this->showAlert('Tidak ada item di form transaksi.', 'danger', 'Error');
            return;
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateTrxCode(),
                'user_id' => $this->sales_id,
                'customer_id' => $this->customer_id,
                'transaction_date' => $this->transaction_date,
                'due_date' => $this->due_date,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'total_price' => $this->total,
                'campaign_id' => $this->campaign?->id,
                'payment_status' => 'belum bayar',
                'status' => 'proses',
                'notes' => $this->notes
            ]);

            foreach ($this->items as $item) {
                $service = $this->servicesGroup->firstWhere('id', $item['service_id']);

                if (!$service) {
                    throw new \Exception("Layanan utama tidak ditemukan.");
                }

                $unitPrice = $service->price;
                $subtotalItem = $unitPrice * $item['qty'];

                $transactionItem = TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'item_name' => $item['item_name'],
                    'service_id' => $item['service_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $unitPrice,
                    'unit_type' => 'pcs',
                    'subtotal' => $subtotalItem,
                ]);

                foreach ($item['addons'] ?? [] as $addonId) {
                    $addon = $this->addonsGroup->firstWhere('id', $addonId);

                    if (!$addon) {
                        throw new \Exception("Addon dengan ID $addonId tidak ditemukan.");
                    }

                    $addonPrice = $addon->price;

                    DB::table('transaction_item_addons')->insert([
                        'transaction_item_id' => $transactionItem->id,
                        'service_id' => $addonId,
                        'price' => $addonPrice,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();

            $this->showAlert('Transaksi berhasil dibuat.');

            $this->reset([
                'customer_id',
                'sales_id',
                'due_date',
                'item_name',
                'qty',
                'service_id',
                'addons',
                'items',
                'campaignCode',
                'campaign',
                'subtotal',
                'discount',
                'total',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            $this->showAlert($e->getMessage(), 'danger', 'Error');
        }
    }

    public function render()
    {
        return view('livewire.menu.transaction-form');
    }
}
