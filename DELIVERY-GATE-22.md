# Delivery Gate 22: Tampilan panel diganti ke gaya Vuexy

Permintaan pemilik proyek, 24 September 2026: pelajari UI/UX templat Vuexy (demo eCommerce
Pixinvent) lalu perbarui tampilan GAIS memakai gaya itu. Arah gaya di `DESIGN.md` bagian 3
sampai 6 ditulis ulang. Mode antislop: **DURING**, sesuai jawaban pemilik 6 September 2026.

Diverifikasi 24 September 2026 pada salinan proyek yang dijalankan dengan `php artisan serve`
di atas PostgreSQL 16 berisi `gais-lokal.sql`, akun `admin@gais.test`, Chromium 1440x900 dan
375x800, tema terang dan gelap.

**Hasil: LULUS.** Dua cacat lama ikut ditemukan dan diperbaiki di ronde ini (bagian 5).

---

## 1. Design Read

Saya baca ini sebagai **panel kerja harian tim GA** untuk **staf dan manajer GA di layar kantor
1366 sampai 1920 piksel**, gaya **Vuexy: kartu putih melayang di atas latar abu muda, ungu
`#7367F0` sebagai satu satunya warna aksi, huruf Montserrat**, dial **ENERGY 2 / RHYTHM 1 /
MOTION 1**.

Cara membaca Vuexy dari demonya (diukur langsung dari halaman yang sedang berjalan, bukan dari
ingatan): latar `#F8F8F8`, kartu putih bersudut 6 piksel dengan bayangan
`0 4px 24px rgba(34,41,47,.1)`, navbar kartu melayang berjarak dari tepi, sidebar putih dengan
menu aktif berupa pil gradien ungu bercahaya, kepala tabel `#F3F2F7` berhuruf kapital 12
piksel, teks `#6E6B7B`, judul `#5E5873`, avatar berlatar warna 12 persen.

## 2. Alasan satu baris tiap keputusan (R-31)

- **Warna:** ungu Vuexy dipertahankan sebagai identitas, tetapi tombol memakai ungu yang
  digelapkan sedikit (`#5D4FE6`) karena tulisan putih di atas `#7367F0` hanya 4,26:1.
- **Layout:** struktur Filament tidak dibongkar (navbar, sidebar, isi), hanya bentuknya yang
  diganti, supaya setiap halaman modul tetap berpola sama dan tidak ada tombol yang pindah.
- **Tipografi:** Montserrat, huruf Vuexy, diinangkan sendiri karena jaringan kantor tidak
  dijamin punya akses internet.
- **Spacing:** kepala kartu 20/24 piksel dan jarak menu 15 piksel dari tepi, mengikuti Vuexy;
  lebar sidebar naik ke 20rem karena Montserrat lebih lebar dan nama menu tidak boleh terpotong.
- **Kartu:** satu bentuk kartu untuk semua permukaan berisi, garis tepi dilepas karena bayangan
  sudah memisahkan kartu dari latar.
- **Ikon statistik:** tiap angka dasbor mendapat ikon yang menggambarkan isinya (kubus untuk
  aset, truk untuk kendaraan, keranjang untuk barang perlu dipesan), warnanya mengikuti kondisi
  data yang sudah dipilih widget.
- **Ilustrasi:** tidak ada. Ilustrasi karakter di demo Vuexy tidak disalin (R-22).

## 3. Yang berubah

| Berkas | Perubahan |
|---|---|
| `resources/css/gais.css` dan salinannya `public/css/app/gais.css` | Lapisan identitas lama (terracotta, serif) diganti lapisan Vuexy: navbar melayang, sidebar, kartu, tabel, formulir, tombol, badge, kartu statistik, tab, halaman masuk. Peluncur menu dan kartu akun demo memakai variabel Filament, jadi ikut berganti di tema gelap |
| `app/Providers/Filament/AdminPanelProvider.php` | Palet ungu Vuexy sebelas tingkat untuk enam peran, huruf Montserrat, sidebar 20rem, kait footer ganda dihapus |
| `public/fonts/montserrat/` | Baru. Montserrat 400/500/600/700 latin dan latin-ext, woff2, dari @fontsource/montserrat 5.3.0, OFL 1.1 |
| `app/Support/InisialAvatar.php` | Avatar inisial gaya Vuexy: latar warna tipis, inisial berwarna, enam pasangan yang semuanya lolos 4,5:1 |
| `app/Support/PeluncurMenu.php` | Nama warna ubin diganti dari petrol, slate, dan seterusnya ke ungu, nila, jingga, dan seterusnya |
| `app/Filament/Widgets/*Ringkasan.php`, `AsetJatuhTempo.php` | 32 statistik mendapat `->icon()` |
| `app/Filament/Widgets/*Chart.php` | Warna grafik ke palet Vuexy, batang bersudut, pai tanpa garis tepi putih |
| `resources/views/filament/pages/dasbor.blade.php` | Tab dasbor menjadi nav pills Vuexy |
| `resources/views/filament/footer.blade.php` | Warna dari variabel, ditambah aturan tema gelap |
| `DESIGN.md` | Bagian 3 sampai 6 ditulis ulang untuk arah Vuexy, bagian 8 disesuaikan |

