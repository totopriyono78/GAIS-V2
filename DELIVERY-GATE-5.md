# Delivery Gate: GAIS Tahap 2, kiriman D

Melengkapi siklus aset: serah terima aset, pelepasan aset, dan kartu riwayat aset.
Dikeluarkan 6 September 2026.

**Status keseluruhan: LULUS.** Modul ini dijalankan sendiri di server pengembangan Anda di
`127.0.0.1:8000` memakai akun uji. Satu cacat ditemukan saat dicoba, sudah diperbaiki dan
diuji ulang. Rinciannya di bagian 4.

---

## 1. Design Read

> Saya baca ini sebagai layar dokumen kantor untuk staf GA yang sedang menyerahkan barang di
> depan orangnya, gaya berita acara yang dicetak rapi, dial ENERGY 1 / RHYTHM 1 / MOTION 1.

Yang membedakan kiriman ini dari modul lain: hasilnya bukan data, melainkan dokumen. Karena itu
kartu riwayat dibuat bisa langsung dicetak, dan formulir serah terima menaruh dua nama, yang
menyerahkan dan yang menerima, sebagai seksi tersendiri, bukan sebagai kolom catatan.

## 2. Alasan satu baris tiap keputusan besar (R-31)

| Keputusan | Alasan |
|---|---|
| Perpindahan aset dicatat sebagai dokumen, bukan dengan mengubah kolom lokasi di formulir aset | Kolom lokasi hanya menyimpan keadaan sekarang. Pertanyaan yang sebenarnya ditanyakan orang adalah kenapa berubah dan siapa yang menyerahkannya kepada siapa, dan itu tidak muat di satu kolom |
| Keadaan asal disalin sendiri dari asetnya saat dokumen dibuat | Kalau aset dipindah lagi besok, dokumen hari ini harus tetap menunjukkan dari mana barangnya berangkat. Menyalinnya membekukan angka itu |
| Kolom tujuan yang dikosongkan berarti tidak berubah | Memindahkan ruangan saja tidak boleh memaksa orang mengisi ulang penanggung jawab dan departemen yang memang tidak berubah |
| Dokumen yang tidak memindahkan apa apa ditolak | Menyimpannya hanya menambah satu baris riwayat yang isinya sama dengan keadaan sebelumnya, dan itu membuat kartu riwayat lebih sulit dibaca, bukan lebih lengkap |
| Menyimpan dokumen sekaligus memindahkan asetnya, tanpa tahap persetujuan | Serah terima dicatat setelah barangnya benar benar berpindah tangan, jadi tidak ada apa apa yang masih perlu diputuskan. Ini berbeda dari penyesuaian stock opname, yang memang keputusan penyelia |
| Dokumen serah terima tidak bisa diubah | Tidak ada orang yang menghapus tanda tangan di berita acara yang sudah ditandatangani. Kalau isinya salah, yang benar adalah mencatat serah terima baru yang mengembalikan barangnya |
| Pembatalan hanya untuk dokumen terakhir milik aset itu, dan hanya kalau asetnya masih persis seperti yang dokumen itu tetapkan | Membatalkan dokumen lama akan mengembalikan keadaan yang sudah tidak berlaku, dan itu merusak datanya. Batasan ini yang membuat pembatalan aman untuk salah ketik, tanpa membuka pintu bagi penulisan ulang sejarah |
| Pembatalan diletakkan di model, bukan di tombolnya | Jalur apa pun yang menghapus dokumen tetap mengembalikan asetnya, termasuk penghapusan lewat perintah artisan |
| Status Sudah dilepas dibuang dari pilihan di formulir aset | Sejak ada layar Pelepasan aset, status itu adalah akibat dari satu peristiwa yang ada tanggal, cara, dan dokumennya, bukan label yang bisa dipilih siapa saja. Ini menutup lubang yang saya catat sendiri saat menjawab pertanyaan Anda tentang kelengkapan siklus aset |
| Satu aset hanya bisa punya satu dokumen pelepasan, dijaga di tingkat basis data | Aset yang dilepas dua kali tidak punya arti, dan penjagaan di layar saja bisa ditembus lewat impor atau perintah artisan |
| Status sebelum pelepasan disimpan di dokumennya | Pembatalan pelepasan bisa mengembalikan keadaan yang persis, bukan menebak dengan status Dipakai |
| Laba atau rugi pelepasan tidak dihitung, dan itu dikatakan di layar | Angkanya memerlukan nilai buku, dan nilai buku memerlukan penyusutan yang belum dibangun. Menampilkan selisih hasil pelepasan terhadap nilai perolehan akan terbaca sebagai laba rugi padahal bukan |
| Kartu riwayat membaca dokumen, bukan jejak audit | Jejak audit menyimpan nama kolom dan nilai mentah. Berguna untuk memeriksa siapa mengubah apa, tetapi tidak bisa menjelaskan peristiwanya |
| Kartu riwayat berada di luar panel dan dibuat bisa dicetak | Kartu ini sering harus ikut dilampirkan ke berita acara serah terima atau pelepasan, jadi tampilan polos tanpa menu justru yang dibutuhkan |
| Lokasi awal di kartu riwayat dibaca dari dokumen serah terima paling tua, bukan dari kolom lokasi aset | Kolom lokasi sudah ikut berubah setiap kali aset dipindah. Ini cacat nomor 1 di bagian 4 |

