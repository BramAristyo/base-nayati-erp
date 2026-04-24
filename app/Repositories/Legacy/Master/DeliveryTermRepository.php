<?php

declare(strict_types=1);

namespace App\Repositories\Legacy\Master;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DeliveryTermRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'id' => 'ID',
        'name' => 'KET',
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
            ->orderBy('ID', 'asc')
            ->get()
            ->map(fn(object $item) => $this->transform($item));
    }

    public function find(int $id): ?object
    {
        $item = $this->baseQuery()
            ->where('ID', $id)
            ->first();

        return $item ? $this->transform($item) : null;
    }

    protected function baseQuery(): Builder
    {
        return DB::table('kirim')->select([
            'ID as id',
            'KET as name',
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['KET']);

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'ID', 'asc');
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
