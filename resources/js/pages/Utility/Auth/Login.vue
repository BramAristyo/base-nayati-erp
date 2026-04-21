<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Checkbox from 'primevue/checkbox';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';

const form = useForm({
    email: '',
    password: '',
    remember: false as boolean,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center"
        style="background-image: url('https://picsum.photos/seed/inox/1920/1080'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" />

        <Card class="relative z-10 w-full max-w-sm shadow-2xl">
            <template #header>
                <div class="flex flex-col items-center gap-1 pt-6 px-6">
                    <img src="/images/logo_ima.png" alt="Logo" class="h-12 w-auto mb-2" />
                    <span class="text-surface-900 text-xl font-semibold">Welcome to the IMA ERP</span>
                    <span class="text-surface-500 text-sm">Sign in to your account</span>
                </div>
            </template>

            <template #content>
                <form @submit.prevent="submit" class="flex flex-col gap-4">
                    <div class="flex flex-col gap-1">
                        <label for="email" class="text-sm font-medium text-surface-700">Email Address</label>
                        <InputText id="email" v-model="form.email" type="email" placeholder="Email address" size="small"
                            class="w-full" :invalid="!!form.errors.email" autocomplete="email" />
                        <small v-if="form.errors.email" class="text-red-500">{{ form.errors.email }}</small>
                    </div>

                    <div class="flex flex-col gap-1">
                        <label for="password" class="text-sm font-medium text-surface-700">Password</label>
                        <Password id="password" v-model="form.password" placeholder="Password" size="small"
                            :toggleMask="true" :feedback="false" :invalid="!!form.errors.password" input-class="w-full!"
                            autocomplete="current-password" />
                        <small v-if="form.errors.password" class="text-red-500">{{ form.errors.password }}</small>
                    </div>

                    <div class="flex items-center gap-2">
                        <Checkbox id="remember" v-model="form.remember" :binary="true" />
                        <label for="remember" class="text-sm text-surface-700">Remember me</label>
                    </div>

                    <Button type="submit" label="Sign In" icon="pi pi-sign-in" size="small" class="w-full mt-1"
                        :loading="form.processing" />
                </form>
            </template>
        </Card>
    </div>
</template>