<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import AppSidebar from '@/components/AppSidebar.vue';
import Avatar from 'primevue/avatar';
import Button from 'primevue/button';

const authStore = useAuthStore();
const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

onMounted(() => {
    authStore.fetchUser();
});
</script>

<template>
    <div class="flex h-screen bg-white text-gray-950 overflow-hidden font-sans">
        <AppSidebar :collapsed="isSidebarCollapsed" />

        <div class="flex-1 flex flex-col min-w-0 bg-white">
            <header class="h-14 border-b border-gray-200 bg-white flex items-center justify-between px-6 shrink-0 z-40">
                <div class="flex items-center gap-4">
                    <Button icon="pi pi-bars" text plain
                        class="!p-0 !w-8 !h-8 !rounded-md hover:!bg-gray-50 !text-gray-600" @click="toggleSidebar" />
                    <div class="h-4 w-[1px] bg-gray-200 hidden sm:block"></div>
                    <h1 class="text-[10px] font-bold text-gray-800 uppercase tracking-[0.2em] hidden sm:block">Inox
                        Management System</h1>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex flex-col text-right hidden sm:flex">
                        <span class="text-xs font-bold text-black ">
                            {{ authStore.user?.name ?? 'Inox User' }}
                        </span>
                        <span class="text-[10px] text-gray-600 font-medium  max-w-[180px]">
                            {{ authStore.user?.email ?? 'admin@inox.co.id' }}
                        </span>
                    </div>
                    <Avatar :label="authStore.user?.name ? authStore.user.name.charAt(0).toUpperCase() : 'U'"
                        shape="circle"
                        class="!bg-gray-100 !text-gray-950 !text-[10px] !font-bold !w-8 !h-8 border border-gray-200 shadow-sm" />
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <div class="p-4 lg:p-4">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style>
@reference "tailwindcss";

body {
    @apply antialiased bg-white text-gray-950;
}

::selection {
    background-color: #e5e7eb;
    color: #000000;
}

::-webkit-scrollbar {
    width: 5px;
}

::-webkit-scrollbar-track {
    @apply bg-transparent;
}

::-webkit-scrollbar-thumb {
    @apply bg-gray-300 rounded-full;
}

::-webkit-scrollbar-thumb:hover {
    @apply bg-gray-400;
}
</style>
