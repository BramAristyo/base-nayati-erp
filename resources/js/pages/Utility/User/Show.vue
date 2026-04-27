<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import UserForm from '@/components/Users/UserForm.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { ShowUser, UpdateUserRequest } from '@/types/utility/user.types';
import type { Branch } from '@/types/master/master.types';

const props = defineProps<{
    user: ShowUser;
    groupedPermissions: any;
    branches: Branch[];
}>();

const form = useForm<UpdateUserRequest>({
    id: props.user.id,
    name: props.user.name,
    email: props.user.email,
    approver_name: props.user.approver_name,
    approver_title: props.user.approver_title,
    branch_code: props.user.branch_code,
    position: props.user.position,
    roles: props.user.roles?.map(r => r.slug) || [],
    warehouses: props.user.warehouses?.map(w => w.id) || [],
    permissions: props.user.permissions?.map(p => ({
        permission_id: p.id,
        is_denied: p.pivot.is_denied
    })) || [],
    is_active: props.user.is_active,
    password: '',
    password_confirmation: ''
});

const submit = () => {
    form.post(route('utility.users.update', { id: props.user.id }));
};
</script>

<template>
    <Head :title="`User: ${user.name}`" />

    <AppLayout>
        <div class="space-y-10">
            <AppPageHeader title="Edit User" description="Update user credentials, permissions, and account status." />

            <UserForm 
                :form="form" 
                :is-edit="true" 
                :grouped-permissions="groupedPermissions"
                :branches="branches"
                @submit="submit" 
            />
        </div>
    </AppLayout>
</template>
