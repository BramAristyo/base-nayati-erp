<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;

trait HasFilterableQuery
{
    /**
     * Apply date range filters to the query.
     */
    public function applyDateFilter(EloquentBuilder|QueryBuilder $query, array $filters, string $column = 'created_at'): EloquentBuilder|QueryBuilder
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;

        return $query->when($start, fn (EloquentBuilder|QueryBuilder $q) => $q->where($column, '>=', "{$start} 00:00:00"))
            ->when($end, fn (EloquentBuilder|QueryBuilder $q) => $q->where($column, '<=', "{$end} 23:59:59"));
    }

    /**
     * Apply a fuzzy search filter across multiple columns.
     */
    public function applySearchFilter(EloquentBuilder|QueryBuilder $query, array $filters, array $columns): EloquentBuilder|QueryBuilder
    {
        $search = $filters['search'] ?? null;

        return $query->when($search, function (EloquentBuilder|QueryBuilder $q) use ($search, $columns) {
            $q->where(function (EloquentBuilder|QueryBuilder $sub) use ($search, $columns) {
                foreach ($columns as $column) {
                    $sub->orWhere($column, 'like', "%{$search}%");
                }
            });
        });
    }

    /**
     * Apply sorting to the query.
     */
    public function applySortFilter(
        EloquentBuilder|QueryBuilder $query,
        array $filters,
        string $defaultSort = 'created_at',
        string $defaultOrder = 'desc'
    ): EloquentBuilder|QueryBuilder {
        $sort = $filters['sort_by'] ?? $defaultSort;
        $order = $filters['sort_order'] ?? $defaultOrder;

        return $query->orderBy($sort, $order);
    }

    /**
     * Summary of applyStatusFilter
     * @param EloquentBuilder|QueryBuilder $query
     * @param array $filters
     * @param string $column
     * @param string $approvedValue
     * @return EloquentBuilder|QueryBuilder
     */
    public function applyStatusFilter(EloquentBuilder|QueryBuilder $query, array $filters, string $column, string $approvedValue = 'Y'): EloquentBuilder|QueryBuilder
    {
        return $query->when(isset($filters['status']), function ($q) use ($filters, $column, $approvedValue) {
            if ($filters['status'] === 'approved') {
                $q->where($column, $approvedValue);
            } elseif ($filters['status'] === 'pending') {
                $q->where($column, '!=', $approvedValue);
            }
        });
    }
}
