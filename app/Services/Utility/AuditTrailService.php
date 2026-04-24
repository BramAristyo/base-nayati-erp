<?php

namespace App\Services\Utility;

use App\Models\Utility\AuditTrail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditTrailService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = AuditTrail::query()
            ->with('causer')
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%");
            });

        $this->applyDateFilter($query, $filters, 'created_at');

        return $query->orderBy($filters['sort_by'] ?? 'created_at', $filters['sort_order'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 25)
            ->withQueryString();
    }

    private function applyDateFilter($query, array $filters, string $column): void
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;

        if ($start && $end) {
            $query->whereBetween($column, [$start . ' 00:00:00', $end . ' 23:59:59']);
        } elseif ($start) {
            $query->where($column, '>=', $start . ' 00:00:00');
        } elseif ($end) {
            $query->where($column, '<=', $end . ' 23:59:59');
        }
    }
}
