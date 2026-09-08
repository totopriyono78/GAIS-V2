# Delivery Gate 17: Kiriman P dan Q, kebersihan dan keamanan

Sisa Tahap 4. Diverifikasi 8 September 2026 di `http://127.0.0.1:8000` memakai akun
`test@gais.test`.

**Hasil: LULUS**, dengan enam cacat yang ditemukan dan diperbaiki di ronde ini. Dua di
antaranya membuat perubahan tersimpan sebagai bukan apa apa tanpa satu pun pesan.

**Bagian 6 berisi kesalahan saya sendiri yang perlu Anda baca**, yaitu laporan keliru bahwa
lampiran berkas rusak di seluruh aplikasi. Bagian 10 berisi data uji yang tertinggal, dan
bagian 11 berisi satu perubahan pada data Anda yang mungkin ingin Anda kembalikan.

---

## 1. Design Read

Dua layar dengan dua pembaca yang berbeda, dan itu yang membentuk keduanya.

**Kebersihan** saya baca sebagai **lembar periksa untuk pengawas GA yang sedang berjalan
keliling sambil memegang telepon**. Yang menentukan bentuknya: setiap ketukan tambahan per
area dikalikan jumlah areanya. Dial ENERGY rendah, RHYTHM mengikuti tabel yang sudah ada,
MOTION nol.

**Keamanan** saya baca sebagai **buku jaga dan buku kejadian untuk manajer GA yang membacanya
belakangan**, bukan untuk satpam yang sedang bertugas. Sebagian satpamnya tenaga rekanan yang
tidak punya akun, jadi tidak ada satu layar pun di sini yang mereka buka sendiri.

## 2. Alasan satu baris tiap keputusan (R-31)

- **Warna:** hijau bersih, kuning kurang rapi, merah kotor. Pada insiden, merah hanya untuk
  yang berat, dan kuning untuk yang belum ditindaklanjuti, karena belum ditindaklanjuti adalah
  keadaan yang menuntut sesuatu dari pembacanya.
- **Layout:** kedua layar berkartu tunggal dengan `columnSpanFull()`. Insiden berkartu dua,
  memisahkan apa yang terjadi dari apa yang sudah dilakukan atasnya.
- **Tipografi:** nomor dokumen dan kode area memakai huruf lebar tetap, sama seperti seluruh
  nomor lain.
- **Spacing:** mengikuti panel, tidak ada penyesuaian.
- **Kartu:** hasil pemeriksaan dipilih lewat tombol radio, bukan daftar pilihan. Ketiga
  pilihannya selalu terlihat sekaligus, dan itu menghemat satu ketukan pada setiap area.
- **Ilustrasi:** tidak ada.

## 3. Yang dibangun, dan empat keputusan Anda

| Keputusan Anda | Bentuknya di aplikasi |
|---|---|
| Petugas campuran, karyawan dan tenaga rekanan | Tabel `service_staff` tersendiri, dengan dua bentuk baris yang tidak pernah bercampur |
| Checklist diisi pengawas GA | Putaran pemeriksaan milik pengawas. Tidak ada layar yang dibuka petugas |
| Shift mencatat rencana dan kehadiran | Dua kolom nama yang terpisah, dan tidak pernah saling menimpa |
| Insiden bisa jadi tiket perbaikan | Tombol yang melahirkan permintaan perbaikan, tanpa mesin status kedua |

**Petugas punya tabel sendiri, tidak menumpang di karyawan.** Daftar karyawan dipakai untuk
hal hal yang tidak berlaku bagi tenaga rekanan: departemen yang dibebani biaya, atasan yang
menyetujui, akun yang bisa masuk, dan penomoran NIP. Memasukkan mereka ke sana berarti setiap
layar lain harus belajar mengabaikan mereka satu per satu.

**Jadwal kebersihan tidak dibuat sebagai tabel tersendiri.** Yang membedakan satu area dari
area lain hanya dua hal, seberapa sering dibersihkan dan siapa penanggung jawabnya, dan
keduanya cukup jadi kolom di area itu. Tabel jadwal baru berguna kalau satu area punya petugas
berbeda per hari, dan itu belum menjadi kebutuhan yang disebutkan.

**Daftar area lahir sendiri saat putaran dibuat**, berbeda dari opname yang menyusun daftarnya
lewat tombol. Opname butuh tombol karena penyusunan daftarnya sekaligus membekukan stok, jadi
perlu menjadi keputusan yang disengaja. Pemeriksaan kebersihan tidak membekukan apa pun.

