<?php

namespace App\Repositories\Legacy\Purchasing;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReceivingItemRepository
{
    /**
     * Get all receiving items by receiving note number.
     */
    public function getByHeaderNumber(string $number): Collection
    {
        return $this->baseQuery()
            ->where('dbeli.nota', $number)
            ->get()
            ->map(fn ($item) => $this->transform($item));
    }

    /**
     * Base query with necessary joins and selections.
     */
    protected function baseQuery(): Builder
    {
        return DB::table('dbeli')
            ->leftJoin('view_stockvariant', 'dbeli.kdbvar_id', '=', 'view_stockvariant.id')
            ->select([
                'dbeli.iddbeli as id',
                'dbeli.iddpo as purchase_order_item_id',
                'dbeli.noop as purchase_order_number',
                'dbeli.kdbvar_id as product_variant_id',
                'view_stockvariant.variantname as variant_name',
                'dbeli.kd_brg as product_code',
                'dbeli.kd_code as product_barcode',
                'dbeli.nama as product_name',
                'dbeli.qty as quantity',
                'dbeli.sat as unit',
                'dbeli.hbeli as buy_price',
                'dbeli.hpp as unit_cost',
                'dbeli.harga as price',
                'dbeli.kd6 as account_type_code',
                'dbeli.settrnket as account_type_name',
                'dbeli.nosr as serial_number',
                'dbeli.NOGARANSI as warranty_number',
            ]);
    }

    /**
     * Transform legacy item to a standardized object.
     */
    protected function transform(object $item): object
    {
        return (object) [
            'id' => $item->id,
            'purchase_order_item_id' => $item->purchase_order_item_id,
            'purchase_order_number' => trim($item->purchase_order_number ?? ''),
            'product_variant_id' => $item->product_variant_id,
            'variant_name' => isset($item->variant_name) ? trim($item->variant_name) : null,
            'product_code' => trim($item->product_code ?? ''),
            'product_barcode' => trim($item->product_barcode ?? ''),
            'product_name' => trim($item->product_name ?? ''),
            'quantity' => (float) $item->quantity,
            'unit' => trim($item->unit ?? ''),
            'buy_price' => (float) $item->buy_price,
            'unit_cost' => (float) $item->unit_cost,
            'price' => (float) $item->price,
            'account_type_code' => trim($item->account_type_code ?? ''),
            'account_type_name' => trim($item->account_type_name ?? ''),
            'serial_number' => trim($item->serial_number ?? ''),
            'warranty_number' => trim($item->warranty_number ?? ''),
        ];
    }

    /**
     * Sanitize legacy date strings.
     */
    protected function sanitizeDate(?string $date): ?string
    {
        if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return null;
        }

        return $date;
    }
}
