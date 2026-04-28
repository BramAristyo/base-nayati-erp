import type { PaginateFilter } from '@/types/common/paginate.types';

export interface Invoice {
    id: number;
    invoice_number: string;
    date: string | null;
    branch_code: string;
    sales_order_number: string | null;
    customer_code: string;
    customer_name: string;
    total: number;
    status: boolean;
}

export interface InvoiceFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
