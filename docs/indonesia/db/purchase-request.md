---
Project: Nayati Inox ERP
Module: Purchasing
Version: 1.0.0
Last Updated: 2026-05-22
Status: Review
---

# Dokumentasi Mapping Database: Purchase Request

Dokumen ini merinci pemetaan data dari database legacy ke sistem baru untuk modul Purchase Request.

## 1. Tabel Sumber & Relasi
Pemetaan ini melibatkan tabel-tabel berikut:
- `hpr`: Header Purchase Request (Tabel Utama)
- `mdept`: Master Department (Join via `kddep`)
- `mkar`: Master Karyawan/Employee (Join via `nik`)
- `supplier`: Master Supplier (Join via `kd_supp`)

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `hpr` | `idhpr` | `id` | Primary Key |
| `hpr` | `nota` | `purchase_request_number` | Nomor unik PR |
| `hpr` | `tgl` | `date` | Tanggal PR |
| `hpr` | `approve` | `status` | `'Y'` → `true`, selain itu `false` |
| `hpr` | `tgldel` | `delivery_date` | Tanggal pengiriman |
| `hpr` | `jnsbudget` | `budget_type` | Jenis budget |
| `hpr` | `kd_cab` | `branch_code` | Kode cabang |
| `hpr` | `approveby` | `approved_by` | Standardized from `approver_name` |
| `hpr` | `tglapp` | `approval_date` | Tanggal persetujuan |
| `hpr` | `inventory` | `inventory_type` | `'FG'` → `'Finish Goods'`, selain itu `'Raw Materials'` |
| `hpr` | `jnspack` | `packaging_type` | Dilakukan `trim()` pada string |
| `hpr` | `tglentry` | `created_at` | Waktu input data |
| `hpr` | `kddep` | `department_code` | Kode departemen pemohon |
| `hpr` | `gp` | `is_general_purchase` | `'Y'` → `true` |
| `hpr` | `kdlok` | `warehouse_code` | Lokasi gudang |
| `hpr` | `kd_supp` | `supplier_code` | Kode supplier terkait |
| `hpr` | `instplace` | `installation_place` | Tempat instalasi |
| `hpr` | `adress` | `installation_address` | Alamat instalasi (Typo di legacy: `adress`) |
| `hpr` | `layout` | `is_layout` | Flag layout (Boolean) |
| `hpr` | `nik` | `employee_nik` | NIK pemohon |
| `hpr` | `user` | `created_by` | User yang membuat entry |
| `mkar` | `ket` | `employee_name` | Nama karyawan dari join `mkar` |
| `mdept` | `ket` | `department_name` | Nama departemen dari join `mdept` |
| `supplier` | `nama` | `supplier_name` | Nama supplier dari join `supplier` |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Status Approval
Status dikonversi menjadi boolean untuk konsistensi di frontend.
```php
$item->status = $item->status === 'Y' ? true : false;
```

### 3.2 Tipe Inventori
Hanya mengenal dua kategori utama berdasarkan kode 'FG'.
- `'FG'` dipetakan ke `'Finish Goods'`.
- Selain `'FG'` (termasuk `'RM'`) dipetakan ke `'Raw Materials'`.

### 3.3 Pembersihan String
Kolom `jnspack` (packaging_type) melalui proses `trim()` untuk menghapus spasi kosong yang sering muncul pada tipe data `CHAR` di database legacy.

## 4. Anomali & Catatan (Anomalies & Notes)

- **Standardized Name Properti:** Kolom `approveby` telah diseragamkan menjadi `approved_by` di seluruh metode.
- **Typo Legacy:** Kolom `adress` pada tabel `hpr` telah dikoreksi menjadi `installation_address` pada level aplikasi.
- **Unified Query:** Menggunakan `baseQuery()` untuk memastikan konsistensi field antara `paginate()` dan `find()`.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
