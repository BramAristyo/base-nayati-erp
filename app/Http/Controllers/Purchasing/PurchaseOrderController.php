<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\PurchaseOrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchaseOrderService $service
    ) {}

    #[Middleware('can:purchasing.purchase-order.view')]
    public function paginate(BasicPaginateRequest $request): Response|JsonResponse
    {
        try {
            $data = $this->service->paginate($request->validated());
            return response()->json($data);

            return Inertia::render('Purchasing/PurchaseOrder/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sort_by', 'sort_order', 'per_page']),
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Order Paginate Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Purchasing/PurchaseOrder/Index', [
                'data' => [],
                'error' => 'Failed to load purchase orders.'
            ]);
        }
    }

    #[Middleware('can:purchasing.purchase-order.view')]
    public function show(int $id): Response
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                abort(404);
            }

            return Inertia::render('Purchasing/PurchaseOrder/Show', [
                'purchaseOrder' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Order Show Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Internal Server Error');
        }
    }
}
