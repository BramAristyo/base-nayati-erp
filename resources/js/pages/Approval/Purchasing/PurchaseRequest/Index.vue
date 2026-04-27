<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';
import http from '@/lib/http';
import { formatToDateString } from '@/utils/date';

// Layout & UI
import AppLayout from '@/Layouts/AppLayout.vue';
import AppPageHeader from '@/components/common/AppPageHeader.vue';

// PrimeVue Components
import Button from 'primevue/button';
import Column from 'primevue/column';
import SelectButton from 'primevue/selectbutton';
import Splitter from 'primevue/splitter';
import SplitterPanel from 'primevue/splitterpanel';
import DataTable, { type DataTableRowClickEvent, type DataTablePageEvent, type DataTableSortEvent } from 'primevue/datatable';
import Badge from 'primevue/badge';
import ProgressSpinner from 'primevue/progressspinner';
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import DatePicker from 'primevue/datepicker';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Divider from 'primevue/divider';
import { useToast } from 'primevue/usetoast';
import Toast from 'primevue/toast';

// Types
import type { PurchaseRequest } from '@/types/purchasing/purchase-request.types';
import type { PurchaseRequestItem } from '@/types/purchasing/purchase-request-item.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<PurchaseRequest>;
    filters: PaginateFilter & {
        approval_status?: 'pending' | 'processed';
        start_date?: string;
        end_date?: string;
    };
    message?: string;
}>();

const toast = useToast();

// --- Local Routing & Filtering ---
const currentStatus = ref<'pending' | 'processed'>(props.filters.approval_status || 'pending');
const search = ref(props.filters.search || '');
const startDate = ref(props.filters.start_date ? new Date(props.filters.start_date) : null);
const endDate = ref(props.filters.end_date ? new Date(props.filters.end_date) : null);

const getRouteName = () => {
    return currentStatus.value === 'pending'
        ? 'approval.purchasing.purchase-requests.pending'
        : 'approval.purchasing.purchase-requests.processed';
};

