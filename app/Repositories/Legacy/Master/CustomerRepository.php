<?php

declare(strict_types=1);

namespace App\Repositories\Legacy\Master;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CustomerRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'id' => 'IDCUST',
        'code' => 'KD_CUST',
        'name' => 'NAMA',
        'city' => 'KOTA',
    ];

    public function paginate(int $perPage = 25, array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();
        $this->applyFilters($query, $filters);
        $paginator = $query->paginate($perPage)->withQueryString();
        $paginator->getCollection()->transform(fn(object $item) => $this->transform($item));
        return $paginator;
    }

    public function find(int $id): ?object
    {
        $item = $this->baseQuery()
            ->where('IDCUST', $id)
            ->first();

        return $item ? $this->transform($item) : null;
    }

    protected function baseQuery(): Builder
    {
        return DB::table('custom')->select([
            'IDCUST as id',
            'KD_CAB as branch_code',
            'KD_CUST as code',
            'NAMA as name',
            'NMCOMERCIAL as commercial_name',
            'KOTA as city',
            'ALAMAT as address',
            'ALAMAT1 as other_address',
            'TELP1 as phone',
            'TELP2 as other_phone',
            'NPWP as npwp',
            'ada_so as is_has_sales_order',
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['KD_CUST', 'NAMA', 'KOTA', 'NPWP']);

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'IDCUST', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->is_has_sales_order = ($item->is_has_sales_order === 'Y');

        foreach ($item as $key => $value) {
            if ($value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
                $item->$key = null;
            }
        }

        return $item;
    }
}
