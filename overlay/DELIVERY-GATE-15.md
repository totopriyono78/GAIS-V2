# Delivery Gate 15: Kiriman N, pembelian dan penerimaan ATK

Tahap 7, kiriman kedua. Diverifikasi 8 September 2026 di `http://127.0.0.1:8000`
memakai akun `test@gais.test`.

**Hasil: LULUS**, dengan tiga cacat yang ditemukan dan diperbaiki di ronde ini, salah satunya
fatal. Bagian 6 berisi satu pertanyaan kebijakan yang perlu Anda jawab, dan bagian 9 berisi
data uji yang tertinggal.

---

## 1. Design Read

Saya baca ini sebagai **layar rantai tiga dokumen untuk tim GA dan manajernya**: apa yang
dipesan, apa yang benar benar datang, dan apa yang ditagihkan. Gaya visual mengikuti modul
alur yang sudah ada. Dial ENERGY rendah, RHYTHM mengikuti tabel yang sudah ada, MOTION nol.

Yang membedakannya dari kiriman sebelumnya: layar ini menampilkan tiga angka yang selama ini
dianggap sama padahal tidak. Nilai pesanan, nilai barang yang sudah diterima, dan nilai
tagihan. Hampir seluruh keputusan di bawah lahir dari kewajiban memisahkan ketiganya dengan
tegas dan menyebut selisihnya di tempat orang benar benar membacanya.

## 2. Alasan satu baris tiap keputusan (R-31)

- **Warna:** merah hanya di satu tempat baru, yaitu kalimat selisih tagihan ketika fakturnya
  melebihi barang yang datang pada pesanan yang sudah lengkap. Selisih pada pesanan yang belum
  lengkap berwarna kuning, karena itu keadaan wajar, bukan masalah.
- **Layout:** satu kartu formulir dengan `columnSpanFull()`, isinya berubah mengikuti cara
  membeli, dan dua kartu infolist yang memisahkan isi pembelian dari jejak persetujuan
  serta kedatangan barang.
- **Tipografi:** nomor dokumen memakai huruf lebar tetap, sama seperti seluruh nomor lain.
- **Spacing:** mengikuti panel, tidak ada penyesuaian.
- **Kartu:** dua relation manager sebagai tab, Items dan Deliveries Received, karena keduanya
  dibaca pada saat yang berbeda dan menumpuknya membuat halaman terlalu panjang untuk dibaca
  sambil berdiri di gudang.
- **Ilustrasi:** tidak ada.

## 3. Yang dibangun

Dua bentuk pembelian hidup di satu tabel dan satu layar, dibedakan kolom `kind`:

```
Pesanan resmi:      GA menyusun -> Manajer menyetujui -> Barang datang bertahap -> Selesai
Pembelian langsung: GA mencatat apa yang sudah dibeli -> Langsung diterima penuh -> Selesai
```

Tiga keputusan Anda pada 8 September 2026, dan bentuknya di kode:

| Keputusan | Bentuknya di aplikasi |
|---|---|
| Pesanan disetujui manajer GA | Aksi `approve` terpisah dari `receive`. Pesanan resmi lewat draf, diajukan, disetujui |
| Penjual boleh ditulis bebas | Pesanan resmi wajib menunjuk rekanan terdaftar, pembelian langsung mengetik nama toko di kolomnya sendiri |
| Tagihan dihubungkan sekarang | Kolom `supply_purchase_id` di tagihan, plus kalimat selisih di halaman lihat dan di kotak persetujuan |

**Pembelian langsung sengaja tidak melewati persetujuan**, dan ini keputusan saya, bukan
jawaban Anda. Pertanyaan yang Anda jawab menyebut "sebelum dikirim ke pemasok", dan pembelian
langsung tidak pernah punya momen itu: saat orang mencatatnya, uangnya sudah keluar dan
barangnya sudah di tangan, jadi persetujuan di titik itu tidak bisa mencegah apa pun. Alasan
lompatannya ditulis ke kolomnya sendiri dan terbaca di layar. Kalau Anda tetap ingin pembelian
langsung disetujui juga, itu perubahan kecil.

**Jumlah yang sudah diterima tidak pernah disimpan sebagai kolom.** Ia dijumlahkan dari baris
penerimaan, sama seperti stok dijumlahkan dari mutasi sejak kiriman C dan nilai tagihan
dijumlahkan dari barisnya sejak kiriman K.

## 4. Bukti verifikasi (R-35)

Seluruh angka di bawah dibaca dari layar dan dihitung ulang dengan tangan.

### 4.1 Pesanan resmi, PP/2026/09/0001

