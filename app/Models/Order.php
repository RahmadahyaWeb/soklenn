<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($order) {
            if (empty($order->code)) {
                $last = self::orderByDesc('id')->first();
                $number = $last ? ((int) substr($last->code, -4)) + 1 : 1;

                $order->code = 'PO-'.str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
