---
Project: Nayati Inox ERP
Module: Sales (Legacy Mapping - Delivery Order)
Version: 1.1.0
Last Updated: 2026-04-28
Status: Final
---

# Delivery Order Legacy Mapping

## 1. Overview
Dokumentasi ini merinci pemetaan data dari tabel legacy `hdo` ke entitas Delivery Order (Surat Jalan) dalam sistem baru.

## 2. Tabel & Mapping Kolom

- **Tabel Legacy**: `hdo`
- **Primary Key**: `ID`

| Legacy Column | Standardized Alias | Data Type | Deskripsi/Transformasi |
| :--- | :--- | :--- | :--- |
| `hdo.ID` | `id` | Integer | Primary Key |
| `hdo.NOTA` | `delivery_order_number`| String | Nomor Surat Jalan (DO) |
| `hdo.TGL` | `date` | Date | Tanggal Pembuatan DO |
| `hdo.NOP` | `sales_order_number` | String | Referensi Nomor Sales Order (Relasi ke `mo.nop`) |
| `hdo.NMPROJECT` | `customer_name` | String | Nama Customer/Proyek (Langsung dari tabel transaksi) |
| `hdo.KD_CUST` | `customer_code` | String | Kode Customer |
| `hdo.KATEGORY` | `category` | String | Kategori atau Klasifikasi DO |
| `hdo.TGLDEL` | `delivery_date` | Date | Tanggal Rencana/Aktual Pengiriman |
| `hdo.KD_CAB` | `branch_code` | String | Kode Cabang |
| `hdo.APPROVE` | `status` | Boolean | `Y` -> `true` (Processed) |
| `hdo.TGLAPP` | `approval_date` | Date | Tanggal Persetujuan/Verifikasi |

## 3. Logika Filtering & Sorting

### Filtering
- **Search**: Mencari pada `hdo.NOTA`, `hdo.NOP`, `hdo.NMPROJECT`, dan `hdo.KD_CUST`.
- **Date**: Berdasarkan `hdo.TGL` (Tanggal DO).
- **Status (Approval Status)**: 
    - `pending`: `hdo.APPROVE != 'Y'`
    - `processed`: `hdo.APPROVE = 'Y'`

### Sorting (Sortable Fields)
- `delivery_order_number` -> `hdo.NOTA`
- `date` -> `hdo.TGL`
- `sales_order_number` -> `hdo.NOP`
- `customer_name` -> `hdo.NMPROJECT`
- `customer_code` -> `hdo.KD_CUST`
- `category` -> `hdo.KATEGORY`
- `delivery_date` -> `hdo.TGLDEL`
- `branch_code` -> `hdo.KD_CAB`
- `status` -> `hdo.APPROVE`
- `approval_date` -> `hdo.TGLAPP`

## 4. Anomali & Temuan Teknis
- **Sumber Nama Customer**: Tabel ini menyimpan `NMPROJECT` secara redundan. Tidak dilakukan join ke master `custom`, sehingga risiko ketidaksinkronan data nama cukup tinggi jika ada perubahan di master.
- **Relasi NOP**: Berbeda dengan tabel `mo`, kolom `NOP` di sini berfungsi sebagai **Foreign Key** yang merujuk pada `sales_order_number`.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
