<?php

namespace App\Repositories\Legacy\Sales;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProformaRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'proforma_number' => 'mproforma.NOIN',
        'invoice_number' => 'mproforma.No_kwitansi',
        'date' => 'mproforma.TGI',
        'customer_name' => 'mproforma.NMCUST',
        'customer_code' => 'mproforma.KDCUST',
        'branch_code' => 'mproforma.KDCAB',
        'status' => 'mproforma.approve1',
        'created_at' => 'mproforma.ID',
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
            ->where('mproforma.ID', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('mproforma')
            ->select([
                'mproforma.ID as id',
                'mproforma.NOIN as proforma_number',
                'mproforma.No_kwitansi as invoice_number',
                'mproforma.TGI as date',
                'mproforma.KDCAB as branch_code',
                'mproforma.NOP as sales_order_number',
                'mproforma.TOTSO as bruto',
                'mproforma.TDISC as discount_amount_1',
                'mproforma.TDISC1 as discount_amount_2',
                'mproforma.BINSTALL as installation_cost',
                'mproforma.TERM as payment_term',
                'mproforma.PPN as tax_amount',
                'mproforma.TDP as netto',
                'mproforma.KDCUST as customer_code',
                'mproforma.NMCUST as customer_name',
                'mproforma.DP as down_payment_percentage',
                'mproforma.approve1 as status',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['mproforma.NOIN', 'mproforma.NMCUST', 'mproforma.KDCUST']);
        $this->applyDateFilter($query, $filters, 'mproforma.TGI');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $approvalStatus = $filters['approval_status'] ?? null;
        $query->when($approvalStatus, function ($q) use ($approvalStatus) {
            if ($approvalStatus === 'pending') {
                $q->where('mproforma.approve1', '!=', 'Y');
            } elseif ($approvalStatus === 'processed') {
                $q->where('mproforma.approve1', 'Y');
            }
        });

        $this->applySortFilter($query, $filters, 'mproforma.ID', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);

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
