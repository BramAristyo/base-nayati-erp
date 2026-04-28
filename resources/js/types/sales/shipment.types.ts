import type { PaginateFilter } from '@/types/common/paginate.types';

export interface Shipment {
    id: number;
    shipment_number: string;
    invoice_number: string | null;
    invoice_date: string | null;
    delivery_order_number: string | null;
    customer_code: string;
    customer_name: string; // From repo it seems we might need to join to get customer name if not in msj
    date: string | null;
    branch_code: string;
    sales_order_number: string | null;
    warehouse_code: string | null;
    warehouse_name: string | null;
}

export interface ShipmentFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
