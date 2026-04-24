<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Repositories\Legacy\Master\SupplierRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function __construct(
        protected SupplierRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters['per_page'] ?? 25, $filters);
    }
}
