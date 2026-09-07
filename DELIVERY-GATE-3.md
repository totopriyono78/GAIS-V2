# Delivery Gate: GAIS Tahap 2, kiriman B

Stock opname: sesi pemeriksaan fisik, daftar target, pencatatan temuan, penyesuaian data aset.
Dikeluarkan 6 September 2026, diperbarui 6 September 2026 setelah verifikasi di aplikasi berjalan.

**Status keseluruhan: LULUS, dengan tiga langkah yang masih harus Anda coba sendiri.**
R-35 sudah dijawab: kode ini dijalankan di server pengembangan Anda di `127.0.0.1:8000`
memakai akun uji, dan lima cacat yang ditemukan sudah diperbaiki serta diuji ulang.
Rinciannya di bagian 5.

---

## 1. Design Read

> Saya baca ini sebagai layar kerja lapangan untuk staf GA yang sedang berjalan keliling kantor sambil
> memindai barang, gaya dokumen kantor yang dicetak rapi, dial ENERGY 1 / RHYTHM 1 / MOTION 1.

Dial sama dengan modul lain karena ini bagian dari aplikasi yang sama. Yang berbeda adalah kecepatan:
petugas opname mengulang tindakan yang sama ratusan kali, jadi jalur tercepat dibuat satu tombol, dan
jalur yang butuh mengetik baru muncul kalau memang ada bedanya.

## 2. Alasan satu baris tiap keputusan besar (R-31)

| Keputusan | Alasan |
|---|---|
| Cakupan berupa tiga penyaring yang digabung, bukan satu pilihan jenis cakupan | Kebutuhan nyata sering campur, misalnya komputer milik Finance di lantai 3, dan itu tidak bisa diungkapkan dengan satu pilihan tunggal |
| Memilih satu lantai ikut mencakup ruangan di dalamnya | Orang berpikir opname per lantai, bukan per ruangan satu per satu |
| Kode dan nama aset disalin ke baris pemeriksaan | Kalau aset berganti nama di tengah sesi, lembar hasilnya harus tetap menunjukkan apa yang dibawa petugas ke lapangan |
| Hasil pemeriksaan disimpulkan, bukan disimpan sebagai kolom | Kolom hasil yang disimpan bisa bertentangan dengan datanya sendiri setelah temuan diubah. Menyimpulkannya membuat itu mustahil |
| Satu tombol centang untuk kasus "sesuai catatan" | Itu kasus mayoritas. Kalau butuh mengetik, opname seribu aset jadi pekerjaan berhari hari |
| Pencarian dipakai sebagai kotak pindai | Pemindai USB mengetikkan kode lalu menekan Enter. Kotak pencarian tabel sudah berperilaku persis seperti itu, jadi tidak perlu layar pindai tersendiri |
| Daftar target tidak bisa disusun ulang setelah pemeriksaan dimulai | Menyusun ulang di tengah jalan menghapus temuan yang sudah dicatat petugas |
| Penyesuaian dipisah dari penutupan sesi | Menutup sesi adalah pekerjaan petugas, mengubah data induk aset adalah keputusan penyelia. Dua orang berbeda, dua tindakan berbeda |
| Penyesuaian butuh izin `stock_opnames.approve` | Supaya hak menutup sesi dan hak mengubah data aset bisa dipegang orang yang berbeda |
| Aset tidak ditemukan tidak diubah statusnya | Tidak ketemu saat opname belum tentu hilang. Mengubahnya otomatis akan menyembunyikan masalah yang justru harus ditindaklanjuti |
| Penyesuaian hanya bisa dijalankan sekali | Menjalankan dua kali akan menerapkan temuan lama ke data yang sudah berubah |
| Sesi yang sudah diterapkan penyesuaiannya tidak bisa dihapus | Sesi itu bukti kenapa lokasi dan kondisi aset berubah |
| Nomor sesi kembali ke satu tiap bulan | Mengikuti kebiasaan penomoran surat kantor, dan membuat nomor tetap pendek |
| Ada unduhan hasil CSV | Hasil opname biasanya harus dilampirkan ke berita acara, dan itu dikerjakan di luar aplikasi |
| Halaman sesi memakai satu kolom penuh, bukan dua kolom bawaan Filament | Seksi cakupan berisi tiga pilihan berdampingan. Di grid dua kolom, ketiganya hanya kebagian separuh layar dan teksnya patah jadi dua baris |
| Seluruh tingkat warna ditulis sendiri, bukan diturunkan dari satu warna dasar | `Color::hex()` hanya mengambil rona lalu memakai kepekatan bawaannya, dan itu mengubah petrol tua di DESIGN.md menjadi sian menyala di layar |

