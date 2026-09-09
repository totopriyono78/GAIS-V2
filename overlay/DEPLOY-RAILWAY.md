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

**3. `composer.json`, batasan versi PHP.**

Semula tertulis `"php": "^8.3"`, sekarang `"php": "^8.4.1"`. Angka lama itu warisan kerangka
Laravel dan sudah tidak menggambarkan kenyataan: `composer.lock` proyek ini terkunci pada
Symfony 8.1 yang menuntut PHP 8.4.1. Selama batasannya masih `^8.3`, Railway memasang PHP 8.3
lalu `composer install` berhenti dengan daftar panjang paket yang menolak. Di komputer Anda hal
ini tidak pernah terlihat karena PHP di sana sudah 8.5.

Setelah mengubahnya, sidik jari berkas kunci perlu disegarkan:

```bash
composer update --lock
```

Perintah itu hanya menulis ulang sidik jarinya. Tidak satu pun versi paket berubah, jadi apa
yang berjalan di komputer Anda tetap persis sama.

**4. `composer.json`, ekstensi PHP yang dibutuhkan.**

Ditambahkan `ext-intl`, `ext-pdo_pgsql`, dan `ext-zip` pada `require`.

Railpack memasang ekstensi PHP berdasarkan `require` di `composer.json` **milik proyek ini
saja**, bukan milik seluruh paket yang ikut terpasang. Filament sendiri sudah menyebut
`ext-intl` di berkasnya sendiri, tetapi Railpack tidak membacanya, jadi `composer install`
berhenti dengan keluhan ekstensi yang hilang. Menuliskannya di sini menyelesaikan itu, dan
sekaligus jujur: aplikasi ini memang tidak bisa jalan tanpa ketiganya.

**5. `railpack.json`, menerbitkan aset panel saat citra dibangun.**

```json
{
    "$schema": "https://schema.railpack.com",
    "steps": {
        "build": {
            "commands": ["...", "php artisan filament:assets"]
        }
    }
}
```

Railway menjalankan `composer install` dengan `--no-scripts`, sehingga blok
`post-autoload-dump` di `composer.json` dilewati dan `filament:upgrade` yang biasanya
menerbitkan aset panel ikut tidak berjalan. Tanpa langkah ini panel terbit tanpa satu pun
berkas gaya dan tampil sebagai teks polos.

Tanda `"..."` berarti "kerjakan dulu semua langkah bawaan", termasuk `npm run build`, baru
kerjakan yang di bawahnya. Tanpa tanda itu, daftar ini menggantikan langkah bawaannya, bukan
menambah.

Langkah ini harus terjadi saat citra dibangun, bukan di pra-deploy, karena pra-deploy berjalan
di wadah yang berbeda dan berkas yang ditulisnya tidak pernah sampai ke wadah yang melayani
permintaan.

**6. Berkas panduan ini sendiri.**

### Kirim ketiganya ke GitHub

Di folder `D:\DEVELOPMENT\Sistem GA`:

```bash
composer update --lock
git add bootstrap/app.php railway/init-app.sh railpack.json DEPLOY-RAILWAY.md composer.json composer.lock
git add app resources database
git commit -m "Siapkan penerapan ke Railway"
git push origin main
```

**Tentang `composer update --lock`.** Mengubah `require` di `composer.json` mengubah sidik jari
yang tersimpan di `composer.lock`, dan tanpa menyegarkannya `composer install` di Railway
memulai dengan peringatan bahwa berkas kuncinya tidak sejalan. Perintah itu hanya menulis ulang
sidik jarinya, tidak satu pun versi paket berubah.

Peringatan itu **tidak menggagalkan** pembangunan, dan Railpack membaca ekstensi PHP dari
`composer.json`, bukan dari `composer.lock`. Jadi kalau perintah ini bermasalah di komputer
Anda, silakan tetap push tanpa menjalankannya, dan bereskan belakangan. Penyebab tersering di
Windows: `composer` di terminal yang sedang dipakai berjalan di atas PHP lama yang lain,
misalnya PHP 7.4 bawaan XAMPP, bukan PHP 8.5 yang dipakai proyek ini. Periksa dengan `php -v`
di terminal yang sama, lalu jalankan perintahnya di terminal yang biasa Anda pakai untuk
`php artisan`.

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
3. Buka layanan Postgres itu, tab **Variables**. Nilainya disembunyikan, tekan ikon mata atau
   **Show values** untuk melihatnya. Yang ada di sana `DATABASE_URL`, yaitu alamat dari dalam
   jaringan Railway. Ini yang nanti dipakai aplikasi, dan Anda tidak perlu menyalinnya.

