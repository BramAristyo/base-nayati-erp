<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import RoleForm from '@/components/Roles/RoleForm.vue';
import type { StoreRoleRequest } from '@/types/utility/role-permissions.types';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm<StoreRoleRequest>({
    name: '',
    slug: '',
    description: '',
    permission_ids: []
});

const submit = () => {
    form.clearErrors();

    if (form.permission_ids.length === 0) {
        form.setError('permission_ids', 'You must select at least one action to create a role.');
        return;
    }

    form.post(route('utility.roles.store'));
};
</script>

<template>

    <Head title="Create Role" />

    <AppLayout>
        <div class="space-y-10">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold text-black uppercase tracking-tight">Create New Role</h1>
                <p class="text-xs text-gray-500 font-medium italic">Define a new system role and assign specific access
                    permissions.</p>
            </div>

            <RoleForm :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
