# Presensi Web - Sistem Pencatatan Kehadiran Sekolah

Aplikasi pencatatan dan pengelolaan kehadiran (presensi) sekolah berbasis web yang dibangun menggunakan framework **Laravel**. Aplikasi ini dilengkapi dengan Manajemen Pengguna (CRUD User), Role-Based Access Control (RBAC), pindaian QR Code, serta penerapan standar keamanan dasar.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Prasyarat](#️prasyarat-prerequisites)
- [Cara Instalasi & Menjalankan Aplikasi](#️cara-instalasi--menjalankan-aplikasi)
- [Kredensial Pengguna](#kredensial-pengguna-default-accounts)
- [Dokumentasi Teknis](#dokumentasi-teknis)

---

## Fitur Utama

- **Role-Based Access Control (RBAC)** — Pembagian hak akses khusus untuk 3 peran utama: `Administrator`, `Guru (Teacher)`, dan `Siswa (Student)`.
- **Manajemen Pengguna (CRUD User)** — Pengelolaan akun pengguna (Tambah, Lihat, Edit, Hapus) beserta penentuan hak akses (role).
- **Pengelolaan Data Master** — Pengaturan ruang kelas, mata pelajaran, jadwal pelajaran dan jadwal mengajar.
- **Sistem Presensi QR Code** — Pembuatan QR Code kehadiran interaktif oleh Guru/Admin dan fitur scan QR oleh Siswa.

### Fitur Keamanan (Security)

| Fitur | Keterangan |
|---|---|
| Password Hashing | Menggunakan algoritma **Bcrypt** |
| Proteksi Endpoint/API | Menggunakan Laravel **Middleware** (`auth`, `verified`, dan pengecekan role) |
| Pencegahan SQL Injection | Menggunakan **Eloquent ORM** & **PDO Prepared Statements** |

---

## Prasyarat (Prerequisites)

Sebelum menjalankan aplikasi di lingkungan lokal, pastikan perangkat kamu telah terinstal:

- [ ] PHP >= 8.2
- [ ] Composer
- [ ] Node.js (v18 atau versi terbaru) & NPM
- [ ] SQLite Extension pada PHP

---

## Cara Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk melakukan setup dan menjalankan project di lingkungan lokal:

### 1. Clone Repositori

```bash
git clone https://github.com/tiyawt/presensi-web.git
cd presensi-web
```

### 2. Instal Dependensi

Instal paket-paket PHP dan Node.js yang dibutuhkan:

```bash
composer install
npm install
```

### 3. Konfigurasi Environment (.env)

Salin file `.env.example` menjadi file `.env`:

```bash
cp .env.example .env
```

> Variabel dasar dan konfigurasi database SQLite sudah otomatis terkonfigurasi pada file `.env.example`.

### 4. Buat File Database SQLite

Buat file kosong `database.sqlite` di dalam folder `database/`:

**Untuk Linux / macOS / Git Bash:**

```bash
touch database/database.sqlite
```

**Untuk Windows PowerShell:**

```powershell
New-Item database/database.sqlite -ItemType File
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Jalankan Migrasi & Seeder Database

Eksekusi migrasi tabel dan seeder untuk mengisi data awal akun pengguna bawaan:

```bash
php artisan migrate --seed
```

### 7. Jalankan Aplikasi

Jalankan server lokal Laravel dan aset frontend pada dua terminal terpisah:

**Terminal 1 — Jalankan server Laravel:**

```bash
php artisan serve
```

**Terminal 2 — Jalankan build frontend:**

```bash
npm run dev
```

Buka browser dan akses aplikasi melalui URL:

```
http://127.0.0.1:8000
```

---

## Kredensial Pengguna (Default Accounts)

Setelah proses `php artisan migrate --seed` selesai, kamu dapat masuk menggunakan akun uji coba bawaan berikut:

| Peran (Role) | Email | Password |
|---|---|---|
| Administrator | `admin@example.com` | `12345678` |
| Guru (Teacher) | `teacher@example.com` | `12345678` |
| Siswa (Student) | `student@example.com` | `12345678` |

> **Catatan:** Ganti kredensial default ini sebelum melakukan deployment ke lingkungan production.

---

## Dokumentasi Teknis

Dokumentasi teknis lengkap mengenai alur kerja sistem, rincian pembagian role, dan penjelasan detail mengenai fitur keamanan dapat diakses melalui tautan berikut:

👉 [Tautan Dokumentasi Teknis](https://app.notion.com/p/Dokumentasi-Teknis-Presensi-Web-3c8e72e1d0ae806e9e16fe0c9699ca94?source=copy_link)

---

<p align="center">Dibuat dengan ❤️ menggunakan Laravel</p>