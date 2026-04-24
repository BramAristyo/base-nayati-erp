import { ref, watch } from 'vue';
import { useDataTable } from '@/composables/common/useDataTable';
import { formatToDateString } from '@/utils/date';
import type { PurchaseRequestFilters } from '@/types/purchasing/purchase-request.types';
import type { PaginatedResponse } from '@/types/common/paginate.types';

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

    return {
        search,
        startDate,
        endDate,
        onPage,
        onSort,
        resetFilters
    };
}
