<?php

namespace App\Repositories\Legacy\Purchasing;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseRequestRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'purchase_request_number' => 'hpr.nota',
        'date' => 'hpr.tgl',
        'delivery_date' => 'hpr.tgldel',
        'approved_by' => 'hpr.approveby',
        'approval_date' => 'hpr.tglapp',
        'employee_name' => 'e.ket',
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
            ->where('hpr.idhpr', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('hpr')
            ->leftJoin('mdept as d', 'd.kddep', '=', 'hpr.kddep')
            ->leftJoin('mkar as e', 'e.nik', '=', 'hpr.nik')
            ->leftJoin('supplier as s', 's.KD_SUPP', '=', 'hpr.kd_supp')
            ->select([
                'hpr.idhpr as id',
                'hpr.nota as purchase_request_number',
                'hpr.tgl as date',
                'hpr.approve as status',
                'hpr.tgldel as delivery_date',
                'hpr.jnsbudget as budget_type',
                'hpr.kd_cab as branch_code',
                'hpr.approveby as approved_by',
                'hpr.tglapp as approval_date',
                'hpr.inventory as inventory_type',
                'hpr.jnspack as packaging_type',
                'hpr.gp as is_general_purchase',
                'hpr.kdlok as warehouse_code',
                'hpr.kd_supp as supplier_code',
                'hpr.instplace as installation_place',
                'hpr.adress as installation_address',
                'hpr.layout as is_layout',
                'hpr.nik as employee_nik',
                'hpr.kddep as department_code',
                'hpr.user as created_by',
                'hpr.tglentry as created_at',
                'e.ket as employee_name',
                'd.ket as department_name',
                's.nama as supplier_name',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['hpr.nota', 'e.ket', 'd.ket']);
        $this->applyDateFilter($query, $filters, 'hpr.tglentry');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'hpr.idhpr', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->delivery_date = $this->sanitizeDate($item->delivery_date);
        $item->approval_date = $this->sanitizeDate($item->approval_date);
        $item->created_at = $this->sanitizeDate($item->created_at);

        $item->status = $item->status === 'Y';
        $item->is_general_purchase = $item->is_general_purchase === 'Y';
        $item->is_layout = $item->is_layout === 'Y' || $item->is_layout === 1 || $item->is_layout === '1';
        $item->inventory_type = $item->inventory_type === 'FG' ? 'Finish Goods' : 'Raw Materials';
        $item->packaging_type = trim($item->packaging_type ?? '');

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
