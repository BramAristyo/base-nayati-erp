import { usePage } from '@inertiajs/vue3';
import { storeToRefs } from 'pinia';
import { ref, computed, watch, onMounted  } from 'vue';
import type {Ref} from 'vue';
import { route } from 'ziggy-js';
import { useAuthStore } from '@/stores/utility/useAuthStore';
import type { MenuItem } from '../../types/common/sidebar.types';

export function useSidebar(props: { collapsed: boolean }, emit: (event: 'update:collapsed', value: boolean) => void) {
    const searchQuery = ref('');
    const page = usePage();
    const authStore = useAuthStore();
    const { permissions: userPermissions } = storeToRefs(authStore);

    const menus = ref<MenuItem[]>([
        { label: 'Dashboard', icon: 'pi pi-objects-column', route: 'dashboard' },
        {
            label: 'System',
            icon: 'pi pi-cog',
            isOpen: false,
            items: [
                { label: 'User Management', route: 'utility.users.paginate', permission: 'utility.user.view' },
                { label: 'Role & Permissions', route: 'utility.roles.paginate', permission: 'utility.role.view' },
            ]
        }
    ]);

    const isRouteActive = (routeName?: string) => {
        if (!routeName) {
return false;
}

        const current = route().current();

        if (!current) {
return false;
}

        if (current === routeName) {
return true;
}

        if (routeName.endsWith('.paginate')) {
            const base = routeName.replace('.paginate', '');

            return current.startsWith(base);
        }

        return current.startsWith(routeName);
    };

    const syncExpandedState = () => {
        menus.value.forEach(menu => {
            if (menu.items) {
                const hasActiveChild = menu.items.some(child => isRouteActive(child.route));

                if (hasActiveChild) {
                    menu.isOpen = true;
                }
            }
        });
    };

    const toggleSubMenu = (clickedMenu: MenuItem) => {
        if (props.collapsed) {
            emit('update:collapsed', false);
        }

        const currentState = clickedMenu.isOpen;

        menus.value.forEach(menu => {
            if (menu.items) {
                menu.isOpen = false;
            }
        });

        clickedMenu.isOpen = !currentState;
    };

    const filteredMenus = computed(() => {
        const permittedMenus = menus.value.reduce((acc: MenuItem[], menu) => {
            const hasParentPermission = !menu.permission || (userPermissions.value && userPermissions.value.includes(menu.permission));

            if (menu.items) {
                const permittedChildren = menu.items.filter(child =>
                    !child.permission || (userPermissions.value && userPermissions.value.includes(child.permission))
                );

                if (permittedChildren.length > 0) {
                    acc.push({
                        ...menu,
                        isOpen: !!menu.isOpen,
                        items: permittedChildren
                    });
                }
            } else if (hasParentPermission) {
                acc.push({
                    ...menu,
                    items: undefined,
                    isOpen: false
                });
            }

            return acc;
        }, []);

        if (!searchQuery.value) {
            return permittedMenus;
        }

        const query = searchQuery.value.toLowerCase();

        return permittedMenus.reduce((acc: MenuItem[], menu) => {
            const matchesLabel = menu.label.toLowerCase().includes(query);
            const filteredChildren = menu.items?.filter(child =>
                child.label.toLowerCase().includes(query)
            );

            if (matchesLabel || (filteredChildren && filteredChildren.length > 0)) {
                acc.push({
                    ...menu,
                    isOpen: true,
                    items: matchesLabel ? menu.items : filteredChildren
                });
            }

            return acc;
        }, []);
    });

    watch(() => page.url, () => {
        syncExpandedState();
    }, { immediate: true });

    onMounted(() => {
        syncExpandedState();
    });

    return {
        searchQuery,
        filteredMenus,
        isRouteActive,
        toggleSubMenu
    };
}
