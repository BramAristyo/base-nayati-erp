import { ref, watch, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { useDebounceFn } from '@vueuse/core';
import { formatToDateString } from '@/utils/date';
import http from '@/lib/http';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import type { Receiving } from '@/types/purchasing/receiving.types';
import type { ReceivingItem } from '@/types/purchasing/receiving-item.types';

export interface ApprovalFilters extends PaginateFilter {
    approval_status?: 'pending' | 'processed';
    start_date?: string;
    end_date?: string;
}

export function useReceivingApproval(props: {
    data: PaginatedResponse<Receiving>;
    filters: ApprovalFilters;
}) {
    const currentStatus = ref<'pending' | 'processed'>(props.filters.approval_status || 'pending');
    const search = ref(props.filters.search || '');
    const startDate = ref(props.filters.start_date ? new Date(props.filters.start_date) : null);
    const endDate = ref(props.filters.end_date ? new Date(props.filters.end_date) : null);

    const selectedItems = ref<Receiving[]>([]);
    const activeRow = ref<Receiving | null>(null);
    const detailItems = ref<ReceivingItem[]>([]);
    const isLoadingDetails = ref(false);

    const routeName = computed(() =>
        currentStatus.value === 'pending'
            ? 'approval.purchasing.receivings.pending'
            : 'approval.purchasing.receivings.processed'
    );

    const navigate = (params: Record<string, any> = {}) => {
        const rawParams: Record<string, any> = {
            search: search.value,
            start_date: formatToDateString(startDate.value),
            end_date: formatToDateString(endDate.value),
            sortField: props.filters.sortField,
            sortOrder: props.filters.sortOrder,
            per_page: props.data.per_page,
            page: props.data.current_page,
            ...params,
        };

        const finalParams = Object.entries(rawParams).reduce((acc: Record<string, any>, [key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                acc[key] = value;
            }
            return acc;
        }, {});

        router.get(route(routeName.value), finalParams, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const debouncedSearch = useDebounceFn(() => navigate({ page: 1 }), 500);

    watch(search, () => debouncedSearch());
    watch([startDate, endDate], () => navigate({ page: 1 }));

    watch(currentStatus, () => {
        selectedItems.value = [];
        activeRow.value = null;
        detailItems.value = [];

        router.visit(route(routeName.value), {
            method: 'get',
            preserveScroll: true,
            preserveState: false,
        });
    });

    const onPage = (event: any) => {
        navigate({ page: (event.page ?? 0) + 1, per_page: event.rows });
    };

    const onSort = (event: any) => {
        navigate({ sortField: event.sortField, sortOrder: event.sortOrder, page: 1 });
    };

    const resetFilters = () => {
        search.value = '';
        startDate.value = null;
        endDate.value = null;
        navigate({ page: 1 });
    };

    const loadDetail = async (row: Receiving) => {
        if (activeRow.value?.id === row.id) return;

        activeRow.value = row;
        isLoadingDetails.value = true;
        detailItems.value = [];

        try {
            const response: any = await http.get(
                route('api.purchasing.receivings.show', { id: row.id })
            );
            if (response.status) {
                detailItems.value = response.data.items || [];
            }
        } catch {
            detailItems.value = [];
        } finally {
            isLoadingDetails.value = false;
        }
    };

    return {
        currentStatus,
        search,
        startDate,
        endDate,
        selectedItems,
        activeRow,
        detailItems,
        isLoadingDetails,
        routeName,
        onPage,
        onSort,
        resetFilters,
        loadDetail,
    };
}
