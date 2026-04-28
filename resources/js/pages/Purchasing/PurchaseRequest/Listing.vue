<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Column from 'primevue/column';
import DatePicker from 'primevue/datepicker';
import Divider from 'primevue/divider';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import InputNumber from 'primevue/inputnumber';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import StandardDataTable from '@/components/common/table/StandardDataTable.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { PaginatedResponse, PaginateFilter } from '@/types/common/paginate.types';
import type { PurchaseRequestItem } from '@/types/purchasing/purchase-request-item.types';
import { formatDate, formatToDateString } from '@/utils/date';
import { ref, watch, computed } from 'vue';
import { useDataTable } from '@/composables/common/useDataTable';

const props = defineProps<{
    data: PaginatedResponse<PurchaseRequestItem>;
    filters: PaginateFilter & { start_date?: string; end_date?: string };
}>();

// DataTable Logic
const startDate = ref(props.filters?.start_date ? new Date(props.filters.start_date) : null);
const endDate = ref(props.filters?.end_date ? new Date(props.filters.end_date) : null);

const { search, onPage, onSort, updateRoute } = useDataTable({
    routeName: 'purchasing.purchase-requests.listingItems',
    filters: props.filters,
    pagination: props.data,
    extraParams: () => ({
        start_date: formatToDateString(startDate.value),
        end_date: formatToDateString(endDate.value),
    })
});

watch([startDate, endDate], () => {
    updateRoute({ page: 1 });
});

const resetFilters = () => {
    search.value = '';
    startDate.value = null;
    endDate.value = null;
    updateRoute({ page: 1 });
};

// Dialog Logic
const isDialogVisible = ref(false);
const selectedItem = ref<PurchaseRequestItem | null>(null);
const editAdjQty = ref<number>(0);

const resultingQty = computed(() => {
    const currentQty = selectedItem.value?.quantity || 0;
    return currentQty + (editAdjQty.value || 0);
});

const minAllowedAdjustment = computed(() => {
    if (!selectedItem.value) return 0;
    // Rule: resultingQty >= ordered_quantity
    // Qty + AdjQty >= OrderedQty
    // AdjQty >= OrderedQty - Qty
    return selectedItem.value.ordered_quantity - selectedItem.value.quantity;
});

const onEditAdjustedQty = (data: PurchaseRequestItem) => {
    selectedItem.value = data;
    editAdjQty.value = data.adjusted_quantity || 0;
    isDialogVisible.value = true;
};

const saveAdjustedQty = () => {
    if (selectedItem.value) {
        selectedItem.value.adjusted_quantity = editAdjQty.value;
        alert(`Successfully updated Adjusted Qty for ${selectedItem.value.purchase_request_number}`);
        isDialogVisible.value = false;
    }
};
</script>

