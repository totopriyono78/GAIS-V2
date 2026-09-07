# Delivery Gate: GAIS Tahap 2, kiriman D2

Kelengkapan data aset, dokumen serah terima yang bisa diunduh, dan dasbor bertab.
Dikeluarkan 7 September 2026.

**Status keseluruhan: LULUS.** Seluruhnya dijalankan sendiri di server pengembangan Anda di
`127.0.0.1:8000` memakai akun uji, pada 1366 dan 375 piksel. Tujuh cacat ditemukan saat dicoba,
semuanya sudah diperbaiki dan diuji ulang. Rinciannya di bagian 4.

Kiriman ini bukan modul baru. Isinya permintaan Anda pada 6 September 2026 untuk melengkapi
yang sudah ada, dikerjakan sebelum penyusutan supaya penyusutan nanti berdiri di atas data
aset yang sudah lengkap.

---

## 1. Design Read

> Saya baca ini sebagai layar pendataan dan layar pembuka untuk staf GA yang membuka aplikasi
> ini tiap pagi, gaya arsip kantor yang tenang, dial ENERGY 1 / RHYTHM 1 / MOTION 1.

Dua jenis pekerjaan bertemu di kiriman ini. Formulir aset bertambah panjang karena datanya
memang bertambah, jadi yang dijaga adalah pengelompokan: seksi baru hanya muncul kalau
relevan, dan seksi yang tidak relevan tidak sekadar dikunci melainkan tidak ada. Dasbor
sebaliknya bertambah lebar, jadi yang dijaga adalah pembagian: tab memisahkan pekerjaan orang
yang mengurus aset dari pekerjaan orang yang menjaga stok.

## 2. Alasan satu baris tiap keputusan besar (R-31)

| Keputusan | Alasan |
|---|---|
| Status sewa dibuat radio dua pilihan, bukan tanda centang | Tanda centang yang tidak dicentang tidak bisa dibedakan dari pertanyaan yang belum dijawab. Dua pilihan yang terlihat memaksa orang menentukan sikap |
| Kolom sewa muncul hanya kalau statusnya sewa | Tujuh dari sepuluh aset perusahaan adalah milik sendiri. Menampilkan empat kolom sewa yang selalu kosong pada semua aset itu adalah pekerjaan mata yang tidak berguna |
| Aset sewaan diberi keterangan bahwa ia tidak disusutkan | Ini keputusan akuntansi yang akan menggigit di kiriman penyusutan. Menuliskannya sekarang, di tempat orang memilihnya, lebih murah daripada menjelaskannya nanti |
| `sewa` dibuang dari pilihan sumber perolehan, dipindah jadi status kepemilikan | Sewa bukan cara memperoleh, melainkan bentuk kepemilikan. Yang lama dibiarkan tetap terbaca pada data yang sudah terlanjur memakainya, supaya tidak ada data yang berubah diam diam |
| Kondisi saat diperoleh dipisah dari kondisi fisik sekarang | Barang bekas yang sekarang kondisinya baik dan barang baru yang sekarang kondisinya baik punya umur ekonomis yang berbeda. Satu kolom tidak bisa membawa dua arti |
| Dokumen pendukung dibuat daftar tersendiri, bukan kolom unggah di formulir | Jumlah berkas per aset tidak tetap. Satu aset bisa punya satu berkas, bisa tujuh, dan tiap berkas perlu jenis dan namanya sendiri supaya masih bisa dicari setahun kemudian |
| Nomor sertifikat muncul dari penanda per kategori, bukan dari daftar kategori yang ditulis di kode | Kategori tanah dan bangunan bisa bertambah. Kalau daftarnya ditulis di kode, kategori baru butuh perubahan kode; dengan penanda, cukup satu sakelar di layar Kategori aset |
| Kategori ditulis `1202 - Bangunan permanen` di mana mana | Nomor akun adalah segmen kedua kode aset dan yang dipakai finance. Bentuk yang sama dipakai di pemilih kategori, kolom tabel, legenda diagram, dan berita acara, jadi tidak ada satu tempat pun yang menyebutnya dengan cara lain |
| Detail mutasi dibuat halaman infolist, bukan modal | Isinya dua puluh baris, termasuk tabel perubahan. Modal sebesar itu tidak bisa ditautkan, tidak bisa dicetak, dan tidak bisa dibuka dari dua tab |
| Kolom sebelum di halaman detail diberi keterangan bahwa itu salinan beku | Angka yang tidak ikut berubah padahal asetnya berubah selalu terlihat seperti kesalahan, sampai ada yang menjelaskan bahwa memang begitu maksudnya |
| BAM dan BAST dibuat PDF sungguhan, bukan halaman cetak peramban | Dokumen ini ditandatangani dan diarsip. Hasil cetak peramban membawa header, footer, dan alamat halaman, dan ukurannya berubah ubah menurut pengaturan orangnya |
| Isi BAM dan BAST diambil dari catatan, tabel perubahannya dihitung dari selisih | Berita acara yang isinya diketik ulang bisa berbeda dari datanya. Kalau dihitung, keduanya mustahil bertentangan |
| Dasbor dipecah tab, dan tab tersimpan di alamat halaman | Tab yang tidak tersimpan di alamat akan kembali ke awal tiap kali halaman dimuat ulang, dan tidak bisa dikirim sebagai tautan ke rekan kerja |
| Tab yang seluruh isinya tidak boleh dilihat tidak ditampilkan | Tab kosong adalah bentuk lain dari menu yang menuju halaman yang tidak ada, dan itu R-24 |
| Widget dasbor dimuat bersama halamannya, tidak ditunda | Isinya beberapa kueri agregat kecil. Satu permintaan lebih cepat daripada enam, dan tulisan "Loading..." tidak sempat berkedip. Ini juga cacat nomor 1 di bagian 4 |
| Jatuh tempo garansi dihitung sebagai rentang yang tidak bertumpuk | Kalau 1 bulan ikut terhitung di dalam 2 bulan dan 3 bulan, ketiga angkanya tidak bisa dijumlahkan dan orang akan salah membaca. Satu aset hanya muncul di satu kotak |
| Nilai rupiah besar ditulis pendek di kartu, penuh di keterangannya | Angka penuhnya lebih lebar daripada kartunya di layar 1366 piksel. Yang dipendekkan tampilannya, bukan datanya, dan angka penuhnya tetap ada di layar yang sama |

