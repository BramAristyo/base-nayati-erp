export interface PurchaseRequestItem {
    id: number;
    product_variant_id: number;
    product_code: string;
    variant_name: string;
    product_name: string;
    quantity: number;
    price: number;
    unit: string;
    usage_date: string | null;
    supplier_name: string;
    brand: string;
    remarks: string;
}
