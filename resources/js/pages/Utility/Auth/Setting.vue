<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

const props = defineProps<{
    user: any;
}>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('user.settings.post'), {
        onSuccess: () => form.reset('current_password', 'password', 'password_confirmation'),
    });
};
</script>

<template>

    <Head title="Settings" />

    <AppLayout>
        <div class="space-y-10">
            <div class="flex flex-col gap-1">
                <h1 class="text-xl font-bold text-black uppercase tracking-tight">Account Settings</h1>
                <p class="text-xs text-gray-500 font-medium italic">Manage your profile information and account
                    credentials.</p>
            </div>

            <form @submit.prevent="submit" class="max-w-4xl space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div class="flex flex-col gap-1.5">
                        <label for="name" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Full
                            Name</label>
                        <InputText id="name" v-model="form.name" size="small" class="w-full!"
                            :invalid="!!form.errors.name" />
                        <small v-if="form.errors.name" class="text-[10px] text-red-600 font-bold">{{ form.errors.name
                        }}</small>
                    </div>

                    <div class="flex flex-col gap-1.5">
                        <label for="email" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Email
                            Address</label>
                        <InputText id="email" v-model="form.email" type="email" size="small" class="w-full!"
                            :invalid="!!form.errors.email" />
                        <small v-if="form.errors.email" class="text-[10px] text-red-600 font-bold">{{ form.errors.email
                        }}</small>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="flex flex-col gap-1">
                        <h2 class="text-xl font-bold text-black uppercase tracking-tight">Security</h2>
                        <p class="text-xs text-gray-500 font-medium italic">Leave password fields blank if you don't
                            want to
                            change it.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                        <div class="flex flex-col gap-1.5">
                            <label for="current_password"
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Current
                                Password</label>
                            <Password id="current_password" v-model="form.current_password" placeholder="••••••••"
                                toggleMask size="small" :feedback="false" :invalid="!!form.errors.current_password"
                                class="w-full" inputClass="w-full!" />
                            <small v-if="form.errors.current_password" class="text-[10px] text-red-600 font-bold">{{
                                form.errors.current_password }}</small>
                        </div>

                        <div class="hidden md:block"></div>

                        <div class="flex flex-col gap-1.5">
                            <label for="password"
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">New
                                Password</label>
                            <Password id="password" v-model="form.password" placeholder="••••••••" toggleMask
                                size="small" :feedback="false" :invalid="!!form.errors.password" class="w-full"
                                inputClass="w-full!" />
                            <small v-if="form.errors.password" class="text-[10px] text-red-600 font-bold">{{
                                form.errors.password }}</small>
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label for="password_confirmation"
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Confirm New
                                Password</label>
                            <Password id="password_confirmation" v-model="form.password_confirmation"
                                placeholder="••••••••" toggleMask size="small" :feedback="false" class="w-full"
                                inputClass="w-full!" />
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-start">
                    <Button type="submit" label="Save Changes" size="small"
                        class="px-8! rounded-md! text-xs! bg-black! border-none! text-white! font-bold! uppercase! tracking-widest! transition-all! active:scale-95!"
                        :loading="form.processing" />
                </div>
            </form>
        </div>
    </AppLayout>
</template>
