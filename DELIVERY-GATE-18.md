# Delivery Gate 18: Kiriman R, S, dan T, surat, paket, dan perjalanan dinas

Tiga modul yang diminta untuk kelengkapan demo, ditambah satu perubahan bentuk pada perjalanan
dinas yang diminta setelah kiriman T selesai. Diverifikasi 8 September 2026 di
`http://127.0.0.1:8000` memakai akun `test@gais.test`.

**Hasil: LULUS**, dengan satu cacat yang ditemukan dan diperbaiki di ronde ini, yaitu D-37:
hasil serah terima surat tersimpan tetapi tidak pernah bisa dibaca lagi setelah
pemberitahuannya hilang.

Bagian 9 berisi data uji yang tertinggal, termasuk dua baris pagu anggaran yang saya buat
sendiri untuk membuktikan angka realisasinya, dan itu perlu Anda putuskan mau disimpan atau
dihapus.

---

## 1. Design Read

Tiga layar, tiga pembaca yang berbeda, dan itu yang membentuk ketiganya.

**Surat** saya baca sebagai **buku agenda yang menggantikan buku besar bergaris di meja
resepsionis**. Yang menentukan bentuknya: pertanyaan yang paling sering diajukan ke buku itu
bukan "surat apa saja yang masuk", melainkan "surat itu sudah sampai ke siapa". Dial ENERGY
rendah, RHYTHM mengikuti tabel, MOTION nol.

**Paket** saya baca sebagai **buku ekspedisi milik tim GA yang juga dipakai bagian keuangan
untuk mencocokkan tagihan kurir**. Karena itu biayanya dipecah per komponen, bukan satu angka
gelondongan: tagihan kurir datang per komponen dan pencocokannya dilakukan per komponen.

**Perjalanan dinas** saya baca sebagai **surat tugas yang berujung pada penyelesaian uang**.
Pembacanya berganti empat kali di sepanjang alurnya: pemohon, atasan, tim GA yang membayar,
lalu pemeriksa yang menutup. Layar yang sama harus menjawab pertanyaan yang berbeda pada tiap
tahap, dan itu yang membuat subjudul halamannya berubah mengikuti status.

## 2. Alasan satu baris tiap keputusan (R-31)

- **Warna:** biru untuk yang menunggu langkah berikutnya, hijau untuk yang selesai, kuning
  untuk selisih uang yang belum diselesaikan, abu abu untuk surat keluar yang memang tidak
  punya alur lanjutan.
- **Layout:** surat dan paket berkartu tunggal dengan `columnSpanFull()`. Perjalanan dinas
  berkartu tiga di halaman Lihat, memisahkan perjalanannya, uangnya, dan jejaknya, karena
  ketiganya dibaca oleh orang yang berbeda.
- **Tipografi:** nomor agenda, nomor kiriman, dan nomor SPD memakai huruf lebar tetap, sama
  seperti seluruh nomor dokumen sejak kiriman B.
- **Spacing:** mengikuti panel, tidak ada penyesuaian.
- **Kartu:** rincian pertanggungjawaban perjalanan diletakkan sebagai tabel di bawah, bukan
  sebagai formulir berulang di dalam kartu, supaya jumlahnya bisa muncul di kaki kolom.
- **Ilustrasi:** tidak ada.

## 3. Yang dibangun

**Kiriman R, surat masuk dan surat keluar.** Satu tabel untuk kedua arah. Nomor agenda dibuat
sistem dengan urutan terpisah untuk masuk (AM) dan keluar (AK), karena buku agenda masuk dan
buku agenda keluar memang dua buku berbeda di hampir setiap kantor. Nomor surat perusahaan
diketik sendiri mengikuti format yang berlaku, sesuai keputusan Anda pada 8 September 2026,
dan disimpan di kolomnya sendiri.

**Kiriman S, pengiriman paket.** Kurirnya memakai tabel `vendors` yang sudah ada, bukan master
baru, supaya tagihan kurir bisa mengalir lewat modul tagihan rekanan yang sudah berjalan sejak
kiriman K. Biayanya dipecah menjadi ongkos kirim, asuransi, pengepakan, potongan, dan pajak.

