<?php

namespace App\Repositories\Legacy\Sales;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'invoice_number' => 'minv.NOIN',
        'date' => 'minv.TGI',
        'customer_name' => 'c.NAMA',
        'branch_code' => 'minv.kdcab',
        'status' => 'minv.APPROVE1',
        'updated_at' => 'minv.TGLUPDATE',
    ];

    public function paginate(int $perPage = 25, array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->transform(fn(object $item) => $this->transform($item));

        return $paginator;
    }

    public function find(int $id): ?array
    {
        $item = $this->baseQuery()
            ->where('minv.IDMINV', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('minv')
            ->leftJoin('custom as c', 'minv.kdcust', '=', 'c.kd_cust')
            ->select([
                'minv.IDMINV as id',
                'minv.NOIN as invoice_number',
                'minv.TGI as date',
                'minv.kdcab as branch_code',
                'minv.NOP as sales_order_number',
                'minv.KDCUST as customer_code',
                'c.NAMA as customer_name',
                'minv.NAMA as created_by',
                'minv.TOTAL as total',
                'minv.BIAYA as installation_cost',
                'minv.BKIRIM as delivery_cost',
                'minv.BPACKING as packing_cost',
                'minv.APPROVE1 as status',
                'minv.TGLUPDATE as updated_at',
                'minv.STOT as subtotal',
                'minv.PPN as tax_amount',
                'minv.DISC as discount_percentage',
                'minv.TDISC as discount_amount',
                'minv.DISC1 as discount_percentage_1',
                'minv.TDISC1 as discount_amount_1',
                'minv.DP as down_payment',
                'minv.TDP as down_payment_amount',
                'minv.KETBIA as remark_cost',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['minv.NOIN', 'c.NAMA', 'minv.KDCUST']);
        $this->applyDateFilter($query, $filters, 'minv.TGI');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $approvalStatus = $filters['approval_status'] ?? null;
        $query->when($approvalStatus, function ($q) use ($approvalStatus) {
            if ($approvalStatus === 'pending') {
                $q->where('minv.APPROVE1', '!=', 'Y');
            } elseif ($approvalStatus === 'processed') {
                $q->where('minv.APPROVE1', 'Y');
            }
        });

        $this->applySortFilter($query, $filters, 'minv.IDMINV', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->updated_at = $this->sanitizeDate($item->updated_at);

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
