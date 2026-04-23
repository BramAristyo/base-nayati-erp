export interface ChildMenu {
    label: string;
    route?: string;
    permission?: string;
}

export interface MenuItem {
    label: string;
    icon: string;
    route?: string;
    permission?: string;
    items?: ChildMenu[];
    isOpen?: boolean;
}
