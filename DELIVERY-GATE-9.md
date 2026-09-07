# Delivery Gate 9: Kiriman H bagian pertama, kendaraan dinas dan dokumennya

Tanggal: 7 September 2026
Cakupan: Tahap 4 bagian kedua, data kendaraan beserta pajak, STNK, KIR, dan asuransinya
Mode penerapan: DURING

---

## 1. Design Read

Saya baca ini sebagai **daftar kerja bulanan untuk tim GA**, bukan katalog kendaraan.

Layar ini dibuka untuk satu pertanyaan yang berulang tiap bulan: mana yang pajaknya, KIR-nya,
atau asuransinya segera habis. Karena itu kolom kedua setelah nomor polisi adalah keadaan
dokumen, bukan merek atau tahun, dan urutannya dari yang paling dekat jatuh tempo, bukan menurut
abjad pelat. Dial: ENERGY rendah, RHYTHM tenang, MOTION nol.

## 2. Alasan keputusan (R-31)

| Keputusan | Alasan satu baris |
|---|---|
| Warna | Merah untuk yang sudah lewat, kuning untuk yang mendekat, hijau untuk yang aman; arti warna sama persis dengan modul pemeliharaan supaya tidak perlu dipelajari dua kali |
| Layout | Halaman detail dibagi Kendaraan, Dokumen yang berlaku, dan riwayat dokumen, karena tiga hal itu dibaca pada tiga kesempatan yang berbeda |
| Tipografi | Nomor polisi, nomor rangka, dan nomor mesin memakai monospace, karena ketiganya dibandingkan karakter demi karakter saat mengurus perpanjangan |
| Spacing | Sama dengan modul aset, karena kendaraan memang satu baris di daftar aset dan orang berpindah bolak balik |
| Kartu | Ringkasan dokumen berlaku ditulis sebagai kalimat, bukan kartu per jenis, supaya kendaraan yang baru punya satu dokumen tidak menampilkan empat kartu kosong |
| Ilustrasi | Tidak ada |

## 3. Keputusan rancangan yang menentukan

**Kendaraan adalah aset dengan keterangan tambahan, bukan daftar tersendiri.**
Tabel `vehicles` menyimpan `asset_id` unik. Penyusutan, mutasi, perintah kerja, dan pelepasan
tetap memakai jalur aset yang sudah ada. Daftar kendaraan yang berdiri sendiri berarti membangun
semua itu untuk kedua kalinya, lalu menghabiskan sisa umur aplikasi mencocokkan dua daftar.

**Satu baris dokumen adalah satu masa berlaku, bukan satu jenis dokumen.**
Perpanjangan menambah baris, tidak menimpa. Yang berlaku sekarang dihitung saat dibaca, yaitu
tanggal berakhir terjauh untuk tiap jenis. Tidak ada kolom penanda "aktif" yang harus dijaga
tetap benar, karena kolom seperti itu selalu berakhir salah pada baris yang terlupa.

**Pajak tahunan dan perpanjangan STNK lima tahunan dipisah.** Di Samsat keduanya dua urusan
berbeda. Digabung jadi satu jenis, salah satunya pasti terlewat.

**Odometer disimpan, dan itu satu satunya pengecualian dari aturan derived-not-stored di modul
ini.** Alasannya ditulis di migrasinya: saat log perjalanan dan pengisian BBM dibangun, angka ini
yang dipakai memvalidasi bahwa odometer tidak mundur, dan menghitungnya ulang dari seluruh
riwayat tiap kali orang mengetik satu baris terlalu mahal.

## 4. Yang diverifikasi jalan

Diuji di browser pada basis data nyata di mesin pemilik proyek, dengan kendaraan uji
`B 1234 UJI` yang menempel pada aset `FIN-1205-2018-0001`.

| Yang diuji | Hasil |
|---|---|
| Menu Kendaraan dan tab dasbor Kendaraan muncul setelah sync izin | Ya |
| Nomor polisi dirapikan | Diketik `b1234uji`, tersimpan `B 1234 UJI` |
| Nama kendaraan diambil dari aset | "Toyota Avanza Tipe 266 2018" |
| Penanggung jawab diambil dari aset | Maya Kusuma, tidak diketik ulang |
| Odometer diformat | 84.210 km |
| Aset yang sudah jadi kendaraan tidak bisa dipilih dua kali | Ya, lewat `whereDoesntHave('vehicle')` |
| Dokumen pajak lewat jatuh tempo | "Lewat 18 hari", merah |
| Dokumen KIR mendekati | "Sisa 24 hari", kuning |
| Tombol Perpanjang menyalin nomor dan penerbit | Ya, dan mengusulkan 20 Agustus 2027 dari 20 Agustus 2026 |
| Biaya sengaja dikosongkan saat perpanjang | Ya |
| Baris lama berubah keadaan setelah diperpanjang | "Berlaku sekarang" menjadi "Sudah diperpanjang" |
| Ringkasan dan subjudul ikut bergeser | Yang terdekat berpindah dari pajak ke KIR |
| Biaya dokumen 12 bulan terakhir | Rp 3.995.000, yaitu KIR Rp 275.000 ditambah pajak baru Rp 3.720.000; pajak lama 20 Agustus 2025 benar benar di luar jendela 12 bulan |
| Dasbor tab Kendaraan | Dipakai 1, sudah lewat 0, jatuh tempo 30 hari 1, biaya Rp 4,00 juta dengan angka penuh di bawahnya |
| Dasbor tidak menghitung dokumen yang sudah diperpanjang | Ya, "sudah lewat" tetap 0 padahal ada baris pajak 2026 yang tanggalnya sudah lewat |

