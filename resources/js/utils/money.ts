/**
 * Formats a number as Indonesian Rupiah (IDR).
 * 
 * @param val - The number to format
 * @returns Formatted currency string
 */
export const formatCurrency = (val: number | string | null): string => {
    const value = typeof val === 'string' ? parseFloat(val) : val;
    
    if (value === null || isNaN(value)) return 'Rp 0';

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
};
