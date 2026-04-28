<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import Panel from 'primevue/panel';
import { route } from 'ziggy-js';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { Proforma } from '@/types/sales/proforma.types';
import { formatDate } from '@/utils/date';
import { formatCurrency } from '@/utils/money';

const props = defineProps<{
    proforma: Proforma;
}>();

const onBack = () => {
    router.get(route('sales.proformas.index'));
};
</script>

<template>
    <Head :title="'Proforma - ' + proforma.proforma_number" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader
                    :title="proforma.proforma_number"
                    :description="'Created on ' + formatDate(proforma.date)"
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
                                <span class="text-sm font-semibold text-foreground">{{ proforma.customer_name }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Sales Order Number</span>
                                <span class="text-sm font-semibold text-foreground">{{ proforma.sales_order_number || '-' }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Branch Code</span>
                                <span class="text-sm font-semibold text-foreground">{{ proforma.branch_code }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Invoice Number (Kwitansi)</span>
                                <span class="text-sm font-semibold text-foreground">{{ proforma.invoice_number || '-' }}</span>
                            </div>
                        </div>
                    </Panel>

                    <Panel header="Proforma Items">
                        <div class="p-4 text-center text-muted-foreground italic text-xs">
                            Detailed items view is currently under development.
                        </div>
                    </Panel>
                </div>

                <div class="flex flex-col gap-6">
                    <Panel header="Financial Summary" class="h-full">
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-xs font-medium text-muted-foreground">Netto Total</span>
                                <span class="text-lg font-bold text-primary">{{ formatCurrency(proforma.netto) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-xs font-medium text-muted-foreground">Status</span>
                                <span :class="proforma.status ? 'text-green-600' : 'text-orange-600'" class="text-sm font-bold uppercase">
                                    {{ proforma.status ? 'Approved' : 'Pending' }}
                                </span>
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
