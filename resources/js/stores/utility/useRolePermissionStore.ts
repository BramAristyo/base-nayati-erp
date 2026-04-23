import { defineStore } from 'pinia';
import { useToast } from 'primevue/usetoast';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import http from '@/lib/http';
import type { Role, Permission } from '@/types/utility/role-permissions.types';

export const useRolePermissionStore = defineStore('rolePermission', () => {
    const toast = useToast();
    const roles = ref<Role[]>([]);
    const permissions = ref<Permission[]>([]);
    const isFetchingRoles = ref<boolean>(false);
    const isFetchingPermissions = ref<boolean>(false);

    const fetchAllRoles = async (force: boolean = false) => {
        if (!force && roles.value.length > 0) {
return;
}

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

    const fetchAllPermissions = async (force: boolean = false) => {
        if (!force && permissions.value.length > 0) {
return;
}

        isFetchingPermissions.value = true;

        try {
            const response: any = await http.get(route('api.utility.permissions.all'));

            if (response.status) {
                permissions.value = response.data;
            }
        } catch (error: any) {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: error.message || 'Failed to fetch permissions',
                life: 3000
            });
        } finally {
            isFetchingPermissions.value = false;
        }
    };

    return {
        roles,
        permissions,
        isFetchingRoles,
        isFetchingPermissions,
        fetchAllRoles,
        fetchAllPermissions
    };
});
