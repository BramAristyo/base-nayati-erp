<?php
namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Purchasing\PurchaseRequestService;
use App\Exports\Purchasing\PurchaseRequestExport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PurchaseRequestController extends Controller
{
    public function __construct(
        protected PurchaseRequestService $service
    ) {}

    #[Middleware('can:purchasing.purchase-request.view')]
    public function paginate(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->paginate($request->validated());

            return Inertia::render('Purchasing/PurchaseRequest/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Request Paginate Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Purchasing/PurchaseRequest/Index', [
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
                'error' => 'Failed to load purchase requests.'
            ]);
        }
    }

    /**
     * API Method to find PR with items.
     */
    #[Middleware('can:purchasing.purchase-request.view')]
    public function find(int $id): JsonResponse
    {
        try {
            $data = $this->service->find($id);

            if (!$data) {
                return $this->errorResponse('Purchase request not found.', 404);
            }

            return $this->successResponse($data);
        } catch (Exception $e) {
            Log::error('Purchase Request Find API Error: ' . $e->getMessage(), ['id' => $id]);
            return $this->errorResponse('An error occurred while fetching the purchase request.');
        }
    }

    #[Middleware('can:purchasing.purchase-request.view')]
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

    #[Middleware('can:purchasing.purchase-request.export')]
    public function export(BasicPaginateRequest $request)
    {
        try {
            $data = $this->service->getAllByFilter($request->validated());
            
            $this->service->logExport($request->validated());

            return Excel::download(
                new PurchaseRequestExport($data),
                'purchase-requests-' . now()->format('Y-m-d') . '.xlsx'
            );
        } catch (Exception $e) {
            Log::error('Purchase Request Export Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Failed to export purchase requests.');
        }
    }

    #[Middleware('can:purchasing.purchase-request.view')]
    public function listingItems(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->getListingItems($request->validated());

            return Inertia::render('Purchasing/PurchaseRequest/Listing', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page', 'start_date', 'end_date']),
            ]);
        } catch (Exception $e) {
            Log::error('Purchase Request Listing Items Error: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return Inertia::render('Purchasing/PurchaseRequest/Listing', [
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
                'error' => 'Failed to load purchase request items.'
            ]);
        }
    }
}
