<?php

namespace App\Repositories\Legacy\Purchasing;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LandedCostRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'landed_cost_number' => 'bimport.NOKAL',
        'landed_cost_date' => 'bimport.TGL',
        'receiving_number' => 'bimport.NOLPB',
        'purchase_order_number' => 'bimport.NOPO',
        'created_at' => 'bimport.tglentry',
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
            ->where('bimport.ID', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('bimport')
            ->leftJoin('hbeli', 'hbeli.nota', '=', 'bimport.NOLPB')
            ->select([
                'bimport.ID as id',
                'bimport.NOKAL as landed_cost_number',
                'bimport.TGL as landed_cost_date',
                'bimport.NOLPB as receiving_number',
                'bimport.NOPO as purchase_order_number',
                'bimport.NOIN as supplier_invoice_number',
                'hbeli.kd_supp as supplier_code',
                'bimport.user as created_by',
                'bimport.tglentry as created_at',

                // Financials (Standardized)
                'bimport.EXWORK as sub_total',
                'bimport.DISC as discount_percentage_1',
                'bimport.TDISC as discount_amount_1',
                'bimport.FCOST as factor_cost',
                'hbeli.m_uang as currency_code',
                'hbeli.RATE as currency_rate',

                // Extra Charges - Freight & Insurance
                'bimport.AIRF as air_freight_charge',
                'bimport.SEAF as sea_freight_charge',
                'bimport.KDCUR3 as freight_currency_code',
                'bimport.NILAI3 as freight_currency_rate',
                'bimport.KDSPLYA as freight_supplier_code',
                'bimport.REMARKA as freight_remark',
                'bimport.INS as insurance_charge',
                'bimport.RATEINS as insurance_currency_rate',
                'bimport.KDSPLYD as insurance_supplier_code',
                'bimport.REMARKD as insurance_remark',

                // Extra Charges - Duties & Taxes
                'bimport.BEA as bea_charge',
                'bimport.PNBP as pnbp_charge',
                'bimport.KDSPLYB as bea_supplier_code',
                'bimport.REMARKB as bea_remark',

                // Extra Charges - Logistik & EMKL
                'bimport.PAKING as packing_charge',
                'bimport.RATEPACK as packing_currency_rate',
                'bimport.KDSPLYE as packing_supplier_code',
                'bimport.REMARKC as packing_remark',
                'bimport.ROYSAT as emkl_unit',
                'bimport.ROYJUM as emkl_unit_rate',
                'bimport.ROYNOM as emkl_charge',
                'bimport.KDSPLY as emkl_supplier_code',
                'bimport.FORWA as forwarding_charge',
                'bimport.KDCUR4 as forwarding_currency_code',
                'bimport.NILAI4 as forwarding_currency_rate',
                'bimport.KDSPLY1 as forwarding_supplier_code',
                'bimport.ANGKUT as delivery_charge',
                'bimport.KDCUR2 as delivery_currency_code',
                'bimport.NILAI2 as delivery_currency_rate',

                // Extra Charges - Bank & Others
                'bimport.BANK as bank_charge',
                'bimport.LC1 as lc_opening_charge',
                'bimport.LC1RATE as lc_opening_currency_rate',
                'bimport.LC2 as lc_settlement_charge',
                'bimport.LC2RATE as lc_settlement_currency_rate',
                'bimport.OUR as margin_charge',
                'bimport.LAIN as other_charge',
                'bimport.KDCUR1 as other_charge_currency_code',
                'bimport.NILAI1 as other_charge_currency_rate',
                'bimport.SURVAI as survey_cost_charge',
                'bimport.CURSURVAI as survey_cost_currency_code',
                'bimport.RATESURVAI as survey_cost_currency_rate',
                'bimport.KDSPLYF as survey_supplier_code',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, [
            'bimport.NOKAL',
            'bimport.NOLPB',
            'bimport.NOPO',
            'hbeli.kd_supp',
            'bimport.NOIN'
        ]);
        
        $this->applyDateFilter($query, $filters, 'bimport.TGL');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $this->applySortFilter($query, $filters, 'bimport.ID', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->landed_cost_date = $this->sanitizeDate($item->landed_cost_date);
        $item->created_at = $this->sanitizeDate($item->created_at);

        $item->sub_total = (float) $item->sub_total;
        $item->discount_percentage_1 = (float) $item->discount_percentage_1;
        $item->discount_amount_1 = (float) $item->discount_amount_1;
        $item->factor_cost = (float) $item->factor_cost;
        $item->currency_rate = (float) $item->currency_rate;

        $item->air_freight_charge = (float) $item->air_freight_charge;
        $item->sea_freight_charge = (float) $item->sea_freight_charge;
        $item->freight_currency_rate = (float) $item->freight_currency_rate;
        $item->insurance_charge = (float) $item->insurance_charge;
        $item->insurance_currency_rate = (float) $item->insurance_currency_rate;
        $item->bea_charge = (float) $item->bea_charge;
        $item->pnbp_charge = (float) $item->pnbp_charge;
        $item->packing_charge = (float) $item->packing_charge;
        $item->packing_currency_rate = (float) $item->packing_currency_rate;
        $item->emkl_unit_rate = (float) $item->emkl_unit_rate;
        $item->emkl_charge = (float) $item->emkl_charge;
        $item->forwarding_charge = (float) $item->forwarding_charge;
        $item->forwarding_currency_rate = (float) $item->forwarding_currency_rate;
        $item->delivery_charge = (float) $item->delivery_charge;
        $item->delivery_currency_rate = (float) $item->delivery_currency_rate;
        $item->bank_charge = (float) $item->bank_charge;
        $item->lc_opening_charge = (float) $item->lc_opening_charge;
        $item->lc_opening_currency_rate = (float) $item->lc_opening_currency_rate;
        $item->lc_settlement_charge = (float) $item->lc_settlement_charge;
        $item->lc_settlement_currency_rate = (float) $item->lc_settlement_currency_rate;
        $item->margin_charge = (float) $item->margin_charge;
        $item->other_charge = (float) $item->other_charge;
        $item->other_charge_currency_rate = (float) $item->other_charge_currency_rate;
        $item->survey_cost_charge = (float) $item->survey_cost_charge;
        $item->survey_cost_currency_rate = (float) $item->survey_cost_currency_rate;

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
