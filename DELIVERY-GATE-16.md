# Delivery Gate 16: Kiriman O, opname ATK dan penyesuaian stok

Tahap 7, kiriman ketiga dan penutup. Diverifikasi 8 September 2026 di
`http://127.0.0.1:8000` memakai akun `test@gais.test`.

**Hasil: LULUS**, dengan empat cacat yang ditemukan dan diperbaiki di ronde ini, satu di
antaranya membuat sebuah tombol tidak melakukan apa yang dijanjikan namanya. Bagian 5 juga
memuat satu cacat lama di luar kiriman ini yang ketahuan justru saat menyiapkan penomorannya.
Bagian 9 berisi data uji yang tertinggal di basis data Anda.

---

## 1. Design Read

Saya baca ini sebagai **lembar hitungan gudang untuk staf GA yang berdiri di depan rak, dan
lembar pertanggungjawaban untuk manajer yang menandatangani penggeseran angka**. Dua pembaca,
dua saat, satu halaman. Gaya visual mengikuti opname aset yang sudah ada sejak kiriman D.
Dial ENERGY rendah, RHYTHM mengikuti tabel yang sudah ada, MOTION nol.

Yang membedakannya dari kiriman lain: di halaman ini ada **tiga angka yang mudah dikira satu**,
dan hampir seluruh keputusan di bawah lahir dari kewajiban memisahkan ketiganya.

| Angka | Artinya | Kapan berubah |
|---|---|---|
| `system_quantity` | Stok menurut catatan **saat daftar disusun** | Dibekukan sekali, tidak pernah berubah lagi |
| Stok sekarang | Stok menurut catatan **saat ini**, dijumlahkan ulang dari mutasi | Setiap ada barang masuk atau keluar |
| Hitungan fisik | Apa yang benar benar ada di rak | Saat orang mengetiknya |

## 2. Alasan satu baris tiap keputusan (R-31)

- **Warna:** hijau untuk Cocok, kuning untuk lebih, merah untuk kurang, abu abu untuk belum
  dihitung. Kuning dan merah dibedakan karena barang yang kurang adalah kehilangan yang perlu
  dijelaskan, sedangkan barang yang lebih biasanya hanya salah catat.
- **Layout:** dua kartu infolist, keadaan sesi dan jejak penghitungan, ditambah satu kalimat
  peringatan selebar halaman yang hanya muncul kalau memang ada yang perlu diwaspadai.
- **Tipografi:** nomor dokumen dan kode barang memakai huruf lebar tetap, sama seperti seluruh
  nomor lain di aplikasi ini.
- **Spacing:** mengikuti panel, tidak ada penyesuaian.
- **Kartu:** ketiga angka ditaruh berdampingan sebagai tiga kolom, bukan dua kolom dengan
  selisih diserahkan ke kepala pembacanya. Pengurangan di kepala sambil berdiri di depan rak
  adalah tempat kesalahan opname bermula.
- **Ilustrasi:** tidak ada.

## 3. Yang dibangun

Tiga keputusan Anda pada 8 September 2026, dan bentuknya di kode:

| Keputusan | Bentuknya di aplikasi |
|---|---|
| Koreksi langsung boleh, tetapi wajib beralasan | Label dan kewajiban isi kolom catatan berubah sendiri jadi "Alasan koreksi" begitu jenis mutasi dipilih koreksi tambah atau koreksi kurang |
| Sesi bebas, seperti opname aset | Tidak ada periode bulanan. Sesi dibuat kapan saja, dengan cakupan kategori dan lokasi |
| Penyesuaian perlu tindakan terpisah berizin | Izin baru `adjust`, dipisah dari `update`. Staf GA menyusun, menghitung, dan menutup sesi, manajer yang menggeser angka gudang |

Alurnya lima langkah, dan **stok tidak tersentuh sama sekali sampai langkah kelima**:

```
Draf -> Susun daftar target -> Mulai menghitung -> Selesai dihitung -> Terapkan penyesuaian
                                                                       (baru di sini stok berubah)
```

**Penyesuaian melahirkan mutasi, bukan menimpa angka.** Tiap baris yang selisih menghasilkan
satu mutasi `koreksi_tambah` atau `koreksi_kurang` yang membawa nomor sesinya, hitungan
fisiknya, dan angka catatannya di dalam keterangan. Barang yang tidak selisih dan barang yang
tidak jadi dihitung tidak melahirkan apa apa.

