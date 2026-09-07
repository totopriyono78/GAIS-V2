# Delivery Gate 8: Kiriman G, permintaan perbaikan

Tanggal: 7 September 2026
Cakupan: Tahap 4 bagian pertama, modul permintaan perbaikan dan jenis permintaan
Mode penerapan: DURING

---

## 1. Design Read

Saya baca ini sebagai **layar antrean kerja untuk tiga pembaca yang berbeda**, bukan satu
formulir untuk semua orang.

- Karyawan membukanya untuk satu pertanyaan: sekarang menunggu siapa.
- Kepala departemen membukanya untuk satu pekerjaan: menandatangani atau menolak.
- Tim GA membukanya untuk satu tindakan: menerima dan menugaskan.

Gaya visual mengikuti modul pemeliharaan yang sudah ada, karena tiket dan perintah kerja
dibaca berurutan oleh orang yang sama. Dial: ENERGY rendah, RHYTHM tenang, MOTION nyaris nol.
Layar ini dibuka saat ada yang rusak, dan orang yang sedang kesal tidak butuh animasi.

## 2. Alasan keputusan (R-31)

| Keputusan | Alasan satu baris |
|---|---|
| Warna | Meneruskan palet Filament yang sudah dipakai modul lain; status memakai warna yang sama artinya di seluruh aplikasi, sehingga kuning selalu berarti menunggu dan merah selalu berarti mendesak |
| Layout | Tiga bagian berurutan waktu (apa rusak, di mana, siapa melapor) karena itulah urutan orang menceritakan kerusakan |
| Tipografi | Nomor tiket monospace supaya kolom nomor sejajar dan mudah dibandingkan sekilas |
| Spacing | Sama dengan perintah kerja, karena keduanya sering dibuka berurutan dan perbedaan spacing terbaca sebagai aplikasi yang berbeda |
| Kartu | Halaman detail memakai dua bagian, Permintaan dan Perjalanan tiket, supaya isi laporan terpisah dari riwayat penanganannya |
| Ilustrasi | Tidak ada. Empty state memakai kalimat yang menjelaskan langkah berikutnya, bukan gambar |

## 3. Yang diverifikasi jalan

Diuji di browser pada basis data nyata di mesin pemilik proyek, dengan tiket yang
dibuat pemilik proyek sendiri lewat layar aplikasi.

| Yang diuji | Hasil |
|---|---|
| Menu Permintaan perbaikan dan Jenis permintaan muncul setelah sync izin | Ya |
| Nomor tiket otomatis | `PB/2026/09/0001` |
| Departemen dibekukan di tiket | Information Technology, ikut tampil di daftar dan detail |
| Lompatan persetujuan tercatat dengan alasannya | "Lewat persetujuan. Departemen pemohon belum punya kepala departemen." |
| Tiket tanpa aset | "Bukan aset tertentu", tersimpan |
| Tombol Terima membuat perintah kerja korektif | `WO/2026/09/0005`, status dibuka |
| Tiket ikut berubah status | Menunggu tim GA menjadi Sedang dikerjakan |
| Penerima dan waktunya tercatat | 07 September 2026, 06:37 oleh Administrator |
| Batas waktu saat kategori belum punya target | "Kategorinya belum punya target waktu", bukan angka nol |
| Tombol yang tidak lagi berlaku menghilang | Setelah diterima, Terima, Tolak, Batalkan, dan Ubah permintaan hilang (R-26) |
| Tautan ke perintah kerja dari halaman tiket | Ada, menuju halaman yang benar (R-24) |
| Empty state lampiran | Spesifik, menjelaskan gunanya foto sebelum diperbaiki (R-27) |
| Lencana menu | Permintaan perbaikan 1, Perintah kerja 1, keduanya dihitung dari basis data (R-17) |

## 4. Cacat yang ditemukan dan diperbaiki

**D-1. Perintah kerja menolak tiket yang tidak menyebut aset.**
`work_orders.asset_id` dibuat wajib isi waktu perintah kerja hanya bisa lahir dari kartu aset.
Tiket boleh tidak menyebut aset, jadi tombol Terima gagal dengan
`SQLSTATE[23502] null value in column "asset_id"`.
Diperbaiki dengan migrasi `2026_09_07_230300` yang membuat kolom itu boleh kosong, bukan dengan
mewajibkan pemohon memilih aset. Kalau diwajibkan, orang akan menunjuk aset terdekat yang
kebetulan ada di daftar, dan riwayat pemeliharaan aset itu terisi pekerjaan yang tidak pernah
menyentuhnya. Ikut disesuaikan: kolom Aset di formulir perintah kerja tidak lagi wajib, tabelnya
menulis "Bukan aset tertentu", dan pertanyaan kondisi aset di tombol Selesaikan disembunyikan
kalau perintah kerjanya memang tidak menyebut aset.

