# Delivery Gate 14: Kiriman M, permintaan pemakaian ATK

Tahap 7, kiriman pertama. Diverifikasi 8 September 2026 di `http://127.0.0.1:8000`
memakai akun `test@gais.test`.

**Hasil: LULUS**, dengan satu cacat yang ditemukan dan diperbaiki di ronde ini, dan dengan
catatan data uji di bagian 8 yang perlu Anda baca.

---

## 1. Design Read

Saya baca ini sebagai **layar alur persetujuan bertahap untuk tiga kelompok pembaca yang
berbeda**: karyawan yang bertanya "permintaan saya sudah sampai mana", kepala departemen yang
bertanya "mana yang menunggu tanda tangan saya", dan tim GA yang bertanya "mana yang bisa saya
serahkan hari ini". Gaya visual mengikuti tiga modul alur yang sudah ada dan tidak
memperkenalkan bahasa visual baru. Dial ENERGY rendah, RHYTHM mengikuti tabel yang sudah ada,
MOTION nol.

Satu hal membedakannya dari tiga modul alur sebelumnya, dan itu yang menentukan hampir semua
keputusan di bawah: langkah terakhir di sini bukan penandaan, melainkan perubahan angka.
Menyerahkan barang mengurangi stok, dan stok yang berkurang langsung menjadi realisasi
anggaran ATK. Karena itu setiap tombol menyebutkan apa yang akan terjadi sebelum ditekan.

## 2. Alasan satu baris tiap keputusan (R-31)

- **Warna:** tidak ada warna baru. Status memakai lencana yang sama dengan penggantian biaya,
  dan satu satunya warna tambahan adalah merah pada kolom Stok sekarang saat stoknya kurang,
  karena itu satu satunya keadaan di layar ini yang menghalangi pekerjaan.
- **Layout:** satu kartu formulir dengan `columnSpanFull()`, sesuai aturan kartu tunggal, dan
  dua kartu infolist yang memisahkan isi permintaan dari jejak persetujuan dan penyerahan.
- **Tipografi:** nomor dokumen memakai huruf lebar tetap seperti seluruh nomor dokumen lain.
- **Spacing:** mengikuti panel, tidak ada penyesuaian.
- **Kartu:** dua kartu infolist, bukan tiga, karena persetujuan dan penyerahan adalah satu
  cerita berurutan dan memisahkannya membuat orang membaca bolak balik.
- **Ilustrasi:** tidak ada. Empty state memakai kalimat yang menyebut apa yang harus
  dilakukan, bukan gambar.

## 3. Yang dibangun

Alur tiga langkah dengan tiga orang:

```
Karyawan meminta  ->  Atasan menyetujui  ->  Tim GA menyerahkan barangnya
     draft              diajukan                    disetujui
                                                        |
                                                   diserahkan
```

Keputusan pemilik proyek pada 8 September 2026, dan bagaimana masing masing jatuh di kode:

| Keputusan | Bentuknya di aplikasi |
|---|---|
| Pemohon dibedakan izin | Izin baru `supply_requests.request_for_others`. Tanpa izin itu, pilihan pemohon terkunci pada diri sendiri |
| Persetujuan atasan lalu tim GA | Sama dengan penggantian biaya, tetapi hanya dua keadaan yang melewati atasan, bukan tiga |
| Pembelian ATK dua cara | Ditunda ke Kiriman N. Tidak ada bagian dari keputusan itu yang mendahului di kiriman ini |

Hanya dua keadaan yang melewati persetujuan atasan, karena departemen wajib diisi pada
permintaan barang, sehingga keadaan "tidak dibebankan ke departemen mana pun" yang ada di
penggantian biaya tidak pernah bisa terjadi di sini.

**Dua kolom jumlah, dan itu disengaja.** `quantity_requested` ditulis pemohon dan berhenti
bisa diubah begitu diajukan. `quantity_issued` ditulis tim GA saat barangnya keluar lemari.
Kejadian paling sering di gudang ATK adalah diminta sepuluh, ada tujuh, diserahkan tujuh.
Kalau angkanya ditimpa, tiga sisanya hilang dari catatan, padahal justru itu angka yang paling
berguna saat menyusun pesanan berikutnya.

