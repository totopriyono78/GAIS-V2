# Delivery Gate: GAIS Tahap 2, kiriman A

Aset, kategori aset, kode otomatis, barcode dan cetak label, impor CSV.
Dikeluarkan 6 September 2026, diperbarui hari yang sama setelah format kode aset diubah mengikuti
pola BUMN dan klasifikasi kategori diarahkan ke standar akuntansi.

**Status keseluruhan: BELUM LULUS.** Sama seperti Tahap 1, satu item Hard Gate belum bisa dijawab:
R-35, aplikasi belum dijalankan dengan kode ini. Sisanya sudah terpenuhi, dan dua bagian yang paling
rawan salah sudah saya uji terpisah sebelum dikirim. Bagian 5 berisi daftar yang perlu Anda coba.

---

## 1. Design Read

> Saya baca ini sebagai layar kerja harian pencatatan inventaris untuk staf GA, ditambah satu halaman
> cetak yang hasilnya ditempel ke barang fisik, gaya dokumen kantor yang dicetak rapi,
> dial ENERGY 1 / RHYTHM 1 / MOTION 1.

Dial tidak berubah dari Tahap 1 karena ini modul dalam aplikasi yang sama. Halaman cetak label
mengikuti dial yang sama dengan cara yang berbeda: tidak ada dekorasi sama sekali, karena apa pun yang
dicetak di stiker menghabiskan ruang yang dibutuhkan barcode.

## 2. Alasan satu baris tiap keputusan besar (R-31)

| Keputusan | Alasan |
|---|---|
| Kode aset berbentuk FIN-1201-2026-0001 | Mengikuti pola yang lazim dipakai BUMN: kode departemen, kode akun COA, tahun perolehan, nomor urut. Kodenya langsung menunjukkan siapa pemakainya dan masuk akun mana, jadi rekonsiliasi dengan finance bisa dilakukan dari stikernya saja |
| Departemen dan nomor akun COA jadi wajib diisi | Keduanya membentuk kode aset. Kalau boleh kosong, kodenya tidak bisa dibentuk |
| Nomor akun COA tidak diisi seeder standar | Bagan akun berbeda di tiap perusahaan dan nomornya ikut tercetak di stiker. Salah tebak berarti seribu stiker harus dicetak ulang |
| Nomor akun contoh dipisah ke perintah tersendiri | Demo butuh data yang jalan, produksi butuh angka yang benar. Memisahkannya membuat keduanya bisa dilayani tanpa yang satu mencemari yang lain |
| Nomor akun contoh diberi penanda dan badge kuning | Supaya orang yang membuka layar kategori tahu angka itu belum final, tanpa harus membaca dokumentasi |
| Nomor urut dihitung per departemen, akun, dan tahun | Sesuai pilihan Anda, dan membuat nomor tetap pendek serta mudah dibaca |
| Kelas aset mengikuti PSAK 216, masa manfaat dari PMK 72 Tahun 2023 | Angka masa manfaat punya dasar yang bisa ditunjukkan ke auditor, bukan tebakan |
| Kelompok pajak disimpan sebagai kolom sendiri | Masa manfaat bisa diturunkan otomatis dan terlihat asalnya, tapi tetap bisa ditimpa kalau kebijakan akuntansi perusahaan berbeda dari pajak |
| Tahun diambil dari tanggal perolehan, bukan tanggal input | Kalau memakai tanggal input, seribu aset lama hasil impor akan berkode 2026 semua dan tahunnya kehilangan arti |
| Kode aset dilebarkan jadi 80 karakter | Kode departemen dan nomor akun bisa panjang. Kode 18 karakter menghasilkan lebar modul barcode 0,27 mm di label 70 mm, masih aman untuk printer laser |
| Penghitung nomor dipisah per periode | Impor memasukkan aset 2019 dan 2026 berselang seling, satu penghitung tunggal akan mengulang nomor yang sudah dipakai |
| Barcode ditulis sendiri, bukan memakai paket | Hanya butuh satu simbologi, dan hasilnya harus SVG mm yang ukuran cetaknya pasti. Paket tambahan berarti satu ketergantungan lagi yang harus ikut diperbarui |
| Code 128, bukan Code 39 atau QR | Code 128 lebih rapat sehingga muat di stiker kecil, dan semua pemindai barcode garis membacanya tanpa pengaturan tambahan |
| Garis motif di label dicetak hitam | Label dicetak di printer hitam putih, terracotta akan jadi abu abu pucat dan garisnya hilang |
| Halaman cetak di luar panel | Yang keluar dari printer harus hanya labelnya, tanpa menu dan sidebar |
| Ukuran label disimpan di Pengaturan | Merek stiker yang beredar ukurannya berbeda beda, tim GA harus bisa menyesuaikan sendiri tanpa menunggu pengembang |
| Impor memakai CSV, bukan XLSX | Membaca XLSX butuh paket tambahan, sedangkan Excel menyimpan CSV dalam satu klik |
| Baris bermasalah ditolak, kolom bermasalah dikosongkan | Nama dan kategori tidak bisa ditebak jadi barisnya ditolak, sedangkan lokasi yang belum terdaftar bisa diisi belakangan tanpa menahan seluruh berkas |
| Mode periksa dulu menyala secara bawaan | Impor seribu baris yang salah lebih mahal untuk dibereskan daripada satu klik tambahan |
| Kolom kode aset memakai huruf mono dan bisa disalin | Kode dibaca dan diketik ulang berkali kali saat mencocokkan barang, huruf mono mencegah salah baca antara 0 dan O |
| Tabel aset punya filter "belum punya lokasi" dan "belum punya penanggung jawab" | Setelah impor data berantakan, dua kolom itu yang paling sering kosong dan harus dikejar |
| Kategori yang sudah punya aset tidak bisa dihapus | Kodenya sudah tercetak di stiker yang menempel di barang |
| Sidebar bisa dilipat jadi ikon saja | Layar kantor paling umum masih 1366 piksel, dan sidebar yang selalu terbuka memakan 16rem dari lebar itu |
| Lebar isi halaman dibuat penuh | Tabel aset padat kolom, membatasi lebarnya hanya menyisakan ruang kosong di kanan kiri sambil memaksa tabel digeser |
| Tabel hanya menampilkan kolom yang paling sering dipakai | Sepuluh kolom sekaligus memaksa geser ke samping, dan menggeser tabel untuk membaca satu baris jauh lebih lambat daripada mencentang kolom sekali |

