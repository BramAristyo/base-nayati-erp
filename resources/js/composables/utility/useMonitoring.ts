import { ref } from 'vue';
import http from '@/lib/http';
import { route } from 'ziggy-js';
import type { MonitoringStats, MonitoringFilter } from '@/types/utility/monitoring.types';

export function useMonitoring() {
    const stats = ref<MonitoringStats | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    const fetchStats = async (filters: MonitoringFilter = {}) => {
        isLoading.value = true;
        error.value = null;
        try {
            const response: any = await http.get(route('api.utility.monitoring.stats'), {
                params: filters
            });
            
            if (response.status) {
                stats.value = response.data;
            } else {
                error.value = response.message || 'Failed to fetch monitoring stats';
            }
        } catch (err: any) {
            error.value = err.message || 'An error occurred while fetching monitoring stats';
        } finally {
            isLoading.value = false;
        }
    };

    return {
        stats,
        isLoading,
        error,
        fetchStats
    };
}
