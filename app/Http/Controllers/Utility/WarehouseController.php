<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Utility\Warehouse;
use Exception;
use Illuminate\Support\Facades\Log;

class WarehouseController extends Controller
{
    public function getAll()
    {
        try {
            $warehouses = Warehouse::active()->get();

            return $this->successResponse(
                $warehouses,
                'Warehouses fetched successfully',
                200
            );
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return $this->errorResponse(
                'Failed to fetch warehouses',
                500,
            );
        }
    }
}
