<?php

namespace App\Repositories\Legacy\Sales;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ShipmentRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'shipment_number' => 'msj.NOBUK',
        'invoice_number' => 'msj.NOIN',
        'invoice_date' => 'msj.TGI',
        'delivery_order_number' => 'msj.NOP',
        'customer_code' => 'msj.KDCUST',
        'date' => 'msj.TGL',
        'branch_code' => 'msj.KDCAB',
        'is_local_purchase' => 'msj.LE',
        'unit_code' => 'msj.KDUN',
        'sales_order_number' => 'msj.NOSO',
        'sales_order_date' => 'msj.TGLSO',
        'warehouse_code' => 'msj.kdlok',
        'warehouse_name' => 'w.name',
        'updated_at' => 'msj.TGLUPDATE',
        'created_at' => 'msj.IDMSJ',
    ];

    public function paginate(int $perPage = 25, array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->transform(fn(object $item) => $this->transform($item));

        return $paginator;
    }

    public function getAllByFilter(array $filters = []): \Illuminate\Support\Collection
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        return $query->get()->map(fn(object $item) => $this->transform($item));
    }

    public function find(int $id): ?array
    {
        $item = $this->baseQuery()
            ->where('msj.IDMSJ', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('msj')
            ->leftJoin('warehouses as w', 'msj.kdlok', '=', 'w.code')
            ->leftJoin('custom as c', 'msj.KDCUST', '=', 'c.kd_cust')
            ->select([
                'msj.IDMSJ as id',
                'msj.NOBUK as shipment_number',
                'msj.NOIN as invoice_number',
                'msj.TGI as invoice_date',
                'msj.NOP as delivery_order_number',
                'msj.KDCUST as customer_code',
                'c.NAMA as customer_name',
                'msj.TGL as date',
                'msj.KDCAB as branch_code',
                'msj.LE as is_local_purchase',
                'msj.KDUN as unit_code',
                'msj.NOSO as sales_order_number',
                'msj.TGLSO as sales_order_date',
                'msj.kdlok as warehouse_code',
                'w.name as warehouse_name',
                'msj.TGLUPDATE as updated_at',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['msj.NOBUK', 'msj.NOIN', 'msj.KDCUST', 'msj.NOSO']);
        $this->applyDateFilter($query, $filters, 'msj.TGL');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'msj.IDMSJ', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->invoice_date = $this->sanitizeDate($item->invoice_date);
        $item->sales_order_date = $this->sanitizeDate($item->sales_order_date);
        $item->updated_at = $this->sanitizeDate($item->updated_at);

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
