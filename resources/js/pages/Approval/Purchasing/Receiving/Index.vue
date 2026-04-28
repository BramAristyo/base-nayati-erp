<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';

import AppLayout from '@/Layouts/AppLayout.vue';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import ApprovalMenu from '@/components/Approvals/ApprovalMenu.vue';
import ApprovalDataTable from '@/components/common/table/ApprovalDataTable.vue';
import ApproveAllButton from '@/components/common/buttons/ApproveAllButton.vue';
import RevokeButton from '@/components/common/buttons/RevokeButton.vue';
import ApproveBadge from '@/components/common/badges/ApproveBadge.vue';
import AccountTypeSelect from '@/components/common/select/AccountTypeSelect.vue';
import { useReceivingApproval } from '@/composables/approval/useReceivingApproval';
import { useAccountingTypeStore } from '@/stores/accounting/useAccountingType';
import { formatDate } from '@/utils/date';
import { useAuthStore } from '@/stores/utility/useAuthStore';

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

import type { Receiving } from '@/types/purchasing/receiving.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<Receiving>;
    filters: PaginateFilter & {
        approval_status?: 'pending' | 'processed';
        start_date?: string;
        end_date?: string;
    };
}>();

const toast = useToast();
const authStore = useAuthStore();
const accountingTypeStore = useAccountingTypeStore();

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
} = useReceivingApproval(props);

const statusOptions = [
    { label: 'Pending', value: 'pending' },
    { label: 'Processed', value: 'processed' },
];

const handleRowClick = async (event: any) => {
    const target = event.originalEvent?.target as HTMLElement;
    if (target?.closest('.p-checkbox') || target?.closest('.p-datatable-selection-column')) return;

    try {
        await loadDetail(event.data as Receiving);
    } catch {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load line items.', life: 3000 });
    }
};

const formatCurrency = (val: number) =>
    new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

const detailTotal = computed(() =>
    detailItems.value.reduce((sum, item) => sum + item.price, 0)
);

const canApprove = computed(() => authStore.hasPermission('approval.receiving.approve'));
const canReject = computed(() => authStore.hasPermission('approval.receiving.reject'));

const canPerformAction = computed(() => {
    if (currentStatus.value === 'pending') return canApprove.value;
    if (currentStatus.value === 'processed') return canReject.value;
    return false;
});

const handleApprove = (items: Receiving[]) => {
    const payload = items.map((item) => ({
        id: item.id,
        number: item.receiving_number,
        account_type_code: item.account_type_code,
        account_type_name: accountingTypeStore.getNameByCode(item.account_type_code) || item.account_type_name
    }));
    console.log('Approve Receiving Payload:', payload);
};

const handleRevoke = (items: Receiving[]) => {
    const payload = items.map((item) => ({
        id: item.id,
        number: item.receiving_number,
        account_type_code: item.account_type_code,
        account_type_name: accountingTypeStore.getNameByCode(item.account_type_code) || item.account_type_name
    }));
    console.log('Revoke Receiving Payload:', payload);
};
</script>

