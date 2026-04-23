<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\PaginateFilterRequest;
use App\Models\Utility\AuditTrail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class AuditTrailController extends Controller
{
    #[Middleware('can:utility.audit-trail.view')]
    public function paginate(PaginateFilterRequest $request)
    {
        try {
            $query = AuditTrail::query()
                ->with('causer')
                ->when($request->search, function ($q) use ($request) {
                    $q->where('description', 'like', "%{$request->search}%")
                      ->orWhere('action', 'like', "%{$request->search}%")
                      ->orWhere('subject_type', 'like', "%{$request->search}%");
                });

            $query = $this->applyDateFilter($query, [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ], 'created_at');

            $auditTrails = $query->orderBy($request->sort_by, $request->sort_order)
                ->paginate($request->per_page)
                ->withQueryString();

            return inertia('Utility/AuditTrail/Index', [
                'auditTrails' => $auditTrails,
                'filters' => [
                    'search' => $request->search,
                    'sortField' => $request->input('sortField', 'created_at'),
                    'sortOrder' => (int) $request->input('sortOrder', -1),
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Error while loading audit trail data.');
        }
    }

    private function applyDateFilter(Builder $query, array $filters, string $column): Builder
    {
        $start = $filters['start_date'] ?? null;
        $end = $filters['end_date'] ?? null;

        if ($start && $end) {
            return $query->whereBetween($column, [$start, $end]);
        }

        if ($start) {
            return $query->where($column, '>=', $start);
        }

        if ($end) {
            return $query->where($column, '<=', $end);
        }

        return $query;
    }
}
