<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useMasterDatatable } from '@/composables/master/useMasterDatatable';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { Supplier } from '@/types/master/master.types';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import { formatDate } from '@/utils/date';

const props = defineProps<{
    data: PaginatedResponse<Supplier>;
    filters: PaginateFilter;
}>();

const { search, onPage, onSort } = useMasterDatatable({
    routeName: 'master.suppliers.index',
    props
});
</script>

<template>
    <Head title="Master Supplier" />
    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <div class="flex flex-col">
                    <h1 class="text-xl font-bold uppercase tracking-tight text-foreground">Master Supplier</h1>
                    <p class="text-xs font-medium italic text-muted-foreground">Manage vendor and supplier records.</p>
                </div>

                <IconField>
                    <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                    <InputText v-model="search" placeholder="Search Supplier..." size="small" class="w-64!" />
                </IconField>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" @page="onPage" @sort="onSort">
                    <Column field="code" header="CODE" sortable class="w-32 font-bold" />
                    <Column field="name" header="NAME" sortable />
                    <Column field="address" header="ADDRESS" />
                    <Column field="city" header="CITY" class="w-32" />
                    <Column field="country" header="COUNTRY" class="w-32" />
                    <Column field="contact_person" header="CONTACT" class="w-40" />
                    <Column field="phone" header="PHONE" class="w-40" />
                    <Column field="fax" header="FAX" class="w-32" />
                    <Column field="tin" header="TIN/NPWP" class="w-48" />
                    <Column field="updated_at" header="LAST UPDATE" class="w-40">
                        <template #body="slotProps">
                            {{ formatDate(slotProps.data.updated_at) }}
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
