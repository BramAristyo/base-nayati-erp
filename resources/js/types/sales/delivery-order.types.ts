import type { PaginateFilter } from '@/types/common/paginate.types';

export interface DeliveryOrder {
    id: number;
    delivery_order_number: string;
    date: string | null;
    sales_order_number: string | null;
    customer_name: string;
    customer_code: string;
    category: string | null;
    delivery_date: string | null;
    branch_code: string;
    status: boolean;
    approval_date: string | null;
}

export interface DeliveryOrderFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