Yang sengaja tidak diubah: halaman cetak di `resources/views/cetak/` (label barcode, kartu
riwayat aset, berita acara). Itu dokumen kertas, bukan bagian panel, dan warna petrolnya
dicetak di atas kertas yang sudah dipakai. Kalau ingin ikut ungu, itu pekerjaan tersendiri.

## 4. Delivery Gate

### Blok 1: Hard Gate (semua harus tidak)

| Item | Jawaban | Bukti |
|---|---|---|
| Em dash (R-02) | Tidak | pencarian karakter em dash pada seluruh baris tambahan diff, gais.css, DESIGN.md: 0 |
| Mobile rusak (R-03) | Tidak | 375 piksel: `scrollWidth > innerWidth` salah di dasbor, peluncur, aset, karyawan. Tabel digulir di dalam kartunya sendiri. Butir menu dan tab dasbor 44 piksel |
| Statistik tanpa sumber (R-17) | Tidak | Tidak ada angka baru. Ikon ditambahkan pada statistik yang sudah dihitung dari basis data |
| Testimoni fiktif (R-18) | Tidak | Tidak ada |
| Aset visual dikarang (R-23) | Tidak | Logo tetap teks "GAIS". Logo, ilustrasi, dan foto Vuexy tidak dipakai |
| Link navigasi mati (R-24) | Tidak | Menu tetap lahir dari Resource. Tidak ada item navigasi baru |
| Kontras di bawah AA (R-25) | Tidak | axe-core `color-contrast` pada 21 halaman dan keadaan (masuk, dasbor semua tab, aset, formulir aset, work order, role, pengguna, dokumen, pengaturan, barang, jejak audit, profil, kendaraan, penggantian biaya, peluncur) di tema terang dan gelap: 0 pelanggaran. Tiga pelanggaran yang ditemukan di putaran pertama sudah diperbaiki, lihat bagian 5 |
| Elemen interaktif mati (R-26) | Tidak | Peluncur dibuka dan ditutup Escape, menu pengguna dibuka, dialog hapus dibuka dan ditutup Escape tanpa menghapus, sidebar ponsel dibuka dan ditutup dari tirai |
| Tanpa empty/loading/error state (R-27) | Tidak | State bawaan Filament dan teks empty state per modul tidak disentuh. Pencarian global tanpa hasil menampilkan "Pencarian tidak ditemukan." di kartu Vuexy |
| FAQ generik (R-28) | Tidak | Tidak ada FAQ |
| Keyboard dan fokus (R-32) | Tidak | Tab dari awal halaman: tautan merek mendapat garis fokus ungu 2 piksel. Garis bawaan kotak cari peluncur diganti garis ungu dua lapis, bukan dihapus |
| Patching lewat skrip (R-33) | Tidak | Perubahan ditulis langsung di berkas sumbernya |
| Salah satu tema rusak (R-34) | Tidak | Tema gelap diperiksa di 1440 dan 375 piksel, termasuk peluncur, halaman masuk, dan tabel |
| Belum dijalankan (R-35) | Tidak | Lihat bagian 6 |
| Klaim dikarang (R-36) | Tidak | Tidak ada |
| Tanpa arah gaya (R-37) | Tidak | Arah Vuexy dari pemilik, dicatat di DESIGN.md |
| Konten realistis dikarang (R-38) | Tidak | Tidak ada konten baru |

### Blok 2: Purpose-Gate

| Item | Jawaban | Alasan tertulis |
|---|---|---|
| Gradien atau glow tanpa tujuan (R-01, R-13) | Tidak | Gradien dan cahaya hanya di pil menu aktif (penanda "Anda di sini"). Gradien kedua adalah pudar latar di belakang navbar, fungsional |
| Ikon tidak relevan (R-04) | Tidak | Setiap ikon statistik menggambarkan isinya. Tidak ada sparkle, bintang, atau petir |
| Typeface tanpa alasan (R-06) | Tidak | Montserrat karena huruf Vuexy. Kapital hanya di judul kelompok menu dan kepala tabel, jarak huruf 0,04em |
| Grid latar (R-07) | Tidak | Tidak ada |
| Panah dekoratif (R-08) | Tidak | Tidak ada |
| Badge kapsul tanpa fungsi (R-09) | Tidak | Badge hanya status dan jumlah, sudut 5 piksel, bukan kapsul |
| Glassmorphism (R-10) | Tidak | Tidak ada blur |
| Bayangan di semua komponen (R-12) | Tidak | Satu bayangan kartu untuk permukaan berisi, alasannya: bayangan menggantikan garis tepi sebagai pemisah dari latar. Tombol hanya berbayang saat disorot |
| Kartu identik (R-14) | Tidak | Kartu statistik berbeda warna lingkaran sesuai kondisi datanya |
| Animasi template (R-19) | Tidak | Hanya geser 5 piksel pada hover menu dan bayangan tombol, sesuai MOTION 1, dimatikan untuk `prefers-reduced-motion` |
| Ilustrasi generik (R-22) | Tidak | Tidak ada |