const navigate = (params: any = {}) => {
    const finalParams = {
        search: search.value || undefined,
        start_date: formatToDateString(startDate.value) || undefined,
        end_date: formatToDateString(endDate.value) || undefined,
        sortField: props.filters.sortField,
        sortOrder: props.filters.sortOrder,
        per_page: props.data.per_page,
        page: props.data.current_page,
        ...params
    };

    router.get(route(getRouteName()), finalParams, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const onLocalPage = (event: DataTablePageEvent) => {
    navigate({ page: (event.page ?? 0) + 1, per_page: event.rows });
};

const onLocalSort = (event: DataTableSortEvent) => {
    navigate({ sortField: event.sortField, sortOrder: event.sortOrder, page: 1 });
};

const resetFilters = () => {
    search.value = '';
    startDate.value = null;
    endDate.value = null;
    navigate({ page: 1 });
};

// --- Selection & Detail State ---
const selectedItems = ref<PurchaseRequest[]>([]);
const activeRow = ref<PurchaseRequest | null>(null);
const detailItems = ref<PurchaseRequestItem[]>([]);
const isLoadingDetails = ref(false);

// CRITICAL: Watch for status change and perform FULL ROUTE VISIT
watch(currentStatus, (newVal) => {
    selectedItems.value = [];
    activeRow.value = null;
    detailItems.value = [];

    const targetRoute = newVal === 'pending'
        ? 'approval.purchasing.purchase-requests.pending'
        : 'approval.purchasing.purchase-requests.processed';

    router.visit(route(targetRoute), {
        method: 'get',
        preserveScroll: true,
        preserveState: false
    });
});

watch([startDate, endDate], () => navigate({ page: 1 }));
watch(search, (newVal) => { if (newVal === '' || newVal.length > 2) navigate({ page: 1 }); });

// --- Interaction Logic ---
const handleRowClick = async (event: DataTableRowClickEvent) => {
    const target = event.originalEvent?.target as HTMLElement;
    if (target?.closest('.p-checkbox') || target?.closest('.p-datatable-selection-column')) return;

    const row = event.data as PurchaseRequest;
    if (activeRow.value?.id === row.id) return;

    activeRow.value = row;
    isLoadingDetails.value = true;
    detailItems.value = [];

    try {
        const response: any = await http.get(route('api.purchasing.purchase-requests.show', { id: row.id }));
        if (response.status) detailItems.value = response.data.items || [];
    } catch (e) {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load items.', life: 3000 });
    } finally {
        isLoadingDetails.value = false;
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);
};

const statusOptions = [{ label: 'Pending', value: 'pending' }, { label: 'Processed', value: 'processed' }];
const activeTab = ref('PR');
</script>

<template>

    <Head title="Purchase Request Approval" />
    <Toast />

    <AppLayout>
        <div class="flex flex-col gap-2 h-[calc(100vh-8rem)]">

            <!-- Level 1 Header: Title and Primary Actions -->
            <div class="flex items-center justify-between shrink-0 px-1">
                <AppPageHeader title="Purchase Request Approval"
                    description="Authorize pending purchase requests and track history." />

                <div class="flex items-center gap-4 h-10">
                    <div v-if="selectedItems.length > 0"
                        class="flex items-center gap-3 bg-card px-4 py-2 rounded-lg border border-border shadow-sm animate-in fade-in zoom-in-95 duration-200">
                        <span
                            class="text-[10px] font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                            <Badge :value="selectedItems.length" severity="primary" size="small" />
                            Items Selected
                        </span>
                        <Button v-if="currentStatus === 'pending'" label="Approve" size="small"
                            class="!bg-success-green !border-none !text-white !font-bold !uppercase !text-[10px] !px-4" />
                        <Button v-else label="Revoke" size="small"
                            class="!bg-destructive !border-none !text-white !font-bold !uppercase !text-[10px] !px-4" />
                    </div>
                </div>
            </div>

            <div class="px-1 shrink-0">
                <Tabs v-model:value="activeTab"
                    @update:value="(v) => v !== 'PR' ? router.visit(route('dashboard')) : null">
                    <TabList>
                        <Tab value="PR" class="!text-sm">Purchase
                            Request</Tab>
                        <Tab value="PO" class="!text-sm !opacity-40">
                            Purchase Order</Tab>
                        <Tab value="RCV" class="!text-sm !opacity-40">
                            Receiving</Tab>
                    </TabList>
                </Tabs>
            </div>

            <Divider class="my-1! px-1" />

            <!-- Level 2 Header: Filter Strip -->
            <div class="flex items-center justify-between gap-4 px-1 mb-2 ">
                <div class="flex items-center gap-3">
                    <SelectButton v-model="currentStatus" :options="statusOptions" optionLabel="label"
                        optionValue="value" class="approval-toggle" :allowEmpty="false" />
                    <IconField class="w-80">
                        <InputIcon class="pi pi-search" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Search PR number or requester..." size="small"
                            class="!w-full !bg-background !border-border! !text-foreground! !rounded-md! focus:ring-1! !focus:ring-ring! !shadow-sm !transition-all !placeholder:text-muted-foreground!" />
                    </IconField>
                </div>

                <div class="flex items-center gap-2">
                    <DatePicker v-model="startDate" placeholder="Start Date" size="small" showIcon iconDisplay="input"
                        dateFormat="yy-mm-dd" class="w-36!" />
                    <span class="text-border">/</span>
                    <DatePicker v-model="endDate" placeholder="End Date" size="small" showIcon iconDisplay="input"
                        dateFormat="yy-mm-dd" class="w-36!" />
                    <Button icon="pi pi-refresh" size="small" variant="outlined" severity="secondary"
                        @click="resetFilters" v-tooltip.top="'Reset All Filters'" class="!border-border/60" />
                </div>
            </div>

            <!-- Master-Detail Splitter -->
            <div class="flex-1 min-h-0 overflow-hidden mx-1">
                <Splitter stateKey="pr-approval-splitter-clean" layout="vertical"
                    class="!h-full !border-none !bg-transparent">

                    <!-- Top Pane: Paginated List -->
                    <SplitterPanel :size="55" :minSize="30"
                        class="flex flex-col overflow-hidden bg-card rounded-xl border border-border shadow-xs">
                        <DataTable :key="getRouteName()" :value="data.data" v-model:selection="selectedItems"
                            dataKey="id" lazy paginator :rows="data.per_page" :totalRecords="data.total"
                            :first="(data.current_page - 1) * data.per_page" :sortField="filters.sortField"
                            :sortOrder="Number(filters.sortOrder) || -1" @page="onLocalPage" @sort="onLocalSort"
                            @row-click="handleRowClick"
                            :rowClass="(d: any) => activeRow?.id === d.id ? 'bg-primary/5!' : ''" size="small"
                            showGridlines responsiveLayout="scroll" class="flex-1 cursor-pointer"
                            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown CurrentPageReport"
                            currentPageReportTemplate="{first}-{last} of {totalRecords}">
                            <Column selectionMode="multiple" headerStyle="width: 3rem" />
                            <Column field="purchase_request_number" header="PR NUM" sortable
                                class="font-bold text-primary text-[10px]" />
                            <Column field="date" header="DATE" sortable class="text-[10px]" />
                            <Column field="delivery_date" header="USAGE DATE" sortable class="text-[10px]" />
                            <Column field="budget_type" header="BUDGET" class="text-[10px]" />
                            <Column field="branch_code" header="BRANCH" class="text-[10px]" />
                            <Column field="employee_name" header="REQUESTER" sortable class="text-[10px]" />
                            <Column field="approved_by" header="APPROVED BY" sortable class="text-[10px]" />
                            <Column field="approval_date" header="APP DATE" sortable class="text-[10px]" />
                            <Column field="inventory_type" header="INVENTORY" class="text-[10px]" />
                            <Column field="department_name" header="DEPARTMENT" class="text-[10px]" />
                            <Column field="status" header="STATUS" class="text-center">
                                <template #body="{ data }">
                                    <span class="text-[10px] font-bold"
                                        :style="{ color: data.status ? 'var(--success-green)' : '#f97316' }">
                                        {{ data.status ? 'YES' : 'NO' }}
                                    </span>
                                </template>
                            </Column>
                            <template #empty>
                                <div class="flex flex-col items-center justify-center p-8 gap-2">
                                    <!-- <span class="text-[11px] font-medium text-muted-foreground">{{ message || 'No
                                        records found.' }}</span> -->
                                </div>
                            </template>
                        </DataTable>
                    </SplitterPanel>

                    <!-- Bottom Pane: Line Items -->
                    <SplitterPanel :size="45" :minSize="20"
                        class="flex flex-col overflow-hidden bg-card rounded-xl border border-border shadow-xs mt-4">

                        <div class="flex-1 overflow-auto relative">
                            <DataTable :value="detailItems" size="small" stripedRows class="!text-[10px]">
                                <Column field="product_code" header="CODE" class="font-mono" />
                                <Column field="product_name" header="DESCRIPTION">
                                    <template #body="{ data }">
                                        <div class="flex flex-col leading-tight">
                                            <span class="font-bold">{{ data.product_name }}</span>
                                            <span class="text-[9px] opacity-60">{{ data.variant_name }}</span>
                                        </div>
                                    </template>
                                </Column>
                                <Column field="quantity" header="QTY" class="text-right">
                                    <template #body="{ data }">
                                        <span class="font-bold">{{ data.quantity }}</span> <span class="text-[9px]">{{
                                            data.unit }}</span>
                                    </template>
                                </Column>
                                <Column field="price" header="EST. PRICE" class="text-right">
                                    <template #body="{ data }">{{ formatCurrency(data.price) }}</template>
                                </Column>
                                <Column field="remarks" header="REMARKS" />
                            </DataTable>
                            <div v-if="isLoadingDetails"
                                class="absolute inset-0 bg-background/50 backdrop-blur-[1px] flex items-center justify-center z-10">
                                <ProgressSpinner style="width: 24px; height: 24px" strokeWidth="8" />
                            </div>
                        </div>
                    </SplitterPanel>
                </Splitter>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.p-tablist-tab-list) {
    border-style: none !important;
    background-color: transparent !important;
}

:deep(.approval-toggle .p-button) {
    font-size: 10px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    padding: 0.25rem 1rem !important;
    border-color: var(--border) !important;
}

:deep(.approval-toggle .p-button.p-highlight) {
    background-color: var(--primary) !important;
    border-color: var(--primary) !important;
    color: var(--primary-foreground) !important;
}
</style>
