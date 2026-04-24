<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\PaginateFilterRequest;
use App\Services\Utility\MonitoringService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;

class MonitoringController extends Controller
{
    public function __construct(
        protected MonitoringService $monitoringService
    ) {}

    #[Middleware('can:utility.audit-trail.view')]
    public function show(PaginateFilterRequest $request): JsonResponse
    {
        try {
            return $this->successResponse($this->monitoringService->stats($request->all()), 'Monitoring statistics fetched successfully.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('An error occurred while fetching monitoring statistics.', 500);
        }
    }
}
