<?php

namespace App\Services\Purchasing;

use App\Enums\LogAction;
use App\Enums\LogModule;
use App\Repositories\Legacy\Purchasing\PurchaseRequestRepository;
use App\Traits\Trailable;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseRequestService
{
    use Trailable;

    public function __construct(
        protected PurchaseRequestRepository $repository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }

    public function getAll(array $filters): \Illuminate\Support\Collection
    {
        return $this->repository->getAll($filters);
    }

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function logExport(array $filters): void
    {
        $count = $this->repository->getAll($filters)->count();
        $this->trail(
            LogModule::PURCHASING,
            LogAction::EXPORT,
            "Exported {$count} purchase requests to Excel"
        );
    }
}
