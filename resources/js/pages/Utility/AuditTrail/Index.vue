<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { useMonitoring } from '@/composables/utility/useMonitoring';
import type { PaginatedAuditTrails } from '@/types/utility/audit-trail.types';
import type { PaginateFilter } from '@/types/common/paginate.types';
import { Head, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import Column from 'primevue/column';
import DataTable, { type DataTablePageEvent, type DataTableSortEvent } from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import DatePicker from 'primevue/datepicker';
import { ref, watch, onMounted } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps<{
    auditTrails: PaginatedAuditTrails;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

const { stats, fetchStats, isLoading: isStatsLoading } = useMonitoring();
const search = ref(props.filters?.search || '');
const startDate = ref(props.filters?.start_date ? new Date(props.filters.start_date) : null);
const endDate = ref(props.filters?.end_date ? new Date(props.filters.end_date) : null);

const formatToDateString = (date: Date | null) => {
    if (!date) return null;
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

const updateRoute = (params: any) => {
    const finalParams = {
        search: search.value,
        sortField: props.filters?.sortField,
        sortOrder: props.filters?.sortOrder,
        per_page: props.auditTrails.per_page,
        page: props.auditTrails.current_page,
        start_date: formatToDateString(startDate.value),
        end_date: formatToDateString(endDate.value),
        ...params
    };

    router.get(route('utility.audit-trails.paginate'), finalParams, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
            fetchStats(finalParams);
        }
    });
};

const onPage = (event: DataTablePageEvent) => {
    updateRoute({
        page: event.page + 1,
        per_page: event.rows
    });
};

const onSort = (event: DataTableSortEvent) => {
    updateRoute({
        sortField: event.sortField,
        sortOrder: event.sortOrder,
        page: 1
    });
};

const performSearch = useDebounceFn(() => {
    updateRoute({ page: 1 });
}, 500);

const performDateFilter = useDebounceFn(() => {
    updateRoute({ page: 1 });
}, 400);

watch(search, () => performSearch());
watch([startDate, endDate], () => performDateFilter());

onMounted(() => {
    fetchStats(props.filters);
});

const formatDate = (dateString?: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const getActionSeverity = (action: string) => {
    const act = action.toLowerCase();
    if (act.includes('create')) return 'success';
    if (act.includes('update')) return 'info';
    if (act.includes('delete')) return 'danger';
    if (act.includes('login')) return 'success';
    return 'secondary';
};
</script>

<template>
    <Head title="Audit Trail" />

    <AppLayout>
        <div class="flex flex-col gap-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Active Users</span>
                        <i class="pi pi-users text-gray-300 group-hover:text-black transition-colors duration-300"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span v-if="!isStatsLoading" class="text-2xl font-bold text-black">{{ stats?.active_user_count ?? 0 }}</span>
                        <div v-else class="h-8 w-16 bg-gray-100 animate-pulse rounded"></div>
                        <span class="text-[10px] text-gray-500 font-medium tracking-tight">System-wide</span>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 bg-black w-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Activities</span>
                        <i class="pi pi-history text-gray-300 group-hover:text-black transition-colors duration-300"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span v-if="!isStatsLoading" class="text-2xl font-bold text-black">{{ stats?.activity_count ?? 0 }}</span>
                        <div v-else class="h-8 w-24 bg-gray-100 animate-pulse rounded"></div>
                        <span class="text-[10px] text-gray-500 font-medium tracking-tight">Current Filter</span>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 bg-black w-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col gap-2 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Active Sessions</span>
                        <i class="pi pi-desktop text-gray-300 group-hover:text-black transition-colors duration-300"></i>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span v-if="!isStatsLoading" class="text-2xl font-bold text-black">{{ stats?.active_sessions_count ?? 0 }}</span>
                        <div v-else class="h-8 w-12 bg-gray-100 animate-pulse rounded"></div>
                        <span class="text-[10px] text-gray-500 font-medium tracking-tight">Within Range</span>
                    </div>
                    <div class="absolute bottom-0 left-0 h-1 bg-black w-full opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
            </div>

            <!-- Header -->
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold text-black uppercase tracking-tight">Audit Trail</h1>
                <p class="text-xs text-gray-500 font-medium italic">Track system activities, user actions, and historical modifications.</p>
            </div>

            <!-- Filters -->
            <div class="bg-gray-50/50 border border-gray-100 p-4 rounded-xl flex flex-wrap items-center gap-4">
                <IconField class="flex-1 min-w-[300px]">
                    <InputIcon class="pi pi-search text-gray-400!" style="font-size: 14px" />
                    <InputText v-model="search" placeholder="Search by description, action, or module..." size="small"
                        class="w-full! bg-white border-gray-200! text-gray-900! rounded-md! focus:ring-1! focus:ring-gray-300! transition-all shadow-sm placeholder:text-gray-400!" />
                </IconField>

                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">Date Range</span>
                    <div class="flex items-center gap-2">
                        <DatePicker v-model="startDate" placeholder="Start Date" size="small" dateFormat="yy-mm-dd" showIcon iconDisplay="input" 
                            class="w-40! shadow-sm" inputClass="py-2! text-sm!" />
                        <span class="text-gray-300">/</span>
                        <DatePicker v-model="endDate" placeholder="End Date" size="small" dateFormat="yy-mm-dd" showIcon iconDisplay="input" 
                            class="w-40! shadow-sm" inputClass="py-2! text-sm!" />
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden">
                <DataTable :value="auditTrails.data" lazy paginator :rows="auditTrails.per_page"
                    :rowsPerPageOptions="[10, 25, 50, 100]" :totalRecords="auditTrails.total"
                    :first="(auditTrails.current_page - 1) * auditTrails.per_page" @page="onPage" @sort="onSort" removableSort
                    :sortField="filters?.sortField || 'created_at'" :sortOrder="filters?.sortOrder || -1" size="small"
                    stripedRows showGridlines responsiveLayout="scroll">
                    
                    <template #empty>
                        <div class="p-8 text-center text-gray-500 text-sm font-medium">No activity logs found for the current filters.</div>
                    </template>

                    <Column field="created_at" header="TIMESTAMP" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-[11px] font-bold text-gray-600 tracking-tight">
                                {{ formatDate(slotProps.data.created_at) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="action" header="ACTION" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest border"
                                :class="{
                                    'bg-green-50 text-green-700 border-green-100': getActionSeverity(slotProps.data.action) === 'success',
                                    'bg-blue-50 text-blue-700 border-blue-100': getActionSeverity(slotProps.data.action) === 'info',
                                    'bg-red-50 text-red-700 border-red-100': getActionSeverity(slotProps.data.action) === 'danger',
                                    'bg-gray-50 text-gray-700 border-gray-100': getActionSeverity(slotProps.data.action) === 'secondary',
                                }">
                                {{ slotProps.data.action }}
                            </span>
                        </template>
                    </Column>

                    <Column field="subject_type" header="MODULE" sortable class="w-40">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-gray-900 uppercase tracking-tight">{{ slotProps.data.subject_type }}</span>
                        </template>
                    </Column>

                    <Column field="description" header="DESCRIPTION" sortable>
                        <template #body="slotProps">
                            <span class="text-xs text-gray-700 font-medium">{{ slotProps.data.description }}</span>
                        </template>
                    </Column>

                    <Column field="causer.name" header="PERFORMED BY" class="w-56">
                        <template #body="slotProps">
                            <div v-if="slotProps.data.causer" class="flex flex-col gap-0.5">
                                <span class="text-xs font-bold text-black leading-none">{{ slotProps.data.causer.name }}</span>
                                <span class="text-[10px] text-gray-500 font-medium">{{ slotProps.data.causer.email }}</span>
                            </div>
                            <span v-else class="text-[10px] font-bold text-gray-400 italic">SYSTEM / GUEST</span>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@reference "../../../css/app.css";

:deep(.p-datepicker-input) {
    @apply text-sm! py-2! bg-white border-gray-200! rounded-md! focus:ring-1! focus:ring-gray-300! transition-all;
}
</style>
