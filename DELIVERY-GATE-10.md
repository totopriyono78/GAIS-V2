# Delivery Gate 10: Kiriman I, pemakaian kendaraan

Tanggal: 7 September 2026
Cakupan: Tahap 4 bagian ketiga, pemesanan kendaraan, log perjalanan, dan pengisian BBM.
Termasuk dua catatan tambahan pemilik proyek: foto kendaraan, dan batas enam kolom per tabel.
Mode penerapan: DURING

---

## 1. Design Read

Saya baca ini sebagai **tiga buku catatan yang saling mengunci**, bukan tiga modul terpisah.

Pemesanan menjawab "boleh tidak, dan pakai mobil yang mana". Log perjalanan menjawab "benar
tidak dipakai, sejauh apa". Pengisian BBM menjawab "berapa habisnya". Ketiganya bertemu di satu
angka: odometer. Karena itu odometer punya satu penjaga di seluruh aplikasi, dan tiap layar
memanggil penjaga yang sama. Dial: ENERGY rendah, RHYTHM tenang, MOTION nol.

## 2. Alasan keputusan (R-31)

| Keputusan | Alasan satu baris |
|---|---|
| Warna | Status memakai arti warna yang sama dengan permintaan perbaikan, karena orangnya sama dan dua kosakata warna hanya membuat keduanya salah dibaca |
| Layout | Tiga tabel jadi tab di halaman kendaraan, bukan tiga menu, karena semuanya selalu dibaca dalam konteks satu kendaraan |
| Tipografi | Odometer dan liter rata kanan supaya angka bisa dibandingkan lurus antar baris |
| Spacing | Sama dengan modul lain, tanpa kekhususan |
| Kartu | Halaman pemesanan dibagi dua, isi pesanan dan kendaraan beserta persetujuannya, karena pemohon membaca yang pertama dan tim GA membaca yang kedua |
| Ilustrasi | Tidak ada |

## 3. Keputusan rancangan yang menentukan

**Pemohon tidak memilih kendaraan.** Ia menyebut kapan, ke mana, berapa orang, dan perlu sopir
atau tidak. Kendaraan ditentukan tim GA setelah disetujui. Membiarkan pemohon memilih sendiri
adalah cara tercepat membuat dua orang memesan mobil yang sama.

**Bentrok diperiksa dua kali**, sekali sebagai keterangan hidup di bawah pemilih kendaraan, dan
sekali lagi tepat sebelum disimpan. Dua orang bisa membuka layar itu bersamaan, dan yang menekan
tombol belakangan tidak boleh menang hanya karena layarnya dimuat lebih dulu.

**Odometer punya satu penjaga**, `App\Services\OdometerKendaraan`, dan aturannya dua rasa:
penutupan perjalanan memakai "tidak boleh lebih kecil dari yang tertinggi" karena selalu terjadi
sekarang, sedangkan pengisian BBM memakai pemeriksaan berbasis tanggal karena struk lama memang
odometernya lebih kecil daripada perjalanan minggu lalu.

**Konsumsi hanya dihitung antara dua pengisian penuh.** Hanya di dua titik itu isi tangkinya
diketahui sama. Pengisian setengah tangki tetap dicatat dan liternya tetap ikut dibagi, tetapi
tidak pernah jadi titik ukur. Baris yang belum memenuhi syarat menyebut alasannya.

## 4. Yang diverifikasi jalan

Satu siklus penuh dijalankan di browser pada basis data nyata.

| Yang diuji | Hasil |
|---|---|
| Pemesanan dibuat, nomor otomatis | `PK/2026/09/0001` |
| Lompatan persetujuan tercatat | "Departemen pemohon belum punya kepala departemen" |
| Jadwal dirangkum satu baris | "10 Sep 2026, 08:00 sampai 16:00", "Sekitar 8 jam" |
| Penugasan kendaraan | B 1234 UJI dengan sopir Andi Prasetyo |
| Keterangan bentrok saat memilih kendaraan | "Bentrok dengan PK/2026/09/0001, 10 Sep 2026, 08:00 sampai 16:00, ke Gudang Bekasi" |
| Penolakan bentrok saat menyimpan | Ditolak, kotak tetap terbuka, pesan menyebut nomor, jam, dan tujuan pemesanan lain |
| Pemesanan yang sudah selesai membebaskan jadwalnya | Ya, kendaraan yang sama bisa ditugaskan ke pemesanan berikutnya |
| Keberangkatan dicatat, odometer awal terisi sendiri | 84.210 km, diambil dari kendaraan |
| Odometer kembali lebih kecil daripada berangkat | Ditolak, "Saat berangkat tercatat 84.210 km" |
| Penutupan perjalanan | 84.356 km, jarak 146 km, lama 8 jam 15 menit |
| Odometer kendaraan ikut maju | 84.356 km per 10 Sep 2026 |
| Pemesanan ikut selesai sendiri | `PK/2026/09/0001` menjadi Selesai, tombolnya hilang (R-26) |
| Pengisian BBM dengan odometer mundur | Ditolak, kotak tetap terbuka |
| Konsumsi dari dua pengisian penuh | (84.210 − 83.500) ÷ 52 liter = **13,65 km per liter**, cocok dengan hitungan tangan |
| Pengisian pertama tanpa pembanding | "Belum ada pengisian penuh sebelumnya", bukan strip kosong (R-17) |
| Harga per liter | Rp 15.000, cocok untuk kedua baris |
| Konsumsi rata rata di ringkasan kendaraan | 13,65 km per liter |
| Unggah foto kendaraan | Berkas tersimpan, ukuran tercatat, gambar kecil tampil, tautan penuh membalas 200, gambar 240x160 |
| Batas enam kolom | Perintah kerja 6, permintaan perbaikan 6, pemesanan kendaraan 6, seluruh 40 tabel lolos audit |

