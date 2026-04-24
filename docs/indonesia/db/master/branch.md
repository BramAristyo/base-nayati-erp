---
Project: Nayati Inox ERP
Module: Master
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Master Branch

## 1. Tabel Sumber
- `kd_cab`: Tabel Master Cabang/Branch.

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `kd_cab` | `kd_cab` | `code` | Primary Key |
| `kd_cab` | `nm_cab` | `name` | Nama Cabang |
| `kd_cab` | `alm_cab` | `address` | Alamat Cabang |
| `kd_cab` | `npwp_cab` | `npwp` | NPWP Cabang |
| `kd_cab` | `telp_cab` | `phone` | Telepon Cabang |
| `kd_cab` | `email_cab` | `email` | Email Cabang |
| `kd_cab` | `aktif` | `is_active` | `'Y'` → `true` |

---
**Nayati Inox ERP - Technical Documentation**
