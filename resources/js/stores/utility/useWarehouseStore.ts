import { defineStore } from 'pinia';
import { ref } from 'vue';
import http from '@/lib/http';
import { route } from 'ziggy-js';
import type { Warehouse } from '@/types/utility/warehouse.types';
import { useToast } from 'primevue/usetoast';

export const useWarehouseStore = defineStore('warehouse', () => {
    const toast = useToast();
    const warehouses = ref<Warehouse[]>([]);
    const isFetching = ref<boolean>(false);

    const fetchAll = async () => {
        isFetching.value = true;
        try {
            const response: any = await http.get(route('api.utility.warehouses.all'));
            if (response.status) {
                warehouses.value = response.data;
            }
        } catch (error: any) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.message || 'Failed to fetch warehouses',
                life: 3000
            });
        } finally {
            isFetching.value = false;
        }
    };

    return {
        warehouses,
        isFetching,
        fetchAll
    };
});
