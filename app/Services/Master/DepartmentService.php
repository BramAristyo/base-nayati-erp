<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Repositories\Legacy\Master\DepartmentRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DepartmentService
{
    public function __construct(
        protected DepartmentRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters['per_page'] ?? 25, $filters);
    }

    public function getAll(array $filters = []): Collection
    {
        return $this->repository->getAll($filters);
    }
}
