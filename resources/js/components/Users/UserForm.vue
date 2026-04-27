<script setup lang="ts">
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import MultiSelect from 'primevue/multiselect';
import Select from 'primevue/select';
import ToggleSwitch from 'primevue/toggleswitch';
import Divider from 'primevue/divider';
import { onMounted, computed } from 'vue';
import { route } from 'ziggy-js';
import { useCancelConfirm } from '@/composables/common/useCancelConfirm';
import { useRolePermissionStore } from '@/stores/utility/useRolePermissionStore';
import { useWarehouseStore } from '@/stores/utility/useWarehouseStore';
import type { Branch } from '@/types/master/master.types';

const props = defineProps<{
    form: any;
    isEdit?: boolean;
    groupedPermissions: any;
    branches: Branch[];
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

// Compute flattened permissions from currently selected roles
const inheritedPermissionIds = computed(() => {
    const selectedRoles = roleStore.roles.filter(r => props.form.roles.includes(r.slug));
    const ids = new Set<number>();
    selectedRoles.forEach(role => {
        role.permissions?.forEach(p => ids.add(p.id));
    });
    return ids;
});

const getPermissionState = (permissionId: number) => {
    const override = props.form.permissions.find((p: any) => p.permission_id === permissionId);
    
    if (override) {
        return override.is_denied ? 'denied' : 'granted';
    }
    
    if (inheritedPermissionIds.value.has(permissionId)) {
        return 'inherited';
    }
    
    return 'none';
};

const togglePermission = (permissionId: number) => {
    const state = getPermissionState(permissionId);
    const index = props.form.permissions.findIndex((p: any) => p.permission_id === permissionId);

    if (state === 'none') {
        // None -> Grant (Green)
        props.form.permissions.push({ permission_id: permissionId, is_denied: false });
    } else if (state === 'inherited') {
        // Inherited (Gray) -> Deny (Red)
        props.form.permissions.push({ permission_id: permissionId, is_denied: true });
    } else {
        // Granted or Denied -> None (Neutral)
        if (index !== -1) {
            props.form.permissions.splice(index, 1);
        }
    }
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
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Full Name</label>
                        <InputText v-model="form.name" placeholder="Enter user's full name" size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm"
                            :class="{ 'border-destructive!': form.errors.name }" />
                        <small v-if="form.errors.name" class="text-[10px] text-destructive font-bold italic">{{
                            form.errors.name }}</small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Email
                            Address</label>
                        <InputText v-model="form.email" placeholder="user@pt-inox.com" size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm"
                            :class="{ 'border-destructive!': form.errors.email }" />
                        <small v-if="form.errors.email" class="text-[10px] text-destructive font-bold italic">{{
                            form.errors.email }}</small>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">{{ isEdit ?
                                'New Password' : 'Password' }}</label>
                            <InputText v-model="form.password" type="password" placeholder="••••••••" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm"
                                :class="{ 'border-destructive!': form.errors.password }" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Confirm
                                Password</label>
                            <InputText v-model="form.password_confirmation" type="password" placeholder="••••••••"
                                size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Employment & Access -->
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-1">
                    <h2 class="text-sm font-bold text-foreground uppercase tracking-widest">Professional Profile</h2>
                    <p class="text-[11px] text-muted-foreground font-medium italic">Employment details and system access levels.</p>
                </div>

                <div class="flex flex-col gap-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Job Position</label>
                            <Select v-model="form.position" :options="positions" optionLabel="label" optionValue="value"
                                placeholder="Select Position" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm" />
                        </div>
                        <div class="flex flex-col gap-2">
                            <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Branch Code</label>
                            <Select v-model="form.branch_code" :options="branches" optionLabel="name" optionValue="code"
                                placeholder="Select Branch" size="small"
                                class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm" />
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Security Roles</label>
                        <MultiSelect v-model="form.roles" :options="roleStore.roles" filter optionLabel="name"
                            optionValue="slug" placeholder="Assign Roles" :loading="roleStore.isFetchingRoles"
                            size="small" class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Assigned Warehouses</label>
                        <MultiSelect v-model="form.warehouses" :options="warehouseStore.warehouses" filter
                            optionLabel="name" optionValue="id" placeholder="Select Warehouses"
                            :loading="warehouseStore.isFetching" size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! shadow-sm" />
                    </div>

                    <div class="flex items-center gap-4 bg-muted/50 p-3 rounded-lg border border-border mt-2">
                        <div class="flex flex-col gap-0.5 flex-1">
                            <span class="text-[10px] font-bold text-foreground uppercase tracking-widest">Account Status</span>
                            <span class="text-[10px] text-muted-foreground font-medium italic">Active users can access system.</span>
                        </div>
                        <ToggleSwitch v-model="form.is_active" :trueValue="1" :falseValue="0" size="small" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Matrix -->
        <div class="flex flex-col gap-6 mt-4">
            <div class="flex flex-col gap-1">
                <h2 class="text-sm font-bold text-foreground uppercase tracking-widest text-primary">Direct Permission Overrides</h2>
                <div class="flex items-center gap-4 text-[9px] font-bold uppercase tracking-tighter opacity-70">
                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-foreground border border-border"></span> Inherited (Role)</div>
                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-success-green"></span> Granted (Direct)</div>
                    <div class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-destructive"></span> Denied (Direct)</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
                <div v-for="(subModules, module) in groupedPermissions" :key="module" 
                    class="flex flex-col gap-4 p-4 rounded-xl border border-border bg-card shadow-xs">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-foreground opacity-50 border-b border-border pb-2">{{ module }}</h3>
                    
                    <div v-for="(perms, subModule) in subModules" :key="subModule" class="flex flex-col gap-3">
                        <h4 class="text-[9px] font-bold uppercase text-primary/70 tracking-tight">{{ subModule }}</h4>
                        <div class="flex flex-wrap gap-2">
                            <button 
                                v-for="permission in perms" 
                                :key="permission.id"
                                type="button"
                                @click="togglePermission(permission.id)"
                                class="px-2 py-1 rounded text-[9px] font-bold uppercase border transition-all duration-200 flex items-center gap-2"
                                :class="{
                                    'bg-background border-border text-foreground/40': getPermissionState(permission.id) === 'none',
                                    'bg-foreground border-foreground text-background shadow-md': getPermissionState(permission.id) === 'inherited',
                                    'bg-success-green border-success-green text-success-green-foreground shadow-md scale-105': getPermissionState(permission.id) === 'granted',
                                    'bg-destructive border-destructive text-destructive-foreground shadow-md scale-105': getPermissionState(permission.id) === 'denied'
                                }"
                            >
                                <i :class="[
                                    getPermissionState(permission.id) === 'none' ? 'pi pi-circle' : 
                                    getPermissionState(permission.id) === 'inherited' ? 'pi pi-check-circle' :
                                    getPermissionState(permission.id) === 'granted' ? 'pi pi-plus-circle' : 'pi pi-ban'
                                ]" style="font-size: 10px"></i>
                                {{ permission.action }}
                            </button>
                        </div>
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

<style scoped>
.bg-success-green { background-color: var(--success-green); }
.text-success-green-foreground { color: var(--success-green-foreground); }
</style>