<template>

    <Head title="Purchase Request Listing" />

    <AppLayout>
        <div class="flex flex-col gap-2">
            <!-- Level 1 Header: Title -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader title="Purchase Request Listing" description="View and adjust purchase request items." />
            </div>

            <Divider class="my-2!" />

            <!-- Level 2 Header: Filters Strip -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-1 mb-4">
                <div class="flex-1">
                    <IconField>
                        <InputIcon class="pi pi-search text-muted-foreground!" style="font-size: 14px" />
                        <InputText v-model="search" placeholder="Quick Search..." size="small"
                            class="w-full! bg-background border-border! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! shadow-sm transition-all placeholder:text-muted-foreground!" />
                    </IconField>
                </div>

                <div class="flex items-center gap-2 self-end md:self-auto">
                    <DatePicker v-model="startDate" placeholder="Start Date" size="small" dateFormat="yy-mm-dd" showIcon
                        iconDisplay="input" class="w-36!" inputClass="py-2! text-sm!" />
                    <span class="text-border px-1">/</span>
                    <DatePicker v-model="endDate" placeholder="End Date" size="small" dateFormat="yy-mm-dd" showIcon
                        iconDisplay="input" class="w-36!" inputClass="py-2! text-sm!" />

                    <Button icon="pi pi-refresh" size="small" variant="outlined" severity="secondary"
                        class="rounded-md! border-border!" v-tooltip.top="'Reset Filters'" @click="resetFilters" />
                </div>
            </div>

            <div class="overflow-hidden">
                <StandardDataTable :data="data" :filters="filters" @page="onPage" @sort="onSort">
                    <template #empty>
                        <div class="p-8 text-center text-sm font-medium text-muted-foreground">
                            No items found matching your search.
                        </div>
                    </template>

                    <!-- Move Edit Button to Far Left and make it Grey -->
                    <Column class="w-16 text-center">
                        <template #body="slotProps">
                            <Button icon="pi pi-pencil" severity="secondary" variant="text" rounded size="small"
                                v-tooltip.top="'Edit Adjusted Qty'" @click="onEditAdjustedQty(slotProps.data)" />
                        </template>
                    </Column>

                    <Column field="purchase_request_number" header="PR NUM" sortable class="w-48">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-foreground">{{ slotProps.data.purchase_request_number
                            }}</span>
                        </template>
                    </Column>

                    <Column field="date" header="DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="product_code" header="PRODUCT CODE" sortable class="w-40">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-foreground">{{ slotProps.data.product_code }}</span>
                        </template>
                    </Column>

                    <Column field="product_name" header="PRODUCT NAME" sortable>
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-foreground">{{ slotProps.data.product_name }}</span>
                        </template>
                    </Column>

                    <Column field="quantity" header="QTY" sortable class="w-24 text-right">
                        <template #body="slotProps">
                            <span class="text-xs font-semibold text-foreground">{{ slotProps.data.quantity }}</span>
                        </template>
                    </Column>

                    <Column field="adjusted_quantity" header="ADJ QTY" sortable class="w-24 text-right">
                        <template #body="slotProps">
                            <span class="text-xs font-bold text-primary">{{ slotProps.data.adjusted_quantity }}</span>
                        </template>
                    </Column>

                    <Column field="ordered_quantity" header="PO QTY" sortable class="w-24 text-right">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.ordered_quantity
                            }}</span>
                        </template>
                    </Column>

                    <Column field="usage_date" header="USAGE DATE" sortable class="w-32">
                        <template #body="slotProps">
                            <span class="text-[11px] font-medium text-muted-foreground">
                                {{ formatDate(slotProps.data.usage_date) }}
                            </span>
                        </template>
                    </Column>

                    <Column field="remarks" header="REMARKS">
                        <template #body="slotProps">
                            <span class="text-xs font-medium text-muted-foreground">{{ slotProps.data.remarks || '-'
                            }}</span>
                        </template>
                    </Column>
                </StandardDataTable>
            </div>
        </div>

        <!-- Edit Adjusted Quantity Dialog -->
        <Dialog v-model:visible="isDialogVisible" header="Edit Adjusted Quantity" :modal="true" :style="{ width: '450px' }">
            <div v-if="selectedItem" class="flex flex-col gap-4">
                <div class="p-4 bg-muted/30 rounded-lg border border-border">
                    <p class="text-sm font-semibold text-foreground mb-1">
                        PR Number: <span class="text-primary">{{ selectedItem.purchase_request_number }}</span>
                    </p>
                    <p class="text-xs text-muted-foreground">
                        Product: <span class="font-medium text-foreground">{{ selectedItem.product_name }}</span>
                    </p>
                </div>

                <div class="flex flex-col gap-2">
                    <label for="adj_qty" class="text-sm font-bold text-foreground">Adjusted Quantity</label>
                    <InputNumber id="adj_qty" v-model="editAdjQty" :min="minAllowedAdjustment" fluid showButtons
                        class="bg-background border-border! text-foreground!" />
                    <p class="text-[10px] text-muted-foreground italic">
                        Enter the adjustment value (positive to increase, negative to decrease).
                    </p>
                </div>

                <div class="mt-2 p-3 bg-muted/20 border border-border rounded-md">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-muted-foreground">Current Quantity</span>
                        <span class="font-medium text-foreground">{{ selectedItem.quantity }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mt-1">
                        <span class="text-muted-foreground">Adjustment</span>
                        <span class="font-medium" :class="editAdjQty < 0 ? 'text-red-500' : 'text-green-500'">
                            {{ editAdjQty > 0 ? '+' : '' }}{{ editAdjQty || 0 }}
                        </span>
                    </div>
                    <Divider class="my-2!" />
                    <div class="flex justify-between items-center text-sm font-bold">
                        <span class="text-foreground">Resulting Quantity</span>
                        <span class="text-primary">{{ resultingQty }}</span>
                    </div>
                    <div class="mt-2 text-[10px] text-muted-foreground" v-if="selectedItem.ordered_quantity > 0">
                        * Resulting quantity cannot be lower than the PO Quantity ({{ selectedItem.ordered_quantity }}). Max negative adjustment: {{ minAllowedAdjustment }}.
                    </div>
                </div>
            </div>

            <template #footer>
                <div class="flex justify-end gap-2 mt-4">
                    <Button label="Cancel" severity="secondary" variant="text" size="small" @click="isDialogVisible = false" />
                    <Button label="Save Changes" severity="primary" size="small" @click="saveAdjustedQty"
                        class="bg-primary! text-primary-foreground! px-4! font-bold!" />
                </div>
            </template>
        </Dialog>
    </AppLayout>
</template>
