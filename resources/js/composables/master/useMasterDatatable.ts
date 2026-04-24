import { useDataTable } from '@/composables/common/useDataTable';
import type { PaginatedResponse } from '@/types/common/paginate.types';

export function useMasterDatatable(options: { 
    routeName: string; 
    props: { data: PaginatedResponse<any>; filters: any } 
}) {
    const { search, onPage, onSort, updateRoute } = useDataTable({
        routeName: options.routeName,
        filters: options.props.filters,
        pagination: options.props.data,
    });

    const resetFilters = () => {
        search.value = '';
        updateRoute({ page: 1 });
    };

    return {
        search,
        onPage,
        onSort,
        resetFilters
    };
}