## 3. Delivery Gate

**Blok 1, Hard Gate.** Semua jawaban tidak.

R-02 tidak ada em dash di seluruh copy baru. R-03 diperiksa pada 1366 dan 375 piksel, hasilnya
di bagian 4; tombol tab tingginya persis 44 piksel. R-17 seluruh angka dasbor dihitung dari
basis data saat halaman dibuka, tidak ada satu pun yang ditulis di kode. R-23 tidak ada aset
visual buatan sendiri; dua diagram lingkaran memakai warna palet yang sudah ada. R-24 tidak ada
menu baru, dan tiga tab yang ada semuanya berisi. R-26 semua tombol baru punya perilaku nyata:
Buka, Unduh BAM, Unduh BAST, Kartu riwayat aset, Unggah dokumen, Unduh, dan tiga tombol tab.
R-27 tabel baru punya empty state khusus, dan tabel aset sekarang punya dua pesan kosong yang
berbeda menurut sebabnya. R-32 penanda fokus tab memakai terracotta yang sama dengan modul lain.
R-36 tidak ada klaim. R-38 kop berita acara diambil dari tabel pengaturan, tidak ada nama
perusahaan yang ditulis di kode maupun di template. **R-35 LULUS**, buktinya di bagian 4.

**Blok 2, Purpose-Gate.** Semua jawaban tidak. Tidak ada gradien, glow, blur, atau animasi
tambahan. Warna membawa arti di tiga tempat baru dan hanya di sana: kolom garansi berubah merah
kalau sudah lewat dan terracotta kalau tinggal tiga bulan, badan keadaan stok tetap seperti
kiriman C, dan dua diagram lingkaran memakai tingkat petrol yang berbeda tanpa menambah rona baru.

**Blok 3, Liveliness.** Semua jawaban ya. Focal point tiap tab adalah baris angka di atas, dan
diagram di bawahnya sengaja tidak diberi judul besar supaya tidak bersaing. Motif identitas
tetap: garis aksen terracotta di judul seksi dan angka bertabular numerals. Tab memakai garis
bawah petrol tiga piksel, bentuk yang sama dengan garis aksen judul.

