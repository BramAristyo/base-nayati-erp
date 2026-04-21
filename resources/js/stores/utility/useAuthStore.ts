import { defineStore } from 'pinia';
import { ref } from 'vue';
import http from '@/lib/http';
import { route } from 'ziggy-js'
import type { User } from '@/types/utility/user.types';

export const useAuthStore = defineStore('auth', () => {
    const user = ref<User | null>(null);
    const roles = ref<string[]>([]);
    const permissions = ref<string[]>([]);
    const isInitialized = ref<boolean>(false);

    const fetchUser = async () => {
        if (isInitialized.value) return;

        try {
            const response: any = await http.get(route('api.auth.me'));
            
            if (response.success) {
                user.value = response.data.user;
                roles.value = response.data.roles;
                permissions.value = response.data.permissions;
                isInitialized.value = true;
            }
        } catch (error) {
            user.value = null;
            roles.value = [];
            permissions.value = [];
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