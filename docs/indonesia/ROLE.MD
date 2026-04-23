---
Project: Nayati Inox ERP
Module: Utility - Role & Permission
Version: 1.0.0
Last Updated: 2026-04-23
Status: Final
---

# PENGGUNAAN ROLE & PERMISSION

Dokumentasi ini merinci implementasi teknis dan alur otorisasi dalam sistem Inox ERP, yang mencakup manajemen peran berbasis grup serta izin spesifik pengguna.

## 1. Arsitektur Basis Data

Sistem ini menggunakan pendekatan *Hybrid Role-Based Access Control* (RBAC) yang memungkinkan fleksibilitas tinggi.

### Model Utama
- **Role (`App\Models\Utility\Role`)**: Representasi grup akses (contoh: `admin`, `warehouse-manager`).
- **Permission (`App\Models\Utility\Permission`)**: Unit terkecil dari izin akses (contoh: `utility.user.view`).
- **User (`App\Models\Utility\User`)**: Entitas pengguna yang mengimplementasikan `App\Traits\HasPermission`.

### Relasi Tabel (Pivot)
- **`user_role`**: Menentukan peran apa saja yang dimiliki pengguna.
- **`role_permission`**: Menentukan izin apa saja yang melekat pada suatu peran.
- **`user_permission`**: Izin langsung (*Direct Permission*) pada level user. Memiliki kolom `is_denied` yang berfungsi sebagai *override* (pembatalan izin) meskipun peran pengguna mengizinkannya.

## 2. Logika Otorisasi (Backend)

Otorisasi diproses melalui Trait `HasPermission` dengan urutan prioritas sebagai berikut:

1. **Explicit Denied**: Jika izin pada tabel `user_permission` diset `is_denied = true`, akses ditolak.
2. **Direct Grant**: Jika izin diberikan langsung ke user tanpa `is_denied`, akses diberikan.
3. **Role-Based**: Jika salah satu peran pengguna memiliki izin tersebut, akses diberikan.

### Contoh Implementasi PHP
```php
// Pengecekan izin tunggal
if ($user->hasPermission('sales.order.create')) {
    // Logika pembuatan order
}

// Pengecekan multi-izin
$user->hasAnyPermission(['sales.order.edit', 'sales.order.delete']);
```

## 3. Integrasi Frontend (Vue 3 + Pinia)

Izin pengguna dimuat ke dalam `useAuthStore` saat inisialisasi aplikasi untuk menghindari pemanggilan API berulang.

### Penggunaan pada Script (TypeScript)
```typescript
import { useAuthStore } from '@/stores/utility/useAuthStore';

const auth = useAuthStore();

const canEdit = auth.hasPermission('purchasing.purchase-order.edit');
```

### Penggunaan pada Template
```html
<Button 
    v-if="auth.hasPermission('master.product.create')" 
    label="Tambah Barang" 
    size="small" 
/>
```

## 4. Konvensi Penamaan (Slug)

Izin harus didaftarkan melalui `PermissionSeeder` dengan format dot-notation:
`{module}.{sub_module}.{action}`

Contoh:
- `approval.purchase-request.approve`
- `reports.inventory.export`

## 5. Pemeliharaan & Sinkronisasi

Jika terdapat penambahan fitur baru, pastikan untuk:
1. Menambahkan slug baru di `database/seeders/PermissionSeeder.php`.
2. Menjalankan perintah:
   ```bash
   php artisan db:seed --class=PermissionSeeder
   php artisan db:seed --class=RoleSeeder
   ```
3. Mengosongkan cache jika diperlukan menggunakan `php artisan cache:clear`.

---
**Nayati Inox ERP - Technical Documentation**  
*Confidential - Internal Use Only*  
© 2026 PT Nayati Indonesia. All rights reserved.