**Penyerahan dibungkus satu transaksi basis data.** Kalau baris kelima gagal, empat mutasi
sebelumnya ikut batal. Tanpa itu, kegagalan di tengah meninggalkan gudang yang catatannya
tidak sama dengan isinya, dan tidak ada layar yang bisa menjelaskan keadaan itu kepada orang.

## 4. Bukti verifikasi (R-35)

Seluruh angka di bawah dibaca dari layar dan dihitung ulang dengan tangan.

### 4.1 Jalur yang melewati atasan, PB/2026/09/0001

Departemen Finance belum punya kepala saat permintaan ini dibuat.

| Yang diperiksa | Yang muncul di layar |
|---|---|
| Keterangan jalur di formulir | "Langsung ke tim GA untuk diserahkan. Alasannya: departemen yang dibebani belum punya kepala departemen." |
| Departemen terisi sendiri | Memilih Dewi Anggraini mengisi Finance tanpa diketik |
| Nomor dokumen | PB/2026/09/0001 |
| Pilihan barang membawa stok | 26 barang, contohnya "Kertas HVS A4 80 gram (ATK-0001, stok 48 rim)" |
| Penjaga barang ganda | Tiga barang yang sudah ada di daftar dimatikan di pilihan, satu barang yang belum ada tetap bisa dipilih |
| Kotak dialog ajukan | "3 jenis barang. Langsung ke tim GA, karena departemen yang dibebani belum punya kepala departemen. Hari ini 1 barang stoknya belum cukup, dan itu tidak menghalangi pengajuan. Tim GA yang memutuskan berapa yang bisa diserahkan. Setelah diajukan, daftar barangnya tidak bisa diubah lagi." |
| Setelah diajukan | Status langsung "Menunggu diserahkan", alasan lompatan tertulis di kartu persetujuan, tombol Add Item hilang |

### 4.2 Penolakan penyerahan saat stok kurang

Baris ketiga meminta 6 pcs Spidol papan tulis hitam yang stoknya 0.

| Yang diperiksa | Yang muncul di layar |
|---|---|
| Subjudul halaman | "Menunggu tim GA. Belum bisa diserahkan penuh: Spidol papan tulis hitam diminta 6 pcs, stok tinggal 0 pcs." |
| Kotak dialog Issue Supplies | "Belum bisa diserahkan seluruhnya. Spidol papan tulis hitam diminta 6 pcs, stok tinggal 0 pcs. Ubah jumlah serah barang itu di daftar Requested Items lebih dulu, atau tambah stoknya dari menu Supply Movements." |
| Saat tombol tetap ditekan | Pemberitahuan "Stok belum cukup" dengan barang dan angkanya, status tidak berubah |
| Menyetel jumlah serah 3 pcs | Ditolak: "Melebihi stok. Stok Spidol papan tulis hitam tinggal 0 pcs. Turunkan jumlahnya, atau tambah stoknya dari menu Supply Movements lebih dulu." |
| Menyetel jumlah serah 0 | Tersimpan: "Spidol papan tulis hitam akan diserahkan 0 pcs" |

Penjaga stok memang diperiksa dua kali dengan sengaja: sekali saat kotak dialog digambar
supaya enak dibaca, dan sekali lagi tepat sebelum disimpan supaya dua orang yang membuka
layar bersamaan tidak sama sama mengeluarkan barang yang sama.

### 4.3 Aritmetika stok, dihitung tangan

| Barang | Stok sebelum | Diserahkan | Stok sesudah | Hitungan |
|---|---|---|---|---|
| Kertas HVS A4 80 gram | 48 rim | 5 rim | 43 rim | 48 − 5 = 43 |
| Kertas struk kasir | 12 roll | 4 roll | 8 roll | 12 − 4 = 8 |
| Spidol papan tulis hitam | 0 pcs | 0 pcs | 0 pcs | tidak ada mutasi |

Dibaca ulang dari layar Supply Items, bukan hanya dari layar permintaan, dan angkanya cocok.

Di daftar Supply Movements muncul **tepat dua** mutasi baru bertanggal 8 September 2026,
keduanya "Barang keluar", keduanya dibebankan ke Finance, bernilai −5 rim dan −4 roll. Tidak
ada mutasi ketiga untuk baris yang jumlah serahnya nol, sesuai rancangan.

