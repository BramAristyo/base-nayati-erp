---
Project: Nayati Inox ERP
Module: Purchasing
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Receiving (Penerimaan Barang)

Dokumen ini merinci pemetaan data dari database legacy ke sistem baru untuk modul Receiving (LPB - Laporan Penerimaan Barang).

## 1. Tabel Sumber & Relasi
Pemetaan ini melibatkan tabel-tabel berikut:
- `hbeli`: Header Penerimaan Barang (Tabel Utama)
- `supplier` (alias `s`): Master Supplier (Join via `kd_supp`)
- `mdept` (alias `d`): Master Department (Join via `kddep`)
- `warehouses` (alias `w`): Master Gudang (Join via `kdlok`)
- `hpo` (alias `po`): Header Purchase Order (Join via `noop` ke `nota`)
- `dbeli`: Detail Penerimaan Barang (Digunakan dalam subquery)

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `hbeli` | `idhbeli` | `id` | Primary Key |
| `hbeli` | `kd_cab` | `branch_code` | Kode Cabang |
| `hbeli` | `nota` | `receiving_number` | Nomor LPB |
| `hbeli` | `approve` | `status` | `'Y'` → `true` |
| `hbeli` | `userapp` | `approved_by` | User yang menyetujui |
| `hbeli` | `tglapp` | `approval_date` | Tanggal persetujuan (Sanitized) |
| `hbeli` | `tgl` | `date` | Tanggal transaksi (Sanitized) |
| `hbeli` | `nota1` | `supplier_invoice_number` | Nomor invoice dari supplier |
| `hbeli` | `tgl1` | `supplier_invoice_date` | Tanggal invoice supplier (Sanitized) |
| `hbeli` | `noop` | `purchase_order_number` | Nomor PO referensi |
| `po` | `id` | `purchase_order_id` | ID PO referensi |
| `po` | `tgl` | `purchase_order_date` | Tanggal PO (Sanitized) |
| `hbeli` | `IPPN` | `is_inclusive_tax` | `'Y'` → `true` |
| `hbeli` | `PPN` | `tax_percentage` | Persentase PPN |
| `hbeli` | `GP` | `is_general_purchase` | `'Y'` → `true` |
| `hbeli` | `STATUS` | `is_consignment` | `'Y'` → `true` |
| `hbeli` | `inventory` | `inventory_type` | `'FG'` → `'Finish Goods'`, else `'Raw Materials'` |
| `hbeli` | `kd_supp` | `supplier_code` | Kode Supplier |
| `hbeli` | `ket` | `supplier_name` | Nama supplier (Snapshot dari `hbeli.ket`) |
| `s` | `id` | `supplier_id` | ID Master Supplier |
| `d` | `kddep` | `department_code` | Kode Departemen |
| `d` | `ket` | `department_name` | Nama Departemen |
| `d` | `id` | `department_id` | ID Master Departemen |
| `hbeli` | `m_uang` | `currency_code` | Kode Mata Uang |
| `hbeli` | `JT_TEMPO` | `due_date` | Jatuh Tempo (Sanitized) |
| `hbeli` | `L_I` | `is_local_purchase` | `'L'` → `true` |
| `hbeli` | `DPP` | `total_tax_base` | Dasar Pengenaan Pajak |
| `hbeli` | `PPNRP` | `total_tax_amount` | Nilai PPN dalam Rupiah |
| `hbeli` | `RATE` | `currency_rate` | Nilai tukar mata uang |
| `hbeli` | `JNSBELI` | `category` | `1` → `'Standard'`, else `'Non Standard'` |
| `hbeli` | `DISC1` | `total_discount_global` | Diskon global (Header) |
| `hbeli` | `REPACKING` | `is_repacking` | `'Y'` → `true` |
| `hbeli` | `USER` | `created_by` | User pembuat entry |
| `hbeli` | `keterangan` | `remarks` | Catatan tambahan |
| `w` | `code` | `warehouse_code` | Kode Gudang |
| `w` | `name` | `warehouse_name` | Nama Gudang |
| `hbeli` | `T_HARGA` | `total_price` | Total nilai transaksi |
| `po` | `TGL_KRM` | `delivery_date` | Tanggal kirim dari PO (Sanitized) |
| `dbeli` | `kd6` | `account_type_code` | Subquery (Item pertama) |
| `dbeli` | `settrnket` | `account_type_name` | Subquery (Item pertama) |
| `hbeli` | `TGL_UPDATE` | `updated_at` | Waktu update terakhir |
| `hbeli` | `TGLENTRY` | `created_at` | Waktu entry data |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Konversi Boolean
Berbagai flag string pada database legacy dikonversi menjadi boolean untuk konsistensi:
- **Status & Tax**: `'Y'` dipetakan ke `true`.
- **Local Purchase**: Menggunakan karakter `'L'` untuk mengidentifikasi pembelian lokal (`is_local_purchase = true`).

### 3.2 Pemetaan Tipe & Kategori
- **Inventory Type**: Jika `'FG'` maka `'Finish Goods'`, selain itu dianggap `'Raw Materials'`.
- **Category**: Menggunakan integer `1` untuk mengidentifikasi kategori `'Standard'`, selain itu `'Non Standard'`.

### 3.3 Pembersihan Data (Sanitization)
- **Nama Supplier**: Kolom `hbeli.ket` dibersihkan dari karakter kutip ganda (`"`) dan spasi tambahan menggunakan `trim($name, '" ')`.
- **Tanggal**: Fungsi `sanitizeDate` digunakan untuk mengubah nilai `'0000-00-00'` (default legacy) menjadi `null`.

## 4. Anomali & Catatan (Anomalies & Notes)

- **Sumber Nama Supplier (Anomali)**: `ReceivingRepository` mengambil nama supplier dari kolom `hbeli.ket` (Header Beli), sedangkan `PurchaseOrderRepository` mengambilnya dari master `supplier.NAMA`. Penggunaan `hbeli.ket` pada modul Receiving perlu diperhatikan karena merupakan field keterangan/snapshot.
- **Konsistensi Flag Consignment**: Pada modul Receiving, flag consignment dipetakan dari `hbeli.STATUS`. Sebagai perbandingan, modul Purchase Order menggunakan kolom `hpo.KONS`.
- **Representasi Akun (Subqueries)**: Properti `account_type_code` and `account_type_name` diambil menggunakan subquery ke tabel detail (`dbeli`) dengan `LIMIT 1`. Hal ini hanya merepresentasikan data akun dari item pertama dalam satu nomor transaksi.
- **Join Purchase Order**: Menggunakan `noop` pada `hbeli` untuk join ke `nota` pada `hpo` guna mendapatkan informasi tanggal kirim dan ID PO.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
