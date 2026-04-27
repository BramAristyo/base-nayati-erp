<script setup lang="ts">
import Tabs from 'primevue/tabs';
import TabList from 'primevue/tablist';
import Tab from 'primevue/tab';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';

const props = defineProps<{
    activeTab: 'PR' | 'PO' | 'RCV'
}>();

const activeValue = ref(props.activeTab);

const onTabChange = (value: any) => {
    if (value === 'PR') {
        router.visit(route('approval.purchasing.purchase-requests.index'));
    } else {
        router.visit(route('dashboard'));
    }
};

watch(() => props.activeTab, (newVal) => {
    activeValue.value = newVal;
});
</script>

<template>
    <div class="purchasing-approval-tabs">
        <Tabs v-model:value="activeValue" @update:value="onTabChange">
            <TabList class="tab-list-custom">
                <Tab value="PR" class="tab-item">Purchase Request</Tab>
                <Tab value="PO" class="tab-item inactive">Purchase Order</Tab>
                <Tab value="RCV" class="tab-item inactive">Receiving</Tab>
            </TabList>
        </Tabs>
    </div>
</template>

<style scoped>
.tab-list-custom {
    border-style: none !important;
    background-color: transparent !important;
}

.tab-item {
    font-size: 10px !important;
    font-weight: 900 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
}

.inactive {
    opacity: 0.5 !important;
}
</style>
