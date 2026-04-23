<script setup lang="ts">
interface Props {
    title: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <div class="flex flex-col gap-0.5">
        <h1 class="text-xl font-bold uppercase tracking-tight text-black">{{ title }}</h1>
        <p v-if="description" class="text-xs font-medium italic text-gray-500">
            {{ description }}
        </p>
    </div>
</template>
