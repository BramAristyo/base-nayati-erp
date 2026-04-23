<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import RoleForm from '@/components/Roles/RoleForm.vue';
import type { ShowRole, UpdateRoleRequest } from '@/types/utility/role-permissions.types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    role: ShowRole;
}>();

const form = useForm<UpdateRoleRequest>({
    id: props.role.id,
    name: props.role.name,
    slug: props.role.slug,
    description: props.role.description || '',
    permission_ids: props.role.permissions.map(p => p.id)
});

const submit = () => {
    form.clearErrors();
    
    if (form.permission_ids?.length === 0) {
        form.setError('permission_ids', 'You must select at least one action for this role.');
        return;
    }

    form.post(route('utility.roles.update', { id: props.role.id }));
};
</script>

<template>
    <Head :title="`Role: ${role.name}`" />

    <AppLayout>
        <div class="space-y-10">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold text-black uppercase tracking-tight">Role Details</h1>
                <p class="text-xs text-gray-500 font-medium italic">Viewing and managing permissions for the <span class="text-black font-bold">{{ role.name }}</span> role.</p>
            </div>

            <RoleForm :form="form" :is-edit="true" @submit="submit" />
        </div>
    </AppLayout>
</template>