**`DATABASE_PUBLIC_URL` belum ada di daftar itu, dan itu memang benar.** Basis data di Railway
tertutup dari internet secara bawaan, jadi alamat dari luar belum lahir. Cara membukanya ada
di bagian 3, karena hanya di sanalah alamat itu dibutuhkan.

## Bagian 3: isi basis datanya

Ada dua jalan, dan keduanya sah. Pilih satu.

**Jalan A, biarkan Railway membangunnya sendiri.** Lewati seluruh bagian 3 ini. Skrip
pra-deploy di bagian 7 menjalankan `migrate` dan seluruh seeder, jadi tabel, modul, izin,
peran, kategori, penomoran, pengaturan, dan satu akun administrator terbentuk sendiri saat
deploy pertama. Anda tidak perlu menyentuh basis datanya sama sekali, dan tidak perlu membuka
aksesnya ke internet.

Yang tidak ikut hanyalah transaksi: aset, tagihan, surat, paket, perjalanan dinas, dan
anggaran yang sudah ada di komputer Anda. Railway mulai bersih.

Jalan A punya dua keuntungan yang tidak kecil. Akun `test@gais.test` berkata sandi `password`
tidak ikut naik ke internet, dan dua baris pagu anggaran bertanda `[DATA UJI]` yang angkanya
saya karang juga tidak ikut. Keduanya adalah hal yang harus dibereskan sendiri kalau memilih
jalan B.

**Jalan B, salin isi basis data dari komputer.** Demo langsung memperlihatkan sistem yang
sudah terisi, termasuk SPD/2026/09/0002 dengan rombongan lima orang dan angka anggaran yang
sudah berjalan. Harganya: satu setelan jaringan perlu dibuka sementara, dan dua hal di
paragraf sebelumnya perlu dibereskan setelah live.

Sisa bagian 3 ini adalah jalan B.

### 3a. Buka pintu basis datanya sementara

Basis data di Railway tertutup dari internet secara bawaan. Selama masih tertutup,
`RAILWAY_TCP_PROXY_DOMAIN` dan `RAILWAY_TCP_PROXY_PORT` belum ada, dan `psql` dari komputer
Anda tidak bisa menjangkaunya sama sekali.

1. Buka layanan **Postgres**, tab **Settings**, cari bagian **Networking**.
2. Pilih **TCP Proxy**.
3. Isi porta internalnya dengan **5432**, porta yang didengarkan PostgreSQL.
4. Railway membuat alamat proxy dan **menampilkannya langsung di panel itu**, berbentuk seperti
   `shuttle.proxy.rlwy.net:15140`. Alamat itu contoh, bukan alamat Anda. Pakai yang benar benar
   tertulis di panel Anda sendiri.

   **Portanya bukan 5432.** Angka 5432 tadi porta di dalam jaringan Railway. Porta proxy yang
   dipakai dari luar selalu angka lima digit yang dibuatkan Railway, dan berbeda untuk tiap
   layanan. Memakai 5432 dari komputer akan berakhir dengan `Connection timed out`, karena
   porta itu memang tidak dibuka ke internet.
5. Kalau ingin melihatnya sebagai variabel, tab **Variables** sekarang juga memuat
   `RAILWAY_TCP_PROXY_DOMAIN` dan `RAILWAY_TCP_PROXY_PORT`. Nilainya tersembunyi, tekan ikon
   mata atau **Show values**.

`DATABASE_PUBLIC_URL` sendiri sudah ada sejak awal di daftar variabel, tetapi isinya hanya
rumus yang menunjuk ke dua variabel di atas. Selama proxy-nya belum dibuat, rumus itu menunjuk
ke sesuatu yang belum lahir. Uraiannya di 3a2.

### 3a2. Isi `DATABASE_PUBLIC_URL` terbaca sebagai rumus, bukan alamat

Yang tampil di layar biasanya begini:

```
postgresql://${{PGUSER}}:${{PGPASSWORD}}@${{RAILWAY_TCP_PROXY_DOMAIN}}:${{RAILWAY_TCP_PROXY_PORT}}/${{PGDATABASE}}
```

Itu bukan salah dan bukan alamat yang belum jadi. Railway menyimpan variabel sebagai rumus,
dan `${{NAMA}}` berarti "ambil nilai variabel `NAMA` di layanan ini". Isinya baru dirakit
menjadi alamat sungguhan saat dipakai. Yang Anda lihat adalah resepnya, bukan masakannya.

