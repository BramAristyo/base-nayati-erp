<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import Panel from 'primevue/panel';
import { route } from 'ziggy-js';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { DeliveryOrder } from '@/types/sales/delivery-order.types';
import { formatDate } from '@/utils/date';

const props = defineProps<{
    deliveryOrder: DeliveryOrder;
}>();

const onBack = () => {
    router.get(route('sales.delivery-orders.index'));
};
</script>

<template>
    <Head :title="'Delivery Order - ' + deliveryOrder.delivery_order_number" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader
                    :title="deliveryOrder.delivery_order_number"
                    :description="'Created on ' + formatDate(deliveryOrder.date)"
                />

                <div class="flex items-center gap-2">
                    <Button
                        label="Back to List"
                        icon="pi pi-arrow-left"
                        size="small"
                        variant="outlined"
                        severity="secondary"
                        class="rounded-md!"
                        @click="onBack"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <Panel header="General Information">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Customer Name</span>
                                <span class="text-sm font-semibold text-foreground">{{ deliveryOrder.customer_name }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Sales Order Number</span>
                                <span class="text-sm font-semibold text-foreground">{{ deliveryOrder.sales_order_number || '-' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Branch Code</span>
                                <span class="text-sm font-semibold text-foreground">{{ deliveryOrder.branch_code }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Delivery Date</span>
                                <span class="text-sm font-semibold text-foreground">{{ formatDate(deliveryOrder.delivery_date) }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Category</span>
                                <span class="text-sm font-semibold text-foreground">{{ deliveryOrder.category || '-' }}</span>
                            </div>
                        </div>
                    </Panel>

                    <Panel header="Delivery Items">
                        <div class="p-4 text-center text-muted-foreground italic text-xs">
                            Detailed items view is currently under development.
                        </div>
                    </Panel>
                </div>

                <div class="flex flex-col gap-6">
                    <Panel header="Status Summary" class="h-full">
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-xs font-medium text-muted-foreground">Status</span>
                                <span :class="deliveryOrder.status ? 'text-green-600' : 'text-orange-600'" class="text-sm font-bold uppercase">
                                    {{ deliveryOrder.status ? 'Approved' : 'Pending' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center py-2" v-if="deliveryOrder.approval_date">
                                <span class="text-xs font-medium text-muted-foreground">Approval Date</span>
                                <span class="text-sm font-semibold text-foreground">{{ formatDate(deliveryOrder.approval_date) }}</span>
                            </div>
                        </div>
                    </Panel>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
:deep(.p-panel-header) {
    background-color: transparent;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0.75rem;
    margin-bottom: 1rem;
}
:deep(.p-panel-title) {
    font-size: 0.75rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--muted-foreground);
}
</style>
