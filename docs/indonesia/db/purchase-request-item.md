---
Project: Nayati Inox ERP
Module: Purchasing
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Purchase Request Items (Detail)

Dokumen ini merinci pemetaan data dari database legacy ke sistem baru untuk detail item pada Purchase Request (DPR).

## 1. Tabel Sumber & Relasi
Pemetaan ini melibatkan tabel-tabel berikut:
- `dpr`: Detail Purchase Request (Tabel Utama)
- `view_stockvariant`: View Master Produk/Variant (Join via `kdbvar_id`)

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `dpr` | `iddpr` | `id` | Primary Key |
| `dpr` | `kdbvar_id` | `product_variant_id` | ID Variant Produk |
| `dpr` | `kd_brg` | `product_code` | Kode Produk |
| `view_stockvariant` | `variantname` | `variant_name` | Nama Variant Produk |
| `dpr` | `nama` | `product_name` | Nama Produk (Snapshot) |
| `dpr` | `qty` | `quantity` | Jumlah (Float) |
| `dpr` | `harga` | `price` | Harga per unit (Float) |
| `dpr` | `sat` | `unit` | Satuan (e.g., PCS, KG) |
| `dpr` | `tglpakai` | `usage_date` | Tanggal Penggunaan (Sanitized) |
| `dpr` | `nmsupplier` | `supplier_name` | Nama Supplier yang disarankan |
| `dpr` | `merk` | `brand` | Merk/Brand produk |
| `dpr` | `ket` | `remarks` | Keterangan/Catatan item |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Sanitasi Tanggal
Nilai `'0000-00-00'` dikonversi menjadi `null`.
```php
if ($date === '0000-00-00') return null;
```

### 3.2 Pembersihan String
Semua kolom teks (nama, unit, brand, keterangan) melalui proses `trim()` untuk menghapus spasi kosong dari tipe data `CHAR`.

## 4. Anomali & Catatan (Anomalies & Notes)
- **Relasi via Nota**: Item dihubungkan ke header menggunakan kolom `nota` (string), mengikuti struktur database legacy.
- **Urutan**: Data diurutkan berdasarkan `id` secara descending untuk memastikan item terbaru muncul di atas jika ada input simultan.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
