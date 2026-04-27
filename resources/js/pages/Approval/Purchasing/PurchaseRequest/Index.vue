<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

import AppLayout from '@/Layouts/AppLayout.vue';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import ApprovalMenu from '@/components/Approvals/ApprovalMenu.vue';
import ApproveBadge from '@/components/common/badges/ApproveBadge.vue';
import { usePurchaseRequestApproval } from '@/composables/approval/usePurchaseRequestApproval';
import { formatDate } from '@/utils/date';

import Badge from 'primevue/badge';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import DatePicker from 'primevue/datepicker';
import Divider from 'primevue/divider';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';

import ProgressSpinner from 'primevue/progressspinner';
import SelectButton from 'primevue/selectbutton';
import Splitter from 'primevue/splitter';
import SplitterPanel from 'primevue/splitterpanel';
import { useToast } from 'primevue/usetoast';

import type { PurchaseRequest } from '@/types/purchasing/purchase-request.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<PurchaseRequest>;
    filters: PaginateFilter & {
        approval_status?: 'pending' | 'processed';
        start_date?: string;
        end_date?: string;
    };
}>();

const toast = useToast();

const {
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
} = usePurchaseRequestApproval(props);

const statusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'Processed', value: 'processed' },
];

const handleRowClick = async (event: any) => {
    const target = event.originalEvent?.target as HTMLElement;
    if (target?.closest('.p-checkbox') || target?.closest('.p-datatable-selection-column')) return;

    try {
        await loadDetail(event.data as PurchaseRequest);
    } catch {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load line items.', life: 3000 });
    }
};

