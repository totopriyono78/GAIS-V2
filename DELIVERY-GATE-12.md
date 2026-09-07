# Delivery Gate 12: Kiriman K, tagihan rekanan

Tanggal: 7 September 2026
Cakupan: Tahap 5 bagian kedua, pencatatan tagihan rekanan, pembebanannya ke departemen dan
kategori biaya, persetujuan, dan penandaan pembayaran
Mode penerapan: DURING

---

## 1. Design Read

Saya baca ini sebagai **tumpukan faktur yang harus habis sebelum jatuh tempo**, untuk dua
orang sekaligus.

Staf GA membacanya sebagai antrean pekerjaan, dan yang ia butuhkan adalah urutan menurut
jatuh tempo beserta tanda mana yang sudah lewat. Manajer membacanya sebagai antrean tanda
tangan, dan yang ia butuhkan adalah nilai beserta pembebanannya dalam satu tarikan mata.
Karena itu kolom Nilai tidak pernah berdiri sendiri: di bawahnya selalu tertulis dibebankan
ke mana. Dial: ENERGY rendah, RHYTHM tenang, MOTION nol.

## 2. Alasan keputusan (R-31)

| Keputusan | Alasan satu baris |
|---|---|
| Warna | Merah hanya untuk jatuh tempo yang sudah lewat, kuning untuk tujuh hari terakhir, sisanya abu abu, karena tanggal adalah satu satunya hal di layar ini yang benar benar mendesak |
| Layout | Halaman Lihat dibagi dua, faktur di atas dan persetujuan beserta pembayaran di bawah, karena dua bagian itu dibaca dua orang yang berbeda |
| Tipografi | Nomor internal memakai huruf lebar tetap, seluruh rupiah rata kanan supaya ribuan sejajar antar baris |
| Spacing | Sama dengan modul lain, tanpa kekhususan |
| Kartu | Rincian pembebanan sebagai tabel dengan jumlah di kakinya, bukan kartu, karena yang dilakukan orang di situ adalah mencocokkan jumlah dengan faktur di tangannya |
| Ilustrasi | Tidak ada |

## 3. Keputusan rancangan yang menentukan

**Nilai tagihan tidak pernah disimpan.** Ia dijumlahkan dari baris pembebanannya, dan tidak
ada satu pun tempat di layar yang bisa mengetiknya. Tagihan yang totalnya berbeda dari
jumlah rinciannya adalah cacat yang paling mahal ditemukan belakangan, yaitu saat finance
sudah terlanjur membayar angka yang salah.

**Tanggal faktur yang menentukan tahun anggaran, bukan tanggal bayar.** Faktur Desember yang
dibayar Januari tetap membebani tahun lalu, dan itu yang dipakai tim finance saat menutup
buku.

**Rincian terkunci begitu tagihan diajukan.** Setelah itu mengubah barisnya berarti mengubah
angka yang sedang atau sudah ditandatangani orang lain tanpa ia tahu. Tagihan yang ditolak
punya tombol Kembalikan ke draf, dan alasan penolakannya sengaja tidak dihapus supaya bisa
dibaca sambil memperbaiki. Tanpa jalan kembali itu, orang akan membuat tagihan kedua untuk
faktur yang sama, dan dua baris untuk satu faktur adalah cara termudah membuat realisasi
terhitung dua kali.

**Menyetujui dan menandai lunas adalah dua izin yang berbeda.** `vendor_bills.approve` dan
`vendor_bills.pay` dipisah karena menggabungkannya berarti satu orang bisa menyetujui
tagihan lalu menyatakannya lunas sendiri tanpa ada yang tahu.

**Pilihan kategori dibatasi pada kategori bersumber tagihan.** Pemeliharaan, BBM, pajak
kendaraan, dan ATK tidak muncul di pemilih, karena keempatnya sudah dijumlahkan dari catatan
aslinya dan memasukkan fakturnya lagi akan membuat angkanya terhitung dua kali di layar
anggaran, dalam bentuk yang sangat sulit ditemukan orang. Pembatasannya di layar, bukan di
basis data, supaya data lama tetap bisa dimasukkan kalau kelak kebijakannya berubah.

**Sumber "manual" berganti nama menjadi "tagihan".** Enam kategori yang selama ini menulis
"Menunggu tagihan" sekarang punya modul yang mengisinya. Nama sumber diganti lewat migrasi,
bukan dibiarkan, karena kolom itulah yang dibaca orang untuk memutuskan apakah angka
realisasinya bisa dipercaya, dan nama yang tertinggal satu tahap di belakang kenyataan adalah
cara termurah membuat orang berhenti mempercayai seluruh layarnya.

