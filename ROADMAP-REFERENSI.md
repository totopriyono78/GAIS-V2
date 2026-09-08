# GAIS: Analisis Sistem Referensi dan Peta Pengembangan Lanjutan

Disusun 8 September 2026, setelah mempelajari *Panduan Pengguna General Administration
Information System* (Putera Sampoerna Foundation), 160 halaman.

Dokumen ini bukan pengganti `ROADMAP.md`. Ia menambah Tahap 7 sampai Tahap 10 di ujungnya,
dan menyebut dengan jelas bagian mana dari sistem referensi yang sebaiknya tidak ditiru.

---

## 1. Cara dokumen referensi ini dibaca

Yang saya baca lengkap: daftar isi, seluruh Data Induk (halaman 7 sampai 28), Stationery
(29 sampai 37), GA Transaction (38 sampai 54), Taxi Voucher (55 sampai 58), Operational Car
(59 sampai 66), Generate To Acumatica (67 sampai 68), dan seluruh Modul Procurement
(143 sampai 160).

Bagian G, Pengelolaan GA (halaman 69 sampai 142), sengaja tidak dibaca halaman per halaman.
Daftar isinya memperlihatkan bahwa isinya adalah cerminan sisi administrator dari bagian A
sampai E, submenu demi submenu, dengan nomor halaman yang berpasangan satu ke satu. Tidak ada
modul baru di sana.

Satu hal yang perlu Anda tahu sebelum membaca sisanya: **tangkapan layar di dokumen itu
bertanggal 2014 dan 2015**. Beberapa alur di dalamnya menyelesaikan masalah yang sudah berubah
bentuk sejak saat itu, dan itu saya sebutkan satu per satu di bagian 6.

---

## 2. Apa sebenarnya yang dicakup sistem referensi

Sistem itu adalah **sistem transaksi administrasi harian**. Isinya lalu lintas kantor:
surat masuk dan keluar, paket, parkir, taksi, mobil sewa, alat tulis, dan hubungan dengan
rekanan.

Sistem itu **bukan** sistem aset. Di seluruh 160 halaman tidak ada daftar aset tetap, tidak ada
penyusutan, tidak ada nilai buku, tidak ada pelepasan aset, tidak ada jadwal pemeliharaan
preventif, dan tidak ada tiket perbaikan. Kendaraan pun tidak dimiliki: menu Operational Car
berputar di sekitar kendaraan **sewaan**, lengkap dengan perusahaan penyewa, sopir yang melekat,
tagihan sewa per periode, dan lembur sopir.

Ini penting karena artinya GAIS dan sistem referensi hampir tidak bertumpang tindih. Sistem
referensi mengisi tepat bagian yang paling kosong di GAIS, dan sebaliknya.

---

## 3. Yang sudah ada di GAIS dan tidak ada di sistem referensi

Ditulis lebih dulu supaya perbandingannya jujur dua arah:

- Aset tetap dengan penyusutan garis lurus dan saldo menurun, penutupan periode, nilai buku,
  serta laba rugi pelepasan
- Stock opname aset, mutasi aset dengan BAM dan BAST, dokumen pendukung per aset
- Jadwal pemeliharaan preventif dengan riwayat kunjungan, dan perintah kerja korektif
- Tiket permintaan perbaikan dari karyawan dengan batas waktu penyelesaian
- Masa berlaku dokumen kendaraan (pajak, STNK, KIR, asuransi) beserta tombol perpanjang
- Konsumsi bahan bakar kilometer per liter dari dua pengisian penuh
- Anggaran per departemen per kategori per tahun dengan realisasi yang dihitung sendiri
- Penggantian biaya karyawan untuk segala jenis biaya, bukan hanya parkir
- Peran dan izin yang bisa disusun sendiri, bukan peran tetap yang ditulis di kode

Jadi tidak ada satu pun bagian GAIS yang perlu dibongkar karena sistem referensi melakukannya
dengan cara lain.

---

## 4. Daftar selisih

Kolom "Beban" adalah perkiraan kasar besarnya pekerjaan, bukan janji tanggal.

