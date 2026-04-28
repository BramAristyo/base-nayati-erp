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
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useShipmentDatatable } from '@/composables/sales/useShipmentDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { Shipment } from '@/types/sales/shipment.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import { formatDate } from '@/utils/date';

const props = defineProps<{
    data: PaginatedResponse<Shipment>;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

const authStore = useAuthStore();
const { search, startDate, endDate, onPage, onSort, resetFilters, onExport } = useShipmentDatatable(props);

const onRowClick = (event: any) => {
    if (authStore.hasPermission('sales.shipment.view')) {
        router.get(route('sales.shipments.show', { id: event.data.id }));
    }
};
</script>

<template>
    <Head title="Shipment" />

    <AppLayout>
        <div class="flex flex-col gap-2">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader title="Shipment" description="Manage and track shipments." />

                <div class="flex items-center gap-2">
                    <Button v-if="authStore.hasPermission('sales.shipment.export')" icon="pi pi-file-excel"
                        severity="success" rounded size="small"
                        class="bg-success-green! border-none! text-success-green-foreground! rounded-md! shadow-sm!"
                        v-tooltip.bottom="'Export to Excel'" @click="onExport" />

                    <Button v-if="authStore.hasPermission('sales.shipment.create')" icon="pi pi-plus"
                        label="Create" size="small"
                        class="bg-primary! border-none! text-primary-foreground! px-4! font-bold! uppercase! tracking-widest! rounded-md! shadow-md!"
                        @click="router.get(route('dashboard'))" />
                </div>
            </div>

            <Divider class="my-2!" />

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
                            No shipments found matching your search.
                        </div>
                    </template>

                    <Column field="shipment_number" header="SHIPMENT NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{ slotProps.data.shipment_number }}</span>
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
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.customer_name }}</span>
                        </template>
                    </Column>

                    <Column field="delivery_order_number" header="DO NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.delivery_order_number || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="sales_order_number" header="SO NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.sales_order_number || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="warehouse_name" header="WAREHOUSE NAME" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.warehouse_name || '-' }}</span>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
