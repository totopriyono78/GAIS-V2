# Delivery Gate: GAIS Tahap 3, kiriman E dan F

Penyusutan aset, dan pemeliharaan preventif serta korektif.
Dikeluarkan 7 September 2026. Dua kiriman digabung dalam satu gate karena diuji sekaligus.

**Status keseluruhan: LULUS.** Seluruhnya dijalankan sendiri di server pengembangan Anda di
`127.0.0.1:8000` memakai akun uji, pada 1366 dan 375 piksel. Enam cacat ditemukan saat
dicoba, semuanya sudah diperbaiki dan diuji ulang. Rinciannya di bagian 4.

Dengan kiriman ini Tahap 3 selesai.

---

## 1. Design Read

> Saya baca ini sebagai dua layar yang berbeda watak. Penyusutan adalah layar buku besar
> untuk orang finance, gaya laporan yang dingin dan tidak bisa dibantah, dial ENERGY 1 /
> RHYTHM 1 / MOTION 1. Pemeliharaan adalah daftar kerja untuk staf GA yang membukanya tiap
> pagi, gaya papan tugas, dial yang sama.

Perbedaan watak itu menentukan bentuknya. Layar penyusutan diurutkan periode terbaru di
atas dan hampir tidak punya tombol, karena yang dilakukan orang di sana cuma satu hal
sebulan sekali. Layar pemeliharaan diurutkan menurut jatuh tempo terdekat dan tombol
utamanya bukan "tambah" melainkan tindakan pada baris yang sudah waktunya dikerjakan.

## 2. Alasan satu baris tiap keputusan besar (R-31)

### Penyusutan

| Keputusan | Alasan |
|---|---|
| Mesin hitung dipisah jadi kelas yang tidak menyentuh basis data | Angka penyusutan akan dibaca orang pajak dan auditor, jadi rumusnya harus bisa diuji terhadap hitungan tangan tanpa perlu basis data. Itu yang membuat 17 pengujian di `JadwalPenyusutanTest` mungkin |
| Bulan terakhir umur ekonomis menyerap seluruh sisa pembulatan | Membulatkan dua angka di belakang koma tiap bulan meninggalkan recehan yang tidak pernah habis. Bangunan 24,5 miliar dengan umur 240 bulan menyisakan 80 sen selamanya, dan aset yang sudah habis umurnya harus bernilai tepat nol, bukan mendekati |
| Penyerapan itu tidak berlaku pada saldo menurun murni | Saldo menurun murni memang dirancang menyisakan nilai buku di akhir umur. Menghabiskannya di bulan terakhir mengingkari metodenya, jadi penyerapan hanya untuk jadwal yang memang menargetkan nilai sisa |
| Tiga kebijakan diletakkan di layar Pengaturan, bawaannya pajak Indonesia | Kebijakan penyusutan berbeda antara buku fiskal dan buku komersial, dan yang tahu mana yang dipakai adalah perusahaannya, bukan kode |
| Kebijakan ditampilkan sebagai daftar pilihan, bukan kotak teks | Kotak teks mengundang salah eja, dan salah eja pada kebijakan penyusutan berarti angka salah di seluruh laporan tanpa ada yang tahu |
| Periode ditutup berurutan, tidak bisa melompat | Akumulasi tiap bulan bertumpu pada bulan sebelumnya. Menutup Oktober sebelum September membuat angkanya menggantung pada sesuatu yang belum ada |
| Angka dibekukan di tabel entri saat ditutup, tidak dihitung ulang saat dibaca | Memperbaiki umur ekonomis sebuah aset hari ini tidak boleh mengubah laporan bulan lalu. Buku yang sudah ditutup tidak berubah sendiri di belakang punggung orang |
| Entri menyimpan juga metode, umur, nilai perolehan, dan nilai sisa saat itu | Tanpa itu, laporan lama tidak bisa dijelaskan lagi setelah data asetnya diperbaiki |
| Hanya periode terakhir yang bisa dibuka kembali | Membuka periode di tengah membuat akumulasi periode sesudahnya bertumpu pada angka yang sudah tidak ada |
| Akumulasi awal dihitung sendiri dari jadwal kalau dikosongkan | Perusahaan yang mulai memakai GAIS hari ini punya aset dari bertahun lalu. Menghitungnya sendiri berarti tidak ada data yang perlu diketik ulang saat aplikasi mulai dipakai; kolomnya tetap ada untuk aset yang bukunya berkata lain |
| Nilai sisa yang kosong berarti ikut kebijakan, dan nol lama dikosongkan lewat migrasi | Bawaan nol yang tidak pernah diketik siapa pun akan membekukan kebijakan "ikut kategori" tanpa ada yang tahu |
| Laba rugi pelepasan diberi keterangan "sementara" selama bulan pelepasannya belum ditutup | Angka yang masih bisa bergerak tetapi ditampilkan tanpa catatan akan dikutip orang sebagai angka final |

