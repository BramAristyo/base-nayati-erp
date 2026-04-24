import type { PaginateFilter } from '@/types/common/paginate.types';

export interface LandedCost {
    id: number;
    landed_cost_number: string;
    landed_cost_date: string | null;
    receiving_number: string;
    purchase_order_number: string;
    supplier_invoice_number: string | null;
    supplier_code: string;
    created_by: string;
    created_at: string | null;

    // Financials
    sub_total: number;
    discount_percentage_1: number;
    discount_amount_1: number;
    factor_cost: number;
    currency_code: string;
    currency_rate: number;

    // Extra Charges
    air_freight_charge: number;
    sea_freight_charge: number;
    freight_currency_code: string | null;
    freight_currency_rate: number;
    freight_supplier_code: string | null;
    freight_remark: string | null;
    
    insurance_charge: number;
    insurance_currency_rate: number;
    insurance_supplier_code: string | null;
    insurance_remark: string | null;

    bea_charge: number;
    pnbp_charge: number;
    bea_supplier_code: string | null;
    bea_remark: string | null;

    packing_charge: number;
    packing_currency_rate: number;
    packing_supplier_code: string | null;
    packing_remark: string | null;

    emkl_unit: string | null;
    emkl_unit_rate: number;
    emkl_charge: number;
    emkl_supplier_code: string | null;

    forwarding_charge: number;
    forwarding_currency_code: string | null;
    forwarding_currency_rate: number;
    forwarding_supplier_code: string | null;

    delivery_charge: number;
    delivery_currency_code: string | null;
    delivery_currency_rate: number;

    bank_charge: number;
    lc_opening_charge: number;
    lc_opening_currency_rate: number;
    lc_settlement_charge: number;
    lc_settlement_currency_rate: number;

    margin_charge: number;
    other_charge: number;
    other_charge_currency_code: string | null;
    other_charge_currency_rate: number;

    survey_cost_charge: number;
    survey_cost_currency_code: string | null;
    survey_cost_currency_rate: number;
    survey_supplier_code: string | null;
}

export interface LandedCostFilters extends PaginateFilter {
    start_date?: string;
    end_date?: string;
}
