import type { PaginateFilter } from '@/types/common/paginate.types';

export interface SalesOrder {
    id: number;
    branch_code: string;
    sales_order_number: string;
    date: string | null;
    customer_name: string;
    project_name: string | null;
    status: boolean;
    grand_total: number;
    delivery_date: string | null;
    
    // Additional fields for consistency with Purchasing/Common patterns
    customer_code: string;
    customer_id: number;
    currency_code: string;
    currency_rate: number;
    approved_by: string | null;
    approval_date: string | null;
    created_by: string;
    created_at: string | null;
    updated_at: string | null;
}

export interface SalesOrderFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
