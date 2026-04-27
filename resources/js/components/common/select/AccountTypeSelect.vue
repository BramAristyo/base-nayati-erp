<script setup lang="ts">
import Select from 'primevue/select';
import { ref, onMounted } from 'vue';
import { route } from 'ziggy-js';
import http from '@/lib/http';
import type { AccountType, AccountTypeResponse } from '@/types/utility/account-type.types';

const props = defineProps<{
    modelValue: string | null;
    placeholder?: string;
    disabled?: boolean;
    invalid?: boolean;
}>();

const emit = defineEmits(['update:modelValue', 'change']);

const accountTypes = ref<AccountType[]>([]);
const loading = ref(false);

const fetchAccountTypes = async () => {
    loading.value = true;
    try {
        const response = await http.get<AccountTypeResponse>(route('api.utility.account-types.index'));
        accountTypes.value = response.data;
    } catch (error) {
        console.error('Failed to fetch account types', error);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchAccountTypes();
});
</script>

<template>
    <Select
        :modelValue="props.modelValue"
        @update:modelValue="emit('update:modelValue', $event)"
        @change="emit('change', $event)"
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