| Yang diperiksa | Hasil |
|---|---|
| Formulir berubah mengikuti cara membeli | Pesanan resmi menampilkan Rekanan dan Barang dijanjikan datang. Pembelian langsung menggantinya dengan Beli di mana, dan label tanggalnya berubah menjadi Tanggal beli |
| Nilai pesanan | Rp 400.000, dan hitungan tangan 12×8.500 + 10×22.000 + 12×6.500 = 102.000 + 220.000 + 78.000 = **Rp 400.000** |
| Kotak ajukan | "Rp 400.000 untuk 3 jenis barang dari PT. Abadi Jaya. Setelah disetujui manajer, pesanan boleh dikirim ke rekanan dan barangnya ditunggu. Stok belum bertambah sampai barangnya benar benar datang." |
| Kotak setujui | Menyebut bahwa yang disetujui adalah izin mengirim pesanan, dan bahwa belum ada uang keluar maupun stok bertambah |

### 4.2 Penerimaan sebagian, dan penjaganya

Kotak Receive Goods berisi satu baris per barang yang belum lengkap, jumlahnya sudah terisi
sisa yang belum datang.

| Yang diperiksa | Hasil |
|---|---|
| Penjaga kelebihan terima | Diisi 15 box padahal sisa 10: "Teh celup kotak isi 25 diterima 15 box, padahal sisa yang belum datang tinggal 10 box." Status tidak berubah, kotak dialog tetap terbuka |
| Nilai penerimaan pertama | Rp 268.000, dan hitungan tangan 12×8.500 + 4×22.000 + 12×6.500 = 102.000 + 88.000 + 78.000 = **Rp 268.000** |
| Status sesudahnya | Diterima sebagian, dengan tahap "1 jenis barang belum datang seluruhnya" |
| Tombol Cancel | Hilang sendiri, karena pembelian yang barangnya sudah masuk gudang tidak boleh dibatalkan |
| Per baris | Spidol Lengkap, Teh Kurang 6 box, Kertas struk Lengkap |
| Penerimaan kedua | Kotaknya hanya menampilkan satu baris, yaitu Teh, dengan keterangan "sisa 6 box dari 10 box". Dua baris yang sudah lengkap tidak ikut ditampilkan |
| Status akhir | Selesai, "2 kali penerimaan tercatat, senilai Rp 400.000. Seluruh barang sudah datang." |

### 4.3 Aritmetika stok, dihitung tangan

| Barang | Stok sebelum | Diterima | Stok sesudah |
|---|---|---|---|
| Spidol papan tulis hitam | 0 pcs | 12 pcs | 12 pcs |
| Teh celup kotak isi 25 | 0 box | 4 lalu 6 box | 10 box |
| Kertas struk kasir | 8 roll | 12 roll | 20 roll |

Di daftar Supply Movements muncul tepat tiga mutasi "Barang masuk" untuk penerimaan pertama,
tidak lebih. Stok Kertas struk kasir dibaca ulang dari layar Supply Items dan berbunyi 20 roll,
dan penanda keadaannya berubah sendiri dari "Perlu dipesan" menjadi "Aman" karena 20 sudah di
atas batas minimum 12.

**Harga pembelian terakhir ikut diperbarui sendiri.** Kartu Spidol papan tulis hitam yang
sebelumnya tidak punya harga sekarang berbunyi Rp 8.500, diambil dari harga pesanannya lewat
mutasi barang masuk. Inilah yang membuat nilai persediaan di dasbor bergerak mengikuti harga
yang benar benar dibayar, bukan angka yang diketik terpisah.

### 4.4 Pembelian langsung, PP/2026/09/0002

| Yang diperiksa | Hasil |
|---|---|
| Penjual bebas | "Toko Sinar Jaya, Jalan Kaliurang", tanpa perlu didaftarkan sebagai rekanan |
| Nilai | Rp 488.000, hitungan tangan 6×18.000 + 4×95.000 = 108.000 + 380.000 = **Rp 488.000** |
| Satu tombol | Record and Receive langsung membuat penerimaan penuh dan menutup pembelian |
| Alasan lompatan | "Lewat persetujuan. Pembelian langsung, barangnya sudah di tangan saat dicatat." |
| Stok | Pembersih lantai 3 → 9 botol, Tinta printer warna 4 → 8 botol |

### 4.5 Penghubungan ke tagihan rekanan

Ini bagian yang Anda minta dikerjakan sekalian, dan bagian yang paling banyak cabangnya.

Daftar pesanan di layar tagihan menyempit ke pesanan milik rekanan yang dipilih. PP/2026/09/0002
tidak muncul di sana, dan itu benar: penjualnya toko yang ditulis bebas, bukan rekanan
terdaftar.

