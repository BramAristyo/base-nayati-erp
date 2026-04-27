<?php

declare(strict_types=1);

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\PurchaseRequestService;
use Exception;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PurchasingApprovalController extends Controller
{
    public function __construct(
        protected PurchaseRequestService $purchaseRequestService
    ) {}

    /**
     * Display the Purchase Request approval list.
     */
    #[Middleware('can:approval.purchase-request.view')]
    public function purchaseRequest(BasicPaginateRequest $request): Response
    {
        try {
            // We reuse the paginate method but likely need to add a 'pending' filter
            $filters = array_merge($request->validated(), [
                'approval_status' => 'pending'
            ]);
            
            $data = $this->purchaseRequestService->paginate($filters);

            return Inertia::render('Approval/Purchasing/PurchaseRequest/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
            ]);
        } catch (Exception $e) {
            Log::error('Purchasing Approval PR Paginate Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Approval/Purchasing/PurchaseRequest/Index', [
                'data' => [
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'per_page' => 25,
                ],
                'filters' => [],
                'error' => 'Failed to load pending purchase requests.'
            ]);
        }
    }
}
