<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import type { DataTableRowClickEvent } from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { route } from 'ziggy-js';
import ActiveBadge from '@/components/common/badges/ActiveBadge.vue';
import PositionBadge from '@/components/common/badges/PositionBadge.vue';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useDataTable } from '@/composables/common/useDataTable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { PaginateFilter } from '@/types/common/paginate.types';
import type { PaginatedUsers } from '@/types/utility/user.types';

const authStore = useAuthStore();
const props = defineProps<{
    users: PaginatedUsers;
    filters: PaginateFilter;
}>();

const { search, onPage, onSort } = useDataTable({
    routeName: 'utility.users.paginate',
    filters: props.filters,
    pagination: props.users,
});

const onRowClick = (event: DataTableRowClickEvent) => {
    if (authStore.hasPermission('utility.user.view') || authStore.hasPermission('utility.user.edit')) {
        router.get(route('utility.users.show', { id: event.data.id }));
    }
};
</script>

<template>
    <Head title="User Management" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold uppercase tracking-tight text-black">User Management</h1>
                    <p class="text-xs font-medium italic text-gray-500">
                        Overview of all system terminals and authorized personnel.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <IconField>
                        <InputIcon class="pi pi-search text-gray-400!" style="font-size: 14px" />
                        <InputText
                            v-model="search"
                            placeholder="Quick Search..."
                            size="small"
                            class="w-64! bg-white border-gray-200! text-gray-900! rounded-md! focus:ring-1! focus:ring-gray-300! shadow-sm transition-all placeholder:text-gray-400!"
                        />
                    </IconField>
                    <Link v-if="authStore.hasPermission('utility.user.create')" :href="route('utility.users.create')">
                        <Button
                            icon="pi pi-plus"
                            label="Create"
                            size="small"
                            class="bg-black! border-none! text-white! px-4! font-bold! uppercase! tracking-widest! rounded-md! shadow-md!"
                        />
                    </Link>
                </div>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable
                    :data="users"
                    :filters="filters"
                    class="cursor-pointer"
                    @page="onPage"
                    @sort="onSort"
                    @row-click="onRowClick"
                >
                    <template #empty>
                        <div class="p-8 text-center text-sm font-medium text-gray-500">
                            No users found matching your search.
                        </div>
                    </template>

                    <Column field="name" header="NAME" sortable></Column>
                    <Column field="email" header="IDENTITY" sortable></Column>

                    <Column field="position" header="POSITION" sortable>
                        <template #body="slotProps">
                            <PositionBadge :position="slotProps.data.position" />
                        </template>
                    </Column>

                    <Column field="approver_name" header="APPROVER"></Column>
                    <Column field="approver_title" header="APPROVER TITLE"></Column>

                    <Column field="branch_code" header="BRANCH"></Column>

                    <Column field="is_active" header="STATUS">
                        <template #body="slotProps">
                            <ActiveBadge :active="!!slotProps.data.is_active" />
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