## 3. Delivery Gate

**Blok 1, Hard Gate.** Semua jawaban tidak.

R-02 tidak ada em dash. R-03 diverifikasi pada 1366 piksel dan 375 piksel. Di 1366 tidak ada
geseran samping sama sekali. Di 375 halaman tidak bergeser ke samping, dan tabel baris pemeriksaan
menggeser dirinya sendiri di dalam wadahnya karena empat kolom bawaannya butuh 505 piksel.
R-17 semua angka di layar sesi opname dihitung dari basis data, dan itu dibuktikan dengan
mencocokkan 33 baris terhadap 33 aset lantai 3 yang dihitung terpisah dari daftar aset.
R-23 tidak ada aset visual yang dibuat sendiri. R-24 satu menu baru, halamannya ada, dan
tersembunyi kalau izin Lihat tidak dicentang. R-26 setiap tombol punya perilaku nyata. Cacat
nomor 3 di bagian 5 adalah pelanggaran R-26 yang ditemukan dan sudah diperbaiki. R-27 tabel sesi
dan tabel baris punya empty state khusus, dan keduanya terlihat di layar. R-32 seluruh kontrol
yang bisa difokus punya penanda fokus yang terlihat, diperiksa satu per satu lewat DOM, tidak
ada satu pun yang penandanya hilang. R-36 tidak ada klaim. R-38 tidak ada data karangan.
**R-35 LULUS**, buktinya di bagian 5.

**Blok 2, Purpose-Gate.** Semua jawaban tidak. Tidak ada gradien, glow, blur, ilustrasi, atau animasi
tambahan. Ikon dipilih sesuai isinya: daftar untuk menyusun target, tombol putar untuk memulai, lingkaran
centang untuk menyelesaikan, panah berputar untuk menerapkan penyesuaian. Badge dipakai untuk status sesi
dan hasil pemeriksaan yang nyata ada nilainya.

**Blok 3, Liveliness.** Semua jawaban ya. Focal point halaman sesi adalah tabel baris pemeriksaan, dan
subjudul halaman menyebut status serta cakupan sesi dalam satu kalimat. Motif identitas tetap: garis
aksen di judul seksi dan angka bertabular numerals. Keduanya terlihat di layar.

**Blok 4, Kriya dan Quality Locks.** Semua jawaban tidak. Tidak ada kontrol mati. CTA spesifik:
"Buat sesi opname", "Susun daftar target", "Mulai pemeriksaan", "Selesaikan opname",
"Terapkan penyesuaian", "Unduh hasil CSV". Palet tidak bertambah. Pola halaman sama dengan modul lain,
sesuai RHYTHM 1.

## 4. Perintah yang perlu Anda jalankan

Perubahan PHP sudah langsung berlaku, tidak perlu apa apa. Satu berkas gaya perlu diterbitkan ulang,
karena yang dilayani ke peramban adalah salinannya di `public/`:

```powershell
php85
php artisan filament:assets
php artisan optimize:clear
```

Setelah itu muat ulang paksa di peramban dengan `Ctrl` + `F5`, supaya salinan lama berkas gaya
tidak dipakai lagi.

## 5. Hasil verifikasi R-35

Dijalankan tanggal 6 September 2026 di `http://127.0.0.1:8000` dengan akun uji yang Anda berikan,
pada jendela 1366x768 dan diulang pada 375x812. Data yang dipakai adalah data demo yang sudah ada,
154 aset.