const formatCurrency = (val: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

const detailTotal = computed(() =>
    detailItems.value.reduce((sum, item) => sum + item.quantity * item.price, 0)
);
</script>

<template>

    <Head title="Purchase Request Approval" />

    <AppLayout>
        <div class="flex gap-6 h-[calc(100vh-8rem)]">

            <aside class="w-56 shrink-0 flex flex-col">
                <ApprovalMenu active="purchase-request" />
            </aside>

            <div class="flex-1 flex flex-col gap-2 min-w-0">

                <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between shrink-0">
                    <AppPageHeader title="Purchase Request"
                        description="Authorize pending purchase requests and track processing history." />

                    <div class="flex items-center gap-3 h-10">
                        <Transition enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 scale-95" enter-to-class="opacity-100 scale-100"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
                            <div v-if="selectedItems.length > 0"
                                class="flex items-center gap-3 bg-card px-4 py-2 rounded-lg border border-border shadow-sm">
                                <span
                                    class="text-[10px] font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                                    <Badge :value="selectedItems.length" severity="info" />
                                    Selected
                                </span>
                                <Button v-if="currentStatus === 'pending'" label="Approve All" icon="pi pi-check"
                                    size="small" severity="success"
                                    class="!font-bold !uppercase !text-[10px] !tracking-wider" />
                                <Button v-else label="Revoke" icon="pi pi-undo" size="small" severity="danger"
                                    class="!font-bold !uppercase !text-[10px] !tracking-wider" />
                            </div>
                        </Transition>
                    </div>
                </div>

                <Divider class="my-1!" />

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 shrink-0">
                    <div class="flex items-center gap-3 flex-1">
                        <SelectButton v-model="currentStatus" :options="statusOptions" optionLabel="label"
                            optionValue="value" :allowEmpty="false" class="approval-status-toggle" />

                        <IconField class="w-full!">
                            <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                            <InputText v-model="search" placeholder="Quick Search..." size="small"
                                class="w-full! bg-background border-border! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! shadow-sm transition-all placeholder:text-muted-foreground!" />
                        </IconField>
                    </div>

                    <div class="flex items-center gap-2 self-end md:self-auto">
                        <DatePicker v-model="startDate" placeholder="Start Date" size="small" showIcon
                            iconDisplay="input" dateFormat="yy-mm-dd" class="w-36!" inputClass="py-2! text-sm!" />
                        <span class="text-border px-1">/</span>
                        <DatePicker v-model="endDate" placeholder="End Date" size="small" showIcon iconDisplay="input"
                            dateFormat="yy-mm-dd" class="w-36!" inputClass="py-2! text-sm!" />
                        <Button icon="pi pi-refresh" size="small" variant="outlined" severity="secondary"
                            class="rounded-md! border-border!" v-tooltip.top="'Reset Filters'" @click="resetFilters" />
                    </div>
                </div>

                <div class="flex-1 min-h-0 overflow-hidden">
                    <Splitter stateKey="pr-approval-splitter" layout="vertical"
                        class="h-full! border-none! bg-transparent!">
                        <SplitterPanel :size="55" :minSize="30"
                            class="flex flex-col overflow-hidden bg-card rounded-xl border border-border shadow-xs">
                            <DataTable :key="routeName" :value="data.data" v-model:selection="selectedItems"
                                dataKey="id" lazy paginator :rows="data.per_page"
                                :rowsPerPageOptions="[10, 25, 50, 100]" :totalRecords="data.total"
                                :first="(data.current_page - 1) * data.per_page" :sortField="filters.sortField"
                                :sortOrder="Number(filters.sortOrder) || -1" @page="onPage" @sort="onSort"
                                @row-click="handleRowClick"
                                :rowClass="(d: any) => activeRow?.id === d.id ? 'bg-primary/5!' : ''" size="small"
                                stripedRows showGridlines responsiveLayout="scroll" class="flex-1 cursor-pointer"
                                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown CurrentPageReport"
                                currentPageReportTemplate="Showing {first} to {last} of {totalRecords}">

                                <Column selectionMode="multiple" headerStyle="width: 3rem" />

                                <Column field="purchase_request_number" header="PR NUM" sortable class="w-48">
                                    <template #body="slotProps">
                                        <span class="text-xs font-bold text-foreground">
                                            {{ slotProps.data.purchase_request_number }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="date" header="DATE" sortable class="w-32">
                                    <template #body="slotProps">
                                        <span class="text-[11px] font-medium text-muted-foreground">
                                            {{ formatDate(slotProps.data.date) }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="delivery_date" header="USAGE DATE" sortable class="w-32">
                                    <template #body="slotProps">
                                        <span class="text-[11px] font-medium text-muted-foreground">
                                            {{ formatDate(slotProps.data.delivery_date) }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="employee_name" header="REQUESTER" sortable>
                                    <template #body="slotProps">
                                        <span class="text-xs font-semibold text-foreground">
                                            {{ slotProps.data.employee_name }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="department_name" header="DEPARTMENT">
                                    <template #body="slotProps">
                                        <span class="text-xs font-medium text-foreground">
                                            {{ slotProps.data.department_name }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="branch_code" header="BRANCH" class="w-24">
                                    <template #body="slotProps">
                                        <span class="text-xs font-semibold text-foreground">
                                            {{ slotProps.data.branch_code }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="budget_type" header="BUDGET" class="w-28">
                                    <template #body="slotProps">
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-tight text-muted-foreground">
                                            {{ slotProps.data.budget_type }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="inventory_type" header="INVENTORY" class="w-32">
                                    <template #body="slotProps">
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-tight text-muted-foreground">
                                            {{ slotProps.data.inventory_type }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="approved_by" header="APPROVED BY" sortable>
                                    <template #body="slotProps">
                                        <span class="text-xs font-medium text-muted-foreground">
                                            {{ slotProps.data.approved_by || '-' }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="approval_date" header="APPROVAL DATE" sortable class="w-32">
                                    <template #body="slotProps">
                                        <span class="text-[11px] font-medium text-muted-foreground">
                                            {{ formatDate(slotProps.data.approval_date) }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="status" header="STATUS" class="w-28 text-center">
                                    <template #body="slotProps">
                                        <ApproveBadge :approved="!!slotProps.data.status" />
                                    </template>
                                </Column>

                                <template #empty>
                                    <div class="flex flex-col items-center justify-center py-12 gap-2">
                                        <i class="pi pi-inbox text-3xl text-muted-foreground/40"></i>
                                        <span class="text-[11px] font-medium text-muted-foreground">
                                            No purchase requests found.
                                        </span>
                                    </div>
                                </template>
                            </DataTable>
                        </SplitterPanel>

                        <!-- Detail: Line Items -->
                        <SplitterPanel :size="45" :minSize="20"
                            class="flex flex-col overflow-hidden bg-card rounded-xl border border-border shadow-xs mt-2">

                            <div
                                class="flex items-center justify-between px-4 py-2.5 border-b border-border bg-muted/30 shrink-0">
                                <div class="flex items-center gap-3">
                                    <i class="pi pi-list text-xs text-muted-foreground"></i>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-foreground">
                                        Line Items
                                    </span>
                                    <Badge v-if="detailItems.length" :value="detailItems.length" severity="secondary" />
                                </div>
                                <div v-if="activeRow" class="flex items-center gap-3">
                                    <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
                                        {{ activeRow.purchase_request_number }}
                                    </span>
                                    <span v-if="detailItems.length"
                                        class="text-[10px] font-bold text-primary tracking-wider">
                                        Total: {{ formatCurrency(detailTotal) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex-1 overflow-auto relative">
                                <DataTable :value="detailItems" size="small" stripedRows showGridlines>

                                    <Column field="product_code" header="CODE" class="w-32">
                                        <template #body="slotProps">
                                            <span class="text-[11px] ">
                                                {{ slotProps.data.product_code }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="product_name" header="DESCRIPTION">
                                        <template #body="slotProps">
                                            <div class="flex flex-col leading-tight">
                                                <span class="text-xs font-bold text-foreground">
                                                    {{ slotProps.data.product_name }}
                                                </span>
                                                <span v-if="slotProps.data.variant_name"
                                                    class="text-[10px] text-muted-foreground">
                                                    {{ slotProps.data.variant_name }}
                                                </span>
                                            </div>
                                        </template>
                                    </Column>

                                    <Column field="quantity" header="QTY" class="w-28 text-right">
                                        <template #body="slotProps">
                                            <span class="text-xs font-bold text-foreground">
                                                {{ slotProps.data.quantity }}
                                            </span>
                                            <span class="text-[10px] text-muted-foreground ml-1">
                                                {{ slotProps.data.unit }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="price" header="EST. PRICE" class="w-36 text-right">
                                        <template #body="slotProps">
                                            <span class="text-xs font-medium text-foreground">
                                                {{ formatCurrency(slotProps.data.price) }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="supplier_name" header="SUPPLIER">
                                        <template #body="slotProps">
                                            <span class="text-xs font-medium text-muted-foreground">
                                                {{ slotProps.data.supplier_name || '-' }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="brand" header="BRAND" class="w-28">
                                        <template #body="slotProps">
                                            <span class="text-xs font-medium text-muted-foreground">
                                                {{ slotProps.data.brand || '-' }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="remarks" header="REMARKS">
                                        <template #body="slotProps">
                                            <span class="text-[11px] text-muted-foreground italic">
                                                {{ slotProps.data.remarks || '-' }}
                                            </span>
                                        </template>
                                    </Column>

                                    <template #empty>
                                        <div class="flex flex-col items-center justify-center py-8 gap-2">
                                            <i class="pi pi-arrow-up text-xl text-muted-foreground/30"></i>
                                            <span class="text-[11px] font-medium text-muted-foreground">
                                                Select a purchase request above to view its line items.
                                            </span>
                                        </div>
                                    </template>
                                </DataTable>

                                <div v-if="isLoadingDetails"
                                    class="absolute inset-0 bg-background/60 backdrop-blur-[1px] flex items-center justify-center z-10">
                                    <ProgressSpinner style="width: 28px; height: 28px" strokeWidth="6" />
                                </div>
                            </div>
                        </SplitterPanel>
                    </Splitter>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.approval-status-toggle .p-togglebutton) {
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
    padding: 0.35rem 1rem !important;
    border-color: var(--border) !important;
}
</style>
