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
        label: 'System',
        icon: 'pi pi-cog',
        isOpen: false,
        items: [
            { label: 'User Management', route: 'utility.users.paginate', permission: 'utility.user.view' },
            { label: 'Role & Permissions', route: 'utility.roles.paginate', permission: 'utility.role.view' },
        ]
    }
]);

const isRouteActive = (routeName?: string) => {
    if (!routeName) {
        return false;
    }

    const current = route().current();
    if (!current) return false;

    if (current === routeName) return true;

    if (routeName.endsWith('.paginate')) {
        const base = routeName.replace('.paginate', '');
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
    if (!targetMenu) return;

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
        class="border-r border-gray-200 bg-gray-50/80 flex flex-col h-screen sticky top-0 shrink-0 transition-all duration-300 ease-in-out z-50"
        :class="[collapsed ? 'w-16' : 'w-64']">
        <div class="p-4 flex flex-col h-full overflow-hidden">
            <div class="flex items-center gap-3 mb-5 h-10 overflow-hidden whitespace-nowrap px-1"
                :class="[collapsed ? 'justify-center' : '']">
                <img src="/images/logo_ima.png" alt="Inox Logo"
                    class="w-auto object-contain shrink-0 transition-all duration-300"
                    :class="[collapsed ? 'h-9' : 'h-7']" />
                <div v-if="!collapsed" class="flex flex-col min-w-0 transition-opacity duration-300">
                    <span class="font-bold text-black leading-none text-sm tracking-tight">Inox ERP</span>
                    <span class="text-[10px] text-gray-700 font-bold uppercase tracking-widest mt-0.5">PT Inox Metal
                        Asia</span>
                </div>
            </div>

            <div v-if="!collapsed" class="mb-5 transition-opacity duration-300">
                <IconField>
                    <InputText v-model="searchQuery" placeholder="Search Menu..." size="small"
                        class="w-full! py-2! text-sm! bg-white border-gray-300! text-gray-900! rounded-md! focus:ring-1! focus:ring-gray-300! transition-all shadow-sm placeholder:text-gray-400!" />
                </IconField>
            </div>

            <nav class="flex-1 space-y-0.5 overflow-y-auto no-scrollbar">
                <div v-for="menu in filteredMenus" :key="menu.label" class="space-y-0.5">
                    <template v-if="!menu.items">
                        <Link v-tooltip.right="collapsed ? menu.label : ''"
                            :href="menu.route ? route(menu.route) : '#'"
                            class="flex items-center gap-3 px-2.5 py-2 rounded-md transition-all group relative border border-transparent"
                            :class="[
                                isRouteActive(menu.route) ? 'bg-white shadow-sm border-gray-100 text-black font-bold' : 'text-gray-800 hover:text-black hover:bg-gray-100/50',
                                collapsed ? 'justify-center' : ''
                            ]">
                            <i
                                :class="[menu.icon, 'text-base', isRouteActive(menu.route) ? 'text-black' : 'text-gray-600 group-hover:text-black']"></i>
                            <span v-if="!collapsed"
                                class="text-sm font-semibold tracking-wide whitespace-nowrap">{{ menu.label }}</span>
                        </Link>
                    </template>

                    <template v-else>
                        <button v-tooltip.right="collapsed ? menu.label : ''" @click="toggleSubMenu(menu)"
                            class="w-full flex items-center gap-3 px-2.5 py-2 rounded-md transition-all group text-gray-800 hover:text-black hover:bg-gray-100/50"
                            :class="[collapsed ? 'justify-center' : 'justify-between']">
                            <div class="flex items-center gap-3">
                                <i :class="[menu.icon, 'text-base text-gray-600 group-hover:text-black']"></i>
                                <span v-if="!collapsed"
                                    class="text-sm font-semibold tracking-wide whitespace-nowrap">{{ menu.label
                                    }}</span>
                            </div>
                            <i v-if="!collapsed"
                                class="pi pi-chevron-down text-[10px] text-gray-600 transition-transform duration-200"
                                :class="{ 'rotate-180': menu.isOpen }"></i>
                        </button>

                        <div v-show="!collapsed && menu.isOpen"
                            class="ml-4 border-l border-gray-200 pl-3 space-y-0.5 my-0.5">
                            <Link v-for="child in menu.items" :key="child.label"
                                :href="child.route ? route(child.route) : '#'"
                                class="block px-2 py-1.5 rounded-md transition-all group"
                                :class="isRouteActive(child.route) ? 'text-black font-bold bg-white/50 shadow-sm' : 'text-gray-700 hover:text-black font-medium'">
                                <span class="text-sm whitespace-nowrap">{{ child.label }}</span>
                            </Link>
                        </div>
                    </template>
                </div>
            </nav>

            <div class="pt-4 border-t border-gray-200 mt-auto space-y-0.5">
                <Link v-tooltip.right="collapsed ? 'Settings' : ''" :href="route('user.settings')"
                    class="flex items-center gap-3 px-2.5 py-2 rounded-md transition-all group text-gray-800 hover:text-black hover:bg-gray-100/50"
                    :class="[
                        isRouteActive('user.settings') ? 'bg-white shadow-sm border-gray-100 text-black font-bold' : '',
                        collapsed ? 'justify-center' : ''
                    ]">
                    <i
                        :class="['pi pi-cog text-base', isRouteActive('user.settings') ? 'text-black' : 'text-gray-600 group-hover:text-black']"></i>
                    <span v-if="!collapsed" class="text-sm font-semibold tracking-wide">Settings</span>
                </Link>
                <Link v-tooltip.right="collapsed ? 'Log out' : ''" :href="route('logout')" method="post"
                    as="button"
                    class="w-full flex items-center gap-3 px-2.5 py-2 rounded-md transition-all group text-red-600 hover:text-red-700 hover:bg-red-50"
                    :class="[collapsed ? 'justify-center' : '']">
                    <i class="pi pi-power-off text-base"></i>
                    <span v-if="!collapsed" class="text-sm tracking-wide font-bold">Log out</span>
                </Link>
            </div>
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
