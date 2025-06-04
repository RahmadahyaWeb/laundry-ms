<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'customer_id',
        'user_id',
        'invoice_number',
        'transaction_date',
        'due_date',
        'status',
        'notes',
        'total_price',
        'discount',
        'paid_amount',
        'change',
        'payment_status'
    ];

    public static function generateTrxCode()
    {
        $lastCode = self::orderBy('invoice_number', 'desc')->value('invoice_number');

        $lastNumber = $lastCode ? (int)str_replace('TRX-', '', $lastCode) : 0;

        $nextNumber = $lastNumber + 1;

        return 'TRX-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
