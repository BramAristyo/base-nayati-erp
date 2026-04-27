<?php

declare(strict_types=1);

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\PurchaseRequestService;
use App\Services\Purchasing\PurchaseOrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PurchasingApprovalController extends Controller
{
    public function __construct(
        protected PurchaseRequestService $purchaseRequestService,
        protected PurchaseOrderService $purchaseOrderService
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
     * Display the Processed Purchase Request list.
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

    /**
     * Display the Pending Purchase Order list.
     */
    #[Middleware('can:approval.purchase-order.view')]
    public function pendingPurchaseOrder(BasicPaginateRequest $request): Response
    {
        try {
            $filters = array_merge($request->validated(), [
                'approval_status' => 'pending'
            ]);
            
            $data = $this->purchaseOrderService->getApprovalList($filters);

            return Inertia::render('Approval/Purchasing/PurchaseOrder/Index', [
                'data' => $data,
                'filters' => array_merge($request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']), [
                    'approval_status' => 'pending'
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('Purchasing Approval Pending PO Error: ' . $e->getMessage());
            return $this->errorInertiaPO();
        }
    }

    /**
     * Display the Processed Purchase Order list.
     */
    #[Middleware('can:approval.purchase-order.view')]
    public function processedPurchaseOrder(BasicPaginateRequest $request): Response
    {
        try {
            $filters = array_merge($request->validated(), [
                'approval_status' => 'processed'
            ]);
            
            $data = $this->purchaseOrderService->getApprovalList($filters);

            return Inertia::render('Approval/Purchasing/PurchaseOrder/Index', [
                'data' => $data,
                'filters' => array_merge($request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']), [
                    'approval_status' => 'processed'
                ]),
            ]);
        } catch (Exception $e) {
            Log::error('Purchasing Approval Processed PO Error: ' . $e->getMessage());
            return $this->errorInertiaPO();
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

    protected function errorInertiaPO(): Response
    {
        return Inertia::render('Approval/Purchasing/PurchaseOrder/Index', [
            'data' => ['data' => [], 'total' => 0, 'current_page' => 1, 'per_page' => 25],
            'filters' => [],
            'error' => 'Failed to load purchase orders.'
        ]);
    }
}
