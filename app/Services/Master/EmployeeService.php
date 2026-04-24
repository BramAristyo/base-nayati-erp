<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Repositories\Legacy\Master\EmployeeRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class EmployeeService
{
    public function __construct(
        protected EmployeeRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters['per_page'] ?? 25, $filters);
    }
}