**Hasil pemeriksaan tiga tingkat, bukan dua.** Bersih dan kotor saja memaksa pengawas memilih
antara memaafkan dan menghukum, padahal yang paling sering ia temui ada di tengah: sudah
disapu tetapi tempat sampahnya belum dikosongkan. Tanpa tingkat tengah, temuan seperti itu
akan dicatat sebagai bersih supaya tidak terasa berlebihan, dan lembarnya berhenti menerangkan
keadaan sebenarnya.

**Rencana dan kenyataan pada shift tidak saling menimpa.** Kalau keduanya digabung jadi satu
kolom, rekap akhir bulan kehilangan dua pertanyaan yang paling sering ditanyakan: berapa kali
seseorang tidak masuk, dan berapa kali ia menggantikan orang lain.

**Insiden tidak punya kolom status sendiri.** Membuat mesin status kedua berarti satu insiden
punya dua status yang bisa berbeda pendapat, "selesai" di sini sementara tiketnya masih
terbuka di sana. Keadaannya dibaca dari tiga hal yang sudah ada, dan menutup insiden sengaja
tidak menutup tiketnya.

**Tiket dibuat atas nama orang yang menekan tombol, bukan petugas yang melapor.** Alur tiket
sejak kiriman G menentukan penyetuju dari departemen pemohonnya, dan satpam yang tenaga
rekanan tidak punya departemen sama sekali. Membuat tiket atas namanya berarti membuat tiket
yang tidak pernah bisa disetujui siapa pun.

## 4. Bukti verifikasi, kebersihan (R-35)

### 4.1 Tiga bentuk asal petugas

Ketiganya dibaca dari layar, bukan dari kode:

| Petugas | Yang tertulis di bawah namanya |
|---|---|
| Sumarno | Tenaga luar, tanpa rekanan penyedia |
| Agus Setiawan | Karyawan, Information Technology |
| Wati Suryani | Tenaga dari PT. Abadi Jaya |

Nama karyawan dibaca dari kartu karyawannya, bukan disalin. Telepon Agus berbunyi "Tidak
dicatat", dan itu benar: kartu karyawannya memang tidak punya nomor telepon. Saya membuka
kartunya untuk memastikan, bukan menganggapnya sudah pasti.

### 4.2 Cakupan putaran menyaring dengan benar

Empat area didaftarkan, tiga berjadwal harian dan satu mingguan. Putaran `KB/2026/09/0001`
dibatasi pada jadwal harian, dan daftarnya berisi **tepat tiga area**. Ruang rapat yang
berjadwal mingguan tidak ikut, dan itu yang membuat pemeriksaan harian tidak menyeret area
yang memang hanya dijadwalkan sebulan sekali.

Nama penanggung jawab ikut tersalin ke tiap baris, jadi lembar bulan lalu tetap menyebut orang
yang saat itu memang bertugas di sana.

### 4.3 Satu putaran penuh

| Area | Hasil | Catatan |
|---|---|---|
| Lobi dan meja resepsionis | Bersih | tidak ada |
| Pantry lantai 2 | Kurang rapi | meja sudah dilap, tempat sampah masih penuh |
| Toilet pria lantai 2 | Kotor | lantai tergenang, tempat sampah meluber |

Angka kemajuan dan jumlah temuan di kepala halaman ikut bergerak sendiri saat hasil dicatat di
lembar bawahnya, tanpa memuat ulang. Itu pendengar `#[On('rincian-berubah')]` yang dipasang
sejak awal kiriman ini, bukan setelah ketahuan, karena cacatnya sudah dikenal sejak D-17 dan
D-25.

Setelah diselesaikan, tombol pencatatan hilang sendiri dan lembarnya berhenti bisa diubah.

## 5. Bukti verifikasi, keamanan (R-35)

### 5.1 Petugas yang merangkap ikut terbawa

Sumarno diubah menjadi bertugas di kebersihan dan keamanan. Ia langsung muncul di pilihan
petugas jaga tanpa kehilangan satu pun area kebersihan yang ia pegang. Kolom jumlah area di
daftar petugas berbunyi Sumarno 1 dan Wati 2, cocok dengan pembagian area yang dibuat.

### 5.2 Kehadiran, dan penjaganya

