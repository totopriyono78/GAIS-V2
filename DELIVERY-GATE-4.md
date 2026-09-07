# Delivery Gate: GAIS Tahap 2, kiriman C

Barang habis pakai: daftar barang, buku stok, batas pemesanan ulang, peringatan stok menipis.
Dikeluarkan 6 September 2026.

**Status keseluruhan: LULUS.** Modul ini dijalankan sendiri di server pengembangan Anda di
`127.0.0.1:8000` memakai akun uji. Dua cacat ditemukan saat dicoba, keduanya sudah diperbaiki
dan diuji ulang. Rinciannya di bagian 5.

---

## 1. Design Read

> Saya baca ini sebagai layar gudang untuk staf GA yang menyerahkan barang di meja gudang sambil
> membuka layar dengan satu tangan, gaya buku stok kantor yang dicetak rapi, dial ENERGY 1 /
> RHYTHM 1 / MOTION 1.

Dial sama dengan modul lain karena satu aplikasi. Yang membedakan modul ini: pekerjaannya berulang
dan pendek. Menyerahkan satu rim kertas tidak layak menempuh empat halaman, jadi jalur tercepat
dibuat satu tombol di baris barangnya, dan yang perlu diketik tinggal jumlahnya.

## 2. Alasan satu baris tiap keputusan besar (R-31)

| Keputusan | Alasan |
|---|---|
| Barang habis pakai tidak disimpan di tabel aset | Aset dicatat satu baris satu barang karena dilacak sampai umur ekonomisnya. Menggabungkan keduanya membuat 500 pulpen menjadi 500 baris aset |
| Stok tidak disimpan sebagai kolom, melainkan dijumlahkan dari mutasinya | Angka stok jadi mustahil bertentangan dengan riwayatnya, dan tidak ada yang perlu dihitung ulang setelah mutasi diperbaiki. Sama polanya dengan kolom Hasil di stock opname |
| Jumlah disimpan bertanda, layar tetap meminta angka positif | Stok cukup dihitung dengan satu SUM, dan tandanya dipasang model dari jenis mutasi, sehingga tidak ada tempat di aplikasi yang bisa memasukkan tanda yang salah |
| Empat jenis mutasi, bukan tiga plus pilihan arah | Koreksi tambah dan koreksi kurang ditulis terpisah supaya arahnya terbaca langsung di buku stok, tanpa kolom kedua yang harus dibaca dulu |
| Kategori berupa daftar tetap di kode, bukan tabel tersendiri | Kategori hanya dipakai mengelompokkan dan menyaring. Tabel tersendiri berarti satu modul dan satu layar CRUD lagi tanpa manfaat yang sepadan |
| Kolom Keadaan berupa badge, dan kolom Stok dibiarkan polos | Satu sinyal untuk satu hal. Kalau angka stok ikut diberi warna, ada dua penanda yang mengatakan hal yang sama di baris yang sama |
| Habis diperiksa lebih dulu daripada menipis | Stok nol juga selalu berada di bawah batas minimum, jadi urutannya menentukan |
| Ada tombol Catat mutasi di tiap baris daftar barang | Itu pekerjaan harian staf gudang. Barangnya sudah diketahui dari barisnya, jadi yang perlu diketik tinggal jumlahnya |
| Mutasi yang membuat stok minus ditolak, bukan diperingatkan | Stok minus berarti buku stok berbohong, dan tidak ada koreksi yang bisa memperbaiki angka yang sudah dipakai orang mengambil keputusan |
| Penolakan itu dipasang di dua lapis, layar dan model | Layar memberi pesan yang enak dibaca, model menjaga jalur lain seperti perintah artisan dan impor data |
| Barang keluar wajib menyebut departemen | Angka itu yang nanti dipakai membandingkan anggaran ATK tiap departemen dengan pemakaian sebenarnya. Kalau tidak diwajibkan sejak awal, data lama tidak bisa dipakai |
| Harga satuan hanya diminta pada barang masuk | Barang keluar dinilai dari harga perolehannya, bukan dari harga yang diketik ulang saat menyerahkan barang |
| Harga satuan terakhir disalin ke barangnya, disimpan diam diam | Nilai persediaan bisa dihitung tanpa menelusuri mutasi satu per satu, dan harga itu salinan dari mutasi yang sudah punya jejak auditnya sendiri |
| Barang yang sudah punya mutasi tidak bisa dihapus | Menghapus barang ikut menghapus buku stoknya. Barang yang tidak dibeli lagi dimatikan lewat sakelar Barang masih dipakai |
| Kode barang tanpa periode, nomor mutasi kembali ke satu tiap bulan | Kode barang adalah penanda tetap, sedangkan nomor mutasi mengikuti kebiasaan penomoran surat kantor |

## 3. Delivery Gate

**Blok 1, Hard Gate.** Semua jawaban tidak.