| # | Ada di referensi | Keadaan di GAIS | Beban |
|---|---|---|---|
| 1 | Permintaan pemakaian ATK oleh karyawan, persetujuan atasan, riwayat pemakaian | Belum ada. Sudah tercatat ditunda sejak Kiriman C | Kecil |
| 2 | Pembelian ATK (PO) dan penerimaan barang terhadap PO | Belum ada. GAIS hanya punya mutasi "barang masuk" yang diketik tangan | Sedang |
| 3 | Stock opname ATK per periode, dan penyesuaian stok sebagai dokumen | Opname hanya untuk aset. Koreksi stok ATK masih berupa baris mutasi | Sedang |
| 4 | Surat masuk, surat keluar, dan Board Letter | Belum ada sama sekali | Sedang |
| 5 | Permintaan pengiriman paket dan realisasinya (kurir, AWB, asuransi, packing, diskon, pajak) | Belum ada sama sekali | Sedang |
| 6 | Kendaraan pribadi karyawan sebagai data induk | Belum ada. GAIS hanya mengenal kendaraan perusahaan | Kecil |
| 7 | Langganan parkir per periode dengan tarif motor dan mobil | Belum ada | Kecil |
| 8 | Klaim parkir per kejadian atas kendaraan pribadi | Sebagian. Bisa lewat penggantian biaya, tetapi tanpa kaitan ke kendaraan | Kecil |
| 9 | Perjalanan dinas dan pengecualian kebijakan perjalanan | Belum ada. Konsepnya sudah dibahas, belum diputuskan | Sedang |
| 10 | Sopir sebagai data induk yang melekat pada kendaraan | Belum ada | Kecil |
| 11 | Kendaraan sewa: perusahaan penyewa, tagihan sewa per periode, KM awal dan akhir, lembur | Belum ada. GAIS mengasumsikan kendaraan milik sendiri | Sedang |
| 12 | Pembagian biaya kendaraan ke beberapa pemakai menurut persentase | Belum ada | Kecil |
| 13 | Pendaftaran rekanan lengkap: identitas, keuangan, teknis, referensi kerja | Minim. Rekanan GAIS hanya nama, kontak, dan alamat | Sedang |
| 14 | Masa berlaku dokumen legal rekanan (SIUP, TDP, NPWP, PKP, domisili) | Belum ada | Kecil |
| 15 | Sektor bisnis berjenjang dan spesialisasi bisnis | Belum ada | Kecil |
| 16 | Evaluasi rekanan dengan parameter berskor dan ambang kelayakan pakai ulang | Belum ada | Sedang |
| 17 | Rekanan pilihan dengan persetujuan berjenjang sampai direksi | Belum ada | Sedang |
| 18 | Pengecualian rekanan | Belum ada | Kecil |
| 19 | Kunjungan lapangan pra kualifikasi dengan daftar periksa dan kesimpulan | Belum ada | Sedang |
| 20 | Tanda terima dokumen dari rekanan | Belum ada | Kecil |
| 21 | Ekspor rekap periode ke sistem akuntansi, dengan pemetaan akun per departemen | Sudah direncanakan sebagai Tahap 6 | Besar |
| 22 | Ekspor Excel dan cetak di hampir semua layar daftar | Sebagian. Hanya hasil opname aset yang bisa diunduh | Kecil |
| 23 | Penguncian data induk (Is Locked) supaya tidak bisa diubah lagi | Belum ada | Kecil |

Tiga catatan atas tabel di atas.

**Nomor 2 dan 3 lebih penting daripada kelihatannya.** Saat ini stok ATK di GAIS bertambah
karena seseorang mengetik baris "barang masuk". Tidak ada yang menghubungkan angka itu dengan
pesanan yang dikirim ke pemasok, dan tidak ada yang menghubungkannya dengan tagihan yang
nantinya dibayar. Jadi tiga angka yang seharusnya sama, yaitu yang dipesan, yang diterima, dan
yang ditagih, hidup di tiga tempat tanpa saling memeriksa. Sistem referensi menutup celah itu.
GAIS sudah punya kedua ujungnya, yakni buku stok dan tagihan rekanan, jadi yang perlu dibangun
tinggal jembatannya.

