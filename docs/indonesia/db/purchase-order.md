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
| `hpo` | `JUMLAH` | `sub_total` | Total sebelum diskon & pajak |
| `hpo` | `JUMLAHRP` | `sub_total_rupiah` | Sub total dalam IDR |
| `hpo` | `TOTALRP` | `grand_total_rupiah` | Grand total dalam IDR |
| `hpo` | `DISC1` | `disc1_percentage` | Persentase diskon 1 |
| `hpo` | `DISCRP1` | `disc1_amount_rupiah` | Nominal diskon 1 dalam IDR |
| `hpo` | `TPPN` | `tax_ppn_amount` | Nominal PPN |
| `hpo` | `DPP` | `tax_base_amount` | Dasar Pengenaan Pajak |
| `hpo` | `USER` | `created_by` | User pembuat entry |
| `hpo` | `TGL_UPDATE` | `updated_at` | Waktu update terakhir |
| `hpo` | `TGLENTRY` | `created_at` | Waktu pembuatan entry |
| `hpo` | `PRICECON` | `price_condition` | Kondisi harga (Informasi Cetak) |
| `hpo` | `PACKING` | `packing_information` | Informasi pengemasan |
| `hpo` | `REMARK1` | `remark1` | Catatan tambahan 1 |
| `hpo` | `biaya` | `other_expenses` | Biaya tambahan lainnya |
| `supplier` | `NAMA` | `supplier_name` | Nama Supplier (dilakukan `trim()`) |
| `supplier` | `ALAMAT` | `supplier_address` | Alamat lengkap supplier |
| `mdept` | `ket` | `department_name` | Nama departemen pemesan |
| `mdept` | `kddep` | `department_code` | Kode departemen |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Status Boolean
Beberapa field legacy menggunakan karakter `'Y'` atau `'L'` yang dikonversi menjadi tipe data boolean pada level aplikasi untuk mempermudah logika di frontend.
- `status` (`approve`): `'Y'` menjadi `true`.
- `is_inclusive_tax` (`IPPN`): `'Y'` menjadi `true`.
- `is_consignment` (`KONS`): `'Y'` menjadi `true`.
- `is_local_purchase` (`L_I`): `'L'` menjadi `true`.

### 3.2 Tipe Inventori & Kategori
Mapping eksplisit untuk memberikan informasi yang lebih deskriptif:
- **Inventory Type**: Jika kode adalah `'FG'`, maka ditampilkan sebagai `'Finish Goods'`. Selain itu (biasanya `'RM'`), ditampilkan sebagai `'Raw Materials'`.
- **PO Category**: Jika `JNSPO` bernilai `1`, maka dikategorikan sebagai `'Standard'`. Nilai lainnya dikategorikan sebagai `'Non Standard'`.

### 3.3 Pembersihan Data (Data Cleaning)
Khusus untuk `supplier_name`, dilakukan pembersihan karakter kutipan ganda (`"`) dan spasi kosong di awal/akhir string yang sering terbawa dari sistem legacy.

## 4. Anomali & Catatan (Anomalies & Notes)

- **Sanitasi Tanggal:** Field tanggal dengan nilai `'0000-00-00'` dikonversi menjadi `null` untuk mencegah error pada parsing Carbon/Date.
- **Unified Query:** Menggunakan `baseQuery()` untuk memastikan konsistensi field antara `paginate()` dan `find()`.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
