<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\PurchaseRequestService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseRequestController extends Controller
{
    public function __construct(
        protected PurchaseRequestService $service
    ) {}

    public function paginate(BasicPaginateRequest $request): Response | JsonResponse
    {
        try {
            $data = $this->service->paginate($request->validated());

            return response()->json($data);
            return Inertia::render('Purchasing/PurchaseRequest/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sort_by', 'sort_order', 'per_page']),
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Request Paginate Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Purchasing/PurchaseRequest/Index', [
                'data' => [],
                'error' => 'Failed to load purchase requests.'
            ]);
        }
    }

    public function find(int $id): JsonResponse
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                return $this->errorResponse('Purchase request not found.', 404);
            }

            return $this->successResponse($data);
        } catch (Exception $e) {
            Log::error('Purchase Request Find Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('An error occurred while retrieving the purchase request.');
        }
    }

    public function show(int $id): Response
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                abort(404);
            }

            return Inertia::render('Purchasing/PurchaseRequest/Show', [
                'purchaseRequest' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Request Show Error: ' . $e->getMessage(), [
                'id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            abort(500, 'Internal Server Error');
        }
    }
}