**Nomor 4 dan 5 adalah bagian GAIS yang paling kosong.** Untuk banyak tim GA, mencatat surat
masuk dan mengurus kiriman paket adalah pekerjaan yang paling sering dilakukan setiap hari,
dan justru itu yang sama sekali belum tersentuh.

**Nomor 21 sudah ada di peta.** Tahap 6 GAIS sudah merencanakan hal yang sama dengan Generate
To Acumatica. Yang bisa diambil dari referensi adalah bentuk layar Budget Account Setting,
yaitu satu tabel yang memetakan pasangan jenis transaksi, tahun, budget, akun, sub akun, dan
departemen. Bentuk itu lebih baik daripada memetakan akun di kolom kategori saja, karena satu
kategori biaya bisa jatuh ke akun berbeda tergantung departemennya.

---

## 5. Peta tahapan lanjutan

Urutannya mengikuti dua hal: apa yang paling sering dipakai orang setiap hari, dan apa yang
paling sedikit membutuhkan keputusan baru dari Anda.

### Tahap 7: Menutup siklus ATK

Alasan ditaruh pertama: seluruh bahannya sudah ada di GAIS. Barang habis pakai, buku stok,
rekanan, kategori biaya, anggaran, dan tagihan rekanan semuanya sudah jalan. Tahap ini hanya
menyambungkan yang sudah ada, jadi ia yang paling cepat menghasilkan dan paling kecil risikonya.

**Kiriman M, permintaan pemakaian ATK**

- Karyawan meminta barang, atasan menyetujui, tim GA menyerahkan
- Alurnya sama persis dengan tiket perbaikan dan penggantian biaya yang sudah ada, termasuk
  tiga keadaan yang melewati persetujuan atasan. Tidak ada pola baru yang perlu dipelajari
  pemakainya
- Penyerahan barang otomatis menjadi baris "barang keluar" di buku stok, lengkap dengan
  departemen pemohon, sehingga pemakaian per departemen berhenti diketik tangan
- Stok yang tidak cukup ditolak sebelum permintaan bisa diserahkan, dengan menyebut angka
  stok tersedia. Penjaga yang sama sudah ada di mutasi stok
- Riwayat pemakaian per karyawan dan per departemen

**Kiriman N, pembelian dan penerimaan ATK**

- Pesanan pembelian ke rekanan: nomor sendiri, tanggal, rekanan, dan baris barang dengan
  jumlah serta harga satuan. Nilainya dijumlahkan dari baris, seperti tagihan rekanan
- Penerimaan barang mengacu pada pesanan, dan boleh sebagian. Yang diterima menjadi baris
  "barang masuk" secara otomatis
- Sisa yang belum diterima terlihat di kartu pesanan, jadi pesanan yang menggantung tidak
  hilang dari pandangan
- Tagihan rekanan boleh menunjuk pesanan pembelian. Saat ditunjuk, selisih antara nilai
  tagihan dan nilai yang sudah diterima disebutkan di layar. Ini yang menjawab pertanyaan
  "kenapa tagihannya lebih besar dari yang datang"
- Harga satuan terakhir tersimpan di kartu barang sebagai keterangan, bukan sebagai harga
  resmi, karena harga resmi adalah yang ada di pesanan

**Kiriman O, opname dan penyesuaian stok ATK**

- Periode opname yang bisa dikunci, seperti periode penyusutan yang sudah ada
- Daftar target disusun dari kategori dan tempat simpan, sama seperti opname aset
- Selisih antara catatan dan hitungan fisik menjadi dokumen penyesuaian tersendiri, bukan
  baris koreksi yang bisa diketik siapa saja kapan saja
- Hasil opname bisa diunduh

Selesai kalau: satu siklus penuh bisa dijalankan, mulai dari karyawan meminta pulpen sampai
pesanan ke pemasok, penerimaan barang, tagihan yang dibayar, dan opname akhir bulan yang
angkanya cocok.

