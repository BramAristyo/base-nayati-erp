<?php

namespace App\Repositories\Legacy\Sales;

use App\Traits\HasFilterableQuery;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SalesOrderRepository
{
    use HasFilterableQuery;

    private array $sortableFields = [
        'sales_order_number' => 'mo.nop',
        'date' => 'mo.TGS',
        'customer_code' => 'mo.KDCUST',
        'branch_code' => 'mo.KDCAB',
        'project_name' => 'mo.KET',
        'delivery_date' => 'mo.TGD',
        'due_date' => 'mo.JTHTMP',
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
            ->where('mo.id', $id)
            ->first();

        if (!$item) {
            return null;
        }

        return (array) $this->transform($item);
    }

    protected function baseQuery(): Builder
    {
        return DB::table('mo')
            ->leftJoin('custom as c', 'c.kd_cust', '=', 'mo.KDCUST')
            ->select([
                'mo.id',
                'mo.nop as sales_order_number',
                'mo.NOPI as invoice_number',
                'mo.KDCUST as customer_code',
                'c.NAMA as customer_name',
                'mo.KDCAB as branch_code',
                'mo.cperson as contact_person',
                'mo.KDSEG as customer_segment',
                'mo.KET as project_name',
                'mo.TGS as date',
                'mo.TGD as delivery_date',
                'mo.JTHTMP as due_date',
                'mo.VERIFIKASI as status',
                'mo.LE as order_type',
                'mo.IEPPN as tax_type',
                'mo.KDCUR as currency_code',
                'mo.NILAI as currency_rate',
                'mo.TERM1 as term_1',
                'mo.TERM2 as term_2',
                'mo.TERM3 as term_3',
                'mo.TERM4 as term_4',
                'mo.TERM5 as term_5',
                'mo.TERM6 as term_6',
                'mo.EVEN1 as term_description_1',
                'mo.EVEN2 as term_description_2',
                'mo.EVEN3 as term_description_3',
                'mo.EVEN4 as term_description_4',
                'mo.EVEN5 as term_description_5',
                'mo.EVEN6 as term_description_6',
                'mo.DP as down_payment',
                'mo.NIK as salesman_nik',
                'mo.PENG as delivery_term',
                'mo.TOT as net_price',
                'mo.DISC as discount_percentage',
                'mo.TDISC as discount_amount',
                'mo.DISC1 as discount_percentage_2',
                'mo.TDISC1 as discount_amount_2',
                'mo.ehc as commission',
                'mo.BIAYA as installation_price',
                'mo.KETBIA as installation_price_remark',
                'mo.DISCINSTAL as installation_price_discount_percentage',
                'mo.DISCINST as installation_price_discount_amount',
                'mo.BPACKING as packing_cost',
                'mo.BKIRIM as delivery_cost',
                'mo.TGL_UPDATE as updated_at',
            ]);
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $this->applySearchFilter($query, $filters, ['mo.nop', 'c.NAMA', 'mo.KDCUST', 'mo.KET']);
        $this->applyDateFilter($query, $filters, 'mo.TGS');

        if (isset($filters['sort_by'], $this->sortableFields[$filters['sort_by']])) {
            $filters['sort_by'] = $this->sortableFields[$filters['sort_by']];
        }

        $status = $filters['status'] ?? null;
        $query->when($status, function ($q) use ($status) {
            if ($status === 'approved') {
                $q->where('mo.VERIFIKASI', 'Y');
            } elseif ($status === 'not_approved') {
                $q->where('mo.VERIFIKASI', '!=', 'Y');
            }
        });

        $this->applySortFilter($query, $filters, 'mo.id', 'desc');
    }

    protected function transform(object $item): object
    {
        $item->date = $this->sanitizeDate($item->date);
        $item->delivery_date = $this->sanitizeDate($item->delivery_date);
        $item->due_date = $this->sanitizeDate($item->due_date);
        $item->updated_at = $this->sanitizeDate($item->updated_at);

        $item->status = $item->status === 'Y';
        $item->net_price = (float) $item->net_price;
        $item->currency_rate = (float) $item->currency_rate;
        $item->down_payment = (float) $item->down_payment;
        
        $item->customer_name = trim($item->customer_name ?? '');
        $item->project_name = trim($item->project_name ?? '');

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
