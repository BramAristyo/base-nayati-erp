<?php

declare(strict_types=1);

namespace App\Repositories\Legacy\Master;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class EmployeeRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'nik' => 'nik',
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

    public function find(string $nik): ?object
    {
        $item = $this->baseQuery()
            ->where('nik', $nik)
            ->first();

        return $item ? $this->transform($item) : null;
    }

    protected function baseQuery(): Builder
    {
        return DB::table('mkar')->select([
            'nik',
            'ket as name',
            'alrm as address',
            'ktrm as city',
            'telpr as phone',
            'no_hp as mobile_phone',
        ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['nik', 'ket', 'alrm', 'ktrm']);

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        } else {
            $filters['sort_by'] = 'nik';
        }

        $this->applySortFilter($query, $filters, 'nik', 'asc');
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
