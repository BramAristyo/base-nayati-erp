<?php

namespace App\Services\Sales;

use App\Repositories\Legacy\Sales\ProformaRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ProformaService
{
    public function __construct(
        protected ProformaRepository $repository
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
