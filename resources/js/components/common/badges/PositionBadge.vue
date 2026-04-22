<script setup lang="ts">
import Badge from 'primevue/badge';
import { computed } from 'vue';

const props = defineProps<{
    position: string;
}>();

const configMap: Record<string, { severity: string; label: string }> = {
    BO: { severity: 'info', label: 'Back Office' },
    FO: { severity: 'success', label: 'Front Office' },
};

const config = computed(() => configMap[props.position?.toUpperCase()] || { severity: 'secondary', label: props.position });
</script>

<template>
    <Badge :severity="config.severity" :value="config.label" size="small" />
</template>