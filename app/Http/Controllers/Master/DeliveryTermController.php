<?php

declare(strict_types=1);

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Common\BasicPaginateRequest;
use App\Services\Master\DeliveryTermService;
use Exception;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryTermController extends Controller
{
    public function __construct(
        protected DeliveryTermService $service
    ) {}

    #[Middleware('can:master.delivery-term.view')]
    public function paginate(BasicPaginateRequest $request): Response
    {
        try {
            $data = $this->service->paginate($request->validated());

            return Inertia::render('Master/DeliveryTerm/Index', [
                'data' => $data,
                'filters' => $request->only(['search', 'sortField', 'sortOrder', 'per_page']),
            ]);
        } catch (Exception $e) {
            Log::error('Delivery Term Paginate Error: ' . $e->getMessage());
            return Inertia::render('Master/DeliveryTerm/Index', [
                'data' => [
                    'data' => [],
                    'total' => 0,
                    'current_page' => 1,
                    'per_page' => 25,
                ],
                'filters' => [],
                'error' => 'Failed to load delivery term records.'
            ]);
        }
    }
}