**Yang belum disetujui disebut, bukan dijumlahkan.** Tagihan berstatus draf dan menunggu
persetujuan tidak masuk realisasi, tetapi nilainya tertulis di bawah angka realisasi tiap
pagu dan di subjudul halaman anggaran. Tumpukan faktur yang belum ditandatangani adalah cara
termudah membuat pagu terlihat sehat tepat pada hari uangnya sudah habis.

## 4. Yang diverifikasi jalan

Satu siklus penuh dijalankan di browser pada basis data nyata, dan tiap angka dicocokkan
dengan hitungan tangan.

| Yang diuji | Hitungan tangan | Yang muncul di layar |
|---|---|---|
| Nomor tagihan otomatis | Urutan pertama bulan ini | `TG/2026/09/0001` |
| Tagihan tanpa rincian tidak bisa diajukan | Nol baris | "Belum ada rincian pembebanan. Tambahkan minimal satu baris supaya ada nilai yang disetujui." |
| Nilai dari tiga baris | 4.500.000 + 3.200.000 + 1.300.000 | **Rp 9.000.000**, cocok di kaki tabel dan di kepala halaman |
| Pemilih kategori | Hanya 6 kategori bersumber tagihan | KBRS, LAIN, LSTR, RMTG, SEWA, TRNS. PMLH, BBM, DOKKEN, dan ATK tidak muncul |
| Jatuh tempo mendatang | 15 Sep dikurangi 7 Sep | "Sisa 8 hari" |
| Jatuh tempo terlewat | 25 Agu dibanding 7 Sep | "Lewat 13 hari", merah |
| Anggaran saat tagihan masih menunggu | Realisasi nol, tertunda 4.500.000 | **Rp 0**, "Sisa Rp 10.000.000. Rp 4.500.000 lagi menunggu persetujuan" |
| Subjudul anggaran saat menunggu | Seluruh nilai tagihan, termasuk baris tanpa departemen | "Rp 9.000.000 tagihan sudah masuk tetapi belum disetujui" |
| Anggaran setelah disetujui | 4.500.000 dari 10.000.000 | **Rp 4.500.000**, sisa Rp 5.500.000, **45,0 persen**, Masih aman |
| Baris tanpa departemen | 1.300.000 | "Rp 1.300.000 berupa biaya bersama pada tagihan yang memang tidak menyebut departemen" |
| Empat sumber otomatis tidak terganggu migrasi | Angka gate 11 | BBM Rp 1.245.000, DOKKEN Rp 3.995.000, PMLH GA Rp 2.100.000, semuanya tetap |
| Penandaan pembayaran | Realisasi tidak berubah | Status Sudah dibayar, "Dibayar 07 September 2026 oleh tester. Bukti: TRF-BCA-20260907-118" |
| Penolakan lalu kembali ke draf | Rincian bisa diubah lagi | Tombol Tambah pembebanan muncul kembali, alasan penolakan tetap terbaca |
| Tombol pada tagihan yang sudah dibayar | Tidak ada yang berlaku | Tidak ada satu pun tombol tindakan (R-26) |
| Batas enam kolom | Tabel tagihan dan rinciannya | 6 dan 4 kolom, seluruh 45 tabel lolos audit |
| Log Laravel selama pengujian | Tidak ada galat baru | Entri terakhir masih dari gate 11 pukul 11:57 |

## 5. Cacat yang ditemukan dan diperbaiki

**D-15. Kotak persetujuan menyebut "3 departemen" untuk dua departemen dan satu porsi
bersama.** Baris tanpa departemen ikut dihitung sebagai departemen. Ini kalimat yang dibaca
manajer tepat sebelum menandatangani, dan bunyinya membuat ia mengira seluruh nilainya sudah
ada pemiliknya padahal justru ada bagian yang belum. Sekarang berbunyi "2 departemen dan
sebagian biaya bersama", dan tagihan yang seluruhnya tanpa departemen mengatakannya apa
adanya.

**D-16. Nama departemen dikecilkan hurufnya di dua kotak.** "dibebankan ke finance" terbaca
seperti salah ketik. Sama seperti perbaikan singkatan STNK pada gate 9, `strtolower()`
dilepas.

