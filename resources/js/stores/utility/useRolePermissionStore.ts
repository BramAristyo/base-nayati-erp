import { defineStore } from 'pinia';
import { ref } from 'vue';
import http from '@/lib/http';
import type { Role, Permission } from '@/types/utility/role-permissions.types';
import { route } from 'ziggy-js';
import { useToast } from 'primevue/usetoast';

export const useRolePermissionStore = defineStore('rolePermission', () => {
    const toast = useToast();
    const roles = ref<Role[]>([]);
    const permissions = ref<Permission[]>([]);
    const isFetchingRoles = ref<boolean>(false);
    const isFetchingPermissions = ref<boolean>(false);

    const fetchAllRoles = async () => {
        isFetchingRoles.value = true;
        try {
            const response: any = await http.get(route('api.utility.roles.all'));
            if (response.status) {
                roles.value = response.data;
            }
        } catch (error: any) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.message || 'Failed to fetch roles',
                life: 3000
            });
        } finally {
            isFetchingRoles.value = false;
        }
    };

    return {
        roles,
        permissions,
        isFetchingRoles,
        isFetchingPermissions,
        fetchAllRoles
    };
});
