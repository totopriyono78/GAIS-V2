# GAIS: Pemasangan Tahap 1

Yang Anda terima di folder ini adalah kode GAIS, bukan aplikasi Laravel yang sudah lengkap dengan
`vendor/`. Kerangka Laravel dan Filament diunduh di komputer Anda oleh langkah di bawah, karena
lingkungan tempat saya bekerja tidak punya akses ke Packagist.

Contoh isi `.env` ada di berkas `env-gais.txt`, bukan `.env.example`, karena berkas berawalan titik tidak bisa saya tulis lewat jembatan ke komputer Anda.

Yang saya siapkan ada di folder `overlay/`, isinya model, migrasi, seeder, layar Filament,
berkas bahasa Indonesia, dan berkas CSS identitas. Semua itu disalin ke atas kerangka Laravel.

## Cara cepat

Buka PowerShell di folder ini, lalu jalankan:

```powershell
Set-ExecutionPolicy -Scope Process -ExecutionPolicy Bypass
.\setup.ps1
```

Skrip itu hanya menjalankan perintah composer, artisan, npm, dan menyalin berkas. Skrip itu tidak
menyunting isi berkas source mana pun.

Sebelum menjalankannya, buat dulu databasenya di PostgreSQL:

```powershell
psql -U postgres -c "CREATE DATABASE gais"
```

## Cara manual

Kalau Anda lebih suka menjalankan perintahnya satu per satu:

```powershell
# 1. Kerangka Laravel, dibuat di folder sementara lalu dipindah ke sini
composer create-project "laravel/laravel:^13.0" .laravel-tmp
Get-ChildItem .laravel-tmp -Force | ForEach-Object { Move-Item $_.FullName . -Force }
Remove-Item .laravel-tmp -Recurse -Force

# 2. Filament 5
composer require "filament/filament:~5.0"
php artisan filament:install --panels

# 3. Berkas GAIS
Copy-Item .\overlay\* . -Recurse -Force

# 4. Konfigurasi
Copy-Item env-gais.txt .env -Force
php artisan key:generate
# buka .env, sesuaikan DB_USERNAME dan DB_PASSWORD

# 5. Tabel dan data awal
php artisan migrate --seed

# 6. Aset
php artisan filament:assets
php artisan storage:link
npm install
npm run build

# 7. Jalankan
php artisan serve
```

Buka `http://localhost:8000`. Alamat pangkal langsung dialihkan ke `/admin`, dan kalau Anda belum
masuk, Filament mengarahkan ke halaman login.

## Akun pertama

Akun administrator dibuat oleh seeder memakai `GAIS_ADMIN_EMAIL` di `.env`, bawaannya `admin@gais.test`.
Kalau `GAIS_ADMIN_PASSWORD` dikosongkan, seeder membuat kata sandi acak dan mencetaknya **sekali** di layar
saat `php artisan migrate --seed` berjalan.

Kalau baris itu terlewat, atau Anda mengisi `GAIS_ADMIN_PASSWORD` setelah akunnya terlanjur dibuat,
menjalankan ulang `migrate --seed` tidak akan mengubah apa apa. Itu disengaja: seeder tidak boleh diam diam
mereset kata sandi akun yang sudah dipakai orang. Gantilah dengan perintah ini:

```powershell
php artisan gais:admin-password
```

Perintah itu memakai email dari `GAIS_ADMIN_EMAIL`, meminta kata sandi baru dua kali, dan ketikannya tidak
ditampilkan di layar. Untuk akun lain, sebutkan emailnya:

```powershell
php artisan gais:admin-password nama@perusahaan.co.id
```

Akun pertama bertanda super admin, artinya melewati seluruh pemeriksaan izin. Untuk menguji apakah pengaturan
role benar benar bekerja, buat akun kedua tanpa tanda super admin dan beri satu role saja.

## Data contoh untuk demo

