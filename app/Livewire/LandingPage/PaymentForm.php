<?php

namespace App\Livewire\LandingPage;

use App\Models\Transaction;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.landing-page')]
class PaymentForm extends Component
{
    public $invoice_number;
    public $transaction;

    public function mount($invoice_number)
    {
        try {
            $invoiceNumber = Crypt::decryptString($invoice_number);

            $this->transaction = Transaction::with('customer', 'items.service')
                ->where('invoice_number', $invoiceNumber)
                ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }

        // Ambil addons untuk tiap item secara manual
        foreach ($this->transaction->items as $item) {
            $item->addons = DB::table('transaction_item_addons')
                ->where('transaction_item_id', $item->id)
                ->join('services', 'services.id', '=', 'transaction_item_addons.service_id')
                ->select('services.name', 'transaction_item_addons.price')
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.landing-page.payment-form');
    }
}
