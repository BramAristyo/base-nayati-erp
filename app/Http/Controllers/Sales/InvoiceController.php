<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Sales\InvoiceService;
use Exception;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $service
    ) {}

    #[Middleware('can:sales.invoice.view')]
    public function paginate(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->paginate($request->validated());

            return Inertia::render('Sales/Invoice/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
            ]);
        } catch (Exception $e) {
            Log::error('Sales Invoice Paginate Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Sales/Invoice/Index', [
                'data' => ['data' => [], 'total' => 0, 'current_page' => 1, 'per_page' => 25],
                'filters' => ['search' => '', 'sortField' => 'created_at', 'sortOrder' => -1],
                'error' => 'Failed to load invoices.'
            ]);
        }
    }

    #[Middleware('can:sales.invoice.view')]
    public function show(int $id): Response
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                abort(404);
            }

            return Inertia::render('Sales/Invoice/Show', [
                'invoice' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Sales Invoice Show Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Internal Server Error');
        }
    }
}
