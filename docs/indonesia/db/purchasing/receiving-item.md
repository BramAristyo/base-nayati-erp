---
Project: Nayati Inox ERP
Module: Purchasing
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Receiving Items (Detail)

Dokumen ini merinci pemetaan data dari database legacy ke sistem baru untuk detail item pada penerimaan barang (Receiving/Nota Beli).

## 1. Tabel Sumber & Relasi
Pemetaan ini melibatkan tabel-tabel berikut:
- `dbeli`: Detail Nota Beli / Receiving (Tabel Utama)
- `view_stockvariant`: View Master Produk/Variant (Join via `kdbvar_id`)

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `dbeli` | `iddbeli` | `id` | Primary Key |
| `dbeli` | `iddpo` | `purchase_order_item_id` | Referensi ke ID Item PO |
| `dbeli` | `noop` | `purchase_order_number` | Nomor Purchase Order terkait |
| `dbeli` | `kdbvar_id` | `product_variant_id` | ID Variant Produk |
| `view_stockvariant` | `variantname` | `variant_name` | Nama Variant Produk |
| `dbeli` | `kd_brg` | `product_code` | Kode Produk |
| `dbeli` | `kd_code` | `product_barcode` | Barcode Produk |
| `dbeli` | `nama` | `product_name` | Nama Produk |
| `dbeli` | `qty` | `quantity` | Jumlah (Float) |
| `dbeli` | `sat` | `unit` | Satuan (e.g., PCS, KG) |
| `dbeli` | `hbeli` | `buy_price` | Harga Beli Netto (Float) |
| `dbeli` | `hpp` | `unit_cost` | Harga Pokok Penjualan/COGS (Float) |
| `dbeli` | `harga` | `price` | Harga Jual/Base Price (Float) |
| `dbeli` | `kd6` | `account_type_code` | Kode Tipe Akun |
| `dbeli` | `settrnket` | `account_type_name` | Nama Tipe Akun |
| `dbeli` | `nosr` | `serial_number` | Nomor Serial |
| `dbeli` | `NOGARANSI` | `warranty_number` | Nomor Garansi |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Casting Tipe Data
Semua kolom numerik (`quantity`, `buy_price`, `unit_cost`, `price`) dipastikan bertipe `float` melalui casting di layer repository.

### 3.2 Pembersihan String
Semua kolom teks melalui proses `trim()` dan penanganan null-safe (`?? ''`) untuk menghapus spasi kosong yang umum ditemukan pada tipe data `CHAR` di database legacy.

## 4. Anomali & Catatan (Anomalies & Notes)
- **Konsistensi Harga**: Pemetaan `hbeli` ke `buy_price` dilakukan untuk menjaga konsistensi dengan modul Purchase Order.
- **Gap Analysis**: Field `total`, `discpro`, and `discrp` dari tabel `dbeli` tidak dipetakan dalam repository ini. Perlu dikonfirmasi apakah detail diskon per item diperlukan untuk tampilan atau laporan receiving.
- **Serial & Garansi**: Field `nosr` dan `NOGARANSI` tersedia di level item, mendukung penelusuran aset/barang yang memiliki identitas unik.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
