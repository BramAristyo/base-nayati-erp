<script setup lang="ts">
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { ref, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppSidebar from '@/components/AppSidebar.vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';

const authStore = useAuthStore();
const toast = useToast();
const page = usePage();
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

onMounted(() => {
    authStore.fetchUser();
});

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
        <AppSidebar v-model:collapsed="isSidebarCollapsed" />

        <div class="flex-1 flex flex-col min-w-0 bg-white">
            <header class="h-18 border-b border-gray-200 bg-white flex items-center justify-between px-6 shrink-0 z-40">
                <div class="flex items-center gap-4">
                    <Button icon="pi pi-bars" text plain
                        class="p-0! w-8! h-8! rounded-md! hover:bg-gray-50! text-gray-600!" @click="toggleSidebar" />
                    <div class="h-4 w-px bg-gray-200 hidden sm:block"></div>
                    <h1 class="text-[10px] font-bold text-gray-800 uppercase tracking-[0.2em] hidden sm:block">Inox
                        Management System</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex-col text-right hidden sm:flex">
                        <span class="text-sm font-bold text-black ">
                            {{ authStore.user?.name ?? 'Inox User' }}
                        </span>
                        <span class="text-sm text-gray-600 font-medium  max-w-[180px]">
                            {{ authStore.user?.email ?? 'admin@inox.co.id' }}
                        </span>
                    </div>
                    <Avatar :label="authStore.user?.name ? authStore.user.name.charAt(0).toUpperCase() : 'U'"
                        shape="circle"
                        class="bg-gray-100! text-gray-950! text-[10px]! font-bold! w-10! h-10! border border-gray-200 shadow-sm" />
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <div class="p-2 lg:p-2">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style>
/* body {
    @apply antialiased bg-white text-gray-950;
} */

::selection {
    background-color: #e5e7eb;
    color: #000000;
}

::-webkit-scrollbar {
    width: 5px;
}

/* ::-webkit-scrollbar-track {
    @apply bg-transparent;
}

::-webkit-scrollbar-thumb {
    @apply bg-gray-300 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400;
} */
</style>
