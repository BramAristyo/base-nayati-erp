<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useDataTable } from '@/composables/common/useDataTable';
import Column from 'primevue/column';

const props = defineProps<{
    data: any;
    filters: any;
}>();

const { onPage, onSort } = useDataTable('approval.purchasing.purchase-requests.index', props.filters);
</script>

<template>
    <Head title="Purchase Request Approval" />

    <AppLayout>
        <div class="space-y-6">
            <AppPageHeader 
                title="Purchase Request Approval" 
                description="Manage and process pending purchase requests."
            />

            <div class="bg-card rounded-xl border border-border shadow-xs overflow-hidden">
                <StandardDataTable 
                    :data="data" 
                    :filters="filters"
                    @page="onPage"
                    @sort="onSort"
                >
                    <Column field="purchase_request_number" header="PR Number" sortable />
                    <Column field="date" header="Date" sortable />
                    <Column field="employee_name" header="Requested By" sortable />
                    <Column field="department_name" header="Department" />
                    <Column field="status" header="Status">
                        <template #body="{ data }">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase"
                                :class="data.status ? 'bg-success-green/10 text-success-green' : 'bg-orange-500/10 text-orange-500'">
                                {{ data.status ? 'Approved' : 'Pending' }}
                            </span>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
