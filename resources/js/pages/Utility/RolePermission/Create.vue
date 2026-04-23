<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { useRolePermissionStore } from '@/stores/utility/useRolePermissionStore';
import type { Permission, StoreRoleRequest } from '@/types/utility/role-permissions.types';
import { Head, useForm, router } from '@inertiajs/vue3';
import Accordion from 'primevue/accordion';
import AccordionContent from 'primevue/accordioncontent';
import AccordionHeader from 'primevue/accordionheader';
import AccordionPanel from 'primevue/accordionpanel';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import { useConfirm } from 'primevue/useconfirm';
import { computed, onMounted, watch } from 'vue';

const form = useForm<StoreRoleRequest>({
    name: '',
    slug: '',
    description: '',
    permission_ids: []
});

const roleStore = useRolePermissionStore();
const confirm = useConfirm();

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

const generateSlug = (name: string) => {
    return name
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)+/g, '');
};

watch(() => form.name, (newName) => {
    form.slug = generateSlug(newName);
});

const submit = () => {
    form.clearErrors();
    
    if (form.permission_ids.length === 0) {
        form.setError('permission_ids', 'You must select at least one action to create a role.');
        return;
    }

    form.post(route('utility.roles.store'));
};

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
            router.get(route('utility.roles.paginate'));
        }
    });
};

onMounted(() => {
    roleStore.fetchAllPermissions();
});
</script>

<template>

    <Head title="Create Role" />

    <AppLayout>
        <div class="space-y-10">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold text-black uppercase tracking-tight">Create New Role</h1>
                <p class="text-xs text-gray-500 font-medium italic">Define a new system role and assign specific access
                    permissions.</p>
            </div>

            <form @submit.prevent="submit" class="max-w-4xl space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6 border-b border-gray-100 pb-10">
                    <div class="flex flex-col gap-1.5">
                        <label for="name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role
                            Name</label>
                        <InputText id="name" v-model="form.name" size="small" :invalid="!!form.errors.name"
                            class="w-full!" placeholder="e.g. Administrator" />
                        <small v-if="form.errors.name" class="text-[10px] text-red-600 font-bold">{{ form.errors.name
                            }}</small>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="slug"
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Slug</label>
                        <InputText id="slug" v-model="form.slug" size="small" :invalid="!!form.errors.slug"
                            class="w-full!" placeholder="role-slug" />
                        <small v-if="form.errors.slug" class="text-[10px] text-red-600 font-bold">{{ form.errors.slug
                            }}</small>
                    </div>

                    <div class="flex flex-col gap-1.5 md:col-span-2">
                        <label for="description"
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Description</label>
                        <Textarea id="description" v-model="form.description" rows="3" size="small"
                            :invalid="!!form.errors.description" class="w-full! text-sm!"
                            placeholder="Describe the purpose of this role..." />
                        <small v-if="form.errors.description" class="text-[10px] text-red-600 font-bold">{{
                            form.errors.description }}</small>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-end justify-between border-b border-gray-100 pb-4">
                        <div class="flex flex-col gap-1">
                            <h2 class="text-sm font-bold text-black uppercase ">Permissions Assignment</h2>
                            <p class="text-[11px] text-gray-500 font-medium italic">Select the actions this role is
                                authorized to perform across different modules.</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="flex flex-col items-end">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total
                                    Selected</span>
                                <span class="text-xs font-bold text-black">{{ form.permission_ids.length }} / {{
                                    roleStore.permissions.length }} Actions</span>
                            </div>
                            <div class="h-8 w-px bg-gray-100"></div>
                            <div class="flex gap-2">
                                <Button type="button" label="Select All" size="small" variant="text"
                                    class="text-[10px]! font-bold! uppercase! tracking-widest! py-1! px-2!"
                                    @click="form.permission_ids = roleStore.permissions.map(p => p.id)" />
                                <Button type="button" label="Deselect All" size="small" variant="text" severity="danger"
                                    class="text-[10px]! font-bold! uppercase! tracking-widest! py-1! px-2!"
                                    @click="form.permission_ids = []" />
                            </div>
                        </div>
                    </div>

                    <Accordion :multiple="true" class="border-none!">
                        <AccordionPanel v-for="(subModules, module) in groupedPermissions" :key="module" :value="module"
                            class="border border-gray-100 rounded-lg! overflow-hidden mb-2">
                            <AccordionHeader class="bg-gray-50/50! py-3! px-4! hover:bg-gray-100/50! transition-all!">
                                <span class="text-xs font-bold uppercase tracking-widest text-gray-700">{{ module
                                    }}</span>
                            </AccordionHeader>
                            <AccordionContent class="p-4! border-t border-gray-50!">
                                <div class="space-y-6">
                                    <div v-for="(perms, subModule) in subModules" :key="subModule" class="space-y-3">
                                        <h3
                                            class="text-[10px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-1">
                                            {{ subModule }}</h3>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                            <div v-for="permission in perms" :key="permission.id"
                                                class="flex items-center gap-2 group">
                                                <Checkbox v-model="form.permission_ids"
                                                    :inputId="'perm-' + permission.id" :value="permission.id"
                                                    size="small" class="transition-transform group-active:scale-90" />
                                                <label :for="'perm-' + permission.id"
                                                    class="text-xs font-medium text-gray-700 cursor-pointer select-none group-hover:text-black transition-colors">
                                                    {{ permission.action }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </AccordionContent>
                        </AccordionPanel>
                    </Accordion>
                    <small v-if="form.errors.permission_ids" class="text-[10px] text-red-600 font-bold">{{
                        form.errors.permission_ids }}</small>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-start gap-3">
                    <Button type="submit" label="Save Role" :loading="form.processing"
                        class="px-8! rounded-md! text-[10px]! bg-black! border-none! text-white! font-bold! uppercase! tracking-widest! transition-all! active:scale-95!" />

                    <Button label="Cancel" severity="secondary" variant="text"
                        class="text-[10px]! font-bold! uppercase! tracking-widest!" @click="confirmCancel" />
                </div>
            </form>
        </div>
    </AppLayout>
</template>
