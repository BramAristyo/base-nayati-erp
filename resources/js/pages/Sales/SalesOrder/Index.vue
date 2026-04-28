<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DatePicker from 'primevue/datepicker';
import Divider from 'primevue/divider';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { route } from 'ziggy-js';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import ApproveBadge from '@/components/common/badges/ApproveBadge.vue';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useSalesOrderDatatable } from '@/composables/sales/useSalesOrderDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { SalesOrder } from '@/types/sales/sales-order.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import { formatDate } from '@/utils/date';

import { formatCurrency } from '@/utils/money';

const props = defineProps<{
    data: PaginatedResponse<SalesOrder>;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

const authStore = useAuthStore();
const { search, startDate, endDate, onPage, onSort, resetFilters, onExport } = useSalesOrderDatatable(props);

const onRowClick = (event: any) => {
    if (authStore.hasPermission('sales.order.view')) {
        router.get(route('sales.orders.show', { id: event.data.id }));
    }
};
</script>

<template>

    <Head title="Sales Order" />

    <AppLayout>
        <div class="flex flex-col gap-2">
            <!-- Level 1 Header: Title and Primary Actions -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader title="Sales Order" description="View and track incoming sales orders from customers." />

                <div class="flex items-center gap-2">
                    <Button v-if="authStore.hasPermission('sales.order.export')" icon="pi pi-file-excel"
                        severity="success" rounded size="small"
                        class="bg-success-green! border-none! text-success-green-foreground! rounded-md! shadow-sm!"
                        v-tooltip.bottom="'Export to Excel'" @click="onExport" />

                    <Button v-if="authStore.hasPermission('sales.order.create')" icon="pi pi-plus"
                        label="Create" size="small"
                        class="bg-primary! border-none! text-primary-foreground! px-4! font-bold! uppercase! tracking-widest! rounded-md! shadow-md!"
                        @click="router.get(route('dashboard'))" />
                </div>
            </div>

            <Divider class="my-2!" />

            <!-- Level 2 Header: Filters Strip -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-1 mb-4">
                <div class="flex-1">
                    <IconField>
                        <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Quick Search..." size="small"
                            class="w-full! bg-background border-border! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! shadow-sm transition-all placeholder:text-muted-foreground!" />
                    </IconField>
                </div>

                <div class="flex items-center gap-2 self-end md:self-auto">
                    <DatePicker v-model="startDate" placeholder="Start Date" size="small" dateFormat="yy-mm-dd" showIcon
                        iconDisplay="input" class="w-36!" inputClass="py-2! text-sm!" />
                    <span class="text-border px-1">/</span>
                    <DatePicker v-model="endDate" placeholder="End Date" size="small" dateFormat="yy-mm-dd" showIcon
                        iconDisplay="input" class="w-36!" inputClass="py-2! text-sm!" />

                    <Button icon="pi pi-refresh" size="small" variant="outlined" severity="secondary"
                        class="rounded-md! border-border!" v-tooltip.top="'Reset Filters'" @click="resetFilters" />
                </div>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" class="cursor-pointer" @page="onPage" @sort="onSort"
                    @row-click="onRowClick">
                    <template #empty>
                        <div class="p-8 text-center text-sm font-medium text-muted-foreground">
                            No sales orders found matching your search.
                        </div>
                    </template>

                    <Column field="sales_order_number" header="SO NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{ slotProps.data.sales_order_number
                                }}</span>
                        </template>
                    </Column>

                    <Column field="date" header="DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="customer_name" header="CUSTOMER NAME" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.customer_name
                                }}</span>
                        </template>
                    </Column>

                    <Column field="project_name" header="PROJECT NAME" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.project_name ||
                                '-' }}</span>
                        </template>
                    </Column>

                    <Column field="status" header="STATUS" class="w-24 text-center">
                        <template #body="slotProps">
                            <ApproveBadge :approved="!!slotProps.data.status" />
                        </template>
                    </Column>

                    <Column field="branch_code" header="BRANCH" class="w-24">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.branch_code }}</span>
                        </template>
                    </Column>

                    <Column field="net_price" header="TOTAL" sortable class="text-right">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{
                                formatCurrency(slotProps.data.net_price) }}</span>
                        </template>
                    </Column>

                    <Column field="delivery_date" header="DELIVERY DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.delivery_date) }}
                            </span>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
