<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppPageHeader from '@/components/common/AppPageHeader.vue';
import UserForm from '@/components/Users/UserForm.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import type { StoreUserRequest } from '@/types/utility/user.types';
import type { Branch } from '@/types/master/master.types';

defineProps<{
    groupedPermissions: any;
    branches: Branch[];
}>();

const form = useForm<StoreUserRequest>({
    name: '',
    email: '',
    approver_name: '',
    approver_title: '',
    branch_code: '',
    position: '',
    roles: [],
    warehouses: [],
    permissions: [],
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

            <UserForm 
                :form="form" 
                :grouped-permissions="groupedPermissions"
                :branches="branches"
                @submit="submit" 
            />
        </div>
    </AppLayout>
</template>
