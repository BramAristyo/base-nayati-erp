<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import UserForm from '@/components/Users/UserForm.vue';
import type { StoreUserRequest } from '@/types/utility/user.types';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm<StoreUserRequest>({
    name: '',
    email: '',
    approver_name: '',
    approver_title: '',
    branch_code: '',
    position: '',
    roles: [],
    warehouses: [],
    is_active: 1
});

const submit = () => {
    form.post(route('utility.users.store'));
};
</script>

<template>
    <Head title="Create User" />

    <AppLayout>
        <div class="space-y-10">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold text-black uppercase tracking-tight">Create New User</h1>
                <p class="text-xs text-gray-500 font-medium italic">Register a new system user and assign permissions.</p>
            </div>

            <UserForm :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