**Blok 4, Kriya dan Quality Locks.** Semua jawaban tidak. Tidak ada kontrol mati. CTA spesifik:
"Unggah dokumen", "Unduh BAM", "Unduh BAST", "Kartu riwayat aset". Palet tidak bertambah.
Pola halaman sama dengan modul lain.

## 4. Hasil verifikasi R-35

Dijalankan 7 September 2026 di `http://127.0.0.1:8000` dengan akun uji, pada jendela 1366x768
dan diulang pada 375x812.

| Yang diperiksa | Hasil |
|---|---|
| Dasbor bertab | Tiga tab muncul: Ringkasan, Aset, Persediaan. Menekan tab mengganti isinya tanpa memuat ulang halaman |
| Tab tersimpan di alamat | `?tab=aset` dan `?tab=persediaan` membuka tab yang benar langsung dari alamat |
| Ringkasan aset | Aset aktif 154, Nilai perolehan Rp 46,93 miliar dengan angka penuh Rp 46.929.166.000 di bawahnya, Aset sewaan 0, Tanpa penanggung jawab 24 |
| Dua diagram lingkaran | Keduanya tergambar. Legenda kategori berbunyi `1208 - Kendaraan roda dua`, `1207 - Perabot kantor kayu`, sesuai bentuk yang diminta |
| Jatuh tempo garansi dan sewa | Lima kotak, semuanya nol pada data sekarang, dengan keterangan "Tidak ada yang jatuh tempo". Bukan kotak kosong tanpa penjelasan |
| Tabel garansi segera habis | Empty state khusus: "Tidak ada aset yang masa garansinya berakhir dalam tiga bulan ke depan. Masa garansi diisi per aset di menu Daftar aset" |
| Ringkasan persediaan | Jenis barang 26, Perlu dipesan 6 dengan keterangan "Termasuk 2 yang stoknya sudah habis", Perkiraan nilai Rp 18,49 juta, Keluar bulan ini 33 |
| Diagram pemakaian per departemen | Tergambar sebagai batang, dengan keterangan bahwa angkanya jumlah satuan dan belum dikali harga |
| Tabel barang perlu dipesan | Enam baris, cocok dengan angka 6 di kartu di atasnya, urut dari kekurangan terbesar: 20, 12, 6, 3, 2, lalu satu yang pas di batas |
| Formulir aset, seksi Kepemilikan | Radio dua pilihan. Memilih Sewa memunculkan Sewa mulai, Sewa sampai, Nomor kontrak sewa, dan Pemberi sewa |
| Formulir aset, sumber perolehan | Pilihan Dari proyek ada. Memilihnya memunculkan Nama proyek yang wajib diisi |
| Formulir aset, seksi Garansi | Memilih Ya memunculkan Garansi mulai dan Garansi sampai, keduanya wajib |
| Formulir aset, seksi Sertifikat | Muncul pada aset `GA-1202-2012-0001` yang kategorinya Bangunan permanen, dengan Nomor sertifikat tanah dan Nomor sertifikat bangunan. Tidak muncul pada aset lain |
| Pemilih kategori | Menampilkan `1202 - Bangunan permanen`, persis bentuk yang Anda minta |
| Daftar kategori aset | Tiap kategori menampilkan bentuk yang sama sebagai keterangan di bawah namanya |
| Dokumen pendukung | Daftar muncul di bawah formulir aset dengan empty state khusus, tombol Unggah dokumen, dan penyaring jenis dokumen |
| Daftar mutasi aset | Tiap baris sekarang menjadi tautan ke halaman detail, ditambah tombol Buka, Unduh BAM, Unduh BAST, dan Batalkan serah terima |
| Halaman detail mutasi | Empat seksi terbuka penuh: Dokumen, Aset, Perpindahan, Serah terima. Kolom sebelum dan sesudah berdampingan dengan penanda Berubah Ya atau Tidak |
| Unduhan BAM | 200, `application/pdf`, `%PDF-1.7`, 23 kB, nama berkas `BAM-MA-2026-09-0001.pdf` |
| Unduhan BAST | 200, `application/pdf`, `%PDF-1.7`, 24 kB, nama berkas `BAST-MA-2026-09-0001.pdf` |
| Isi PDF | Satu halaman A4, judul dokumen `Berita Acara Serah Terima Aset MA/2026/09/0001`, dua font DejaVu Sans tertanam di dalam berkas |
| Empat penyaring aset baru | Kepemilikan, Garansi segera habis, Garansi sudah lewat, dan Sewa segera habis semuanya berjalan tanpa galat dan mengembalikan nol baris pada data sekarang, yang memang benar |
| Kolom aset baru | Kepemilikan, Garansi sampai, Sewa sampai, dan Dokumen ada di pengatur kolom, tersembunyi secara bawaan, dan tidak ada dua kolom berlabel sama |
| Console | Bersih di dasbor, layar ubah aset, dan halaman detail mutasi. Tidak ada galat sama sekali |
| Lebar 1366 | Tidak ada geseran samping. Tidak ada satu pun kartu yang isinya terpotong |
| Lebar 375 | Halaman tidak bergeser. Tabel menggeser di dalam wadahnya sendiri. Tombol tab tingginya 44 piksel |