### Pemeliharaan

| Keputusan | Alasan |
|---|---|
| Rekanan dibuat data induk sejak awal, bukan kolom teks bebas | Nama yang diketik bebas bercabang jadi beberapa ejaan dalam hitungan bulan, dan setelah itu pertanyaan "berapa yang sudah kita bayar ke vendor ini" tidak bisa dijawab lagi |
| Riwayat penjadwalan dibuat tabel tersendiri, satu baris per jatuh tempo | Tanpa itu, jatuh tempo yang tidak pernah dibuatkan perintah kerja lenyap tanpa jejak, dan setahun kemudian tidak ada yang bisa membedakan "tidak perlu dikerjakan" dari "terlupakan" |
| Status kunjungan ada tiga, termasuk Dilewati dengan alasan wajib | Ini yang paling sering hilang. Tanpa Dilewati, satu satunya cara melewatkan servis adalah dengan tidak melakukan apa apa, dan itu tidak bisa dibedakan dari kelalaian |
| Selalu tepat satu kunjungan terbuka per jadwal | Jadwal tanpa kunjungan terbuka tidak akan pernah muncul di daftar kerja siapa pun. Kunjungan berikutnya lahir tiap kali satu kunjungan ditutup |
| Kunjungan yang dikerjakan menghitung jatuh tempo berikutnya dari tanggal pengerjaan | Aset harus mendapat satu interval penuh masa layanan meski vendor datang telat |
| Kunjungan yang dilewati menghitung dari tanggal jatuh temponya | Siklusnya tetap menempel di kalender dan tidak ikut mundur karena satu kali tidak dikerjakan |
| Dua jalur mencatat pekerjaan: catat langsung, atau lewat perintah kerja | Vendor rutin yang datang lalu pergi cuma perlu tanggal, siapa, dan hasil. Membuat perintah kerja untuk itu menambah satu layar tanpa menambah informasi. Perintah kerja tetap ada untuk pekerjaan yang perlu penugasan, lampiran, dan uraian panjang |
| Menyelesaikan perintah kerja ikut menutup kunjungannya | Rantainya sengaja lewat kunjungan, bukan langsung ke jadwal, supaya riwayat selalu terisi apa pun jalur penyelesaiannya |
| Penyelesaian perintah kerja dibuat tindakan tersendiri, bukan kolom status | Mengisi hasil dan mengubah status harus terjadi bersamaan. Hasil tanpa status berarti pekerjaan yang tampak selesai tetapi masih terhitung menggantung; status tanpa hasil berarti riwayat yang tidak menjelaskan apa apa |
| Perintah kerja yang sudah selesai tidak bisa dihapus | Biayanya sudah masuk ke total biaya pemeliharaan aset, dan menghapusnya mengubah angka itu tanpa jejak |
| Preventif dan korektif bercampur di riwayat aset | Orang yang membuka kartu aset bertanya apa saja yang pernah dikerjakan pada barang ini, dan jawabannya tidak berubah menurut asal usul pekerjaannya |
| Satu jadwal satu aset, ditambah pembuatan massal dari daftar aset | Riwayat, vendor, dan biaya bisa berbeda per barang. Yang dipangkas hanya pengetikannya: pilih 40 AC, sekali tekan jadi 40 jadwal |
| Biaya pemeliharaan dijawab satu kelas, bukan dijumlah di tiap layar | Biaya tersimpan di dua tabel karena ada dua jalur pencatatan. Menjumlahkan salah satu kurang, menjumlahkan keduanya dobel. Ini cacat nomor 4 di bagian 4 |

