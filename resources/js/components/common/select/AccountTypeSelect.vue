<script setup lang="ts">
import Select from 'primevue/select';
import { onMounted } from 'vue';
import { useAccountingTypeStore } from '@/stores/accounting/useAccountingType';
import { storeToRefs } from 'pinia';

const props = defineProps<{
    modelValue: string | null;
    placeholder?: string;
    disabled?: boolean;
    invalid?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | null): void;
    (e: 'change', value: string | null): void;
}>();

const store = useAccountingTypeStore();
const { accountTypes, loading } = storeToRefs(store);

onMounted(() => {
    store.fetchAccountTypes();
});
</script>

<template>
    <Select
        :modelValue="props.modelValue"
        @update:modelValue="emit('update:modelValue', $event)"
        @change="emit('change', $event.value)"
        :options="accountTypes"
        optionLabel="name"
        optionValue="code"
        :placeholder="props.placeholder || 'Select Account Type'"
        :loading="loading"
        :disabled="props.disabled"
        :invalid="props.invalid"
        filter
        size="small"
        class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm"
    >
        <template #option="slotProps">
            <div class="flex items-center gap-2">
                <span class="font-bold text-primary">{{ slotProps.option.code }}</span>
                <span class="text-muted-foreground opacity-50">-</span>
                <span class="font-medium">{{ slotProps.option.name }}</span>
            </div>
        </template>
    </Select>
</template>