### Blok 3: Liveliness (semua harus ya)

| Item | Jawaban |
|---|---|
| Dial eksplisit | Ya, ENERGY 2 / RHYTHM 1 / MOTION 1 |
| Konsisten dengan dial | Ya |
| Satu focal point per layar | Ya, tombol utama ungu di kepala halaman, di dasbor tab aktif |
| Whitespace struktural | Ya, jarak 1rem di sekeliling navbar dan 28 piksel antar kartu adalah yang membuat kartu terbaca melayang |
| Satu aksen sengaja | Ya, ungu |
| Motif identitas | Ya, pil ungu bercahaya dan lingkaran ikon berwarna tipis |
| Design Read sebelum mulai | Ya, bagian 1 |

### Blok 4: Kriya dan Quality Locks (semua harus tidak)

| Item | Jawaban |
|---|---|
| C-1 keputusan tanpa alasan | Tidak |
| C-2 elemen mati | Tidak |
| C-3 seksi pengisi template | Tidak, tidak ada seksi baru |
| C-4 rusak di state, tema, breakpoint, keyboard | Tidak |
| C-5 klaim dikarang | Tidak |
| Layout template AI (R-05) | Tidak |
| Semua berbentuk pil (R-11) | Tidak, sudut 6 piksel kartu, 5 piksel kontrol, lingkaran hanya ikon dan avatar |
| CTA generik (R-15) | Tidak, label tombol tidak diubah |
| Buzzword (R-16) | Tidak |
| Generik kalau logo diganti (R-20) | Tidak untuk konteks ini. Catatan jujur: bahasa visualnya memang milik Vuexy atas permintaan pemilik, jadi yang khas adalah Vuexy, bukan GAIS |
| Dark mode dipaksa (R-21) | Tidak, terang tetap bawaan. Gelap tersedia dari menu pengguna |
| Palet lebih dari 2 sampai 3 inti + 1 aksen (R-29) | Tidak. Ungu inti, abu ungu inti kedua. Warna status dipakai hanya sebagai sinyal |
| Klon produk populer (R-30) | Tidak tanpa diminta. Tiruan Vuexy adalah permintaan eksplisit pemilik |
| Keputusan tanpa alasan satu baris (R-31) | Tidak |

## 5. Cacat yang ditemukan dan diperbaiki

- **D-38, baris hak cipta dua kali di halaman masuk.** Cacat lama, bukan dari ronde ini: sejak
  Filament 5.7 tata letak sederhana ikut menggambar kait `FOOTER`, sehingga kait
  `SIMPLE_LAYOUT_END` yang dipasang untuk halaman masuk membuatnya ganda. Kait kedua dihapus.
- **D-39, teks petunjuk kotak isian di bawah AA.** Cacat lama: petunjuk memakai gray-400
  (sekitar 2,5:1 di palet petrol). Sekarang gray-500, 5,18:1. Berlaku juga untuk petunjuk
  pilihan dan sel tabel kosong.
- Temuan axe putaran pertama, sudah diperbaiki: tulisan badge jingga 4,44:1 (warning-800
  digelapkan ke `#875833`, sekarang 5,26:1), sel tabel kosong di tema gelap 2,53:1, catatan
  jumlah menu di peluncur tema gelap 2,53:1.

## 6. Bukti verifikasi (R-35)

- Aplikasi dijalankan, 42 tangkapan layar diambil dan yang utama diperiksa langsung (terang dan gelap, 1440 dan 375).
- Console: tidak ada error atau `pageerror` di seluruh alur uji.
- Tombol dicoba: masuk, peluncur (buka, Escape), menu pengguna, dialog hapus (buka, Escape,
  data tidak terhapus), sidebar ponsel (buka, tutup), tab dasbor.
- `php artisan test`: 18 lulus, 1 gagal. Yang gagal adalah `ExampleTest` bawaan Laravel yang
  mengharapkan `/` menjawab 200, padahal `/` memang dialihkan ke `/admin` sejak Tahap 1. Gagal
  yang sama sebelum ronde ini.
- Setelah menarik perubahan: jalankan `php artisan filament:assets` atau pastikan
  `public/css/app/gais.css` ikut tersalin, lalu tekan Ctrl+F5 sekali, karena alamat berkas gaya
  memakai nomor versi Filament dan peramban bisa masih menyimpan versi lama.
