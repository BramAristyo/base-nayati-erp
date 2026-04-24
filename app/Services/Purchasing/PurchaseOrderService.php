<?php

namespace App\Services\Purchasing;

use App\Enums\LogAction;
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

    public function find(int $id): ?array
    {
        return $this->repository->find($id);
    }

    public function logPrint(int $id): void
    {
        $po = $this->repository->find($id);
        $this->trail(
            LogModule::PURCHASING,
            LogAction::PRINT,
            "Printed Purchase Order: " . ($po['purchase_order_number'] ?? $id),
            $id
        );
    }

    public function logExport(array $filters): void
    {
        $count = $this->repository->getAll($filters)->count();
        $this->trail(
            LogModule::PURCHASING,
            LogAction::EXPORT,
            "Exported {$count} purchase orders to Excel"
        );
    }
}
