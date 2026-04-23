<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\PaginateFilterRequest;
use App\Services\Utility\MonitoringService;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

#[Middleware('can:utility.audit-trail.view')]
class MonitoringController extends Controller
{
    protected $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    /**
     * Get combined monitoring statistics.
     */
    public function getStats(PaginateFilterRequest $request)
    {
        try {
            $stats = $this->monitoringService->getMonitoringStats([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            return $this->successResponse($stats, 'Monitoring stats fetched successfully');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return $this->errorResponse("Something went wrong: " . $th->getMessage(), 500);
        }
    }
}
