import { router } from '@inertiajs/vue3';
import { useConfirm } from 'primevue/useconfirm';

export function useCancelConfirm() {
    const confirm = useConfirm();

    const confirmCancel = (targetRoute: string) => {
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
                size: 'small',
            },
            acceptProps: {
                label: 'Yes, discard',
                severity: 'danger',
                size: 'small',
            },
            accept: () => {
                router.get(targetRoute);
            },
        });
    };

    return {
        confirmCancel,
    };
}
