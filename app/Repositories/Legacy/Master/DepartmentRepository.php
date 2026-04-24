<?php

declare(strict_types=1);

namespace App\Repositories\Legacy\Master;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DepartmentRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'id' => 'id',
        'code' => 'kddep',
        'name' => 'ket',
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
            ->orderBy('kddep', 'asc')
            ->get()
            ->map(fn(object $item) => $this->transform($item));
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
        return DB::table('mdept')->select([
            'id',
            'kddep as code',
            'ket as name',
            'tglentry as created_at',
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['kddep', 'ket']);

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'id', 'desc');
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
