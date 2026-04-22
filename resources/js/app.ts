import { createInertiaApp } from '@inertiajs/vue3';
import { definePreset } from '@primeuix/themes';
import Aura from '@primeuix/themes/aura';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

const Noir = definePreset(Aura, {
    semantic: {
        primary: {
            50: '{zinc.50}',
            100: '{zinc.100}',
            200: '{zinc.200}',
            300: '{zinc.300}',
            400: '{zinc.400}',
            500: '{zinc.500}',
            600: '{zinc.600}',
            700: '{zinc.700}',
            800: '{zinc.800}',
            900: '{zinc.900}',
            950: '{zinc.950}',
        },
        colorScheme: {
            light: {
                primary: {
                    color: '{zinc.950}',
                    inverseColor: '#ffffff',
                    hoverColor: '{zinc.900}',
                    activeColor: '{zinc.800}',
                },
                highlight: {
                    background: '{zinc.950}',
                    focusBackground: '{zinc.700}',
                    color: '#ffffff',
                    focusColor: '#ffffff',
                },
            },
            dark: {
                primary: {
                    color: '{zinc.50}',
                    inverseColor: '{zinc.950}',
                    hoverColor: '{zinc.100}',
                    activeColor: '{zinc.200}',
                },
                highlight: {
                    background: 'rgba(250, 250, 250, .16)',
                    focusBackground: 'rgba(250, 250, 250, .24)',
                    color: 'rgba(255,255,255,.87)',
                    focusColor: 'rgba(255,255,255,.87)',
                },
            },
        },
    },
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(
        `./pages/${name}.vue`,
        import.meta.glob<DefineComponent>('./pages/**/*.vue')
    ),
    progress: {
        color: '#4B5563',
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(PrimeVue, {
                theme: {
                    preset: Noir,
                    options: {
                        darkModeSelector: 'none',
                    },
                },
                pt: {
                    column: {
                        headercell: { class: 'text-[14px]! font-bold! uppercase!' },
                        bodycell: { class: 'text-[14px]!' }
                    },
                    datatable: {
                        paginator: {
                            root: { class: 'py-1! border-t! border-gray-100! bg-white!' }
                        }
                    },
                    paginator: {
                        root: { class: 'py-0! gap-1!' },
                        pcFirstButton: { root: { class: 'w-6! h-6! text-[9px]! rounded-full!' } },
                        pcPrevButton: { root: { class: 'w-6! h-6! text-[9px]! rounded-full!' } },
                        pcNextButton: { root: { class: 'w-6! h-6! text-[9px]! rounded-full!' } },
                        pcLastButton: { root: { class: 'w-6! h-6! text-[9px]! rounded-full!' } },
                        pcPageButton: { 
                            root: ({ context }: any) => ({ 
                                class: [
                                    'w-6! h-6! text-[9px]! font-bold! rounded-full!',
                                    context.active ? 'bg-black! text-white!' : 'text-gray-800! hover:bg-gray-100!'
                                ] 
                            }) 
                        },
                        pcRowPerPageDropdown: {
                            root: { class: 'h-6! text-[9px]! font-bold! border-gray-100! shadow-none! bg-gray-50/50! rounded-md!' },
                            label: { class: 'px-2! py-0!' }
                        }
                    }
                }
            })
            .use(ToastService)
            .directive('tooltip', Tooltip)
            .use(pinia)
            .use(ZiggyVue)
            .mount(el);
    }
});
