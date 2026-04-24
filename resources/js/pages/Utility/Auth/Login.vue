<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import { ref } from 'vue';

const form = useForm({
    email: '',
    password: '',
    remember: false as boolean,
});

const showInfo = ref(false);

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="min-h-screen flex w-full bg-background">
        <!-- Left Side: Background Image (Hidden on smaller screens) -->
        <div class="hidden lg:flex relative w-1/2 bg-primary overflow-hidden text-primary-foreground">
            <img src="/images/bg.webp" alt="Background" class="absolute inset-0 w-full h-full " />
            <div class="relative z-10 flex flex-col justify-end p-12 w-full h-full">
            </div>

            <!-- Bottom Left Text -->
            <div class="absolute bottom-8 left-12 z-20 text-muted-foreground text-sm font-medium">
                <p>&copy; 2026 PT. Inox Metal Asia. All rights reserved.</p>
                <p class="mt-0.5 opacity-80">Created by IT Nayati</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12 relative">

            <!-- Info Button -->
            <div class="absolute top-6 right-6">
                <Button icon="pi pi-info-circle" rounded text severity="secondary" aria-label="Information"
                    @click="showInfo = true" />
            </div>

            <div class="w-full max-w-md flex flex-col pt-8 pb-12 px-6 sm:px-10">

                <!-- Logo & Header -->
                <div class="flex flex-col items-center text-center gap-2 mb-10">
                    <img src="/images/logo_ima.png" alt="IMA Logo" class="h-10 w-auto mb-4" />
                    <h2 class="text-3xl font-bold text-foreground tracking-tight">Welcome back</h2>
                    <p class="text-muted-foreground font-medium">Please enter your details to sign in.</p>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <!-- Email Field -->
                    <div class="flex flex-col gap-2">
                        <label for="email" class="text-sm font-semibold text-foreground">Email</label>
                        <InputText id="email" v-model="form.email" type="email" placeholder="Enter your email"
                            class="w-full px-4 py-3 bg-muted/50 hover:bg-accent/50 focus:ring-1 focus:ring-primary transition-all duration-200"
                            :invalid="!!form.errors.email" autocomplete="email" size="small" />
                        <small v-if="form.errors.email" class="text-destructive font-medium">{{ form.errors.email }}</small>
                    </div>

                    <!-- Password Field -->
                    <div class="flex flex-col gap-2">
                        <label for="password" class="text-sm font-semibold text-foreground">Password</label>
                        <Password id="password" v-model="form.password" placeholder="••••••••" :toggleMask="true"
                            :feedback="false" :invalid="!!form.errors.password"
                            input-class="w-full px-4 py-3 rounded-xl border-none bg-muted/50 hover:bg-accent/50 focus:ring-1 focus:ring-primary transition-all duration-200"
                            class="w-full" autocomplete="current-password" size="small" />
                        <small v-if="form.errors.password" class="text-destructive font-medium">{{ form.errors.password
                            }}</small>
                    </div>

                    <div class="flex items-center justify-between mt-1">
                        <div class="flex items-center gap-3">
                            <Checkbox id="remember" v-model="form.remember" :binary="true" class="rounded-md" />
                            <label for="remember"
                                class="text-sm font-medium text-foreground cursor-pointer select-none">
                                Remember for 30 days
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <Button type="submit" label="Sign In"
                        class="w-full py-4 mt-2 rounded-xl text-base font-semibold tracking-wide transition-all duration-200 shadow-md hover:shadow-lg"
                        :loading="form.processing" size="small" />
                </form>

            </div>
        </div>

        <!-- Info Dialog -->
        <Dialog v-model:visible="showInfo" modal header="User Guide" :style="{ width: '30rem' }"
            :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
            <div class="text-muted-foreground flex flex-col gap-4 mb-6 text-sm leading-relaxed">
                <p>
                    <strong>Welcome to the ns-inox web version!</strong>
                </p>
                <ul class="list-disc pl-5 flex flex-col gap-2">
                    <li>
                        For your email, use your old username in all lowercase followed by <strong>@ns.inox</strong>.
                        For example, if your old username was <em>JOHN</em>, your new email is
                        <strong>john@ns.inox</strong>.
                        The default password is <strong>password</strong>.
                    </li>
                    <li>
                        Please note that this web application is currently under development up to the
                        <strong>Purchasing Module</strong>.
                    </li>
                </ul>
                <p>
                    If you encounter any issues, please call the IT Nayati Team immediately. Thank you.
                </p>
            </div>
            <div class="flex justify-end gap-2">
                <Button type="button" label="Close" severity="secondary" @click="showInfo = false" size="small" />
            </div>
        </Dialog>
    </div>
</template>