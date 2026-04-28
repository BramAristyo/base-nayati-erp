<?php

namespace App\Services\Sales;

use App\Repositories\Legacy\Sales\DeliveryOrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class DeliveryOrderService
{
    public function __construct(
        protected DeliveryOrderRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }

    public function getAllByFilter(array $filters = []): \Illuminate\Support\Collection
    {
        return $this->repository->getAllByFilter($filters);
    }

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }
}
