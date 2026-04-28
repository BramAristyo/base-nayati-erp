<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Sales\DeliveryOrderService;
use Exception;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryOrderController extends Controller
{
    public function __construct(
        protected DeliveryOrderService $service
    ) {}

    #[Middleware('can:sales.delivery-order.view')]
    public function paginate(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->paginate($request->validated());

            return Inertia::render('Sales/DeliveryOrder/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
            ]);
        } catch (Exception $e) {
            Log::error('Sales Delivery Order Paginate Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Sales/DeliveryOrder/Index', [
                'data' => ['data' => [], 'total' => 0, 'current_page' => 1, 'per_page' => 25],
                'filters' => ['search' => '', 'sortField' => 'created_at', 'sortOrder' => -1],
                'error' => 'Failed to load delivery orders.'
            ]);
        }
    }

    #[Middleware('can:sales.delivery-order.view')]
    public function show(int $id): Response
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                abort(404);
            }

            return Inertia::render('Sales/DeliveryOrder/Show', [
                'deliveryOrder' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Sales Delivery Order Show Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Internal Server Error');
        }
    }
}