Untuk mendapat alamat sungguhannya, buka nilai kelima variabel itu satu per satu di tab
**Variables** yang sama. Nilainya kira kira begini:

| Variabel | Contoh isinya |
| --- | --- |
| `PGUSER` | `postgres` |
| `PGPASSWORD` | deretan huruf dan angka yang panjang |
| `RAILWAY_TCP_PROXY_DOMAIN` | `centerbeam.proxy.rlwy.net` |
| `RAILWAY_TCP_PROXY_PORT` | `43127` |
| `PGDATABASE` | `railway` |

Alamat rakitannya menjadi seperti ini:

```
postgresql://postgres:KATASANDIPANJANG@centerbeam.proxy.rlwy.net:43127/railway
```

**Sebenarnya alamat rakitan itu tidak perlu dibuat sama sekali.** `psql` menerima keempat
bagiannya sebagai pilihan terpisah, dan itu lebih aman karena kata sandinya diketik ke
pertanyaan, bukan ikut tertulis di perintah dan tersimpan di riwayat terminal. Cara ini juga
kebal terhadap kata sandi yang memuat tanda seperti `@`, `:`, atau `/`, yang di dalam alamat
justru punya arti sendiri dan membuat sambungannya gagal dengan pesan yang membingungkan.
Bentuk perintahnya ada di 3c.

**Alamat itu adalah pintu masuk ke basis data Anda dari mana pun di internet.** Perlakukan
seperti kata sandi, jangan tempelkan ke mana pun selain terminal Anda sendiri, dan tutup lagi
pintunya setelah selesai. Caranya ada di 3d, dan langkah itu jangan dilewati.

**Jalan lain tanpa membuka pintu sama sekali.** Kalau Anda memasang Railway CLI, terowongan
lewat SSH bisa dipakai menggantikan seluruh 3a dan 3d:

```bash
npm i -g @railway/cli
railway login
railway link
railway connect --ssh --tunnel-only
```

Perintah terakhir menyebutkan porta lokal yang ia buka, misalnya 54321. Basis datanya lalu
dijangkau seperti basis data di komputer sendiri, dan perintah pemulihan di 3c ditulis
`-h 127.0.0.1 -p 54321` alih alih memakai alamat publik. Cara ini tidak pernah membuka basis
data Anda ke internet dan tidak menimbulkan biaya lalu lintas keluar, hanya perlu satu alat
tambahan.

### 3b. Buat salinan basis data lokal

Basis data lokal Anda bernama `gais`, di `127.0.0.1:5432` dengan pengguna `postgres`. Kata
sandinya ada di `.env` pada kunci `DB_PASSWORD`.

Di folder proyek, jalankan:

```bash
pg_dump --no-owner --no-privileges --clean --if-exists -h 127.0.0.1 -p 5432 -U postgres -d gais -f gais-lokal.sql
```

`--no-owner` dan `--no-privileges` dipakai karena nama pengguna di Railway berbeda dengan
`postgres` di komputer Anda, dan tanpa kedua bendera itu pemulihannya berhenti pada perintah
kepemilikan yang tidak berlaku di sana. `--clean --if-exists` membuat berkasnya bisa
dijalankan berulang kali tanpa menggandakan apa pun.

### 3c. Kirimkan ke Railway

Ganti `<domain>` dan `<port>` dengan `RAILWAY_TCP_PROXY_DOMAIN` dan
`RAILWAY_TCP_PROXY_PORT` milik layanan Postgres Anda. Kata sandinya ditanyakan setelah
perintahnya dijalankan, dan yang diisikan adalah `PGPASSWORD`.

```bash
psql -h <domain> -p <port> -U postgres -d railway -f gais-lokal.sql
```

Beberapa pesan `NOTICE` tentang objek yang tidak ada adalah hal biasa, itu datang dari
`--if-exists` pada berkas yang dijalankan di basis data yang masih kosong. Yang perlu
diperhatikan hanya baris yang berbunyi `ERROR`.

**Periksa hasilnya:**

```bash
psql -h <domain> -p <port> -U postgres -d railway -c "select count(*) from business_trips;"
```

Jumlahnya harus sama dengan yang ada di komputer Anda. Kalau perintah `pg_dump` atau `psql`
tidak dikenali, keduanya ada di folder `bin` instalasi PostgreSQL Anda, biasanya
`C:\Program Files\PostgreSQL\17\bin`.

