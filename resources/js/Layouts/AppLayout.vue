<script setup lang="ts">
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import ConfirmDialog from 'primevue/confirmdialog';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { ref, onMounted, watch } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import AppSidebar from '@/components/AppSidebar.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import { route } from 'ziggy-js';

const authStore = useAuthStore();
const toast = useToast();
const page = usePage();
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

authStore.fetchUser();

watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 3000 });
    }
    if (flash?.error) {
        toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 3000 });
    }
}, { deep: true, immediate: true });
</script>

<template>
    <div class="flex h-screen bg-white text-gray-950 overflow-hidden font-sans">
        <Toast />
        <AppSidebar v-model:collapsed="isSidebarCollapsed" v-if="authStore.isInitialized" />

        <div class="flex-1 flex flex-col min-w-0 bg-white">
            <header
                class="h-14 border-b border-gray-100 bg-white/80! backdrop-blur-md! flex items-center justify-between px-6 shrink-0 z-40 sticky top-0">
                <div class="flex items-center gap-4">
                    <Button icon="pi pi-bars" text plain
                        class="p-0! w-8! h-8! rounded-md! hover:bg-gray-100! text-gray-500!" @click="toggleSidebar" />
                    <div class="h-4 w-px bg-gray-200 hidden sm:block"></div>
                    <h1 class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] hidden sm:block">Inox
                        Management System</h1>
                </div>

                <Link :href="route('user.settings')"
                    class="flex items-center gap-3 px-2 py-1 hover:bg-gray-50! rounded-lg! transition-colors! cursor-pointer!">
                    <div class="flex flex-col text-right hidden sm:flex">
                        <span class="text-sm font-bold text-gray-900 leading-tight">
                            {{ authStore.user?.name ?? 'Inox User' }}
                        </span>
                        <span class="text-[11px] text-gray-500 font-medium tracking-tight">
                            {{ authStore.user?.email ?? 'admin@inox.co.id' }}
                        </span>
                    </div>
                    <Avatar :label="authStore.user?.name ? authStore.user.name.charAt(0).toUpperCase() : 'U'"
                        shape="circle"
                        class="bg-gray-100! text-gray-900! text-xs! font-bold! w-9! h-9! border border-gray-200 shadow-sm!" />
                </Link>
            </header>

            <main class="flex-1 overflow-y-auto p-6 lg:p-10">
                <slot />
            </main>
        </div>
        <ConfirmDialog />
    </div>
</template>