Kertas struk kasir turun ke 8 roll dengan batas minimum 12 roll, dan penanda keadaannya
berubah sendiri menjadi "Perlu dipesan". Barang itu lalu muncul di daftar Items to Reorder
di dasbor dengan keterangan kurang 4 roll.

### 4.4 Jalur yang melewati atasan, PB/2026/09/0002

Kepala departemen Finance disetel sementara ke Rizal Hakim untuk menguji cabang ini.

| Yang diperiksa | Yang muncul di layar |
|---|---|
| Keterangan jalur di formulir | "Menunggu persetujuan Rizal Hakim, lalu tim GA menyerahkan barangnya." |
| Setelah diajukan | Status "Menunggu atasan", tahap "Di meja Rizal Hakim" |
| Tombol yang muncul | Approve as Supervisor, Reject, Cancel. Issue Supplies **tidak** muncul, karena belum disetujui |
| Kotak dialog setujui | "1 jenis barang untuk Dewi Anggraini. Yang Anda setujui adalah bahwa barangnya memang dibutuhkan. Stok belum berkurang sekarang, dan baru berkurang saat tim GA menyerahkannya." |
| Setelah disetujui dan diserahkan | "Disetujui Rizal Hakim pada 08 September 2026, 06:11. Catatan: Setuju, kertas memang habis di ruang keuangan" |
| Stok | 43 rim → 40 rim, sesuai 3 rim yang diminta |

### 4.5 Realisasi anggaran, dibuktikan sampai rupiah terakhir

Ini pembuktian terpenting di gate ini, karena inilah alasan Kiriman M ada.

Satu pagu uji dibuat untuk pasangan Finance dan kategori ATK tahun 2026, lalu realisasinya
dibaca dua kali: sebelum dan sesudah PB/2026/09/0002 diserahkan.

| Pembacaan | Realisasi di layar |
|---|---|
| Sebelum penyerahan PB/0002 | Rp 2.728.900 |
| Sesudah penyerahan PB/0002 | Rp 2.914.900 |
| Selisih | **Rp 186.000** |

Hitungan tangan: 3 rim × Rp 62.000 harga satuan terakhir Kertas HVS A4 = **Rp 186.000**. Cocok.

Angka turunannya juga diperiksa: 2.914.900 dibagi 5.000.000 adalah 58,298 persen, dan layar
menulis "58,3 persen". Sisa 5.000.000 − 2.914.900 = Rp 2.085.100, dan layar menulis angka yang
sama.

Perlu dicatat bahwa penyerahan sengaja **tidak** menyimpan harga satuan pada mutasinya. Harga
barang yang keluar adalah harga pembeliannya, bukan angka yang boleh disetel pemohon, jadi
penilaiannya jatuh ke harga pembelian terakhir di kartu barang. Itu memang perilaku yang sudah
dirancang sejak Kiriman J, dan pembuktian di atas menunjukkan jalurnya bekerja.

### 4.6 Dasbor, lencana menu, dan subjudul daftar

| Yang diperiksa | Hasil |
|---|---|
| Widget di tab Office Supplies | Muncul di urutan kedua, sesudah ringkasan stok dan sebelum diagram pemakaian |
| Empty state widget | "Tidak ada permintaan ATK yang menunggu. Semua permintaan sudah disetujui dan barangnya diserahkan..." |
| Widget saat ada antrean | Menampilkan PB/2026/09/0003 lengkap dengan kolom Kesiapan stok |
| Lencana menu saat tidak ada antrean | Tidak ada angka, sesuai rancangan. Angka yang tidak bisa ditindaklanjuti tidak ditampilkan |
| Lencana menu setelah ada yang menunggu tim GA | Berubah menjadi 1 |
| Subjudul daftar | "1 sudah disetujui dan menunggu diserahkan tim GA, 1 di antaranya belum bisa diserahkan penuh karena stoknya kurang." |
| Pembatalan | "Dibatalkan. Stok tidak tersentuh." |

### 4.7 Console, log, kolom, dan layar sempit

- **Log Laravel:** satu galat tercatat hari ini, dan galat itu **milik saya**, bukan aplikasi.
  Isinya `Public property [$toggledTableColumns] not found`, akibat saya mencoba menyalakan
  kolom tersembunyi lewat konsol peramban saat memeriksa harga barang. Tidak ada satu pun
  galat yang datang dari modul ini.
