---
Project: Nayati Inox ERP
Module: Sales (Legacy Mapping - Sales Order)
Version: 1.1.0
Last Updated: 2026-04-28
Status: Final
---

# Sales Order Legacy Mapping

## 1. Overview
Dokumentasi ini merinci pemetaan data dari tabel legacy `mo` ke entitas Sales Order dalam sistem baru.

## 2. Tabel & Mapping Kolom

- **Tabel Legacy**: `mo`
- **Primary Key**: `id`
- **Joins**:
    - `leftJoin('custom as c', 'c.kd_cust', '=', 'mo.KDCUST')`: Digunakan untuk mengambil nama customer yang deskriptif dari tabel master customer.

| Legacy Column | Standardized Alias | Data Type | Deskripsi/Transformasi |
| :--- | :--- | :--- | :--- |
| `mo.id` | `id` | Integer | Primary Key |
| `mo.nop` | `sales_order_number` | String | Nomor Sales Order (Makna: Unik untuk tabel ini) |
| `mo.NOPI` | `invoice_number` | String | Nomor Invoice terkait |
| `mo.KDCUST` | `customer_code` | String | Kode Customer |
| `c.NAMA` | `customer_name` | String | Nama Customer dari Master (Trimmed) |
| `mo.KDCAB` | `branch_code` | String | Kode Cabang |
| `mo.cperson` | `contact_person` | String | Orang yang dapat dihubungi |
| `mo.KDSEG` | `customer_segment` | String | Segmentasi Customer |
| `mo.KET` | `project_name` | String | Nama Proyek/Keterangan (Trimmed) |
| `mo.TGS` | `date` | Date | Tanggal Sales Order |
| `mo.TGD` | `delivery_date` | Date | Tanggal Rencana Pengiriman |
| `mo.JTHTMP` | `due_date` | Date | Tanggal Jatuh Tempo |
| `mo.VERIFIKASI` | `status` | Boolean | `Y` -> `true` (Approved) |
| `mo.LE` | `order_type` | String | Tipe Order (Lokal/Ekspor) |
| `mo.IEPPN` | `tax_type` | String | Tipe Pajak (Include/Exclude) |
| `mo.KDCUR` | `currency_code` | String | Kode Mata Uang |
| `mo.NILAI` | `currency_rate` | Float | Kurs Mata Uang |
| `mo.TERM1` | `term_1` | Float | Nilai Termin Pembayaran 1 |
| `mo.EVEN1` | `term_desc_1` | String | Deskripsi Termin 1 |
| `mo.DP` | `down_payment` | Float | Nilai Uang Muka |
| `mo.NIK` | `salesman_nik` | String | NIK Salesman |
| `mo.PENG` | `delivery_term` | String | Syarat Pengiriman |
| `mo.TOT` | `net_price` | Float | Total Nilai Netto |
| `mo.DISC` | `discount_percentage` | Float | Persentase Diskon 1 |
| `mo.TDISC` | `discount_amount` | Float | Nominal Diskon 1 |
| `mo.BIAYA` | `installation_price` | Float | Biaya Instalasi |
| `mo.TGL_UPDATE` | `updated_at` | DateTime | Waktu Pembaruan Terakhir |

## 3. Logika Filtering & Sorting

### Filtering
- **Search**: Mencari pada `mo.nop`, `c.NAMA`, `mo.KDCUST`, dan `mo.KET`.
- **Date**: Berdasarkan `mo.TGS` (Tanggal Sales Order).
- **Status**: 
    - `approved`: `mo.VERIFIKASI = 'Y'`
    - `not_approved`: `mo.VERIFIKASI != 'Y'`

### Sorting (Sortable Fields)
- `sales_order_number` -> `mo.nop`
- `date` -> `mo.TGS`
- `customer_code` -> `mo.KDCUST`
- `branch_code` -> `mo.KDCAB`
- `project_name` -> `mo.KET`
- `delivery_date` -> `mo.TGD`
- `due_date` -> `mo.JTHTMP`

## 4. Anomali & Temuan Teknis
- **Semantik `LE`**: Kolom `mo.LE` di sini digunakan sebagai `order_type` (Lokal/Ekspor). Berbeda dengan tabel lain yang mungkin menggunakan alias `is_local_purchase`.
- **Nomor Dokumen**: Kolom `nop` pada tabel ini adalah Primary Document Number, sedangkan pada tabel lain (DO, Invoice, Shipment) `NOP` biasanya adalah kolom referensi (Foreign Key).

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
