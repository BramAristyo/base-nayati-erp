import { ref, watch } from 'vue';
import { useDataTable } from '@/composables/common/useDataTable';
import { formatToDateString } from '@/utils/date';
import type { PaginatedResponse } from '@/types/common/paginate.types';
import { route } from 'ziggy-js';

export function usePurchaseRequestDatatable(props: { data: PaginatedResponse<any>; filters: any }) {
    const startDate = ref(props.filters?.start_date ? new Date(props.filters.start_date) : null);
    const endDate = ref(props.filters?.end_date ? new Date(props.filters.end_date) : null);

    const { search, onPage, onSort, updateRoute } = useDataTable({
        routeName: 'purchasing.purchase-requests.index',
        filters: props.filters,
        pagination: props.data,
        extraParams: () => ({
            start_date: formatToDateString(startDate.value),
            end_date: formatToDateString(endDate.value),
        })
    });

    watch([startDate, endDate], () => {
        updateRoute({ page: 1 });
    });

    const resetFilters = () => {
        search.value = '';
        startDate.value = null;
        endDate.value = null;
        updateRoute({ page: 1 });
    };

    const onExport = () => {
        const params = {
            search: search.value || undefined,
            sortField: props.filters.sortField || undefined,
            sortOrder: props.filters.sortOrder || undefined,
            start_date: formatToDateString(startDate.value) || undefined,
            end_date: formatToDateString(endDate.value) || undefined,
        };

        const url = route('purchasing.purchase-requests.export', params);
        window.location.assign(url);
    };

    return {
        search,
        startDate,
        endDate,
        onPage,
        onSort,
        resetFilters,
        onExport
    };
}
