---
Project: Nayati Inox ERP
Module: Master
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Master Customer

## 1. Tabel Sumber
- `custom`: Tabel Master Customer.

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `custom` | `IDCUST` | `id` | Primary Key |
| `custom` | `KD_CAB` | `branch_code` | Kode Cabang Terkait |
| `custom` | `KD_CUST` | `code` | Kode unik Customer |
| `custom` | `NAMA` | `name` | Nama Customer |
| `custom` | `NMCOMERCIAL` | `commercial_name` | Nama Komersial |
| `custom` | `KOTA` | `city` | Kota |
| `custom` | `ALAMAT` | `address` | Alamat Utama |
| `custom` | `ALAMAT1` | `other_address` | Alamat Tambahan |
| `custom` | `TELP1` | `phone` | Telepon 1 |
| `custom` | `TELP2` | `other_phone` | Telepon 2 |
| `custom` | `NPWP` | `npwp` | Nomor NPWP |
| `custom` | `ada_so` | `is_has_sales_order` | `'Y'` → `true` |

---
**Nayati Inox ERP - Technical Documentation**