- **Aturan enam kolom:** daftar permintaan 6 kolom bawaan (tiga lagi tersembunyi), daftar
  barang di dalam permintaan 5 kolom bawaan (satu tersembunyi), widget dasbor 6 kolom.
- **Layar sempit 390 piksel:** halaman tidak meluber ke samping (lebar gulir 390 sama dengan
  lebar layar), tabel barang menggulir di dalam wadahnya sendiri, dan tidak ada satu pun
  elemen teks yang terpotong.
- **Tap target:** di dalam isi halaman ini ada tiga elemen di bawah 44 piksel, yaitu dua
  tautan remah roti setinggi 20 piksel dan tombol Pilih kolom setinggi 36 piksel. Ketiganya
  komponen bawaan Filament yang sama persis di seluruh 27 layar lama, bukan yang baru dibuat
  di kiriman ini. Memperbaikinya adalah perubahan tema menyeluruh, bukan perubahan Kiriman M,
  dan saya tidak menyelundupkannya ke sini.

## 5. Cacat yang ditemukan dan diperbaiki di ronde ini

**D-22. Judul kotak dialog berbunyi "Buat Supply Request Line".**
Filament menyusun judul kotak dialog relation manager dari nama kelas modelnya, dan hasilnya
adalah istilah basis data yang tidak pernah dipakai siapa pun saat bicara. Diperbaiki dengan
menulis judulnya sendiri: "Add Item" untuk penambahan, "Edit " ditambah nama barangnya untuk
perubahan, dan "Remove " ditambah nama barangnya untuk penghapusan. Berkasnya sudah dikirim
ulang ke mesin.

## 6. Delivery Gate checklist

**Blok 1, hal yang membatalkan penyerahan**

- Tombol atau tautan yang tidak melakukan apa apa (R-26): tidak ada. Tujuh tindakan alur
  semuanya terhubung ke metode model dan sudah dicoba satu per satu di peramban.
- Menu menuju halaman yang tidak ada (R-24): tidak ada. Menu lahir dari Resource dan
  disembunyikan lewat izin.
- Angka yang dikarang (R-17, R-18, R-36, R-38): tidak ada. Seluruh angka di layar dijumlahkan
  dari basis data, dan yang paling penting, nilai permintaan tidak pernah disimpan sebagai
  kolom.

**Blok 2, hal yang perlu diperbaiki sebelum diserahkan**

- Empty state (R-27): ada tiga, dan ketiganya berbeda menurut keadaannya. Daftar permintaan
  kosong, daftar barang kosong yang bisa diisi, dan daftar barang kosong yang sudah terkunci
  masing masing menulis kalimat yang berbeda.
- Em dash (R-02): tidak ada. Seluruh copy diperiksa.
- CTA generik (R-15, R-16): tidak ada. Tombolnya berbunyi Submit, Approve as Supervisor,
  Issue Supplies, Adjust Issued Quantity, bukan Save atau Continue.
- Kartu sendirian di barisnya: formulirnya berkartu tunggal dan sudah diberi
  `columnSpanFull()`.

**Blok 3, hal yang dicatat tetapi tidak menghalangi**

- Tap target bawaan Filament di bawah 44 piksel, seperti dijelaskan di 4.7.

## 7. Yang belum diuji, dan kenapa

Ditulis supaya tidak ada yang mengira gate ini menguji lebih banyak daripada yang sebenarnya.

- **Penguncian pemohon dari sudut pandang pengguna yang tidak berizin.** Seluruh pengujian
  dijalankan dengan akun administrator yang memegang `request_for_others`, jadi yang saya
  lihat adalah cabang kalimat bantuannya, bukan penguncian yang benar benar menolak. Kuncinya
  ada di tiga tempat: daftar pilihan yang hanya berisi diri sendiri, atribut disabled pada
  pilihannya, dan pemaksaan ulang di `CreateSupplyRequest::mutateFormDataBeforeCreate()` yang
  menutup jalur permintaan Livewire yang dikarang. Untuk membuktikannya perlu satu akun
  karyawan biasa yang bisa masuk, dan itu belum ada.
- **Dua orang menyerahkan barang yang sama pada saat bersamaan.** Pemeriksaan stok kedua yang
  berjalan tepat sebelum penyimpanan memang ditulis untuk keadaan itu, tetapi menirukan dua
  peramban yang menekan tombol dalam detik yang sama tidak saya lakukan.