### Tahap 8: Lalu lintas surat dan kiriman

Alasan ditaruh kedua: ini pekerjaan harian tim GA yang paling belum tersentuh, dan seluruhnya
berdiri sendiri sehingga tidak menunggu tahap lain.

**Kiriman P, surat masuk dan surat keluar**

- Kategori surat sebagai data induk
- Surat masuk: tanggal terima, pengirim, perihal, tujuan internal, dan pindaian
- Surat keluar: nomor surat dari penomoran dokumen yang sudah ada, penanda tangan, tujuan,
  perihal, dan pindaian
- Serah terima surat masuk ke orang yang dituju, dengan waktunya. Inilah yang menjawab
  "surat itu sudah sampai ke siapa"
- Board Letter di sistem referensi tidak dibuat sebagai modul terpisah. Ia surat keluar
  dengan kategori khusus dan penanda tangan tingkat direksi, dan memisahkannya menjadi menu
  sendiri hanya menggandakan layar yang sama

**Kiriman Q, pengiriman paket**

- Kurir atau ekspedisi sebagai data induk, dengan diskon dan pajak yang berlaku
- Permintaan pengiriman: tujuan, isi, perkiraan biaya, dan departemen yang dibebani
- Realisasi: kurir yang dipakai, nomor resi, biaya sebenarnya, asuransi, packing, diskon,
  dan pajak. Perkiraan dan kenyataan disimpan berdampingan, tidak saling menimpa
- Biaya kiriman masuk ke realisasi anggaran lewat kategori biaya, memakai jalur yang sudah
  dibangun untuk tagihan rekanan

Selesai kalau: satu surat masuk bisa ditelusuri sampai ke mejanya, dan biaya kiriman satu
bulan bisa dijumlahkan per departemen tanpa membuka arsip fisik.

### Tahap 9: Transportasi dan perjalanan

**Kiriman R, kendaraan pribadi dan parkir**

- Kendaraan pribadi karyawan sebagai data induk, dengan pemilik, nomor polisi, jenis, dan foto
- Periode parkir dengan tarif motor dan tarif mobil, ditetapkan per periode
- Langganan parkir: memilih periode lalu mencentang karyawan beserta kendaraannya. Totalnya
  dihitung dari tarif periode kali jumlah kendaraan per jenis, tidak pernah diketik
- Klaim parkir per kejadian, menunjuk kendaraan pribadi yang sudah terdaftar. Alurnya
  memakai penggantian biaya karyawan yang sudah ada, bukan modul baru

**Kiriman S, perjalanan dinas**

Konsepnya sudah dibahas pada 7 September 2026 dan menunggu keputusan Anda. Pengecualian
perjalanan dari sistem referensi tidak perlu menjadi modul sendiri: ia adalah cabang
persetujuan tambahan pada pengajuan perjalanan yang jatuh di luar kebijakan, dengan alasan
dan penyetuju yang lebih tinggi.

**Kiriman T, sopir dan kendaraan sewa**

- Sopir sebagai data induk, dengan nomor SIM dan masa berlakunya, serta kendaraan yang
  ditugaskan kepadanya. Masa berlaku SIM masuk ke pengingat jatuh tempo yang sudah ada
- Kendaraan sewa: perusahaan penyewa, nomor kontrak, dan masa sewa. Kendaraan sewa tidak
  disusutkan, dan aturan itu sudah ada di GAIS sejak Kiriman D2
- Tagihan sewa per periode per kendaraan, dengan KM awal dan KM akhir. Odometer memakai
  penjaga yang sama dengan log perjalanan
- Pembagian biaya satu tagihan ke beberapa departemen menurut persentase, bukan hanya nilai.
  Sisa pembulatan jatuh ke baris terakhir supaya jumlahnya selalu tepat

### Tahap 10: Rekanan dan pengadaan

Ditaruh terakhir bukan karena paling tidak penting, melainkan karena paling banyak
membutuhkan keputusan kebijakan perusahaan sebelum bisa dibangun. Parameter penilaian dan
ambang kelayakan pakai ulang adalah kesepakatan tim pengadaan, bukan angka yang boleh dikarang.

