# Delivery Gate 11: Kiriman J, kategori biaya dan anggaran versus realisasi

Tanggal: 7 September 2026
Cakupan: Tahap 5 bagian pertama, kategori biaya dan pagu anggaran per departemen per tahun
Mode penerapan: DURING

---

## 1. Design Read

Saya baca ini sebagai **satu layar pembanding untuk manajer GA**, bukan formulir pencatatan.

Pertanyaan yang dibawa orang ke layar ini cuma satu: pagu mana yang sudah mau habis. Karena
itu kolom Realisasi dan Terpakai berdampingan tepat di sebelah Pagu, dan keadaannya diberi
warna. Tidak ada satu pun kolom di layar ini yang perlu diketik selain pagunya sendiri.
Dial: ENERGY rendah, RHYTHM tenang, MOTION nol.

## 2. Alasan keputusan (R-31)

| Keputusan | Alasan satu baris |
|---|---|
| Warna | Merah untuk pagu yang lewat, kuning untuk yang di atas 85 persen, hijau untuk sisanya; abu abu khusus untuk kategori yang realisasinya memang belum bisa dihitung |
| Layout | Pagu, Realisasi, dan Terpakai berurutan dalam satu tarikan mata, karena ketiganya hanya berarti kalau dibaca bersamaan |
| Tipografi | Seluruh angka rupiah rata kanan supaya ribuan sejajar antar baris |
| Spacing | Sama dengan modul lain |
| Kartu | Tidak ada kartu. Perbandingan antar departemen butuh tabel, dan kartu justru memisahkan angka yang perlu dibandingkan |
| Ilustrasi | Tidak ada |

## 3. Keputusan rancangan yang menentukan

**Realisasi tidak pernah diketik siapa pun.** Ini satu satunya alasan modul anggaran lebih
berguna daripada spreadsheet. Realisasi yang diisi tangan akan selalu tertinggal dari kenyataan
dan pelan pelan berhenti dipercaya. Empat kategori menjumlahkan sendiri dari catatan yang sudah
ada karena pekerjaan sehari hari: pemeliharaan dari perintah kerja dan kunjungan yang selesai,
BBM dari pengisian, pajak kendaraan dari tanggal terbit dokumennya, dan ATK dari barang yang
keluar gudang.

**Realisasi tidak pernah menjadi kolom di basis data.** Angka realisasi yang disimpan akan salah
sejak perintah kerja berikutnya ditutup. Ia dijumlahkan saat halaman dibuka, dan diingat selama
satu permintaan supaya tiga puluh baris tidak menjadi tiga puluh penjumlahan yang sama.

**Kategori yang belum bisa dihitung mengatakannya apa adanya.** Enam kategori bersumber manual
dan menunggu modul tagihan. Layarnya menulis "Belum dijumlahkan" dan "Menunggu tagihan", bukan
Rp 0 dan nol persen terpakai, karena yang kedua terbaca seolah departemen itu hemat (R-17).

**Biaya yang tidak bisa dibebankan dilaporkan terpisah.** Biaya menempel pada aset, dan aset yang
belum punya departemen menghasilkan biaya tanpa pemilik. Angka itu muncul di subjudul halaman.
Kalau tidak disebut, selisihnya akan dikira kesalahan hitung.

**Nomor akun dikosongkan seluruhnya di seeder.** Nomor akun milik bagan akun perusahaan, dan
mengarangnya berarti menaruh angka palsu di jalur yang berujung ke jurnal di Tahap 6 (R-38).

## 4. Yang diverifikasi jalan

Tiga sumber otomatis diuji dengan angka yang bisa dihitung tangan dari data yang ada.

| Yang diuji | Hitungan tangan | Yang muncul di layar |
|---|---|---|
| BBM, departemen Finance, 2026 | Rp 465.000 + Rp 780.000 | **Rp 1.245.000**, sisa Rp 755.000, 62,3 persen |
| Pajak dan dokumen kendaraan, Finance, 2026 | Rp 275.000 KIR + Rp 3.720.000 pajak baru; pajak 20 Agustus 2025 di luar jendela | **Rp 3.995.000**, lewat Rp 995.000, 133,2 persen, Melewati pagu |
| Pemeliharaan, General Affair, 2026 | Rp 2.100.000 dari kunjungan yang ditutup pada gate 7 | **Rp 2.100.000**, sisa Rp 7.900.000, 21,0 persen |
| Pemeliharaan, Finance, 2026 | Nol, karena aset Finance tidak punya pemeliharaan selesai tahun ini | **Rp 0** |
| Kategori bersumber manual | Tidak bisa dihitung | "Belum dijumlahkan", "Menunggu tagihan" |
| Subjudul halaman | 1 pagu terlewati | "Tahun 2026. 1 pagu sudah terlewati." |
| Sepuluh kategori awal ter-seed | Empat otomatis, enam manual, semuanya tanpa nomor akun | Sesuai |
| Batas enam kolom | Kedua tabel baru | Enam kolom |

