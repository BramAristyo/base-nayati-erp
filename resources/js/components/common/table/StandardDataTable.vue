<script setup lang="ts">
import DataTable from 'primevue/datatable';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

interface Props {
    data: PaginatedResponse<any>;
    filters?: PaginateFilter;
    loading?: boolean;
    removableSort?: boolean;
}

withDefaults(defineProps<Props>(), {
    loading: false,
    removableSort: true,
});

const emit = defineEmits(['page', 'sort']);
</script>

<template>
    <DataTable
        v-bind="$attrs"
        :value="data.data"
        lazy
        paginator
        :rows="data.per_page"
        :rowsPerPageOptions="[10, 25, 50, 100]"
        :totalRecords="data.total"
        :first="(data.current_page - 1) * data.per_page"
        :loading="loading"
        :sortField="filters?.sortField"
        :sortOrder="filters?.sortOrder"
        :removableSort="removableSort"
        size="small"
        stripedRows
        showGridlines
        responsiveLayout="scroll"
        @page="emit('page', $event)"
        @sort="emit('sort', $event)"
    >
        <template v-for="(_, name) in $slots" :key="name" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps || {}" />
        </template>
    </DataTable>
</template>