| Yang diperiksa | Hasil |
|---|---|
| Pilihan kehadiran | "Belum dicatat" tidak ikut ditawarkan, karena itu keadaan awal, bukan pilihan |
| Daftar pengganti | Hanya berisi Sumarno. Agus tidak bisa menggantikan dirinya sendiri |
| Setelah disimpan | Baris berbunyi "Digantikan, Digantikan Sumarno" |
| Mengubah menjadi hadir terlambat | Nama pengganti hilang sendiri, meskipun formulir masih membawanya di kolom tersembunyi |

Yang terakhir itu penjaga di model, dan diuji dengan benar benar mencoba melanggarnya:
formulir mengirim `replacement_staff_id` yang masih terisi, dan barisnya tetap tersimpan tanpa
pengganti. Tanpa penjaga itu, rekap akhir bulan akan menghitung satu penggantian yang tidak
pernah terjadi.

### 5.3 Insiden diteruskan menjadi tiket

`LI/2026/09/0001`, kerusakan berat di area parkir, dihubungkan ke shift pagi yang sedang jaga.

| Yang diperiksa | Hasil |
|---|---|
| Judul tiket yang diusulkan | "Kerusakan atau perusakan di Area parkir kendaraan dinas, LI/2026/09/0001" |
| Prioritas yang diusulkan | Mendesak, dari berat kejadian yang bernilai Berat |
| Tiket yang lahir | `PB/2026/09/0003`, pemohon CONTOH Staf GA, lokasi dan uraian ikut tersalin |
| Alur persetujuannya | "Lewat persetujuan. Prioritas mendesak, langsung diteruskan ke tim GA." |
| Sesudahnya | Tombol Create Repair Ticket hilang, diganti Open Repair Ticket |

Baris terakhir itu yang paling melegakan: tiket yang lahir dari insiden masuk ke aturan
persetujuan yang sudah berjalan sejak kiriman G tanpa satu pun pengecualian yang ditulis
khusus untuknya.

**Penjaga akun tanpa kartu karyawan diuji lebih dulu, sebelum akunnya ditautkan.** Kotak
dialognya menjelaskan bahwa tiket tidak bisa dibuat dan kenapa, dan menekan tombolnya tetap
menolak dengan pemberitahuan, bukan gagal diam diam.

### 5.4 Menutup insiden tidak menutup tiketnya

Kotak dialognya menyebutkan itu lebih dulu: "Tiket perbaikan PB/2026/09/0003 tidak ikut
ditutup, dan pekerjaannya tetap berjalan di sana." Sesudah ditutup, tiketnya diperiksa lagi
dan memang masih berstatus Menunggu tim GA.

Catatan penutup wajib diisi, dan itu diuji dengan mencoba menyimpannya kosong: ditolak dengan
"catatan penutup wajib diisi."

### 5.5 Log, kolom, dan layar sempit

- **Log Laravel:** satu galat sepanjang ronde ini, yaitu cacat D-34 di bawah, dan tidak ada
  satu pun galat setelah perbaikannya. Seluruh pengujian sesudah itu, termasuk pembuatan
  tiket dan penutupan insiden, tidak menambah satu baris pun ke log.
- **Aturan enam kolom:** daftar pemeriksaan 6 kolom, lembar pemeriksaan 5 kolom, daftar
  petugas 6 kolom, daftar area 6 kolom, daftar shift 6 kolom, daftar insiden 6 kolom.
  Dihitung dari kepala tabel yang benar benar tergambar, bukan dari kode.
- **Layar sempit 375 piksel:** tidak ada satu pun halaman yang meluber ke samping, dan tidak
  ada teks yang terpotong. Empat elemen yang sempat terbaca sebagai terpotong ternyata elemen
  khusus pembaca layar yang memang sengaja dipangkas.

## 6. Kesalahan saya di ronde ini

Ini bagian yang paling perlu Anda baca, karena saya sempat membuat Anda menjalankan perintah
yang tidak diperlukan.

**Saya melaporkan bahwa lampiran berkas rusak di seluruh aplikasi. Itu keliru.**

Yang saya lihat memang nyata: kolom unggah berkas tergambar sebagai kotak pilih berkas polos,
di kolom Foto pemeriksaan yang baru saya tulis maupun di Add Document kendaraan yang sudah ada
sejak kiriman H. Kesimpulan saya yang salah.

Sebabnya ada pada cara saya memeriksa. Filament membangun komponen unggahnya lewat
`IntersectionObserver`, jadi baru saat kolomnya benar benar terlihat di layar. Panel peramban
yang saya kendalikan sedang tersembunyi, jadi pengamat itu tidak pernah berbunyi. Begitu saya
paksa panelnya menggambar, komponennya langsung terbentuk di keduanya.

