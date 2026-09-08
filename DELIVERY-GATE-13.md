# Delivery Gate 13: Kiriman L, penggantian biaya karyawan

Tanggal: 7 September 2026
Cakupan: Tahap 5 bagian ketiga, pengajuan penggantian biaya karyawan beserta struknya,
persetujuan atasan, pemeriksaan tim GA, dan penandaan transfer
Mode penerapan: DURING

---

## 1. Design Read

Saya baca ini sebagai **amplop berisi struk yang berpindah dari meja ke meja**, dibaca tiga
orang dengan pertanyaan yang berbeda.

Karyawan bertanya "sudah sampai mana punya saya". Kepala departemen bertanya "mana yang
menunggu tanda tangan saya". Tim GA bertanya "mana yang struknya belum saya periksa".
Ketiganya dijawab satu kolom: Status, dengan keterangan sedang ada di meja siapa tepat di
bawahnya. Dial: ENERGY rendah, RHYTHM tenang, MOTION nol.

## 2. Alasan keputusan (R-31)

| Keputusan | Alasan satu baris |
|---|---|
| Warna | Kuning untuk yang menunggu orang, biru untuk yang menunggu uang, hijau untuk yang selesai, dan kuning juga untuk struk yang belum ada fotonya karena itu satu satunya hal di baris yang perlu ditindaklanjuti |
| Layout | Halaman Lihat dibagi dua, isi pengajuan di atas dan tiga tahap persetujuan di bawah, karena bagian bawah itulah yang dibaca berulang kali sementara bagian atas dibaca sekali |
| Tipografi | Nomor memakai huruf lebar tetap, seluruh rupiah rata kanan supaya ribuan sejajar antar baris |
| Spacing | Sama dengan modul lain, tanpa kekhususan |
| Kartu | Struk sebagai tabel dengan jumlah di kakinya, bukan kartu, karena yang dilakukan pemeriksa adalah mencocokkan jumlah dengan amplop di tangannya |
| Ilustrasi | Tidak ada |

## 3. Keputusan rancangan yang menentukan

**Dua persetujuan, karena ada dua pertanyaan.** Atasan menjawab "benar ini keperluan kerja".
Tim GA menjawab "struknya ada dan angkanya cocok". Menggabungkan keduanya berarti salah satu
pertanyaan itu tidak pernah ditanyakan. Izinnya juga dipisah bertiga: `approve` untuk atasan,
`verify` untuk tim GA, `pay` untuk yang mentransfer.

**Tanpa batas nominal**, sesuai keputusan pemilik proyek pada 7 September 2026. Pengajuan
Rp 50.000 melewati jalur yang sama dengan Rp 5.000.000.

**Satu baris adalah satu struk, dan fotonya menempel di barisnya.** Menaruh berkas di
tingkat pengajuan memaksa orang menggabung lima struk jadi satu PDF sebelum bisa mengunggah,
dan itu pekerjaan yang tidak perlu ada.

**Foto boleh kosong dan tidak memblokir pengajuan.** Struk memang kadang hilang, dan menutup
jalannya berarti memaksa orang mengarang berkas supaya bisa lanjut. Yang dilakukan aplikasi
adalah menghitung berapa yang belum ada fotonya dan menyebutkannya di kotak persetujuan,
sehingga tim GA memutuskan sambil tahu.

**Departemen ada di kepala pengajuan, bukan di tiap struk.** Struk milik satu orang hampir
selalu jatuh ke satu departemen, dan meminta pilihan departemen di tiap struk berarti
membebani seratus pengajuan demi satu perkecualian. Kolomnya boleh dikosongkan untuk belanja
kantor bersama, dan angkanya lalu muncul terpisah di layar anggaran. Ini berbeda dari tagihan
rekanan, yang departemennya ada di tiap baris karena satu faktur listrik memang dibagi ke
banyak departemen sekaligus.

**Tanggal struk, bukan tanggal pengajuan**, yang menentukan tahun anggaran. Struk Desember
yang baru diajukan Januari tetap membebani tahun lalu, sama seperti faktur rekanan yang
memakai tanggal fakturnya. Pengajuan yang struknya menyeberang tahun menyebutkannya sendiri.

**Persetujuan atasan bisa dilewati, dan alasannya ditulis.** Tiga keadaan: departemen yang
dibebani belum punya kepala, pemohon adalah kepala departemen itu sendiri, dan pengajuan yang
memang tidak dibebankan ke departemen mana pun. Tanpa jalan keluar itu, pengajuan kepala
departemen akan tersangkut selamanya menunggu tanda tangannya sendiri.

**Penolakan menyebut tahapnya dari status, bukan dari izin orangnya.** Manajer GA memegang
`approve` dan `verify` sekaligus, jadi menebak dari izin akan salah menuliskan siapa yang
mengembalikan. Yang dipakai adalah status pengajuan saat tombol ditekan.