## 3. Yang sudah saya uji sendiri sebelum mengirim

Dua bagian paling rawan salah bisa diuji tanpa menjalankan Laravel, dan sudah saya uji:

**Barcode Code 128.** Hasil keluaran kelas `Code128` saya bandingkan dengan implementasi acuan, lalu
saya decode balik dengan pembaca terpisah: pola awal, checksum, dan pola akhirnya benar, dan teksnya
kembali persis seperti kode aslinya. Setelah format kode diubah, saya hitung ulang kerapatannya:
`FIN-1201-2026-0001` menghasilkan 233 modul, yaitu 0,27 mm per modul di ruang 62 mm pada label 70 mm.

**Pembacaan angka dan tanggal di impor.** Fungsi pembaca angka dan tanggal saya jalankan dengan 20 contoh:
`1.500.000`, `Rp 1.500.000,50`, `1,500,000.50`, `750,5`, `99.5`, `12/03/2024`, `2024-03-12`, dan seterusnya.
Dua kesalahan ketemu di sini dan sudah diperbaiki: angka `750,5` sempat terbaca `7505`, dan tanggal
mustahil `32/13/2024` sempat diterima lalu meluber jadi 1 Februari 2025. Sekarang keduanya benar.

**Konsistensi kode.** Semua berkas PHP lolos `php -l`. Kode modul di setiap Resource cocok dengan
registri modul, seluruh kunci izin yang dipakai di kode ada di registri, dan nama route yang dipanggil
ada di berkas route.

## 4. Delivery Gate

**Blok 1, Hard Gate.** Semua jawaban tidak, kecuali satu.

R-02 tidak ada em dash. R-03 dikerjakan setelah laporan pemilik proyek bahwa isi halaman terpotong
sidebar: sidebar sekarang bisa dilipat, lebar isi dibuat penuh, dan tabel hanya menampilkan kolom
yang paling sering dipakai. Hasilnya belum diverifikasi di layar sempit, halaman cetak sengaja tidak responsif
karena satuannya milimeter kertas, dan itu memang bukan halaman untuk dibaca di ponsel. R-17 satu satunya
angka di layar berasal dari basis data. R-18 tidak ada testimoni. R-23 tidak ada logo atau gambar yang
dibuat sendiri, label hanya memuat nama singkat perusahaan dari Pengaturan. R-24 dua menu baru punya
halaman nyata, dan tombol cetak label hanya muncul untuk yang punya izin cetak. R-25 halaman cetak
memakai tinta hitam di atas kertas putih. R-26 setiap tombol punya perilaku nyata, termasuk tombol
Cetak sekarang yang memanggil dialog cetak peramban, dan tombol unduh laporan yang hanya muncul kalau
laporannya benar benar ada. R-27 kedua tabel punya empty state khusus, dan impor punya jalur kesalahan
yang menjelaskan apa yang salah di baris berapa. R-28 tidak ada FAQ. R-32 belum diverifikasi dengan
keyboard. R-33 tidak ada patching lewat skrip. R-34 tetap satu tema. **R-35 FAIL, belum dijalankan.**
R-36 tidak ada klaim. R-37 arah desain ada dan dipakai. R-38 tidak ada data karangan, dan berkas templat
impor berisi satu baris contoh yang jelas ditandai sebagai contoh.

