<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import ActiveBadge from '@/components/common/badges/ActiveBadge.vue';
import PositionBadge from '@/components/common/badges/PositionBadge.vue';
import type { PaginatedUsers } from '@/types/utility/user.types';
import type { PaginateFilter } from '@/types/common/paginate.types';
import { Head, router } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable, { type DataTablePageEvent, type DataTableSortEvent } from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { ref, watch } from 'vue';

const props = defineProps<{
    users: PaginatedUsers;
    filters: PaginateFilter;
}>();

const search = ref(props.filters?.search || '');

const updateRoute = (params: any) => {
    router.get(route('utility.users.paginate'), {
        search: search.value,
        sortField: props.filters?.sortField,
        sortOrder: props.filters?.sortOrder,
        per_page: props.users.per_page,
        page: props.users.current_page,
        ...params
    }, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
};

const onPage = (event: DataTablePageEvent) => {
    updateRoute({
        page: event.page + 1,
        per_page: event.rows
    });
};

const onSort = (event: DataTableSortEvent) => {
    updateRoute({
        sortField: event.sortField,
        sortOrder: event.sortOrder,
        page: 1
    });
};

const performSearch = useDebounceFn(() => {
    updateRoute({
        search: search.value,
        page: 1
    });
}, 500);

watch(search, () => {
    performSearch();
});
</script>

<template>

    <Head title="User Management" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-black uppercase tracking-tight">User Management</h1>
                    <p class="text-xs text-gray-500 font-medium italic">Overview of all system terminals and authorized
                        personnel.</p>
                </div>

                <div class="flex items-center gap-3">
                    <IconField>
                        <InputIcon class="pi pi-search text-gray-400!" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Quick Search..." size="small"
                            class="w-64! bg-white border-gray-200! text-gray-900! rounded-md! focus:ring-1! focus:ring-gray-300! transition-all shadow-sm placeholder:text-gray-400!" />
                    </IconField>
                    <Button icon="pi pi-plus" label="Create" size="small"
                        class="bg-black! border-none! text-white! font-bold! uppercase! tracking-widest! rounded-md! px-4! shadow-md!" />
                </div>
            </div>

            <div class="overflow-hidden ">
                <DataTable :value="users.data" lazy paginator :rows="users.per_page"
                    :rowsPerPageOptions="[10, 25, 50, 100]" :totalRecords="users.total"
                    :first="(users.current_page - 1) * users.per_page" @page="onPage" @sort="onSort" removableSort
                    :sortField="filters?.sortField || 'name'" :sortOrder="filters?.sortOrder || 1" size="small"
                    stripedRows showGridlines responsiveLayout="scroll">
                    <template #empty>
                        <div class="p-8 text-center text-gray-500 text-sm font-medium">No users found matching your
                            search.</div>
                    </template>

                    <Column field="name" header="NAME" sortable></Column>
                    <Column field="email" header="IDENTITY" sortable></Column>

                    <Column field="position" header="POSITION">
                        <template #body="slotProps">
                            <PositionBadge :position="slotProps.data.position" />
                        </template>
                    </Column>

                    <Column field="approver_name" header="APPROVER"></Column>
                    <Column field="approver_title" header="APPROVER TITLE"></Column>

                    <Column field="branch_code" header="BRANCH">
                    </Column>

                    <Column field="is_active" header="STATUS">
                        <template #body="slotProps">
                            <ActiveBadge :active="!!slotProps.data.is_active" />
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