<template>

    <Head title="Receiving Approval" />

    <AppLayout>
        <div class="flex gap-6 h-[calc(100vh-8rem)]">

            <aside class="w-56 shrink-0 flex flex-col">
                <ApprovalMenu active="receiving" />
            </aside>

            <div class="flex-1 flex flex-col gap-2 min-w-0">

                <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between shrink-0">
                    <AppPageHeader title="Receiving"
                        description="Authorize pending receiving records and track processing history." />

                    <div class="flex items-center gap-3 h-10">
                        <div v-if="selectedItems.length > 0 && canPerformAction"
                            class="flex items-center gap-3 bg-card px-4 py-2 rounded-lg border border-border">
                            <span
                                class="text-[10px] font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                                <Badge :value="selectedItems.length" severity="info" />
                                Selected
                            </span>
                            <ApproveAllButton v-if="currentStatus === 'pending'"
                                @click="handleApprove(selectedItems)" />
                            <RevokeButton v-else @click="handleRevoke(selectedItems)" />
                        </div>
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
                                class="w-full! bg-background border-border! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! placeholder:text-muted-foreground!" />
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
                    <Splitter stateKey="rc-approval-splitter" layout="vertical"
                        class="h-full! border-none! bg-transparent!">
                        <SplitterPanel :size="55" :minSize="30">
                            <ApprovalDataTable :data="data" :filters="filters" v-model:selection="selectedItems"
                                :status="currentStatus" :activeRowId="activeRow?.id" :routeName="routeName"
                                @page="onPage" @sort="onSort" @row-click="handleRowClick" @approve="handleApprove"
                                @revoke="handleRevoke">
                                <Column v-if="canPerformAction" selectionMode="multiple" headerStyle="width: 3rem" />

                                <Column field="receiving_number" header="RC NUM" sortable class="w-48">
                                    <template #body="slotProps">
                                        <span class="text-xs font-bold text-foreground">
                                            {{ slotProps.data.receiving_number }}
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

                                <Column field="supplier_invoice_number" header="INV NUM" sortable class="w-40">
                                    <template #body="slotProps">
                                        <span class="text-xs font-medium text-foreground">
                                            {{ slotProps.data.supplier_invoice_number || '-' }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="purchase_order_number" header="PO NUM" sortable class="w-40">
                                    <template #body="slotProps">
                                        <span class="text-xs font-medium text-primary hover:underline">
                                            {{ slotProps.data.purchase_order_number || '-' }}
                                        </span>
                                    </template>
                                </Column>

                                <Column field="is_general_purchase" header="GP" class="w-16 text-center">
                                    <template #body="slotProps">
                                        <i v-if="slotProps.data.is_general_purchase"
                                            class="pi pi-check-circle text-success text-xs"></i>
                                        <i v-else class="pi pi-minus text-muted-foreground/30 text-xs"></i>
                                    </template>
                                </Column>

                                <Column field="is_inclusive_tax" header="TAX" class="w-16 text-center">
                                    <template #body="slotProps">
                                        <i v-if="slotProps.data.is_inclusive_tax"
                                            class="pi pi-check-circle text-primary text-xs"></i>
                                        <i v-else class="pi pi-minus text-muted-foreground/30 text-xs"></i>
                                    </template>
                                </Column>

                                <Column field="status" header="STATUS" class="w-28 text-center">
                                    <template #body="slotProps">
                                        <ApproveBadge :approved="!!slotProps.data.status" />
                                    </template>
                                </Column>

                                <Column field="grand_total" header="GRAND TOTAL" sortable class="w-36 text-right">
                                    <template #body="slotProps">
                                        <span class="text-xs font-bold text-foreground">
                                            {{ formatCurrency(slotProps.data.grand_total) }}
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

                                <template #empty>
                                    <div class="flex flex-col items-center justify-center py-12 gap-2">
                                        <i class="pi pi-inbox text-3xl text-muted-foreground/40"></i>
                                        <span class="text-[11px] font-medium text-muted-foreground">
                                            No receiving records found.
                                        </span>
                                    </div>
                                </template>
                            </ApprovalDataTable>
                        </SplitterPanel>

                        <!-- Detail: Line Items -->
                        <SplitterPanel :size="45" :minSize="20"
                            class="flex flex-col overflow-hidden bg-card rounded-xl border border-border mt-2">

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
                                        {{ activeRow.receiving_number }}
                                    </span>
                                    <span v-if="detailItems.length"
                                        class="text-[10px] font-bold text-primary tracking-wider">
                                        Total: {{ formatCurrency(detailTotal) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex-1 overflow-auto relative">
                                <DataTable :value="detailItems" size="small" stripedRows showGridlines>

                                    <Column field="product_name" header="PRODUCT NAME">
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

                                    <Column field="purchase_order_number" header="PO NUM" class="w-40">
                                        <template #body="slotProps">
                                            <span class="text-[11px] font-medium text-muted-foreground">
                                                {{ slotProps.data.purchase_order_number || '-' }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="account_type_code" header="ACCOUNT TYPE" class="w-56">
                                        <template #body="slotProps">
                                            <AccountTypeSelect 
                                                v-model="slotProps.data.account_type_code"
                                                placeholder="Assign Type"
                                            />
                                        </template>
                                    </Column>

                                    <Column field="quantity" header="QTY" class="w-24 text-right">
                                        <template #body="slotProps">
                                            <span class="text-xs font-bold text-foreground">
                                                {{ slotProps.data.quantity }}
                                            </span>
                                            <span class="text-[10px] text-muted-foreground ml-1">
                                                {{ slotProps.data.unit }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="buy_price" header="BUY PRICE" class="w-32 text-right">
                                        <template #body="slotProps">
                                            <span class="text-xs font-medium text-foreground">
                                                {{ formatCurrency(slotProps.data.buy_price) }}
                                            </span>
                                        </template>
                                    </Column>

                                    <Column field="price" header="SUB TOTAL" class="w-32 text-right">
                                        <template #body="slotProps">
                                            <span class="text-xs font-bold text-foreground">
                                                {{ formatCurrency(slotProps.data.price) }}
                                            </span>
                                        </template>
                                    </Column>

                                    <template #empty>
                                        <div class="flex flex-col items-center justify-center py-8 gap-2">
                                            <i class="pi pi-arrow-up text-xl text-muted-foreground/30"></i>
                                            <span class="text-[11px] font-medium text-muted-foreground">
                                                Select a receiving record above to view its line items.
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