### 3d. Tutup lagi pintunya, dan bersihkan

**Matikan Public Networking** di layanan Postgres, kembali ke **Settings** lalu **Networking**
dan hapus akses publiknya. Aplikasi di Railway memakai `DATABASE_URL` yang lewat jaringan
dalam, jadi mematikan akses publik tidak mengganggunya sama sekali. Membiarkannya menyala
berarti basis data berisi seluruh data GA perusahaan tetap bisa dicoba dibuka dari mana pun,
dan lalu lintas keluarnya ikut ditagih.

**Hapus berkas salinannya.** `gais-lokal.sql` berisi seluruh isi basis data Anda termasuk hash
kata sandi setiap akun, dan ia tidak masuk `.gitignore`, jadi jangan sampai ikut ter-commit.

Kalau nanti perlu menyalin data lagi, 3a sampai 3d diulang seperlunya. Membuka dan menutup
akses publik boleh dilakukan berkali kali dan tidak mengubah apa pun di dalam basis datanya.

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

Tidak ada variabel `RAILPACK_` apa pun di daftar ini, dan itu disengaja. Ekstensi PHP dan
langkah menerbitkan aset panel keduanya diatur lewat berkas di repositori, bukan lewat setelan
di dasbor, karena berkas ikut terbaca saat orang lain membuka repositorinya sedangkan setelan
di dasbor hanya diketahui orang yang membukanya. Uraiannya ada di butir 4 dan 5 bagian 0.

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

**`Your lock file does not contain a compatible set of packages`, disusul daftar panjang paket
symfony yang meminta `php >=8.4.1`.** Terjadi pada percobaan pertama 9 September 2026, dan
sudah diperbaiki di repositori. Sebabnya `composer.json` menyebut `php: ^8.3`, sehingga Railway
memasang PHP 8.3, padahal `composer.lock` proyek ini dikunci pada Laravel 13 dan Symfony 8.1
yang menuntut 8.4.1. Perbaikannya mengganti batasan itu menjadi `"php": "^8.4.1"`, lalu
menyegarkan sidik jari berkas kunci dengan `composer update --lock` dan mendorongnya ke GitHub.
`composer update --lock` hanya menulis ulang sidik jarinya, tidak satu pun versi paket berubah.

**`ext-intl` atau `ext-zip` disebut hilang.** Terjadi pada percobaan kedua 9 September 2026, dan
sudah diperbaiki di repositori lewat butir 4 bagian 0. Railpack hanya membaca `require` di
`composer.json` proyek ini, tidak membaca milik paket yang ikut terpasang, jadi `ext-intl` yang
sudah disebut Filament di berkasnya sendiri tetap tidak terpasang. Kalau kelak muncul nama
ekstensi lain, tambahkan barisnya di `composer.json`, jalankan `composer update --lock`, lalu
push.

Menambahkan ekstensi lewat variabel `RAILPACK_PHP_EXTENSIONS` di dasbor juga dimungkinkan,
tetapi pada percobaan yang sama cara itu tidak berpengaruh, jadi jalur `composer.json` yang
dipakai. Jalur itu juga lebih benar, karena aplikasi ini memang tidak bisa jalan tanpa ketiga
ekstensi tersebut, di Railway maupun di mana pun.

**Log memulai dengan `The lock file is not up to date with the latest changes in
composer.json`.** `composer update --lock` belum dijalankan setelah `composer.json` diubah. Itu
baru peringatan, bukan penyebab kegagalan, tetapi tetap perlu dibereskan supaya yang terpasang
di Railway benar benar sama dengan yang berjalan di komputer.

**Halaman tampil tanpa gaya, hurufnya berganti.** Aset Filament belum terbit. Sudah ditutup
oleh `railpack.json` di butir 5 bagian 0. Kalau masih terjadi, buka log pembangunan dan
pastikan baris `php artisan filament:assets` benar benar berjalan dan tidak berhenti dengan
galat. Kalau Anda sempat menambahkan variabel `RAILPACK_BUILD_CMD` di dasbor, hapus variabel
itu: ia menggantikan seluruh langkah bawaan, termasuk `npm run build`.

**Peramban menolak berkas gaya sebagai isi campuran, atau alamat http muncul di halaman
https.** Berarti perubahan `bootstrap/app.php` belum ikut terkirim. Periksa dengan
`git log --oneline -- bootstrap/app.php`.

