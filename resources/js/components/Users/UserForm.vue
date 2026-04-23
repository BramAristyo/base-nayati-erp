<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';
import { useConfirm } from 'primevue/useconfirm';
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import { useRolePermissionStore } from '@/stores/utility/useRolePermissionStore';
import { useWarehouseStore } from '@/stores/utility/useWarehouseStore';
import type { StoreUserRequest, UpdateUserRequest } from '@/types/utility/user.types';

const props = defineProps<{
    form: any; // Using any for Inertia useForm type
    isEdit?: boolean;
}>();

const emit = defineEmits(['submit']);

const roleStore = useRolePermissionStore();
const warehouseStore = useWarehouseStore();
const authStore = useAuthStore();
const confirm = useConfirm();

const positions = [
    { label: 'Back Office', value: 'BO' },
    { label: 'Front Office', value: 'FO' }
];

const confirmCancel = () => {
    confirm.require({
        message: 'Are you sure you want to cancel? Any unsaved changes will be lost.',
        header: 'Discard Changes',
        icon: 'pi pi-exclamation-triangle',
        rejectLabel: 'No, stay',
        acceptLabel: 'Yes, discard',
        rejectProps: {
            label: 'No, stay',
            severity: 'secondary',
            outlined: true,
            size: 'small'
        },
        acceptProps: {
            label: 'Yes, discard',
            severity: 'danger',
            size: 'small'
        },
        accept: () => {
            router.get(route('utility.users.paginate'));
        }
    });
};

onMounted(async () => {
    await Promise.all([
        roleStore.fetchAllRoles(),
        warehouseStore.fetchAll()
    ]);
});
</script>

<template>
    <form @submit.prevent="emit('submit')" class="max-w-4xl space-y-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
            <!-- Basic Information -->
            <div class="flex flex-col gap-1.5">
                <label for="name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Full
                    Name</label>
                <InputText id="name" v-model="form.name" size="small" :invalid="!!form.errors.name" class="w-full!"
                    placeholder="e.g. John Doe" />
                <small v-if="form.errors.name" class="text-[10px] text-red-600 font-bold">{{ form.errors.name }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="email" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email
                    Address</label>
                <InputText id="email" v-model="form.email" size="small" :invalid="!!form.errors.email" class="w-full!"
                    placeholder="john@ns.inox" />
                <small v-if="form.errors.email" class="text-[10px] text-red-600 font-bold">{{ form.errors.email
                    }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="position"
                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Position</label>
                <Select id="position" v-model="form.position" :options="positions" optionLabel="label"
                    optionValue="value" size="small" :invalid="!!form.errors.position" class="w-full!"
                    placeholder="Select Position" />
                <small v-if="form.errors.position" class="text-[10px] text-red-600 font-bold">{{ form.errors.position
                    }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="branch_code" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Branch
                    Code</label>
                <InputText id="branch_code" v-model="form.branch_code" size="small" :invalid="!!form.errors.branch_code"
                    class="w-full!" placeholder="e.g. JKT01" />
                <small v-if="form.errors.branch_code" class="text-[10px] text-red-600 font-bold">{{
                    form.errors.branch_code }}</small>
            </div>

            <!-- Approver Information -->
            <div class="flex flex-col gap-1.5">
                <label for="approver_name"
                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Approver
                    Name</label>
                <InputText id="approver_name" v-model="form.approver_name" size="small"
                    :invalid="!!form.errors.approver_name" class="w-full!" placeholder="e.g. Jane Smith" />
                <small v-if="form.errors.approver_name" class="text-[10px] text-red-600 font-bold">{{
                    form.errors.approver_name
                    }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="approver_title"
                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Approver
                    Title</label>
                <InputText id="approver_title" v-model="form.approver_title" size="small"
                    :invalid="!!form.errors.approver_title" class="w-full!" placeholder="e.g. Operational Director" />
                <small v-if="form.errors.approver_title" class="text-[10px] text-red-600 font-bold">{{
                    form.errors.approver_title
                    }}</small>
            </div>

            <!-- Roles and Warehouses -->
            <div class="flex flex-col gap-1.5">
                <label for="roles" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Roles</label>
                <MultiSelect id="roles" v-model="form.roles" :options="roleStore.roles" optionLabel="name"
                    optionValue="slug" placeholder="Select Roles" size="small" :invalid="!!form.errors.roles"
                    class="w-full!" :loading="roleStore.isFetchingRoles" filter showClear />
                <small v-if="form.errors.roles" class="text-[10px] text-red-600 font-bold">{{ form.errors.roles
                    }}</small>
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="warehouses" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Warehouse
                    Access</label>
                <MultiSelect id="warehouses" v-model="form.warehouses" :options="warehouseStore.warehouses"
                    optionLabel="display_name" optionValue="id" placeholder="Select Warehouses" size="small"
                    :invalid="!!form.errors.warehouses" class="w-full!" :loading="warehouseStore.isFetching" filter
                    showClear />
                <small v-if="form.errors.warehouses" class="text-[10px] text-red-600 font-bold">{{
                    form.errors.warehouses }}</small>
            </div>

            <!-- Password (Optional for Edit) -->
            <template v-if="props.isEdit">
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">New
                        Password
                        (Optional)</label>
                    <InputText id="password" v-model="form.password" type="password" size="small"
                        :invalid="!!form.errors.password" class="w-full!" />
                    <small v-if="form.errors.password" class="text-[10px] text-red-600 font-bold">{{
                        form.errors.password }}</small>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password_confirmation"
                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Confirm
                        New Password</label>
                    <InputText id="password_confirmation" v-model="form.password_confirmation" type="password"
                        size="small" class="w-full!" />
                </div>
            </template>

            <!-- Status -->
            <div class="flex items-center gap-4 col-span-1 md:col-span-2 mt-2">
                <div class="flex flex-col">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Account Status</span>
                    <span class="text-[10px] text-gray-500 font-medium italic">Toggle to activate or deactivate this
                        user
                        account.</span>
                </div>
                <ToggleSwitch v-model="form.is_active" :trueValue="1" :falseValue="0" />
            </div>
        </div>

        <!-- Submit -->
        <div class="pt-6 border-t border-gray-100 flex justify-start gap-3">
            <Button
                v-if="(isEdit && authStore.hasPermission('utility.user.edit')) || (!isEdit && authStore.hasPermission('utility.user.create'))"
                type="submit" :label="isEdit ? 'Update User' : 'Save User'" :loading="form.processing"
                class="px-8! rounded-md! text-[10px]! bg-black! border-none! text-white! font-bold! uppercase! tracking-widest! transition-all! active:scale-95!" />

            <Button label="Cancel" severity="secondary" variant="text"
                v-if="(isEdit && authStore.hasPermission('utility.user.edit')) || (!isEdit && authStore.hasPermission('utility.user.create'))"
                class="text-[10px]! font-bold! uppercase! tracking-widest!" @click="confirmCancel" />
        </div>
    </form>
</template>
