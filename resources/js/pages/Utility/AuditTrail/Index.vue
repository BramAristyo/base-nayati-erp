<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Column from 'primevue/column';
import DatePicker from 'primevue/datepicker';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import type { DataTableRowClickEvent } from 'primevue/datatable';
import { ref, watch, onMounted } from 'vue';
import { route } from 'ziggy-js';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useDataTable } from '@/composables/common/useDataTable';
import { useMonitoring } from '@/composables/utility/useMonitoring';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { PaginateFilter } from '@/types/common/paginate.types';
import type { PaginatedAuditTrails } from '@/types/utility/audit-trail.types';
import { formatDate, formatDateTime, formatToDateString } from '@/utils/date';

const props = defineProps<{
    auditTrails: PaginatedAuditTrails;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

const { stats, fetchStats, isLoading: isStatsLoading } = useMonitoring();
const startDate = ref(props.filters?.start_date ? new Date(props.filters.start_date) : null);
const endDate = ref(props.filters?.end_date ? new Date(props.filters.end_date) : null);

const { search, onPage, onSort, updateRoute } = useDataTable({
    routeName: 'utility.audit-trails.paginate',
    filters: props.filters,
    pagination: props.auditTrails,
    extraParams: () => ({
        start_date: formatToDateString(startDate.value),
        end_date: formatToDateString(endDate.value),
    }),
    onSuccess: (params) => {
        fetchStats(params);
    },
});

const onRowClick = (event: DataTableRowClickEvent) => {
    if (event.data.detail_route) {
        router.get(route(event.data.detail_route, { id: event.data.subject_id }));
    }
};

watch([startDate, endDate], () => {
    updateRoute({ page: 1 });
});

onMounted(() => {
    fetchStats(props.filters);
});

const getActionSeverity = (action: string) => {
    const act = action.toLowerCase();

    if (act.includes('create')) {
return 'success';
}

    if (act.includes('update')) {
return 'info';
}

    if (act.includes('delete')) {
return 'danger';
}

    if (act.includes('login')) {
return 'success';
}

    return 'secondary';
};
</script>

<template>
    <Head title="Audit Trail" />

    <AppLayout>
        <div class="flex flex-col gap-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Active Users Card -->
                <div
                    class="group flex flex-col gap-3 rounded-xl border border-blue-100 bg-blue-50/40 p-5 shadow-xs transition-all duration-300 hover:border-blue-200"
                >
                    <div class="flex items-center justify-between">
                        <div class="bg-blue-500! flex items-center justify-center rounded-lg p-2 shadow-sm">
                            <i class="pi pi-users text-xs text-primary-foreground"></i>
                        </div>
                        <span class="text-[9px] font-bold leading-none uppercase tracking-widest text-blue-400"
                            >Active Users</span
                        >
                    </div>
                    <div class="flex flex-col">
                        <span v-if="!isStatsLoading" class="text-3xl font-bold tracking-tighter text-foreground">{{
                            stats?.active_user_count ?? 0
                        }}</span>
                        <div v-else class="h-9 w-16 animate-pulse rounded-md bg-blue-100/50"></div>
                        <span class="mt-1 text-[10px] font-medium italic tracking-tight text-blue-600/60"
                            >Currently active in system</span
                        >
                    </div>
                </div>

                <!-- Total Activities Card -->
                <div
                    class="group flex flex-col gap-3 rounded-xl border border-emerald-100 bg-emerald-50/40 p-5 shadow-xs transition-all duration-300 hover:border-emerald-200"
                >
                    <div class="flex items-center justify-between">
                        <div class="bg-emerald-500! flex items-center justify-center rounded-lg p-2 shadow-sm">
                            <i class="pi pi-history text-xs text-primary-foreground"></i>
                        </div>
                        <span class="text-[9px] font-bold leading-none uppercase tracking-widest text-emerald-400"
                            >Total Activities</span
                        >
                    </div>
                    <div class="flex flex-col">
                        <span v-if="!isStatsLoading" class="text-3xl font-bold tracking-tighter text-foreground">{{
                            stats?.activity_count ?? 0
                        }}</span>
                        <div v-else class="h-9 w-24 animate-pulse rounded-md bg-emerald-100/50"></div>
                        <span class="mt-1 text-[10px] font-medium italic tracking-tight text-emerald-600/60"
                            >Matching current filters</span
                        >
                    </div>
                </div>

                <!-- Active Sessions Card -->
                <div
                    class="group flex flex-col gap-3 rounded-xl border border-violet-100 bg-violet-50/40 p-5 shadow-xs transition-all duration-300 hover:border-violet-200"
                >
                    <div class="flex items-center justify-between">
                        <div class="bg-violet-500! flex items-center justify-center rounded-lg p-2 shadow-sm">
                            <i class="pi pi-desktop text-xs text-primary-foreground"></i>
                        </div>
                        <span class="text-[9px] font-bold leading-none uppercase tracking-widest text-violet-400"
                            >Active Sessions</span
                        >
                    </div>
                    <div class="flex flex-col">
                        <span v-if="!isStatsLoading" class="text-3xl font-bold tracking-tighter text-foreground">{{
                            stats?.active_sessions_count ?? 0
                        }}</span>
                        <div v-else class="h-9 w-12 animate-pulse rounded-md bg-violet-100/50"></div>
                        <span class="mt-1 text-[10px] font-medium italic tracking-tight text-violet-600/60"
                            >Sessions in time range</span
                        >
                    </div>
                </div>
            </div>

            <!-- Header & Filters -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between">
                <div class="flex flex-col gap-0.5">
                    <h1 class="text-xl font-bold uppercase tracking-tight text-foreground">Audit Trail</h1>
                    <p class="text-xs font-medium italic text-muted-foreground">
                        Track system activities, user actions, and historical modifications.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <IconField>
                        <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Quick Search..." size="small" class="w-64!" />
                    </IconField>

                    <div class="flex items-center gap-2">
                        <DatePicker
                            v-model="startDate"
                            placeholder="Start Date"
                            size="small"
                            dateFormat="yy-mm-dd"
                            showIcon
                            iconDisplay="input"
                            class="w-36!"
                            inputClass="py-2! text-sm!"
                        />
                        <span class="text-border">/</span>
                        <DatePicker
                            v-model="endDate"
                            placeholder="End Date"
                            size="small"
                            dateFormat="yy-mm-dd"
                            showIcon
                            iconDisplay="input"
                            class="w-36!"
                            inputClass="py-2! text-sm!"
                        />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden">
                <StandardDataTable
                    :data="auditTrails"
                    :filters="filters"
                    class="cursor-pointer"
                    @page="onPage"
                    @sort="onSort"
                    @row-click="onRowClick"
                >
                    <template #empty>
                        <div class="p-8 text-center text-sm font-medium text-muted-foreground">
                            No activity logs found for the current filters.
                        </div>
                    </template>

                    <Column field="created_at" header="TIMESTAMP" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-[11px] font-bold tracking-tight text-muted-foreground">
                                {{ formatDateTime(slotProps.data.created_at) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="action" header="ACTION" sortable class="w-32">
                        <template #body="slotProps">
                            <span
                                class="rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest border"
                                :class="{
                                    'bg-green-50 text-green-700 border-green-100':
                                        getActionSeverity(slotProps.data.action) === 'success',
                                    'bg-blue-50 text-blue-700 border-blue-100':
                                        getActionSeverity(slotProps.data.action) === 'info',
                                    'bg-destructive/10 text-destructive border-destructive/20':
                                        getActionSeverity(slotProps.data.action) === 'danger',
                                    'bg-muted text-muted-foreground border-border':
                                        getActionSeverity(slotProps.data.action) === 'secondary',
                                }"
                            >
                                {{ slotProps.data.action }}
                            </span>
                        </template>
                    </Column>

                    <Column field="subject_type" header="MODULE" sortable class="w-40">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold uppercase tracking-tight text-foreground">{{
                                slotProps.data.subject_type
                            }}</span>
                        </template>
                    </Column>

                    <Column field="description" header="DESCRIPTION" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.description }}</span>
                        </template>
                    </Column>

                    <Column field="causer.name" header="PERFORMED BY" class="w-56">
                        <template #body="slotProps">
                            <div v-if="slotProps.data.causer" class="flex flex-col gap-0.5">
                                <span class="text-xs font-bold leading-none text-foreground">{{
                                    slotProps.data.causer.name
                                }}</span>
                                <span class="text-[10px] font-medium text-muted-foreground">{{
                                    slotProps.data.causer.email
                                }}</span>
                            </div>
                            <span v-else class="text-[10px] font-bold italic text-muted-foreground">SYSTEM / GUEST</span>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