**Kiriman T, perjalanan dinas.** Lima langkah dengan izin terpisah: mengajukan, menyetujui,
membayarkan uang muka, mempertanggungjawabkan, menutup. Biayanya baru masuk realisasi anggaran
saat ditutup.

**Perubahan bentuk pada kiriman T, rombongan.** Diminta pada 8 September 2026: satu perjalanan
bisa memberangkatkan lebih dari satu orang, dengan satu orang yang bertanggung jawab atas
biaya dan pertanggungjawabannya.

Bentuk yang dipilih: `business_trips.employee_id` tetap ada dan artinya dipertegas menjadi
**penanggung jawab**. Seluruh alur uang menempel padanya, yaitu departemen yang dibebani,
atasan yang menyetujui, penerima uang muka, dan nama yang disebut saat selisihnya diselesaikan.
Peserta lain masuk ke tabel `business_trip_participants` dan tidak membawa kewajiban uang apa
pun. Karena itu tidak ada satu pun angka dari kiriman T yang perlu dihitung ulang.

Tabel pesertanya sengaja tanpa kolom biaya. Biaya tetap dicatat per pengeluaran, bukan per
kepala, karena membagi biaya per orang berarti mengarang pembagian yang tidak tertulis di
struk mana pun.

## 4. Bukti verifikasi, perjalanan dinas berombongan (R-35)

### 4.1 Lima orang berangkat, satu yang bertanggung jawab

SPD/2026/09/0002 dibuat dengan penanggung jawab Andi Prasetyo (General Affair) dan empat
peserta: Budi Santoso (General Affair), Siti Rahmawati (General Affair), Dewi Anggraini
(Finance), Putri Maharani (Information Technology).

Halaman Lihat berbunyi:

> Berangkat 5 orang. Andi Prasetyo sebagai penanggung jawab biaya dan pertanggungjawabannya,
> bersama Budi Santoso, Dewi Anggraini, Putri Maharani, Siti Rahmawati.

Di bawahnya, karena dua peserta berasal dari departemen lain:

> Ada yang berangkat dari departemen lain, tetapi seluruh biayanya tetap dibebankan ke
> General Affair.

Kalimat kedua itu hanya muncul kalau memang ada peserta dari departemen lain. Pada perjalanan
sendirian dan pada rombongan sedepartemen, kalimat itu tidak ada.

Di daftar, baris yang sama berbunyi "General Affair, berangkat 5 orang" di bawah nama
penanggung jawab, sedangkan SPD/2026/09/0001 yang berangkat sendiri hanya berbunyi
"Information Technology" tanpa tambahan apa pun. Jumlah orang tidak ditulis untuk perjalanan
sendirian, karena "berangkat 1 orang" adalah keterangan yang tidak memberi tahu apa apa.

### 4.2 Penanggung jawab tidak bisa dicatat dua kali

Pada kotak dialog Ubah untuk SPD/2026/09/0002, daftar pilihan peserta berisi 12 nama, dan Andi
Prasetyo tidak ada di dalamnya. Ia dikeluarkan dari pilihan karena namanya sudah tercatat
sebagai penanggung jawab.

Lapisan kedua ada di model. Daftar rombongan yang dibaca layar selalu lewat
`semuaYangBerangkat()`, yang menaruh penanggung jawab di depan lalu membuang nama kembar. Jadi
seandainya baris kembar sempat masuk lewat jalur lain, jumlah rombongan maupun daftarnya tetap
benar.

### 4.3 Satu alur penuh, dan aritmetikanya dihitung tangan

Uang muka dibayarkan Rp 10.000.000 dari perkiraan Rp 12.000.000. Empat rincian dicatat:

| Tanggal | Jenis | Nilai |
| --- | --- | --- |
| 14 Sep 2026 | Transportasi, tiket kereta pulang pergi untuk 5 orang | Rp 3.250.000 |
| 14 Sep 2026 | Penginapan, 3 kamar untuk 2 malam | Rp 4.800.000 |
| 15 Sep 2026 | Uang harian 5 orang selama 3 hari | Rp 2.250.000 |
| 16 Sep 2026 | Konsumsi | Rp 875.000 |

