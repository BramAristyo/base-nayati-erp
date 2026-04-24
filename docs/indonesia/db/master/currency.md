---
Project: Nayati Inox ERP
Module: Master
Version: 1.0.0
Last Updated: 2026-05-22
Status: Final
---

# Dokumentasi Mapping Database: Master Currency

## 1. Tabel Sumber
- `muang`: Tabel Master Mata Uang/Currency.

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `muang` | `KODE` | `code` | Primary Key (IDR, USD, etc) |
| `muang` | `NAMA` | `name` | Nama Mata Uang |
| `muang` | `KURS` | `rate` | Nilai Tukar (Float) |

---
**Nayati Inox ERP - Technical Documentation**
