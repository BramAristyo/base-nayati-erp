<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import RoleForm from '@/components/Roles/RoleForm.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { StoreRoleRequest } from '@/types/utility/role-permissions.types';

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
            <AppPageHeader
                title="Create New Role"
                description="Define a new system role and assign specific access permissions."
            />

            <RoleForm :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
