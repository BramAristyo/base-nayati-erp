<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import UserForm from '@/components/Users/UserForm.vue';
import type { ShowUser, UpdateUserRequest } from '@/types/utility/user.types';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    user: ShowUser;
}>();

const form = useForm<UpdateUserRequest>({
    id: props.user.id,
    name: props.user.name,
    email: props.user.email,
    approver_name: props.user.approver_name,
    approver_title: props.user.approver_title,
    branch_code: props.user.branch_code,
    position: props.user.position,
    roles: props.user.roles?.map(r => r.name) || [],
    warehouses: props.user.warehouses?.map(w => w.id) || [],
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
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold text-black uppercase tracking-tight">Edit User</h1>
                <p class="text-xs text-gray-500 font-medium italic">Update user credentials, permissions, and account status.</p>
            </div>

            <UserForm :form="form" :is-edit="true" @submit="submit" />
        </div>
    </AppLayout>
</template>