## 5. Cacat yang ditemukan dan diperbaiki

**D-9. Pesan penugasan tidak menyebut nomor polisi.** Berbunyi " disiapkan untuk PK/2026/09/0001"
dengan bagian kendaraan kosong, karena yang baru disimpan adalah kolom `vehicle_id`, bukan objek
relasinya. Relasi dimuat ulang sebelum dipakai. Sekarang: "B 1234 UJI disiapkan untuk
PK/2026/09/0002."

**D-10. Kotak penugasan tertutup saat bentrok.** Yang sedang menugaskan perlu memilih kendaraan
lain sekarang juga, dan menutup kotaknya memaksa ia mengulang seluruh langkah untuk mengganti
satu pilihan. Ditambahkan `halt()`, dan sekarang perilakunya sama dengan penolakan odometer di
layar BBM yang memang sudah benar sejak awal.

**D-11. Aturan odometer terlalu ketat untuk pengisian BBM.** Aturan "tidak boleh lebih kecil dari
yang tertinggi" benar untuk penutupan perjalanan, tetapi memblokir pemasukan struk lama saat
pertama memakai aplikasi, yaitu justru data yang paling dibutuhkan untuk menghitung konsumsi.
Ditambahkan `alasanDitolakPadaTanggal()` yang memeriksa urutan waktu: tidak boleh lebih kecil
daripada catatan sebelum tanggalnya, tidak boleh lebih besar daripada catatan sesudahnya.

**Dua catatan tambahan pemilik proyek** juga dikerjakan pada kiriman ini: foto kendaraan sebagai
tabel tersendiri dengan tanggal pengambilan dan odometer, serta batas enam kolom bawaan yang
diterapkan setelah mengaudit seluruh tabel. Tiga tabel melanggar dan sudah dirapikan tanpa
membuang informasinya, dengan memindahkannya ke keterangan di bawah kolom yang berhubungan.

## 6. Delivery Gate checklist

**Blok 1, yang membatalkan kiriman**

- Tombol atau link mati (R-26): tidak ada. Tombol tindakan hilang begitu statusnya tidak berlaku.
- Navigasi ke halaman yang tidak ada (R-24): tidak ada. Tautan pemesanan ke kendaraan diuji.
- Angka yang dikarang (R-17): tidak ada. Konsumsi hanya muncul kalau penggalnya lengkap, dan
  kalau tidak, alasannya yang ditulis.

**Blok 2, yang harus diperbaiki sebelum diserahkan**

- Empty state (R-27): ada dan spesifik untuk pemesanan, log perjalanan, pengisian BBM, dan foto.
- Em dash (R-02): tidak ada.
- CTA generik (R-15, R-16): tidak ada. "Pesan kendaraan", "Tugaskan kendaraan",
  "Catat keberangkatan", "Catat pengisian".
- Tap target 44px (R-03): mengikuti komponen Filament.

**Blok 3, catatan**

- Loading state: seluruh relation manager memakai `$isLazy = false`.
- Mobile: belum diperiksa pada kiriman ini.

## 7. Data uji yang ditinggalkan

Semuanya bertanda `[DATA UJI]` kecuali dokumen kendaraan, dan seluruhnya menempel pada satu
kendaraan uji. Menghapus kendaraan `B 1234 UJI` membuang dokumen, foto, perjalanan, dan pengisian
BBM-nya sekaligus; asetnya sendiri tidak ikut terhapus.

- Kendaraan `B 1234 UJI` pada aset `FIN-1205-2018-0001`
- Tiga dokumen kendaraan, satu foto, satu perjalanan, dua pengisian BBM
- Tiga pemesanan `PK/2026/09/0001` sampai `0003`
- Permintaan perbaikan `PB/2026/09/0002`

## 8. Status

**LULUS.** Tiga cacat ditemukan lewat pengujian dan sudah diperbaiki, satu di antaranya (D-11)
adalah kesalahan rancangan yang baru terlihat saat mencoba memasukkan data bertanggal lampau.

Tahap 4 tersisa: kebersihan (area layanan, jadwal dan checklist petugas) dan keamanan (jadwal
shift, laporan insiden).