### 4.1 Cacat yang ditemukan dan sudah diperbaiki

1. **Seluruh widget dasbor berhenti di "Loading..." dan tidak pernah selesai.** Widget Filament
   dimuat lewat permintaan susulan, satu permintaan per widget, dan lima permintaan itu berangkat
   bersamaan. `php artisan serve` di Windows hanya melayani satu permintaan pada satu waktu, jadi
   empat sisanya kena `ERR_CONNECTION_RESET` dan tidak dicoba lagi. Akibatnya dasbor tidak bisa
   dipakai sama sekali di mesin Anda. Diperbaiki dengan memuat widget bersama halamannya. Ini
   bukan sekadar tambalan lingkungan: sembilan widget itu isinya kueri agregat kecil, dan satu
   permintaan memang lebih cepat daripada enam. Efek sampingnya, seluruh galat
   `ERR_CONNECTION_RESET` yang menghantui tiga kiriman terakhir ikut hilang.

2. **Daftar dokumen pendukung juga berhenti di "Loading...".** Sebab yang sama. Diperbaiki dengan
   cara yang sama.

3. **Nilai perolehan terpotong di ujung kartunya.** `Rp 46.929.166.000` butuh 239 piksel di kartu
   yang lebarnya 224 piksel pada layar 1366. Diperbaiki dengan menulis angka besar dalam bentuk
   pendek, `Rp 46,93 miliar`, dan menaruh angka penuhnya di baris keterangan tepat di bawahnya.
   Bentuk penulisannya dipindah ke satu tempat, `App\Support\Rupiah`, dan dipakai juga oleh kartu
   nilai persediaan yang punya masalah sama dalam bentuk lebih ringan.

4. **Tabel barang perlu dipesan menampilkan lima baris padahal kartunya berkata enam.** Halamannya
   memang lima baris, tetapi orang membaca dua angka bertetangga sebagai satu kenyataan. Ditambah
   lagi urutannya menurut nama, padahal komentar di kodenya sendiri berjanji urut menurut yang
   paling parah. Diperbaiki: kekurangan dihitung di dalam kueri sehingga urutannya benar lintas
   halaman, dan satu halaman memuat sepuluh baris.

5. **Kolom Kurang menulis "0 pcs" untuk barang yang stoknya pas di batas minimum.** Barang itu
   memang masuk daftar, tetapi angka nol di kolom kekurangan membuat orang bertanya kenapa ia ada
   di sana. Diganti menjadi "Pas di batas".

6. **Ada dua kolom berlabel Kategori di pengatur kolom aset.** Saya menambahkan kolom kategori
   berformat nomor akun tanpa membuang yang lama, jadi orang melihat pilihan yang sama dua kali
   tanpa cara membedakannya. Diperbaiki dengan menyatukannya: satu kolom, berformat nomor akun,
   dan tetap bisa diurutkan menurut nama kategori.

