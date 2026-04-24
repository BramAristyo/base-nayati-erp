<?php

namespace App\Repositories\Legacy\Purchasing;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReceivingRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'receiving_number' => 'hbeli.nota',
        'date' => 'hbeli.tgl',
        'approval_date' => 'hbeli.tglapp',
        'created_by' => 'hbeli.USER',
        'purchase_order_date' => 'po.tgl',
        'supplier_invoice_date' => 'hbeli.tgl1',
    ];

    public function paginate(int $perPage = 25, array $filters = []): LengthAwarePaginator
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage)->withQueryString();

        $paginator->getCollection()->transform(fn(object $item) => $this->transform($item));

        return $paginator;
    }

    public function getAllByFilter(array $filters = []): Collection
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $filters);

        return $query->get()->map(fn(object $item) => $this->transform($item));
    }

    public function find(int $id): ?array
    {
        $item = $this->baseQuery()
            ->where('hbeli.idhbeli', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('hbeli')
            ->leftJoin('supplier as s', 's.KD_SUPP', '=', 'hbeli.kd_supp')
            ->leftJoin('mdept as d', 'd.kddep', '=', 'hbeli.kddep')
            ->leftJoin('warehouses as w', 'w.code', '=', 'hbeli.kdlok')
            ->leftJoin('hpo as po', 'po.nota', '=', 'hbeli.noop')
            ->select([
                'hbeli.idhbeli as id',
                'hbeli.kd_cab as branch_code',
                'hbeli.nota as receiving_number',
                'hbeli.approve as status',
                'hbeli.userapp as approved_by',
                'hbeli.tglapp as approval_date',
                'hbeli.tgl as date',
                'hbeli.nota1 as supplier_invoice_number',
                'hbeli.tgl1 as supplier_invoice_date',
                'hbeli.noop as purchase_order_number',
                'po.id as purchase_order_id',
                'po.tgl as purchase_order_date',
                'hbeli.IPPN as is_inclusive_tax',
                'hbeli.PPN as tax_percentage',
                'hbeli.GP as is_general_purchase',
                'hbeli.STATUS as is_consignment',
                'hbeli.inventory as inventory_type',
                'hbeli.kd_supp as supplier_code',
                'hbeli.ket as supplier_name',
                's.id as supplier_id',
                'd.kddep as department_code',
                'd.ket as department_name',
                'd.id as department_id',
                'hbeli.m_uang as currency_code',
                'hbeli.JT_TEMPO as due_date',
                'hbeli.L_I as is_local_purchase',
                'hbeli.DPP as tax_base_amount',
                'hbeli.DPPRP as tax_base_amount_rupiah',
                'hbeli.PPNRP as tax_amount_rupiah',
                'hbeli.RATE as currency_rate',
                'hbeli.JNSBELI as category',
                'hbeli.DISC1 as discount_percentage_1',
                'hbeli.REPACKING as is_repacking',
                'hbeli.USER as created_by',
                'hbeli.keterangan as remarks',
                'w.code as warehouse_code',
                'w.name as warehouse_name',
                'hbeli.T_HARGA as grand_total',
                'po.TGL_KRM as delivery_date',
                DB::raw('(SELECT kd6 FROM dbeli WHERE dbeli.nota = hbeli.nota LIMIT 1) as account_type_code'),
                DB::raw('(SELECT settrnket FROM dbeli WHERE dbeli.nota = hbeli.nota LIMIT 1) as account_type_name'),
                'hbeli.TGL_UPDATE as updated_at',
                'hbeli.TGLENTRY as created_at',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['hbeli.nota', 'hbeli.ket', 'w.name', 'hbeli.userapp', 'hbeli.nota1', 'hbeli.USER']);
        $this->applyDateFilter($query, $filters, 'hbeli.TGLENTRY');
        $this->applyStatusFilter($query, $filters, 'hbeli.approve', 'Y');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'hbeli.idhbeli', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->delivery_date = $this->sanitizeDate($item->delivery_date);
        $item->approval_date = $this->sanitizeDate($item->approval_date);
        $item->supplier_invoice_date = $this->sanitizeDate($item->supplier_invoice_date);
        $item->due_date = $this->sanitizeDate($item->due_date);
        $item->created_at = $this->sanitizeDate($item->created_at);
        $item->updated_at = $this->sanitizeDate($item->updated_at);
        $item->purchase_order_date = $this->sanitizeDate($item->purchase_order_date);

        $item->status = $item->status === 'Y';
        $item->is_inclusive_tax = $item->is_inclusive_tax === 'Y';
        $item->is_consignment = $item->is_consignment === 'Y';
        $item->is_general_purchase = $item->is_general_purchase === 'Y';
        $item->is_local_purchase = $item->is_local_purchase === 'L';
        $item->is_repacking = $item->is_repacking === 'Y';

        $item->category = $item->category === 1 ? 'Standard' : 'Non Standard';
        $item->inventory_type = $item->inventory_type === 'FG' ? 'Finish Goods' : 'Raw Materials';
        $item->supplier_name = trim($item->supplier_name ?? '', '" ');

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
