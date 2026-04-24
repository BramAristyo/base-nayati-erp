import type { PaginateFilter } from '@/types/common/paginate.types';

export interface Receiving {
    id: number;
    branch_code: string;
    receiving_number: string;
    status: boolean;
    approved_by: string | null;
    approval_date: string | null;
    date: string | null;
    supplier_invoice_number: string | null;
    supplier_invoice_date: string | null;
    purchase_order_number: string | null;
    purchase_order_id: number | null;
    purchase_order_date: string | null;
    delivery_date: string | null;
    is_inclusive_tax: boolean;
    tax_percentage: number;
    tax_base_amount: number;
    tax_amount_rupiah: number;
    discount_percentage_1: number;
    grand_total: number;
    is_general_purchase: boolean;
    is_consignment: boolean;
    is_local_purchase: boolean;
    is_repacking: boolean;
    inventory_type: string;
    category: string;
    supplier_code: string;
    supplier_name: string;
    supplier_id: number;
    department_name: string;
    department_id: number;
    department_code: string;
    warehouse_name: string;
    warehouse_code: string;
    currency_code: string;
    currency_rate: number;
    due_date: string | null;
    remarks: string | null;
    account_type_code: string | null;
    account_type_name: string | null;
    created_by: string;
    created_at: string | null;
    updated_at: string | null;
}

export interface ReceivingFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
