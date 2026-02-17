<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($customer) {
            if (empty($customer->code)) {
                $last = self::orderByDesc('id')->first();
                $number = $last ? ((int) substr($last->code, -4)) + 1 : 1;

                $customer->code = 'CUST-'.str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