**Selisih dihitung terhadap stok sekarang, bukan terhadap angka beku.** Ini keputusan yang
paling mudah salah dan paling mahal akibatnya. Kalau selisih dihitung terhadap angka beku,
setiap barang yang keluar gudang di sela sela penghitungan akan ikut terkoreksi balik, dan
penyerahan yang sah terhapus dari buku stok tanpa jejak. Yang dilakukan sebagai gantinya:
angka beku dipakai untuk hal lain, yaitu mendeteksi bahwa barangnya sempat bergerak, lalu
keadaan itu **disebutkan di layar, tidak diperbaiki diam diam**.

## 4. Bukti verifikasi (R-35)

Seluruh angka di bawah dibaca dari layar dan dihitung ulang dengan tangan.

### 4.1 Satu sesi penuh, OP/2026/09/0001

Cakupan kategori Alat tulis, daftar berisi 9 barang. Delapan dihitung, satu sengaja
ditinggalkan untuk membuktikan bahwa barang yang tidak dihitung tidak ikut dikoreksi.

| Barang | Stok saat dihitung | Hitungan fisik | Selisih |
|---|---|---|---|
| Isi stapler nomor 10 | 38 box | 38 box | Cocok |
| Lakban bening 2 inci | 36 roll | 34 roll | Kurang 2 roll |
| Map plastik berkancing | 100 pcs | 103 pcs | Lebih 3 pcs |
| Ordner arsip folio | 24 pcs | 24 pcs | Cocok |
| Pensil kayu 2B | 66 pcs | **tidak dihitung** | tidak dihitung |
| Pulpen tinta biru 0,5 mm | 180 pcs | 180 pcs | Cocok |
| Pulpen tinta hitam 0,5 mm | 30 pcs | 28 pcs | Kurang 2 pcs |
| Spidol papan tulis hitam | 8 pcs | 8 pcs | Cocok |
| Stapler ukuran sedang | 14 pcs | 14 pcs | Cocok |

Spidol papan tulis hitam sempat berangka lain. Daftarnya membekukannya pada 12 pcs, lalu di
tengah penghitungan sengaja dibuat koreksi langsung 4 pcs untuk menguji dua hal sekaligus,
yaitu kewajiban alasan dan peringatan stok bergerak. Layar lalu menyuruh menghitungnya ulang,
dan setelah dihitung ulang pada 8 pcs barisnya berbunyi Cocok. Inilah bukti bahwa selisih
memang dihitung terhadap stok terbaru: kalau ia dihitung terhadap angka beku 12, sistem akan
menyimpulkan barangnya kurang 4 dan menambahkan koreksi kedua atas kekurangan yang sudah
tercatat sekali.

Setelah penyesuaian diterapkan, di daftar Supply Movements muncul **tepat tiga** mutasi
koreksi, tidak lebih:

| Nomor mutasi | Barang | Jenis | Jumlah |
|---|---|---|---|
| MP/2026/09/0168 | Lakban bening 2 inci | Koreksi kurang | 2 roll |
| MP/2026/09/0169 | Map plastik berkancing | Koreksi tambah | 3 pcs |
| MP/2026/09/0170 | Pulpen tinta hitam 0,5 mm | Koreksi kurang | 2 pcs |

Tidak ada mutasi untuk lima barang yang cocok, dan tidak ada mutasi untuk Pensil kayu 2B yang
tidak jadi dihitung. Sesudahnya seluruh baris yang dihitung berbunyi Cocok, karena catatan dan
rak memang sudah sama.

### 4.2 Kemajuan hitungan yang ikut bergerak tanpa memuat ulang

Kalimat "8 dari 9 barang" dan "0 dari 8 yang sudah dihitung" berada di infolist halaman induk,
sedangkan yang mengubahnya adalah tombol di relation manager, yaitu komponen Livewire yang
berbeda. Ini keluarga cacat D-17 dan D-25 pada kiriman K dan N.

Dipasang sejak awal di kiriman ini, bukan setelah ketahuan: relation manager menyiarkan
peristiwa biasa, halaman induk memasang pendengar `#[On]`. Diuji dengan mencatat hitungan satu
per satu dan mengamati angkanya naik sendiri, tanpa memuat ulang halaman.

