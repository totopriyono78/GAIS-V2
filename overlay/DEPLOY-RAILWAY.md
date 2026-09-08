# Menaikkan GAIS ke Railway

Panduan ini menaikkan GAIS ke Railway sebagai dua layanan: aplikasi dan PostgreSQL, dengan
satu volume untuk berkas unggahan, dan seluruh data yang sekarang ada di komputer Anda ikut
disalin ke sana.

Ditulis 8 September 2026 untuk repositori `totopriyono78/GAIS-V2` cabang `main`.

**Perkiraan waktu:** sekitar satu jam kalau lancar, dan sebagian besarnya menunggu.

**Biaya.** Railway sudah tidak punya paket gratis yang memadai untuk ini. Paket Free hanya
memberi 0,5 GB memori dan 0,5 GB volume, terlalu sempit untuk Laravel dengan PostgreSQL di
sampingnya. Yang masuk akal adalah paket **Hobby**, 5 dolar per bulan sudah termasuk pemakaian
5 dolar dan volume sampai 5 GB. Akun baru mendapat kredit percobaan 5 dolar satu kali, cukup
untuk mencoba beberapa hari sebelum memutuskan. Angka ini per September 2026, periksa lagi di
halaman harga Railway sebelum berlangganan.

---

## Bagian 0: tiga perubahan di kode, sudah saya siapkan

Ketiganya sudah ada di folder proyek Anda. Yang perlu Anda lakukan hanya mengirimkannya ke
GitHub, karena Railway membangun dari GitHub, bukan dari komputer Anda.

**1. `bootstrap/app.php`, mempercayai proxy.**

Railway menyelesaikan HTTPS di proxy mereka lalu meneruskan permintaannya ke aplikasi lewat
HTTP biasa. Tanpa perubahan ini Laravel membaca permintaannya sebagai http, sehingga setiap
alamat yang ia buat sendiri berawalan http di halaman yang dibuka lewat https. Peramban
menolaknya sebagai isi campuran, dan berkas gaya, ikon, serta foto unggahan berhenti tampil.
Efek keduanya, alamat asal di log masuk menjadi alamat proxy dan sama untuk semua orang.

**2. `railway/init-app.sh`, dijalankan sebelum tiap versi baru melayani.**

Isinya migrasi dan seeder, dengan `gais:sync-permissions` disisipkan pada urutan yang benar.
Ini yang tidak dilakukan Railway sendiri: ia tahu cara menjalankan `migrate` dan `db:seed`
untuk Laravel, tetapi tidak tahu bahwa proyek ini punya langkah menurunkan izin dari modul di
antara keduanya. Tanpa langkah itu, peran Manajer GA dan Staf GA lahir tanpa satu izin pun,
dan orang yang masuk dengan peran itu melihat panel tanpa menu apa apa.

**3. Berkas panduan ini sendiri.**

### Kirim ketiganya ke GitHub

Di folder `D:\DEVELOPMENT\Sistem GA`:

```bash
git add bootstrap/app.php railway/init-app.sh DEPLOY-RAILWAY.md
git add app resources database
git commit -m "Siapkan penerapan ke Railway"
git push origin main
```

Sebelum melanjutkan, pastikan tiga hal ini benar benar ikut terkirim, karena ketiganya mudah
tertinggal dan ketiganya membuat aplikasi tampil rusak di Railway:

- `composer.lock`. Tanpa berkas ini Railway menghitung ulang versi paket sendiri, dan proyek
  ini memakai Laravel 13 dan Filament 5 yang masih bergerak, jadi hasilnya bisa berbeda dari
  yang berjalan di komputer Anda.
- `public/fonts/ibm-plex-sans/`. Huruf diinangkan sendiri, tidak diambil dari internet. Kalau
  foldernya tidak ikut, seluruh tampilan berganti ke huruf bawaan sistem.
- `resources/css/gais.css`. Warna peluncur menu, latar kertas, dan garis terracotta ada di
  sini.

