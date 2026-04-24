export interface Branch {
    code: string;
    name: string;
    address: string | null;
    npwp: string | null;
    phone: string | null;
    email: string | null;
    is_active: boolean;
}

export interface Currency {
    code: string;
    name: string;
    rate: number;
}

export interface Customer {
    id: number;
    branch_code: string;
    code: string;
    name: string;
    commercial_name: string | null;
    city: string | null;
    address: string | null;
    other_address: string | null;
    phone: string | null;
    other_phone: string | null;
    npwp: string | null;
    is_has_sales_order: boolean;
}

export interface DeliveryTerm {
    id: number;
    name: string;
}

export interface Department {
    id: number;
    code: string;
    name: string;
    created_at: string | null;
}

export interface Employee {
    nik: string;
    name: string;
    address: string | null;
    city: string | null;
    phone: string | null;
    mobile_phone: string | null;
}

export interface Supplier {
    id: number;
    code: string;
    name: string;
    address: string | null;
    city: string | null;
    country: string | null;
    phone: string | null;
    fax: string | null;
    tin: string | null;
    contact_person: string | null;
    updated_at: string | null;
}