### 4.3 Kewajiban alasan pada koreksi langsung

Jawaban Anda "boleh, tapi wajib alasan" diuji dengan benar benar mencoba melanggarnya.

| Yang diperiksa | Hasil |
|---|---|
| Label berubah | Memilih jenis Koreksi kurang mengubah label kolom dari "Catatan" menjadi "Alasan koreksi", lengkap dengan tanda wajib |
| Penjaganya nyata | Menyimpan tanpa mengisinya ditolak dengan pesan "alasan koreksi wajib diisi." |
| Jenis lain tidak terganggu | Barang masuk dan barang keluar tetap boleh disimpan tanpa catatan |

### 4.4 Peringatan stok bergerak, diuji dari kedua arah

Ini bagian yang paling banyak menghabiskan waktu ronde ini, dan pantas.

**Arah pertama, peringatannya muncul saat memang perlu.** Sesi OP/2026/09/0003 disusun,
daftarnya membekukan gula pasir pada 8 kg, lalu penghitungan dimulai. Sesudah itu sengaja
dibuat mutasi 2 kg untuk barang yang sama. Layar langsung berbunyi di tiga tempat sekaligus:

- Kepala halaman: "1 barang stoknya berubah setelah daftar ini disusun..."
- Baris barangnya: "10 kg" dengan keterangan "Bertambah 2 kg sejak daftar disusun", berwarna kuning
- Kotak Record Count barang itu: "Perhatikan, bertambah 2 kg sejak daftar disusun, jadi
  pastikan Anda menghitungnya setelah itu."

**Arah kedua, peringatannya diam saat tidak ada yang perlu diperingatkan.** Diuji pada sesi
yang sudah disesuaikan dan pada sesi yang dibatalkan. Keduanya diam, dan uraiannya ada di
cacat D-27 di bawah.

### 4.5 Mutasi koreksi bisa ditemukan kembali dari nomor sesinya

Mencari `OP/2026/09/0001` di daftar Supply Movements menghasilkan **tepat tiga baris**, yaitu
ketiga koreksi sesi itu. Koreksi langsung 4 spidol yang dibuat terpisah pada hari yang sama
tidak ikut terjaring, karena ia memang tidak lahir dari sesi mana pun.

Tombol Open Correction Movement pada tiap baris juga dibuka satu per satu. Menekannya pada
baris Map plastik membuka daftar mutasi yang tersaring pada `MP/2026/09/0169`, satu hasil,
barang yang benar, koreksi tambah 3 pcs. Sebelum perbaikan D-29 tombol ini membuka seluruh
170 mutasi tanpa saring.

### 4.6 Log, kolom, dan layar sempit

- **Log Laravel:** tidak ada satu pun galat baru dari aplikasi sepanjang ronde ini. Galat
  terakhir yang tercatat pukul 11:45 adalah milik saya sendiri, yaitu percobaan memanggil
  metode Livewire yang tidak ada dari konsol peramban saat mencari daftar pilihan barang.
  Galat pukul 10:07 adalah cacat D-24 kiriman N yang sudah diperbaiki dan sudah dilaporkan.
- **Aturan enam kolom:** daftar opname tepat 6 kolom bawaan, dihitung dari kepala tabel yang
  benar benar tergambar, yaitu Nomor, Nama sesi, Kemajuan, Selisih, Status, Dibuat. Tiga kolom
  lagi tersembunyi. Lembar hitungan 5 kolom bawaan, satu tersembunyi.
- **Layar sempit 375 piksel:** halaman tidak meluber ke samping sama sekali (`scrollWidth`
  sama dengan `clientWidth`), tabel bergulir di dalam wadahnya sendiri, dan tidak ada teks
  yang terpotong.

## 5. Cacat yang ditemukan dan diperbaiki

**D-27. Peringatan stok bergerak menuduh sesi itu sendiri, lalu masih bersuara pada sesi yang
sudah tidak ada gunanya diperingatkan.**

Ditemukan dua kali dalam ronde ini, dan yang kedua justru muncul karena perbaikan pertama saya
terlalu sempit.

Mula mula: sesudah penyesuaian diterapkan, kepala halaman berbunyi "4 barang stoknya berubah
setelah daftar ini disusun". Angka itu adalah koreksi sesi itu sendiri. Lembar hitungan yang
baru saja selesai mengabarkan bahwa dirinya patut dicurigai, dan menyuruh pembacanya
memastikan sesuatu yang sudah lewat.

