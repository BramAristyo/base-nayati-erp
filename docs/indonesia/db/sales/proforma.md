---
Project: Nayati Inox ERP
Module: Sales (Legacy Mapping - Proforma)
Version: 1.1.0
Last Updated: 2026-04-28
Status: Final
---

# Proforma Invoice Legacy Mapping

## 1. Overview
Dokumentasi ini merinci pemetaan data dari tabel legacy `mproforma` ke entitas Proforma Invoice dalam sistem baru.

## 2. Tabel & Mapping Kolom

- **Tabel Legacy**: `mproforma`
- **Primary Key**: `ID`

| Legacy Column | Standardized Alias | Data Type | Deskripsi/Transformasi |
| :--- | :--- | :--- | :--- |
| `mproforma.ID` | `id` | Integer | Primary Key |
| `mproforma.NOIN` | `proforma_number` | String | Nomor Proforma Invoice |
| `mproforma.No_kwitansi`| `invoice_number` | String | Referensi Nomor Kwitansi atau Invoice Final |
| `mproforma.TGI` | `date` | Date | Tanggal Proforma |
| `mproforma.KDCAB` | `branch_code` | String | Kode Cabang |
| `mproforma.NOP` | `sales_order_number` | String | Referensi Nomor Sales Order |
| `mproforma.TOTSO` | `bruto` | Float | Total Bruto dari Sales Order |
| `mproforma.TDISC` | `discount_amount_1`| Float | Nominal Diskon 1 |
| `mproforma.TDISC1` | `discount_amount_2`| Float | Nominal Diskon 2 |
| `mproforma.BINSTALL` | `installation_cost` | Float | Biaya Instalasi |
| `mproforma.TERM` | `payment_term` | String | Syarat Pembayaran (Textual) |
| `mproforma.PPN` | `tax_amount` | Float | Nominal Pajak (PPN) |
| `mproforma.TDP` | `netto` | Float | Total Netto setelah DP/Diskon |
| `mproforma.KDCUST` | `customer_code` | String | Kode Customer |
| `mproforma.NMCUST` | `customer_name` | String | Nama Customer (Langsung dari tabel) |
| `mproforma.DP` | `down_payment_pct` | Float | Persentase Uang Muka |
| `mproforma.approve1` | `status` | Boolean | `Y` -> `true` |

## 3. Logika Filtering & Sorting

### Filtering
- **Search**: Mencari pada `mproforma.NOIN`, `mproforma.NMCUST`, dan `mproforma.KDCUST`.
- **Date**: Berdasarkan `mproforma.TGI` (Tanggal Proforma).
- **Status (Approval Status)**: 
    - `pending`: `mproforma.approve1 != 'Y'`
    - `processed`: `mproforma.approve1 = 'Y'`

### Sorting (Sortable Fields)
- `proforma_number` -> `mproforma.NOIN`
- `invoice_number` -> `mproforma.No_kwitansi`
- `date` -> `mproforma.TGI`
- `customer_name` -> `mproforma.NMCUST`
- `customer_code` -> `mproforma.KDCUST`
- `branch_code` -> `mproforma.KDCAB`
- `status` -> `mproforma.approve1`

## 4. Anomali & Temuan Teknis
- **Sumber Nama Customer**: Mirip dengan Delivery Order, tabel ini menyimpan `NMCUST` secara langsung. Ini bisa menyebabkan inkonsistensi jika data di master `custom` berubah.
- **Relasi NOP**: Merujuk pada `sales_order_number`.
- **Naming Conv**: Kolom PPN di sini juga menyimpan nominal, konsisten dengan tabel `minv` (Invoice).

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
