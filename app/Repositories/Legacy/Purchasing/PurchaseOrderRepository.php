<?php

namespace App\Repositories\Legacy\Purchasing;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PurchaseOrderRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'purchase_order_number' => 'hpo.nota',
        'date' => 'hpo.tgl',
        'delivery_date' => 'hpo.TGL_KRM',
        'approved_by' => 'hpo.userapp',
        'approval_date' => 'hpo.tglapp',
        'created_at' => 'hpo.id',
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
            ->where('hpo.id', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('hpo')
            ->leftJoin('supplier as s', 's.KD_SUPP', '=', 'hpo.KD_SUPP')
            ->leftJoin('mdept as d', 'd.kddep', '=', 'hpo.kddep')
            ->select([
                'hpo.id',
                'hpo.kd_cab as branch_code',
                'hpo.nota as purchase_order_number',
                'hpo.tgl as date',
                'hpo.approve as status',
                'hpo.userapp as approved_by',
                'hpo.tglapp as approval_date',
                'hpo.IPPN as is_inclusive_tax',
                'hpo.PPN as tax_percentage',
                'hpo.TOTAL as grand_total',
                'hpo.TOTALRP as grand_total_rupiah',
                'hpo.JUMLAH as sub_total',
                'hpo.JUMLAHRP as sub_total_rupiah',
                'hpo.DPP as tax_base_amount',
                'hpo.DPPRP as tax_base_amount_rupiah',
                'hpo.TPPN as tax_amount',
                'hpo.TPPNRP as tax_amount_rupiah',
                'hpo.DISC1 as discount_percentage_1',
                'hpo.DISCRP1 as discount_amount_1',
                'hpo.DISC2 as discount_percentage_2',
                'hpo.DISCRP2 as discount_amount_2',
                'hpo.DISC3 as discount_percentage_3',
                'hpo.DISCRP3 as discount_amount_3',
                'hpo.JNSPO as category',
                'hpo.KD_SUPP as supplier_code',
                's.NAMA as supplier_name',
                's.id as supplier_id',
                's.ALAMAT as supplier_address',
                's.NEGARA as supplier_country',
                's.KOTA as supplier_city',
                's.TELP1 as supplier_phone',
                's.PERSON as supplier_contact_person',
                'd.ket as department_name',
                'd.id as department_id',
                'd.kddep as department_code',
                'hpo.REPACKING as is_repacking',
                'hpo.inventory as inventory_type',
                'hpo.SBYR as supplier_payment_term',
                'hpo.TKRM as supplier_delivery_term',
                'hpo.L_I as is_local_purchase',
                'hpo.GP as is_general_purchase',
                'hpo.KONS as is_consignment',
                'hpo.RATE as currency_rate',
                'hpo.M_UANG as currency_code',
                'hpo.JT_TEMPO as due_date',
                'hpo.TGL_KRM as delivery_date',
                'hpo.tgl_krm_rev as delivery_date_revision',
                'hpo.USER as created_by',
                'hpo.TGL_UPDATE as updated_at',
                'hpo.TGLENTRY as created_at',
                'hpo.PRICECON as price_condition',
                'hpo.PACKING as packing_information',
                'hpo.DELIVERY as delivery_date_information',
                'hpo.SHIPMENT as shipment_information',
                'hpo.DESTINAT as destination_information',
                'hpo.PAYMENT as payment_information',
                'hpo.INSURANCE as insurance_information',
                'hpo.REMARK1 as remark1',
                'hpo.REMARK2 as remark2',
                'hpo.FREIGCHAR as freight_charge',
                'hpo.OTHERCHAR as other_charge',
                'hpo.biaya as other_expenses',
                'hpo.ketbiaya as other_expenses_remark',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['hpo.nota', 's.NAMA', 'hpo.KD_SUPP', 'd.ket']);
        $this->applyDateFilter($query, $filters, 'hpo.TGLENTRY');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $approvalStatus = $filters['approval_status'] ?? null;
        $query->when($approvalStatus, function ($q) use ($approvalStatus) {
            if ($approvalStatus === 'pending') {
                $q->where('hpo.approve', '!=', 'Y');
            } elseif ($approvalStatus === 'processed') {
                $q->where('hpo.approve', 'Y');
            }
        });


        $this->applySortFilter($query, $filters, 'hpo.id', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->delivery_date = $this->sanitizeDate($item->delivery_date);
        $item->delivery_date_revision = $this->sanitizeDate($item->delivery_date_revision);
        $item->approval_date = $this->sanitizeDate($item->approval_date);
        $item->due_date = $this->sanitizeDate($item->due_date);
        $item->created_at = $this->sanitizeDate($item->created_at);
        $item->updated_at = $this->sanitizeDate($item->updated_at);

        $item->status = $item->status === 'Y';
        $item->is_inclusive_tax = $item->is_inclusive_tax === 'Y';
        $item->is_consignment = $item->is_consignment === 'Y';
        $item->is_general_purchase = $item->is_general_purchase === 'Y';
        $item->is_repacking = $item->is_repacking === 'Y';
        $item->is_local_purchase = $item->is_local_purchase === 'L';
        $item->supplier_name = trim($item->supplier_name ?? '', '" ');
        $item->category = $item->category === 1 ? 'Standard' : 'Non Standard';
        $item->inventory_type = $item->inventory_type === 'FG' ? 'Finish Goods' : 'Raw Materials';

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
