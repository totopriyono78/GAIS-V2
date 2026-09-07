# DESIGN.md

Arah gaya untuk proyek ini. File ini adalah pasangan wajib dari `ANTISLOP.md`.

`ANTISLOP.md` hanya bisa menolak yang buruk. File inilah yang menentukan yang baik.

Status: **diisi oleh Claude pada 6 September 2026 atas permintaan pemilik proyek, menunggu review.**
Bagian yang belum bisa saya ketahui sendiri (logo, angka nyata, testimoni) sengaja ditulis apa adanya
sebagai belum ada, sesuai R-23 dan R-38.

---

## 1. Produk

**Nama:** GAIS (General Affair Information System)

**Satu kalimat:** Aplikasi untuk membantu Bagian General Affair perusahaan agar proses lebih mudah, cepat, dan efektif.

**Stack:** Laravel 13, PostgreSQL, Livewire 4, Filament 5, Tailwind 4

**Halaman/layar yang ada (Tahap 1 dan 2):**

| Rute | Isi |
|---|---|
| `/` | Dialihkan ke `/admin` |
| `/admin/login` | Masuk |
| `/admin` | Dasbor, ringkasan angka nyata dari basis data |
| `/admin/roles` | Role dan matriks izin per modul per aksi |
| `/admin/users` | Pengguna sistem, penetapan role, override izin |
| `/admin/modules` | Registri modul, sumber kebenaran daftar izin |
| `/admin/asset-categories` | Kategori aset, awalan kode, umur ekonomis |
| `/admin/assets` | Daftar aset, filter, impor CSV, cetak label |
| `/admin/assets/create` | Tambah aset |
| `/admin/assets/{id}/edit` | Ubah aset |
| `/admin/stock-opnames` | Daftar sesi stock opname |
| `/admin/stock-opnames/create` | Buat sesi opname |
| `/admin/stock-opnames/{id}/edit` | Sesi opname beserta daftar target pemeriksaan |
| `/cetak/label-aset` | Halaman cetak label barcode, di luar panel supaya polos |
| `/admin/employees` | Data karyawan |
| `/admin/departments` | Departemen |
| `/admin/locations` | Lokasi di Head Office (gedung, lantai, ruangan, area) |
| `/admin/settings` | Pengaturan sistem |
| `/admin/audit-logs` | Jejak audit |
| `/admin/profile` | Profil dan ganti kata sandi pengguna yang sedang masuk |

Daftar ini menegakkan R-24. Menu navigasi hanya boleh berisi rute yang ada di tabel ini.
Modul tahap berikutnya (ATK, kendaraan, tiket, biaya) tidak boleh muncul di menu sebelum halamannya ada.

---

## 2. Audiens

**Siapa:** staf admin GA, manajer GA, kepala unit, dan karyawan yang mengajukan permintaan.

**Konteks pemakaian:** desktop, dipakai harian sepanjang jam kerja, sering berdampingan dengan Excel dan email.
Layar kantor umum 1366x768 sampai 1920x1080.

**Level kepercayaan yang dibutuhkan:** tinggi. Aplikasi ini memegang data aset perusahaan dan angka biaya
yang dipakai untuk keputusan finansial, jadi tampilan harus terasa tenang dan akurat, bukan atraktif.

---

## 3. Karakter Visual

**Tiga kata sifat:** tenang, presisi, hangat

**Tiga kata sifat yang HARUS dihindari:** futuristik, playful, korporat kaku

**Referensi rasa (bukan untuk dijiplak):** buku manual teknik terbitan lama, formulir kertas kantor yang
dicetak rapi, papan inventaris kayu di gudang. Rasanya: kertas hangat, garis tegas, angka jelas.

**Motif identitas:** setiap judul seksi dan setiap baris yang menunggu tindakan Anda ditandai
**garis aksen terracotta setebal 3px di sisi kiri**. Selain itu, semua angka, kode aset, dan nomor dokumen
selalu memakai tabular numerals sehingga kolom angka berbaris lurus. Dua kebiasaan ini yang membuat GAIS
tetap dikenali kalau logonya dilepas.

---

