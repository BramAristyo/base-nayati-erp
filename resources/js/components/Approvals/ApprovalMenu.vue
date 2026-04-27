<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';
import { useAuthStore } from '@/stores/utility/useAuthStore';

const props = defineProps<{
    active?: string;
}>();

const authStore = useAuthStore();

interface ApprovalMenuItem {
    key: string;
    label: string;
    icon: string;
    route: string;
    permission: string;
}

interface ApprovalMenuGroup {
    label: string;
    items: ApprovalMenuItem[];
}

const menus: ApprovalMenuGroup[] = [
    {
        label: 'Purchasing',
        items: [
            { key: 'purchase-request', label: 'Purchase Request', icon: 'pi pi-file-edit', route: 'approval.purchasing.purchase-requests.pending', permission: 'approval.purchase-request.view' },
            { key: 'purchase-order', label: 'Purchase Order', icon: 'pi pi-file-check', route: 'approval.purchasing.purchase-orders.pending', permission: 'approval.purchase-order.view' },
            { key: 'receiving', label: 'Receiving', icon: 'pi pi-truck', route: 'dashboard', permission: 'approval.receiving.view' },
        ],
    },
];

const filteredMenus = computed(() =>
    menus
        .map(group => ({
            ...group,
            items: group.items.filter(item => authStore.hasPermission(item.permission)),
        }))
        .filter(group => group.items.length > 0)
);
</script>

<template>
    <nav class="border border-border rounded-lg bg-card p-1.5 space-y-0.5">
        <div v-for="group in filteredMenus" :key="group.label">
            <span class="block px-3 pt-2 pb-1 text-[10px] font-medium uppercase tracking-widest text-muted-foreground">
                {{ group.label }}
            </span>
            <Link v-for="item in group.items" :key="item.key" :href="route(item.route)"
                class="flex items-center gap-2 px-3 py-1.5 rounded-md text-xs transition-colors" :class="active === item.key
                    ? 'bg-accent text-foreground font-medium'
                    : 'text-muted-foreground hover:text-foreground hover:bg-accent/40'
                    ">
                <i :class="[item.icon, 'text-[11px]']"></i>
                {{ item.label }}
            </Link>
        </div>
    </nav>
</template>