Hitungan tangan: 3.250.000 + 4.800.000 + 2.250.000 + 875.000 = **11.175.000**. Kaki kolom
menampilkan Rp 11.175.000. Cocok.

Selisih terhadap uang muka: 10.000.000 − 11.175.000 = **−1.175.000**. Layar berbunyi "Kurang
bayar Rp 1.175.000. Perusahaan masih perlu mengganti Andi Prasetyo sebesar itu." Cocok, dan
kalimatnya menyebut siapa berutang kepada siapa, bukan angka bertanda.

Kalimat itu ikut berubah seketika setiap kali satu baris ditambahkan, tanpa halamannya dimuat
ulang. Ini yang dijaga siaran `rincian-berubah`, cacat yang sama dengan D-17 dan D-25.

### 4.4 Biayanya masuk anggaran hanya setelah ditutup

Sebelum ditutup, realisasi kategori Perjalanan dinas untuk General Affair tahun 2026 masih
Rp 0. Setelah ditutup dengan catatan penutup, halaman Budgets berbunyi:

> Perjalanan dinas, pagu Rp 20.000.000, realisasi **Rp 11.175.000**, sisa Rp 8.825.000,
> 55,9 persen.

Angka realisasinya persis sama dengan jumlah rincian yang dihitung tangan di atas. SPD/0001
milik Agus Setiawan tidak ikut terhitung di sini karena departemennya Information Technology,
dan itu memang benar.

Setelah ditutup, tombol Add Expense, Ubah, dan Hapus di tabel rincian hilang seluruhnya.

## 5. Bukti verifikasi, surat (R-35)

### 5.1 Dua buku agenda yang terpisah

Surat masuk pertama mendapat AM/2026/09/0001, surat keluar pertama mendapat AK/2026/09/0001.
Keduanya bernomor 0001 pada bulan yang sama, dan itu memang yang diinginkan: dua buku, dua
urutan.

Nomor surat perusahaan tersimpan terpisah dari nomor agenda. Pada surat masuk kolom itu berisi
nomor milik pengirim (800/1245/DISNAKER/IX/2026), pada surat keluar berisi nomor kita sendiri
(012/GA-GTI/IX/2026). Keduanya tampil di bawah nomor agenda pada kolom yang sama.

### 5.2 Nomor surat keluar yang kembar ditolak, nomor surat masuk yang kembar tidak

Mencatat surat keluar kedua dengan nomor 012/GA-GTI/IX/2026 ditolak dengan pesan:

> Nomor surat ini sudah dipakai pada agenda AK/2026/09/0001, perihal Permintaan penawaran jasa
> kebersihan tahun 2027.

Pesannya menyebut agenda mana yang bertabrakan, jadi yang mencatat bisa langsung memeriksanya
tanpa mencari.

Formulir yang sama diubah arahnya menjadi surat masuk, dengan nomor yang persis sama, dan kali
ini **diterima**. Itu benar: aturan kekembaran hanya menjaga penomoran kita sendiri. Kantor
lain boleh saja mengirimi kita surat bernomor apa pun, termasuk yang kebetulan mirip.

Surat masuk uji itu sudah saya hapus lagi setelah pengujiannya selesai.

### 5.3 Serah terima mencatat penerima yang sebenarnya

AM/2026/09/0001 ditujukan kepada Lestari Ningsih, tetapi diserahkan kepada Hendra Wijaya. Kotak
dialognya lebih dulu mengingatkan:

> Ditujukan kepada Lestari Ningsih. Catat siapa yang benar benar menerimanya, karena keduanya
> tidak selalu orang yang sama.

Setelah dicatat, barisnya berbunyi:

> Diterima Hendra Wijaya pada 08 Sep 2026, 15:31. Surat ini ditujukan kepada Lestari Ningsih,
> dan diterima orang lain atas namanya.

Kalimat itu sebelumnya hanya muncul sekali di pemberitahuan lalu hilang. Uraiannya di
bagian 7.