Yang paling meyakinkan dari ketiganya adalah baris pajak kendaraan: dokumen pajak 2025 yang
odometernya ada di basis data yang sama **tidak** ikut terhitung, karena jendelanya memang
mengikuti tanggal terbit dalam tahun anggaran, bukan seluruh riwayat.

## 5. Cacat yang ditemukan dan diperbaiki

**D-12. Halaman anggaran gagal terbuka dengan galat basis data.**
`SQLSTATE[42702] column reference "status" is ambiguous`. Penyaring `WorkOrder::selesai()` dan
`MaintenanceVisit::terbuka()` menyebut kolom `status` tanpa nama tabelnya, dan begitu dipakai
bersama join ke tabel aset yang juga punya kolom `status`, PostgreSQL tidak tahu yang mana.
Diperbaiki di modelnya dengan `qualifyColumn()`, bukan di kelas laporan, karena penyaring yang
sama akan dipakai laporan berikutnya dan cacatnya akan muncul lagi di tempat lain. Gejalanya
jauh dari sebabnya, jadi alasannya ditulis sebagai komentar di kedua model.

**D-13. Pagu yang terlewati menulis "Sisa Rp -995.000".**
Sisa yang negatif bukan sisa, dan tanda minus di depan rupiah adalah bentuk yang paling mudah
terbaca salah saat halaman dibaca cepat. Sekarang berbunyi "Lewat Rp 995.000".

**D-14. Perintah kerja tanpa aset hilang dari kedua sisi laporan.**
Sejak permintaan perbaikan dibangun, satu perintah kerja boleh tidak menyebut aset. Dengan join
biasa, biaya pekerjaan seperti itu tidak masuk departemen mana pun **dan** tidak masuk angka
belum terbebankan, jadi uang yang keluar tidak muncul di laporan mana pun. Diganti `leftJoin`,
sehingga biayanya jatuh ke kelompok belum terbebankan dan tetap terlihat. Cacat ini belum
berdampak pada data sekarang karena perintah kerja tanpa aset yang ada belum diselesaikan, dan
justru itu yang membuatnya berbahaya: ia baru muncul saat sudah ada uangnya.

## 6. Delivery Gate checklist

**Blok 1, yang membatalkan kiriman**

- Tombol atau link mati (R-26): tidak ada.
- Navigasi ke halaman yang tidak ada (R-24): tidak ada.
- Angka yang dikarang (R-17, R-38): tidak ada. Realisasi seluruhnya dijumlahkan dari catatan
  nyata, kategori yang belum bisa dihitung menyebut alasannya, dan nomor akun dibiarkan kosong.

**Blok 2, yang harus diperbaiki sebelum diserahkan**

- Empty state (R-27): ada dan spesifik untuk kategori biaya maupun pagu anggaran.
- Em dash (R-02): tidak ada.
- CTA generik (R-15, R-16): tidak ada. "Tetapkan pagu", "Tambah kategori".
- Tap target 44px (R-03): mengikuti komponen Filament.

**Blok 3, catatan**

- Penyaring "Sudah melewati pagu" bekerja di PHP lewat daftar id, karena realisasinya memang
  tidak ada di basis data untuk dibandingkan lewat SQL. Ini berterima untuk puluhan sampai
  ratusan baris pagu, dan perlu ditinjau ulang kalau kelak jadi ribuan.
- Mobile: belum diperiksa pada kiriman ini.

## 7. Data uji yang ditinggalkan

Lima pagu bertanda `[DATA UJI]` di catatannya, seluruhnya tahun 2026: BBM, pajak kendaraan,
sewa, dan pemeliharaan untuk Finance, serta pemeliharaan untuk General Affair. Menghapusnya
tidak menghilangkan biaya apa pun, karena pagu memang hanya pembanding.

## 8. Status

**LULUS.** Tiga cacat ditemukan lewat pengujian dan sudah diperbaiki. Ketiga sumber realisasi
otomatis yang bisa diuji dengan data yang ada terbukti cocok sampai rupiah terakhir.

Tahap 5 tersisa: tagihan vendor dan reimbursement karyawan, keduanya mengisi kategori yang
sekarang masih bersumber manual.