Tidak ada data contoh yang masuk otomatis, supaya tidak ada nama karyawan atau nilai aset karangan yang
terbawa ke data sungguhan. Untuk demo, isi dengan dua perintah berurutan:

```powershell
php artisan gais:coa-demo
php artisan gais:demo-data
```

Yang dibuat: 5 departemen (GA, FIN, IT, HRD, OPS), 1 gedung dengan 3 lantai, 12 ruangan dan 2 area,
12 karyawan, serta 154 aset yang tersebar di seluruh kategori, departemen, lokasi, dan tahun perolehan
2018 sampai sekarang. Jumlah asetnya bisa diatur, misalnya `php artisan gais:demo-data --jumlah=400`.

Data itu sengaja dibuat tidak seragam supaya fitur yang ada benar benar terlihat saat diperagakan:
sebagian aset tanpa lokasi, sebagian tanpa penanggung jawab, statusnya bercampur antara dipakai,
dipinjam, dan sedang diperbaiki, dan kondisinya bercampur antara baik, perlu perbaikan, dan rusak.
Jadi penyaring, badge status, dan penghitung nomor per tahun semuanya ada isinya.

**Semua nama orang, merek, dan angka di dalamnya karangan.** Penandanya: karyawan demo memakai NIP
berawalan `DEMO-`, dan setiap aset demo diberi catatan `[DATA DEMO]`.

Membersihkannya kembali sebelum data sungguhan masuk:

```powershell
php artisan gais:demo-data --hapus
php artisan gais:coa-demo --hapus
```

Perintah itu hanya menghapus yang bertanda demo, termasuk jejak auditnya, dan tidak menyentuh data yang
Anda masukkan sendiri. Departemen yang sudah dipakai data lain juga dilewati, bukan dihapus paksa.

Satu hal yang tidak ikut mundur: nomor urut kode aset. Kalau demo sudah membuat `GA-1209-2026-0042`,
aset sungguhan berikutnya di kombinasi yang sama akan mulai dari 0043, bukan 0001. Kalau nomor urut itu
harus bersih, kosongkan tabel `number_sequence_periods` setelah menghapus data demo.

## Kalau ada yang gagal

| Gejala | Yang biasanya jadi sebabnya |
|---|---|
| `could not find driver` saat migrate | Ekstensi `pdo_pgsql` belum aktif di `php.ini` |
| `SQLSTATE[08006] connection refused` | Layanan PostgreSQL belum jalan, atau port bukan 5432 |
| `database "gais" does not exist` | Databasenya belum dibuat, jalankan perintah `CREATE DATABASE` di atas |
| Menu tidak muncul setelah masuk | Akun Anda belum punya role, atau rolenya belum dicentang aksi Lihat |
| Tampilan polos tanpa gaya | `npm run build` atau `php artisan filament:assets` belum dijalankan |
| Foto aset tidak tampil, hanya tulisan memuat yang berputar lama | Alamat foto dulu mengikuti `APP_URL`. Kalau `APP_URL` berisi `localhost` sedangkan aplikasi dibuka lewat `127.0.0.1`, peramban di Windows mencoba `::1` dulu dan permintaannya menggantung. Sejak 6 September 2026 alamat foto dibuat relatif, jadi selalu mengikuti host yang sedang dipakai. Kalau masih kosong, pastikan `public/storage` ada, kalau tidak jalankan `php artisan storage:link` |
| Isi halaman tertutup sidebar, atau tabel harus digeser ke samping | Klik tombol lipat di sebelah nama GAIS di kiri atas untuk menyempitkan sidebar jadi ikon saja. Pilihan itu diingat peramban Anda. Kalau masih sempit, kurangi kolom lewat tombol pengatur kolom di kanan atas tabel |
| Yang muncul halaman sambutan Laravel, bukan GAIS | Aplikasi ada di `/admin`, bukan di alamat pangkal. Sejak 6 September 2026 alamat pangkal sudah dialihkan otomatis. Kalau masih muncul, jalankan `php artisan optimize:clear` |
| `Constant PDO::MYSQL_ATTR_SSL_CA is deprecated since 8.5` | Berkas `config/database.php` masih memakai konstanta lama. Sudah diperbaiki, berkasnya memilih konstanta sesuai versi PHP yang jalan |

