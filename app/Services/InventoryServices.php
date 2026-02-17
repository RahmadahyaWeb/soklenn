<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\StockMovement;
use Exception;

class InventoryServices
{
    /**
     * Kurangi stock berdasarkan order items
     */
    public static function reduce_stock_from_order($order, $reference, $note = 'Penjualan')
    {
        foreach ($order->orderDetails as $item) {

            $variant = ProductVariant::lockForUpdate()->find($item->product_variant_id);

            if (! $variant) {
                throw new Exception('Variant tidak ditemukan');
            }

            $stock_before = $variant->stock;

            $stock_after = $stock_before - $item->qty;

            if ($stock_after < 0) {
                throw new Exception("Stock {$variant->code} tidak cukup");
            }

            // update stock
            $variant->update([
                'stock' => $stock_after,
            ]);

            // history
            StockMovement::create([

                'product_variant_id' => $variant->id,

                'type' => 'out',

                'qty' => $item->qty,

                'stock_before' => $stock_before,

                'stock_after' => $stock_after,

                'reference' => $reference,

                'note' => $note,

            ]);
        }
    }
}