## 3. Delivery Gate

**Blok 1, Hard Gate.** Semua jawaban tidak.

R-02 tidak ada em dash di seluruh copy baru. R-03 diperiksa pada 1366 dan 375 piksel;
halaman tidak bergeser, tabel riwayat menggeser di dalam wadahnya sendiri. R-17 seluruh
angka dihitung dari basis data, dan angka penyusutan bahkan dicocokkan dengan hitungan
tangan di bagian 4. R-23 tidak ada aset visual buatan sendiri. R-24 empat menu baru,
semuanya ada halamannya, dan cacat nomor 1 justru soal menu yang tidak muncul. R-26 setiap
tombol baru punya perilaku nyata, dan tombol yang tidak berlaku disembunyikan, bukan
dimatikan diam diam. R-27 seluruh tabel baru punya empty state khusus. R-32 penanda fokus
mengikuti aturan yang sama. R-36 tidak ada klaim, dan justru ada penyangkalan eksplisit
pada laba rugi pelepasan yang bulannya belum ditutup. R-38 tidak ada data karangan yang
dibuat perintah apa pun; data uji yang saya buat sendiri ditandai `[DATA UJI]`.
**R-35 LULUS**, buktinya di bagian 4.

**Blok 2, Purpose-Gate.** Semua jawaban tidak. Tidak ada gradien, glow, blur, atau animasi
tambahan. Warna membawa arti di empat tempat baru dan hanya di sana: keadaan kunjungan
(merah lewat, terracotta segera, hijau selesai, abu dilewati), prioritas perintah kerja,
status perintah kerja, dan beban susulan penyusutan yang diberi terracotta karena angkanya
jauh lebih besar daripada bulan biasa.

**Blok 3, Liveliness.** Semua jawaban ya. Focal point layar penyusutan adalah total beban
periode; focal point layar pemeliharaan adalah kolom jatuh tempo di paling kiri. Motif
identitas tetap: garis aksen terracotta di judul seksi dan angka bertabular numerals.

**Blok 4, Kriya dan Quality Locks.** Semua jawaban tidak. Tidak ada kontrol mati. CTA
spesifik: "Tutup September 2026", "Buka kembali periode", "Catat sudah dikerjakan",
"Lewati", "Buat perintah kerja", "Selesaikan", "Catat kerusakan", "Tambah rekanan".
Palet tidak bertambah. Pola halaman sama dengan modul lain.

## 4. Hasil verifikasi R-35

Dijalankan 7 September 2026 di `http://127.0.0.1:8000` dengan akun uji, pada 1366x768 dan
diulang pada 375x812.

### 4.1 Penyusutan

