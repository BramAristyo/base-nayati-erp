<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useMasterDatatable } from '@/composables/master/useMasterDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { Employee } from '@/types/master/master.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<Employee>;
    filters: PaginateFilter;
}>();

const { search, onPage, onSort } = useMasterDatatable({
    routeName: 'master.employees.index',
    props
});
</script>

<template>

    <Head title="Master Employee" />
    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold uppercase tracking-tight text-foreground">Employee</h1>
                    <p class="text-xs font-medium italic text-muted-foreground">Manage employee records (mkar).</p>
                </div>

                <IconField>
                    <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                    <InputText v-model="search" placeholder="Search Employee..." size="small" class="w-64!" />
                </IconField>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" @page="onPage" @sort="onSort">
                    <Column field="nik" header="NIK" sortable class="w-32 font-bold" />
                    <Column field="name" header="NAME" sortable />
                    <Column field="address" header="ADDRESS" />
                    <Column field="city" header="CITY" class="w-32" />
                    <Column field="phone" header="PHONE" class="w-40" />
                    <Column field="mobile_phone" header="MOBILE" class="w-40" />
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
