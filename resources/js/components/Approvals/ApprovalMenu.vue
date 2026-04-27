<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { useAuthStore } from '@/stores/utility/useAuthStore';

import Menu from 'primevue/menu';
import type { MenuItem } from 'primevue/menuitem';

const authStore = useAuthStore();

const currentRoute = computed(() => route().current() || '');

const isActive = (routeName: string) => currentRoute.value.startsWith(routeName.replace(/\.(pending|processed|index)$/, ''));

const menuItems = computed<MenuItem[]>(() => {
    const groups: MenuItem[] = [];

    const purchasingItems: MenuItem[] = [];
    if (authStore.hasPermission('approval.purchase-request.view')) {
        purchasingItems.push({
            label: 'Purchase Request',
            icon: 'pi pi-file-edit',
            class: isActive('approval.purchasing.purchase-requests') ? 'approval-menu-active' : '',
            command: () => router.visit(route('approval.purchasing.purchase-requests.pending')),
        });
    }
    if (authStore.hasPermission('approval.purchase-order.view')) {
        purchasingItems.push({
            label: 'Purchase Order',
            icon: 'pi pi-file-check',
            class: isActive('approval.purchasing.purchase-orders') ? 'approval-menu-active' : '',
            command: () => router.visit(route('approval.purchasing.purchase-orders.pending')),
        });
    }
    if (authStore.hasPermission('approval.receiving.view')) {
        purchasingItems.push({
            label: 'Receiving',
            icon: 'pi pi-truck',
            class: isActive('approval.purchasing.receivings') ? 'approval-menu-active' : '',
            command: () => router.visit(route('approval.purchasing.receivings.pending')),
        });
    }

    if (purchasingItems.length > 0) {
        groups.push({
            label: 'Purchasing',
            items: purchasingItems,
        });
    }

    return groups;
});

const hasAnyApproval = computed(() => menuItems.value.length > 0);
</script>

<template>
    <Menu v-if="hasAnyApproval" :model="menuItems" class="approval-side-menu" />

    <div v-else
        class="flex flex-col items-center justify-center py-12 gap-3 border border-border rounded-xl bg-card">
        <i class="pi pi-lock text-2xl text-muted-foreground/40"></i>
        <span class="text-[11px] font-medium text-muted-foreground">
            No approval modules available.
        </span>
    </div>
</template>

<style scoped>
:deep(.approval-side-menu) {
    border: 1px solid var(--border) !important;
    border-radius: 0.75rem !important;
    background: var(--card) !important;
    padding: 0.5rem !important;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
    width: 100% !important;
}

:deep(.approval-side-menu .p-menu-submenu-label) {
    font-size: 10px !important;
    font-weight: 900 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.15em !important;
    color: var(--muted-foreground) !important;
    padding: 0.75rem 0.75rem 0.5rem !important;
}

:deep(.approval-side-menu .p-menu-item-content) {
    border-radius: 0.375rem !important;
    transition: all 0.15s ease !important;
}

:deep(.approval-side-menu .p-menu-item-link) {
    padding: 0.5rem 0.75rem !important;
    gap: 0.625rem !important;
}

:deep(.approval-side-menu .p-menu-item-icon) {
    font-size: 12px !important;
    color: var(--muted-foreground) !important;
}

:deep(.approval-side-menu .p-menu-item-label) {
    font-size: 11px !important;
    font-weight: 700 !important;
    letter-spacing: 0.025em !important;
}

:deep(.approval-side-menu .approval-menu-active .p-menu-item-content) {
    background-color: var(--primary) !important;
}

:deep(.approval-side-menu .approval-menu-active .p-menu-item-label) {
    color: var(--primary-foreground) !important;
}

:deep(.approval-side-menu .approval-menu-active .p-menu-item-icon) {
    color: var(--primary-foreground) !important;
}

:deep(.approval-side-menu .p-menu-item.p-disabled .p-menu-item-content) {
    opacity: 0.4 !important;
}
</style>