**Blok 2, Purpose-Gate.** Semua jawaban tidak.
Tidak ada gradien. Ikon dipilih sesuai isinya: kubus untuk aset, kelompok persegi untuk kategori,
printer untuk cetak, panah ke atas untuk unggah. Tipografi mengikuti `DESIGN.md`, dan huruf mono dipakai
tepat pada tempatnya yaitu kode aset dan kode di bawah barcode. Tidak ada pola latar, panah hiasan, blur,
glow, atau ilustrasi. Badge dipakai untuk status dan kondisi yang nyata ada nilainya. Tidak ada animasi
tambahan.

**Blok 3, Liveliness.** Semua jawaban ya.
Dial dinyatakan di bagian 1. Focal point tiap layar adalah tabelnya, dan di halaman cetak adalah lembar
labelnya. Motif identitas ikut ke modul baru dalam dua bentuk: garis aksen di judul seksi pada layar,
dan garis hitam 1mm di sisi kiri tiap label cetak. Angka tetap memakai tabular numerals.

**Blok 4, Kriya dan Quality Locks.** Semua jawaban tidak.
Tidak ada kontrol mati. Tidak ada seksi pengisi. CTA spesifik: "Tambah aset", "Impor dari CSV",
"Unduh templat CSV", "Cetak label terpilih", "Cetak sekarang". Palet tidak bertambah. Layout modul baru
mengikuti pola yang sama dengan modul Tahap 1, sesuai RHYTHM 1.

## 5. Yang perlu Anda jalankan

Setelah menjalankan perintah pembaruan di `SETUP.md`:

1. Buka menu Aset, Kategori aset. Tiga belas kelas standar harus sudah ada, dan kolom Akun aset tetap
   harus berwarna merah bertuliskan belum diisi
2. Jalankan `php artisan gais:coa-demo`, muat ulang halaman kategori. Semua badge nomor akun berubah
   kuning, dan keterangannya berakhiran `[COA CONTOH]`. Kuning berarti terisi tapi belum final
3. Pastikan ada minimal satu departemen dengan kode pendek, misalnya `FIN`, di menu Departemen
4. Buka Daftar aset, tambah satu aset dengan kategori Komputer dan perangkat jaringan, departemen FIN,
   tanpa mengisi tanggal perolehan. Pemberitahuan harus menyebut kode `FIN-1209-2026-0001`
5. Tambah aset kedua yang sama, tapi tanggal perolehannya tahun 2019. Kodenya harus
   `FIN-1209-2019-0001`, bukan melanjutkan nomor 2026. Ini yang membuktikan penghitung per periode bekerja
6. Tambah aset ketiga di kategori Kendaraan roda empat, departemen FIN yang sama. Kodenya harus
   `FIN-1205-2026-0001`, jadi nomor urutnya kembali ke 0001 karena akunnya berbeda
7. Klik Label pada salah satu aset. Halaman cetak harus terbuka di tab baru, barcode terlihat,
   dan tombol Cetak sekarang memunculkan dialog cetak
8. Cetak satu lembar di kertas biasa dulu, tempelkan di atas lembar stiker Anda untuk mengecek posisinya.
   Kalau bergeser, ubah angka margin di menu Pengaturan, tidak perlu mengubah kode
9. Kalau punya pemindai barcode, pindai label yang tercetak. Hasilnya harus persis sama dengan kode
   yang tertulis di bawah barcode
10. Klik Unduh templat CSV, hapus baris contohnya, isi beberapa baris dari data asli Anda, lalu Impor dari CSV dengan
   "Periksa dulu" menyala. Baca laporannya
11. Sengaja rusak tiga baris: kosongkan namanya, isi kategori dengan nama yang tidak terdaftar, dan
    kosongkan departemennya. Ketiganya harus muncul sebagai baris ditolak beserta alasan masing masing
12. Isi lokasi dengan nama yang belum terdaftar. Barisnya harus tetap masuk, dengan peringatan bahwa
    lokasinya dikosongkan. Bedanya dengan langkah 10: lokasi bisa dibereskan belakangan, sedangkan
    departemen dan kategori tidak bisa karena membentuk kode
13. Matikan "Periksa dulu" lalu impor sungguhan. Periksa jumlah yang tersimpan, lalu buka Jejak audit,
    setiap aset baru harus tercatat di sana
14. Buat role uji yang hanya punya izin Lihat pada Daftar aset. Masuk sebagai pengguna itu:
    tombol Tambah, Impor, dan Label tidak boleh muncul, dan membuka alamat `/cetak/label-aset` langsung
    harus ditolak dengan pesan 403

Kirimkan yang gagal apa adanya. Setelah lolos, saya kerjakan kiriman B: stock opname dan inventaris ATK.
