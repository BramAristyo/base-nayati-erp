import type { PaginateFilter } from '@/types/common/paginate.types';

export interface Proforma {
    id: number;
    proforma_number: string;
    invoice_number: string | null;
    date: string | null;
    branch_code: string;
    sales_order_number: string | null;
    customer_code: string;
    customer_name: string;
    netto: number;
    status: boolean;
}

export interface ProformaFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