## 4. Palet

| Peran | Nilai | Alasan singkat |
|---|---|---|
| Inti 1 | `#17505E` | Petrol tua untuk semua aksi utama, nav aktif, dan fokus. Tenang, bukan biru neon, dan kontras putih di atasnya 8.9:1 |
| Inti 2 | `#3A4A50` | Slate hangat untuk teks sekunder, border struktural, dan header tabel. Menjaga tabel padat tetap terbaca tanpa menambah warna |
| Aksen | `#A2542F` | Terracotta. Hanya untuk dua hal: garis motif 3px di kiri header seksi, dan penanda baris atau badge "menunggu tindakan Anda". Tidak dipakai di tempat lain |
| Netral gelap | `#1C1A17` | Teks utama. Hitam kehangatan, bukan hitam murni, supaya tidak keras dibaca 8 jam |
| Netral terang | `#FAF7F2` | Latar. Putih kertas hangat, mengurangi silau dibanding putih murni |
| Sukses / Error | `#1F6B3F` / `#A32020` | Status selesai atau kondisi baik, dan status gagal atau kondisi rusak |

Warna status (sukses, error, dan kuning peringatan bawaan Filament) diperlakukan sebagai sinyal fungsional,
bukan bagian palet, sama seperti netral. Aturannya: warna status hanya boleh muncul pada badge status dan
ikon status, tidak pernah sebagai warna dekoratif.

### Bagaimana nilai di atas dipasang ke Filament

Nilai di tabel itu warna penuhnya, bukan satu satunya warna yang tampil. Filament butuh sebelas tingkat
untuk tiap peran, dan tingkat itu ditulis lengkap di `AdminPanelProvider::colors()`, tidak diturunkan
dari satu warna dasar. Alasannya ditemukan saat verifikasi 6 September 2026: `Color::hex()` hanya
mengambil rona warna yang diberikan lalu memakai kepekatan bawaannya sendiri, sehingga `#17505E`
yang kepekatannya 0,062 dalam OKLCH keluar di layar dengan kepekatan 0,169, yaitu sian menyala.
Warna penuh dipasang di tingkat yang tingkat terangnya paling dekat: 700 untuk Inti 1 dan Inti 2,
600 untuk sukses, error, dan peringatan.

Perlu diketahui saat membaca layar: Filament 5 melukis tombol terang dengan tingkat 400 sebagai latar
dan tingkat 950 sebagai tulisannya, bukan warna penuh dengan tulisan putih. Jadi tombol utama tampil
sebagai petrol muda. Warna penuh `#17505E` tetap muncul di tempat yang disebut tabel di atas: teks,
nav aktif, dan garis fokus. Kontras 8.9:1 berlaku untuk pemakaian itu.

R-01: tidak ada gradien di aplikasi ini. Permukaan diberi bidang warna rata karena tabel padat lebih terbaca
di atas bidang rata.

**Tema:** terang saja untuk Tahap 1.

Alasan (R-21): pengguna memakai aplikasi ini di ruang kantor ber-lampu terang sepanjang hari, dan hampir
semua dokumen pendamping mereka (formulir, faktur, laporan cetak) berlatar putih. Tema gelap bukan
kebutuhan mereka, jadi lebih jujur mengirim satu tema yang benar-benar rapi daripada dua tema setengah jadi.
Ini keputusan, bukan penundaan. Kalau nanti tema gelap dibutuhkan, itu jadi pekerjaan tersendiri dengan
verifikasi kontras ulang di kedua mode sesuai R-34.

---

## 5. Tipografi

| Peran | Typeface | Alasan (R-06) |
|---|---|---|
| Heading | Source Serif 4 | Serif hangat untuk judul halaman dan judul seksi, memberi karakter "dokumen kantor yang dicetak rapi" dan langsung membedakan GAIS dari dasbor sans generik |
| Body | IBM Plex Sans | Dirancang untuk antarmuka padat data, bentuk huruf jelas di ukuran kecil, dan punya tabular numerals yang dibutuhkan motif identitas |
| Mono | IBM Plex Mono | Hanya untuk kode aset, nomor dokumen, dan nilai barcode. Tidak pernah untuk heading atau body |

