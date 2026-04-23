<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import type { DataTableRowClickEvent } from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { route } from 'ziggy-js';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import { useDataTable } from '@/composables/common/useDataTable';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import type { Role } from '@/types/utility/role-permissions.types';
import { formatDate } from '@/utils/date';

const authStore = useAuthStore();
const props = defineProps<{
    roles: PaginatedResponse<Role>;
    filters: PaginateFilter;
}>();

const { search, onPage, onSort } = useDataTable({
    routeName: 'utility.roles.paginate',
    filters: props.filters,
    pagination: props.roles,
});

const onRowClick = (event: DataTableRowClickEvent) => {
    if (event.data.id === 1) return;
    if (authStore.hasPermission('utility.role.view') || authStore.hasPermission('utility.role.edit')) {
        router.get(route('utility.roles.show', { id: event.data.id }));
    }
};

const getRowClass = (data: any) => {
    return data.id === 1 ? 'opacity-80' : 'cursor-pointer';
};
</script>

<template>

    <Head title="Role & Permissions" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold uppercase tracking-tight text-black">Role & Permissions</h1>
                    <p class="text-xs font-medium italic text-gray-500">
                        Configure system roles and define access control policies.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <IconField>
                        <InputIcon class="pi pi-search text-gray-400!" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Quick Search..." size="small"
                            class="w-64! bg-white border-gray-200! text-gray-900! rounded-md! focus:ring-1! focus:ring-gray-300! shadow-sm transition-all placeholder:text-gray-400!" />
                    </IconField>
                    <Link v-if="authStore.hasPermission('utility.role.create')" :href="route('utility.roles.create')">
                        <Button icon="pi pi-plus" label="Create" size="small"
                            class="bg-black! border-none! text-white! px-4! font-bold! uppercase! tracking-widest! rounded-md! shadow-md!" />
                    </Link>
                </div>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="roles" :filters="filters" class="cursor-pointer" @page="onPage" @sort="onSort"
                    @row-click="onRowClick">
                    <template #empty>
                        <div class="p-8 text-center text-sm font-medium text-gray-500">
                            No roles found matching your search.
                        </div>
                    </template>

                    <Column field="name" header="NAME" sortable></Column>
                    <Column field="slug" header="SLUG" sortable></Column>
                    <Column field="description" header="DESCRIPTION"></Column>

                    <Column field="permissions_count" header="PERMISSIONS" sortable>
                        <template #body="slotProps">
                            <span
                                class="rounded-full border border-gray-200 bg-gray-100 px-2 py-0.5 text-xs font-bold text-gray-800">
                                {{ slotProps.data.permissions_count || 0 }} Actions
                            </span>
                        </template>
                    </Column>

                    <Column field="created_at" header="CREATED AT" sortable>
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-gray-600">
                                {{ formatDate(slotProps.data.created_at) }}
                            </span>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>
    </AppLayout>
</template>
