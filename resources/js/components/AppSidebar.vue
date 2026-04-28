<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import IconField from 'primevue/iconfield';
import InputText from 'primevue/inputtext';
import { ref, computed, watch, onMounted } from 'vue';
import { route } from 'ziggy-js';
import { useAuthStore } from '@/stores/utility/useAuthStore';

const collapsed = defineModel<boolean>('collapsed');

interface MenuItem {
    label: string;
    icon: string;
    route?: string;
    permission?: string;
    items?: ChildMenu[];
    isOpen?: boolean;
}

interface ChildMenu {
    label: string;
    route?: string;
    permission?: string;
}

const searchQuery = ref('');
const page = usePage();
const authStore = useAuthStore();
const { permissions: userPermissions } = storeToRefs(authStore);

const menus = ref<MenuItem[]>([
    { label: 'Dashboard', icon: 'pi pi-objects-column', route: 'dashboard' },
    {
        label: 'Master',
        icon: 'pi pi-database',
        isOpen: false,
        items: [
            { label: 'Branches', route: 'master.branches.index', permission: 'master.branch.view' },
            { label: 'Currencies', route: 'master.currencies.index', permission: 'master.currency.view' },
            { label: 'Customers', route: 'master.customers.index', permission: 'master.customer.view' },
            { label: 'Delivery Terms', route: 'master.delivery-terms.index', permission: 'master.delivery-term.view' },
            { label: 'Departments', route: 'master.departments.index', permission: 'master.department.view' },
            { label: 'Employees', route: 'master.employees.index', permission: 'master.employee.view' },
            { label: 'Suppliers', route: 'master.suppliers.index', permission: 'master.supplier.view' },
        ]
    },
    {
        label: 'Purchasing',
        icon: 'pi pi-shopping-cart',
        isOpen: false,
        items: [
            { label: 'Purchase Request', route: 'purchasing.purchase-requests.index', permission: 'purchasing.purchase-request.view' },
            { label: 'Purchase Order', route: 'purchasing.purchase-orders.index', permission: 'purchasing.purchase-order.view' },
            { label: 'Receiving', route: 'purchasing.receivings.index', permission: 'purchasing.receiving.view' },
            { label: 'Landed Cost', route: 'purchasing.landed-costs.index', permission: 'purchasing.landed-cost.view' },
        ]
    },
    {
        label: 'Sales',
        icon: 'pi pi-chart-line',
        isOpen: false,
        items: [
            { label: 'Sales Order', route: 'sales.orders.index', permission: 'sales.order.view' },
            { label: 'Proforma', route: 'sales.proformas.index', permission: 'sales.proforma.view' },
            { label: 'Delivery Order', route: 'sales.delivery-orders.index', permission: 'sales.delivery-order.view' },
            { label: 'Shipment', route: 'sales.shipments.index', permission: 'sales.shipment.view' },
            { label: 'Invoice', route: 'sales.invoices.index', permission: 'sales.invoice.view' },
        ]
        },
    {
        label: 'Approval',
        icon: 'pi pi-check-circle',
        isOpen: false,
        permission: 'approval.approval.view',
        route: 'approval.index',
    },
    {
        label: 'System',
        icon: 'pi pi-cog',
        isOpen: false,
        items: [
            { label: 'User Management', route: 'utility.users.paginate', permission: 'utility.user.view' },
            { label: 'Role & Permissions', route: 'utility.roles.paginate', permission: 'utility.role.view' },
            { label: 'Monitoring', route: 'utility.audit-trails.paginate', permission: 'utility.audit-trail.view' },
        ]
    }
]);

const isRouteActive = (routeName?: string) => {
    if (!routeName) {
        return false;
    }

    const current = route().current();

    if (!current) {
        return false;
    }

    if (current === routeName) {
        return true;
    }

    if (routeName.endsWith('.paginate') || routeName.endsWith('.index')) {
        const base = routeName.replace(/\.(paginate|index)$/, '');

        return current.startsWith(base);
    }

    return current.startsWith(routeName);
};

const syncExpandedState = () => {
    menus.value.forEach(menu => {
        if (menu.items) {
            const hasActiveChild = menu.items.some(child => isRouteActive(child.route));

            if (hasActiveChild) {
                menu.isOpen = true;
            }
        }
    });
};

const toggleSubMenu = (clickedMenu: MenuItem) => {
    if (collapsed.value) {
        collapsed.value = false;
    }

    const targetMenu = menus.value.find(m => m.label === clickedMenu.label);

    if (!targetMenu) {
        return;
    }

    const currentState = targetMenu.isOpen;

    menus.value.forEach(menu => {
        if (menu.items) {
            menu.isOpen = false;
        }
    });

    targetMenu.isOpen = !currentState;
};