Bukti yang paling meyakinkan ada di mesin Anda sendiri, dan seharusnya saya periksa lebih
dulu: `storage/app/public/struk-penggantian` berisi dua PDF yang benar benar terunggah pada
7 September lewat kotak dialog yang bentuknya sama persis.

Yang seharusnya saya lakukan adalah menguji tuduhan saya sebelum menyampaikannya. `php artisan
filament:assets` yang Anda jalankan tidak merusak apa pun, tetapi juga tidak diperlukan.

**Akibatnya pada kiriman ini:** jalur foto pemeriksaan belum saya buktikan sampai berkasnya
benar benar tersimpan. Komponennya terbukti terbentuk dengan pengaturan yang saya tulis, dan
antrean unggahnya ikut terhenti saat panelnya tersembunyi sehingga tidak bisa saya tuntaskan.
Cukup satu foto di satu area untuk memastikannya, dan itu ada di bagian 9.

## 7. Cacat yang ditemukan dan diperbaiki

**D-31. Mengubah asal petugas tersimpan sebagai bukan apa apa.**

Mengubah petugas dari karyawan menjadi tenaga rekanan: nama baru diketik, Simpan ditekan,
tidak ada pesan galat, dan barisnya tetap menyebut nama karyawan yang lama.

Sebabnya berlapis. Formulir menyembunyikan sisi yang tidak dipakai, dan Filament tidak
mengirimkan kolom yang sedang tersembunyi saat menyimpan, jadi `employee_id` lama bertahan di
basis data. Penjaga di model lalu melihat `employee_id` masih terisi dan mengosongkan nama
ketikan yang baru saja diisi. Dua hal yang masing masing benar bertemu menjadi satu perubahan
yang lenyap.

**Perbaikan pertama saya gagal.** Saya menandai kolomnya `->dehydrated()` supaya tetap
terkirim meskipun tersembunyi, dan itu ternyata tidak berlaku pada versi Filament ini.
Ketahuan hanya karena saya mengujinya lagi setelah memperbaiki, bukan karena menganggapnya
sudah beres.

Perbaikan yang benar mengubah dasar penjaganya: bukan melihat kolom mana yang terisi,
melainkan kolom mana yang baru saja berubah. Nama yang baru diketik berarti orangnya tenaga
rekanan; karyawan yang baru ditunjuk berarti namanya milik kartu karyawan. Diuji tiga arah
bolak balik, termasuk memastikan rekanan penyedia ikut terhapus saat berubah menjadi karyawan.

**D-32. Pencarian yang tidak menemukan apa apa berbunyi seperti tabel yang masih kosong.**

Mencari nama yang tidak ada memunculkan "Belum ada petugas terdaftar, daftarkan dulu", padahal
tiga petugas sudah ada. Orang yang membacanya akan menambah petugas keempat yang juga tidak
akan terlihat.

Ternyata `AssetResource` sudah punya pemecahnya sejak kiriman B dan saya tidak memakainya.
Dipindahkan menjadi trait bersama `DetectsTableFilters`, dan `AssetResource` ikut memakainya
supaya aturannya tidak hidup di dua tempat.

**D-33. Pesan kosong yang menyuruh orang membuang pekerjaannya sendiri.**

Ini yang paling berbahaya di antara keenamnya, walau tidak mematikan apa pun.

Penyaring lembar pemeriksaan disimpan per sesi peramban, jadi penyaring yang menyala di satu
putaran masih menyala saat putaran lain dibuka. Lembar yang sebenarnya berisi lalu berbunyi
"Daftar areanya kosong" dan menyuruh pembacanya menekan Rebuild Area List. Menyusun ulang
daftar menghapus seluruh hasil yang sudah dicatat.

Pesan yang salah di tempat itu tidak sekadar membingungkan. Ia menyuruh orang membuang
pekerjaannya sendiri, dengan nada yang meyakinkan. Diperbaiki, dan karena lembar opname ATK
punya penyaring tersimpan yang sama, perbaikannya dipasang di sana juga.

**D-34. Jadwal jaga ganda ditolak tanpa satu pun pesan.**

Menjadwalkan orang yang sama dua kali pada shift dan tanggal yang sama: kotak dialognya tetap
terbuka, tidak ada pesan apa pun, dan tidak ada baris baru. Orang yang menekan Simpan tidak
tahu apakah jadwalnya tersimpan atau tidak.

