<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\PurchaseOrderService;
use App\Exports\Purchasing\PurchaseOrderExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
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
    public function paginate(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->paginate($request->validated());

            return Inertia::render('Purchasing/PurchaseOrder/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
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

    #[Middleware('can:purchasing.purchase-order.export')]
    public function export(BasicPaginateRequest $request)
    {
        try {
            $data = $this->service->getAllByFilter($request->validated());
            
            $this->service->logExport($request->validated());

            return Excel::download(
                new PurchaseOrderExport($data),
                'purchase-orders-' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (Exception $e) {
            Log::error('Purchase Order Export Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export purchase orders.');
        }
    }

    #[Middleware('can:purchasing.purchase-order.print')]
    public function print(int $id)
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                abort(404);
            }

            $this->service->logPrint($id);

            return view('purchasing.purchase-order.print', [
                'purchaseOrder' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Order Print Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Internal Server Error');
        }
    }
}
