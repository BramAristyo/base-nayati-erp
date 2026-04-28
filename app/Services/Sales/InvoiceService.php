<?php

namespace App\Services\Sales;

use App\Repositories\Legacy\Sales\InvoiceRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceService
{
    public function __construct(
        protected InvoiceRepository $repository
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
