<script setup lang="ts">
import Button from 'primevue/button';

defineProps<{
    loading?: boolean;
}>();

defineEmits<{
    (e: 'click'): void;
}>();
</script>

<template>
    <Button
        label="Approve All"
        icon="pi pi-check"
        size="small"
        severity="success"
        class="!font-bold !uppercase !text-[10px] !tracking-wider"
        :loading="loading"
        @click="$emit('click')"
    />
</template>