Perbaikan pertama saya menyembunyikan kepala halamannya saja. Saat diperiksa ulang, ternyata
keterangan di **tiap barisnya** masih berbunyi "Berkurang 2 roll sejak daftar disusun",
lengkap dengan warna kuning, pada sesi yang sudah beres. Cacat yang sama, satu tingkat di
bawah, dan lolos karena saya memperbaiki tempat yang saya lihat, bukan aturannya.

Lalu ketahuan hal ketiga saat menguji sesi yang dibatalkan: peringatan itu tetap muncul di
sana juga. Kalimatnya berbunyi "pastikan hitungan fisiknya diambil setelah barang itu
bergerak", padahal pada sesi yang dibatalkan tidak akan pernah ada hitungan yang dipakai.

Perbaikan akhirnya memindahkan syaratnya ke satu tempat,
`SupplyOpname::pergerakanPerluDiwaspadai()`, yang dibaca `SupplyOpnameLine::stokBergerak()`.
Kepala halaman, warna baris, keterangan baris, dan kalimat di kotak Record Count sekarang
diam bersama sama atau bersuara bersama sama, karena semuanya bertanya pada satu tempat yang
sama. Syarat yang tadinya saya tulis ulang di berkas resource sengaja dihapus dari sana:
aturan yang ditulis di dua tempat pada suatu hari akan berbeda pendapat.

**D-28. Nomor dokumen asal tidak bisa dicari di daftar mutasi.**

Mencari `OP/2026/09/0001` mengembalikan seluruh 170 baris tanpa tersaring, karena kolom
`reference` belum `->searchable()`. Padahal kolom itulah satu satunya jalan menelusuri mutasi
kembali ke dokumen yang melahirkannya. Diperbaiki, dan kolomnya sekalian diberi label
"Dokumen asal" supaya isinya terbaca sebagai jejak, bukan sebagai catatan bebas.

**D-29. Tombol Open Correction Movement membuka seluruh buku stok, bukan mutasi yang dimaksud.**

Alamat yang dibuat tombol itu memakai kunci `tableSearch`, mengikuti nama properti Livewire
yang memang bernama begitu. Tetapi Filament menuliskan pencarian tabel ke alamat dengan nama
pendek `search`, dan hanya nama pendek itu yang dibaca kembali saat halaman dibuka. Akibatnya
alamatnya sah, halamannya terbuka, dan tidak ada satu pun pesan galat, tetapi yang tergambar
adalah seluruh 170 mutasi. Orang yang menekan tombol berlabel "buka mutasi koreksinya"
mendarat di seluruh isi buku stok dan harus mencarinya sendiri.

Ketahuan bukan dari membaca kode, melainkan karena saya menempelkan alamat itu ke peramban dan
menghitung jumlah barisnya. Diperbaiki menjadi `search`, lalu diuji ulang: satu hasil, barang
yang benar.

**D-30. Nomor permintaan barang bertabrakan dengan nomor permintaan perbaikan.**

Cacat lama di luar kiriman ini, ketahuan saat mendaftarkan awalan nomor opname dan memeriksa
seluruh awalan yang sudah ada.

Permintaan barang pada kiriman M memakai awalan `PB`, padahal `PB` sudah dipakai permintaan
perbaikan sejak kiriman G. Keduanya urutan yang terpisah, jadi `PB/2026/09/0001` bisa lahir
dua kali untuk dua dokumen yang sama sekali berbeda, dan orang yang menyebut nomor itu di
telepon tidak akan pernah tahu yang mana yang dimaksud.

Awalan permintaan barang diubah menjadi `PM`. **Permintaan yang terlanjur bernomor `PB` tetap
memakai nomor lamanya**, karena nomor dokumen adalah penanda tetap yang tidak boleh berubah
setelah disebut orang. Yang berubah hanya nomor berikutnya. Seluruh 14 awalan lain sudah
diperiksa dan tidak ada lagi yang bertabrakan.

## 6. Yang sengaja tidak diikuti dari opname aset

Opname aset sejak kiriman D memakai izin `approve` untuk konsep yang di sini bernama `adjust`.
Keduanya tidak saya samakan, dan itu pilihan sadar. `approve` di seluruh aplikasi berarti
menyetujui pengajuan orang lain, sedangkan yang terjadi di sini adalah menggeser angka gudang.
Menyamakan namanya akan membuat pemberian izin di layar Role menyesatkan.

