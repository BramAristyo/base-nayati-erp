<?php

namespace App\Services\Purchasing;

use App\Enums\LogAction;
use App\Enums\LogModule;
use App\Repositories\Legacy\Purchasing\PurchaseRequestRepository;
use App\Repositories\Legacy\Purchasing\PurchaseRequestItemRepository;
use App\Traits\Trailable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PurchaseRequestService
{
    use Trailable;

    public function __construct(
        protected PurchaseRequestRepository $repository,
        protected PurchaseRequestItemRepository $itemRepository
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->repository->paginate(
            $filters['per_page'] ?? 25,
            $filters
        );
    }

    public function getAllByFilter(array $filters): Collection
    {
        return $this->repository->getAllByFilter($filters);
    }

    public function find(int $id): ?array
    {
        $data = $this->repository->find($id);

        if ($data) {
            $data['items'] = $this->itemRepository->getByHeaderNumber($data['purchase_request_number']);
        }

        return $data;
    }

    public function logExport(array $filters): void
    {
        $count = $this->repository->getAllByFilter($filters)->count();
        $this->trail(
            LogModule::PURCHASING,
            LogAction::EXPORT,
            "Exported {$count} purchase requests to Excel"
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

    public function getListingItems(array $filters): LengthAwarePaginator
    {
        return $this->itemRepository->getListings(
            $filters['per_page'] ?? 25,
            $filters
        );
    }
}
