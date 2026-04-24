<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\PaginateFilterRequest;
use App\Services\Utility\AuditTrailService;
use Exception;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Response;

class AuditTrailController extends Controller
{
    public function __construct(
        protected AuditTrailService $auditTrailService
    ) {}

    #[Middleware('can:utility.audit-trail.view')]
    public function paginate(PaginateFilterRequest $request): Response
    {
        try {
            $auditTrails = $this->auditTrailService->paginate($request->all());

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
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'An error occurred while loading audit trails.');
        }
    }
}
