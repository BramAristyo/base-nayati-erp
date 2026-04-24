<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Services\Utility\WarehouseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    public function __construct(
        protected WarehouseService $warehouseService
    ) {}

    public function getAll(): JsonResponse
    {
        try {
            return $this->successResponse($this->warehouseService->getAll(), 'Warehouses fetched successfully.');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse('An error occurred while fetching warehouses.', 500);
        }
    }
}