- **Pembatalan transaksi saat penyerahan gagal di tengah.** Pembungkus `DB::transaction()`
  ada, tetapi untuk membuktikannya perlu memaksa kegagalan buatan di baris kelima, dan itu
  berarti menyuntikkan kode yang tidak boleh ikut terkirim.

## 8. Data uji yang tertinggal di basis data Anda

Ini perlu Anda baca sebelum memakai aplikasinya lagi.

**Yang sudah saya kembalikan sendiri:**

- Kepala departemen Finance sempat disetel ke Rizal Hakim untuk menguji cabang persetujuan
  atasan, lalu **sudah dikosongkan kembali**. Seluruh departemen sekarang kembali berbunyi
  "Belum ditentukan", persis seperti sebelum verifikasi.
- Pagu uji Finance dan kategori ATK tahun 2026 sebesar Rp 5.000.000 dibuat untuk membuktikan
  angka realisasi, lalu **sudah dihapus**. Daftar anggaran kembali berisi 7 baris.
- PB/2026/09/0003 dibuat lalu **dibatalkan**. Tidak ada stok yang tersentuh olehnya.

**Yang tidak bisa saya kembalikan tanpa memalsukan catatan:**

Dua permintaan benar benar diserahkan, dan itu benar benar mengurangi stok:

| Barang | Berkurang | Stok sekarang |
|---|---|---|
| Kertas HVS A4 80 gram | 8 rim, dari PB/0001 lima rim dan PB/0002 tiga rim | 40 rim, sebelumnya 48 rim |
| Kertas struk kasir | 4 roll | 8 roll, sebelumnya 12 roll |

Saya sengaja tidak membuat mutasi "koreksi tambah" untuk mengembalikannya, karena itu berarti
menulis barang masuk yang tidak pernah terjadi, dan catatan gudang yang dipalsukan lebih mahal
daripada delapan rim kertas. Kalau Anda ingin angkanya kembali ke keadaan semula, cara yang
jujur adalah menghapus dua permintaan itu beserta mutasinya lewat basis data, atau membiarkan
selisihnya terkoreksi sendiri pada stock opname ATK berikutnya.

Perlu dicatat juga bahwa Kertas struk kasir sekarang berada di bawah batas minimumnya, jadi ia
muncul di daftar Items to Reorder. Itu akibat penyerahan uji, bukan kebutuhan sungguhan.

## 9. Berkas yang dikirim

15 berkas di ronde pertama, 1 berkas dikirim ulang setelah perbaikan D-22, masing masing ke
`D:/DEVELOPMENT/Sistem GA/` dan cerminannya di `overlay/`. Seluruhnya lolos `php -l`, dan
empat berkas terbesar diambil kembali dari mesin lalu dibandingkan isinya untuk memastikan
tidak ada yang tertulis basi.

Baru:

- `database/migrations/2026_09_08_290000_create_supply_requests_table.php`
- `database/migrations/2026_09_08_290100_create_supply_request_lines_table.php`
- `app/Models/SupplyRequest.php`
- `app/Models/SupplyRequestLine.php`
- `app/Filament/Resources/SupplyRequests/SupplyRequestResource.php`
- `app/Filament/Resources/SupplyRequests/RelationManagers/LinesRelationManager.php`
- `app/Filament/Resources/SupplyRequests/Pages/CreateSupplyRequest.php`
- `app/Filament/Resources/SupplyRequests/Pages/ListSupplyRequests.php`
- `app/Filament/Resources/SupplyRequests/Pages/ViewSupplyRequest.php`
- `app/Filament/Widgets/PermintaanBarangMenunggu.php`

Diubah:

- `app/Models/Module.php`, dua aksi baru `issue` dan `request_for_others`
- `app/Filament/Pages/Dasbor.php`, widget baru di tab Office Supplies
- `database/seeders/ModuleSeeder.php`, modul `supply_requests`
- `database/seeders/NumberSequenceSeeder.php`, urutan nomor PB
- `database/seeders/RoleSeeder.php`, izin untuk Manajer GA, Staf GA, dan Karyawan

## 10. Kiriman berikutnya

Kiriman N, pembelian dan penerimaan ATK. Karena Anda memakai dua cara, pembelian langsung
akan dibangun sebagai pesanan yang langsung diterima penuh, bukan sebagai jalur terpisah.