Empat cabang kalimat selisih diuji satu per satu:

| Keadaan | Kalimat di layar |
|---|---|
| Tagihan Rp 268.000, diterima Rp 268.000 | "Cocok dengan barang yang sudah diterima, Rp 268.000." |
| Tagihan Rp 400.000, diterima Rp 268.000, 1 barang belum lengkap | "Tagihan lebih besar Rp 132.000 daripada barang yang sudah diterima, Rp 268.000. Masih ada 1 jenis barang yang belum datang seluruhnya, jadi selisih ini wajar kalau fakturnya menagih seluruh pesanan di muka." |
| Tagihan Rp 450.000, diterima Rp 400.000, seluruhnya sudah datang | "Tagihan lebih besar Rp 50.000 daripada barang yang sudah diterima, Rp 400.000. Seluruh barang pesanan itu sudah datang, jadi selisih ini perlu ditanyakan ke rekanannya." Berwarna merah |
| Tagihan lebih kecil dari yang diterima | "Kemungkinan masih ada faktur susulan atas pesanan yang sama." |

Hitungan tangan: 400.000 − 268.000 = 132.000, dan 450.000 − 400.000 = 50.000. Keduanya cocok.

Kalimat yang sama juga muncul di **kotak persetujuan tagihan**, bukan hanya di halaman lihat:

> Approve TG/2026/09/0003. Rp 400.000 dari PT. Abadi Jaya, dibebankan ke General Affair.
> Setelah disetujui, nilainya masuk ke realisasi anggaran tahun 2026. Tagihan ini menagih
> pesanan PP/2026/09/0001. Cocok dengan barang yang sudah diterima, Rp 400.000.

Tautan baliknya juga bekerja: halaman pesanan menyebut "TG/2026/09/0003 senilai Rp 400.000".

### 4.6 Menutup pesanan yang sisanya tidak datang

Diuji pada PP/2026/09/0003 yang sengaja belum menerima barang sama sekali, supaya pengujian
ini tidak menyentuh stok.

| Yang diperiksa | Hasil |
|---|---|
| Kotak dialog | Menyebut berapa jenis yang belum datang, dan bahwa sisanya tetap terbaca serta stok tidak tersentuh |
| Sesudahnya | Status Selesai dengan tahap "Ditutup sebelum lengkap", alasan tersimpan, baris tetap berbunyi "Kurang 5 pcs" |
| Stok | Tidak ada satu pun mutasi yang lahir |

### 4.7 Log, kolom, dan layar sempit

- **Log Laravel:** empat galat tercatat hari ini. Satu milik saya dari sesi kemarin
  (`toggledTableColumns`, akibat saya mencoba menyalakan kolom lewat konsol peramban), dan
  tiga sisanya adalah cacat D-24 di bawah, seluruhnya pada pukul 10:07 sebelum perbaikan.
  **Tidak ada satu pun galat setelah perbaikan**, termasuk selama dua penerimaan, pembelian
  langsung, penutupan pesanan, dan seluruh pengujian tagihan.
- **Aturan enam kolom:** daftar pembelian tepat 6 kolom bawaan (tiga lagi tersembunyi), tabel
  Items 5 kolom bawaan (satu tersembunyi), tabel Deliveries Received 6 kolom. Dihitung dua
  kali, dari kode dan dari kepala tabel yang benar benar tergambar.
- **Layar sempit 501 piksel:** halaman tidak meluber ke samping dan tidak ada satu pun elemen
  teks yang terpotong.

## 5. Cacat yang ditemukan dan diperbaiki di ronde ini

**D-24. FATAL. Tombol Receive Goods mematikan seluruh permintaan dengan galat 500.**

Penampung nama barang di dalam repeater diberi nama `nama` dan isinya dibaca dengan
`$get('nama')`. Karena penampung itu sendiri bernama `nama`, ia membaca dirinya sendiri:
mengambil isinya memanggil closure content, closure itu memanggil pengambilan isinya lagi,
dan seterusnya sampai memori PHP habis. Layar tidak menampilkan apa pun kecuali halaman galat.

Diperbaiki dengan memisahkan kunci datanya dari nama penampungnya. Nilainya sekarang dipegang
satu `Hidden`, dan penampungnya bernama `barang` yang hanya membaca. Sesudah perbaikan, kotak
dialog tergambar penuh dengan tiga baris beserta sisa masing masing.

**D-25. Kalimat selisih tagihan menunjukkan angka yang salah sampai halaman dimuat ulang.**