## 4. Yang diverifikasi jalan

Dua siklus dijalankan di browser pada basis data nyata, satu untuk tiap cabang persetujuan.

| Yang diuji | Hitungan tangan | Yang muncul di layar |
|---|---|---|
| Nomor otomatis | Urutan pertama bulan ini | `PG/2026/09/0001` |
| Departemen terisi sendiri dari pemohon | Rizal Hakim ada di Finance | Departemen berubah ke Finance saat pemohon dipilih |
| Nilai dari tiga struk | 185.000 + 40.000 + 275.000 | **Rp 500.000**, cocok di kaki tabel dan di kepala halaman |
| Rentang tanggal struk | 2 dan 3 September | "02 Sep 2026 sampai 03 Sep 2026" |
| Hitungan struk tanpa foto | Tiga baris tanpa berkas | "3 struk belum ada fotonya", disebut di subjudul dan di kotak persetujuan |
| Pengajuan tanpa struk tidak bisa diajukan | Nol baris | "Belum ada struk yang dicatat. Tambahkan minimal satu baris supaya ada nilai yang disetujui." |
| **Cabang lewat persetujuan** | Finance belum punya kepala | "Langsung ke tim GA, karena departemen yang dibebani belum punya kepala departemen" |
| **Cabang lewat atasan** | Kepala Finance disetel Dewi Anggraini | "Menunggu persetujuan Dewi Anggraini, lalu tim GA memeriksa struknya" |
| Tombol tim GA belum muncul saat masih di atasan | Dua tahap berurutan | Hanya Setujui sebagai atasan, Tolak, Batalkan |
| Persetujuan atasan tercatat beserta catatannya | | "Disetujui Dewi Anggraini pada 07 September 2026, 13:42. Catatan: ..." |
| **Anggaran saat masih menunggu** | 225.000 TRNS Finance | Realisasi **Rp 0**, "Sisa Rp 2.000.000. Rp 225.000 lagi menunggu persetujuan" |
| Pemisahan kategori dalam satu pengajuan | TRNS 225.000, RMTG 275.000 | Hanya 225.000 yang muncul di pagu TRNS, bukan 500.000 |
| Subjudul anggaran gabungan | 2.750.000 faktur + 500.000 struk | **Rp 3.250.000** belum disetujui |
| **Anggaran setelah diperiksa** | 225.000 dari pagu 2.000.000 | **Rp 225.000**, sisa Rp 1.775.000, **11,3 persen**, Masih aman |
| Angka tertunda turun setelah disetujui | 3.250.000 dikurangi 500.000 | Rp 2.750.000 |
| Penandaan transfer | Realisasi tidak berubah | "Diganti 07 September 2026 oleh tester. Bukti: TRF-BCA-20260907-402" |
| Penolakan oleh tim GA | Penguji memegang kedua izin | "Ditolak tim GA", bukan "Ditolak atasan" |
| Kembali ke draf lalu diajukan ulang | Persetujuan atasan diulang | Kembali ke "Menunggu Dewi Anggraini", bukan lompat ke tim GA |
| Alasan penolakan saat draf | Masih perlu dibaca sambil memperbaiki | Tetap tampil, dan baru hilang setelah diajukan ulang |
| Tombol pada pengajuan yang sudah diganti | Tidak ada yang berlaku | Tidak ada satu pun tombol tindakan (R-26) |
| Batas enam kolom | Tabel pengajuan dan tabel struk | 6 dan 4 kolom, seluruh 47 tabel lolos audit |
| Log Laravel selama pengujian | Tidak ada galat baru | Entri terakhir masih dari gate 11 pukul 18:57 |

Yang paling meyakinkan dari daftar ini adalah baris pemisahan kategori: satu pengajuan
Rp 500.000 hanya menaikkan pagu transportasi sebesar Rp 225.000, karena Rp 275.000 sisanya
memang struk konsumsi yang jatuh ke kategori rumah tangga kantor.

## 5. Cacat yang ditemukan dan diperbaiki

**D-20. Subjudul anggaran masih menyebut semuanya "tagihan".** Sejak struk karyawan ikut
dijumlahkan, kalimat "Rp 3.250.000 tagihan sudah masuk tetapi belum disetujui" menutupi
kenyataan bahwa sebagian angka itu berasal dari pengajuan karyawan, bukan dari faktur
rekanan. Hal yang sama pada kalimat biaya bersama. Keduanya kini menyebut kedua sumbernya.
Cacat ini kecil di layar tetapi persis jenis yang membuat orang mencari faktur yang tidak
pernah ada.

## 6. Delivery Gate checklist

**Blok 1, yang membatalkan kiriman**

