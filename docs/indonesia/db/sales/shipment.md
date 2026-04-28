---
Project: Nayati Inox ERP
Module: Sales (Legacy Mapping - Shipment)
Version: 1.1.0
Last Updated: 2026-04-28
Status: Final
---

# Shipment Legacy Mapping

## 1. Overview
Dokumentasi ini merinci pemetaan data dari tabel legacy `msj` ke entitas Shipment dalam sistem baru.

## 2. Tabel & Mapping Kolom

- **Tabel Legacy**: `msj`
- **Primary Key**: `IDMSJ`
- **Joins**:
    - `leftJoin('warehouses as w', 'msj.kdlok', '=', 'w.code')`: Mengambil nama gudang dari tabel gudang di sistem baru untuk kejelasan lokasi.

| Legacy Column | Standardized Alias | Data Type | Deskripsi/Transformasi |
| :--- | :--- | :--- | :--- |
| `msj.IDMSJ` | `id` | Integer | Primary Key |
| `msj.NOBUK` | `shipment_number` | String | Nomor Bukti Shipment (Standard Nayati) |
| `msj.NOIN` | `invoice_number` | String | Nomor Invoice terkait |
| `msj.TGI` | `invoice_date` | Date | Tanggal Invoice terkait |
| `msj.NOP` | `delivery_order_number`| String | **Peringatan**: Di tabel ini NOP merujuk pada Nomor DO (NOTA) |
| `msj.KDCUST` | `customer_code` | String | Kode Customer |
| `msj.TGL` | `date` | Date | Tanggal Aktual Shipment |
| `msj.KDCAB` | `branch_code` | String | Kode Cabang |
| `msj.LE` | `is_local_purchase` | String | Indikator Lokal/Impor (Alias berbeda dengan SO) |
| `msj.KDUN` | `unit_code` | String | Kode Unit atau Divisi pengirim |
| `msj.NOSO` | `sales_order_number` | String | Nomor Sales Order (Referensi eksplisit) |
| `msj.TGLSO` | `sales_order_date` | Date | Tanggal Sales Order |
| `msj.kdlok` | `warehouse_code` | String | Kode Gudang asal barang |
| `w.name` | `warehouse_name` | String | Nama Gudang dari sistem baru |
| `msj.TGLUPDATE` | `updated_at` | DateTime | Waktu Pembaruan Data |

## 3. Logika Filtering & Sorting

### Filtering
- **Search**: Mencari pada `msj.NOBUK`, `msj.NOIN`, `msj.KDCUST`, dan `msj.NOSO`.
- **Date**: Berdasarkan `msj.TGL` (Tanggal Shipment).

### Sorting (Sortable Fields)
- `shipment_number` -> `msj.NOBUK`
- `invoice_number` -> `msj.NOIN`
- `invoice_date` -> `msj.TGI`
- `delivery_order_number` -> `msj.NOP`
- `customer_code` -> `msj.KDCUST`
- `date` -> `msj.TGL`
- `branch_code` -> `msj.KDCAB`
- `is_local_purchase` -> `msj.LE`
- `unit_code` -> `msj.KDUN`
- `sales_order_number` -> `msj.NOSO`
- `sales_order_date` -> `msj.TGLSO`
- `warehouse_code` -> `msj.kdlok`
- `warehouse_name` -> `w.name`
- `updated_at` -> `msj.TGLUPDATE`

## 4. Anomali & Temuan Teknis
- **Kekacauan NOP (Critical)**: Pada tabel `msj`, kolom `NOP` menyimpan Nomor Delivery Order (bukan Sales Order). Hal ini sangat rawan tertukar jika tidak didokumentasikan dengan benar. Nomor Sales Order disimpan secara terpisah di kolom `msj.NOSO`.
- **Semantik `LE`**: Di sini `msj.LE` dipetakan ke `is_local_purchase`, sedangkan di Sales Order dipetakan ke `order_type`. Keduanya merujuk pada hal yang sama (Lokal/Ekspor).

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