Indeks unik di basis data memang menolaknya, dan itu benar. Yang salah adalah penolakannya
berupa galat basis data yang ditelan diam diam. Ditambahkan lapis kalimatnya di formulir, dan
sekarang berbunyi menyebut shift dan tanggal yang bentrok. Indeksnya tetap ada sebagai lapis
dalam, karena jadwal sering disusun dua orang sekaligus dan hanya basis data yang sanggup
menengahi dua penyimpanan pada detik yang sama.

**D-35. Pemberitahuan yang membantah barisnya sendiri.**

Setelah mencatat penggantian, pemberitahuannya berbunyi "Agus Setiawan digantikan orang yang
belum dicatat namanya", tepat di sebelah baris tabel yang sudah menyebut nama Sumarno.
Menyimpan kolomnya tidak membuat Eloquent membaca ulang relasinya. Diperbaiki dengan memuat
ulang relasi sebelum kalimatnya disusun, dan sekarang berbunyi "digantikan Sumarno".

**D-36. Singkatan yang rusak karena dikecilkan hurufnya.**

Keadaan insiden berbunyi "Ditangani lewat PB/2026/09/0003, menunggu tim ga". Saya mengecilkan
huruf status tiketnya supaya menyambung mulus di tengah kalimat, dan singkatan GA ikut
mengecil sampai berhenti terbaca sebagai nama bagian. Kalimat yang mengalir tidak sepadan
dengan singkatan yang rusak.

## 8. Delivery Gate checklist

**Blok 1, hal yang membatalkan penyerahan**

- Tombol atau tautan yang tidak melakukan apa apa (R-26): tidak ada sekarang. Ada dua sebelum
  ronde ini, yaitu D-31 dan D-34, dan keduanya berupa tombol Simpan yang tidak menyimpan.
  Keduanya ditemukan karena hasil tiap penyimpanan dibaca ulang dari tabel, bukan karena
  kotak dialognya tertutup dengan mulus.
- Menu menuju halaman yang tidak ada (R-24): tidak ada. Kelima modul sudah didaftarkan di
  `ModuleSeeder`, izinnya disinkronkan, dan ditambahkan ke `RoleSeeder` dengan pembagian yang
  berbeda antara manajer dan staf.
- Angka yang dikarang (R-17, R-18, R-36, R-38): tidak ada. Jam mulai tiap shift sengaja tidak
  ditulis di kode, karena jam jaga adalah kesepakatan perusahaan dan bukan angka yang boleh
  dikarang. Yang tercatat hanya jam masuk dan jam pulang yang benar benar diisi orang.

**Blok 2, hal yang perlu diperbaiki sebelum diserahkan**

- Empty state (R-27): ada, dan sejak D-32 dan D-33 membedakan tabel yang memang kosong dari
  tabel yang sedang tersaring. Kelima tabel baru memakai pembedaan itu.
- Em dash (R-02): tidak ada, diperiksa di seluruh berkas kedua kiriman.
- CTA generik (R-15, R-16): tidak ada. Tombolnya Record Result, Finish Inspection, Rebuild
  Area List, Record Attendance, Create Repair Ticket, Close Incident.
- Kartu sendirian di barisnya: seluruh formulirnya berkartu tunggal dan sudah
  `columnSpanFull()`.

**Blok 3, hal yang dicatat tetapi tidak menghalangi**

- **Tap target di bawah 44 piksel (R-03).** Diukur pada gate 16 dan hasilnya 32 sampai 36
  piksel untuk tombol ikon di dalam tabel. Berlaku di seluruh aplikasi, termasuk kedua kiriman
  ini, dan memperbaikinya berarti menyetel ulang komponen ikon Filament secara global.
- Jam mulai shift belum bisa diatur di mana pun. Kalau tim Anda punya jam baku, itu pantas
  menjadi pengaturan tersendiri, bukan angka di kode.
- Judul halaman buat tiket sekarang berbunyi "New Corrective Maintenance" mengikuti penggantian
  nama menu pada 8 September 2026. Terbaca agak kaku, dan bisa diperhalus kalau Anda mau.

## 9. Yang belum diuji

- **Unggah foto pemeriksaan sampai berkasnya tersimpan.** Uraiannya ada di bagian 6. Ini yang
  paling perlu Anda coba sendiri, cukup satu foto di satu area.
- **Sudut pandang staf yang tidak punya izin menyusun jadwal.** Seluruh pengujian dijalankan
  sebagai administrator. Yang terbukti baru bahwa izinnya terbagi di seeder, bukan bahwa
  tombolnya benar benar hilang bagi staf.
