import type { PaginatedResponse } from '../common/paginate.types';

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
}

export type PaginatedUsers = PaginatedResponse<User>;