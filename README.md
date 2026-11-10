# SIM Mahasiswa

Sistem Informasi Manajemen Mahasiswa per Program Studi, Universitas Muhammadiyah Bengkulu.
Tugas mata kuliah Web Semantik (P3: CRUD Page Web Tradisional).

**NPM:** 2455201233
**Penyusun:** Brilian Anugra
**Demo online:** http://websematic-2455201233.infinityfree.io/simmahasiswa/
**SQL dump:** [database/sim_mahasiswa.sql](database/sim_mahasiswa.sql)

> **Semua akun dan data di README ini adalah data contoh untuk demo.**
> Jangan memakai password asli di aplikasi ini. Demo online berjalan di HTTP tanpa enkripsi.

---

## Akun Demo

Semua akun memakai password **`password123`**.

| Role | Username | Akses menu |
|---|---|---|
| Admin | `Admin` | Semua menu |
| Operator Prodi | `operator.ti` | Fakultas, Program Studi (lihat), Mahasiswa (kelola prodi sendiri), Laporan |
| Dekanat | `dekanat.ft` | Fakultas, Program Studi, Mahasiswa, Laporan (lihat, cakupan fakultas) |
| Rektorat | `rektorat` | Fakultas, Program Studi, Mahasiswa, Laporan (lihat, semua) |
| Mahasiswa | `20260001` | Dashboard saja |

---

## Teknologi

| Bagian | Yang dipakai |
|---|---|
| Bahasa | PHP 8.x native, tanpa framework |
| Database | MySQL lewat MySQLi (prepared statement) |
| Antarmuka | Bootstrap 5.3.3 dan Bootstrap Icons (CDN jsDelivr) |
| Lokal | Laragon |
| Hosting demo | InfinityFree |

## Fitur

- Login, logout, dan **RBAC** 5 role dengan menu berbeda per role.
- **CRUD** 7 modul: Universitas, Role, Fakultas, Program Studi, Pengguna, Mahasiswa.
- **Laporan** Daftar Mahasiswa Per Program Studi, bisa dicetak.
- Dropdown relasi, validasi di server, hapus diblokir jika data masih dipakai.
- Pembatasan data per role: operator hanya prodinya, dekanat hanya fakultasnya.

| Modul | Lihat | Tambah/ubah/hapus |
|---|---|---|
| Universitas, Role, Pengguna | Admin | Admin |
| Fakultas, Program Studi | Admin, Operator, Dekanat, Rektorat | Admin |
| Mahasiswa | Admin, Operator, Dekanat, Rektorat | Admin, Operator (prodi sendiri) |
| Laporan | Admin, Operator, Dekanat, Rektorat | - |

## Keamanan

- Prepared statement di semua query (SQL Injection).
- `password_hash` dan `password_verify`, `session_regenerate_id` saat login.
- Token CSRF pada semua form POST, termasuk hapus.
- `htmlspecialchars` pada semua output (XSS).
- Hak akses diperiksa di server (`wajib_role`), bukan hanya menyembunyikan menu.
- Tidak memakai `CREATE VIEW` karena ditolak hosting gratis.

## Menjalankan di Komputer Sendiri

1. Salin proyek ke folder `simmahasiswa` (misal `C:\laragon\www\simmahasiswa`). Nama folder harus `simmahasiswa` karena `config/app.php` memakai `BASE_URL = /simmahasiswa`.
2. Buat database kosong `sim_mahasiswa` (charset `utf8mb4`).
3. Impor `database/sim_mahasiswa.sql`.
4. Buka `http://localhost/simmahasiswa/`. Koneksi bawaan: `root`, tanpa password. `config/config.php` diabaikan di localhost.
5. Login dengan akun demo di atas.

Untuk hosting, salin `config/config.example.php` menjadi `config/config.php` lalu isi data MySQL hosting.

## Struktur Folder

```
simmahasiswa/
├── index.php            landing page
├── config/              app.php, koneksi.php, config.example.php
├── includes/            helper, auth, csrf, layout, topbar, sidebar, *_helper, *_form
├── auth/                login.php, logout.php
├── pages/               dashboard, universitas*, role*, fakultas*, program_studi*,
│                        pengguna*, mahasiswa*, laporan_mahasiswa
├── assets/css/          style.css
└── database/            sim_mahasiswa.sql
```

## Keterbatasan

- Menonaktifkan akun atau mengubah cakupan/role tidak memutus sesi yang sedang berjalan sampai pengguna login ulang.
- Role buatan admin belum punya menu dan hak akses, karena hak akses diatur di kode.
- Daftar mahasiswa belum memakai pagination.
- Demo online memakai HTTP, bukan HTTPS.