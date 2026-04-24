---
Project: Nayati Inox ERP
Module: Master
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Master Department

## 1. Tabel Sumber
- `mdept`: Tabel Master Departemen.

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `mdept` | `id` | `id` | Primary Key |
| `mdept` | `kddep` | `code` | Kode Departemen |
| `mdept` | `ket` | `name` | Nama Departemen |
| `mdept` | `tglentry` | `created_at` | Waktu Pembuatan (Sanitized) |

---
**Nayati Inox ERP - Technical Documentation**
