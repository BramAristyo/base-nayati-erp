<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import UserForm from '@/components/Users/UserForm.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { StoreUserRequest } from '@/types/utility/user.types';

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
            <AppPageHeader
                title="Create New User"
                description="Register a new system user and assign permissions."
            />

            <UserForm :form="form" @submit="submit" />
        </div>
    </AppLayout>
</template>
