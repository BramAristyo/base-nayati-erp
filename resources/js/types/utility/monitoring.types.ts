export interface MonitoringStats {
    active_user_count: number;
    activity_count: number;
    active_sessions_count: number;
}

export interface MonitoringFilter {
    start_date?: string;
    end_date?: string;
    search?: string;
    per_page?: number;
    page?: number;
    sortField?: string;
    sortOrder?: number;
}
