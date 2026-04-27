<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\ReceivingService;
use App\Exports\Purchasing\ReceivingExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class ReceivingController extends Controller
{
    public function __construct(
        protected ReceivingService $service
    ) {}

    #[Middleware('can:purchasing.receiving.view')]
    public function paginate(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->paginate($request->validated());

            return Inertia::render('Purchasing/Receiving/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
            ]);
        } catch (Exception $e) {
            Log::error('Receiving Paginate Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Purchasing/Receiving/Index', [
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
                'error' => 'Failed to load receiving records.'
            ]);
        }
    }

    /**
     * API Method to find Receiving with items.
     */
    #[Middleware('can:purchasing.receiving.view')]
    public function find(int $id): JsonResponse
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                return $this->errorResponse('Receiving not found.', 404);
            }

            return $this->successResponse($data);
        } catch (Exception $e) {
            Log::error('Receiving Find API Error: ' . $e->getMessage(), ['id' => $id]);
            return $this->errorResponse('An error occurred while fetching the receiving record.');
        }
    }

    #[Middleware('can:purchasing.receiving.view')]
    public function show(int $id): Response
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                abort(404);
            }

            return Inertia::render('Purchasing/Receiving/Show', [
                'receiving' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Receiving Show Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Internal Server Error');
        }
    }

    #[Middleware('can:purchasing.receiving.export')]
    public function export(BasicPaginateRequest $request)
    {
        try {
            $data = $this->service->getAllByFilter($request->validated());
            
            $this->service->logExport($request->validated());

            return Excel::download(
                new ReceivingExport($data),
                'receiving-records-' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (Exception $e) {
            Log::error('Receiving Export Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export receiving records.');
        }
    }
}
