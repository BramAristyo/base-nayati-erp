<script setup lang="ts">
import { useForm, Head, usePage } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Password from 'primevue/password';
import Toast from 'primevue/toast';
import { useToast } from 'primevue/usetoast';
import { watch } from 'vue';

const toast = useToast();
const page = usePage();
const form = useForm({
    password: '',
    password_confirmation: '',
});

watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 3000 });
    }

    if (flash?.error) {
        toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 3000 });
    }
}, { deep: true, immediate: true });

const submit = () => {
    form.post('/change-password', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center ">

        <Head title="Update Security" />
        <Toast />

        <div class="w-full max-w-sm flex flex-col p-6 ">
            <div class="flex flex-col items-center text-center gap-2 mb-8">
                <img src="/images/logo_ima.png" alt="IMA Logo" class="h-10 w-auto mb-2 object-contain" />
                <h2 class="text-xl font-bold text-black tracking-tight">Update Password</h2>
                <p class="text-xs text-gray-500 font-medium">Please set a new password for your account.</p>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-5">
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">New
                        Password</label>
                    <Password id="password" v-model="form.password" placeholder="••••••••" toggleMask size="small"
                        :feedback="false" :invalid="!!form.errors.password" class="w-full" inputClass="w-full!" />
                    <small v-if="form.errors.password" class="text-[10px] text-red-600 font-bold">{{
                        form.errors.password }}</small>
                </div>

                <div class="flex flex-col gap-1.5">
                    <label for="password_confirmation"
                        class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Confirm Password</label>
                    <Password id="password_confirmation" v-model="form.password_confirmation" placeholder="••••••••"
                        toggleMask size="small" :feedback="false" :invalid="!!form.errors.password_confirmation"
                        class="w-full" inputClass="w-full!" />
                    <small v-if="form.errors.password_confirmation" class="text-[10px] text-red-600 font-bold">{{
                        form.errors.password_confirmation }}</small>
                </div>

                <Button type="submit" label="Save" size="small"
                    class="w-full! mt-2! rounded-md! text-xs! bg-black! border-none! text-white! font-bold! uppercase! tracking-widest! transition-all! active:scale-95!"
                    :loading="form.processing" />
            </form>
        </div>
    </div>
</template>
