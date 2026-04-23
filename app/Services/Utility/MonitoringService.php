<?php

namespace App\Services\Utility;

use App\Models\Utility\AuditTrail;
use App\Models\Utility\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class MonitoringService
{
    /**
     * Get combined monitoring statistics.
     */
    public function getMonitoringStats(array $filters): array
    {
        return [
            'active_user_count' => User::where('is_active', 1)->count(),
            'activity_count' => $this->getActivityCount($filters),
            'active_sessions_count' => $this->getActiveSessionsCount($filters),
        ];
    }

    /**
     * Get total activity count based on audit trails.
     */
    private function getActivityCount(array $filters): int
    {
        $query = AuditTrail::query();
        
        $this->applyDateFilter($query, $filters, 'created_at');

        return $query->count();
    }

    /**
     * Get active sessions count from the database sessions table.
     */
    private function getActiveSessionsCount(array $filters): int
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;

        $query = DB::table('sessions');

        if ($start) {
            $query->where('last_activity', '>=', Carbon::parse($start)->timestamp);
        }

        if ($end) {
            $query->where('last_activity', '<=', Carbon::parse($end)->timestamp);
        }

        return $query->count();
    }

    /**
     * Generic date filter applier.
     */
    private function applyDateFilter($query, array $filters, string $column): void
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;

        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);
        } elseif ($start) {
            $query->where($column, '>=', $start);
        } elseif ($end) {
            $query->where($column, '<=', $end);
        }
    }
}