| Yang diperiksa | Hasil |
|---|---|
| Rumusnya sendiri | 17 pengujian di `tests/Unit/JadwalPenyusutanTest.php`, 59 pernyataan, termasuk saldo menurun ganda yang ditelusuri tahun per tahun dan satu sapuan 240 kombinasi umur, metode, harga, dan nilai sisa. Semua lulus |
| Empat kebijakan di Pengaturan | Muncul dengan label bacanya, bukan kunci mentah. Ketiganya berbentuk daftar pilihan |
| Pratinjau penutupan | "89 aset, total beban Rp 191.070.563", tanpa beban susulan, karena periode mulai bawaannya bulan berjalan |
| Penutupan | Angka yang tercatat persis sama dengan pratinjaunya, jadi keduanya memang jalur kode yang sama |
| **Cocok dengan hitungan tangan, aset 1** | `GA-1202-2012-0001` Gedung Head Office. Nilai perolehan 24.500.000.000, garis lurus, 240 bulan. Beban sebulan 24.500.000.000 / 240 = 102.083.333,33, layar menulis Rp 102.083.333. Dari akumulasinya saya hitung mundur tanggal perolehannya seharusnya September 2012, lalu saya buka asetnya: **2012-09-20**. Akumulasi 169 bulan = Rp 17.252.083.333, nilai buku Rp 7.247.916.667. Cocok sampai rupiah terakhir |
| **Cocok dengan hitungan tangan, aset 2** | `HRD-1205-2024-0001`. Perolehan 516.763.000, 96 bulan. 516.763.000 / 96 = 5.382.947,92, layar menulis Rp 5.382.948. Akumulasi Rp 145.339.594 = 27 bulan, cocok dengan kode tahun 2024 |
| Kartu aset | Seksi Penyusutan menampilkan metode, umur, akumulasi, nilai buku, dan "Dihitung sampai September 2026, beban bulan itu Rp 102.083.333" |
| Buka kembali | Akumulasi aset mundur ke Rp 17.149.999.999, nilai buku naik ke Rp 7.350.000.001, dan keterangannya berubah menjadi "Belum ada periode yang ditutup untuk aset ini". Persis akumulasi awal menurut jadwal |
| Tutup ulang | Menghasilkan angka yang identik, Rp 191.070.563 dan 89 aset. Penutupan bersifat berulang, bukan bergantung urutan percobaan |
| Riwayat penyusutan per aset | Tab tersendiri di kartu aset, berisi periode, beban, akumulasi, dan nilai buku |

### 4.2 Pemeliharaan

| Yang diperiksa | Hasil |
|---|---|
| Rekanan | Tersimpan, muncul di pemilih pelaksana, dan kolom Pekerjaan menghitung perintah kerjanya |
| Jadwal baru | Dibuat untuk aset `GA-1202-2012-0001`, tiap 3 bulan, terakhir dikerjakan 10 Juni 2026 |
| Kunjungan pertama lahir sendiri | Kunjungan ke 1 jatuh tempo **10 Sep 2026**, yaitu 10 Juni ditambah 3 bulan, berstatus Dijadwalkan dengan keterangan "Tinggal 3 hari" |
| Catat sudah dikerjakan | Kunjungan 1 ditutup dengan tanggal 05 Sep 2026, hasil, dan biaya Rp 850.000. Keterangannya berubah menjadi "Lebih cepat 5 hari dari jadwal" |
| Kunjungan berikutnya berjangkar ke tanggal pengerjaan | Kunjungan 2 lahir dengan jatuh tempo **05 Des 2026**, yaitu 05 Sep ditambah 3 bulan, bukan 10 Sep ditambah 3 bulan |
| Lewati dengan alasan | Kunjungan 2 ditandai Dilewati, alasannya tersimpan dan tampil di riwayat |
| Kunjungan sesudah yang dilewati berjangkar ke jatuh tempo | Kunjungan 3 lahir dengan jatuh tempo **05 Mar 2027**, yaitu 05 Des ditambah 3 bulan, bukan dihitung dari hari ini. Siklusnya tetap di kalender |
| Buat perintah kerja dari kunjungan | `WO/2026/09/0001` terbentuk, jenis Preventif, uraiannya disalin dari nama pekerjaan dan rincian jadwalnya, rekanannya ikut terbawa |
| Selesaikan perintah kerja menutup kunjungannya | Kunjungan 3 berubah menjadi Sudah dikerjakan dengan tanggal, rekanan, hasil, dan biaya Rp 1.250.000 yang disalin dari perintah kerjanya |
| Kunjungan berikutnya lahir dari situ | Kunjungan 4 jatuh tempo **07 Des 2026**, yaitu tanggal selesai ditambah 3 bulan |
| Rekap jadwal | "Sudah dikerjakan 2 kunjungan, Dilewati 1 kunjungan, Total biaya tercatat Rp 2.100.000". Cocok dengan 850.000 ditambah 1.250.000 |
| Tab dasbor Pemeliharaan | Empat kartu angka, tabel jatuh tempo, dan grafik biaya enam bulan. Angkanya cocok dengan layar jadwal setelah cacat nomor 4 diperbaiki |
| Lencana menu | Perintah kerja menampilkan jumlah yang belum selesai, dan hilang ketika tidak ada yang menggantung |
| Lebar layar | 1366 tanpa geseran samping. 375 halaman tidak bergeser; tabel riwayat selebar 880 piksel menggeser di dalam wadahnya sendiri |
| Console | Bersih di dasbor, jadwal, perintah kerja, rekanan, periode penyusutan, dan kartu aset |