Periksa dengan `git status` sebelum commit, dan `git ls-files public/fonts | head` setelah
push.

---

## Bagian 1: siapkan Railway

1. Masuk ke [railway.com](https://railway.com) dengan akun GitHub yang sama dengan pemilik
   repositori `GAIS-V2`.
2. **New Project**, lalu pilih **Empty Project**. Jangan pilih template Laravel, karena
   template itu membawa susunan variabelnya sendiri yang nanti perlu Anda bongkar lagi.
3. Beri nama proyeknya, misalnya `gais`.

## Bagian 2: buat basis datanya lebih dulu

Basis data dibuat lebih dulu supaya datanya sudah ada saat aplikasinya pertama kali menyala.

1. Di kanvas proyek, **Create** lalu **Database** lalu **Add PostgreSQL**.
2. Tunggu sampai layanannya hijau.
3. Buka layanan Postgres itu, tab **Variables**, dan catat dua nilai:
   - `DATABASE_URL`, alamat dari dalam jaringan Railway. Ini yang nanti dipakai aplikasi.
   - `DATABASE_PUBLIC_URL`, alamat dari luar. Ini yang Anda pakai dari komputer untuk
     menyalin data.

`DATABASE_PUBLIC_URL` adalah pintu masuk ke basis data Anda dari internet. Perlakukan seperti
kata sandi, dan jangan tempelkan ke mana pun selain terminal Anda sendiri.

## Bagian 3: salin data dari komputer ke Railway

Basis data lokal Anda bernama `gais`, di `127.0.0.1:5432` dengan pengguna `postgres`. Kata
sandinya ada di `.env` pada kunci `DB_PASSWORD`.

**Buat salinannya.** Di folder proyek, jalankan:

```bash
pg_dump --no-owner --no-privileges --clean --if-exists -h 127.0.0.1 -p 5432 -U postgres -d gais -f gais-lokal.sql
```

`--no-owner` dan `--no-privileges` dipakai karena nama pengguna di Railway berbeda dengan
`postgres` di komputer Anda, dan tanpa kedua bendera itu pemulihannya berhenti pada perintah
kepemilikan yang tidak berlaku di sana. `--clean --if-exists` membuat berkasnya bisa
dijalankan berulang kali tanpa menggandakan apa pun.

**Kirimkan ke Railway.** Ganti bagian dalam tanda kutip dengan `DATABASE_PUBLIC_URL` tadi:

```bash
psql "postgresql://postgres:xxxx@xxxx.proxy.rlwy.net:12345/railway" -f gais-lokal.sql
```

**Periksa hasilnya:**

```bash
psql "postgresql://..." -c "select count(*) from business_trips;"
```

Jumlahnya harus sama dengan yang ada di komputer Anda. Kalau perintah `pg_dump` atau `psql`
tidak dikenali, keduanya ada di folder `bin` instalasi PostgreSQL Anda, biasanya
`C:\Program Files\PostgreSQL\17\bin`.

**Hapus berkas salinannya setelah selesai.** `gais-lokal.sql` berisi seluruh isi basis data
Anda termasuk hash kata sandi setiap akun, dan ia tidak masuk `.gitignore`, jadi jangan
sampai ikut ter-commit.

## Bagian 4: buat layanan aplikasinya

1. Di kanvas yang sama, **Create** lalu **GitHub Repo**, pilih `totopriyono78/GAIS-V2`.
2. Railway akan mencoba membangun langsung dan kemungkinan besar **gagal pada percobaan
   pertama**. Itu wajar, variabelnya memang belum ada. Lanjutkan ke bagian berikutnya, nanti
   deploy diulang.

Railway mengenali ini sebagai proyek Laravel dari adanya berkas `artisan`, lalu membangunnya
dengan Railpack: PHP dengan FrankenPHP sebagai peladen, akar dokumen di `public`, dan
`npm run build` ikut dijalankan karena ada `package.json`.

## Bagian 5: isi variabelnya

Buka layanan aplikasi, tab **Variables**, lalu **Raw Editor**, dan tempelkan blok berikut
sekaligus.

```
APP_NAME=GAIS
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=id_ID
APP_URL=https://ISI-SETELAH-DOMAIN-DIBUAT

LOG_CHANNEL=stderr
LOG_LEVEL=warning
LOG_STDERR_FORMATTER=\Monolog\Formatter\JsonFormatter

DB_CONNECTION=pgsql
DB_URL=${{Postgres.DATABASE_URL}}

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local

GAIS_ADMIN_NAME=Administrator
GAIS_ADMIN_EMAIL=
GAIS_ADMIN_PASSWORD=
```

Empat nilai yang perlu Anda isi sendiri:

**`APP_KEY`.** Salin persis dari `.env` di komputer Anda, jangan buat yang baru. Data yang
Anda salin di bagian 3 dibuat dengan kunci itu, dan kolom apa pun yang tersimpan terenkripsi
hanya bisa dibuka kembali dengan kunci yang sama. Kunci baru membuat isi kolom seperti itu
menjadi sampah yang tidak bisa dipulihkan.

**`APP_URL`.** Dikosongkan dulu, diisi setelah bagian 8.

**`GAIS_ADMIN_EMAIL` dan `GAIS_ADMIN_PASSWORD`.** Boleh sama dengan yang di `.env` Anda. Kalau
akun dengan alamat surel itu sudah ikut tersalin dari basis data lokal, seeder-nya tidak akan
mengubah kata sandinya, hanya memastikan perannya administrator.

Beberapa catatan atas pilihan di atas:

- `DB_URL` dipakai, bukan `DB_HOST` dan kawan kawannya satu per satu. `${{Postgres.DATABASE_URL}}`
  adalah rujukan antar layanan milik Railway: nilainya diisi Railway sendiri dan ikut berubah
  kalau kata sandi basis datanya diputar. Menuliskan alamatnya satu per satu berarti Anda
  perlu memperbaikinya sendiri setiap kali itu terjadi.
- `LOG_CHANNEL=stderr` karena cakram di Railway tidak permanen. Log yang ditulis ke berkas
  akan hilang bersama wadahnya, sedangkan yang ke stderr masuk ke halaman Logs Railway.
- `QUEUE_CONNECTION=sync`, bukan `database`. Di komputer Anda ada `queue:listen` yang berjalan
  sendiri; di Railway tidak ada, kecuali Anda menambahkan satu layanan pekerja lagi.
  Dengan `database` dan tanpa pekerja, pekerjaan yang diantrekan akan mengendap di tabel
  dan tidak pernah dikerjakan, tanpa satu pun pesan galat. `sync` menjalankannya langsung
  di permintaan yang sama. Kalau nanti ada pekerjaan berat yang tidak pantas menahan
  halaman, saat itulah layanan pekerja pantas ditambahkan.
- `APP_DEBUG=false` bukan formalitas. Dengan `true`, setiap galat menampilkan halaman yang
  memuat isi variabel lingkungan, termasuk kata sandi basis data, kepada siapa pun yang
  membuka alamatnya.

## Bagian 6: pasang volume untuk berkas unggahan

Tanpa langkah ini, setiap struk penggantian, bukti perjalanan, pindaian surat, dan foto
pemeriksaan yang diunggah akan hilang pada deploy berikutnya. Cakram wadah di Railway memang
dibuat ulang setiap kali versi baru naik.

1. Di layanan aplikasi, buka tab **Settings** lalu bagian **Volumes**.
2. **Add Volume**, dan isi **Mount path** dengan tepat:

```
/app/storage/app/public
```

Itu folder yang dipakai seluruh `FileUpload` di aplikasi ini. Tautan `public/storage` yang
menunjuk ke sana sudah dibuat saat citra dibangun, dan tautan hanyalah penunjuk alamat, jadi
ia tetap benar setelah folder tujuannya menjadi volume.

Jangan memasang volume di `/app/storage` saja. Folder itu juga berisi `framework` dan `logs`
yang disiapkan saat citra dibangun, dan menimpanya dengan volume kosong membuat aplikasi
gagal menyala.

## Bagian 7: pre-deploy dan pemeriksaan kesehatan

Masih di **Settings** layanan aplikasi:

1. **Deploy** lalu **Pre-Deploy Command**, isi dengan:

   ```
   chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh
   ```

2. **Healthcheck Path**, isi dengan `/up`. Rute itu memang sudah ada di aplikasi ini. Dengan
   ini Railway hanya mengalihkan lalu lintas ke versi baru setelah versi itu benar benar
   menjawab, bukan segera setelah wadahnya menyala.

3. **Variables**, tambahkan satu lagi:

   ```
   RAILPACK_SKIP_MIGRATIONS=1
   ```

   Railway menjalankan migrasi dan seeder sendiri untuk Laravel. Karena urusan itu sudah
   ditangani `init-app.sh` dengan urutan yang benar, biarkan satu tempat saja yang
   mengerjakannya. Dua tempat yang mengerjakan hal yang sama adalah tempat cacat bersembunyi.

## Bagian 8: alamatnya

1. **Settings** lalu **Networking** lalu **Generate Domain**.
2. Railway memberi alamat seperti `gais-v2-production.up.railway.app`, sudah dengan HTTPS.
3. Salin alamat itu ke variabel `APP_URL`, lengkap dengan `https://` dan tanpa garis miring
   di ujungnya.
4. **Deploy** ulang.

## Bagian 9: periksa hasilnya

Buka alamatnya dan periksa satu per satu. Urutannya sengaja: yang di atas membuktikan yang di
bawah bisa dipercaya.

1. **Halaman masuk tampil dengan huruf dan warna yang benar.** Kalau hurufnya berubah atau
   latarnya putih polos, berarti aset belum terbit. Lihat bagian Kalau gagal.
2. **Masuk dengan akun administrator.** Kalau ditolak padahal kata sandinya benar, biasanya
   `APP_KEY` berbeda dengan yang di komputer.
3. **Peluncur menu terbuka dan ubinnya berwarna.** Ini sekaligus membuktikan `gais.css`
   terbit dengan benar.
4. **Buka Business Trips.** SPD/2026/09/0002 harus ada, berikut lima orang rombongannya. Ini
   membuktikan data salinannya utuh.
5. **Buka Budgets.** Realisasi Perjalanan dinas General Affair harus terbaca Rp 11.175.000,
   sama persis dengan yang di komputer Anda.
6. **Unggah satu berkas**, misalnya bukti di satu rincian perjalanan, lalu buka lagi. Setelah
   itu tekan **Redeploy** di Railway, dan buka berkas itu sekali lagi. Kalau masih terbuka,
   volumenya benar. Ini satu satunya cara membuktikannya, dan sebaiknya dilakukan sekarang,
   bukan setelah ada tiga puluh struk di dalamnya.
7. **Buka halaman Logs Railway** dan pastikan tidak ada baris ERROR.

## Bagian 10: yang wajib dikerjakan setelah live

**Akun uji harus ditutup.** Basis data yang Anda salin berisi `test@gais.test` dengan kata
sandi `password`. Di komputer Anda itu tidak apa apa. Di alamat yang bisa dibuka siapa pun
dari internet, itu pintu terbuka menuju seluruh data GA perusahaan, dan alamat surel seperti
itu adalah hal pertama yang dicoba orang. Masuk sebagai administrator, buka **Users**, lalu
nonaktifkan atau hapus akun itu, dan periksa akun lain yang kata sandinya masih kata sandi
contoh.

**Data uji sebaiknya dibersihkan.** Bagian 10 `DELIVERY-GATE-18.md` mencatat data uji yang
tertinggal, termasuk dua baris pagu anggaran yang angkanya saya karang dan diberi tanda
`[DATA UJI]`. Kalau alamat ini akan diperlihatkan ke orang lain, angka karangan yang tampil
sebagai pagu sungguhan adalah hal yang paling mudah disalahpahami.

**Cadangan basis data belum ada.** Volume dan basis data di Railway tidak dicadangkan sendiri
pada paket Hobby. Menjalankan `pg_dump` dari komputer Anda seminggu sekali ke berkas yang
disimpan di tempat lain sudah jauh lebih baik daripada tidak ada sama sekali.

---

## Kalau gagal

**Pembangunan berhenti pada versi PHP.** `composer.json` menyebut `php: ^8.3`, sedangkan
proyek ini dikembangkan di PHP 8.5. Kalau `composer install` di Railway menolak sebuah paket
karena versi PHP, tambahkan variabel `RAILPACK_PACKAGES` berisi `php@8.4`, lalu deploy ulang.

**Pembangunan berhenti pada ekstensi PHP yang hilang.** Gejalanya menyebut nama ekstensi,
misalnya `pdo_pgsql` atau `gd`. Tambahkan variabel `RAILPACK_PHP_EXTENSIONS` berisi nama
ekstensi itu, dipisah koma.

**Halaman tampil tanpa gaya, hurufnya berganti.** Berarti aset Filament belum terbit.
Seharusnya terbit sendiri, karena `composer.json` memanggil `filament:upgrade` pada
`post-autoload-dump` dan perintah itu ikut menerbitkan aset. Kalau tidak, tambahkan
`php artisan filament:assets` di baris pertama `railway/init-app.sh`, lalu push dan deploy
ulang.

**Peramban menolak berkas gaya sebagai isi campuran, atau alamat http muncul di halaman
https.** Berarti perubahan `bootstrap/app.php` belum ikut terkirim. Periksa dengan
`git log --oneline -- bootstrap/app.php`.

**Masuk selalu kembali ke halaman masuk.** Sesi tidak tersimpan. Pastikan
`SESSION_DRIVER=database` dan tabel `sessions` ada. Kalau tabelnya tidak ada, migrasinya
belum jalan, dan itu terlihat di log pra-deploy.

**Menu kosong untuk peran selain administrator.** `gais:sync-permissions` belum berjalan.
Buka log pra-deploy dan cari barisnya. Administrator tetap melihat semuanya karena akun itu
`is_super_admin`, jadi cacat ini hanya terlihat kalau Anda mencoba masuk sebagai Staf GA.

**Aplikasi menyala lalu mati berulang kali.** Buka Logs dan baca baris terakhir sebelum
matinya. Penyebab yang paling sering adalah volume dipasang di `/app/storage`, bukan di
`/app/storage/app/public`.

---

## Yang perlu Anda ketahui tentang bentuk penerapan ini

- **Satu instans saja.** Volume di Railway hanya bisa dipasang ke satu instans. Selama berkas
  unggahan disimpan di volume, aplikasinya tidak bisa diperbanyak menjadi beberapa salinan
  untuk membagi beban. Untuk pemakaian internal satu perusahaan, ini bukan batasan yang
  terasa. Kalau kelak perlu, jalannya adalah memindahkan berkas ke penyimpanan objek.
- **Tidak ada pekerja antrean dan tidak ada penjadwal.** Belum diperlukan: aplikasi ini belum
  punya satu pun tugas terjadwal, dan `routes/console.php` masih kosong dari jadwal.
- **Waktu.** `APP_TIMEZONE=Asia/Jakarta` membuat jam yang tampil di layar sama seperti di
  komputer Anda. Peladen Railway sendiri berjalan pada UTC, dan itu tidak masalah selama
  variabel ini terisi.
- **Surel belum dikonfigurasi.** `MAIL_MAILER` tidak ikut disetel, jadi surel apa pun yang
  dikirim aplikasi hanya masuk log. Belum ada fitur yang mengirim surel, jadi ini belum
  menghalangi apa apa. Kalau nanti ada, itu perlu layanan surel tersendiri.
