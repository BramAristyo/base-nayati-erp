<script setup lang="ts">
import { usePage, Link, router } from '@inertiajs/vue3';
import Avatar from 'primevue/avatar';
import Breadcrumb from 'primevue/breadcrumb';
import Divider from 'primevue/divider';
import Popover from 'primevue/popover';
import Button from 'primevue/button';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { ref, watch, computed, nextTick } from 'vue';
import { route } from 'ziggy-js';
import AppSidebar from '@/components/AppSidebar.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';

const authStore = useAuthStore();
const toast = useToast();
const page = usePage();
const isSidebarCollapsed = ref(false);
const profileMenu = ref();

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

const toggleProfileMenu = (event: any) => {
    profileMenu.value.toggle(event);
};

authStore.fetchUser();

const showToast = (flash: any) => {
    if (flash?.success) {
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: flash.success,
            life: 3000
        });
    }

    if (flash?.error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: flash.error,
            life: 3000
        });
    }
};

const breadcrumbs = computed(() => {
    const currentRoute = route().current();
    if (!currentRoute) return [];

    const items: any[] = [
        { label: 'Dashboard', url: route('dashboard') }
    ];

    if (currentRoute === 'dashboard') {
        return [{ label: 'Dashboard' }];
    }

    const parts = currentRoute.split('.');

    const labels: Record<string, string> = {
        'master': 'Master Data',
        'purchasing': 'Purchasing',
        'utility': 'System',
        'branches': 'Branches',
        'currencies': 'Currencies',
        'customers': 'Customers',
        'delivery-terms': 'Delivery Terms',
        'departments': 'Departments',
        'employees': 'Employees',
        'suppliers': 'Suppliers',
        'purchase-requests': 'Purchase Request',
        'purchase-orders': 'Purchase Order',
        'receivings': 'Receiving',
        'landed-costs': 'Landed Cost',
        'users': 'User Management',
        'roles': 'Roles & Permissions',
        'audit-trails': 'Monitoring',
        'index': 'List',
        'paginate': 'List',
        'show': 'Details',
        'create': 'Create',
        'edit': 'Edit',
        'settings': 'Settings',
        'change-password': 'Change Password'
    };

    parts.forEach((part, index) => {
        const isLast = index === parts.length - 1;
        const label = labels[part] || part.charAt(0).toUpperCase() + part.slice(1).replace(/-/g, ' ');

        if ((part === 'index' || part === 'paginate') && isLast) return;

        let url = null;

        if (!isLast && index > 0) {
            const prefix = parts.slice(0, index + 1).join('.');
            try {
                url = route(prefix + '.index');
            } catch (e) {
                try {
                    url = route(prefix + '.paginate');
                } catch (e2) {
                    url = null;
                }
            }
        }

        if (part !== 'dashboard') {
            items.push({ label, url: isLast ? null : url });
        }
    });

    return items;
});

watch(() => page.props.flash, async (newFlash: any) => {
    await nextTick();
    showToast(newFlash);
}, { deep: true, immediate: true });
</script>

<template>
    <div class="flex h-screen bg-background text-foreground overflow-hidden font-sans">
        <Toast />
        <AppSidebar v-model:collapsed="isSidebarCollapsed" v-if="authStore.isInitialized" />

        <div class="flex-1 flex flex-col min-w-0 bg-background">
            <header
                class="h-14 border-b border-border bg-background/80! backdrop-blur-md! flex items-center justify-between px-6 shrink-0 z-40 sticky top-0">
                <div class="flex items-center gap-4">
                    <Breadcrumb :model="breadcrumbs" class="bg-transparent! border-none! p-0!">
                        <template #item="{ item }">
                            <template v-if="item.url">
                                <Link :href="item.url" class="no-underline!">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground hover:text-primary transition-colors">{{
                                            item.label }}</span>
                                </Link>
                            </template>
                            <template v-else>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-foreground/40">{{
                                    item.label }}</span>
                            </template>
                        </template>
                        <template #separator>
                            <span class="text-[10px] text-muted-foreground/30 mx-2">/</span>
                        </template>
                    </Breadcrumb>
                </div>

                <div @click="toggleProfileMenu"
                    class="flex items-center gap-3 px-2 py-1 hover:bg-accent! rounded-lg! transition-colors! cursor-pointer!">
                    <div class="flex flex-col text-right hidden sm:flex">
                        <span class="text-sm font-bold text-foreground leading-tight">
                            {{ authStore.user?.name ?? 'Inox User' }}
                        </span>
                        <span class="text-[11px] text-muted-foreground font-medium tracking-tight">
                            {{ authStore.user?.email ?? 'admin@inox.co.id' }}
                        </span>
                    </div>
                    <Avatar :label="authStore.user?.name ? authStore.user.name.charAt(0).toUpperCase() : 'U'"
                        shape="circle"
                        class="bg-muted! text-foreground! text-xs! font-bold! w-9! h-9! border border-border shadow-sm!" />
                </div>

                <Popover ref="profileMenu" class="min-w-48">
                    <div class="flex flex-col">
                        <Link :href="route('user.settings')"
                            class="flex items-center gap-3 px-3 py-2 rounded-md hover:bg-accent transition-colors group no-underline!">
                            <i class="pi pi-user-edit text-sm text-muted-foreground group-hover:text-foreground"></i>
                            <span class="text-xs font-bold uppercase tracking-wider text-foreground">Setting</span>
                        </Link>
                        <Divider class="my-1!" />
                        <Link :href="route('logout')" method="post" as="button"
                            class="w-full flex items-center gap-3 px-3 py-2 rounded-md hover:bg-destructive/10 transition-colors group">
                            <i class="pi pi-power-off text-sm text-destructive"></i>
                            <span class="text-xs font-bold uppercase tracking-wider text-destructive">Log out</span>
                        </Link>
                    </div>
                </Popover>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                <slot />
            </main>
        </div>
        <ConfirmDialog />
    </div>
</template>
