<?php

declare(strict_types=1);

namespace App\Repositories\Legacy\Master;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SupplierRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'id' => 'id',
        'code' => 'kd_supp',
        'name' => 'nama',
        'city' => 'kota',
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
            ->where('id', $id)
            ->first();

        return $item ? $this->transform($item) : null;
    }

    protected function baseQuery(): Builder
    {
        return DB::table('supplier')->select([
            'id',
            'kd_supp as code',
            'nama as name',
            'alamat as address',
            'kota as city',
            'negara as country',
            'telp1 as phone',
            'no_fax as fax',
            'npwp as tin',
            'person as contact_person',
            'tglupdate as updated_at',
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['kd_supp', 'nama', 'alamat', 'kota']);

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'tglupdate', 'desc');
    }

    protected function transform(object $item): object
    {
        foreach ($item as $key => $value) {
            if ($value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
                $item->$key = null;
            }
        }

        return $item;
    }
}
