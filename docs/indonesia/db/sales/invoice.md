---
Project: Nayati Inox ERP
Module: Sales (Legacy Mapping - Invoice)
Version: 1.1.0
Last Updated: 2026-04-28
Status: Final
---

# Invoice Legacy Mapping

## 1. Overview
Dokumentasi ini merinci pemetaan data dari tabel legacy `minv` ke entitas Invoice dalam sistem baru.

## 2. Tabel & Mapping Kolom

- **Tabel Legacy**: `minv`
- **Primary Key**: `IDMINV`
- **Joins**:
    - `leftJoin('custom as c', 'minv.kdcust', '=', 'c.kd_cust')`: Menghubungkan ke master customer untuk mendapatkan nama resmi.

| Legacy Column | Standardized Alias | Data Type | Deskripsi/Transformasi |
| :--- | :--- | :--- | :--- |
| `minv.IDMINV` | `id` | Integer | Primary Key |
| `minv.NOIN` | `invoice_number` | String | Nomor Invoice |
| `minv.TGI` | `date` | Date | Tanggal Invoice |
| `minv.kdcab` | `branch_code` | String | Kode Cabang |
| `minv.NOP` | `sales_order_number` | String | Referensi Nomor Sales Order |
| `minv.KDCUST` | `customer_code` | String | Kode Customer |
| `c.NAMA` | `customer_name` | String | Nama Customer dari Master |
| `minv.NAMA` | `created_by` | String | Nama Pembuat Dokumen |
| `minv.TOTAL` | `total` | Float | Total Nilai Akhir (Netto) |
| `minv.STOT` | `subtotal` | Float | Nilai Sebelum Pajak & Diskon |
| `minv.PPN` | `tax_amount` | Float | **Catatan**: Di sini PPN menyimpan nilai nominal (bukan persentase) |
| `minv.DISC` | `discount_percentage` | Float | Persentase Diskon Utama |
| `minv.TDISC` | `discount_amount` | Float | Nominal Diskon Utama |
| `minv.DP` | `down_payment` | Float | Persentase Uang Muka |
| `minv.TDP` | `down_payment_amount`| Float | Nominal Uang Muka |
| `minv.BIAYA` | `installation_cost` | Float | Biaya Instalasi |
| `minv.BKIRIM` | `delivery_cost` | Float | Biaya Pengiriman |
| `minv.BPACKING` | `packing_cost` | Float | Biaya Packing |
| `minv.APPROVE1` | `status` | Boolean | `Y` -> `true` |
| `minv.TGLUPDATE` | `updated_at` | DateTime | Waktu Pembaruan Terakhir |

## 3. Logika Filtering & Sorting

### Filtering
- **Search**: Mencari pada `minv.NOIN`, `c.NAMA`, dan `minv.KDCUST`.
- **Date**: Berdasarkan `minv.TGI` (Tanggal Invoice).
- **Status (Approval Status)**: 
    - `pending`: `minv.APPROVE1 != 'Y'`
    - `processed`: `minv.APPROVE1 = 'Y'`

### Sorting (Sortable Fields)
- `invoice_number` -> `minv.NOIN`
- `date` -> `minv.TGI`
- `customer_name` -> `c.NAMA`
- `branch_code` -> `minv.kdcab`
- `status` -> `minv.APPROVE1`
- `updated_at` -> `minv.TGLUPDATE`

## 4. Anomali & Temuan Teknis
- **Semantik PPN**: Berbeda dengan modul Purchasing (dimana `PPN` adalah persentase), pada modul Sales `minv.PPN` menyimpan nilai **nominal pajak**.
- **Kolom Tanggal**: Tabel ini menggunakan kolom `TGI` (Tanggal Invoice), berbeda dengan standar `TGL` di tabel transaksi lainnya.
- **Relasi NOP**: Kolom `NOP` merujuk pada `sales_order_number`.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
