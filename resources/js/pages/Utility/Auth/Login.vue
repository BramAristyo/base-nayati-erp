<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
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
    <div class="min-h-screen flex w-full bg-surface-50">
        <!-- Left Side: Background Image (Hidden on smaller screens) -->
        <div class="hidden lg:flex relative w-1/2 bg-surface-900 overflow-hidden text-white">
            <img src="https://picsum.photos/seed/inox/1920/1080" alt="Background"
                class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-overlay" />
            <div class="absolute inset-0 bg-linear-to-t from-black/80 to-transparent"></div>
            <div class="relative z-10 flex flex-col justify-end p-12 w-full h-full">
                <div class="max-w-xl">
                    <h1 class="text-4xl lg:text-5xl font-bold mb-4 leading-tight">
                        Transform your workflow with IMA ERP.
                    </h1>
                    <p class="text-lg text-surface-200">
                        A seamless, fast, and modern enterprise solution built for your daily operations.
                    </p>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12">
            <div
                class="w-full max-w-md flex flex-col pt-8 pb-12 px-6 sm:px-10 bg-surface-0 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-surface-100">

                <!-- Logo & Header -->
                <div class="flex flex-col items-center sm:items-start gap-2 mb-10">
                    <img src="/images/logo_ima.png" alt="IMA Logo" class="h-10 w-auto mb-4" />
                    <h2 class="text-3xl font-bold text-surface-900 tracking-tight">Welcome back</h2>
                    <p class="text-surface-500 font-medium">Please enter your details to sign in.</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <!-- Email Field -->
                    <div class="flex flex-col gap-2">
                        <label for="email" class="text-sm font-semibold text-surface-900">Email</label>
                        <InputText id="email" v-model="form.email" type="email" placeholder="Enter your email"
                            class="w-full px-4 py-3 rounded-xl border-surface-200 hover:border-primary-400 focus:ring-primary-500"
                            :invalid="!!form.errors.email" autocomplete="email" />
                        <small v-if="form.errors.email" class="text-red-500 font-medium">{{ form.errors.email }}</small>
                    </div>

                    <!-- Password Field -->
                    <div class="flex flex-col gap-2">
                        <label for="password" class="text-sm font-semibold text-surface-900">Password</label>
                        <Password id="password" v-model="form.password" placeholder="••••••••" :toggleMask="true"
                            :feedback="false" :invalid="!!form.errors.password"
                            input-class="w-full px-4 py-3 rounded-xl border-surface-200 hover:border-primary-400 focus:ring-primary-500"
                            class="w-full" autocomplete="current-password" />
                        <small v-if="form.errors.password" class="text-red-500 font-medium">{{ form.errors.password
                            }}</small>
                    </div>

                    <div class="flex items-center justify-between mt-1">
                        <div class="flex items-center gap-3">
                            <Checkbox id="remember" v-model="form.remember" :binary="true" class="rounded-md" />
                            <label for="remember"
                                class="text-sm font-medium text-surface-700 cursor-pointer select-none">
                                Remember for 30 days
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <Button type="submit" label="Sign In"
                        class="w-full py-4 mt-2 rounded-xl text-base font-semibold tracking-wide transition-all duration-200 shadow-md hover:shadow-lg"
                        :loading="form.processing" />
                </form>

            </div>
        </div>
    </div>
</template>