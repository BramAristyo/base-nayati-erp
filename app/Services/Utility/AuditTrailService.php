<?php

namespace App\Services\Utility;

use App\Models\Utility\AuditTrail;
use App\Traits\HasFilterableQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AuditTrailService
{
    use HasFilterableQuery;

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = AuditTrail::query()->with('causer');

        $this->applySearchFilter($query, $filters, ['description', 'action', 'subject_type']);
        $this->applyDateFilter($query, $filters, 'created_at');
        $this->applySortFilter($query, $filters, 'created_at', 'desc');

        return $query->paginate($filters['per_page'] ?? 25)->withQueryString();
    }
}
