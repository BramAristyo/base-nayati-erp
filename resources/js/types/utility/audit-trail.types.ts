import type { User } from './user.types';
import type { PaginatedResponse } from '../common/paginate.types';

export interface AuditTrail {
    id: number;
    causer_id: number | null;
    action: string;
    description: string;
    subject_type: string;
    subject_id: number;
    created_at: string;
    causer?: User;
}

export type PaginatedAuditTrails = PaginatedResponse<AuditTrail>;