### 4.3 Cacat yang ditemukan dan sudah diperbaiki

1. **Keempat menu baru tidak muncul sama sekali.** Penyusutan aset, Rekanan, Jadwal
   pemeliharaan, dan Perintah kerja semuanya menjawab Forbidden. Modul dan izinnya terbuat,
   tetapi tidak menempel ke role mana pun, karena yang melekatkan seluruh izin ke role
   administrator adalah `RoleSeeder` dan itu tidak pernah saya sebutkan sebagai langkah
   wajib. Ini celah lama dalam alur menambah modul, bukan cacat kiriman ini, tetapi baru
   ketahuan sekarang. Diperbaiki di perintahnya: `gais:sync-permissions` sekarang sekalian
   menyegarkan role administrator, karena memegang seluruh izin memang definisi role itu.
   Role lain sengaja tidak disentuh.

2. **Layar Catat kerusakan gagal terbuka dengan Internal Server Error.** Pesannya
   "Call to a member function qualifyColumn() on null", dan jejaknya seluruhnya di dalam
   Filament sehingga tidak menunjuk ke kode saya sama sekali. Sebabnya nama parameter
   penutup: saya menulis `fn (Builder $q) => ...` alih alih `$query`. Filament mengisi
   argumen penutup menurut namanya lebih dulu, dan nama yang tidak dikenal membuatnya
   mencari `Builder` di service container, yang menghasilkan builder tanpa model. Empat
   penutup diperbaiki, dan alasannya ditulis sebagai komentar di tempatnya supaya tidak
   terulang.

3. **Membuka kembali periode dari halaman detail meninggalkan orang di halaman 404.**
   Barisnya terhapus, halamannya tetap terbuka, dan klik berikutnya menemukan Not Found.
   Diperbaiki: tombolnya sekarang membawa kembali ke daftar. Sudah diuji ulang.

4. **Dasbor dan layar jadwal menyebut angka biaya yang berbeda untuk bulan yang sama.**
   Dasbor menulis Rp 1.250.000, layar jadwal menulis Rp 2.100.000. Sebabnya dasbor
   menjumlahkan tabel perintah kerja saja, padahal kunjungan preventif bisa ditutup tanpa
   perintah kerja dan biayanya tersimpan di kunjungan. Menjumlahkan salah satu tabel
   menghasilkan angka kurang; menjumlahkan keduanya menghasilkan angka dobel, karena
   perintah kerja yang menutup kunjungan menyalin biayanya ke sana. Aturannya sekarang
   ditulis sekali di `App\Services\BiayaPemeliharaan` dan dipakai dasbor, grafik, dan kartu
   aset. Dua angka berbeda untuk pertanyaan yang sama adalah cara tercepat membuat orang
   berhenti mempercayai seluruh layar.

5. **Rekap jadwal tidak ikut menyegarkan setelah kunjungan ditutup.** Angkanya tetap
   memperlihatkan keadaan sebelum tombol ditekan sampai halaman disegarkan tangan.
   Diperbaiki dengan sinyal Livewire dari daftar kunjungan ke halaman jadwalnya.

