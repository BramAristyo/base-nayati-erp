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
const { search, startDate, endDate, onPage, onSort, resetFilters, onExport } = usePurchaseRequestDatatable(props);

const onRowClick = (event: any) => {
    if (authStore.hasPermission('purchasing.purchase-request.view')) {
        router.get(route('purchasing.purchase-requests.show', { id: event.data.id }));
    }
};

const onListingDummy = () => {
    router.get(route('purchasing.purchase-requests.listingItems'));
};
</script>

<template>

    <Head title="Purchase Request" />

    <AppLayout>
        <div class="flex flex-col gap-2">
            <!-- Level 1 Header: Title and Primary Actions -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader title="Purchase Request" description="Manage and track internal purchase requests." />

                <div class="flex items-center gap-2">
                    <Button icon="pi pi-list" severity="secondary" variant="outlined" rounded size="small"
                        class="border-border! text-foreground! hover:bg-accent! rounded-md!"
                        v-tooltip.bottom="'Listing View'" @click="onListingDummy" />

                    <Button v-if="authStore.hasPermission('purchasing.purchase-request.export')" icon="pi pi-file-excel"
                        severity="success" size="small" v-tooltip.bottom="'Export to Excel'"
                        @click="onExport"
                        class="bg-success-green! border-none! text-success-green-foreground! rounded-md! shadow-sm!" />

                    <Button v-if="authStore.hasPermission('purchasing.purchase-request.create')" icon="pi pi-plus"
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
                        <InputText 
                            v-model="search" 
                            placeholder="Quick Search..." 
                            size="small"
                            class="w-full! bg-background border-border! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! shadow-sm transition-all placeholder:text-muted-foreground!" 
                        />
                    </IconField>
                </div>

                <div class="flex items-center gap-2 self-end md:self-auto">
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
                    <span class="text-border px-1">/</span>
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

                    <Button 
                        icon="pi pi-refresh" 
                        size="small" 
                        variant="outlined" 
                        severity="secondary"
                        class="rounded-md! border-border!" 
                        v-tooltip.top="'Reset Filters'" 
                        @click="resetFilters" 
                    />
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