**D-2. Izin pemeliharaan tidak pernah diberikan ke role mana pun.**
Kiriman F membuat modul `vendors`, `maintenance_schedules`, dan `work_orders`, tetapi
`RoleSeeder` tidak pernah menambahkannya ke Manajer GA maupun Staf GA, jadi selain administrator
tidak ada yang bisa membuka menu itu. Ditemukan saat menyusun izin modul baru, bukan saat diuji.
Sudah ditambahkan.

## 5. Yang belum terjawab

**T-1. Kotak dialog tidak terbuka saat tombolnya diklik.**
Di browser yang saya pakai, seluruh kotak dialog Filament di aplikasi ini tidak pernah terbuka:
elemen kotaknya masuk ke halaman tetapi tingginya nol dan Alpine tidak menginisialisasinya.
Ini bukan gejala kiriman G, karena tombol Selesaikan di perintah kerja dan Tambah rekanan di
menu Rekanan berperilaku sama. Seluruh berkas JavaScript lokal termuat tanpa gagal, dan
`filamentModal` terbukti terdaftar saat diuji langsung, jadi ini bukan aset yang belum
diterbitkan. Semua pengujian di atas dijalankan dengan memanggil Livewire langsung, bukan
lewat klik, sehingga hasilnya tetap sah untuk perilaku di sisi server.

Perlu dipastikan pemilik proyek: apakah kotak dialog terbuka normal di browser miliknya. Kalau
ya, ini keterbatasan alat browsing saya dan bukan cacat aplikasi. Kalau tidak, ini cacat yang
memblokir separuh aplikasi dan harus jadi pekerjaan berikutnya.

**T-2. Dua sumber daya luar gagal dimuat.**
`fonts.bunny.net` untuk huruf IBM Plex Sans dan `ui-avatars.com` untuk avatar pengguna tidak
bisa dijangkau. Di kantor tanpa akses internet keluar, huruf akan jatuh ke huruf bawaan sistem
dan avatar tidak muncul. Perlu diputuskan apakah keduanya diinangkan sendiri.

**T-3. Belum diuji:** unggah foto pada tiket, penolakan tiket, pembatalan oleh pemohon, jalur
persetujuan yang benar benar melewati kepala departemen (belum ada departemen yang punya kepala),
penyempitan daftar lewat akun tanpa izin `read_all`, dan penutupan tiket saat perintah kerjanya
diselesaikan atau dibatalkan.

## 6. Delivery Gate checklist

**Blok 1, yang membatalkan kiriman**

- Tombol atau link mati (R-26): tidak ada. Tombol tindakan hilang begitu statusnya tidak lagi berlaku.
- Navigasi ke halaman yang tidak ada (R-24): tidak ada. Tautan ke perintah kerja diuji.
- Angka atau klaim yang dikarang (R-17, R-18, R-36, R-38): tidak ada. Target waktu sengaja
  dikosongkan, dan layar mengatakannya apa adanya alih alih menampilkan nol jam.

**Blok 2, yang harus diperbaiki sebelum diserahkan**

- Empty state (R-27): ada dan spesifik untuk tiap tabel, termasuk lampiran dan jenis permintaan.
- Em dash (R-02): tidak ada.
- CTA generik (R-15, R-16): tidak ada. Tombolnya berbunyi "Ajukan permintaan",
  "Terima dan buat perintah kerja", "Setujui permintaan".
- Tap target 44px (R-03): mengikuti komponen Filament yang sudah dipakai modul lain.

**Blok 3, catatan**

- Loading state: relation manager lampiran memakai `$isLazy = false`, sama seperti modul lain,
  karena server pengembangan hanya melayani satu permintaan pada satu waktu.
- Mobile: belum diperiksa pada kiriman ini.

## 7. Status

**LULUS bersyarat.** Perilaku sisi server terbukti benar dari ujung ke ujung. Syaratnya adalah
T-1: kalau kotak dialog memang tidak terbuka di browser pemilik proyek, kiriman ini belum bisa
dipakai orang sungguhan dan perbaikannya mendahului pekerjaan berikutnya.
