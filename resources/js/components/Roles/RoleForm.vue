<script setup lang="ts">
import Accordion from 'primevue/accordion';
import AccordionContent from 'primevue/accordioncontent';
import AccordionHeader from 'primevue/accordionheader';
import AccordionPanel from 'primevue/accordionpanel';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import { computed, onMounted, watch } from 'vue';
import { route } from 'ziggy-js';
import { useCancelConfirm } from '@/composables/common/useCancelConfirm';
import { useRolePermissionStore } from '@/stores/utility/useRolePermissionStore';
import type { Permission } from '@/types/utility/role-permissions.types';

const props = defineProps<{
    form: any; // Inertia useForm type
    isEdit?: boolean;
}>();

const emit = defineEmits(['submit']);

const roleStore = useRolePermissionStore();
const { confirmCancel } = useCancelConfirm();

const groupedPermissions = computed(() => {
    const groups: Record<string, Record<string, Permission[]>> = {};

    roleStore.permissions.forEach(permission => {
        if (!groups[permission.module]) {
            groups[permission.module] = {};
        }

        const subModule = permission.sub_module || 'General';

        if (!groups[permission.module][subModule]) {
            groups[permission.module][subModule] = [];
        }

        groups[permission.module][subModule].push(permission);
    });

    return groups;
});

const isPermissionSelected = (id: number) => {
    return props.form.permission_ids.includes(id);
};

const togglePermission = (id: number) => {
    const index = props.form.permission_ids.indexOf(id);

    if (index === -1) {
        props.form.permission_ids.push(id);
    } else {
        props.form.permission_ids.splice(index, 1);
    }
};

const selectAll = () => {
    props.form.permission_ids = roleStore.permissions.map(p => p.id);
};

const deselectAll = () => {
    props.form.permission_ids = [];
};

const generateSlug = (name: string) => {
    return name
        .toLowerCase()
        .replace(/[^\w ]+/g, '')
        .replace(/ +/g, '-')
        .replace(/(^-|-$)+/g, '');
};

watch(() => props.form.name, (newName) => {
    if (!props.isEdit) {
        props.form.slug = generateSlug(newName);
    }
});

const handleCancel = () => {
    confirmCancel(route('utility.roles.paginate'));
};

onMounted(() => {
    roleStore.fetchAllPermissions();
});
</script>

<template>
    <form @submit.prevent="emit('submit')" class="flex flex-col gap-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <!-- Left Column: Primary Information -->
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-1">
                    <h2 class="text-sm font-bold text-foreground uppercase tracking-widest">Role Identity</h2>
                    <p class="text-[11px] text-muted-foreground font-medium italic">Basic identification and definition
                        of the
                        role.</p>
                </div>

                <div class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Role Name</label>
                        <InputText v-model="form.name" placeholder="e.g. Sales Manager" size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm placeholder:text-muted-foreground!"
                            :class="{ 'border-destructive!': form.errors.name }" />
                        <small v-if="form.errors.name" class="text-[10px] text-destructive font-bold italic">{{
                            form.errors.name
                            }}</small>
                    </div>

                    <div class="flex flex-col gap-2">
                        <label class="text-[10px] font-bold text-foreground uppercase tracking-widest">Role Slug</label>
                        <InputText v-model="form.slug" placeholder="e.g. sales-manager" size="small"
                            class="w-full! bg-muted border-border! text-muted-foreground! rounded-md! cursor-not-allowed"
                            disabled />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label
                            class="text-[10px] font-bold text-foreground uppercase tracking-widest">Description</label>
                        <Textarea v-model="form.description" rows="4"
                            placeholder="Briefly describe this role's purpose..." size="small"
                            class="w-full! bg-background border-input! text-foreground! rounded-md! focus:ring-1! focus:ring-ring! transition-all shadow-sm placeholder:text-muted-foreground!" />
                    </div>
                </div>
            </div>

            <!-- Right Column: Permissions Selection -->
            <div class="flex flex-col gap-8">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-bold text-foreground uppercase tracking-widest">Access Control Policies
                        </h2>
                        <span
                            class="text-[10px] font-bold bg-primary text-primary-foreground px-2 py-0.5 rounded-full shadow-sm">{{
                                form.permission_ids.length }} SELECTED</span>
                    </div>
                    <p class="text-[11px] text-muted-foreground font-medium italic">Define which actions this role is
                        authorized
                        to
                        perform.</p>
                </div>

                <div v-if="roleStore.isFetchingPermissions" class="flex flex-col gap-3">
                    <div v-for="i in 3" :key="i" class="h-10 w-full bg-muted animate-pulse rounded-md"></div>
                </div>

                <div v-else class="flex flex-col gap-4">
                    <div class="flex items-center gap-2 mb-2">
                        <Button label="Select All" icon="pi pi-check-square" size="small" severity="secondary"
                            variant="text" class="text-[10px]! font-bold! uppercase! tracking-widest!"
                            @click="selectAll" />
                        <span class="text-border">|</span>
                        <Button label="Deselect All" icon="pi pi-stop" size="small" severity="secondary" variant="text"
                            class="text-[10px]! font-bold! uppercase! tracking-widest!" @click="deselectAll" />
                    </div>

                    <Accordion>
                        <AccordionPanel v-for="(subModules, module) in groupedPermissions" :key="module" :value="module"
                            class="border border-border! rounded-lg! overflow-hidden! mb-2!">
                            <AccordionHeader class="bg-muted/50! py-3! px-4!">
                                <div class="flex items-center gap-3">
                                    <i class="pi pi-shield text-muted-foreground text-xs"></i>
                                    <span class="text-[11px] font-bold text-foreground uppercase tracking-widest">{{
                                        module
                                        }}</span>
                                </div>
                            </AccordionHeader>
                            <AccordionContent class="p-0!">
                                <div class="divide-y divide-border">
                                    <div v-for="(permissions, subModule) in subModules" :key="subModule" class="p-4">
                                        <h4
                                            class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-3 italic">
                                            {{ subModule }}</h4>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div v-for="permission in permissions" :key="permission.id"
                                                class="flex items-center gap-3 p-2 rounded-md hover:bg-accent transition-colors cursor-pointer group"
                                                @click="togglePermission(permission.id)">
                                                <Checkbox :modelValue="isPermissionSelected(permission.id)"
                                                    :binary="true" size="small"
                                                    @click.stop="togglePermission(permission.id)" />
                                                <div class="flex flex-col gap-0.5">
                                                    <span class="text-xs font-bold text-foreground leading-none">{{
                                                        permission.action }}</span>
                                                    <span class="text-[10px] text-muted-foreground font-medium">{{
                                                        permission.slug
                                                        }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionPanel>
                    </Accordion>
                    <small v-if="form.errors.permission_ids" class="text-[10px] text-destructive font-bold italic">{{
                        form.errors.permission_ids }}</small>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-border">
            <Button label="Discard" icon="pi pi-times" severity="secondary" variant="text" size="small"
                class="text-[10px]! font-bold! uppercase! tracking-widest!" @click="handleCancel" />
            <Button type="submit" :label="isEdit ? 'Update Role' : 'Create Role'" icon="pi pi-check" size="small"
                :loading="form.processing"
                class="bg-primary! border-none! text-primary-foreground! font-bold! uppercase! tracking-widest! rounded-md! px-6! shadow-md! transition-all hover:bg-primary/90!" />
        </div>
    </form>
</template>