**Kiriman U, data rekanan yang lengkap**

- Sektor bisnis berjenjang dan spesialisasi bisnis sebagai data induk
- Rekanan diperluas: kategori (barang atau jasa), bentuk badan usaha, sektor, spesialisasi,
  nomor dan tanggal pendaftaran, kontak person beserta jabatannya
- Masa berlaku dokumen legal: SIUP, TDP, NPWP, PKP, dan domisili, masing masing dengan
  tanggal berakhir dan berkasnya. Yang mendekati jatuh tempo muncul di pengingat, memakai
  mesin pengingat yang sudah dipakai dokumen kendaraan
- Rekanan yang dokumennya sudah lewat masa berlaku tidak dihalangi, tetapi ditandai di
  layar tagihan dan pesanan pembelian, karena menghalangi pekerjaan yang terlanjur berjalan
  hanya membuat orang mencari jalan memutar

**Kiriman V, evaluasi rekanan**

- Parameter penilaian sebagai data induk yang bisa diubah, bukan sepuluh baris yang ditulis
  di kode. Sistem referensi memakai sepuluh parameter tetap, dan itu membuat kriterianya
  tidak bisa berubah tanpa memanggil pemrogram
- Penilaian per parameter, total dihitung dari barisnya
- Ambang kelayakan pakai ulang ditetapkan di layar Pengaturan, dengan tiga pita seperti di
  sistem referensi. Nilainya kosong sampai tim pengadaan menyebutkannya
- Hasil evaluasi terbaca di kartu rekanan, dan terlihat saat rekanan itu dipilih di tagihan
  atau pesanan pembelian

**Kiriman W, rekanan pilihan, pengecualian, dan kunjungan lapangan**

- Rekanan pilihan dengan persetujuan berjenjang dan alasan pemilihannya
- Pengecualian rekanan untuk keadaan yang tidak memenuhi syarat biasa
- Kunjungan lapangan dengan daftar periksa, kesimpulan, dan penilaian akhir
- Tanda terima dokumen dari rekanan

---

## 6. Pola lintas modul yang layak diambil

Empat kebiasaan di sistem referensi yang berlaku di semua layarnya, dan tiga di antaranya
layak ditiru:

**Penguncian data induk.** Hampir setiap data induk di sana punya penanda Locked, yang
membuatnya tidak bisa diubah lagi oleh pegawai biasa. Ini menyelesaikan masalah nyata:
mengubah harga bawaan barang setelah pesanan dibuat akan menggeser angka yang sudah
terlanjur dipakai. GAIS menyelesaikan sebagian masalah ini dengan pembekuan nilai saat
transaksi dibuat, tetapi penanda kunci tetap berguna untuk data yang jarang berubah.
**Layak diambil**, sebagai satu kolom di data induk yang nilainya dipakai transaksi.

**Ekspor Excel dan cetak di layar daftar.** Ada di hampir semua layar daftar di sana.
GAIS baru punya unduhan di hasil opname aset. **Layak diambil**, dan sebaiknya dikerjakan
sekali sebagai kemampuan bersama semua tabel, bukan ditambahkan satu per satu.

**Perkiraan dan kenyataan disimpan berdampingan.** Pengiriman paket punya Estimated Amount
di permintaan dan Amount di realisasi. Kendaraan operasional punya Request dan Realization
sebagai dua dokumen. **Layak diambil**, karena selisih antara yang diminta dan yang terjadi
adalah pertanyaan yang paling sering diajukan saat anggaran dibahas.

**Layar administrator yang terpisah dari layar pengguna.** Bagian G sistem referensi adalah
73 halaman yang isinya mengulang bagian A sampai E. **Tidak layak diambil.** GAIS memakai
satu layar yang isinya menyempit menurut izin, dan itu yang membuat 48 tabel di GAIS tidak
perlu digandakan menjadi 96.

---

## 7. Yang sengaja tidak diambil

