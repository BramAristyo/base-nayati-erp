<?php

namespace App\Repositories\Legacy\Purchasing;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseRequestItemRepository
{
    public function getByHeaderNumber(string $number): Collection
    {
        return $this->baseQuery()
            ->where('dpr.nota', $number)
            ->get()
            ->map(fn(object $item) => $this->transform($item));
    }

    protected function baseQuery(): Builder
    {
        return DB::table('dpr')
            ->leftJoin('view_stockvariant', 'dpr.kdbvar_id', '=', 'view_stockvariant.id')
            ->select([
                'dpr.iddpr as id',
                'dpr.kdbvar_id as product_variant_id',
                'dpr.kd_brg as product_code',
                'view_stockvariant.variantname as variant_name',
                'dpr.nama as product_name',
                'dpr.qty as quantity',
                'dpr.harga as price',
                'dpr.sat as unit',
                'dpr.tglpakai as usage_date',
                'dpr.nmsupplier as supplier_name',
                'dpr.merk as brand',
                'dpr.ket as remarks',
            ])
            ->orderBy('dpr.iddpr', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->usage_date = $this->sanitizeDate($item->usage_date);

        $item->quantity = (float) ($item->quantity ?? 0);
        $item->price = (float) ($item->price ?? 0);

        $item->product_code = trim($item->product_code ?? '');
        $item->product_name = trim($item->product_name ?? '');
        $item->variant_name = trim($item->variant_name ?? '');
        $item->unit = trim($item->unit ?? '');
        $item->supplier_name = trim($item->supplier_name ?? '');
        $item->brand = trim($item->brand ?? '');
        $item->remarks = trim($item->remarks ?? '');

        return $item;
    }

    protected function sanitizeDate(?string $date): ?string
    {
        if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return null;
        }

        return $date;
    }
}
