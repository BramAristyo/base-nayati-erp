<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

import AppLayout from '@/Layouts/AppLayout.vue';
import ApprovalMenu from '@/components/Approvals/ApprovalMenu.vue';
</script>

<template>
    <Head title="Approval" />

    <AppLayout>
        <div class="flex gap-6 h-[calc(100vh-8rem)]">

            <aside class="w-56 shrink-0 flex flex-col">
                <ApprovalMenu />
            </aside>

            <div class="flex-1 flex flex-col items-center justify-center min-w-0 border border-border rounded-xl bg-card/50">
                <div class="flex flex-col items-center gap-4 text-center px-8">
                    <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center">
                        <i class="pi pi-arrow-left text-2xl text-muted-foreground/50"></i>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <span class="text-sm font-bold text-foreground tracking-tight">
                            Select a Module
                        </span>
                        <span class="text-xs text-muted-foreground leading-relaxed max-w-xs">
                            Choose an approval module from the menu to review and authorize pending transactions.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
