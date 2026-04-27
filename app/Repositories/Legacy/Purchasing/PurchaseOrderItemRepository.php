<?php

namespace App\Repositories\Legacy\Purchasing;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseOrderItemRepository
{
    /**
     * Get all items for a specific Purchase Order by its document number (nota).
     */
    public function getByHeaderNumber(string $number): Collection
    {
        return $this->baseQuery()
            ->where('dpo.nota', $number)
            ->get()
            ->map(fn(object $item) => $this->transform($item));
    }

    /**
     * Centralized base query for Purchase Order Items.
     */
    protected function baseQuery(): Builder
    {
        return DB::table('dpo')
            ->leftJoin('view_stockvariant', 'dpo.kdbvar_id', '=', 'view_stockvariant.id')
            ->leftJoin('dpr', 'dpo.iddpr', '=', 'dpr.iddpr')
            ->leftJoin('hpr', 'dpr.nota', '=', 'hpr.nota')
            ->select([
                'dpo.iddpo as id',
                'dpo.iddpr as purchase_request_item_id',
                'dpo.kdbvar_id as product_variant_id',
                'dpo.kd_brg as product_code',
                'view_stockvariant.variantname as variant_name',
                'dpo.nama as product_name',
                'dpo.no_pr as purchase_request_number',
                'dpo.qty as quantity',
                'dpo.harga as price',
                'dpo.sat as unit',
                'dpo.hbeli as buy_price',
                'dpo.total as sub_total',
                'dpo.discpro as discount_percentage',
                'dpo.discrp as discount_amount',
                'dpo.konvert as price_rate',
                'dpo.ket as remarks',
                'dpo.ket1 as dimension_remarks',
                'dpo.ket2 as additional_remarks',
                'hpr.gp as is_general_purchase',
                'hpr.kddep as department_code',
                DB::raw('(COALESCE(dpr.qty, 0) - COALESCE(dpr.QTY_OP, 0) + COALESCE(dpo.qty, 0)) as max_qty')
            ])
            ->orderBy('dpo.iddpo', 'desc');
    }

    /**
     * Transform the raw database row to standardized PurchaseOrderItem properties.
     */
    protected function transform(object $item): object
    {
        $item->is_general_purchase = ($item->is_general_purchase ?? 'N') === 'Y';
        
        $item->quantity = (float) $item->quantity;
        $item->price = (float) $item->price;
        $item->buy_price = (float) $item->buy_price;
        $item->sub_total = (float) $item->sub_total;
        $item->discount_percentage = (float) $item->discount_percentage;
        $item->discount_amount = (float) $item->discount_amount;
        $item->price_rate = (float) $item->price_rate;
        $item->max_qty = (float) $item->max_qty;

        // Clean up strings from CHAR fields
        $item->product_code = trim($item->product_code ?? '');
        $item->product_name = trim($item->product_name ?? '');
        $item->variant_name = trim($item->variant_name ?? '');
        $item->unit = trim($item->unit ?? '');
        $item->remarks = trim($item->remarks ?? '');
        $item->dimension_remarks = trim($item->dimension_remarks ?? '');
        $item->additional_remarks = trim($item->additional_remarks ?? '');
        $item->purchase_request_number = trim($item->purchase_request_number ?? '');

        return $item;
    }

    /**
     * Sanitize date string from legacy database.
     */
    protected function sanitizeDate(?string $date): ?string
    {
        if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return null;
        }

        return $date;
    }
}
