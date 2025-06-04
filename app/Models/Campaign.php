<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected ?string $errorMessage = null;

    protected $guarded = [];

    public function isValid(): bool
    {
        $today = Carbon::today();

        if (!$this->is_active) {
            $this->errorMessage = 'Voucher tidak aktif.';
            return false;
        }

        if ($this->start_date && $today->lt($this->start_date)) {
            $this->errorMessage = 'Voucher belum berlaku.';
            return false;
        }

        if ($this->end_date && $today->gt($this->end_date)) {
            $this->errorMessage = 'Voucher sudah kedaluwarsa.';
            return false;
        }

        if (!is_null($this->usage_limit) && $this->used_count >= $this->usage_limit) {
            $this->errorMessage = 'Voucher sudah mencapai batas penggunaan.';
            return false;
        }

        return true;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}
