import { defineStore } from 'pinia';
import { ref } from 'vue';
import http from '@/lib/http';
import type { User } from '@/types/utility/user.types';
import { useToast } from 'primevue/usetoast';

export const useAuthStore = defineStore('auth', () => {
    const toast = useToast();
    const user = ref<User | null>(null);
    const roles = ref<string[]>([]);
    const permissions = ref<string[]>([]);
    const isInitialized = ref<boolean>(false);

    const fetchUser = async () => {
        if (isInitialized.value) { return; }

        try {
            const response: any = await http.get('/api/me');

            if (response.status) {
                user.value = response.data.user;
                roles.value = response.data.roles;
                permissions.value = response.data.permissions;
                isInitialized.value = true;
            } else {
                toast.add({ 
                    severity: 'error', 
                    summary: 'Error', 
                    detail: response.message || 'Failed to fetch user data', 
                    life: 3000 
                });
            }
        } catch (error: any) {
            user.value = null;
            roles.value = [];
            permissions.value = [];
            isInitialized.value = true;

            if (error.status !== 401 && error.status !== 419) {
                toast.add({ 
                    severity: 'error', 
                    summary: 'Error', 
                    detail: error.message || 'An unexpected error occurred', 
                    life: 3000 
                });
            }
        }
    };

    const hasRole = (roleName: string): boolean => {
        return roles.value.includes(roleName);
    };

    const hasPermission = (permissionName: string): boolean => {
        return permissions.value.includes(permissionName);
    };

    return {
        user,
        roles,
        permissions,
        isInitialized,
        fetchUser,
        hasRole,
        hasPermission
    };
});
