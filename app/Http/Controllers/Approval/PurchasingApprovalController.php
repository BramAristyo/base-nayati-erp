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
     * Display the Pending Purchase Request list.
     */
    #[Middleware('can:approval.purchase-request.view')]
    public function pendingPurchaseRequest(BasicPaginateRequest $request): Response
    {
        try {
            $filters = array_merge($request->validated(), [
                'approval_status' => 'pending'
            ]);
            
            $data = $this->purchaseRequestService->paginate($filters);

            return Inertia::render('Approval/Purchasing/PurchaseRequest/Index', [
                'data' => $data,
                'filters' => array_merge($request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']), [
                    'approval_status' => 'pending'
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('Purchasing Approval Pending PR Error: ' . $e->getMessage());
            return $this->errorInertia();
        }
    }

    /**
     * Display the Processed Purchase Request list (Dummy for now).
     */
    #[Middleware('can:approval.purchase-request.view')]
    public function processedPurchaseRequest(BasicPaginateRequest $request): Response
    {
        try {
            $filters = array_merge($request->validated(), [
                'approval_status' => 'processed'
            ]);
            
            $data = $this->purchaseRequestService->paginate($filters);

            return Inertia::render('Approval/Purchasing/PurchaseRequest/Index', [
                'data' => $data,
                'filters' => array_merge($request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']), [
                    'approval_status' => 'processed'
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('Purchasing Approval Processed PR Error: ' . $e->getMessage());
            return $this->errorInertia();
        }
    }

    protected function errorInertia(): Response
    {
        return Inertia::render('Approval/Purchasing/PurchaseRequest/Index', [
            'data' => ['data' => [], 'total' => 0, 'current_page' => 1, 'per_page' => 25],
            'filters' => [],
            'error' => 'Failed to load requests.'
        ]);
    }
}
