<?php

namespace App\Livewire\LandingPage;

use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Models\Customer;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Crypt;

#[Layout('components.layouts.landing-page')]
class TrxForm extends BaseComponent
{

    public $transaction_date, $due_date, $notes, $sales_id, $delivery_services, $deliveryFee, $invoice_number;

    public $item_name, $qty = 1, $service_id, $addons = [], $service_category_id;

    public $campaignCode, $campaign;

    public $items = [];

    public $customersGroup, $servicesGroup, $addonsGroup, $salesGroup, $categoriesGroup;

    public $subtotal = 0;
    public $total = 0;
    public $discount = 0;

    public $customer = [
        'name' => '',
        'phone' => '',
        'address' => ''
    ];

    public function mount()
    {
        $this->transaction_date = Carbon::today()->format('Y-m-d');
        $this->invoice_number = Transaction::generateTrxCode();

        $this->fetchCategories();
        $this->fetchAddons();
        $this->servicesGroup = collect();
    }

    public function fetchCategories()
    {
        $this->categoriesGroup = ServiceCategory::where('name', '!=', 'addons')->get();
    }

    public function fetchServices($service_category_id)
    {
        $this->servicesGroup = Service::join('service_categories', 'service_categories.id', '=', 'services.service_category_id')
            ->select([
                'services.id',
                'services.name',
                'services.price'
            ])
            ->where('service_category_id', $service_category_id)
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

    public function updatedDeliveryServices()
    {
        $this->calculateTotal();
    }

    public function updatedServiceCategoryId()
    {
        $this->reset('service_id');
        $this->fetchServices($this->service_category_id);
    }

    public function addItem()
    {
        $this->validate([
            'item_name' => 'required|string',
            'qty' => 'required|integer|min:1',
            'service_id' => 'required|exists:services,id',
            'service_category_id' => 'required|exists:service_categories,id',
        ]);

        $this->items[] = [
            'item_name' => $this->item_name,
            'qty' => $this->qty,
            'service_id' => $this->service_id,
            'addons' => $this->addons,
            'service_category_id' => $this->service_category_id
        ];

        $this->calculateTotal();

        $this->reset(['item_name', 'qty', 'service_id', 'addons', 'service_category_id']);
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
            $this->showAlert('Kode voucher tidak ditemukan.', 'danger', 'Error');
            $this->campaign = null;
            return;
        }

        if (!$campaign->isValid()) {
            $this->discount = 0;
            $this->showAlert($campaign->getErrorMessage(), 'danger', 'Error');
            $this->campaign = null;
            return;
        }

        $this->campaign = $campaign;
        if ($campaign->type === 'percentage') {
            $this->discount = round($this->total * ($campaign->value / 100), 2);
        } else {
            $this->discount = min($campaign->value, $this->total);
        }

        $this->total = $this->total - $this->discount;
        $this->showAlert('Kode voucher berhasil diterapkan.');
    }

    public function save()
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'customer.name' => 'required',
            'customer.phone' => 'required',
            'customer.address' => 'required'
        ]);

        if (empty($this->items)) {
            $this->showAlert('Tidak ada item di form transaksi.', 'danger', 'Error');
            return;
        }

        DB::beginTransaction();

        try {
            $normalizedPhone = $this->convertToLocalNumber($this->customer['phone']);

            $customer = Customer::create([
                'name' => $this->customer['name'],
                'phone' => $normalizedPhone,
                'address' => $this->customer['address'],
            ]);

            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateTrxCode(),
                'customer_id' => $customer->id,
                'transaction_date' => $this->transaction_date,
                'subtotal' => $this->subtotal,
                'discount' => $this->discount,
                'delivery_fee' => $this->deliveryFee,
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

            $encryptedInvoice = Crypt::encryptString($transaction->invoice_number);
            $invoiceUrl = route('landing-page.payment-form', $encryptedInvoice);

            $message = "Terima kasih! Transaksi Anda berhasil.\nNo Transaksi: {$transaction->invoice_number}\n\nLihat detail transaksi:\n{$invoiceUrl}";

            $whatsapp = new WhatsappService();
            $whatsapp->sendMessage($normalizedPhone, $message);

            $this->showAlert('Transaksi berhasil dibuat.');
            $this->resetTrxForm();

            $this->redirectRoute('landing-page.payment-form', $encryptedInvoice);
        } catch (\Exception $e) {
            DB::rollBack();

            $this->showAlert($e->getMessage(), 'danger', 'Error');
        }
    }

    public function resetTrxForm()
    {
        $this->reset([
            'item_name',
            'qty',
            'service_category_id',
            'service_id',
            'addons',
            'items',
            'campaignCode',
            'campaign',
            'subtotal',
            'discount',
            'total',
            'customer',
            'customer',
            'customer',
            'notes',
        ]);
    }

    function convertToLocalNumber($phone)
    {
        // Hilangkan semua karakter non-angka
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika diawali dengan 628, ubah ke 08
        if (substr($phone, 0, 3) === '628') {
            $phone = '08' . substr($phone, 3);
        }

        return $phone;
    }

    public function render()
    {
        return view('livewire.landing-page.trx-form');
    }
}