### 5.1 Langkah yang lulus

| Langkah | Yang diperiksa | Hasil |
|---|---|---|
| 1 | Buat sesi, cakupan lokasi `HO-L3` saja | Nomor terbentuk `SO/2026/09/0001` |
| 2 | Susun daftar target | 33 baris. Dihitung terpisah dari daftar aset: `HO-L3` 3, `R01` 11, `R02` 4, `R03` 6, `R04` 9, jumlahnya 33. Jadi anak lokasi memang ikut terbawa |
| 3 | Sesi kedua tanpa cakupan | 153 baris, sementara seluruh aset ada 154. Sebelumnya satu aset saya beri status Sudah dilepas untuk menguji pengecualiannya, dan aset itulah yang tidak ikut. Statusnya sudah saya kembalikan ke Dipakai |
| 4 | Mulai pemeriksaan | Tombol Susun daftar target dan Mulai pemeriksaan hilang, Selesaikan opname muncul, subjudul berubah jadi "Sedang berjalan" |
| 5 | Tombol centang satu baris | Hasil jadi "Sesuai catatan" |
| 6 | Catat temuan, matikan Barangnya ditemukan | Hasil jadi "Tidak ditemukan" |
| 7 | Catat temuan, ganti lokasi ke `HO-L3-R02` | Hasil jadi "Pindah lokasi" |
| 9 | Aksi massal Tandai sesuai catatan pada lima baris | Kelimanya jadi "Sesuai catatan" |
| 10 | Selesaikan opname | Kotak konfirmasi menyebut "Masih ada 25 baris yang belum diperiksa". Cocok, 33 dikurangi 8 yang sudah diperiksa |
| 11 | Terapkan penyesuaian | Aset dari langkah 7 pindah ke Ruang server `HO-L3-R02`. Aset dari langkah 6 tetap di `HO-L3-R01` dan statusnya tetap Dipakai, tidak diubah sistem |
| 12 | Jejak audit | Tercatat `FIN-1209-2020-0001` dengan kolom yang berubah `location_id`. Perubahan status sesi juga tercatat |
| 13 | Terapkan penyesuaian lagi | Tombolnya sudah hilang, dan subjudul menyebut kapan penyesuaian diterapkan |

### 5.2 Langkah yang belum bisa saya jalankan

| Langkah | Kenapa |
|---|---|
| 8, pindai barcode ke kotak pencarian | Butuh pemindai USB fisik. Yang bisa saya pastikan: kotak pencarian tabel mencari pada kolom kode aset, dan itu sudah terbukti bekerja saat saya mengetikkan kode |
| 14, unduh hasil CSV | Saya tidak mengunduh berkas ke komputer Anda tanpa Anda minta. Tombolnya ada dan hanya muncul kalau daftar target sudah terisi |
| 15, masuk sebagai role Staf GA | Butuh akun lain beserta katasandinya, dan itu Anda yang harus buat |

### 5.3 Cacat yang ditemukan dan sudah diperbaiki

1. **Warna utama tampil sian menyala, bukan petrol tua.** `Color::hex('#17505E')` ternyata hanya
   mengambil rona warnanya, lalu membangun sendiri tingkat terang dan kepekatannya. Kepekatan naik
   dari 0,062 menjadi 0,169 dalam OKLCH, dan itulah yang bikin tombol menyala. Warna abu abu juga
   kehilangan seluruh rona birunya. Diperbaiki dengan menulis sebelas tingkat untuk tiap warna di
   `AdminPanelProvider::colors()`, warna asli DESIGN.md dipasang di tingkat yang paling dekat tingkat
   terangnya. Sudah diperiksa ulang di layar: `--primary-700` sekarang persis `#17505E`.

2. **Seksi Cakupan pemeriksaan sempit.** Halaman formulir memakai grid dua kolom bawaan Filament,
   jadi tiga pilihan cakupan hanya kebagian separuh lebar dan teksnya patah. Diperbaiki dengan
   `->columns(1)` di tingkat halaman. Lebar tiap pilihan naik dari sekitar 140 piksel menjadi 290 piksel.

