---
Project: Nayati Inox ERP
Module: Purchasing
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Purchase Order

Dokumen ini merinci pemetaan data dari database legacy ke sistem baru untuk modul Purchase Order (PO).

## 1. Tabel Sumber & Relasi
Pemetaan ini melibatkan tabel-tabel berikut:
- `hpo`: Header Purchase Order (Tabel Utama)
- `supplier`: Master Supplier (Join via `KD_SUPP`)
- `mdept`: Master Department (Join via `kddep`)

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `hpo` | `id` | `id` | Primary Key |
| `hpo` | `kd_cab` | `branch_code` | Kode Cabang |
| `hpo` | `nota` | `purchase_order_number` | Nomor unik Purchase Order |
| `hpo` | `tgl` | `date` | Tanggal PO |
| `hpo` | `approve` | `status` | `'Y'` → `true` (Boolean) |
| `hpo` | `userapp` | `approved_by` | Nama/ID user penyetuju |
| `hpo` | `tglapp` | `approval_date` | Tanggal persetujuan |
| `hpo` | `IPPN` | `is_inclusive_tax` | `'Y'` → `true` (Boolean) |
| `hpo` | `PPN` | `tax_percentage` | Persentase PPN |
| `hpo` | `TOTAL` | `grand_total` | Total keseluruhan (Mata uang asli) |
| `hpo` | `TOTALRP` | `grand_total_rupiah` | Total keseluruhan dalam IDR |
| `hpo` | `JUMLAH` | `sub_total` | Total sebelum diskon & pajak |
| `hpo` | `JUMLAHRP` | `sub_total_rupiah` | Sub total dalam IDR |
| `hpo` | `DPP` | `tax_base_amount` | Dasar Pengenaan Pajak |
| `hpo` | `DPPRP` | `tax_base_amount_rupiah` | Dasar Pengenaan Pajak dalam IDR |
| `hpo` | `TPPN` | `tax_amount` | Nominal PPN |
| `hpo` | `TPPNRP` | `tax_amount_rupiah` | Nominal PPN dalam IDR |
| `hpo` | `DISC1` | `discount_percentage_1` | Persentase diskon 1 |
| `hpo` | `DISCRP1` | `discount_amount_1` | Nominal diskon 1 dalam IDR |
| `hpo` | `DISC2` | `discount_percentage_2` | Persentase diskon 2 |
| `hpo` | `DISCRP2` | `discount_amount_2` | Nominal diskon 2 dalam IDR |
| `hpo` | `DISC3` | `discount_percentage_3` | Persentase diskon 3 |
| `hpo` | `DISCRP3` | `discount_amount_3` | Nominal diskon 3 dalam IDR |
| `hpo` | `JNSPO` | `category` | `1` → `'Standard'`, else → `'Non Standard'` |
| `hpo` | `KD_SUPP` | `supplier_code` | Kode Supplier |
| `hpo` | `GP` | `is_general_purchase` | `'Y'` → `true` (Boolean) |
| `hpo` | `TGL_KRM` | `delivery_date` | Tanggal pengiriman yang dijadwalkan |
| `hpo` | `JT_TEMPO` | `due_date` | Tanggal jatuh tempo pembayaran |
| `hpo` | `inventory` | `inventory_type` | `'FG'` → `'Finish Goods'`, else → `'Raw Materials'` |
| `hpo` | `REPACKING` | `is_repacking` | `'Y'` → `true` (Boolean) |
| `hpo` | `SBYR` | `supplier_payment_term` | Syarat pembayaran |
| `hpo` | `TKRM` | `supplier_delivery_term` | Syarat pengiriman |
| `hpo` | `L_I` | `is_local_purchase` | `'L'` → `true` (Local), else → `false` (Import) |
| `hpo` | `KONS` | `is_consignment` | `'Y'` → `true` (Boolean) |
| `hpo` | `RATE` | `currency_rate` | Nilai tukar mata uang |
| `hpo` | `M_UANG` | `currency_code` | Kode mata uang (e.g., IDR, USD) |
| `hpo` | `USER` | `created_by` | User pembuat entry |
| `hpo` | `TGL_UPDATE` | `updated_at` | Waktu update terakhir |
| `hpo` | `TGLENTRY` | `created_at` | Waktu pembuatan entry |
| `supplier` | `NAMA` | `supplier_name` | Nama Supplier (dilakukan `trim()`) |
| `mdept` | `ket` | `department_name` | Nama departemen pemesan |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Konversi Boolean
Berbagai flag string pada database legacy dikonversi menjadi boolean untuk konsistensi.

### 3.2 Pemetaan Tipe & Kategori
Mapping eksplisit untuk memberikan informasi yang lebih deskriptif.

### 3.3 Pembersihan Data (Data Cleaning)
Khusus untuk `supplier_name`, dilakukan pembersihan karakter kutipan ganda (`"`) dan spasi kosong.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
