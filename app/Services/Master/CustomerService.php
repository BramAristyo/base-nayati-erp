<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Repositories\Legacy\Master\CustomerRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(
        protected CustomerRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate($filters['per_page'] ?? 25, $filters);
    }
}
