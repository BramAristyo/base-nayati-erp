<?php

namespace App\Repositories\Legacy\Purchasing;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseRequestItemRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'purchase_request_number' => 'dpr.nota',
        'product_code' => 'dpr.kd_brg',
        'product_name' => 'dpr.nama',
        'usage_date' => 'dpr.tglpakai',
        'supplier_name' => 'dpr.nmsupplier',
        'created_at' => 'dpr.tglentry',
    ];

    public function getByHeaderNumber(string $number): Collection
    {
        return $this->baseQuery()
            ->where('dpr.nota', $number)
            ->orderBy('dpr.iddpr', 'asc')
            ->get()
            ->map(fn(object $item) => $this->transform($item));
    }

    public function getListings(int $perPage = 25, array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->transform(fn(object $item) => $this->transform($item));

        return $paginator;
    }

    protected function baseQuery(): Builder
    {
        return DB::table('dpr')
            ->leftJoin('view_stockvariant', 'dpr.kdbvar_id', '=', 'view_stockvariant.id')
            ->select([
                'dpr.iddpr as id',
                'dpr.nota as purchase_request_number',
                'dpr.tgl as date',
                'dpr.kdbvar_id as product_variant_id',
                'dpr.kd_brg as product_code',
                'view_stockvariant.variantname as variant_name',
                'dpr.nama as product_name',
                'dpr.qty as quantity',
                'dpr.QTY_ADJ as adjusted_quantity',
                'dpr.QTY_OP as ordered_quantity',
                'dpr.harga as price',
                'dpr.sat as unit',
                'dpr.tglpakai as usage_date',
                'dpr.nmsupplier as supplier_name',
                'dpr.merk as brand',
                'dpr.ket as remarks',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, [
            'dpr.nota',
            'dpr.kd_brg',
            'dpr.nama',
            'dpr.nmsupplier',
            'dpr.merk'
        ]);

        if (isset($filters['purchase_request_number'])) {
            $query->where('dpr.nota', $filters['purchase_request_number']);
        }

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'dpr.iddpr', 'desc');
        $this->applyDateFilter($query, $filters, 'dpr.tglentry');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->usage_date = $this->sanitizeDate($item->usage_date);

        $item->quantity = (float) ($item->quantity ?? 0);
        $item->adjusted_quantity = (float) ($item->adjusted_quantity ?? 0);
        $item->ordered_quantity = (float) ($item->ordered_quantity ?? 0);
        $item->price = (float) ($item->price ?? 0);

        $item->purchase_request_number = trim($item->purchase_request_number ?? '');
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
