# Dokumentasi Mapping Database: Account Type

Dokumen ini merinci pemetaan data dari database legacy ke sistem baru untuk modul Account Type.

## 1. Tabel Sumber
Pemetaan ini menggunakan satu tabel sumber:
- `uacc`: Master Account Type (Tabel Utama)

## 2. Pemetaan Kolom (Field Mapping)

| Tabel Legacy | Kolom Legacy | Properti Target | Transformasi / Catatan |
| :--- | :--- | :--- | :--- |
| `uacc` | `id` | `id` | Primary Key |
| `uacc` | `kd6` | `code` | Kode tipe akun |
| `uacc` | `ket1` | `name` | Nama/Keterangan tipe akun |

## 3. Logika Transformasi (Transformation Logic)

### 3.1 Pengurutan Data (Sorting)
Data diambil dengan pengurutan berdasarkan kolom `kd6` secara ascending (`ASC`).

## 4. Anomali & Catatan
- **Field yang Diabaikan:** Mapping saat ini hanya mengambil 3 kolom (`id`, `kd6`, `ket1`). Jika terdapat kolom lain pada tabel `uacc` di database legacy, kolom-kolom tersebut saat ini diabaikan oleh sistem baru.
- **Konsistensi:** Mapping ini bersifat langsung (direct mapping) tanpa transformasi nilai data yang kompleks.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