7. **Pesan tabel kosong berbohong saat penyaring aktif.** Menyaring aset sewaan yang jumlahnya nol
   menghasilkan pesan "Belum ada aset yang tercatat" dan ajakan mengimpor CSV, padahal asetnya ada
   154. Cacat ini sudah ada sejak kiriman A, tetapi empat penyaring baru membuatnya jauh lebih
   mudah ditemui. Sekarang ada dua pesan: satu untuk tabel yang memang belum ada isinya, satu untuk
   tabel yang sedang dipersempit.

### 4.2 Perbaikan lain yang ikut dikerjakan

- **Paket bahasa Indonesia Filament untuk tabel tidak lengkap.** Tiga hal muncul dalam bahasa
  Inggris di tengah halaman berbahasa Indonesia: kolom ikon ya atau tidak yang terbaca "Yes",
  jumlah hasil yang terbaca "13 results" dan "No results", serta tulisan "Loading...". Ditambal
  di `lang/vendor/filament-tables/id/table.php` dengan hanya kunci yang kurang, jadi kunci lain
  tetap ikut paketnya dan ikut terbarui sendiri kalau nanti dilengkapi. Sekarang terbaca "Ya",
  "13 hasil", dan "Tidak ada hasil". Ini memperbaiki seluruh tabel di aplikasi, bukan hanya yang
  disentuh kiriman ini.

- **Garis tanda tangan di berita acara menyambung jadi satu garis panjang** di bawah dua nama
  sekaligus, karena tabelnya memakai `border-collapse: collapse`. Terbaca seperti satu tanda
  tangan untuk dua orang. Diperbaiki dengan `border-spacing`, jadi tiap kolom membawa garisnya
  sendiri dan ada jarak kosong yang memisahkan. Saya periksa dengan merender ulang template yang
  sama di peramban terpisah, sebelum dan sesudah.

- **Nama berkas asli dokumen pendukung dijamin tersimpan sebagai teks.** Komponen unggah Filament
  mengembalikan nama berkas sebagai teks untuk unggahan tunggal dan sebagai larik untuk unggahan
  jamak, dan bentuknya pernah berubah antar versi. Ditambahkan perata di model supaya kolomnya
  terisi benar tanpa bergantung pada versi.

### 4.3 Yang belum bisa saya buktikan sendiri

- **Unggah berkas dokumen pendukung.** Layarnya tampil lengkap dan daftarnya berjalan, tetapi
  saya tidak mengunggah berkas dari komputer Anda tanpa Anda minta. Silakan coba satu berkas:
  buka satu aset, gulir ke Dokumen pendukung, tekan Unggah dokumen, pilih jenisnya, beri nama,
  dan pilih satu PDF atau foto. Yang perlu Anda perhatikan: setelah tersimpan, kolom Ukuran
  harus terisi angka, bukan "Tidak diketahui", dan tombol Unduh harus membuka berkasnya.

- **Tampilan PDF berita acara di pembaca PDF sungguhan.** Panel peramban di sini tidak mau
  menampilkan PDF, jadi yang saya buktikan adalah berkasnya sah, satu halaman A4, judulnya benar,
  fontnya tertanam, dan susunannya benar saat template yang sama dirender di peramban. Silakan
  buka satu BAM dan satu BAST untuk memastikan hasil akhirnya sesuai kebiasaan kantor Anda.

- **Tab dasbor yang disembunyikan menurut izin.** Aturannya ada dan tab hanya muncul kalau ada
  isinya yang boleh dilihat, tetapi saya mengujinya dengan akun yang boleh melihat semuanya.
  Untuk membuktikannya perlu satu akun yang izin persediaannya dicabut.

### 4.4 Data uji yang tertinggal

Tidak ada. Kiriman ini tidak membuat data baru. Dokumen `MA/2026/09/0001` dari kiriman D masih
ada dan sekarang punya halaman detail serta dua berita acara yang bisa diunduh.

## 5. Perintah yang perlu Anda jalankan

Satu saja, untuk membersihkan singgahan bahasa:

```powershell
php85
php artisan optimize:clear
```

Berkas terjemahan yang baru tidak terbaca sampai singgahannya dibuang. Setelah itu muat ulang
halamannya.

Migrasi tidak ada yang baru. Semua perubahan di kiriman ini ada di kode dan template.