## 6. Bukti verifikasi, paket (R-35)

KP/2026/09/0001 dicatat berangkat lewat PT. Abadi Jaya dengan resi AJ0098771265 dan lima
komponen biaya:

| Komponen | Nilai |
| --- | --- |
| Ongkos kirim | Rp 185.000 |
| Asuransi | Rp 12.500 |
| Pengepakan | Rp 25.000 |
| Potongan | −Rp 20.000 |
| Pajak | Rp 22.550 |

Hitungan tangan: (185.000 + 12.500 + 25.000 − 20.000) + 22.550 = 202.500 + 22.550 =
**225.050**. Layar menampilkan Rp 225.050. Cocok.

Perbandingan terhadap perkiraan awal Rp 15.000 berbunyi "Lebih mahal Rp 210.050 daripada
perkiraannya". 225.050 − 15.000 = 210.050. Cocok.

**Rumus totalnya ada dua salinan**, satu di PHP (`ParcelShipment::totalBiaya()`) dan satu di
SQL (`RealisasiBiaya::kiriman()`), masing masing dengan komentar yang menyebut salinan yang
lain. Duplikasi itu diambil sadar sadar, dan ronde ini adalah kesempatan membuktikan keduanya
sama: pagu kategori Pengiriman surat dan paket menampilkan realisasi **Rp 225.050**, angka
yang persis sama dengan yang dihitung PHP. Kalau suatu saat rumusnya berubah, pengujian yang
sama akan langsung memperlihatkan kalau hanya satu salinan yang ikut berubah.

## 7. Cacat yang ditemukan dan diperbaiki

### D-37: siapa yang benar benar menerima surat tersimpan tetapi tidak bisa dibaca lagi

**Yang terjadi.** Tombol Record Handover menyimpan penerima sebenarnya, waktunya, dan
catatannya, lalu menampilkan kalimat serah terimanya di pemberitahuan. Pemberitahuan itu hilang
beberapa detik kemudian. Surat tidak punya halaman Lihat, dan tidak ada satu kolom pun yang
menampilkan penerima sebenarnya. Jadi setelah pemberitahuannya hilang, satu satunya yang
terbaca hanyalah badge "Sudah diserahkan" dan nama orang yang **dituju**, bukan yang menerima.

**Mengapa berbahaya.** Justru kalimat itu yang dicari saat sebuah surat dinyatakan tidak pernah
sampai. Datanya ada di basis data tetapi tidak bisa dijangkau siapa pun lewat layar, yang dari
sudut pandang pemakainya sama saja dengan tidak tercatat.

**Perbaikannya.** Keterangan di bawah kolom Untuk siapa tidak lagi berupa label tetap. Untuk
surat masuk yang sudah diserahkan, ia berisi kalimat serah terimanya lengkap. Surat keluar
tetap berbunyi "Penanda tangan", surat masuk yang belum diserahkan tetap berbunyi "Tujuan
surat".

**Buktinya.** Setelah perbaikan, baris AM/2026/09/0001 berbunyi seperti yang dikutip di bagian
5.3, tanpa perlu menekan apa pun.

**Berkas:** `app/Filament/Resources/Letters/LetterResource.php`.

## 8. Delivery Gate checklist

**Blok 1, hal yang membatalkan penyerahan**

- Tombol atau tautan yang tidak melakukan apa apa (R-26): tidak ada. Seluruh tombol pada
  ketiga modul dicoba sampai hasilnya dibaca ulang dari tabel, bukan sampai kotak dialognya
  tertutup.
- Menu menuju halaman yang tidak ada (R-24): tidak ada. Ketiga modul sudah terdaftar di
  `ModuleSeeder`, izinnya disinkronkan, dan sudah ditambahkan ke `RoleSeeder`.
- Angka yang dikarang (R-17, R-18, R-36, R-38): tidak ada. Ketiga angka yang muncul di layar
  pada ronde ini, yaitu Rp 11.175.000, Rp 225.050, dan Rp 1.175.000, ketiganya dihitung tangan
  lebih dulu di gate ini baru dicocokkan dengan layar.

