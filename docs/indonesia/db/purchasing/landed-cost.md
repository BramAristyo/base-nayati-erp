---
Project: Nayati Inox ERP
Module: Purchasing
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Landed Cost (Import)

Dokumen ini merinci pemetaan data dari database legacy ke sistem baru untuk modul Landed Cost atau Biaya Import.

## 1. Tabel Sumber & Relasi
Pemetaan ini melibatkan tabel-tabel berikut:
- `bimport`: Tabel utama biaya import/landed cost.
- `hbeli`: Header Pembelian/Receiving (Join via `hbeli.nota = bimport.NOLPB`).

## 2. Pemetaan Kolom (Field Mapping)

### 2.1 Informasi Utama & Header
| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `bimport` | `ID` | `id` | Primary Key |
| `bimport` | `NOKAL` | `landed_cost_number` | Nomor Kalkulasi Biaya Import |
| `bimport` | `TGL` | `landed_cost_date` | Tanggal Landed Cost |
| `bimport` | `NOLPB` | `receiving_number` | Nomor Laporan Penerimaan Barang |
| `bimport` | `NOPO` | `purchase_order_number` | Nomor Purchase Order |
| `bimport` | `NOIN` | `supplier_invoice_number` | Nomor Invoice dari Supplier |
| `hbeli` | `kd_supp` | `supplier_code` | Kode Supplier Utama |
| `bimport` | `user` | `created_by` | User pembuat entry |
| `bimport` | `tglentry` | `created_at` | Waktu pembuatan entry |

### 2.2 Komponen Biaya & Standarisasi Finansial
Sesuai dengan aturan global standarisasi field finansial:

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `bimport` | `EXWORK` | `sub_total` | Nilai barang (Ex-works) |
| `bimport` | `DISC` | `discount_percentage_1` | Persentase Diskon |
| `bimport` | `TDISC` | `discount_amount_1` | Nominal Diskon |
| `bimport` | `FCOST` | `factor_cost` | Faktor Biaya (Multiplier) |
| `hbeli` | `m_uang` | `currency_code` | Mata uang transaksi utama |
| `hbeli` | `RATE` | `currency_rate` | Nilai tukar mata uang utama |

### 2.3 Biaya Tambahan (Extra Charges)
Biaya-biaya tambahan dipetakan secara ekspresif beserta rate dan vendor terkait:

#### Freight & Insurance
| Tabel Legacy | Kolom Legacy | Properti Target | Catatan |
| :--- | :--- | :--- | :--- |
| `bimport` | `AIRF` | `air_freight_charge` | Biaya angkut udara |
| `bimport` | `SEAF` | `sea_freight_charge` | Biaya angkut laut |
| `bimport` | `KDCUR3` | `freight_currency_code` | Mata uang Freight |
| `bimport` | `NILAI3` | `freight_currency_rate` | Rate Freight |
| `bimport` | `KDSPLYA` | `freight_supplier_code` | Vendor Freight |
| `bimport` | `REMARKA` | `freight_remark` | Keterangan Freight |
| `bimport` | `INS` | `insurance_charge` | Biaya Asuransi |
| `bimport` | `RATEINS` | `insurance_currency_rate` | Rate Asuransi |
| `bimport` | `KDSPLYD` | `insurance_supplier_code` | Vendor Asuransi |
| `bimport` | `REMARKD` | `insurance_remark` | Keterangan Asuransi |

#### Duties & Taxes
| Tabel Legacy | Kolom Legacy | Properti Target | Catatan |
| :--- | :--- | :--- | :--- |
| `bimport` | `BEA` | `bea_charge` | Biaya Bea Masuk |
| `bimport` | `PNBP` | `pnbp_charge` | Penerimaan Negara Bukan Pajak |
| `bimport` | `KDSPLYB` | `bea_supplier_code` | Vendor terkait Bea |
| `bimport` | `REMARKB` | `bea_remark` | Keterangan Bea |

#### Logistik & EMKL
| Tabel Legacy | Kolom Legacy | Properti Target | Catatan |
| :--- | :--- | :--- | :--- |
| `bimport` | `PAKING` | `packing_charge` | Biaya Packing |
| `bimport` | `RATEPACK` | `packing_currency_rate` | Rate Packing |
| `bimport` | `KDSPLYE` | `packing_supplier_code` | Vendor Packing |
| `bimport` | `REMARKC` | `packing_remark` | Keterangan Packing |
| `bimport` | `ROYSAT` | `emkl_unit` | Satuan EMKL |
| `bimport` | `ROYJUM` | `emkl_unit_rate` | Tarif EMKL per unit |
| `bimport` | `ROYNOM` | `emkl_charge` | Total Biaya EMKL |
| `bimport` | `KDSPLY` | `emkl_supplier_code` | Vendor EMKL |
| `bimport` | `FORWA` | `forwarding_charge` | Biaya Forwarding |
| `bimport` | `KDCUR4` | `forwarding_currency_code` | Mata uang Forwarding |
| `bimport` | `NILAI4` | `forwarding_currency_rate` | Rate Forwarding |
| `bimport` | `KDSPLY1` | `forwarding_supplier_code` | Vendor Forwarding |
| `bimport` | `ANGKUT` | `delivery_charge` | Biaya Pengangkutan Lokal |
| `bimport` | `KDCUR2` | `delivery_currency_code` | Mata uang Pengangkutan |
| `bimport` | `NILAI2` | `delivery_currency_rate` | Rate Pengangkutan |

#### Bank & Lain-lain
| Tabel Legacy | Kolom Legacy | Properti Target | Catatan |
| :--- | :--- | :--- | :--- |
| `bimport` | `BANK` | `bank_charge` | Biaya Bank |
| `bimport` | `LC1` | `lc_opening_charge` | Biaya Pembukaan LC |
| `bimport` | `LC1RATE` | `lc_opening_currency_rate` | Rate LC Opening |
| `bimport` | `LC2` | `lc_settlement_charge` | Biaya Penyelesaian LC |
| `bimport` | `LC2RATE` | `lc_settlement_currency_rate` | Rate LC Settlement |
| `bimport` | `OUR` | `margin_charge` | Biaya Margin/OUR |
| `bimport` | `LAIN` | `other_charge` | Biaya Lain-lain |
| `bimport` | `KDCUR1` | `other_charge_currency_code` | Mata uang biaya lain |
| `bimport` | `NILAI1` | `other_charge_currency_rate` | Rate biaya lain |
| `bimport` | `SURVAI` | `survey_cost_charge` | Biaya Survey |
| `bimport` | `CURSURVAI` | `survey_cost_currency_code` | Mata uang Survey |
| `bimport` | `RATESURVAI` | `survey_cost_currency_rate` | Rate Survey |
| `bimport` | `KDSPLYF` | `survey_supplier_code` | Vendor Survey |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Pembersihan Data (Sanitization)
- **Tanggal**: Fungsi `sanitizeDate` digunakan untuk mengubah nilai `'0000-00-00'` (default legacy) menjadi `null`.

## 4. Anomali & Catatan (Anomalies & Notes)

- **Ketidakkonsistenan Nama:** Kolom `EXWORK` dipetakan sebagai `exwork` di method `paginate` tetapi sebagai `total_price` di method `findById`. Dokumentasi ini menetapkan `sub_total` sebagai standar.
- **Grand Total:** Repositori tidak memetakan field `grand_total` secara eksplisit dari database, namun nilai ini biasanya merupakan hasil kalkulasi dari `sub_total` - `discount` + seluruh `extra_charges`.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
