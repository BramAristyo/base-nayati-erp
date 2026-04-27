export interface AccountType {
    id: number;
    code: string;
    name: string;
}

export interface AccountTypeResponse {
    status: boolean;
    message: string;
    data: AccountType[];
}
