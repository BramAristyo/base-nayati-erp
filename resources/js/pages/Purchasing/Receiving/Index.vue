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
import { useReceivingDatatable } from '@/composables/purchasing/useReceivingDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { Receiving } from '@/types/purchasing/receiving.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import { formatDate } from '@/utils/date';

const props = defineProps<{
    data: PaginatedResponse<Receiving>;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

const authStore = useAuthStore();
const { search, startDate, endDate, onPage, onSort, resetFilters, onExport } = useReceivingDatatable(props);

const onRowClick = (event: any) => {
    if (authStore.hasPermission('purchasing.receiving.view')) {
        router.get(route('purchasing.receivings.show', { id: event.data.id }));
    }
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const onListingDummy = () => {
    alert('Listing dummy action clicked');
};
</script>

<template>

    <Head title="Receiving" />

    <AppLayout>
        <div class="flex flex-col gap-2">
            <!-- Level 1 Header: Title and Primary Actions -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader title="Receiving" description="Manage and track incoming goods and supplier invoices." />

                <div class="flex items-center gap-2">
                    <Button icon="pi pi-list" severity="secondary" variant="outlined" rounded size="small"
                        class="border-border! text-foreground! hover:bg-accent! rounded-md!"
                        v-tooltip.bottom="'Listing View'" @click="onListingDummy" />

                    <Button v-if="authStore.hasPermission('purchasing.receiving.export')" icon="pi pi-file-excel"
                        severity="success" rounded size="small"
                        class="bg-success-green! border-none! text-success-green-foreground! rounded-md! shadow-sm!"
                        v-tooltip.bottom="'Export to Excel'" @click="onExport" />

                    <Button v-if="authStore.hasPermission('purchasing.receiving.create')" icon="pi pi-plus"
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
                            No receiving records found matching your search.
                        </div>
                    </template>

                    <Column field="receiving_number" header="RECEIVING NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{ slotProps.data.receiving_number }}</span>
                        </template>
                    </Column>

                    <Column field="date" header="DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="status" header="STATUS" class="w-24 text-center">
                        <template #body="slotProps">
                            <ApproveBadge :approved="!!slotProps.data.status" />
                        </template>
                    </Column>

                    <Column field="purchase_order_number" header="PO NUM" sortable class="w-40">
                        <template #body="slotProps">
                            <span class="text-[11px] font-semibold text-foreground">{{
                                slotProps.data.purchase_order_number || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="purchase_order_date" header="PO DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.purchase_order_date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="supplier_invoice_number" header="SUPPLIER INVOICE" class="w-40">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-foreground">{{ slotProps.data.supplier_invoice_number
                                || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="supplier_invoice_date" header="INVOICE DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.supplier_invoice_date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="branch_code" header="BRANCH" class="w-20">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.branch_code }}</span>
                        </template>
                    </Column>

                    <Column field="supplier_name" header="SUPPLIER" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.supplier_name
                                }}</span>
                        </template>
                    </Column>

                    <Column field="warehouse_name" header="WAREHOUSE" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.warehouse_name
                                }}</span>
                        </template>
                    </Column>

                    <Column field="grand_total" header="TOTAL PRICE (RP)" sortable class="text-right">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{
                                formatCurrency(slotProps.data.grand_total) }}</span>
                        </template>
                    </Column>

                    <Column field="approved_by" header="APPROVED BY" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.approved_by || '-'
                                }}</span>
                        </template>
                    </Column>

                    <Column field="approval_date" header="APPROVAL DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.approval_date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="created_by" header="CREATED BY" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.created_by
                                }}</span>
                        </template>
                    </Column>

                    <Column field="created_at" header="CREATED AT" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.created_at) }}
                            </span>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
