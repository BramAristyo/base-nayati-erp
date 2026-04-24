<?php

namespace App\Services\Purchasing;

use App\Repositories\Legacy\Purchasing\PurchaseOrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseOrderService
{
    public function __construct(
        protected PurchaseOrderRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }
}