Akibatnya ada satu ketidakseragaman yang saya catat, bukan sembunyikan: dua modul opname
memakai nama izin yang berbeda untuk tindakan yang mirip. Merapikannya berarti mengubah izin
yang mungkin sudah Anda atur di layar Role, jadi saya tidak melakukannya tanpa Anda minta.

## 7. Delivery Gate checklist

**Blok 1, hal yang membatalkan penyerahan**

- Tombol atau tautan yang tidak melakukan apa apa (R-26): tidak ada **sekarang**. Ada satu
  sebelum ronde ini, yaitu D-29, dan itu ditemukan justru karena tiap tombol ditekan satu per
  satu dan hasilnya dihitung, bukan sekadar dilihat apakah halamannya terbuka.
- Menu menuju halaman yang tidak ada (R-24): tidak ada. Modul sudah didaftarkan di
  `ModuleSeeder`, izinnya disinkronkan, dan ditambahkan ke `RoleSeeder` untuk Manajer GA dan
  Staf GA dengan pembagian `adjust` yang berbeda.
- Angka yang dikarang (R-17, R-18, R-36, R-38): tidak ada. Stok sekarang selalu dijumlahkan
  ulang dari mutasi saat dibaca, dan satu satunya angka yang disimpan adalah angka beku yang
  memang harus tidak berubah beserta hitungan fisik yang diketik orang.

**Blok 2, hal yang perlu diperbaiki sebelum diserahkan**

- Empty state (R-27): ada, dan berbeda menurut keadaan sesinya. Daftar kosong pada sesi draf
  menyuruh menyusun daftar dan menyebut bahwa stok akan ikut dibekukan, pada sesi yang
  dibatalkan berbunyi lain, dan pada sesi berjalan tanpa baris berbunyi bahwa itu tidak
  seharusnya terjadi dan sesinya sebaiknya dibuat ulang.
- Em dash (R-02): tidak ada, diperiksa di seluruh berkas kiriman ini.
- CTA generik (R-15, R-16): tidak ada. Tombolnya Build Target List, Start Counting, Record
  Count, Clear Count, Finish Counting, Apply Adjustment, Cancel Session.
- Kartu sendirian di barisnya: formulirnya berkartu tunggal dan sudah `columnSpanFull()`.

**Blok 3, hal yang dicatat tetapi tidak menghalangi**

- **Tap target di bawah 44 piksel (R-03).** Gate 14 dan 15 menjawab ini dengan "mengikuti
  komponen Filament". Ronde ini saya mengukurnya, bukan mengasumsikannya: pada lebar 375
  piksel, tombol ikon di dalam tabel berukuran 32x32 piksel, dan tombol bawaan panel seperti
  Pilih kolom serta Menu pengguna berukuran 32 sampai 36 piksel. Jadi jawaban jujurnya adalah
  **aturan ini belum terpenuhi**, di seluruh aplikasi, bukan hanya di kiriman ini.
  Memperbaikinya berarti menyetel ulang komponen ikon Filament secara global, dan itu pekerjaan
  tersendiri yang menyentuh setiap layar.
- Keterangan pergerakan di tiap baris tetap muncul pada sesi berstatus draf, sedangkan kepala
  halamannya tidak. Disengaja: pada sesi draf daftarnya masih boleh disusun ulang, jadi
  peringatan sebesar kepala halaman berlebihan, tetapi keterangan di barisnya tetap berguna.

## 8. Yang belum diuji

- **Dua orang menghitung sesi yang sama pada saat bersamaan.** Pencatatan hitungan per baris
  aman karena tiap baris berdiri sendiri, tetapi dua orang yang menekan Apply Adjustment dalam
  detik yang sama tidak saya tirukan.
- **Pembatalan transaksi saat penyesuaian gagal di tengah.** Pembungkus `DB::transaction()`
  ada, tetapi membuktikannya perlu menyuntikkan kegagalan buatan yang tidak boleh ikut
  terkirim.
- **Sudut pandang pengguna yang hanya punya `update` tanpa `adjust`.** Seluruh pengujian
  dijalankan sebagai administrator yang memegang keduanya, jadi yang terbukti baru bahwa
  izinnya terdaftar dan terbagi di seeder, bukan bahwa tombolnya benar benar hilang bagi staf.
