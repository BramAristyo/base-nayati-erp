<?php

namespace App\Services\Purchasing;

use App\Enums\LogAction;
use App\Enums\LogModule;
use App\Repositories\Legacy\Purchasing\ReceivingRepository;
use App\Repositories\Legacy\Purchasing\ReceivingItemRepository;
use App\Traits\Trailable;
use Illuminate\Pagination\LengthAwarePaginator;

class ReceivingService
{
    use Trailable;

    public function __construct(
        protected ReceivingRepository $repository,
        protected ReceivingItemRepository $itemRepository
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
        $receiving = $this->repository->find($id);

        if (!$receiving) {
            return null;
        }

        $receiving['items'] = $this->itemRepository->getByHeaderNumber($receiving['receiving_number'])->toArray();

        return $receiving;
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
