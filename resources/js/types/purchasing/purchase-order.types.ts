import type { PaginateFilter } from '@/types/common/paginate.types';

export interface PurchaseOrder {
    id: number;
    branch_code: string;
    purchase_order_number: string;
    date: string | null;
    status: boolean;
    approved_by: string | null;
    approval_date: string | null;
    is_inclusive_tax: boolean;
    tax_percentage: number;
    grand_total: number;
    category: string;
    supplier_code: string;
    supplier_name: string;
    is_general_purchase: boolean;
    delivery_date: string | null;
    due_date: string | null;
    inventory_type: string;
    department_name: string;
    created_at: string | null;
    updated_at: string | null;
}

export interface PurchaseOrderFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