R-02 tidak ada em dash. R-03 diperiksa pada 1366 piksel dan pada lebar telepon. Di 1366 lebar
dokumen 1351 piksel, tidak ada geseran samping. Di telepon lebar dokumen sama dengan lebar jendela,
dan tabel enam kolomnya muat 404 piksel di dalam wadah 404 piksel, jadi tidak perlu digeser sama
sekali. R-17 semua angka dihitung dari basis data, dan itu dibuktikan dengan menjumlahkan delapan
baris buku stok satu barang secara manual: hasilnya 15, sama persis dengan angka stok di layar.
R-23 tidak ada aset visual yang dibuat sendiri. R-24 dua menu baru, keduanya ada halamannya, dan
keduanya tersembunyi sebelum izinnya dibuat. Cacat nomor 1 di bagian 5 adalah pelanggaran R-24
yang ditemukan dan sudah diperbaiki. R-26 setiap tombol punya perilaku nyata, dan tombol hapus
disembunyikan pada barang yang sudah punya mutasi. R-27 kedua tabel dan tabel riwayat mutasi
punya empty state khusus. R-32 penanda fokus mengikuti aturan yang sama dengan modul lain, tidak
ada kontrol yang penandanya dihapus. R-36 tidak ada klaim. R-38 nama barang contoh semuanya
bertanda `[DATA DEMO]` dan bisa dihapus lagi. **R-35 LULUS**, buktinya di bagian 5.

**Blok 2, Purpose-Gate.** Semua jawaban tidak. Tidak ada gradien, glow, blur, ilustrasi, atau
animasi tambahan. Ikon dipilih sesuai isinya: kotak arsip untuk daftar barang, dua panah berlawanan
arah untuk mutasi. Badge dipakai untuk kategori dan untuk keadaan stok, keduanya nyata ada nilainya.

**Blok 3, Liveliness.** Semua jawaban ya. Focal point daftar barang adalah kolom Stok dan Keadaan
yang berdampingan, karena itu yang dicari orang saat membuka layar. Halaman satu barang menaruh
angka stoknya di subjudul, bukan menyembunyikannya di dalam tabel. Motif identitas tetap: garis
aksen di judul seksi dan angka bertabular numerals.

**Blok 4, Kriya dan Quality Locks.** Semua jawaban tidak. Tidak ada kontrol mati. CTA spesifik:
"Tambah barang", "Catat mutasi", "Simpan mutasi". Palet tidak bertambah. Pola halaman sama dengan
modul lain, sesuai RHYTHM 1.

## 4. Hasil verifikasi R-35

Dijalankan 6 September 2026 di `http://127.0.0.1:8000` dengan akun uji, pada jendela 1366x768 dan
diulang pada lebar telepon. Data yang dipakai adalah 26 barang contoh dari `php artisan gais:atk-demo`
beserta 155 mutasinya.

| Yang diperiksa | Hasil |
|---|---|
| Menu dan izin | Sebelum `gais:sync-permissions` dijalankan, kedua halaman menolak dengan Forbidden. Setelah izin dibuat, keduanya terbuka. Menu memang lahir dari izin, bukan ditulis manual |
| Penomoran | Kode barang `ATK-0001` sampai `ATK-0026` tanpa periode. Nomor mutasi `MP/2026/09/0156` dan `MP/2026/09/0157` dengan periode bulanan |
| Stok benar benar dijumlahkan | Buku stok satu barang berisi delapan baris: +28, +10, lalu enam pengambilan. Dijumlahkan tangan hasilnya 15, dan angka di layar juga 15 |
| Keadaan stok | Seluruh baris yang tampil dicocokkan satu per satu dengan aturannya. Tidak ada satu pun yang salah, termasuk kasus batas: stok 12 dengan minimum 12 tetap berbunyi Perlu dipesan |
| Barang keluar | Stok turun dari 8 rim menjadi 5 rim |
| Barang masuk | Stok naik dari 5 rim menjadi 15 rim, dan badge Keadaan berubah sendiri dari Perlu dipesan menjadi Aman tanpa memuat ulang halaman |
| Harga satuan terakhir | Terisi 61.500 dari mutasi barang masuk, tanpa diketik di layar barang |
| Penolakan stok minus, lewat tombol di baris | Barang berstok 0 diminta keluar 1. Ditolak, muncul pemberitahuan merah "Stok tidak cukup. Yang tersedia 0 pcs, yang diminta 1 pcs", dan stoknya tidak berubah |
| Penolakan stok minus, lewat halaman mutasi | Barang berstok 0 diminta keluar 5. Ditolak, pesannya muncul persis di bawah kolom Jumlah, dan seluruh isian tetap ada |
| Departemen wajib untuk barang keluar | Disimpan tanpa departemen, tidak tersimpan |
| Seksi yang muncul sesuai jenis | Barang masuk memunculkan harga satuan, pemasok, dan nomor faktur. Barang keluar memunculkan departemen dan penerima. Keduanya tidak pernah muncul bersamaan |
| Penyaring Perlu dipesan | 6 barang, seluruhnya stoknya sama dengan atau di bawah minimumnya |
| Penyaring Stoknya habis | Tepat 2 barang, keduanya berstok nol |
| Jejak audit | Tiap mutasi tercatat beserta nomor dan nama barangnya. Yang dibuat lewat layar tercatat atas nama `tester`, yang dibuat perintah artisan tercatat sebagai `Sistem` |
| Larangan hapus | Barang yang punya mutasi tidak punya tombol Hapus. Barang baru `ATK-0027` yang belum punya mutasi punya tombol itu, dan penghapusannya berhasil |
| Tambah barang | Barang baru dibuat, kodenya terbentuk sendiri, lalu halamannya langsung terbuka dengan subjudul "Stok sekarang 0 pcs, minimum 5 pcs, habis." |
| Lebar layar | 1366 piksel: lebar dokumen 1351, tidak ada geseran samping. Telepon: tabel enam kolom muat di dalam wadahnya, juga tanpa geseran |

