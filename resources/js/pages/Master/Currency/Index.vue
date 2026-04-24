<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useMasterDatatable } from '@/composables/master/useMasterDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { Currency } from '@/types/master/master.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<Currency>;
    filters: PaginateFilter;
}>();

const { search, onPage, onSort } = useMasterDatatable({
    routeName: 'master.currencies.index',
    props
});
</script>

<template>

    <Head title="Master Currency" />
    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold uppercase tracking-tight text-foreground">Currency</h1>
                    <p class="text-xs font-medium italic text-muted-foreground">Manage system currencies and exchange
                        rates.</p>
                </div>

                <IconField>
                    <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                    <InputText v-model="search" placeholder="Search Currency..." size="small" class="w-64!" />
                </IconField>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" @page="onPage" @sort="onSort">
                    <Column field="code" header="CODE" sortable class="w-32 font-bold" />
                    <Column field="name" header="NAME" sortable />
                    <Column field="rate" header="RATE" sortable class="text-right">
                        <template #body="slotProps">
                            {{ slotProps.data.rate.toLocaleString() }}
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
