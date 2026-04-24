<?php

declare(strict_types=1);

namespace App\Repositories\Legacy\Master;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BranchRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'code' => 'kd_cab',
        'name' => 'nm_cab',
        'address' => 'alm_cab',
    ];

    public function paginate(int $perPage = 25, array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();
        $this->applyFilters($query, $filters);
        $paginator = $query->paginate($perPage)->withQueryString();
        $paginator->getCollection()->transform(fn(object $item) => $this->transform($item));
        return $paginator;
    }

    public function getAll(): Collection
    {
        return $this->baseQuery()
            ->orderBy('kd_cab', 'asc')
            ->get()
            ->map(fn(object $item) => $this->transform($item));
    }

    public function find(string $code): ?object
    {
        $item = $this->baseQuery()
            ->where('kd_cab', $code)
            ->first();

        return $item ? $this->transform($item) : null;
    }

    protected function baseQuery(): Builder
    {
        return DB::table('kd_cab')->select([
            'kd_cab as code',
            'nm_cab as name',
            'alm_cab as address',
            'npwp_cab as npwp',
            'telp_cab as phone',
            'email_cab as email',
            'aktif as is_active',
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['kd_cab', 'nm_cab']);

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        } else {
            $filters['sort_by'] = 'kd_cab';
        }

        $this->applySortFilter($query, $filters, 'kd_cab', 'asc');
    }

    protected function transform(object $item): object
    {
        $item->is_active = ($item->is_active === 'Y');

        foreach ($item as $key => $value) {
            if ($value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
                $item->$key = null;
            }
        }

        return $item;
    }
}
