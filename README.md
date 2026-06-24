# TemanCOD

TemanCOD adalah website layanan COD yang menghubungkan pelanggan, companion, dan admin dalam satu alur yang lebih terorganisir.

## Fitur Utama
- Registrasi dan login untuk pelanggan, companion, dan admin
- Dashboard pelanggan untuk melihat status pesanan
- Dashboard companion untuk menerima, menolak, dan mengelola pesanan
- Dashboard admin untuk mengelola pengguna, pesanan, dan verifikasi
- Alur pembayaran dan komunikasi melalui WhatsApp
- Tampilan responsive untuk desktop dan mobile

## Teknologi yang Digunakan
- PHP
- MySQL / MariaDB
- HTML, CSS, JavaScript
- Bootstrap-style custom CSS (tanpa framework tambahan)

## Struktur Proyek
- `pages/` - halaman utama aplikasi
- `includes/` - konfigurasi, fungsi, header, footer
- `css/` - file styling
- `js/` - file JavaScript interaktif
- `uploads/` - direktori unggahan file
- `database.sql` - struktur database

## Persiapan Environment
1. Pastikan PHP dan MySQL sudah terinstall.
2. Buat database sesuai isi file `database.sql`.
3. Sesuaikan konfigurasi database di `includes/config.php`.
4. Jalankan aplikasi dengan perintah:

```bash
php -S localhost:8000
```

5. Buka browser ke:

```text
http://localhost:8000/
```

## Catatan
- Pastikan folder `uploads/` memiliki hak akses tulis.
- Untuk tampilan penuh, gunakan browser modern seperti Chrome atau Edge.

## Kontributor
- Tim TemanCOD
