<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useMasterDatatable } from '@/composables/master/useMasterDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { DeliveryTerm } from '@/types/master/master.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<DeliveryTerm>;
    filters: PaginateFilter;
}>();

const { search, onPage, onSort } = useMasterDatatable({
    routeName: 'master.delivery-terms.index',
    props
});
</script>

<template>

    <Head title="Master Delivery Term" />
    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold uppercase tracking-tight text-foreground">Delivery Term</h1>
                    <p class="text-xs font-medium italic text-muted-foreground">Manage shipment and delivery conditions.
                    </p>
                </div>

                <IconField>
                    <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                    <InputText v-model="search" placeholder="Search Term..." size="small" class="w-64!" />
                </IconField>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" @page="onPage" @sort="onSort">
                    <Column field="id" header="ID" sortable class="w-24 font-bold" />
                    <Column field="name" header="DESCRIPTION" sortable />
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
