<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'code',
        'service_category_id',
        'name',
        'description',
        'unit_type',
        'price',
        'estimated_days',
        'is_active'
    ];

    public static function generateServiceCode()
    {
        $lastCode = self::orderBy('code', 'desc')->value('code');

        $lastNumber = $lastCode ? (int)str_replace('SRVC-', '', $lastCode) : 0;

        $nextNumber = $lastNumber + 1;

        return 'SRVC-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }
}
