<script setup lang="ts">
import Badge from 'primevue/badge';
import { computed } from 'vue';

const props = defineProps<{
    active: boolean;
}>();

const config = computed(() => {
    return props.active
        ? { severity: 'success', label: 'Active' }
        : { severity: 'danger', label: 'Inactive' };
});
</script>

<template>
    <Badge :severity="config.severity" :value="config.label" size="small" />
</template>