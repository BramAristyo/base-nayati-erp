<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DatePicker from 'primevue/datepicker';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { route } from 'ziggy-js';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import ApproveBadge from '@/components/common/badges/ApproveBadge.vue';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { usePurchaseOrderDatatable } from '@/composables/purchasing/usePurchaseOrderDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { PurchaseOrder } from '@/types/purchasing/purchase-order.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import { formatDate } from '@/utils/date';

const props = defineProps<{
    data: PaginatedResponse<PurchaseOrder>;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

const authStore = useAuthStore();
const { search, startDate, endDate, onPage, onSort, resetFilters, onExport } = usePurchaseOrderDatatable(props);

const onRowClick = (event: any) => {
    if (authStore.hasPermission('purchasing.purchase-order.view')) {
        router.get(route('purchasing.purchase-orders.show', { id: event.data.id }));
    }
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Purchase Order" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between">
                <AppPageHeader
                    title="Purchase Order"
                    description="Manage and track outgoing purchase orders."
                />

                <div class="flex flex-wrap items-center gap-3">
                    <IconField>
                        <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                        <InputText
                            v-model="search"
                            placeholder="Quick Search..."
                            size="small"
                            class="w-64! bg-background border-border! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! shadow-sm transition-all placeholder:text-muted-foreground!"
                        />
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

                    <Button 
                        icon="pi pi-refresh" 
                        size="small" 
                        variant="outlined"
                        severity="secondary"
                        class="rounded-md!"
                        v-tooltip.top="'Reset Filters'"
                        @click="resetFilters"
                    />

                    <Button 
                        v-if="authStore.hasPermission('purchasing.purchase-order.export')" 
                        icon="pi pi-file-excel"
                        label="Export" 
                        size="small" 
                        severity="success" 
                        variant="outlined"
                        class="border-green-600! text-green-600! hover:bg-green-50! rounded-md! shadow-sm!"
                        @click="onExport" 
                    />

                    <Button
                        v-if="authStore.hasPermission('purchasing.purchase-order.create')"
                        icon="pi pi-plus"
                        label="Create"
                        size="small"
                        class="bg-primary! border-none! text-primary-foreground! px-4! font-bold! uppercase! tracking-widest! rounded-md! shadow-md!"
                        @click="router.get(route('dashboard'))" 
                    />
                </div>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable
                    :data="data"
                    :filters="filters"
                    class="cursor-pointer"
                    @page="onPage"
                    @sort="onSort"
                    @row-click="onRowClick"
                >
                    <template #empty>
                        <div class="p-8 text-center text-sm font-medium text-muted-foreground">
                            No purchase orders found matching your search.
                        </div>
                    </template>

                    <Column field="purchase_order_number" header="PO NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{ slotProps.data.purchase_order_number }}</span>
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

                    <Column field="delivery_date" header="DELIVERY DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.delivery_date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="branch_code" header="BRANCH" class="w-24">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.branch_code }}</span>
                        </template>
                    </Column>

                    <Column field="is_general_purchase" header="GP" class="w-16 text-center">
                        <template #body="slotProps">
                            <i v-if="slotProps.data.is_general_purchase" class="pi pi-check text-green-500 text-xs"></i>
                            <i v-else class="pi pi-times text-muted-foreground text-[10px]"></i>
                        </template>
                    </Column>

                    <Column field="is_inclusive_tax" header="INC TAX" class="w-16 text-center">
                        <template #body="slotProps">
                            <i v-if="slotProps.data.is_inclusive_tax" class="pi pi-check text-green-500 text-xs"></i>
                            <i v-else class="pi pi-times text-muted-foreground text-[10px]"></i>
                        </template>
                    </Column>

                    <Column field="tax_percentage" header="TAX %" class="w-20 text-right">
                        <template #body="slotProps">
                            <span class="text-xs font-medium">{{ slotProps.data.tax_percentage }}%</span>
                        </template>
                    </Column>

                    <Column field="grand_total" header="GRAND TOTAL" sortable class="text-right">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{ formatCurrency(slotProps.data.grand_total) }}</span>
                        </template>
                    </Column>

                    <Column field="approved_by" header="APPROVED BY" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.approved_by || '-' }}</span>
                        </template>
                    </Column>

                    <Column field="approval_date" header="APPROVAL DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.approval_date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="category" header="CATEGORY" class="w-32">
                        <template #body="slotProps">
                            <span class="text-[10px] font-bold uppercase tracking-tight text-muted-foreground">{{ slotProps.data.category }}</span>
                        </template>
                    </Column>

                    <Column field="inventory_type" header="INVENTORY TYPE" class="w-32">
                        <template #body="slotProps">
                            <span class="text-[10px] font-bold uppercase tracking-tight text-muted-foreground">{{ slotProps.data.inventory_type }}</span>
                        </template>
                    </Column>

                    <Column field="department_name" header="DEPARTMENT">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-foreground">{{ slotProps.data.department_name }}</span>
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