**Voucher taksi dengan tujuh layar.** Sistem referensi mengelola lembar voucher fisik:
nomor seri per penyedia, penyerahan lembarnya ke karyawan, sampai rekap tagihan yang diimpor
dari berkas Excel penyedia. Kebutuhan itu sekarang hampir selalu dijawab akun korporat
penyedia perjalanan daring, yang menagih langsung ke perusahaan. Kalau kelak dibutuhkan,
bentuknya bukan tujuh layar voucher, melainkan satu tagihan rekanan yang barisnya dibebankan
per departemen, dan itu sudah ada di GAIS.

**Template Original Parking Receipt.** Satu halaman cetak berisi kotak tanggal 1 sampai 31
untuk menempelkan struk parkir fisik. Ini solusi kertas untuk masalah kertas. Foto struk per
baris di penggantian biaya sudah menjawab hal yang sama tanpa lem.

**Master Company yang terpisah dari Vendor.** Di sistem referensi ada dua daftar perusahaan
yang saling tumpang tindih: Company di modul GA dan Vendor Registration di modul Procurement,
dengan kolom yang hampir sama. GAIS sudah punya satu daftar rekanan, dan sebaiknya tetap satu.
Kolom yang berbeda antara rekanan pemasok dan perusahaan tujuan kiriman ditangani lewat
penanda peran, bukan lewat dua tabel.

**Penanda tangan (Signer) sebagai data induk.** Di sana ada daftar orang yang berhak
menandatangani, dengan jabatan dan status aktif. GAIS sudah menjawab hal yang sama lewat dua
jalan yang berbeda: kepala departemen untuk persetujuan, dan izin per aksi untuk kewenangan.
Menambahkan daftar ketiga akan membuat tiga tempat yang harus dijaga tetap sama.

**Corporate Stationery dan Others Stationery sebagai menu terpisah.** Di sana alat tulis
dipecah menjadi tiga menu data induk yang bentuk isiannya nyaris identik. GAIS memakai satu
daftar barang habis pakai dengan kategori, dan itu yang benar.

---

## 8. Keputusan yang perlu Anda ambil

| Sebelum | Yang perlu diputuskan |
|---|---|
| Tahap 7 | Apakah pembelian ATK melalui pesanan resmi ke pemasok, atau tim GA membeli langsung lalu mencatat. Kalau yang kedua, Kiriman N menyusut menjadi pencatatan pembelian saja tanpa pesanan |
| Tahap 8 | Apakah surat masuk perlu diserahterimakan dengan tanda terima di dalam sistem, atau cukup dicatat lalu diantar seperti biasa |
| Tahap 9 | Apakah perusahaan menyewa kendaraan atau seluruhnya milik sendiri. Kalau seluruhnya milik sendiri, Kiriman T menyusut menjadi data sopir saja. Untuk perjalanan dinas, tiga pertanyaan dari 7 September 2026 masih menunggu jawaban |
| Tahap 10 | Parameter penilaian rekanan dan ambang kelayakan pakai ulang. Ini kesepakatan tim pengadaan, dan kolomnya akan dibiarkan kosong sampai disepakati, sama seperti target waktu penyelesaian tiket |

Dan satu urutan yang perlu Anda pilih sekarang: **Tahap 7 lebih dulu atau Tahap 8 lebih dulu.**
Tahap 7 lebih cepat selesai dan menutup sesuatu yang sudah setengah jadi. Tahap 8 menyentuh
pekerjaan yang lebih sering dilakukan setiap hari tetapi sama sekali belum ada. Keduanya tidak
saling menunggu.

---

## 9. Yang masih tersisa dari peta lama

Belum selesai dan tidak tergantikan oleh dokumen ini:

- Tahap 4: kebersihan dan keamanan, konsepnya sudah dibahas dan menunggu keputusan
- Tahap 5: laporan anggaran versus realisasi yang bisa diunduh
- Tahap 6: jurnal dan ekspor ke sistem akuntansi, menunggu daftar nomor akun dari tim finance
- Peminjaman aset, yang statusnya sudah ada sebagai label tetapi alurnya belum dibangun
