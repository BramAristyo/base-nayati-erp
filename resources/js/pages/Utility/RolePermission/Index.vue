<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import type { Role } from '@/types/utility/role-permissions.types';
import { Head, router, Link } from '@inertiajs/vue3';
import { useDebounceFn } from '@vueuse/core';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable, { type DataTablePageEvent, type DataTableSortEvent, type DataTableRowClickEvent } from 'primevue/datatable';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import { ref, watch } from 'vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import { route } from 'ziggy-js';

const authStore = useAuthStore();
const props = defineProps<{
    roles: PaginatedResponse<Role>;
    filters: PaginateFilter;
}>();

const search = ref(props.filters?.search || '');

const updateRoute = (params: any) => {
    router.get(route('utility.roles.paginate'), {
        search: search.value,
        sortField: props.filters?.sortField,
        sortOrder: props.filters?.sortOrder,
        per_page: props.roles.per_page,
        page: props.roles.current_page,
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

const onRowClick = (event: DataTableRowClickEvent) => {
    if (authStore.hasPermission('utility.role.view') || authStore.hasPermission('utility.role.edit')) {
        router.get(route('utility.roles.show', { id: event.data.id }));
    }
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

const formatDate = (dateString?: string) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>

    <Head title="Role & Permissions" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-black uppercase tracking-tight">Role & Permissions</h1>
                    <p class="text-xs text-gray-500 font-medium italic">Configure system roles and define access control
                        policies.</p>
                </div>

                <div class="flex items-center gap-3">
                    <IconField>
                        <InputIcon class="pi pi-search text-gray-400!" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Quick Search..." size="small"
                            class="w-64! bg-white border-gray-200! text-gray-900! rounded-md! focus:ring-1! focus:ring-gray-300! transition-all shadow-sm placeholder:text-gray-400!" />
                    </IconField>
                    <Link v-if="authStore.hasPermission('utility.role.create')" :href="route('utility.roles.create')">
                        <Button icon="pi pi-plus" label="Create" size="small"
                            class="bg-black! border-none! text-white! font-bold! uppercase! tracking-widest! rounded-md! px-4! shadow-md!" />
                    </Link>
                </div>
            </div>

            <div class="overflow-hidden ">
                <DataTable :value="roles.data" lazy paginator :rows="roles.per_page"
                    :rowsPerPageOptions="[10, 25, 50, 100]" :totalRecords="roles.total"
                    :first="(roles.current_page - 1) * roles.per_page" @page="onPage" @sort="onSort" removableSort
                    :sortField="filters?.sortField || 'created_at'" :sortOrder="filters?.sortOrder || -1" size="small"
                    stripedRows showGridlines responsiveLayout="scroll" @row-click="onRowClick" class="cursor-pointer">
                    <template #empty>
                        <div class="p-8 text-center text-gray-500 text-sm font-medium">No roles found matching your
                            search.</div>
                    </template>

                    <Column field="name" header="NAME" sortable></Column>
                    <Column field="slug" header="SLUG" sortable></Column>
                    <Column field="description" header="DESCRIPTION"></Column>

                    <Column field="permissions_count" header="PERMISSIONS" sortable>
                        <template #body="slotProps">
                            <span
                                class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-800 border border-gray-200">
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

                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
