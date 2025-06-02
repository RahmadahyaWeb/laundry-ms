<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $fillable = ['name', 'description', 'code'];

    public static function generateServiceCode()
    {
        $lastCode = self::orderBy('code', 'desc')->value('code');

        $lastNumber = $lastCode ? (int)str_replace('SC-', '', $lastCode) : 0;

        $nextNumber = $lastNumber + 1;

        return 'SC-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