**Blok 2, hal yang perlu diperbaiki sebelum diserahkan**

- Empty state (R-27): ada pada ketiga tabel, dan ketiganya membedakan tabel yang memang kosong
  dari tabel yang sedang tersaring lewat trait `DetectsTableFilters`.
- Em dash (R-02): tidak ada, diperiksa di seluruh berkas yang disentuh ronde ini.
- CTA generik (R-15, R-16): tidak ada. Tombolnya Record Letter, Record Handover, Open Scan,
  Request Shipment, Record Shipment, Cancel Request, Approve Trip, Pay Advance, Submit
  Settlement, Close Trip, Add Expense.
- Kartu sendirian di barisnya: formulir surat, paket, dan perjalanan dinas seluruhnya berkartu
  tunggal dan sudah `columnSpanFull()`.
- Maksimal 6 kolom bawaan: daftar perjalanan menampilkan 6 kolom, dengan rombongan lengkap,
  biaya sebenarnya, cara berangkat, dan tanggal berangkat disembunyikan di Pilih kolom.

**Blok 3, hal yang dicatat tetapi tidak menghalangi**

- **Tap target di bawah 44 piksel (R-03).** Masih berlaku di seluruh aplikasi, termasuk ketiga
  modul ini. Diukur pada gate 16, hasilnya 32 sampai 36 piksel untuk tombol ikon di dalam
  tabel. Memperbaikinya berarti menyetel ulang komponen ikon Filament secara global.
- Jumlah peserta tidak dibatasi. Sepuluh orang dalam satu SPD akan membuat kalimat rombongannya
  panjang. Kalau itu sering terjadi di perusahaan Anda, kalimatnya pantas dipendekkan menjadi
  beberapa nama pertama ditambah "dan N lainnya".

**Console peramban.** Memuat halaman daftar dan membuka kotak dialog lewat tombolnya tidak
menghasilkan satu pun pesan baru di console. Pesan galat yang ada di penyangga console berasal
dari sesi pengujian sebelum ronde ini dan dari pemanggilan kotak dialog yang saya lakukan lewat
skrip, bukan lewat tombol. `storage/logs/laravel.log` tidak bertambah satu baris pun selama
verifikasi ronde ini, yang berarti tidak ada satu pun galat sisi server.

**Layar sempit.** Halaman Lihat perjalanan dinas dibuka pada lebar 486 piksel.
`scrollWidth` sama dengan `clientWidth`, jadi tidak ada geseran mendatar, dan kalimat rombongan
membungkus dengan benar. Pada 1440 piksel kartunya kembali menjadi dua kolom.

## 9. Yang belum diuji

- **Unggah bukti pengeluaran perjalanan sampai berkasnya tersimpan.** Sama seperti gate 17,
  ini yang paling perlu Anda coba sendiri, cukup satu struk pada satu baris.
- **Unggah pindaian surat.** Jalur kodenya sama dengan bukti pengeluaran.
- **Perjalanan yang benar benar melewati persetujuan.** Kedua perjalanan uji melewati
  persetujuan karena departemennya belum punya kepala. Alur setuju dan tolak memakai jalur kode
  yang sama dengan permintaan perbaikan sejak kiriman G, tetapi pada modul ini belum saya
  jalankan sendiri.
- **Peserta yang dikeluarkan setelah perjalanan disetujui.** Daftar peserta masih bisa diubah
  selama perjalanan berjalan, tetapi mengeluarkan seseorang di tengah jalan belum saya coba.
- **Sudut pandang staf tanpa izin `pay` dan `verify`.** Seluruh pengujian dijalankan sebagai
  administrator.

## 10. Data uji yang tertinggal di basis data Anda

**Surat:**

- AM/2026/09/0001, undangan sosialisasi dari Dinas Tenaga Kerja Kota Yogyakarta, sudah
  diserahkan kepada Hendra Wijaya.
- AK/2026/09/0001, permintaan penawaran jasa kebersihan kepada PT Sinar Abadi Sentosa.

**Perjalanan dinas:**