### Foto aset

Foto disimpan di `storage/app/public/foto-aset` dan dilayani lewat tautan `public/storage`. Dua hal yang
perlu diketahui:

- Alamat fotonya relatif (`/storage/...`), jadi tidak bergantung pada isi `APP_URL`. Ini disengaja,
  alasannya ada di komentar `config/filesystems.php`.
- Untuk mengganti foto, hapus dulu yang lama lewat tanda silang di pojok gambar, baru unggah yang baru.
  Filament tidak punya tombol ganti terpisah. Ada juga tombol buka dan unduh di bawah gambar.

Kalau `public/storage` hilang, jalankan `php artisan storage:link`. Di Windows perintah itu kadang perlu
PowerShell yang dijalankan sebagai Administrator, atau Developer Mode diaktifkan, karena membuat symlink.

### Versi PHP dan Laravel

Proyek ini memakai **PHP 8.5 dan Laravel 13**. Kombinasi itu dipilih pada 6 September 2026 setelah
Laravel 12 memunculkan peringatan deprecated di PHP 8.5. Laravel 13 menyasar PHP 8.3 ke atas, jadi
PHP 8.5 memang jalurnya, bukan sekadar tambalan.

Kalau muncul peringatan deprecated lain dari paket pihak ketiga, kirimkan pesannya apa adanya.
Peringatan seperti itu tidak menghentikan aplikasi, tapi tetap perlu dibereskan supaya layar bersih.

## Menaikkan aplikasi yang sudah terpasang ke Laravel 13

Kalau Anda sudah memasang aplikasi dengan Laravel 12, tidak perlu memasang ulang dari nol.
Berkas `composer.json` sudah saya perbarui. Jalankan ini di folder proyek:

```powershell
composer update --with-all-dependencies
php artisan optimize:clear
php artisan filament:upgrade
php artisan filament:assets
npm install
npm run build
```

Yang berubah di `composer.json`: `php` naik ke `^8.3`, `laravel/framework` ke `^13.0`,
`phpunit/phpunit` ke `^12.0`, dan beberapa paket pengembangan dilonggarkan supaya boleh naik versi.

Satu berkas kode ikut berubah: `app/Providers/Filament/AdminPanelProvider.php`. Laravel 13 mengganti
nama middleware `VerifyCsrfToken` menjadi `PreventRequestForgery` dan menambahkan pemeriksaan asal
permintaan lewat header `Sec-Fetch-Site`. Nama lama masih ada sebagai alias yang deprecated, tapi
lebih baik langsung memakai nama barunya.

Tidak ada perubahan yang dibutuhkan di `config/cache.php`. Laravel 13 melarang menyimpan objek PHP di
cache kecuali kelasnya didaftarkan, sedangkan GAIS hanya menyimpan array berisi teks, jadi aman.

## Memasang pembaruan Tahap 2

Berkas GAIS yang baru sudah ada di folder `overlay/` dan sudah disalin ke tempatnya. Jalankan ini:

```powershell
php artisan migrate
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=SettingSeeder
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AssetCategorySeeder
php artisan optimize:clear
```

`AssetCategorySeeder` mengisi 13 kelas aset standar beserta kelompok pajaknya, tetapi
**nomor akun COA-nya sengaja kosong**. Itu bukan kelalaian: nomor akun jadi segmen kedua kode aset
dan ikut tercetak di stiker, jadi angkanya harus datang dari bagan akun perusahaan Anda, bukan dari
tebakan saya. Buka menu Aset, Kategori aset, lalu isi nomor akunnya bersama tim finance.
Kategori yang nomor akunnya masih kosong ditandai merah dan belum muncul saat mencatat aset.

