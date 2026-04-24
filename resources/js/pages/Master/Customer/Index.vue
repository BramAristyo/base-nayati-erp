<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useMasterDatatable } from '@/composables/master/useMasterDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { Customer } from '@/types/master/master.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<Customer>;
    filters: PaginateFilter;
}>();

const { search, onPage, onSort } = useMasterDatatable({
    routeName: 'master.customers.index',
    props
});
</script>

<template>

    <Head title="Master Customer" />
    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold uppercase tracking-tight text-foreground">Customer</h1>
                    <p class="text-xs font-medium italic text-muted-foreground">Manage customer records and details.</p>
                </div>

                <IconField>
                    <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                    <InputText v-model="search" placeholder="Search Customer..." size="small" class="w-64!" />
                </IconField>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" @page="onPage" @sort="onSort">
                    <Column field="code" header="CODE" sortable class="w-32 font-bold" />
                    <Column field="name" header="NAME" sortable />
                    <Column field="commercial_name" header="COMMERCIAL NAME" sortable />
                    <Column field="branch_code" header="BRANCH" class="w-24" />
                    <Column field="city" header="CITY" class="w-32" />
                    <Column field="address" header="ADDRESS" />
                    <Column field="phone" header="PHONE" class="w-40" />
                    <Column field="npwp" header="NPWP" class="w-48" />
                    <Column field="is_has_sales_order" header="HAS SO" class="w-24 text-center">
                        <template #body="slotProps">
                            <i v-if="slotProps.data.is_has_sales_order" class="pi pi-check text-green-500 text-xs"></i>
                            <i v-else class="pi pi-times text-muted-foreground text-[10px]"></i>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
