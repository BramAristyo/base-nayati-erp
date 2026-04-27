<script setup lang="ts" generic="T extends Record<string, any>">
import Badge from 'primevue/badge';
import DataTable from 'primevue/datatable';
import ApproveAllButton from '@/components/common/buttons/ApproveAllButton.vue';
import RevokeButton from '@/components/common/buttons/RevokeButton.vue';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';

const props = defineProps<{
    data: PaginatedResponse<T>;
    filters: PaginateFilter;
    status: 'pending' | 'processed';
    loading?: boolean;
    activeRowId?: number | string | null;
    routeName?: string;
}>();

const selection = defineModel<T[]>('selection', { default: () => [] });

const emit = defineEmits<{
    (e: 'approve', items: T[]): void;
    (e: 'revoke', items: T[]): void;
    (e: 'page', event: any): void;
    (e: 'sort', event: any): void;
    (e: 'row-click', event: any): void;
}>();

const handleApprove = () => emit('approve', selection.value);
const handleRevoke = () => emit('revoke', selection.value);
</script>

<template>
    <div class="flex flex-col h-full bg-card rounded-xl border border-border shadow-xs overflow-hidden">
        <!-- Master Data Table -->
        <DataTable
            :key="routeName"
            :value="data.data"
            v-model:selection="selection"
            dataKey="id"
            lazy
            paginator
            :rows="data.per_page"
            :rowsPerPageOptions="[10, 25, 50, 100]"
            :totalRecords="data.total"
            :first="(data.current_page - 1) * data.per_page"
            :sortField="filters.sortField"
            :sortOrder="Number(filters.sortOrder) || -1"
            @page="$emit('page', $event)"
            @sort="$emit('sort', $event)"
            @row-click="$emit('row-click', $event)"
            :rowClass="(d: any) => activeRowId === d.id ? 'bg-primary/5!' : ''"
            size="small"
            stripedRows
            showGridlines
            responsiveLayout="scroll"
            class="flex-1 cursor-pointer"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown CurrentPageReport"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords}"
            :loading="loading"
        >
            <slot />
            <template #empty>
                <slot name="empty" />
            </template>
        </DataTable>
    </div>
</template>

<style scoped>
:deep(.p-datatable-thead > tr > th) {
    background: var(--muted) !important;
}
</style>
