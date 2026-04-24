<script setup lang="ts">
import Badge from 'primevue/badge';
import { computed } from 'vue';

const props = defineProps<{
    approved: boolean;
}>();

const config = computed(() => {
    return props.approved
        ? { severity: 'success', label: 'Approved' }
        : { severity: 'warn', label: 'Pending' };
});
</script>

<template>
    <Badge :severity="config.severity" :value="config.label" size="small" class="text-[10px] font-bold uppercase" />
</template>
