<?php

namespace App\Traits;

use Illuminate\Database\Query\Builder;

trait HasFilterableQuery
{
    public function applyDateFilter(Builder $query, array $filters, string $column): Builder
    {
        return $query->when(isset($filters['date']), function ($q) use ($filters, $column) {
            $start = $filters['date']['start'] ?? null;
            $end = $filters['date']['end'] ?? null;

            if ($start && $end) {
                return $q->whereBetween($column, [$start, $end]);
            }

            if ($start) {
                return $q->where($column, '>=', $start);
            }

            if ($end) {
                return $q->where($column, '<=', $end);
            }
        });
    }

    
}