## 3. Delivery Gate

**Blok 1, Hard Gate.** Semua jawaban tidak.

R-02 tidak ada em dash. R-03 diperiksa pada 1366 piksel dan 375 piksel. Di 1366 lebar dokumen
sama dengan lebar jendela, tidak ada geseran samping. Di 375 halaman juga tidak bergeser, dan
tabel serah terima menggeser dirinya sendiri di dalam wadahnya karena lima kolomnya butuh 588
piksel. Kartu riwayat sudah dilihat pada lebar 486 piksel dan susunannya berubah jadi satu kolom
dengan benar. R-17 seluruh angka dan nama di layar dibaca dari basis data, termasuk ringkasan
keadaan aset yang muncul di bawah pilihan aset sebelum tujuan diisi. R-23 tidak ada aset visual
yang dibuat sendiri. R-24 dua menu baru, keduanya ada halamannya. R-26 setiap tombol punya
perilaku nyata, dan tombol batalkan serah terima disembunyikan pada dokumen yang bukan dokumen
terakhir. R-27 kedua tabel punya empty state khusus, dan kartu riwayat punya pesan tersendiri
kalau belum ada peristiwa. R-32 penanda fokus mengikuti aturan yang sama dengan modul lain.
R-36 tidak ada klaim, dan justru ada penyangkalan eksplisit soal laba rugi pelepasan.
R-38 tidak ada data karangan yang dibuat perintah apa pun. **R-35 LULUS**, buktinya di bagian 4.

**Blok 2, Purpose-Gate.** Semua jawaban tidak. Tidak ada gradien, glow, blur, ilustrasi, atau
animasi tambahan. Warna dipakai sebagai penanda jenis peristiwa di kartu riwayat, dan itu satu
satunya tempat warna membawa arti baru: petrol untuk serah terima, hijau untuk opname yang cocok,
terracotta untuk opname yang ada selisihnya, merah untuk pelepasan, abu untuk pencatatan awal.

**Blok 3, Liveliness.** Semua jawaban ya. Focal point kartu riwayat adalah garis waktunya, dan
ringkasan aset di atasnya sengaja dibuat pendek supaya tidak bersaing. Motif identitas tetap:
garis aksen terracotta di judul dan angka bertabular numerals, keduanya ditulis ulang di berkas
gaya kartu riwayat karena halaman itu berada di luar panel.

**Blok 4, Kriya dan Quality Locks.** Semua jawaban tidak. Tidak ada kontrol mati. CTA spesifik:
"Catat serah terima", "Catat pelepasan", "Batalkan serah terima", "Batalkan dan kembalikan",
"Kartu riwayat", "Cetak". Palet tidak bertambah. Pola halaman sama dengan modul lain.

## 4. Hasil verifikasi R-35

Dijalankan 6 September 2026 di `http://127.0.0.1:8000` dengan akun uji, pada jendela 1366x768,
diulang pada 375 piksel, dan kartu riwayatnya dilihat juga pada 486 piksel.

| Yang diperiksa | Hasil |
|---|---|
| Menu dan empty state | Dua menu baru muncul di grup Aset. Tabel kosong menampilkan pesan khusus, bukan tabel kosong tanpa keterangan |
| Ringkasan keadaan aset | Setelah aset dipilih, muncul "Lokasi HO-L3-R01 Ruang IT. Penanggung jawab belum diisi. Departemen Operations. Status Dipakai." Dibaca saat itu juga dari basis data |
| Dokumen tanpa tujuan ditolak | Disimpan dengan ketiga tujuan kosong. Ditolak dengan pesan "Isi paling tidak satu tujuan: lokasi, penanggung jawab, atau departemen" di bawah kolom Lokasi tujuan, dan isian tetap ada |
| Serah terima pertama | Nomor `MA/2026/09/0001` terbentuk. Kolom Yang berubah berbunyi "Lokasi HO-L3-R01 menjadi HO-L3-R03" |
| Aset ikut berpindah | Data aset `OPS-1211-2025-0001` berubah dari HO-L3-R01 menjadi HO-L3-R03 |
| Serah terima kedua pada aset yang sama | Nomor `MA/2026/09/0002`, alasan Ganti penanggung jawab, berbunyi "Penanggung jawab Belum diisi menjadi CONTOH Staf GA". Nilai asal yang kosong ditangani dengan benar |
| Aturan pembatalan | Setelah dokumen kedua ada, dokumen pertama **kehilangan** tombol Batalkan serah terima, dan hanya dokumen kedua yang punya. Persis seperti yang diputuskan di bagian 2 |
| Pembatalan mengembalikan aset | Dokumen kedua dibatalkan. Penanggung jawab aset kembali kosong, dan lokasinya tetap HO-L3-R03 karena itu ditetapkan dokumen pertama yang masih berlaku |
| Pelepasan aset | Nomor `PA/2026/09/0001` terbentuk. Status aset `OPS-1211-2022-0001` berubah menjadi Sudah dilepas |
| Seksi yang muncul sesuai cara pelepasan | Cara Dijual memunculkan Hasil pelepasan sebagai kolom wajib, dan label pihak terkait berubah menjadi Pembeli |
| Status di formulir aset | Pilihan status sekarang hanya Dipakai, Dipinjam, Sedang diperbaiki, Tidak dipakai. Sudah dilepas tidak bisa dipilih tangan lagi |
| Aset yang sudah dilepas | Kolom status terkunci, tetap menampilkan Sudah dilepas, dengan keterangan cara mengembalikannya |
| Pembatalan pelepasan | Status aset kembali ke Dipakai, yaitu status persis sebelum dilepas, bukan tebakan |
| Kartu riwayat | Menampilkan pencatatan awal, serah terima, dan pelepasan dalam satu daftar berurutan, terbaru di atas, lengkap dengan nomor dokumen dan rincian perubahannya |
| Jejak audit | Tiap dokumen tercatat berpasangan dengan perubahan asetnya. Contohnya `MA/2026/09/0001` Dibuat, lalu `OPS-1211-2025-0001` Diubah pada kolom `location_id`, di detik yang sama |
| Lebar layar | 1366 piksel tanpa geseran samping. 375 piksel halaman tidak bergeser, tabelnya menggeser di dalam wadahnya sendiri |

