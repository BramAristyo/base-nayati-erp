<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Services\Utility\AccountTypeService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AccountTypeController extends Controller
{
    /**
     * AccountTypeController constructor.
     */
    public function __construct(
        protected AccountTypeService $service
    ) {}

    /**
     * Display a listing of account types.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->service->getAll();

            return $this->successResponse($data, 'Account types fetched successfully.');
        } catch (Exception $e) {
            Log::error('Error fetching account types: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return $this->errorResponse('An error occurred while fetching account types.', 500);
        }
    }
}
