# POS SISTEM

**Aplikasi Point of Sale (POS) Berbasis Web**

POS SISTEM adalah aplikasi berbasis web yang dikembangkan untuk membantu pengelolaan penjualan dan transaksi secara terstruktur. Aplikasi ini dibangun menggunakan framework CodeIgniter 4 dan PHP, dengan database MySQL sebagai penyimpanan data.

## 📌 Fitur Utama

- **Manajemen Produk** — Mengelola data produk yang digunakan dalam proses penjualan.
- **Transaksi Penjualan** — Mendukung pencatatan transaksi penjualan.
- **Pengelolaan Data** — Membantu pengelolaan data yang berkaitan dengan operasional penjualan.
- **Antarmuka Berbasis Web** — Dapat diakses melalui browser.
- **Database MySQL** — Menyimpan data aplikasi secara terstruktur.

*Catatan: Fitur yang tersedia mengikuti implementasi pada kode sumber aplikasi.*

## 🛠️ Teknologi yang Digunakan

| Teknologi | Keterangan |
|---|---|
| PHP | Bahasa pemrograman backend |
| CodeIgniter 4 | Framework aplikasi web |
| MySQL | Sistem manajemen basis data |
| HTML5 | Struktur halaman web |
| CSS3 | Pengaturan tampilan |
| JavaScript | Interaksi pada halaman web |
| Composer | Pengelolaan dependensi PHP |

## 📋 Persyaratan Sistem

Sebelum menjalankan aplikasi, pastikan perangkat telah memiliki:

- PHP dengan versi yang sesuai dengan kebutuhan CodeIgniter 4.
- Composer.
- MySQL atau MariaDB.
- Web server lokal, seperti Laragon.
- Git untuk mengelola kode sumber.

## 🚀 Instalasi dan Menjalankan Aplikasi

### 1. Clone Repository

Buka terminal, kemudian jalankan:

```bash
git clone https://github.com/ardianaryanandaa-ship-it/-POS-SISTEM.git
```

### 2. Masuk ke Direktori Proyek

```bash
cd -POS-SISTEM
```

### 3. Instal Dependensi

```bash
composer install
```

### 4. Konfigurasi Environment

Salin file `env` menjadi `.env` jika file `.env` belum tersedia.

Pada Windows PowerShell, jalankan:

```powershell
Copy-Item env .env
```

Sesuaikan konfigurasi environment di file `.env`, termasuk pengaturan database dan base URL sesuai lingkungan pengembangan.

### 5. Konfigurasi Database

1. Jalankan MySQL melalui Laragon atau layanan database yang digunakan.
2. Buat database untuk aplikasi POS SISTEM.
3. Sesuaikan konfigurasi koneksi database pada file `.env`.
4. Jika proyek menyediakan file SQL, import file tersebut ke database yang telah dibuat.

### 6. Jalankan Aplikasi

Gunakan server pengembangan CodeIgniter 4:

```bash
php spark serve
```

Buka browser dan akses:

```text
http://localhost:8080
```

Pastikan konfigurasi database dan persyaratan aplikasi sudah sesuai sebelum menjalankannya.

## 📁 Struktur Proyek

```text
POS-SISTEM/
├── app/              # Kode utama aplikasi
├── public/           # File publik dan aset web
├── tests/            # Pengujian aplikasi
├── writable/         # File yang digunakan saat runtime
├── composer.json     # Konfigurasi dependensi
├── composer.lock     # Versi dependensi yang terkunci
├── phpunit.xml.dist  # Konfigurasi pengujian
├── preload.php       # Konfigurasi preload PHP
├── spark             # CLI CodeIgniter 4
└── README.md         # Dokumentasi proyek
```

## 🔒 Keamanan

- Jangan mengunggah file `.env` yang berisi kredensial atau konfigurasi rahasia.
- Jangan menyimpan kata sandi database atau API key langsung di kode sumber.
- Gunakan konfigurasi environment yang sesuai untuk pengembangan maupun produksi.
- Pastikan data transaksi dan informasi pelanggan dilindungi dengan baik.

## 👨‍💻 Pengembang

**Ardian Arya Nanda**

Repository: [POS SISTEM](https://github.com/ardianaryanandaa-ship-it/-POS-SISTEM)

---

Dikembangkan sebagai proyek aplikasi berbasis web untuk mendukung pengelolaan penjualan dan transaksi.
