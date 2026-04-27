<?php

namespace App\Services\Purchasing;

use App\Enums\LogAction;
use App\Enums\LogDetailRoute;
use App\Enums\LogModule;
use App\Repositories\Legacy\Purchasing\PurchaseOrderRepository;
use App\Traits\Trailable;
use Illuminate\Pagination\LengthAwarePaginator;

class PurchaseOrderService
{
    use Trailable;

    public function __construct(
        protected PurchaseOrderRepository $repository
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
            "Exported {$count} purchase orders to Excel",
            null,
            LogDetailRoute::PURCHASE_ORDER_INDEX
        );
    }

    public function getApprovalList(array $filters): LengthAwarePaginator
    {
        $status = $filters['approval_status'] ?? 'pending';

        $filters['approval_status'] = $status === 'processed' ? 'processed' : 'pending';

        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }
}
