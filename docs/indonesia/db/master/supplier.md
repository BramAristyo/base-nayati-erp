---
Project: Nayati Inox ERP
Module: Master
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Master Supplier

## 1. Tabel Sumber
- `supplier`: Tabel Master Supplier.

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `supplier` | `id` | `id` | Primary Key |
| `supplier` | `kd_supp` | `code` | Kode unik Supplier |
| `supplier` | `nama` | `name` | Nama Supplier |
| `supplier` | `alamat` | `address` | Alamat Lengkap |
| `supplier` | `kota` | `city` | Kota |
| `supplier` | `negara` | `country` | Negara |
| `supplier` | `telp1` | `phone` | Telepon Utama |
| `supplier` | `no_fax` | `fax` | Nomor Fax |
| `supplier` | `npwp` | `tin` | Nomor NPWP (Tax Identification Number) |
| `supplier` | `person` | `contact_person` | Nama Kontak Person |
| `supplier` | `tglupdate` | `updated_at` | Waktu Update Terakhir (Sanitized) |

---
**Nayati Inox ERP - Technical Documentation**
