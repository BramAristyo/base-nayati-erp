<script setup lang="ts">
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';
import { onMounted } from 'vue';
import { route } from 'ziggy-js';
import { useCancelConfirm } from '@/composables/common/useCancelConfirm';
import { useRolePermissionStore } from '@/stores/utility/useRolePermissionStore';
import { useWarehouseStore } from '@/stores/utility/useWarehouseStore';

defineProps<{
    form: any; // Using any for Inertia useForm type
    isEdit?: boolean;
}>();

const emit = defineEmits(['submit']);

const roleStore = useRolePermissionStore();
const warehouseStore = useWarehouseStore();
const { confirmCancel } = useCancelConfirm();

const positions = [
    { label: 'Back Office', value: 'BO' },
    { label: 'Front Office', value: 'FO' }
];

const handleCancel = () => {
    confirmCancel(route('utility.users.paginate'));
};

onMounted(() => {
    roleStore.fetchAllRoles();
    warehouseStore.fetchAll();
});
</script>

<template>
    <form @submit.prevent="emit('submit')" class="flex flex-col gap-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Left Column: Primary Information -->
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-1">
                    <h2 class="text-sm font-bold text-foreground uppercase tracking-widest">Primary Identity</h2>
                    <p class="text-[11px] text-muted-foreground font-medium italic">Basic account credentials and
                        identification.</p>
                </div>

                <div class="flex flex-col gap-6">
                    <!-- Name -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Full Name</label>
                        <InputText v-model="form.name" placeholder="Enter user's full name" size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm placeholder:text-muted-foreground!"
                            :class="{ 'border-destructive!': form.errors.name }" />
                        <small v-if="form.errors.name" class="text-[10px] text-destructive font-bold italic">{{
                            form.errors.name
                            }}</small>
                    </div>

                    <!-- Email -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Email
                            Address</label>
                        <InputText v-model="form.email" placeholder="user@pt-inox.com" size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm placeholder:text-muted-foreground!"
                            :class="{ 'border-destructive!': form.errors.email }" />
                        <small v-if="form.errors.email" class="text-[10px] text-destructive font-bold italic">{{
                            form.errors.email
                            }}</small>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Password -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">{{ isEdit ?
                                'New Password' : 'Password' }}</label>
                            <InputText v-model="form.password" type="password" placeholder="••••••••" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm"
                                :class="{ 'border-destructive!': form.errors.password }" />
                            <small v-if="form.errors.password" class="text-[10px] text-destructive font-bold italic">{{
                                form.errors.password }}</small>
                        </div>

                        <!-- Password Confirmation -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Confirm
                                Password</label>
                            <InputText v-model="form.password_confirmation" type="password" placeholder="••••••••"
                                size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm" />
                        </div>
                    </div>
                    <small v-if="isEdit" class="text-[10px] text-muted-foreground font-medium italic -mt-4">Leave blank to keep
                        current password.</small>
                </div>
            </div>

            <!-- Right Column: Employment & Access -->
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-1">
                    <h2 class="text-sm font-bold text-foreground uppercase tracking-widest">Professional Profile</h2>
                    <p class="text-[11px] text-muted-foreground font-medium italic">Employment details and system access levels.
                    </p>
                </div>

                <div class="flex flex-col gap-6">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Position -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Job
                                Position</label>
                            <Select v-model="form.position" :options="positions" optionLabel="label" optionValue="value"
                                placeholder="Select Position" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm"
                                :class="{ 'border-destructive!': form.errors.position }" />
                            <small v-if="form.errors.position" class="text-[10px] text-destructive font-bold italic">{{
                                form.errors.position }}</small>
                        </div>

                        <!-- Branch Code -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Branch
                                Code</label>
                            <InputText v-model="form.branch_code" placeholder="e.g. HO, BDG" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm"
                                :class="{ 'border-destructive!': form.errors.branch_code }" />
                            <small v-if="form.errors.branch_code" class="text-[10px] text-destructive font-bold italic">{{
                                form.errors.branch_code }}</small>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Approver Name -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Approver
                                Name</label>
                            <InputText v-model="form.approver_name" placeholder="Superior's Name" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm"
                                :class="{ 'border-destructive!': form.errors.approver_name }" />
                            <small v-if="form.errors.approver_name" class="text-[10px] text-destructive font-bold italic">{{
                                form.errors.approver_name }}</small>
                        </div>

                        <!-- Approver Title -->
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Approver
                                Title</label>
                            <InputText v-model="form.approver_title" placeholder="Superior's Title" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm"
                                :class="{ 'border-destructive!': form.errors.approver_title }" />
                            <small v-if="form.errors.approver_title"
                                class="text-[10px] text-destructive font-bold italic">{{ form.errors.approver_title
                                }}</small>
                        </div>
                    </div>

                    <!-- Roles -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Security
                            Roles</label>
                        <MultiSelect v-model="form.roles" :options="roleStore.roles" filter optionLabel="name"
                            optionValue="slug" placeholder="Assign Roles" :loading="roleStore.isFetchingRoles"
                            size="small" class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm"
                            :class="{ 'border-destructive!': form.errors.roles }" />
                        <small v-if="form.errors.roles" class="text-[10px] text-destructive font-bold italic">{{
                            form.errors.roles
                            }}</small>
                    </div>

                    <!-- Warehouses -->
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Assigned
                            Warehouses</label>
                        <MultiSelect v-model="form.warehouses" :options="warehouseStore.warehouses" filter
                            optionLabel="name" optionValue="id" placeholder="Select Warehouses"
                            :loading="warehouseStore.isFetching" size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm"
                            :class="{ 'border-destructive!': form.errors.warehouses }" />
                        <small v-if="form.errors.warehouses" class="text-[10px] text-destructive font-bold italic">{{
                            form.errors.warehouses }}</small>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center gap-4 bg-muted/50 p-3 rounded-lg border border-border mt-2">
                        <div class="flex flex-col gap-0.5 flex-1">
                            <span class="text-[10px] font-bold text-foreground uppercase tracking-widest">Account
                                Status</span>
                            <span class="text-[10px] text-muted-foreground font-medium italic">Active users can access the
                                system.</span>
                        </div>
                        <ToggleSwitch v-model="form.is_active" size="small" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
            <Button label="Discard" icon="pi pi-times" severity="secondary" variant="text" size="small"
                class="text-[10px]! font-bold! uppercase! tracking-widest!" @click="handleCancel" />
            <Button type="submit" :label="isEdit ? 'Update User' : 'Create User'" icon="pi pi-check" size="small"
                :loading="form.processing"
                class="bg-primary! border-none! text-primary-foreground! font-bold! uppercase! tracking-widest! rounded-md! px-6! shadow-md! transition-all hover:bg-primary/90!" />
        </div>
    </form>
</template>
