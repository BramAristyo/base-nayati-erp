<?php

namespace App\Services\Purchasing;

use App\Enums\LogAction;
use App\Enums\LogModule;
use App\Repositories\Legacy\Purchasing\ReceivingRepository;
use App\Traits\Trailable;
use Illuminate\Pagination\LengthAwarePaginator;

class ReceivingService
{
    use Trailable;

    public function __construct(
        protected ReceivingRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }

    public function getAllByFilter(array $filters): \Illuminate\Support\Collection
    {
        return $this->repository->getAllByFilter($filters);
    }

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function logExport(array $filters): void
    {
        $count = $this->repository->getAllByFilter($filters)->count();
        $this->trail(
            LogModule::PURCHASING,
            LogAction::EXPORT,
            "Exported {$count} receiving records to Excel"
        );
    }
}
