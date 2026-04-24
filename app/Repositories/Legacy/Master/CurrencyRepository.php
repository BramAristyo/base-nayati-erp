<?php

declare(strict_types=1);

namespace App\Repositories\Legacy\Master;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CurrencyRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'code' => 'KODE',
        'name' => 'NAMA',
        'rate' => 'KURS',
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
            ->orderBy('KODE', 'asc')
            ->get()
            ->map(fn(object $item) => $this->transform($item));
    }

    public function find(string $code): ?object
    {
        $item = $this->baseQuery()
            ->where('KODE', $code)
            ->first();

        return $item ? $this->transform($item) : null;
    }

    protected function baseQuery(): Builder
    {
        return DB::table('muang')->select([
            'KODE as code',
            'NAMA as name',
            'KURS as rate',
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['KODE', 'NAMA']);

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        } else {
            $filters['sort_by'] = 'KODE';
        }

        $this->applySortFilter($query, $filters, 'KODE', 'asc');
    }

    protected function transform(object $item): object
    {
        $item->rate = (float) ($item->rate ?? 0);

        foreach ($item as $key => $value) {
            if ($value === '0000-00-00 00:00:00' || $value === '0000-00-00') {
                $item->$key = null;
            }
        }

        return $item;
    }
}