### Nomor akun contoh untuk demo

Kalau Anda perlu mendemokan aplikasinya sebelum bagan akun yang sebenarnya tersedia, isi nomor akun
contoh dengan satu perintah:

```powershell
php artisan gais:coa-demo
```

Polanya yang lazim dipakai di Indonesia: 12xx untuk aset tetap, 13xx untuk akumulasi penyusutan,
62xx untuk beban penyusutan.

| Kategori | Akun aset | Akumulasi | Beban |
|---|---|---|---|
| Tanah | 1201 | tidak disusutkan | tidak disusutkan |
| Bangunan permanen | 1202 | 1302 | 6202 |
| Bangunan tidak permanen | 1203 | 1303 | 6203 |
| Prasarana dan instalasi | 1204 | 1304 | 6204 |
| Kendaraan roda empat | 1205 | 1305 | 6205 |
| Kendaraan roda dua | 1206 | 1306 | 6206 |
| Perabot kantor kayu | 1207 | 1307 | 6207 |
| Perabot kantor logam | 1208 | 1308 | 6208 |
| Komputer dan perangkat jaringan | 1209 | 1309 | 6209 |
| Mesin dan peralatan kantor | 1210 | 1310 | 6210 |
| Alat komunikasi | 1211 | 1311 | 6211 |
| Peralatan pengatur udara | 1212 | 1312 | 6212 |
| Aset dalam penyelesaian | 1213 | tidak disusutkan | tidak disusutkan |

Tiga pengaman yang menyertainya:

- Kategori yang nomor akunnya sudah diisi orang tidak ditimpa. Perintah ini hanya mengisi yang kosong
  atau yang sebelumnya diisi olehnya sendiri.
- Setiap kategori yang diisi diberi penanda `[COA CONTOH]` di keterangannya, dan badge nomor akunnya
  berwarna kuning, bukan hijau. Jadi di layar terlihat jelas mana yang belum final.
- Membalikkannya cukup satu perintah:

```powershell
php artisan gais:coa-demo --hapus
```

Yang perlu diingat: **nomor akun ikut membentuk kode aset**. Aset yang dibuat saat demo akan berkode
misalnya `FIN-1209-2026-0001`. Mengosongkan nomor akun tidak mengubah kode aset yang terlanjur dibuat,
jadi hapus dulu aset demonya sebelum data sungguhan masuk. Perintah `--hapus` akan memperingatkan
kalau masih ada aset yang memakai nomor akun contoh.

`RoleSeeder` sekarang hanya menambahkan izin, tidak pernah mencabut. Jadi kalau Anda sudah menyesuaikan
centang izin di layar Role, penyesuaian itu tetap aman saat seeder dijalankan lagi.

Dua perubahan di tabel yang perlu Anda tahu. Pertama, kolom `next_number` dan `current_period` di
`number_sequences` dipindah ke tabel baru `number_sequence_periods`, satu baris penghitung per periode.
Alasannya ada di `ARCHITECTURE.md` bagian 1. Tidak ada data yang hilang karena penomoran dokumen belum
dipakai modul mana pun sebelum ini. Kedua, `asset_categories.code_prefix` dihapus dan diganti
`tax_group`, karena awalan kode per kategori tidak dipakai lagi setelah format kode aset diubah.

Setelah itu, kerjakan daftar verifikasi di `DELIVERY-GATE-2.md` bagian 5.

## Mengatur tampilan tabel

Dua hal yang bisa Anda atur sendiri tanpa mengubah kode:

**Sidebar.** Tombol lipat ada di kiri atas, di sebelah nama aplikasi. Sekali klik, sidebar menyempit
jadi deretan ikon dan isi halaman mendapat tambahan ruang sekitar 13rem. Pilihan itu disimpan di
peramban masing masing pengguna, jadi tiap orang bisa berbeda.