- SPD/2026/09/0002, Andi Prasetyo bersama empat peserta ke Surabaya, sudah ditutup, dengan
  empat baris pengeluaran berjumlah Rp 11.175.000.

**Paket:**

- KP/2026/09/0001 milik Anda, sekarang berstatus sudah dikirim dengan biaya Rp 225.050 lewat
  PT. Abadi Jaya. Ini data Anda sendiri yang saya lanjutkan alurnya, bukan data yang saya buat.

**Dua baris pagu anggaran yang saya buat, dan ini yang perlu Anda putuskan:**

- General Affair, Perjalanan dinas, 2026, pagu Rp 20.000.000.
- CONTOH Finance, Pengiriman surat dan paket, 2026, pagu Rp 1.000.000.

Keduanya diberi catatan berawalan `[DATA UJI]` dan dibuat semata mata untuk membuktikan angka
realisasinya benar. Angka pagunya saya karang, jadi **jangan dipakai sebagai pagu sungguhan**.
Hapus keduanya lewat halaman Budgets, atau ganti angkanya dengan pagu yang sebenarnya kalau
kategori itu memang mau Anda anggarkan.

## 11. Berkas yang dikirim

**Kiriman R, surat:**

- `database/migrations/2026_09_08_340000_create_letters_table.php`
- `app/Models/Letter.php`
- `app/Filament/Resources/Letters/LetterResource.php` (diperbaiki lagi di ronde ini, D-37)
- `app/Filament/Resources/Letters/Pages/ListLetters.php`

**Kiriman S, paket:**

- `database/migrations/2026_09_08_350000_create_parcel_shipments_table.php`
- `app/Models/ParcelShipment.php`
- `app/Filament/Resources/ParcelShipments/ParcelShipmentResource.php`
- `app/Filament/Resources/ParcelShipments/Pages/ListParcelShipments.php`

**Kiriman T, perjalanan dinas:**

- `database/migrations/2026_09_08_360000_create_business_trips_table.php`
- `database/migrations/2026_09_08_360100_create_business_trip_expenses_table.php`
- `app/Models/BusinessTrip.php`
- `app/Models/BusinessTripExpense.php`
- `app/Filament/Resources/BusinessTrips/BusinessTripResource.php`
- `app/Filament/Resources/BusinessTrips/Pages/CreateBusinessTrip.php`
- `app/Filament/Resources/BusinessTrips/Pages/ListBusinessTrips.php`
- `app/Filament/Resources/BusinessTrips/Pages/ViewBusinessTrip.php`
- `app/Filament/Resources/BusinessTrips/RelationManagers/ExpensesRelationManager.php`

**Perubahan rombongan:**

- `database/migrations/2026_09_08_360200_create_business_trip_participants_table.php` (baru)
- `app/Models/BusinessTrip.php`
- `app/Filament/Resources/BusinessTrips/BusinessTripResource.php`
- `app/Filament/Resources/BusinessTrips/RelationManagers/ExpensesRelationManager.php`

**Bersama ketiganya:**

- `app/Services/RealisasiBiaya.php` (dua sumber baru, `kiriman` dan `perjalanan_dinas`)
- `database/seeders/ModuleSeeder.php`
- `database/seeders/RoleSeeder.php`
- `database/seeders/NumberSequenceSeeder.php`
- `database/seeders/ExpenseCategorySeeder.php`

## 12. Kiriman berikutnya

Tahap 5 masih menyisakan **laporan anggaran dibanding realisasi yang bisa diunduh**. Angka
angkanya sudah semuanya ada dan sudah terbukti benar di gate ini; yang belum ada hanya cara
mengeluarkannya sebagai berkas.

Yang masih menggantung dari gate gate sebelumnya dan belum dijawab:

- Anggaran ATK dihitung dari pemakaian atau dari pembelian. Ditanyakan sejak gate 15.
- Jam mulai tiap shift jaga. Ditanyakan sejak gate 17.
- Peminjaman aset belum ada alurnya sama sekali.
- Tahap 6 masih menunggu nilai `account_code` dari bagian keuangan.