## 5. Cacat yang ditemukan dan diperbaiki

**D-3. Seluruh relation manager di halaman Lihat tidak bisa diisi.**
Filament membuat relation manager pada halaman `ViewRecord` menjadi hanya baca secara bawaan,
dan tombol Tambah, Ubah, serta Hapus di dalamnya hilang tanpa pesan apa pun. Akibatnya dokumen
kendaraan tidak bisa dicatat sama sekali, dan cacat yang sama diam diam juga memblokir unggah
foto pada permintaan perbaikan sejak kiriman G, yang di Delivery Gate 8 tercatat sebagai
"belum diuji".

Diperbaiki di tingkat panel dengan `readOnlyRelationManagersOnResourceViewPagesByDefault(false)`,
bukan per relation manager, karena halaman detail di aplikasi ini memang meja kerja: halaman
kendaraan adalah tempat mencatat perpanjangan, halaman permintaan adalah tempat mengunggah foto,
halaman jadwal adalah tempat menutup kunjungan. Izin sebenarnya tetap dijaga `visible()` pada
tiap tombol, yang membaca izin modul.

Diverifikasi dua duanya: tombol "Catat dokumen" muncul di halaman kendaraan, dan tombol
"Unggah foto" muncul pada permintaan `PB/2026/09/0002` yang berstatus menunggu tim GA.

**D-4. Biaya dokumen menulis Rp 0 padahal belum ada yang dicatat.**
Rp 0 dan "belum ada yang dicatat" adalah dua hal berbeda, dan menampilkan yang pertama untuk
yang kedua adalah cara halus mengarang angka (R-17). Sekarang berbunyi "Belum ada biaya yang
dicatat" sampai ada dokumen berbiaya di jendela 12 bulan.

**D-5. Judul kotak perpanjangan mengecilkan singkatan.**
Berbunyi "Perpanjang pajak tahunan dan pengesahan stnk". Huruf besarnya dikembalikan.

**D-6. Judul kotak tambah kendaraan tidak sama dengan tombolnya.**
Tombol berbunyi "Daftarkan kendaraan", kotaknya berbunyi "Buat Kendaraan". Kotaknya disamakan.

## 6. Delivery Gate checklist

**Blok 1, yang membatalkan kiriman**

- Tombol atau link mati (R-26): tidak ada setelah D-3 diperbaiki.
- Navigasi ke halaman yang tidak ada (R-24): tidak ada. Tautan dari kendaraan ke asetnya diuji.
- Angka atau klaim yang dikarang (R-17, R-18, R-36, R-38): tidak ada setelah D-4 diperbaiki.
  Seluruh angka jatuh tempo dan biaya dihitung dari basis data.

**Blok 2, yang harus diperbaiki sebelum diserahkan**

- Empty state (R-27): ada dan spesifik untuk daftar kendaraan, riwayat dokumen, dan tabel dasbor.
- Em dash (R-02): tidak ada.
- CTA generik (R-15, R-16): tidak ada. Tombolnya berbunyi "Daftarkan kendaraan",
  "Catat dokumen", "Perpanjang", "Simpan perpanjangan".
- Tap target 44px (R-03): mengikuti komponen Filament yang sudah dipakai modul lain.

**Blok 3, catatan**

- Loading state: relation manager dokumen memakai `$isLazy = false`, sama seperti modul lain.
- Mobile: belum diperiksa pada kiriman ini.

## 7. Data uji yang ditinggalkan

Bisa dihapus kapan saja, dan menghapus kendaraannya sekaligus membuang ketiga dokumennya.
Asetnya sendiri tidak ikut terhapus.

- Kendaraan `B 1234 UJI`, catatannya ditandai `[DATA UJI]`
- Tiga dokumen di bawahnya: pajak 2026, pajak 2027, KIR 2026
- Permintaan perbaikan `PB/2026/09/0002`, judulnya ditandai `[DATA UJI]`

## 8. Status

**LULUS.** Empat cacat ditemukan dan diperbaiki, satu di antaranya (D-3) ternyata juga
memblokir fitur kiriman G yang sebelumnya tercatat belum teruji.

Belum dibangun di Tahap 4: pemesanan pool car, log perjalanan dengan sopir, dan pengisian BBM
per pengisian dengan odometer. Ketiganya sudah disiapkan tempatnya lewat kolom `usage_mode`,
`default_driver_employee_id`, dan `last_odometer_km`.
