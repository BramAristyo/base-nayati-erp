export interface PurchaseOrderItem {
    id: number;
    purchase_request_item_id: number;
    product_variant_id: number;
    product_code: string;
    variant_name: string;
    product_name: string;
    purchase_request_number: string;
    quantity: number;
    price: number;
    unit: string;
    buy_price: number;
    sub_total: number;
    discount_percentage: number;
    discount_amount: number;
    price_rate: number;
    remarks: string;
    dimension_remarks: string;
    additional_remarks: string;
    is_general_purchase: boolean;
    department_code: string;
    max_qty: number;
}
