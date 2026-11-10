# Sistem Peminjaman Buku Perpustakaan Sekolah

## Baca Dokumentasi Utama Dulu

**[Buka atau unduh DalilulFalihin_Dokumentasi.docx](./DalilulFalihin_Dokumentasi.docx)**

> [!IMPORTANT]
> Mulai dari **`DalilulFalihin_Dokumentasi.docx`**. Dokumen ini berisi ERD, penjelasan program, dokumentasi halaman admin dan siswa, catatan debugging, serta evaluasi proyek.

Kalau GitHub tidak menampilkan pratinjaunya, unduh file lalu buka dengan Microsoft Word atau LibreOffice Writer.

Setelah membaca dokumen tersebut, gunakan README ini untuk mengenal fitur dan mencoba aplikasinya di komputer sendiri. Penjelasan di bawah mengikuti kode yang ada di repositori.

### Mau Langsung ke Bagian Tertentu?

[Tentang proyek](#tentang-proyek) · [Teknologi](#teknologi) · [Fitur](#fitur-dan-hak-akses) · [Alur peminjaman](#cara-meminjam-dan-mengembalikan-buku) · [Instalasi](#menjalankan-di-komputer-sendiri) · [Struktur kode](#mengenal-struktur-kode-dan-data) · [Catatan pengembangan](#catatan-pengembangan)

## Tentang Proyek

Aplikasi ini dibuat untuk membantu pencatatan peminjaman buku di perpustakaan sekolah. Admin bisa mengelola koleksi buku dan data pengguna, sementara siswa bisa melihat koleksi, meminjam, lalu mengembalikan buku melalui aplikasi.

Setiap peminjaman menyimpan tanggal pinjam, batas pengembalian, dan status buku. Saat buku dikembalikan terlambat, aplikasi menghitung dendanya.

- **Penyusun:** Dalilul Falihin.
- **Kelas:** XII RPL 3 (Desktop).
- **Paket UKK:** Paket 3, Peminjaman Buku.

## Teknologi

| Komponen | Teknologi |
| --- | --- |
| Bahasa pemrograman | PHP `^8.2` |
| Framework | Laravel `^12.0` |
| Panel aplikasi | Filament `4.0` |
| Database bawaan | SQLite |
| Aset frontend | Vite `^6.0.11` dan Tailwind CSS `^4.0.0` |
| Pengujian | PHPUnit `^11.5.3` |

Rincian versi dan dependensi bisa dilihat di [`composer.json`](./composer.json) dan [`package.json`](./package.json).

## Fitur dan Hak Akses

Admin dan siswa masuk lewat halaman yang sama, yaitu `/admin`. Menu dan tindakan yang tersedia mengikuti peran akun: `admin` atau `siswa`. Akun yang baru didaftarkan otomatis mendapat peran siswa.

| Fitur | Admin | Siswa |
| --- | --- | --- |
| Login dan dashboard | Ya | Ya |
| Melihat dan mencari buku berdasarkan judul, penulis, atau penerbit | Ya | Ya |
| Menambah, mengubah, dan menghapus buku | Ya | Tidak |
| Mengelola pengguna, kelas, dan peran | Ya | Tidak |
| Meminjam lewat tombol `pinjam` di daftar buku | Tidak | Ya |
| Melihat daftar peminjaman | Semua peminjaman | Hanya peminjaman sendiri |
| Mengembalikan buku berstatus `dipinjam` | Ya | Hanya peminjaman sendiri |
| Menambah, mengedit, atau menghapus transaksi secara manual | Tidak | Tidak |

Setiap buku memiliki judul, penulis, penerbit, tahun terbit, dan stok. Di dashboard, kamu bisa melihat jumlah data buku, transaksi berstatus `dipinjam`, dan akun siswa.

> [!NOTE]
> **Total Peminjam** menghitung transaksi yang masih aktif, bukan jumlah siswa yang berbeda. **Total Buku** menghitung data buku, bukan jumlah seluruh eksemplar dalam stok.

## Cara Meminjam dan Mengembalikan Buku

1. Login sebagai siswa, buka daftar buku, lalu klik **pinjam** dan konfirmasi pilihanmu. Tombol ini tidak bisa digunakan kalau stok buku habis.
2. Aplikasi mencatat tanggal pinjam hari ini dan batas pengembalian **tujuh hari kemudian**. Status menjadi `dipinjam`, lalu stok berkurang satu.
3. Saat ingin mengembalikan buku, buka daftar peminjaman, klik **Kembalikan Buku**, lalu konfirmasi.
4. Aplikasi mencatat tanggal kembali, mengubah status menjadi `dikembalikan`, menghitung denda jika terlambat, dan menambah stok satu.

> [!NOTE]
> Denda dihitung **saat buku dikembalikan**, sebesar **Rp1.000 per hari keterlambatan** dari tanggal jatuh tempo (`due_date`). Pengembalian sebelum atau tepat pada tanggal tersebut tidak dikenai denda. Aplikasi belum menyediakan fitur pembayaran denda.

## Menjalankan di Komputer Sendiri

### Yang Perlu Disiapkan

- PHP 8.2 atau lebih baru yang kompatibel dengan dependensi Composer, beserta ekstensi Laravel/Filament, termasuk `intl`, `mbstring`, PDO, dan `pdo_sqlite` untuk SQLite.
- Composer.
- Node.js yang kompatibel dengan Vite 6, disarankan Node.js 22 LTS, beserta npm.

### Instalasi

Unduh atau clone repositori ini, lalu buka terminal di folder proyek. Ikuti langkah berikut secara berurutan.

1. Siapkan konfigurasi lokal dan dependensi:

   ```bash
   cp .env.example .env
   composer install
   npm install
   php artisan key:generate
   ```

   > [!WARNING]
   > Jika file `.env` sudah ada, lewati perintah `cp .env.example .env` agar konfigurasi yang kamu gunakan tidak tertimpa.

2. Buka `.env`, lalu sesuaikan nama aplikasi dan URL. Contoh ini memakai SQLite sesuai konfigurasi bawaan proyek:

   ```dotenv
   APP_NAME="Perpustakaan Sekolah"
   APP_URL=http://127.0.0.1:8000
   DB_CONNECTION=sqlite
   ```

3. Buat file SQLite jika belum ada, lalu jalankan migrasi:

   ```bash
   php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
   php artisan migrate
   ```

   Migrasi juga menyiapkan tabel sesi, cache, dan antrean yang digunakan dalam `.env.example`. Untuk saat ini, jalankan tanpa `--seed`. Alasannya ada di bagian [catatan pengembangan](#catatan-pengembangan).

4. Buat akun admin:

   ```bash
   php artisan make:filament-user
   ```

   Isi nama, email, dan kata sandi saat diminta. Akun yang dibuat masih mendapat peran bawaan `siswa`, jadi perannya perlu diubah menjadi `admin`. Buka Tinker:

   ```bash
   php artisan tinker
   ```

   Jalankan kode berikut. Ganti `admin@example.test` dengan email akun yang baru dibuat:

   ```php
   App\Models\User::where('email', 'admin@example.test')->firstOrFail()->update(['role' => 'admin']);
   ```

   Ketik `exit` untuk keluar dari Tinker.

5. Siapkan aset tampilan, lalu jalankan server lokal:

   ```bash
   npm run build
   php artisan serve
   ```

   Buka [halaman login](http://127.0.0.1:8000/admin) untuk masuk. Siswa bisa membuat akun lewat [halaman registrasi](http://127.0.0.1:8000/admin/register).

   > [!TIP]
   > Gunakan alamat `/admin` untuk membuka aplikasi perpustakaan. Halaman utama `/` masih menampilkan halaman sambutan Laravel.

### Coba Alur Pertamanya

1. Login sebagai admin, buka menu **Books**, lalu tambahkan buku dengan stok lebih dari nol.
2. Buat akun siswa lewat halaman registrasi atau menu **Users** sebagai admin. Admin juga bisa mengisi kelas siswa melalui menu tersebut.
3. Login sebagai siswa, buka **Books**, lalu pilih **pinjam** pada buku yang tersedia.
4. Buka **Loans** untuk melihat transaksi dan mengembalikan buku.

### Perintah yang Bisa Digunakan

| Perintah | Fungsi |
| --- | --- |
| `php artisan serve` | Menjalankan server aplikasi lokal |
| `npm run dev` | Menjalankan Vite saat mengembangkan aset, di terminal terpisah dari server PHP |
| `npm run build` | Membangun aset untuk digunakan tanpa server Vite |
| `php artisan test` | Menjalankan pengujian yang tersedia |
| `php artisan optimize:clear` | Membersihkan cache konfigurasi dan aplikasi setelah perubahan konfigurasi |

## Mengenal Struktur Kode dan Data

Kalau ingin mempelajari atau mengubah aplikasinya, mulai dari folder berikut.

| Lokasi | Isi |
| --- | --- |
| [`app/Models`](./app/Models) | Model `User`, `Book`, dan `Loan` |
| [`app/Filament/Resources/Books`](./app/Filament/Resources/Books) | Form buku, tabel, hak akses, dan aksi peminjaman |
| [`app/Filament/Resources/Loans`](./app/Filament/Resources/Loans) | Pembatasan riwayat siswa, tabel peminjaman, pengembalian, dan denda |
| [`app/Filament/Resources/Users`](./app/Filament/Resources/Users) | Pengelolaan pengguna oleh admin |
| [`app/Filament/Widgets/StatsOverview.php`](./app/Filament/Widgets/StatsOverview.php) | Statistik dashboard |
| [`app/Providers/Filament/AdminPanelProvider.php`](./app/Providers/Filament/AdminPanelProvider.php) | Konfigurasi panel `/admin`, login, dan registrasi |
| [`database/migrations`](./database/migrations) | Struktur tabel aplikasi |
| [`database/seeders/DatabaseSeeder.php`](./database/seeders/DatabaseSeeder.php) | Data contoh pengguna dan buku |

Tabel utama:

- **`users`**: nama, email unik, kata sandi, peran, dan kelas.
- **`books`**: judul (`title`), penulis (`author`), penerbit (`publisher`), tahun (`year`), dan stok (`stock`).
- **`loans`**: referensi pengguna (`user_id`) dan buku (`book_id`), tanggal pinjam (`loan_date`), jatuh tempo (`due_date`), tanggal kembali (`returned_at`), status, dan denda.

Setiap transaksi terhubung ke satu pengguna dan satu buku. Satu pengguna bisa memiliki banyak transaksi, begitu juga satu buku.

> [!WARNING]
> Relasi database menggunakan `cascadeOnDelete`. Menghapus pengguna atau buku juga menghapus riwayat peminjaman yang terkait. Periksa data terlebih dahulu sebelum menghapusnya.

## Catatan Pengembangan

Ada beberapa hal dalam kode yang perlu diperhatikan saat mencoba atau melanjutkan proyek ini. Klik bagian berikut untuk membaca rinciannya.

<details>
<summary><strong>Batas pinjaman dan data contoh</strong></summary>

- **Batas pinjaman belum sesuai pesan aplikasi.** Pesannya menyebut maksimal tiga buku, tetapi kondisi di `BooksTable.php` masih `$total_buku_dipinjam > 3`. Siswa yang sudah memiliki tiga pinjaman aktif masih bisa meminjam buku keempat.
- **Seeder perlu diperbaiki sebelum digunakan.** Di `DatabaseSeeder.php`, data penulis memakai kunci `Author`, sedangkan nama kolom dan atribut model adalah `author`. Akibatnya, penyimpanan buku contoh bisa gagal karena penulis tidak terisi. Itu sebabnya panduan di atas memakai migrasi tanpa seeder dan membuat akun secara manual.

</details>

<details>
<summary><strong>Stok, pengujian, dan penggunaan di server</strong></summary>

- **Perubahan stok saat akses bersamaan perlu diuji.** Pembuatan transaksi dan perubahan stok belum dibungkus dalam transaksi database atau penguncian baris. Peminjaman dan pengembalian yang dilakukan bersamaan perlu diperiksa sebelum aplikasi digunakan bersama.
- **Pengujian masih berupa contoh bawaan.** Folder `tests/` belum berisi pengujian khusus untuk hak akses, peminjaman, stok, dan perhitungan denda.
- **Panduan ini untuk penggunaan lokal.** Model `User` belum menerapkan kontrak `FilamentUser` dan `canAccessPanel()` untuk akses panel di luar lingkungan lokal. Siapkan dan uji pengaturan akses produksi sebelum memublikasikan aplikasi.

</details>

---

Butuh ERD dan dokumentasi tampilan? Buka kembali **[Dokumentasi Utama (.docx)](./DalilulFalihin_Dokumentasi.docx)**.
