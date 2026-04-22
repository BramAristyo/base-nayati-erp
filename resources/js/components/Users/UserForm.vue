<script setup lang="ts">
import { useRolePermissionStore } from '@/stores/utility/useRolePermissionStore';
import { useWarehouseStore } from '@/stores/utility/useWarehouseStore';
import type { StoreUserRequest, UpdateUserRequest } from '@/types/utility/user.types';
import { Link } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';
import { onMounted } from 'vue';

const props = defineProps<{
    form: any; // Using any for Inertia useForm type
    isEdit?: boolean;
}>();

const emit = defineEmits(['submit']);

const roleStore = useRolePermissionStore();
const warehouseStore = useWarehouseStore();

const positions = [
    { label: 'Back Office', value: 'BO' },
    { label: 'Front Office', value: 'FO' }
];

onMounted(async () => {
    await Promise.all([
        roleStore.fetchAllRoles(),
        warehouseStore.fetchAll()
    ]);
});
</script>

<template>
    <form @submit.prevent="emit('submit')" class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-white p-8 rounded-xl shadow-sm border border-gray-100">
        <!-- Basic Information -->
        <div class="flex flex-col gap-2">
            <label for="name" class="text-xs font-bold uppercase tracking-widest text-gray-700">Full Name</label>
            <InputText id="name" v-model="form.name" size="small" :invalid="!!form.errors.name" class="w-full!" placeholder="e.g. John Doe" />
            <small v-if="form.errors.name" class="text-red-500 font-medium">{{ form.errors.name }}</small>
        </div>

        <div class="flex flex-col gap-2">
            <label for="email" class="text-xs font-bold uppercase tracking-widest text-gray-700">Email Address</label>
            <InputText id="email" v-model="form.email" size="small" :invalid="!!form.errors.email" class="w-full!" placeholder="john@example.com" />
            <small v-if="form.errors.email" class="text-red-500 font-medium">{{ form.errors.email }}</small>
        </div>

        <div class="flex flex-col gap-2">
            <label for="position" class="text-xs font-bold uppercase tracking-widest text-gray-700">Position</label>
            <Select id="position" v-model="form.position" :options="positions" optionLabel="label" optionValue="value" size="small" :invalid="!!form.errors.position" class="w-full!" placeholder="Select Position" />
            <small v-if="form.errors.position" class="text-red-500 font-medium">{{ form.errors.position }}</small>
        </div>

        <div class="flex flex-col gap-2">
            <label for="branch_code" class="text-xs font-bold uppercase tracking-widest text-gray-700">Branch Code</label>
            <InputText id="branch_code" v-model="form.branch_code" size="small" :invalid="!!form.errors.branch_code" class="w-full!" placeholder="e.g. JKT01" />
            <small v-if="form.errors.branch_code" class="text-red-500 font-medium">{{ form.errors.branch_code }}</small>
        </div>

        <!-- Approver Information -->
        <div class="flex flex-col gap-2">
            <label for="approver_name" class="text-xs font-bold uppercase tracking-widest text-gray-700">Approver Name</label>
            <InputText id="approver_name" v-model="form.approver_name" size="small" :invalid="!!form.errors.approver_name" class="w-full!" placeholder="e.g. Jane Smith" />
            <small v-if="form.errors.approver_name" class="text-red-500 font-medium">{{ form.errors.approver_name }}</small>
        </div>

        <div class="flex flex-col gap-2">
            <label for="approver_title" class="text-xs font-bold uppercase tracking-widest text-gray-700">Approver Title</label>
            <InputText id="approver_title" v-model="form.approver_title" size="small" :invalid="!!form.errors.approver_title" class="w-full!" placeholder="e.g. Operational Director" />
            <small v-if="form.errors.approver_title" class="text-red-500 font-medium">{{ form.errors.approver_title }}</small>
        </div>

        <!-- Roles and Warehouses -->
        <div class="flex flex-col gap-2">
            <label for="roles" class="text-xs font-bold uppercase tracking-widest text-gray-700">Roles</label>
            <MultiSelect id="roles" v-model="form.roles" :options="roleStore.roles" optionLabel="name" optionValue="name" placeholder="Select Roles" size="small" :invalid="!!form.errors.roles" class="w-full!" :loading="roleStore.isFetchingRoles" filter showClear />
            <small v-if="form.errors.roles" class="text-red-500 font-medium">{{ form.errors.roles }}</small>
        </div>

        <div class="flex flex-col gap-2">
            <label for="warehouses" class="text-xs font-bold uppercase tracking-widest text-gray-700">Warehouse Access</label>
            <MultiSelect id="warehouses" v-model="form.warehouses" :options="warehouseStore.warehouses" optionLabel="display_name" optionValue="id" placeholder="Select Warehouses" size="small" :invalid="!!form.errors.warehouses" class="w-full!" :loading="warehouseStore.isFetching" filter showClear />
            <small v-if="form.errors.warehouses" class="text-red-500 font-medium">{{ form.errors.warehouses }}</small>
        </div>

        <!-- Password (Optional for Edit) -->
        <template v-if="props.isEdit">
            <div class="flex flex-col gap-2">
                <label for="password" class="text-xs font-bold uppercase tracking-widest text-gray-700">New Password (Optional)</label>
                <InputText id="password" v-model="form.password" type="password" size="small" :invalid="!!form.errors.password" class="w-full!" />
                <small v-if="form.errors.password" class="text-red-500 font-medium">{{ form.errors.password }}</small>
            </div>

            <div class="flex flex-col gap-2">
                <label for="password_confirmation" class="text-xs font-bold uppercase tracking-widest text-gray-700">Confirm New Password</label>
                <InputText id="password_confirmation" v-model="form.password_confirmation" type="password" size="small" class="w-full!" />
            </div>
        </template>

        <!-- Status -->
        <div class="flex items-center gap-4 col-span-1 md:col-span-2 mt-2">
            <div class="flex flex-col">
                <span class="text-xs font-bold uppercase tracking-widest text-gray-700">Account Status</span>
                <span class="text-[10px] text-gray-500 font-medium">Toggle to activate or deactivate this user account.</span>
            </div>
            <ToggleSwitch v-model="form.is_active" :trueValue="1" :falseValue="0" />
        </div>

        <!-- Submit -->
        <div class="flex justify-end gap-3 col-span-1 md:col-span-2 mt-6 pt-6 border-t border-gray-100">
            <Link :href="route('utility.users.paginate')">
                <Button label="Cancel" severity="secondary" variant="text" size="small" class="font-bold! uppercase! tracking-widest!" />
            </Link>
            <Button type="submit" :label="isEdit ? 'Update User' : 'Save User'" icon="pi pi-check" size="small" :loading="form.processing" class="bg-black! border-none! text-white! font-bold! uppercase! tracking-widest! px-6! shadow-md!" />
        </div>
    </form>
</template>
