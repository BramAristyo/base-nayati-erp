# Pemetaan Database: Purchase Order Item (DPO)

Dokumen ini merinci pemetaan antara tabel legacy `dpo` (Detail Purchase Order) dengan struktur data standar dalam aplikasi Nayati Inox ERP.

## Informasi Tabel
- **Tabel Utama**: `dpo`
- **Tabel Terkait**: 
    - `hpo` (Header Purchase Order)
    - `dpr` (Detail Purchase Request)
    - `hpr` (Header Purchase Request)
    - `view_stockvariant` (Informasi Produk & Varian)

## Pemetaan Kolom (Mapping)

| Proyek (Standard) | Legacy Column | Tipe Data | Deskripsi |
| :--- | :--- | :--- | :--- |
| `id` | `iddpo` | `INT` | Primary Key |
| `purchase_request_item_id` | `iddpr` | `INT` | FK ke tabel `dpr` |
| `product_variant_id` | `kdbvar_id` | `INT` | FK ke tabel `stockvariant` |
| `product_code` | `kd_brg` | `CHAR` | Kode Barang |
| `variant_name` | `view_stockvariant.variantname` | `VARCHAR` | Nama Varian Produk |
| `product_name` | `dpo.nama` | `VARCHAR` | Nama Produk |
| `purchase_request_number` | `dpo.no_pr` | `CHAR` | Nomor Purchase Request Terkait |
| `quantity` | `dpo.qty` | `FLOAT` | Jumlah Dipesan |
| `price` | `dpo.harga` | `FLOAT` | Harga Satuan |
| `unit` | `dpo.sat` | `CHAR` | Satuan Barang |
| `buy_price` | `dpo.hbeli` | `FLOAT` | Harga Beli Netto |
| `sub_total` | `dpo.total` | `FLOAT` | Total Harga Item |
| `discount_percentage` | `dpo.discpro` | `FLOAT` | Persentase Diskon |
| `discount_amount` | `dpo.discrp` | `FLOAT` | Nilai Diskon Rupiah |
| `price_rate` | `dpo.konvert` | `FLOAT` | Konversi Rate Harga |
| `remarks` | `dpo.ket` | `VARCHAR` | Keterangan Item |
| `dimension_remarks` | `dpo.ket1` | `VARCHAR` | Keterangan Dimensi/Spesifikasi |
| `additional_remarks` | `dpo.ket2` | `VARCHAR` | Keterangan Tambahan |
| `is_general_purchase` | `hpr.gp` | `CHAR (Y/N)` | Apakah General Purchase? |
| `department_code` | `hpr.kddep` | `CHAR` | Kode Departemen Peminta |

## Kolom Kalkulasi (Calculated Fields)

### `max_qty`
Digunakan untuk menentukan batas maksimal kuantitas yang boleh dipesan dalam PO berdasarkan sisa kuantitas di PR.
**Logika**:
```sql
(COALESCE(dpr.qty, 0) - COALESCE(dpr.QTY_OP, 0) + COALESCE(dpo.qty, 0))
```
*   `dpr.qty`: Kuantitas total di PR.
*   `dpr.QTY_OP`: Kuantitas yang sudah masuk ke PO lain.
*   `dpo.qty`: Kuantitas pada item PO saat ini (ditambahkan kembali agar saat update tidak terbentur validasi sendiri).

## Logika Transformasi (Repository Layer)
1.  **Casting Numeric**: Semua field nominal dan kuantitas di-cast menjadi `float` untuk konsistensi perhitungan di frontend/service.
2.  **String Sanitization**: Menggunakan `trim()` untuk kolom tipe `CHAR` legacy untuk menghilangkan whitespace yang tidak perlu.
3.  **Boolean Mapping**: Field `is_general_purchase` dikonversi dari `'Y'/'N'` menjadi `true/false`.
4.  **Date Sanitization**: Nilai `'0000-00-00'` dikonversi menjadi `null`.

---
*Dokumentasi ini dibuat otomatis oleh AI Assistant sebagai referensi teknis pemetaan database legacy.*