**D-17. Tombol Tambah pembebanan masih terlihat pada tagihan yang baru saja diajukan.**
Rincian hidup di komponen Livewire tersendiri, dan komponen itu tidak ikut digambar ulang
saat tombol di kepala halaman mengubah status. Akibatnya penguncian rincian tidak terlihat
justru pada detik ia mulai berlaku. Diperbaiki dengan memuat ulang halaman Lihat setelah
tiap perubahan status. Pemberitahuannya tetap sampai, dan itu diuji: "TG/2026/09/0001
ditandai sudah dibayar" muncul setelah pemuatan ulang.

**D-18. Subjudul anggaran menjumlahkan tagihan tertunda dari baris pagu yang ada.** Tagihan
yang jatuh ke departemen yang belum diberi pagu jadi tidak terhitung, padahal justru
departemen itu yang paling perlu diketahui sedang berbelanja. Angkanya sempat tertulis
Rp 4.500.000 padahal yang masuk Rp 9.000.000. Sekarang dijumlahkan menurut kategori.

**D-19. Biaya yang belum terbebankan selalu menyebut alasan yang salah.** Kalimatnya
berbunyi "karena asetnya belum punya departemen", padahal Rp 1.300.000 itu berasal dari baris
tagihan yang memang sengaja tidak menyebut departemen. Dua sebab yang berbeda dengan tindakan
yang berbeda: yang pertama bisa dibetulkan di layar aset, yang kedua tidak perlu dibetulkan
sama sekali. Menyatukannya akan mengirim orang membetulkan data aset yang sudah benar.
Sekarang keduanya dihitung dan disebut terpisah.

## 6. Delivery Gate checklist

**Blok 1, yang membatalkan kiriman**

- Tombol atau link mati (R-26): tidak ada. Tombol tindakan hilang begitu statusnya tidak
  berlaku, dan tagihan yang sudah dibayar tidak menyisakan satu pun.
- Navigasi ke halaman yang tidak ada (R-24): tidak ada.
- Angka yang dikarang (R-17, R-38): tidak ada. Nilai tagihan seluruhnya dijumlahkan dari
  barisnya, realisasi dari tagihan yang benar benar disetujui, dan nomor akun tetap kosong.

**Blok 2, yang harus diperbaiki sebelum diserahkan**

- Empty state (R-27): ada dan spesifik untuk daftar tagihan maupun rincian pembebanan.
  Empty state rincian berbeda bunyinya antara tagihan yang masih draf dan yang sudah dikunci.
- Em dash (R-02): tidak ada.
- CTA generik (R-15, R-16): tidak ada. "Catat tagihan", "Tambah pembebanan", "Ajukan untuk
  disetujui", "Tandai sudah dibayar", "Kembalikan ke draf".
- Tap target 44px (R-03): mengikuti komponen Filament.

**Blok 3, catatan**

- Loading state: relation manager memakai `$isLazy = false`.
- Mobile: belum diperiksa pada kiriman ini.
- Satu tagihan hanya menampung satu berkas pindaian. Faktur yang datang berlembar lembar
  perlu digabung jadi satu PDF lebih dulu. Ini berterima untuk sekarang dan perlu ditinjau
  kalau ternyata sering.
- Belum ada pemeriksaan faktur ganda dari rekanan yang sama. Nomor faktur rekanan disimpan
  tetapi belum dipakai memperingatkan.

## 7. Data uji yang ditinggalkan

Seluruhnya bertanda `[DATA UJI]` dan menempel pada rekanan uji `VND-AC-01`.

- `TG/2026/09/0001`, Rp 9.000.000, sudah dibayar, tiga baris pembebanan
- `TG/2026/09/0002`, Rp 2.750.000, draf setelah ditolak dan dikembalikan
- Satu pagu anggaran KBRS untuk departemen Finance tahun 2026, Rp 10.000.000

Menghapus kedua tagihan mengembalikan realisasi KBRS Finance ke nol dan menghilangkan
Rp 1.300.000 dari angka biaya bersama. Tagihan yang sudah dibayar sengaja tidak bisa dihapus
dari layar, jadi penghapusannya perlu lewat basis data.

## 8. Status

**LULUS.** Lima cacat ditemukan lewat pengujian dan sudah diperbaiki. Tiga di antaranya
(D-15, D-18, D-19) adalah kalimat yang menyampaikan angka benar dengan arti yang salah, dan
ketiganya hanya terlihat karena datanya sengaja dibuat berantakan: dua departemen ditambah
satu porsi tanpa departemen, dan satu departemen yang belum punya pagu.

Tahap 5 tersisa: reimbursement karyawan, dan laporan anggaran versus realisasi yang bisa
diunduh.
