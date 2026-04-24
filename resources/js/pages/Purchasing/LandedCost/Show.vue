<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Divider from 'primevue/divider';
import Panel from 'primevue/panel';
import { route } from 'ziggy-js';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { LandedCost } from '@/types/purchasing/landed-cost.types';
import { formatDate } from '@/utils/date';

const props = defineProps<{
    landedCost: LandedCost;
}>();

const onBack = () => {
    router.get(route('purchasing.landed-costs.index'));
};

const formatCurrency = (value: number, currency: string = 'IDR') => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currency === 'IDR' ? 'IDR' : currency,
        minimumFractionDigits: 0
    }).format(value);
};
</script>

<template>
    <Head :title="'Landed Cost - ' + landedCost.landed_cost_number" />

    <AppLayout>
        <div class="flex flex-col gap-6">
            <!-- Header -->
            <div class="flex flex-col gap-4 md:flex-row md:items-center justify-between px-1">
                <AppPageHeader
                    :title="landedCost.landed_cost_number"
                    :description="'Calculated on ' + formatDate(landedCost.landed_cost_date)"
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
                    <Button
                        label="Edit"
                        icon="pi pi-pencil"
                        size="small"
                        severity="secondary"
                        class="bg-accent! text-foreground! border-border! rounded-md!"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Info Card -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <Panel header="General Information">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Receiving Number</span>
                                <span class="text-sm font-semibold text-foreground">{{ landedCost.receiving_number }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Purchase Order</span>
                                <span class="text-sm font-semibold text-foreground">{{ landedCost.purchase_order_number }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Supplier Code</span>
                                <span class="text-sm font-semibold text-foreground">{{ landedCost.supplier_code }}</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] font-bold uppercase text-muted-foreground">Supplier Invoice</span>
                                <span class="text-sm font-semibold text-foreground">{{ landedCost.supplier_invoice_number || '-' }}</span>
                            </div>
                        </div>
                    </Panel>

                    <Panel header="Extra Charges Details">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="flex flex-col gap-1 p-3 bg-muted/30 rounded-lg border border-border/50">
                                <span class="text-[9px] font-bold uppercase text-muted-foreground">Air Freight</span>
                                <span class="text-sm font-bold text-foreground">{{ formatCurrency(landedCost.air_freight_charge, landedCost.freight_currency_code || 'IDR') }}</span>
                            </div>
                            <div class="flex flex-col gap-1 p-3 bg-muted/30 rounded-lg border border-border/50">
                                <span class="text-[9px] font-bold uppercase text-muted-foreground">Sea Freight</span>
                                <span class="text-sm font-bold text-foreground">{{ formatCurrency(landedCost.sea_freight_charge, landedCost.freight_currency_code || 'IDR') }}</span>
                            </div>
                            <div class="flex flex-col gap-1 p-3 bg-muted/30 rounded-lg border border-border/50">
                                <span class="text-[9px] font-bold uppercase text-muted-foreground">Insurance</span>
                                <span class="text-sm font-bold text-foreground">{{ formatCurrency(landedCost.insurance_charge) }}</span>
                            </div>
                            <div class="flex flex-col gap-1 p-3 bg-muted/30 rounded-lg border border-border/50">
                                <span class="text-[9px] font-bold uppercase text-muted-foreground">Bea Masuk</span>
                                <span class="text-sm font-bold text-foreground">{{ formatCurrency(landedCost.bea_charge) }}</span>
                            </div>
                            <div class="flex flex-col gap-1 p-3 bg-muted/30 rounded-lg border border-border/50">
                                <span class="text-[9px] font-bold uppercase text-muted-foreground">Packing</span>
                                <span class="text-sm font-bold text-foreground">{{ formatCurrency(landedCost.packing_charge) }}</span>
                            </div>
                            <div class="flex flex-col gap-1 p-3 bg-muted/30 rounded-lg border border-border/50">
                                <span class="text-[9px] font-bold uppercase text-muted-foreground">Delivery</span>
                                <span class="text-sm font-bold text-foreground">{{ formatCurrency(landedCost.delivery_charge) }}</span>
                            </div>
                        </div>
                    </Panel>
                </div>

                <!-- Financial Summary Card -->
                <div class="flex flex-col gap-6">
                    <Panel header="Financial Summary" class="h-full">
                        <div class="flex flex-col gap-4">
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-xs font-medium text-muted-foreground">Exwork (Subtotal)</span>
                                <span class="text-sm font-bold text-foreground">{{ formatCurrency(landedCost.sub_total, landedCost.currency_code) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-xs font-medium text-muted-foreground">Discount</span>
                                <span class="text-sm font-semibold text-destructive">-{{ formatCurrency(landedCost.discount_amount_1, landedCost.currency_code) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-border/50">
                                <span class="text-xs font-medium text-muted-foreground">Currency Rate</span>
                                <span class="text-sm font-mono">{{ landedCost.currency_rate.toLocaleString() }}</span>
                            </div>
                            <div class="mt-4 p-4 bg-primary/5 rounded-xl border border-primary/20 flex flex-col gap-1 items-center justify-center">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-primary">Factor Cost</span>
                                <span class="text-3xl font-black text-primary">{{ landedCost.factor_cost }}</span>
                            </div>
                        </div>
                    </Panel>

                    <div class="flex flex-col gap-2 p-4 rounded-xl border border-border bg-card shadow-xs">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">Created By</span>
                            <span class="text-xs font-semibold">{{ landedCost.created_by }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-bold uppercase text-muted-foreground">Entry Time</span>
                            <span class="text-xs font-medium italic">{{ formatDate(landedCost.created_at) }}</span>
                        </div>
                    </div>
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
