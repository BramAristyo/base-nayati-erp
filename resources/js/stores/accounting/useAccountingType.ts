import { defineStore } from 'pinia';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import http from '@/lib/http';
import type { AccountType, AccountTypeResponse } from '@/types/utility/account-type.types';

export const useAccountingTypeStore = defineStore('accounting-type', () => {
    const accountTypes = ref<AccountType[]>([]);
    const loading = ref(false);

    const fetchAccountTypes = async (force: boolean = false) => {
        if (!force && accountTypes.value.length > 0) return;

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

    const getNameByCode = (code: string | null) => {
        if (!code) return null;
        const type = accountTypes.value.find((t) => t.code === code);
        return type ? type.name : null;
    };

    return {
        accountTypes,
        loading,
        fetchAccountTypes,
        getNameByCode,
    };
});