Ini yang paling berbahaya di antara ketiganya, walau tidak mematikan apa pun. Saat saya
menambahkan baris pembebanan senilai Rp 400.000, layar tetap berbunyi *"Tagihan lebih kecil
Rp 268.000 daripada barang yang sudah diterima"*, padahal yang benar adalah *lebih besar
Rp 132.000*. Angkanya bukan sekadar basi, melainkan terbalik arah.

Sebabnya: nilai tagihan dan selisihnya dihitung dari baris yang dikelola relation manager,
tetapi ditampilkan di infolist halaman induk, dan halaman induk adalah komponen Livewire yang
berbeda yang tidak ikut digambar ulang. Ini keluarga cacat yang sama dengan D-17 pada kiriman K.

Kalimat itu ada justru untuk menahan orang menandatangani faktur yang menagih lebih banyak
daripada barang yang datang. Kalimat yang salah arah lebih buruk daripada tidak ada kalimat
sama sekali, karena ia menenangkan orang yang seharusnya bertanya.

Perbaikan pertama saya gagal: `dispatch('$refresh')->to(HalamanInduk::class)` tidak
menyegarkan apa pun, dan itu ketahuan hanya karena saya mengujinya lagi setelah memperbaiki.
Perbaikan yang benar adalah menyiarkan peristiwa biasa dari relation manager dan memasang
pendengar `#[On]` di halaman induknya. Dipasang di dua tempat sekaligus, tagihan rekanan dan
pesanan pembelian, karena keduanya menampilkan angka turunan yang sama sifatnya.

Sesudah perbaikan, mengubah baris dari Rp 268.000 menjadi Rp 400.000 langsung mengubah
kalimatnya dari "Cocok" menjadi "Tagihan lebih besar Rp 132.000", tanpa memuat ulang halaman.

**D-26. Pesanan yang sudah ditutup masih berbunyi "Seluruh pesanan masih ditunggu".**

Bertentangan dengan subjudul halamannya sendiri yang sudah menyatakan pesanan itu ditutup.
Diperbaiki dengan memeriksa keadaan ditutup lebih dulu, dan sekarang berbunyi "Ditutup tanpa
satu pun barang datang. Sisanya tercatat tidak jadi dikirim."

## 6. Satu pertanyaan kebijakan yang perlu Anda jawab

Bukan cacat, tetapi ketahuan justru karena kiriman ini menyambungkan pembelian ke tagihan.

**Anggaran ATK di GAIS menghitung pemakaian, bukan pembelian.** Realisasinya dijumlahkan dari
barang yang **keluar** gudang. Membeli 12 spidol tidak menyentuh anggaran sama sekali;
menyerahkannya ke departemen yang menyentuh.

Akibatnya, faktur yang menagih pembelian ATK tidak bisa dibebankan ke kategori ATK. Pemilih
kategori di layar tagihan memang tidak menampilkannya, dan itu aturan yang sudah berlaku sejak
kiriman K supaya angkanya tidak terhitung dua kali. Pada pengujian ini saya membebankannya ke
"Biaya GA lainnya".

Yang perlu Anda putuskan: apakah itu memang yang Anda inginkan. Ada dua cara yang sama sama
masuk akal, dan keduanya dipakai perusahaan sungguhan:

- **Anggaran mengikuti pemakaian**, seperti sekarang. Departemen dibebani saat memakai barang,
  dan pembelian hanyalah perpindahan uang menjadi persediaan. Lebih adil per departemen,
  tetapi anggaran tidak menahan tim GA yang memborong stok di akhir tahun.
- **Anggaran mengikuti pembelian.** Yang dibebani adalah saat membeli. Lebih mudah dikaitkan
  dengan kas keluar, tetapi departemen yang memakai barang tidak lagi terlihat memakainya.

Saya tidak mengubah apa pun sebelum Anda menjawab, karena mengubahnya berarti mengubah angka
realisasi yang sudah berjalan sejak kiriman J.

## 7. Delivery Gate checklist

**Blok 1, hal yang membatalkan penyerahan**

- Tombol atau tautan yang tidak melakukan apa apa (R-26): tidak ada. Delapan tindakan alur
  sudah dicoba satu per satu di peramban, termasuk yang jarang seperti Close Order.
- Menu menuju halaman yang tidak ada (R-24): tidak ada.
- Angka yang dikarang (R-17, R-18, R-36, R-38): tidak ada. Nilai pesanan, nilai yang diterima,
  dan nilai tagihan ketiganya dijumlahkan saat dibaca dan tidak ada satu pun yang disimpan
  sebagai kolom, sehingga selisih yang ditampilkan tidak pernah bisa basi.

