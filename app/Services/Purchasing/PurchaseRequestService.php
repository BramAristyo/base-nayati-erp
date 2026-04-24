<?php

namespace App\Services\Purchasing;

use App\Repositories\Legacy\Purchasing\PurchaseRequestRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseRequestService
{
    public function __construct(
        protected PurchaseRequestRepository $repository
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