- Tombol atau link mati (R-26): tidak ada. Tombol tiap tahap hanya muncul pada status yang
  memang menerimanya, dan pengajuan yang sudah diganti tidak menyisakan satu pun.
- Navigasi ke halaman yang tidak ada (R-24): tidak ada.
- Angka yang dikarang (R-17, R-38): tidak ada. Nilai pengajuan dijumlahkan dari struknya, dan
  realisasi hanya dari pengajuan yang benar benar sudah diperiksa.

**Blok 2, yang harus diperbaiki sebelum diserahkan**

- Empty state (R-27): ada dan spesifik untuk daftar pengajuan maupun daftar struk. Empty
  state struk berbeda bunyinya antara pengajuan yang masih draf dan yang sudah dikunci.
- Em dash (R-02): tidak ada.
- CTA generik (R-15, R-16): tidak ada. "Ajukan penggantian biaya", "Tambah struk", "Setujui
  sebagai atasan", "Selesai diperiksa", "Tandai sudah diganti".
- Tap target 44px (R-03): mengikuti komponen Filament.

**Blok 3, catatan**

- Unggah foto struk tidak diuji ulang pada kiriman ini. Komponennya sama persis dengan
  pindaian dokumen kendaraan dan foto kendaraan yang sudah diuji tuntas pada gate 9 dan 10,
  termasuk penyimpanan nama berkas aslinya. Yang diuji di sini adalah sisi yang baru, yaitu
  penghitungan struk tanpa foto, dan itu terbukti benar untuk satu dan tiga struk.
- Penyempitan daftar lewat `read_all` belum diuji dengan akun karyawan biasa, karena
  pengujian dijalankan dengan akun administrator. Logikanya sama persis dengan permintaan
  perbaikan dan pemesanan kendaraan yang sudah diuji sebelumnya.
- Mobile: belum diperiksa pada kiriman ini.
- Satu pengajuan hanya bisa dibebankan ke satu departemen. Kalau kelak ada karyawan yang
  rutin berbelanja untuk dua departemen sekaligus, ini yang perlu ditinjau ulang.

## 7. Data uji yang ditinggalkan

Seluruhnya bertanda `[DATA UJI]` di judul atau catatannya.

- `PG/2026/09/0001`, Rp 500.000, tiga struk, sudah diganti
- `PG/2026/09/0002`, Rp 420.000, satu struk, draf setelah ditolak tim GA
- Satu pagu anggaran TRNS untuk departemen Finance tahun 2026, Rp 2.000.000

Menghapus kedua pengajuan mengembalikan realisasi TRNS Finance ke nol.

**Satu perubahan sementara pada data induk, sudah dikembalikan.** Tidak ada satu pun
departemen yang punya kepala, sehingga cabang persetujuan atasan mustahil diuji apa adanya.
Kepala departemen Finance disetel sementara ke Dewi Anggraini, cabang atasannya diuji, lalu
kolomnya dikosongkan lagi. Daftar departemen sekarang kembali seperti semula, seluruhnya
"Belum ditentukan". Akibatnya yang masih tersisa: `PG/2026/09/0002` menyimpan Dewi Anggraini
sebagai penyetujunya, karena penyetuju dibekukan di pengajuan dan tidak dibaca ulang.

Selama kolom kepala departemen masih kosong di semua departemen, **seluruh pengajuan akan
melewati persetujuan atasan dan langsung ke tim GA**, dengan alasannya tertulis di layar. Itu
perilaku yang benar, tetapi bukan alur yang Anda minta. Mengisi kepala departemen adalah satu
satunya hal yang perlu dilakukan supaya langkah atasan benar benar berjalan.

## 8. Status

**LULUS.** Satu cacat ditemukan lewat pengujian dan sudah diperbaiki. Kedua cabang
persetujuan terbukti berjalan, dan angka realisasinya cocok sampai rupiah terakhir, termasuk
pemisahan dua kategori di dalam satu pengajuan.

Tahap 5 tersisa satu langkah: laporan anggaran versus realisasi yang bisa diunduh.

---

## 9. Tambahan atas permintaan pemilik proyek, 7 September 2026

Dua catatan yang datang setelah kiriman L lulus, dikerjakan dan diverifikasi pada hari yang
sama.

### 9.1 Tab Anggaran di dasbor, berisi data penggantian biaya

Dasbor sebelumnya berhenti di tab Persediaan, sehingga seluruh angka uang hanya bisa dibaca
dengan membuka menunya satu per satu. Tab Anggaran ditambahkan dengan tiga bagian.

