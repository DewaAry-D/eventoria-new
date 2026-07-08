# Eventoria

Eventoria merupakan sistem informasi manajemen event kampus yang dirancang untuk mendukung proses penyelenggaraan kegiatan secara terintegrasi. Sistem ini memfasilitasi pengelolaan organisasi mahasiswa, publikasi dan moderasi event, pendaftaran peserta, verifikasi berkas, hingga penerbitan sertifikat secara digital.

Aplikasi dikembangkan menggunakan **Laravel 13** dan **Livewire 3** sehingga mampu memberikan pengalaman aplikasi yang interaktif tanpa perlu melakukan reload halaman (Single Page Application).

---

# Teknologi yang Digunakan

| Komponen Sistem | Teknologi | Fungsi Utama |
|-----------------|-----------|--------------|
| Backend | PHP (Laravel 13.x) | Menangani logika bisnis, routing, autentikasi, dan pemrosesan sistem secara modular. |
| Frontend | Livewire 3 | Menghasilkan Single Page Application (SPA) interaktif tanpa page reload melalui fitur `wire:navigate`. |
| UI/UX & Styling | Tailwind CSS & Alpine.js | Menangani tampilan antarmuka, komponen responsif, modal, dropdown, serta interaksi ringan pada halaman. |
| Database | MySQL | Bertindak sebagai Relational Database Management System (RDBMS) untuk penyimpanan data terstruktur. |
| Asset Bundler | Vite | Mengelola proses build dan hot reload aset frontend. |
| Dependency Manager | Composer & NPM | Mengelola dependency backend maupun frontend. |

---

# Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan perangkat telah terpasang:

- PHP **8.3** atau lebih baru
- Composer
- Node.js (disarankan versi LTS)
- NPM
- MySQL
- Git

---

# Panduan Instalasi

## 1. Clone Repository

```bash
git clone <URL_REPOSITORY>
```

Masuk ke direktori project.

```bash
cd eventoria
```

---

## 2. Checkout Branch

Branch utama:

```bash
git checkout main
```

Apabila ingin mengembangkan fitur baru, gunakan branch development sesuai kebutuhan.

---

## 3. Install Dependency Backend

```bash
composer install
```

---

## 4. Install Dependency Frontend

```bash
npm install
```

---

## 5. Membuat File Environment

### Windows (CMD)

```cmd
copy .env.example .env
```

### Windows (PowerShell)

```powershell
Copy-Item .env.example .env
```

### Linux / macOS / Git Bash

```bash
cp .env.example .env
```

---

## 6. Generate Application Key

```bash
php artisan key:generate
```

---

## 7. Konfigurasi Database

Buka file `.env`, kemudian sesuaikan konfigurasi berikut.

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eventoria
DB_USERNAME=root
DB_PASSWORD=
```

---

## 8. Migrasi Database

```bash
php artisan migrate
```

Apabila tersedia data awal (Seeder), jalankan:

```bash
php artisan db:seed
```

atau

```bash
php artisan migrate --seed
```

---

## 9. Membuat Storage Link

Karena aplikasi menggunakan penyimpanan gambar, dokumen, dan berkas lainnya, jalankan perintah berikut:

```bash
php artisan storage:link
```

---

## 10. Menjalankan Aplikasi

Jalankan server Laravel.

```bash
php artisan serve
```

Pada terminal lain, jalankan Vite.

```bash
npm run dev
```

Aplikasi dapat diakses melalui:

```
http://127.0.0.1:8000
```

---

# Struktur Teknologi

```
Laravel 13
│
├── Livewire 3
├── Blade
├── Tailwind CSS
├── Alpine.js
├── Vite
└── MySQL
```

---

# Perintah yang Sering Digunakan

Menjalankan server Laravel

```bash
php artisan serve
```

Menjalankan Vite

```bash
npm run dev
```

Migrasi database

```bash
php artisan migrate
```

Migrasi beserta Seeder

```bash
php artisan migrate --seed
```

Rollback migrasi

```bash
php artisan migrate:rollback
```

Membersihkan cache aplikasi

```bash
php artisan optimize:clear
```

Membuat symbolic link storage

```bash
php artisan storage:link
```

---

# Catatan

- Pastikan layanan MySQL telah berjalan sebelum menjalankan proses migrasi.
- Direktori `storage` harus memiliki hak akses tulis (write permission).
- Jalankan `npm run dev` selama proses pengembangan agar perubahan frontend dapat diperbarui secara otomatis.