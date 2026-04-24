export interface PurchaseRequest {
    id: number;
    purchase_request_number: string;
    date: string | null;
    status: boolean;
    delivery_date: string | null;
    budget_type: string;
    branch_code: string;
    approved_by: string | null;
    approval_date: string | null;
    inventory_type: string;
    packaging_type: string;
    is_general_purchase: boolean;
    warehouse_code: string;
    supplier_code: string;
    installation_place: string | null;
    installation_address: string | null;
    is_layout: boolean;
    employee_nik: string;
    department_code: string;
    created_by: string;
    created_at: string | null;
    employee_name: string;
    department_name: string;
    supplier_name: string | null;
}

export interface PurchaseRequestFilters {
    search: string | null;
    sort_by: string;
    sort_order: 'asc' | 'desc';
    per_page: number;
    start_date: string | null;
    end_date: string | null;
    budget_type: string | null;
    inventory_type: string | null;
    department_id: number[] | null;
}
