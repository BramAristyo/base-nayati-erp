<?php

namespace App\Services\Purchasing;

use App\Enums\LogAction;
use App\Enums\LogDetailRoute;
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
            "Exported {$count} purchase requests to Excel",
            null,
            LogDetailRoute::PURCHASE_REQUEST_INDEX
        );
    }

    public function getPending(array $filters): LengthAwarePaginator
    {
        $filters['approval_status'] = 'pending';

        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }

    public function getProcessed(array $filters): LengthAwarePaginator
    {
        $filters['approval_status'] = 'processed';

        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }
}
