---
Project: Nayati Inox ERP
Module: Sales (Legacy Mapping)
Version: 1.0.0
Last Updated: 2026-04-28
Status: Final
---

# Sales Legacy Mapping Documentation

## 1. Overview
Dokumentasi ini merinci pemetaan data dari database legacy ke sistem baru untuk modul Sales. Repository yang diaudit mencakup Sales Order, Delivery Order, Invoice, Proforma, dan Shipment.

## 2. Tabel & Mapping Kolom

### A. Sales Order (`SalesOrderRepository`)
- **Tabel Legacy**: `mo`
- **Primary Key**: `id`
- **Joins**:
    - `leftJoin('custom as c', 'c.kd_cust', '=', 'mo.KDCUST')` untuk mengambil Nama Customer.

| Legacy Column | Alias | Tipe/Transformasi |
| :--- | :--- | :--- |
| `nop` | `sales_order_number` | String |
| `NOPI` | `invoice_number` | String |
| `KDCUST` | `customer_code` | String |
| `c.NAMA` | `customer_name` | String (Trimmed) |
| `KDCAB` | `branch_code` | String |
| `TGS` | `date` | Date (Sanitized) |
| `TGD` | `delivery_date` | Date (Sanitized) |
| `JTHTMP` | `due_date` | Date (Sanitized) |
| `VERIFIKASI` | `status` | Boolean (`Y` -> true) |
| `LE` | `order_type` | String |
| `NILAI` | `currency_rate` | Float |
| `TOT` | `net_price` | Float |
| `DP` | `down_payment` | Float |

### B. Delivery Order (`DeliveryOrderRepository`)
- **Tabel Legacy**: `hdo`
- **Primary Key**: `ID`

| Legacy Column | Alias | Tipe/Transformasi |
| :--- | :--- | :--- |
| `NOTA` | `delivery_order_number` | String |
| `TGL` | `date` | Date (Sanitized) |
| `NOP` | `sales_order_number` | String |
| `NMPROJECT` | `customer_name` | String |
| `KD_CUST` | `customer_code` | String |
| `APPROVE` | `status` | Boolean (`Y` -> true) |

### C. Invoice (`InvoiceRepository`)
- **Tabel Legacy**: `minv`
- **Primary Key**: `IDMINV`
- **Joins**:
    - `leftJoin('custom as c', 'minv.kdcust', '=', 'c.kd_cust')`

| Legacy Column | Alias | Tipe/Transformasi |
| :--- | :--- | :--- |
| `NOIN` | `invoice_number` | String |
| `TGI` | `date` | Date (Sanitized) |
| `TOTAL` | `total` | Float |
| `STOT` | `subtotal` | Float |
| `PPN` | `tax_amount` | Float |
| `APPROVE1` | `status` | Boolean (`Y` -> true) |

### D. Proforma (`ProformaRepository`)
- **Tabel Legacy**: `mproforma`
- **Primary Key**: `ID`

| Legacy Column | Alias | Tipe/Transformasi |
| :--- | :--- | :--- |
| `NOIN` | `proforma_number` | String |
| `No_kwitansi` | `invoice_number` | String |
| `TOTSO` | `bruto` | Float |
| `TDP` | `netto` | Float |
| `approve1` | `status` | Boolean (`Y` -> true) |

### E. Shipment (`ShipmentRepository`)
- **Tabel Legacy**: `msj`
- **Primary Key**: `IDMSJ`
- **Joins**:
    - `leftJoin('warehouses as w', 'msj.kdlok', '=', 'w.code')`

| Legacy Column | Alias | Tipe/Transformasi |
| :--- | :--- | :--- |
| `NOBUK` | `shipment_number` | String |
| `NOIN` | `invoice_number` | String |
| `NOP` | `delivery_order_number` | String |
| `NOSO` | `sales_order_number` | String |
| `LE` | `is_local_purchase` | String |

## 3. Logika Transformasi Global
- **Date Sanitization**: Nilai `0000-00-00` atau `0000-00-00 00:00:00` diubah menjadi `null` melalui fungsi `sanitizeDate`.
- **Boolean Status**: Kolom status (seperti `VERIFIKASI`, `APPROVE`, `APPROVE1`) dikonversi menjadi boolean berdasarkan nilai `'Y'`.
- **Numeric Casting**: Kolom harga, subtotal, total, dan nilai mata uang di-cast ke `float` untuk akurasi perhitungan di aplikasi.
- **String Sanitization**: Penggunaan `trim()` pada kolom nama (customer/supplier) untuk membersihkan whitespace atau karakter kutip sisa data legacy.

## 4. Anomali & Catatan
1. **Inkonsistensi Kolom `LE`**:
   - Di `SalesOrderRepository`, `mo.LE` dipetakan ke `order_type`.
   - Di `ShipmentRepository`, `msj.LE` dipetakan ke `is_local_purchase`.
   - Di modul Purchasing (`PurchaseOrderRepository`), indikator sejenis menggunakan kolom `L_I`.
   - **Rekomendasi**: Verifikasi apakah `LE` di Sales memiliki semantik yang sama (Lokal/Ekspor atau Lokal/Impor). Jika iya, disarankan menggunakan alias yang seragam (misal: `is_local`).
2. **Sumber Data Nama Customer**:
   - Terdapat variasi antara mengambil nama dari tabel master `custom` via `join` atau langsung dari kolom di tabel transaksi. Hal ini berisiko jika data di tabel transaksi tidak terupdate saat ada perubahan di master.
3. **Ambiguitas Kolom `PPN`**:
   - Kolom `PPN` pada modul Sales (`Invoice`) merujuk pada nilai nominal pajak, sedangkan pada modul Purchasing (`Purchase Order`) merujuk pada persentase. Pastikan logika kalkulasi di level Service menangani perbedaan ini.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