**`Database connection [psql] not configured`, wadahnya menyala lalu mati berulang kali.**
Salah ketik pada variabel `DB_CONNECTION`. Nama sambungan PostgreSQL di Laravel adalah
**`pgsql`**, bukan `psql`. `psql` adalah nama program baris perintahnya, dan keduanya memang
mudah tertukar. Betulkan variabelnya, lalu deploy ulang.

Kalau setelah dibetulkan muncul galat sambungan yang lain, periksa `DB_URL`. Rujukannya harus
menyebut nama layanan basis data Anda apa adanya. Kalau layanan Postgres-nya Anda beri nama
lain, `${{Postgres.DATABASE_URL}}` perlu ikut diganti mengikuti nama itu.

**`connection to server at "127.0.0.1", port 5432 failed`, dengan `Database: laravel`.** Dua
keterangan itu adalah nilai bawaan Laravel di `config/database.php`, bukan nilai milik basis
data Anda. Artinya `DB_URL` kosong saat aplikasi berjalan, dan Laravel jatuh ke bawaannya.

Penyebabnya hampir selalu rujukan `${{Postgres.DATABASE_URL}}` yang tidak menemukan
tujuannya. Nama di dalam kurung harus sama persis dengan nama layanan basis data di kanvas,
termasuk besar kecil hurufnya. Kalau layanan itu bernama `postgres` huruf kecil, atau pernah
Anda ganti namanya, rujukannya ikut berubah.

Cara memastikan tanpa menebak: buka layanan **Postgres**, tab **Variables**, tekan
**Show values**, salin isi `DATABASE_URL` apa adanya, lalu tempelkan sebagai nilai `DB_URL` di
layanan aplikasi. Kalau setelah itu sambungannya berhasil, yang bermasalah memang rujukannya,
bukan basis datanya.

Alamat itu memakai nama `postgres.railway.internal` yang hanya bisa dijangkau dari dalam
jaringan Railway, jadi menempelkannya seperti ini tidak membuka apa apa ke luar. Yang hilang
hanya kepraktisannya: kalau kata sandi basis datanya diputar, nilai yang ditempel itu perlu
Anda perbarui sendiri. Setelah semuanya jalan, kembalikan ke bentuk rujukan lewat tombol
penambah rujukan di tab Variables, bukan dengan mengetiknya lagi.

**Log menyebut `Running migrations and seeding database ...` berulang ulang di setiap
percobaan menyala.** Itu migrasi bawaan Railway yang berjalan saat wadah dinyalakan, bukan
skrip pra-deploy kita. Keduanya mengerjakan hal yang sama, dan kalau salah satunya gagal
wadahnya ikut mati. Pastikan variabel `RAILPACK_SKIP_MIGRATIONS=1` benar benar ada di layanan
aplikasi, dan Pre-Deploy Command di bagian 7 benar benar terisi, supaya hanya satu tempat yang
mengerjakannya.

**Masuk selalu kembali ke halaman masuk.** Sesi tidak tersimpan. Pastikan
`SESSION_DRIVER=database` dan tabel `sessions` ada. Kalau tabelnya tidak ada, migrasinya
belum jalan, dan itu terlihat di log pra-deploy.

**Menu kosong untuk peran selain administrator.** `gais:sync-permissions` belum berjalan.
Buka log pra-deploy dan cari barisnya. Administrator tetap melihat semuanya karena akun itu
`is_super_admin`, jadi cacat ini hanya terlihat kalau Anda mencoba masuk sebagai Staf GA.

**Deploy berhasil, tetapi membuka alamatnya menghasilkan 502 dalam belasan milidetik.**
Cepatnya jawaban itu keterangannya: proxy Railway langsung ditolak, bukan menunggu aplikasi
yang lambat. Artinya tidak ada yang mendengarkan di porta yang dituju proxy.

Dua tempat yang perlu dicocokkan:

1. **Porta yang benar benar didengarkan aplikasi.** Buka **Deploy Logs**, cari baris dari
   peladen setelah `Starting Container` yang menyebut alamat dengarnya, biasanya berbentuk
   `:8080` atau `0.0.0.0:8080`.
2. **Porta tujuan milik domainnya.** Buka **Settings** lalu **Networking**, lalu setelan porta
   pada domain yang sudah dibuat. Angkanya harus sama dengan yang di log.

Kalau di Deploy Logs tidak ada baris peladen sama sekali, atau ada baris keluar dan wadahnya
menyala ulang terus, yang bermasalah bukan portanya melainkan aplikasinya yang mati saat
dinyalakan. Baca baris terakhir sebelum matinya.

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
