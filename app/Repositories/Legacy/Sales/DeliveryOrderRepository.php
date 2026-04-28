<?php

namespace App\Repositories\Legacy\Sales;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DeliveryOrderRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'delivery_order_number' => 'hdo.NOTA',
        'date' => 'hdo.TGL',
        'sales_order_number' => 'hdo.NOP',
        'customer_name' => 'hdo.NMPROJECT',
        'customer_code' => 'hdo.KD_CUST',
        'category' => 'hdo.KATEGORY',
        'delivery_date' => 'hdo.TGLDEL',
        'branch_code' => 'hdo.KD_CAB',
        'status' => 'hdo.APPROVE',
        'approval_date' => 'hdo.TGLAPP',
        'created_at' => 'hdo.ID',
    ];

    public function paginate(int $perPage = 25, array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->transform(fn(object $item) => $this->transform($item));

        return $paginator;
    }

    public function getAllByFilter(array $filters = []): \Illuminate\Support\Collection
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        return $query->get()->map(fn(object $item) => $this->transform($item));
    }

    public function find(int $id): ?array
    {
        $item = $this->baseQuery()
            ->where('hdo.ID', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('hdo')
            ->select([
                'hdo.ID as id',
                'hdo.NOTA as delivery_order_number',
                'hdo.TGL as date',
                'hdo.NOP as sales_order_number',
                'hdo.NMPROJECT as customer_name',
                'hdo.KD_CUST as customer_code',
                'hdo.KATEGORY as category',
                'hdo.TGLDEL as delivery_date',
                'hdo.KD_CAB as branch_code',
                'hdo.APPROVE as status',
                'hdo.TGLAPP as approval_date',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['hdo.NOTA', 'hdo.NOP', 'hdo.NMPROJECT', 'hdo.KD_CUST']);
        $this->applyDateFilter($query, $filters, 'hdo.TGL');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $approvalStatus = $filters['approval_status'] ?? null;
        $query->when($approvalStatus, function ($q) use ($approvalStatus) {
            if ($approvalStatus === 'pending') {
                $q->where('hdo.APPROVE', '!=', 'Y');
            } elseif ($approvalStatus === 'processed') {
                $q->where('hdo.APPROVE', 'Y');
            }
        });

        $this->applySortFilter($query, $filters, 'hdo.ID', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->delivery_date = $this->sanitizeDate($item->delivery_date);
        $item->approval_date = $this->sanitizeDate($item->approval_date);

        $item->status = $item->status === 'Y';

        return $item;
    }

    protected function sanitizeDate(?string $date): ?string
    {
        if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
            return null;
        }

        return $date;
    }
}
