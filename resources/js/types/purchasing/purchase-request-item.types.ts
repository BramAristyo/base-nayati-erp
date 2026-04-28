export interface PurchaseRequestItem {
    id: number;
    purchase_request_number: string;
    date: string | null;
    product_variant_id: number;
    product_code: string;
    variant_name: string;
    product_name: string;
    quantity: number;
    adjusted_quantity: number;
    ordered_quantity: number;
    price: number;
    unit: string;
    usage_date: string | null;
    supplier_name: string;
    brand: string;
    remarks: string;
}