**Blok 2, hal yang perlu diperbaiki sebelum diserahkan**

- Empty state (R-27): ada, dan berbeda menurut keadaannya. Daftar pembelian kosong, daftar
  barang yang masih bisa diisi, daftar barang yang sudah terkunci, dan riwayat penerimaan yang
  berbunyi berbeda untuk draf, menunggu persetujuan, dan menunggu barang.
- Em dash (R-02): tidak ada, diperiksa di seluruh 21 berkas.
- CTA generik (R-15, R-16): tidak ada. Tombolnya Submit for Approval, Record and Receive,
  Receive Goods, Close Order.
- Kartu sendirian di barisnya: formulirnya berkartu tunggal dan sudah `columnSpanFull()`.

**Blok 3, hal yang dicatat tetapi tidak menghalangi**

- Urutan baris di kotak Receive Goods mengikuti urutan basis data, bukan urutan tabel Items.
  Tidak menyesatkan karena tiap baris membawa nama barangnya, tetapi berbeda urutan dengan
  tabel di bawahnya.
- Tap target bawaan Filament di bawah 44 piksel, sama seperti dicatat di gate 14.

## 8. Yang belum diuji

- **Dua orang menerima kiriman yang sama pada saat bersamaan.** Pemeriksaan kelebihan terima
  berjalan tepat sebelum penyimpanan, tetapi menirukan dua peramban yang menekan tombol dalam
  detik yang sama tidak saya lakukan.
- **Pembatalan transaksi saat penerimaan gagal di tengah.** Pembungkus `DB::transaction()` ada,
  tetapi membuktikannya perlu menyuntikkan kegagalan buatan yang tidak boleh ikut terkirim.
- **Pemisahan izin approve dan receive dari sudut pandang pengguna yang hanya punya salah
  satu.** Seluruh pengujian dijalankan sebagai administrator yang memegang keduanya.

## 9. Data uji yang tertinggal di basis data Anda

**Yang inert dan tidak mengganggu apa pun:**

- TG/2026/09/0003 sudah **dibatalkan**, jadi tidak menunggu tanda tangan siapa pun dan tidak
  masuk realisasi anggaran mana pun.
- PP/2026/09/0003 ditutup tanpa pernah menerima barang, jadi tidak menyentuh stok sama sekali.

**Yang benar benar mengubah stok Anda:**

| Barang | Bertambah | Stok sekarang |
|---|---|---|
| Spidol papan tulis hitam | 12 pcs | 12 pcs, sebelumnya 0 |
| Teh celup kotak isi 25 | 10 box | 10 box, sebelumnya 0 |
| Kertas struk kasir | 12 roll | 20 roll, sebelumnya 8 |
| Pembersih lantai | 6 botol | 9 botol, sebelumnya 3 |
| Tinta printer warna | 4 botol | 8 botol, sebelumnya 4 |

Harga pembelian terakhir kelima barang itu juga ikut terisi dari harga pesanan uji, dan angka
itu dipakai menilai persediaan di dasbor. Harganya saya karang untuk keperluan pengujian dan
bukan harga sungguhan dari rekanan Anda.

Seperti pada gate sebelumnya, saya sengaja tidak membuat mutasi koreksi untuk mengembalikannya,
karena itu berarti menulis barang keluar yang tidak pernah terjadi. Kalau Anda ingin angkanya
bersih, cara yang jujur adalah menghapus ketiga pembelian uji beserta penerimaan dan mutasinya
lewat basis data, lalu mengosongkan kembali kolom harga terakhir kelima barang itu.

## 10. Berkas yang dikirim

21 berkas pada ronde pertama, lalu lima berkas dikirim ulang dalam tiga ronde perbaikan untuk
D-24, D-25, dan D-26. Seluruhnya lolos `php -l`, dan empat berkas terbesar diambil kembali dari
mesin lalu dibandingkan isinya.

Baru: empat migrasi tabel dan satu migrasi kolom, empat model, satu resource dengan tiga
halaman dan dua relation manager.

Diubah: `Module.php` (aksi `receive`), `VendorBill.php` (relasi pesanan dan perhitungan
selisih), `VendorBillResource.php` (pemilih pesanan, kalimat selisih di infolist dan di kotak
persetujuan), `ViewVendorBill.php` dan relation manager barisnya (pendengar penyegaran),
serta tiga seeder.

## 11. Kiriman berikutnya

Kiriman O, opname ATK per periode yang bisa dikunci, dan penyesuaian stok sebagai dokumen
tersendiri. Setelah itu Tahap 7 selesai dan siklus ATK tertutup dari permintaan sampai opname.