### 4.1 Cacat yang ditemukan dan sudah diperbaiki

1. **Tombol Tambah barang menuju halaman yang tidak ada.** Halaman pembuatan barang sudah dibuat
   tetapi lupa didaftarkan di `getPages()`, jadi tombolnya mengarah ke alamat yang menjawab
   Not Found. Ini pelanggaran R-24 dan R-26 sekaligus, persis jenis yang paling sering dilanggar
   menurut `CLAUDE.md`. Diperbaiki dengan mendaftarkan halamannya, dan sudah diuji ulang sampai
   satu barang benar benar terbuat dan terhapus lagi.

2. **Modal Catat mutasi menutup diri saat mutasinya ditolak.** Isian yang sudah diketik hilang, dan
   orang harus membuka lagi lalu mengetik ulang hanya untuk mengubah angkanya. Penyebabnya
   pemeriksaan stok dijalankan di dalam aksi, setelah modal dinyatakan selesai. Diperbaiki dengan
   memindahkan pemeriksaan itu menjadi aturan validasi pada kolom Jumlah. Sudah diuji ulang:
   modalnya tetap terbuka, departemen yang sudah dipilih masih ada, dan pesan "Stok tidak cukup.
   Yang tersedia 0 pcs, yang diminta 7 pcs" muncul persis di bawah kolom Jumlah.

### 4.2 Yang tersisa dan tidak diperbaiki

- **Banyak galat `ERR_CONNECTION_RESET` di console, dan beberapa modul JavaScript gagal dimuat.**
  Ini bukan dari modul ini. Penyebabnya `php artisan serve` melayani satu permintaan pada satu waktu,
  dan di Windows tidak bisa dijadikan banyak pekerja. Begitu halaman meminta beberapa berkas
  sekaligus, sebagian ditolak. Akibatnya halaman sesekali tampil tanpa gaya, dan tombol sesekali
  tidak merespons. **Untuk peragaan ke banyak orang, jangan pakai `php artisan serve`.** Pakai
  Laragon, Herd, atau nginx supaya permintaannya dilayani bersamaan.
- **Satu galat `isOpen is not defined`** dari komponen modal Filament, sama dengan yang tercatat di
  kiriman B. Muncul saat modal ditutup bersamaan dengan penggambaran ulang. Fungsinya berjalan benar.
  Ini balapan antara Livewire dan Alpine di dalam Filament, bukan kode kita.
- **Nama departemen di data demo sebagian berawalan CONTOH**, karena perintah data demo memilih acak
  dari seluruh departemen aktif, termasuk departemen contoh bawaan `gais:coa-demo`. Tidak salah,
  hanya kurang rapi saat diperagakan.
- **Foto layar telepon tidak berhasil saya ambil** sesi ini, karena panel peramban berhenti
  menggambar ulang. Pemeriksaan lebar layar dilakukan dengan pengukuran, bukan dengan mata. Dicatat
  apa adanya, bukan diklaim sudah dilihat.

### 4.3 Data uji yang tertinggal

- Barang `ATK-0002` Kertas HVS F4 punya dua mutasi tambahan dari pengujian saya, `MP/2026/09/0156`
  keluar 3 rim dan `MP/2026/09/0157` masuk 10 rim, sehingga stoknya 15 rim, bukan 8 rim seperti
  hasil data demo yang asli. Harga satuan terakhirnya juga berubah menjadi 61.500.
- Penyaring Stoknya habis masih aktif di akun `tester`, karena penyaring memang disimpan per sesi.
  Tekan Hapus filter di atas tabel untuk mengembalikannya.

Kalau ingin data demo benar benar bersih: `php artisan gais:atk-demo --hapus` lalu
`php artisan gais:atk-demo`.

## 5. Perintah yang perlu Anda jalankan

Perbaikan di bagian 4.1 sudah masuk ke folder Anda dan langsung berlaku, tidak perlu perintah apa pun.
Cukup muat ulang halamannya.

Berikutnya kiriman D: transfer aset dengan serah terima, kartu riwayat aset, dan pelepasan aset.
