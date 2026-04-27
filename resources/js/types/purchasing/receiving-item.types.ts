export interface ReceivingItem {
    id: number;
    purchase_order_item_id: number;
    purchase_order_number: string;
    product_variant_id: number;
    variant_name: string | null;
    product_code: string;
    product_barcode: string;
    product_name: string;
    quantity: number;
    unit: string;
    buy_price: number;
    unit_cost: number;
    price: number;
    account_type_code: string;
    account_type_name: string;
    serial_number: string;
    warranty_number: string;
}