**Skala ukuran:** 12 / 13 / 14 / 16 / 20 / 26 / 34 (px)

**Aturan letter-spacing:** heading -0.01em, body 0. Tidak ada label uppercase dengan tracking lebar.
Label kolom tabel memakai huruf kapital di awal kata saja, ukuran 12px, warna Inti 2.

---

## 6. Dial Liveliness

| Dial | Level | Artinya di proyek ini |
|---|---|---|
| ENERGY | 1 | Kalem. Tidak ada hero besar, tidak ada ilustrasi dekoratif, satu focal point per layar |
| RHYTHM | 1 | Grid seragam yang disengaja. Semua halaman modul memakai pola sama: judul, filter, tabel, aksi |
| MOTION | 1 | Hover dan focus saja, plus indikator memuat bawaan Filament. Tidak ada scroll reveal, tidak ada parallax |

**Alasan setting ini:** aplikasi dipakai 8 jam sehari untuk memasukkan dan memeriksa data. Layar yang ramai
dan gerak yang tidak perlu memperlambat kerja dan melelahkan mata. Keseragaman antar halaman di sini adalah
fitur, bukan kemalasan: staf GA berpindah antar modul puluhan kali sehari dan harus menemukan tombol yang
sama di tempat yang sama. Karakter dimasukkan lewat palet, serif pada judul, dan motif garis aksen,
bukan lewat gerak.

---

## 7. Suara dan Copy

**Nada:** langsung, memakai "Anda", tidak menggurui, tanpa lelucon. Pesan kesalahan menyebutkan apa yang
salah dan apa yang harus dilakukan.

**Kata yang dilarang:** selain daftar R-16, hindari juga "sistem terintegrasi", "solusi", "platform",
"digitalisasi", "optimalisasi". Sebut aksinya, bukan kategorinya.

**Contoh CTA yang benar:** "Simpan role", "Tambah karyawan", "Nonaktifkan pengguna", "Lihat jejak perubahan",
"Terapkan izin ke role". Dilarang: "Simpan" tanpa objek kalau konteksnya bisa ambigu, dan semua CTA di daftar R-15.

**Contoh empty state yang benar:** "Belum ada karyawan yang terdaftar. Tambahkan karyawan pertama, atau impor
dari berkas Excel." Bukan "Tidak ada data".

**Bahasa:** Indonesia. Istilah teknis yang sudah lazim dipakai tim GA boleh tetap Inggris (role, dashboard,
barcode, maintenance). Konsisten, jangan campur "peran" dan "role" di layar yang sama.

---

## 8. Aset dan Data Nyata

**Logo:** belum ada. Sampai file logo diberikan, aplikasi memakai nama produk sebagai teks
(brand name "GAIS") dan penanda `[LOGO]` di tempat logo akan diletakkan. Tidak ada logo yang dibuat sendiri.

**Foto atau avatar:** tidak dipakai. Avatar memakai inisial nama pengguna.

**Angka dan statistik yang boleh ditampilkan:** hanya angka yang dihitung langsung dari basis data aplikasi
ini (misalnya jumlah karyawan aktif, jumlah role, jumlah perubahan hari ini). Tidak ada angka yang di-hardcode
di layar. Tidak ada angka contoh di dasbor produksi.

**Testimoni:** belum ada, tidak ada seksi testimoni.

**Klaim keamanan atau kepatuhan:** belum ada sertifikasi apa pun, jangan diklaim. Yang boleh ditulis hanya
fakta teknis yang benar-benar diimplementasikan, misalnya "setiap perubahan data dicatat di jejak audit",
dan itu hanya boleh ditulis setelah fiturnya nyata jalan.

---

## 9. Override terhadap ANTISLOP.md

- Tidak ada override. Semua aturan berlaku penuh.
- Catatan penerapan, bukan override: R-27 (empty, loading, error state) untuk tabel dan form dipenuhi oleh
  perilaku bawaan Filament ditambah teks empty state yang ditulis khusus per modul. Teks bawaan
  "No records found" wajib diganti kalimat spesifik modul.
