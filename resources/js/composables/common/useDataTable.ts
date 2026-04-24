import { router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import { ref, watch } from 'vue';
import type { PaginateFilter, PaginatedResponse } from '@/types/common/paginate.types';

export interface UseDataTableOptions<TFilters extends PaginateFilter> {
    routeName: string;
    filters: TFilters;
    pagination: PaginatedResponse<any>;
    onSuccess?: (params: any) => void;
    extraParams?: () => Record<string, any>;
}

export function useDataTable<TFilters extends PaginateFilter>(options: UseDataTableOptions<TFilters>) {
    const search = ref(options.filters.search || '');

    const updateRoute = (params: any = {}) => {
        const rawParams = {
            search: search.value,
            sortField: options.filters.sortField,
            sortOrder: options.filters.sortOrder,
            per_page: options.pagination.per_page,
            page: options.pagination.current_page,
            ...(options.extraParams ? options.extraParams() : {}),
            ...params
        };

        // Filter out empty strings, nulls, and undefined values
        const finalParams = Object.entries(rawParams).reduce((acc: any, [key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                acc[key] = value;
            }
            return acc;
        }, {});

        router.get(route(options.routeName), finalParams, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onSuccess: () => {
                if (options.onSuccess) {
                    options.onSuccess(finalParams);
                }
            }
        });
    };

    const onPage = (event: any) => {
        updateRoute({
            page: event.page + 1,
            per_page: event.rows
        });
    };

    const onSort = (event: any) => {
        performSort({
            sortField: event.sortField,
            sortOrder: event.sortOrder,
        });
    };

    const performSearch = useDebounceFn(() => {
        updateRoute({ page: 1 });
    }, 500);

    const performSort = useDebounceFn((params: any) => {
        updateRoute({
            ...params,
            page: 1
        });
    }, 500);

    watch(search, () => {
        performSearch();
    });

    return {
        search,
        onPage,
        onSort,
        updateRoute,
    };
}
