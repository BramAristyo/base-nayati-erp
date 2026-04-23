export interface Permission {
    id: number;
    slug: string;
    module: string;
    sub_module?: string;
    action: string;
    created_at?: string;
    updated_at?: string;
}

export interface Role {
    id: number;
    name: string;
    slug: string;
    description?: string;
    permissions_count?: number;
    created_at?: string;
    updated_at?: string;
}

export interface ShowRole extends Role {
    permissions: Permission[];
}

export interface StoreRoleRequest {
    name: string;
    slug: string;
    description?: string;
    permission_ids: number[];
}

export interface UpdateRoleRequest extends Partial<StoreRoleRequest> {
    id: number;
}