**Kolom tabel.** Tombol pengatur kolom ada di kanan atas tiap tabel. Secara bawaan tiap tabel hanya
menampilkan tiga sampai lima kolom yang paling sering dipakai, supaya muat dalam satu layar tanpa perlu
digeser ke samping. Di Daftar aset yang tampil adalah kode, nama, lokasi, status, dan kondisi.
Kategori, departemen, penanggung jawab, nilai perolehan, dan tanggal perolehan disembunyikan tapi
tinggal dicentang kalau dibutuhkan. Pilihan ini diingat per pengguna.

**Tombol aksi di tiap baris** berbentuk ikon saja: printer untuk cetak label, pensil untuk ubah,
tempat sampah untuk hapus. Arahkan penunjuk ke ikonnya untuk melihat namanya. Bentuk ikon dipilih
karena tiga tombol bertuliskan teks memakan lebar yang sama dengan satu kolom data.

## Memasang pembaruan stock opname

```powershell
php artisan migrate
php artisan db:seed --class=ModuleSeeder
php artisan db:seed --class=NumberSequenceSeeder
php artisan db:seed --class=RoleSeeder
php artisan optimize:clear
```

Alur pemakaiannya lima langkah, dan tombolnya muncul bergantian sesuai status sesi:

1. **Buat sesi**, tentukan cakupannya. Kosongkan yang tidak dipakai
2. **Susun daftar target**, sistem mengambil aset yang masuk cakupan beserta lokasi dan kondisi tercatatnya
3. **Mulai pemeriksaan**, setelah ini daftar tidak bisa disusun ulang
4. **Catat temuan** per baris. Dengan pemindai USB: pindai barcode, kodenya masuk ke kotak pencarian,
   barisnya muncul, tekan tombol centang. Kalau ada bedanya, pakai tombol pensil
5. **Selesaikan**, lalu **terapkan penyesuaian** supaya lokasi dan kondisi di data aset ikut diperbarui

Penyesuaian dipisah dari penyelesaian dan butuh izin `stock_opnames.approve`, karena mengubah data induk
aset harus jadi tindakan yang disengaja. Aset yang tidak ditemukan tidak diubah statusnya secara otomatis.

## Format kode aset

```
[Kode Departemen] - [Kode Akun COA] - [Tahun Perolehan] - [Nomor Urut]
       FIN         -      1201       -       2026        -     0001
```

Nomor urut dihitung terpisah untuk tiap kombinasi departemen, akun, dan tahun. Jadi komputer milik
Finance tahun 2026 mulai dari 0001, dan kendaraan milik Finance tahun 2026 juga mulai dari 0001.

Karena itu, saat mencatat aset, kolom Departemen dan Kategori wajib diisi. Impor CSV juga menolak
baris yang salah satunya kosong, dengan pesan yang menyebut alasannya.

## Ukuran label stiker

Bawaannya lembar A4 isi 24 stiker: 70 kali 37 mm, 3 kolom. Kalau merek stiker Anda berbeda, ubah
angkanya di menu Pengaturan, kelompok `label`. Tidak ada kode yang perlu disentuh.

Cara paling aman mengecek posisi: cetak satu halaman di kertas HVS biasa, lalu tumpuk di atas lembar
stiker sambil diterawang. Kalau bergeser, ubah `label.margin_atas_mm` dan `label.margin_kiri_mm`.

## Menambah modul di tahap berikutnya

1. Tambahkan barisnya di `database/seeders/ModuleSeeder.php`, sebutkan aksi yang benar benar ada tombolnya
2. Jalankan `php artisan db:seed --class=ModuleSeeder`, izinnya ikut dibuat sendiri
3. Buat Filament Resource yang memakai trait `AuthorizesModule` dan menyebut `$moduleCode` yang sama
4. Centang izinnya di layar Role