- **Cakupan per lokasi.** Yang diuji cakupan per kategori. Cakupan lokasi memakai jalur kode
  yang sama tetapi tidak saya jalankan sendiri.

## 9. Data uji yang tertinggal di basis data Anda

**Yang benar benar mengubah stok Anda:**

| Barang | Perubahan | Sebabnya |
|---|---|---|
| Lakban bening 2 inci | 36 → 34 roll | Koreksi hasil opname OP/2026/09/0001 |
| Map plastik berkancing | 100 → 103 pcs | Koreksi hasil opname OP/2026/09/0001 |
| Pulpen tinta hitam 0,5 mm | 30 → 28 pcs | Koreksi hasil opname OP/2026/09/0001 |
| Spidol papan tulis hitam | 12 → 8 pcs | Koreksi langsung, dibuat untuk menguji kewajiban alasan |

**Yang berubah lalu dikembalikan:**

Gula pasir kemasan 1 kilogram turun 2 kg lalu naik 2 kg lagi, jadi stoknya kembali 10 kg
seperti semula. Keduanya dibuat untuk menguji peringatan stok bergerak. Buku stok memuat kedua
mutasi itu dan keduanya berketerangan `[DATA UJI]`, karena mutasi tidak saya hapus: buku stok
yang barisnya bisa dihapus bukan buku stok.

**Yang inert dan tidak mengganggu apa pun:**

- OP/2026/09/0002 dan OP/2026/09/0003, keduanya bernama berawalan `[DATA UJI]` dan keduanya
  **dibatalkan**, jadi tidak menunggu tindakan siapa pun dan tidak pernah menyentuh stok.

Kalau Anda ingin angkanya bersih, cara yang jujur adalah menghapus kedua sesi uji itu beserta
kelima mutasi di atas lewat basis data, lalu memeriksa ulang stok kelima barangnya. Saya tidak
membuat mutasi pembalik untuk keempat yang pertama, karena itu berarti menulis barang masuk
dan keluar yang tidak pernah terjadi.

## 10. Berkas yang dikirim

14 berkas pada ronde pertama, lalu 2 berkas pada perbaikan pertama, 4 pada perbaikan kedua,
dan 3 pada perbaikan ketiga. Seluruhnya lolos `php -l`.

Baru: dua migrasi tabel, dua model, satu resource dengan tiga halaman dan satu relation
manager.

Diubah: `Module.php` (aksi `adjust`), `SupplyTransactionResource.php` (kewajiban alasan pada
koreksi, kolom Dokumen asal yang bisa dicari), `ModuleSeeder.php`, `RoleSeeder.php`, dan
`NumberSequenceSeeder.php` (awalan `OP`, serta perbaikan tabrakan `PB` menjadi `PM`).

## 11. Tahap 7 selesai

Siklus ATK sekarang tertutup dari ujung ke ujung, dan tiap sambungannya sudah dibuktikan
dengan hitungan tangan:

```
Karyawan meminta -> Atasan menyetujui -> GA menyerahkan   (stok turun, anggaran terpakai)
GA memesan -> Manajer menyetujui -> Barang datang          (stok naik, harga terakhir terisi)
Rekanan menagih -> Selisih terhadap barang yang datang     (tagihan, anggaran)
GA menghitung fisik -> Manajer menyesuaikan                (stok cocok dengan raknya)
```

**Satu pertanyaan kebijakan dari gate 15 masih menunggu jawaban Anda:** anggaran ATK sekarang
menghitung pemakaian, bukan pembelian, sehingga faktur pembelian ATK tidak bisa dibebankan ke
kategori ATK. Uraiannya ada di bagian 6 `DELIVERY-GATE-15.md`. Saya tidak mengubah apa pun
sebelum Anda menjawab, karena mengubahnya berarti mengubah angka realisasi yang sudah berjalan
sejak kiriman J.

Yang tersisa di luar Tahap 7 dan sudah tercatat di `ROADMAP.md`: laporan anggaran versus
realisasi yang bisa diunduh (Tahap 5), Tahap 6 yang menunggu daftar nomor akun dari keuangan,
serta Tahap 8 sampai 10 yang usulnya ada di `ROADMAP-REFERENSI.md`.