**Ringkasan anggaran** menjawab keadaan pagu tahun berjalan: total pagu, realisasi beserta
persentasenya, jumlah pagu yang terlewati, dan nilai faktur serta struk yang sudah masuk
tetapi belum disetujui. Kartu terakhir itu yang paling mudah dilupakan dan paling mahal
kalau tidak ada, karena tanpa disebut, seluruh pagu akan terlihat sehat tepat pada hari ia
sedang tidak.

**Ringkasan penggantian biaya** mengikuti tiga meja yang dilewati satu pengajuan, berurutan
sama dengan alurnya: menunggu atasan, menunggu tim GA, menunggu ditransfer, ditambah nilai
yang sudah diganti tahun ini. Kartu ketiga membawa nilainya, bukan hanya jumlah
pengajuannya, karena itulah uang karyawan yang sudah keluar dan belum kembali.

**Daftar penggantian biaya yang menunggu tindakan**, urut dari yang paling lama menunggu,
barisnya bisa diklik langsung ke pengajuannya. Draf sengaja tidak masuk daftar, karena draf
belum menunggu siapa pun kecuali pemiliknya sendiri.

Ketiganya memakai penyempitan daftar yang sama dengan menunya, jadi karyawan biasa hanya
melihat angka pengajuannya sendiri.

Angka yang muncul di layar dicocokkan dengan hitungan tangan:

| Yang diuji | Hitungan tangan | Yang muncul di layar |
|---|---|---|
| Total pagu 2026 | 10 + 20 + 50 + 3 + 2 + 10 + 2 juta dari 7 baris | **Rp 97.000.000** |
| Total realisasi | 2.100.000 + 3.995.000 + 1.245.000 + 4.500.000 + 225.000 | **Rp 12,07 juta**, 12,4 persen |
| Pagu terlewati | Hanya pajak kendaraan Finance | 1 |
| Belum disetujui | 2.750.000 faktur + 420.000 + 375.000 struk | **Rp 3.545.000** |
| Menunggu tim GA | Satu pengajuan | 1 |
| Sudah diganti tahun ini | `PG/2026/09/0001` | **Rp 500.000** |
| Draf tidak masuk daftar menunggu | `PG/2026/09/0002` berstatus draf | Tidak muncul |
| Batas enam kolom | Tabel widget baru | 6 kolom, seluruh 48 tabel lolos audit |

### 9.2 Kartu formulir yang berdiri sendiri kini selebar layar

Skema formulir resource memakai dua kolom di layar lebar. Formulir yang kartunya hanya satu
karena itu tampil selebar setengah layar dengan separuh kanan kosong, dan hal yang sama
terjadi pada kartu terakhir di formulir berjumlah ganjil.

Diperbaiki dengan memberi `->columnSpanFull()` pada kartu yang akan berdiri sendiri di
barisnya. Tersentuh dua belas formulir: enam berkartu tunggal (Departemen, Lokasi, Modul,
Pengaturan, Tagihan rekanan, Penggantian biaya) dan enam berkartu tiga (Kategori aset,
Jadwal pemeliharaan, Permintaan perbaikan, Pengguna, Rekanan, Perintah kerja). Formulir yang
kartunya genap tidak diubah, karena kartunya memang sudah berpasangan rapi.

Formulir aset ditangani terpisah karena dua kartunya tampil bersyarat, sehingga jumlah kartu
per baris berubah ubah dan tidak bisa dijaga tetap genap. Kedua kartu bersyarat itu,
Penyusutan dan Sertifikat, diberi lebar penuh sehingga keenam kartu tetapnya selalu tersisa
berpasangan pada keempat kemungkinan tampilannya.

Diverifikasi di browser: kartu formulir tagihan rekanan yang tadinya 472 piksel pada layar
1366 piksel sekarang 967 piksel, selebar isi halamannya. Formulir perintah kerja pada
halaman Buat tetap menampilkan dua kartu berdampingan, karena kartu ketiganya memang baru
muncul saat pekerjaannya ditutup.

Aturannya ditulis ke `CLAUDE.md` beserta pengecualian kartu bersyarat, dan ikut diaudit
skrip yang sama dengan batas enam kolom.

### 9.3 Cacat yang ditemukan dan diperbaiki

**D-21. Seluruh aplikasi berhenti dengan galat fatal.**
`Cannot redeclare non static Filament\Widgets\StatsOverviewWidget::$heading as static`.
Widget ringkasan penggantian biaya menulis `protected static ?string $heading`, mengikuti
pola widget tabel yang memang memakai properti static. `StatsOverviewWidget` mendeklarasikan
properti itu sebagai properti biasa, dan PHP menolak penimpaan yang mengubah sifat static
saat kelasnya dimuat, sehingga setiap halaman panel ikut mati, bukan hanya dasbornya.
Diperbaiki menjadi properti biasa, dan alasannya ditulis sebagai komentar di tempatnya
supaya pola yang benar untuk kedua jenis widget tidak tertukar lagi.
