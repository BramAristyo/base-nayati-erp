<?php

namespace App\Services\Utility;

use App\Models\Utility\AuditTrail;
use App\Models\Utility\User;
use App\Traits\HasFilterableQuery;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonitoringService
{
    use HasFilterableQuery;

    public function stats(array $filters): array
    {
        return [
            'active_user_count' => User::where('is_active', 1)->count(),
            'activity_count' => $this->getActivityCount($filters),
            'active_sessions_count' => $this->getActiveSessionsCount($filters),
        ];
    }

    private function getActivityCount(array $filters): int
    {
        $query = AuditTrail::query();

        $this->applySearchFilter($query, $filters, ['description', 'action', 'subject_type']);
        $this->applyDateFilter($query, $filters, 'created_at');

        return $query->count();
    }

    private function getActiveSessionsCount(array $filters): int
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;

        $query = DB::table('sessions');

        if ($start) {
            $query->where('last_activity', '>=', Carbon::parse($start)->startOfDay()->timestamp);
        }

        if ($end) {
            $query->where('last_activity', '<=', Carbon::parse($end)->endOfDay()->timestamp);
        }

        return $query->count();
    }
}