- **Dua orang menyusun jadwal yang sama pada detik yang sama.** Indeks uniknya ada dan sudah
  terbukti menolak, tetapi menirukan dua peramban serentak tidak saya lakukan.
- **Cakupan pemeriksaan per lokasi.** Yang diuji cakupan per jadwal dan per jenis area.
  Cakupan lokasi memakai jalur kode yang sama tetapi tidak saya jalankan sendiri.
- **Petugas yang dinonaktifkan.** Aturannya ditulis, yaitu hanya petugas aktif yang muncul di
  pilihan, tetapi tidak saya buktikan dengan menonaktifkan seseorang.

## 10. Data uji yang tertinggal di basis data Anda

**Data induk:**

- Tiga petugas: Sumarno (kebersihan dan keamanan), Agus Setiawan (keamanan, tertaut ke kartu
  karyawan sungguhan), Wati Suryani (kebersihan, dari PT. Abadi Jaya). Catatan Wati bertanda
  `[DATA UJI]`, dua lainnya tidak, dan itu kelalaian saya.
- Empat area layanan: L1-LOBI, L2-TOILET-PRIA, L2-PANTRY, L3-RAPAT-A.

**Dokumen:**

| Dokumen | Keadaan |
|---|---|
| `KB/2026/09/0001` | Selesai, 3 area, 2 temuan |
| `KB/2026/09/0002` | Dibatalkan, bernama `[DATA UJI]` |
| `KB/2026/09/0003` | Dibatalkan, bernama `[DATA UJI]` |
| Dua shift 8 September 2026 | Pagi digantikan Sumarno, Malam hadir terlambat |
| `LI/2026/09/0001` | Ditutup, dan sudah melahirkan tiket |
| `PB/2026/09/0003` | **Tiket sungguhan yang masih terbuka**, berstatus Menunggu tim GA |

Yang terakhir itu perlu Anda perhatikan: tiket itu hidup di antrean permintaan perbaikan
seperti tiket lain, dan akan terus terbaca sebagai pekerjaan yang menunggu sampai Anda
membatalkannya.

Dua sesi opname `[DATA UJI]` dan perubahan stok dari gate 16 masih seperti yang dilaporkan di
sana.

## 11. Satu perubahan pada data Anda

Akun `tester` saya tautkan ke kartu karyawan **CONTOH Staf GA**, karena tanpa tautan itu jalur
pembuatan tiket dari insiden tidak bisa diuji sama sekali. Layarnya sendiri yang menyuruh
begitu, jadi saya menuruti petunjuknya.

Kalau Anda ingin mengembalikannya, kosongkan kolom Akun sistem pada kartu karyawan CONTOH Staf
GA. Yang perlu diingat, `PB/2026/09/0003` sudah terlanjur tercatat atas nama karyawan itu, dan
nomor dokumen tidak berubah setelah terbit.

## 12. Berkas yang dikirim

Kiriman P: 21 berkas, lalu 5, 2, dan 2 berkas dalam tiga ronde perbaikan.
Kiriman Q: 13 berkas, lalu 1, 1, dan 1 berkas dalam tiga ronde perbaikan.
Seluruhnya lolos `php -l`.

Baru: empat migrasi tabel pada kiriman P dan dua pada kiriman Q, enam model, lima resource
dengan halamannya, satu relation manager, dan satu trait bersama `DetectsTableFilters`.

Diubah: `AdminPanelProvider.php` (kelompok menu Facility Services), `AssetResource.php`
(memakai trait bersama), `SupplyOpnames/RelationManagers/LinesRelationManager.php` (perbaikan
D-33 yang ikut berlaku di sana), serta tiga seeder.

Di luar kedua kiriman, pada hari yang sama: penggantian nama menu Maintenance Schedules
menjadi Preventive Maintenance dan Service Requests menjadi Corrective Maintenance, menyentuh
delapan berkas. Kode modul dan nama tabelnya tidak diubah, jadi seluruh izin tetap utuh.

## 13. Kiriman berikutnya

Tahap 4 sekarang selesai. Yang tersisa di peta dan sudah tercatat di `ROADMAP.md`: laporan
anggaran versus realisasi yang bisa diunduh (Tahap 5), Tahap 6 yang menunggu daftar nomor akun
dari keuangan, serta Tahap 8 sampai 10 yang usulnya ada di `ROADMAP-REFERENSI.md`.

**Pertanyaan kebijakan dari gate 15 masih menunggu jawaban Anda:** anggaran ATK menghitung
pemakaian, bukan pembelian.
