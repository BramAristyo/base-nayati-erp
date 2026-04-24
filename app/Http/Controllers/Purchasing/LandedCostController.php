<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\LandedCostService;
use App\Exports\Purchasing\LandedCostExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class LandedCostController extends Controller
{
    public function __construct(
        protected LandedCostService $service
    ) {}

    #[Middleware('can:purchasing.landed-cost.view')]
    public function paginate(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->paginate($request->validated());

            return Inertia::render('Purchasing/LandedCost/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
            ]);
        } catch (Exception $e) {
            Log::error('Landed Cost Paginate Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Purchasing/LandedCost/Index', [
                'data' => [
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'per_page' => 25,
                ],
                'filters' => [
                    'search' => '',
                    'sortField' => 'created_at',
                    'sortOrder' => -1,
                ],
                'error' => 'Failed to load landed cost records.'
            ]);
        }
    }

    #[Middleware('can:purchasing.landed-cost.view')]
    public function show(int $id): Response
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                abort(404);
            }

            return Inertia::render('Purchasing/LandedCost/Show', [
                'landedCost' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Landed Cost Show Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Internal Server Error');
        }
    }

    #[Middleware('can:purchasing.landed-cost.export')]
    public function export(BasicPaginateRequest $request)
    {
        try {
            $data = $this->service->getAllByFilter($request->validated());
            
            $this->service->logExport($request->validated());

            return Excel::download(
                new LandedCostExport($data),
                'landed-costs-' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (Exception $e) {
            Log::error('Landed Cost Export Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export landed cost records.');
        }
    }
}
