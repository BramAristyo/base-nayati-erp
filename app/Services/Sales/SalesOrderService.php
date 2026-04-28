<?php

namespace App\Services\Sales;

use App\Repositories\Legacy\Sales\SalesOrderRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class SalesOrderService
{
    public function __construct(
        protected SalesOrderRepository $repository
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