3. **Tombol Hapus masih muncul pada sesi yang sudah diterapkan penyesuaiannya.** `canDelete()` sudah
   melarangnya, tetapi Filament menilai tombol hapus lewat Gate, dan `Gate::before` di
   `AppServiceProvider` meloloskan super admin untuk semua kemampuan, jadi larangan itu terlewat.
   Ini pelanggaran R-26: tombol yang kalau ditekan seharusnya gagal. Diperbaiki dengan menyebut
   `canDelete()` secara eksplisit pada tombol hapus di halaman dan di tabel. Sudah diperiksa ulang:
   sesi `SO/2026/09/0001` tidak lagi punya tombol hapus, sesi lain masih punya.

4. **Daftar target tidak ikut segar setelah tombol di kepala halaman ditekan.** Daftar target adalah
   komponen Livewire tersendiri, jadi setelah Susun daftar target ia masih menulis "Daftar target masih
   kosong" padahal barisnya sudah jadi. Diperbaiki dengan peristiwa `gais-daftar-target-berubah` yang
   dikirim halaman dan didengar daftar target. Sudah diperiksa ulang: 153 baris langsung muncul tanpa
   memuat ulang halaman. Peristiwa yang sama juga dikirim saat sesi dimulai, ditutup, dan dibatalkan,
   karena tombol centang di tiap baris memang bergantung pada status sesi.

5. **Garis fokus memakai warna teks, bukan terracotta.** Berkas `gais.css` dimuat sebelum berkas gaya
   Filament, jadi pemilih dengan kekhususan yang sama kalah. Diperbaiki dengan awalan `html.fi`.
   Perbaikan ini baru berlaku setelah perintah di bagian 4 dijalankan, jadi ini satu satunya perbaikan
   yang belum saya lihat sendiri di layar.

### 5.4 Yang tersisa dan tidak diperbaiki

- **Satu galat di console:** `isOpen is not defined`, muncul sekali dari komponen modal Filament pada
  saat sebuah penghapusan menutup modal lalu mengalihkan halaman. Fungsinya berjalan benar,
  penghapusannya berhasil. Ini balapan antara Livewire dan Alpine di dalam Filament, bukan kode kita,
  dan saya belum bisa memunculkannya lagi dengan kecepatan klik manusia. Dicatat di sini supaya tidak
  hilang, bukan diklaim bersih.
- **Kartu angka di dasbor** sempat tampil sebagai kotak putih kosong sepersekian detik sebelum isinya
  masuk. Ini pemuatan malas bawaan Filament. Tidak diubah karena tidak salah, hanya belum halus.
- **Jejak audit menyimpan nilai lama dan nilai baru, tetapi belum ada layar untuk melihatnya.** Yang
  bisa dilihat baru nama kolom yang berubah, lewat kolom "Kolom yang berubah" yang tersembunyi secara
  bawaan. Ini pekerjaan tersendiri, bukan bagian kiriman ini.

### 5.5 Data uji yang tertinggal

Sesi `SO/2026/09/0001` "Opname lantai 3 uji coba" saya biarkan ada. Isinya 33 target, 8 diperiksa,
2 selisih, penyesuaiannya sudah diterapkan, jadi berguna sebagai contoh saat demo. Sesi itu memang
tidak bisa dihapus, itu aturan yang kita putuskan sendiri di bagian 2. Dua sesi uji lain sudah saya
hapus. Satu akibat yang nyata: aset `FIN-1209-2020-0001` sekarang tercatat di `HO-L3-R02` Ruang server,
karena memang itu hasil opname yang diterapkan. Kalau Anda ingin data demo benar benar bersih,
jalankan `php artisan gais:demo-data --hapus` lalu `php artisan gais:demo-data`, dan hapus sesi itu
sebelum penyesuaiannya diterapkan.

Setelah Anda menjalankan perintah di bagian 4 dan mencoba langkah 8, 14, dan 15, saya kerjakan
kiriman C: inventaris ATK habis pakai.
