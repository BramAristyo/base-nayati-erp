import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import PrimeVue from 'primevue/config';
import Material from '@primeuix/themes/material';
import { ZiggyVue } from 'ziggy-js';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createPinia } from 'pinia';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(
        `./Pages/${name}.vue`,
        import.meta.glob<DefineComponent>('./Pages/**/*.vue')
    ),
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Material,
                },
                options: {
                    darkModeSelector: '.dark',
                }
            })
            .use(pinia)
            .use(ZiggyVue)
            .mount(el);
    }
});