6. **Kunjungan yang dilewati tetap menampilkan nama rekanan di kolom Dikerjakan oleh.**
   Terbaca seolah rekanan itu yang mengerjakannya, padahal justru itu bulan mereka tidak
   datang. Sekarang berbunyi "Tidak dikerjakan".

### 4.4 Perbaikan lain yang ikut dikerjakan

- **`kunjunganTerbuka()` mengambil kunjungan yang salah kalau sampai ada dua yang terbuka.**
  Relasinya sudah membawa urutan terbaru di atas, jadi `orderBy` yang ditambahkan di
  belakangnya hanya menjadi urutan kedua. Dipaksa mengurut ulang.

- **Pratinjau penutupan penyusutan menanyakan hal yang sama ke basis data sekali per aset.**
  Pada 154 aset itu 154 kueri dengan jawaban identik. Sekarang diingat sekali per permintaan,
  sama seperti pencarian entri terakhir yang sudah lebih dulu diingat.

### 4.5 Yang belum bisa saya buktikan sendiri

- **Unggah lampiran perintah kerja.** Layarnya tampil dan daftarnya berjalan, tetapi saya
  tidak mengunggah berkas dari komputer Anda tanpa Anda minta. Mekanismenya sama persis
  dengan dokumen pendukung aset.

- **Pembuatan jadwal massal dari daftar aset.** Tindakannya ada di menu tindakan massal dan
  kodenya melewati jalur pembuatan jadwal yang sama dengan yang sudah saya uji satu per satu,
  termasuk pembuatan kunjungan pertamanya. Yang belum saya jalankan adalah menekannya pada
  sekumpulan aset terpilih. Silakan coba: buka Daftar aset, centang beberapa AC, pilih
  Buat jadwal pemeliharaan.

- **Beban susulan penyusutan.** Pada data Anda periode mulainya bulan berjalan, jadi tidak
  ada bulan yang terlewat dan seluruh entri mencakup satu bulan. Jalur susulan baru terpakai
  kalau `penyusutan.periode_mulai` diisi mundur, misalnya `2026-01`, lalu periode pertama
  ditutup. Kolom Cakupan dan keterangan bulan mana saja yang dicakup sudah siap untuk itu.

- **Layar dengan akun yang izinnya dibatasi.** Semua pengujian memakai akun yang memegang
  seluruh izin. Menyembunyikan menu menurut izin sudah terbukti bekerja justru lewat cacat
  nomor 1, tetapi perilaku per aksi seperti menyembunyikan tombol Tutup periode dari orang
  yang tidak punya izin `close` belum saya coba dengan akun terbatas.

### 4.6 Data uji yang tertinggal

Semuanya bertanda `[DATA UJI]` di namanya supaya mudah dikenali dan dibuang:

- Rekanan `VND-AC-01` `[DATA UJI] Sejuk Abadi Teknik`
- Jadwal `[DATA UJI] Servis rutin dan cuci AC` pada aset `GA-1202-2012-0001`, beserta empat
  kunjungannya: dikerjakan, dilewati, dikerjakan, dan satu yang masih dijadwalkan
- Perintah kerja `WO/2026/09/0001`, selesai, biaya Rp 1.250.000
- Periode penyusutan September 2026, ditutup, 89 aset, Rp 191.070.563

Periode penyusutan sengaja saya biarkan tertutup karena itu keadaan yang benar untuk bulan
berjalan. Kalau Anda ingin memulai dari nol, buka kembali periodenya lalu hapus rekanan dan
jadwal bertanda `[DATA UJI]`; kunjungan dan perintah kerjanya ikut terhapus.

## 5. Perintah yang perlu Anda jalankan

Tidak ada migrasi baru. Seluruh perbaikan di bagian 4 ada di kode, jadi cukup:

```powershell
php85
php artisan optimize:clear
```

Setelah ini Tahap 3 selesai. Yang tersisa di peta: Tahap 4 fasilitas dan kendaraan,
Tahap 5 biaya dan anggaran, Tahap 6 integrasi jurnal. Peminjaman aset masih tercatat
sebagai lubang yang diketahui.
