<?php

declare(strict_types=1);

namespace App\Services\Master;

use App\Repositories\Legacy\Master\DeliveryTermRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DeliveryTermService
{
    public function __construct(
        protected DeliveryTermRepository $repository
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