const filteredMenus = computed(() => {
    const permittedMenus = menus.value.reduce((acc: MenuItem[], menu) => {
        const hasParentPermission = !menu.permission || (userPermissions.value && userPermissions.value.includes(menu.permission));

        if (menu.items) {
            const permittedChildren = menu.items.filter(child =>
                !child.permission || (userPermissions.value && userPermissions.value.includes(child.permission))
            );

            if (permittedChildren.length > 0) {
                acc.push({
                    ...menu,
                    isOpen: !!menu.isOpen,
                    items: permittedChildren
                });
            }
        } else if (hasParentPermission) {
            acc.push({
                ...menu,
                items: undefined,
                isOpen: false
            });
        }

        return acc;
    }, []);

    if (!searchQuery.value) {
        return permittedMenus;
    }

    const query = searchQuery.value.toLowerCase();

    return permittedMenus.reduce((acc: MenuItem[], menu) => {
        const matchesLabel = menu.label.toLowerCase().includes(query);
        const filteredChildren = menu.items?.filter(child =>
            child.label.toLowerCase().includes(query)
        );

        if (matchesLabel || (filteredChildren && filteredChildren.length > 0)) {
            acc.push({
                ...menu,
                isOpen: true,
                items: matchesLabel ? menu.items : filteredChildren
            });
        }

        return acc;
    }, []);
});

watch(() => page.url, () => {
    syncExpandedState();
}, { immediate: true });

onMounted(() => {
    syncExpandedState();
});
</script>

<template>
    <aside
        class="border-r border-border bg-muted/80 flex flex-col h-screen sticky top-0 shrink-0 transition-all duration-300 ease-in-out z-50"
        :class="[collapsed ? 'w-20' : 'w-72']">
        <div class="p-4 flex flex-col h-full overflow-hidden">
            <div class="flex items-center gap-3 mb-5 h-10 overflow-hidden whitespace-nowrap px-1"
                :class="[collapsed ? 'justify-center' : '']">
                <img src="/images/logo_ima.png" alt="Inox Logo"
                    class="w-auto object-contain shrink-0 transition-all duration-300"
                    :class="[collapsed ? 'h-9' : 'h-7']" />
                <div v-if="!collapsed" class="flex flex-col min-w-0 transition-opacity duration-300">
                    <span class="font-bold text-foreground leading-none text-sm tracking-tight">Inox ERP</span>
                    <span class="text-[10px] text-muted-foreground font-bold uppercase tracking-widest mt-0.5">PT Inox
                        Metal
                        Asia</span>
                </div>
            </div>

            <div v-if="!collapsed" class="mb-5 transition-opacity duration-300">
                <IconField>
                    <InputText v-model="searchQuery" placeholder="Search Menu..." size="small"
                        class="w-full! py-2! text-sm! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm placeholder:text-muted-foreground!" />
                </IconField>
            </div>

            <nav class="flex-1 space-y-0.5 overflow-y-auto no-scrollbar">
                <div v-for="menu in filteredMenus" :key="menu.label" class="space-y-0.5">
                    <template v-if="!menu.items">
                        <Link v-tooltip.right="collapsed ? menu.label : ''" :href="menu.route ? route(menu.route) : '#'"
                            class="flex items-center gap-3 px-2.5 py-2 rounded-md transition-all group relative border border-transparent"
                            :class="[
                                isRouteActive(menu.route) ? 'bg-background shadow-sm border-border text-foreground font-bold pointer-events-none' : 'text-foreground hover:text-foreground hover:bg-accent/50',
                                collapsed ? 'justify-center' : ''
                            ]">
                            <i
                                :class="[menu.icon, 'text-base', isRouteActive(menu.route) ? 'text-foreground' : 'text-muted-foreground group-hover:text-foreground']"></i>
                            <span v-if="!collapsed" class="text-sm font-semibold tracking-wide whitespace-nowrap">{{
                                menu.label }}</span>
                        </Link>
                    </template>

                    <template v-else>
                        <button v-tooltip.right="collapsed ? menu.label : ''" @click="toggleSubMenu(menu)"
                            class="w-full flex items-center gap-3 px-2.5 py-2 rounded-md transition-all group text-foreground hover:text-foreground hover:bg-accent/50"
                            :class="[collapsed ? 'justify-center' : 'justify-between']">
                            <div class="flex items-center gap-3">
                                <i
                                    :class="[menu.icon, 'text-base text-muted-foreground group-hover:text-foreground']"></i>
                                <span v-if="!collapsed" class="text-sm font-semibold tracking-wide whitespace-nowrap">{{
                                    menu.label
                                }}</span>
                            </div>
                            <i v-if="!collapsed"
                                class="pi pi-chevron-down text-[10px] text-muted-foreground transition-transform duration-200"
                                :class="{ 'rotate-180': menu.isOpen }"></i>
                        </button>

                        <div v-show="!collapsed && menu.isOpen"
                            class="ml-4 border-l border-border pl-3 space-y-0.5 my-0.5">
                            <Link v-for="child in menu.items" :key="child.label"
                                :href="child.route ? route(child.route) : '#'"
                                class="block px-2 py-1.5 rounded-md transition-all group"
                                :class="isRouteActive(child.route) ? 'text-foreground font-bold bg-background/50 shadow-sm pointer-events-none' : 'text-muted-foreground hover:text-foreground font-medium'">
                                <span class="text-sm whitespace-nowrap">{{ child.label }}</span>
                            </Link>
                        </div>
                    </template>
                </div>
            </nav>
        </div>
    </aside>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
