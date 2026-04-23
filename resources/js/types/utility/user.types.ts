import type { PaginatedResponse } from '../common/paginate.types';
import type { Role, Permission } from './role-permissions.types';
import type { Warehouse } from './warehouse.types';

export interface User {
    id: number;
    name: string;
    email: string;
    approver_name: string;
    approver_title: string;
    branch_code: string;
    position: string;
    is_active: number;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    is_password_changed: number;
    roles?: Role[];
    permissions?: (Permission & { pivot: { is_denied: boolean } })[];
    warehouses?: Warehouse[];
}

export interface ShowUser extends User {
    roles: Role[];
    warehouses: Warehouse[];
    permissions: (Permission & { pivot: { is_denied: boolean } })[];
}

export interface StoreUserRequest {
    name: string;
    email: string;
    approver_name: string;
    approver_title: string;
    branch_code: string;
    position: string;
    roles: string[]; // Role slugs
    warehouses: number[]; // Warehouse IDs
    is_active: number;
}

export interface UpdateUserRequest extends Partial<StoreUserRequest> {
    id: number;
    password?: string;
    password_confirmation?: string;
}

export type PaginatedUsers = PaginatedResponse<User>;
