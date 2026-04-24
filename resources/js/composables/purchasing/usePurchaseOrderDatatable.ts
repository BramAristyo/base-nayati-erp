import { ref, watch } from 'vue';
import { useDataTable } from '@/composables/common/useDataTable';
import { formatToDateString } from '@/utils/date';
import type { PaginatedResponse } from '@/types/common/paginate.types';

export function usePurchaseOrderDatatable(props: { data: PaginatedResponse<any>; filters: any }) {
    const today = new Date();
    const thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);

    const startDate = ref(props.filters?.start_date ? new Date(props.filters.start_date) : thirtyDaysAgo);
    const endDate = ref(props.filters?.end_date ? new Date(props.filters.end_date) : today);

    const { search, onPage, onSort, updateRoute } = useDataTable({
        routeName: 'purchasing.purchase-orders.index',
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
        startDate.value = thirtyDaysAgo;
        endDate.value = today;
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

        window.location.assign(route('purchasing.purchase-orders.export', params));
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
