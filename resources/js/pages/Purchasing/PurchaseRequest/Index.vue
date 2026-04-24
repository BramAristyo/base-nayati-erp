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
import { usePurchaseRequestDatatable } from '@/composables/purchasing/usePurchaseRequestDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { PurchaseRequest } from '@/types/purchasing/purchase-request.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import { formatDate } from '@/utils/date';

const props = defineProps<{
    data: PaginatedResponse<PurchaseRequest>;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

const authStore = useAuthStore();
const { search, startDate, endDate, onPage, onSort, resetFilters } = usePurchaseRequestDatatable(props);

const onRowClick = (event: any) => {
    if (authStore.hasPermission('purchasing.purchase-request.view')) {
        router.get(route('purchasing.purchase-requests.show', { id: event.data.id }));
    }
};
</script>

<template>

    <Head title="Purchase Request" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between">
                <AppPageHeader title="Purchase Request" description="Manage and track internal purchase requests." />

                <div class="flex flex-wrap items-center gap-3">
                    <IconField>
                        <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Quick Search..." size="small"
                            class="w-64! bg-background border-border! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! shadow-sm transition-all placeholder:text-muted-foreground!" />
                    </IconField>

                    <div class="flex items-center gap-2">
                        <DatePicker v-model="startDate" placeholder="Start Date" size="small" dateFormat="yy-mm-dd"
                            showIcon iconDisplay="input" class="w-36!" inputClass="py-2! text-sm!" />
                        <span class="text-border">/</span>
                        <DatePicker v-model="endDate" placeholder="End Date" size="small" dateFormat="yy-mm-dd" showIcon
                            iconDisplay="input" class="w-36!" inputClass="py-2! text-sm!" />
                    </div>

                    <Button icon="pi pi-refresh" size="small" variant="outlined" severity="secondary"
                        class="rounded-md!" v-tooltip.top="'Reset Filters'" @click="resetFilters" />

                    <Button v-if="authStore.hasPermission('purchasing.purchase-request.create')" icon="pi pi-plus"
                        label="Create" size="small"
                        class="bg-primary! border-none! text-primary-foreground! px-4! font-bold! uppercase! tracking-widest! rounded-md! shadow-md!"
                        @click="router.get(route('dashboard'))" />
                </div>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" class="cursor-pointer" @page="onPage" @sort="onSort"
                    @row-click="onRowClick">
                    <template #empty>
                        <div class="p-8 text-center text-sm font-medium text-muted-foreground">
                            No purchase requests found matching your search.
                        </div>
                    </template>

                    <Column field="purchase_request_number" header="PR NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{ slotProps.data.purchase_request_number
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

                    <Column field="status" header="STATUS" class="w-32 text-center">
                        <template #body="slotProps">
                            <ApproveBadge :approved="!!slotProps.data.status" />
                        </template>
                    </Column>

                    <Column field="delivery_date" header="USAGE DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.delivery_date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="budget_type" header="BUDGET TYPE" class="w-32">
                        <template #body="slotProps">
                            <span class="text-[10px] font-bold uppercase tracking-tight text-muted-foreground">{{
                                slotProps.data.budget_type }}</span>
                        </template>
                    </Column>

                    <Column field="branch_code" header="BRANCH" class="w-24">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.branch_code }}</span>
                        </template>
                    </Column>

                    <Column field="employee_name" header="EMPLOYEE NAME" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.employee_name
                                }}</span>
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

                    <Column field="inventory_type" header="INVENTORY TYPE" class="w-32">
                        <template #body="slotProps">
                            <span class="text-[10px] font-bold uppercase tracking-tight text-muted-foreground">{{
                                slotProps.data.inventory_type }}</span>
                        </template>
                    </Column>

                    <Column field="department_name" header="DEPARTMENT">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-foreground">{{ slotProps.data.department_name
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
