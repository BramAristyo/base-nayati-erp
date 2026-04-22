export interface Warehouse {
    id: number;
    name: string;
    code: string;
    branch_code: string;
    is_active: number;
    display_name?: string;
    created_at?: string;
    updated_at?: string;
    pivot?: {
        user_id: number;
        warehouse_id: number;
        is_active: number;
    };
}