### 4.1 Cacat yang ditemukan dan sudah diperbaiki

1. **Kartu riwayat menyebut lokasi awal yang salah.** Baris Pencatatan membaca kolom lokasi aset
   yang sekarang, padahal kolom itu sudah ikut berubah setiap kali aset dipindah. Akibatnya kartu
   riwayat aset yang sudah pindah berbunyi "Lokasi awal tercatat HO-L3-R03", padahal aset itu
   berangkat dari HO-L3-R01. Ini pelanggaran R-17 dalam bentuk yang paling halus: angkanya memang
   dari basis data, tetapi kolom yang dibaca bukan kolom yang menjawab pertanyaannya. Diperbaiki
   dengan membaca kolom asal pada dokumen serah terima paling tua. Sudah diuji ulang, sekarang
   berbunyi "Lokasi awal tercatat HO-L3-R01".

### 4.2 Yang belum bisa saya buktikan sendiri

- **Aset yang sudah dilepas tidak muncul di pemilih aset serah terima.** Pemilih itu memuat
  pilihannya lewat pencarian, jadi daftarnya tidak ada di halaman sampai orang mengetik, dan saya
  tidak bisa menggerakkannya dari luar dengan andal. Yang bisa saya pastikan: status asetnya memang
  berubah menjadi dilepas, dan penyaringnya satu baris yang sama untuk daftar awal maupun
  pencariannya. Silakan Anda coba: buka Catat serah terima lalu cari aset yang sudah dilepas,
  seharusnya tidak muncul.
- **Unggah berkas pendukung pelepasan.** Kolomnya tampil dan menerima seret berkas, tetapi saya
  tidak mengunggah berkas dari komputer Anda tanpa Anda minta. Mekanismenya sama dengan foto aset
  yang sudah jalan.

### 4.3 Yang tersisa dan tidak diperbaiki

- **Galat `ERR_CONNECTION_RESET` di console masih banyak**, sama seperti kiriman C, dan sebabnya
  sama: `php artisan serve` melayani satu permintaan pada satu waktu. Seluruh permintaan aplikasi
  sendiri menjawab 200, saya periksa satu per satu di daftar permintaan jaringan. Untuk peragaan
  ke banyak orang, pakai Laragon, Herd, atau nginx.
- **Peminjaman aset masih belum ada alurnya.** Status Dipinjam tetap bisa dipilih tangan di
  formulir aset. Dicatat di ROADMAP.md supaya tidak terlupa, dan tidak dikerjakan di kiriman ini
  karena Anda memilih transfer dan pelepasan lebih dulu.

### 4.4 Data uji yang tertinggal

- Dokumen `MA/2026/09/0001` saya biarkan ada. Isinya perpindahan aset `OPS-1211-2025-0001`
  Telepon konferensi dari HO-L3-R01 ke HO-L3-R03, dan aset itu memang sekarang ada di sana.
  Berguna sebagai contoh saat demo, dan kartu riwayatnya jadi ada isinya.
- Dokumen `MA/2026/09/0002` dan `PA/2026/09/0001` sudah saya batalkan, dan asetnya sudah kembali
  ke keadaan semula. Nomor urutnya tidak mundur, jadi dokumen berikutnya melanjutkan dari nomor
  terakhir.

## 5. Perintah yang perlu Anda jalankan

Tidak ada. Perbaikan di bagian 4.1 sudah masuk dan langsung berlaku, cukup muat ulang halamannya.

Setelah ini siklus aset lengkap kecuali penyusutan dan pemeliharaan, keduanya ada di Tahap 3.
