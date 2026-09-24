# DESIGN.md

Arah gaya untuk proyek ini. File ini adalah pasangan wajib dari `ANTISLOP.md`.

`ANTISLOP.md` hanya bisa menolak yang buruk. File inilah yang menentukan yang baik.

Status: **diisi oleh Claude pada 6 September 2026 atas permintaan pemilik proyek. Bagian 3 sampai 6
ditulis ulang 24 September 2026 saat arah gaya diganti ke Vuexy atas permintaan pemilik proyek.**
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

**Arah gaya:** Vuexy (templat admin dasbor Pixinvent), dipilih pemilik proyek pada
24 September 2026 menggantikan arah "kertas hangat" sebelumnya. Referensinya
[demo Vuexy eCommerce](https://demos.pixinvent.com/vuexy-vuejs-admin-template-vue2/demo-1/dashboard/ecommerce).
Tiruan sengaja atas permintaan pemilik, jadi R-30 tidak dilanggar, tetapi yang ditiru adalah
bahasa visualnya (warna, huruf, bentuk kartu), bukan isi, ilustrasi, atau logonya.

**Tiga kata sifat:** ringan, rapi, bersahabat

**Tiga kata sifat yang HARUS dihindari:** berat, ramai, dingin

**Bahasa visual:** permukaan putih yang melayang di atas latar abu sangat muda. Semua yang
berisi (navbar, kartu formulir, tabel, statistik, dialog) adalah kartu putih bersudut 6 piksel
tanpa garis tepi, dipisahkan dari latar oleh bayangan lembut. Ungu hanya di tempat yang
menuntut perhatian: tombol utama, menu aktif, tab aktif, fokus.

**Motif identitas:** dua gestur Vuexy yang diulang di seluruh aplikasi.

1. **Pil ungu bercahaya** menandai "Anda di sini": menu sidebar aktif dan tab dasbor aktif.
   Satu satunya elemen bercahaya di aplikasi.
2. **Lingkaran ikon berwarna tipis** (latar warna 12 persen, ikon warna penuh) untuk ikon
   statistik dasbor, ubin peluncur menu, dan avatar inisial. Warnanya membawa arti: pada
   statistik ia mengikuti kondisi data (merah terlambat, hijau aman).

Angka, kode aset, dan nomor dokumen tetap memakai tabular numerals, dipertahankan dari arah
sebelumnya karena alasannya fungsi.

---

## 4. Palet

| Peran | Nilai | Alasan singkat |
|---|---|---|
| Inti 1 | `#7367F0` ungu Vuexy | Warna merek Vuexy. Dipasang di tingkat 400 dan dipakai untuk bentuk tanpa teks kecil: cahaya menu aktif, garis fokus, lingkaran ikon |
| Inti 1, tombol | `#5D4FE6` / hover `#685BED` | Ungu yang sama digelapkan sedikit karena tulisan putih di atas `#7367F0` hanya 4,26:1. Di sini 5,68:1 dan 4,88:1 |
| Inti 2 | `#5E5873` | Abu ungu tua Vuexy untuk judul halaman, judul kartu, dan kepala tabel |
| Netral teks | `#6E6B7B` | Warna teks dasar Vuexy, 5,18:1 di atas putih |
| Netral latar | `#F8F8F8` / garis `#EBE9F1` | Latar halaman dan garis antar baris tabel Vuexy |
| Status | `#28C76F` / `#EA5455` / `#FF9F43` / `#00CFE8` | Sukses, bahaya, peringatan, info Vuexy. Merah digelapkan ke `#C03234` untuk tombol bertulisan putih |

Warna status hanya muncul pada badge, ikon status, lingkaran ikon statistik, dan grafik status.
Tidak dipakai sebagai hiasan.

**Tema gelap:** ikut dikirim, karena Filament sudah menyediakan tombol tema di menu pengguna
dan Vuexy sendiri punya tata letak gelap. Latar `#161D31`, kartu `#283046`, garis `#3B4253`,
persis dark layout Vuexy. Di Filament kedua warna itu adalah tingkat 950 dan 900 palet gray,
jadi tema gelap terbentuk dari palet yang sama tanpa daftar warna kedua.

### Bagaimana nilai di atas dipasang ke Filament

Palet ditulis lengkap sebelas tingkat per peran di `AdminPanelProvider::colors()`, tidak
diturunkan dari satu warna dasar, karena `Color::hex()` membangun tingkatnya sendiri tanpa bisa
diatur kontrasnya. Filament memilih warna tulisan tombol dari palet dengan syarat 4,5:1, jadi
tombol hijau, jingga, dan biru muda otomatis bertulisan gelap.

Bentuk (bayangan, sudut, navbar melayang, menu aktif, tabel) ada di `resources/css/gais.css`.
Aturan Filament berada di dalam `@layer components` sedangkan gais.css tidak berlapis, jadi
pemilih sederhana di gais.css menang tanpa trik kekhususan. Setiap perubahan gais.css disalin
juga ke `public/css/app/gais.css` (atau jalankan `php artisan filament:assets`).

**Gradien:** dua, dua duanya berfungsi. Pil menu aktif (`#5D4FE6` ke `#685BED`, tulisan putih
lolos di seluruh panjangnya) dan pudar latar di belakang navbar melayang supaya isi yang
tergulung tidak menabrak kartunya. Tidak ada gradien dekoratif lain.

---

## 5. Tipografi

| Peran | Typeface | Alasan (R-06) |
|---|---|---|
| Heading dan body | Montserrat 400/500/600/700 | Huruf Vuexy. Geometris dan lebar, memberi rasa ringan dan ramah yang menjadi ciri templat itu. Diinangkan sendiri di `public/fonts/montserrat` supaya jaringan kantor tanpa internet tetap mendapat huruf yang sama |
| Mono | monospace sistem | Hanya untuk kode aset, nomor dokumen, dan nilai barcode, bawaan kolom Filament |

**Skala ukuran:** 12 / 13 / 14 / 16 / 18 / 24 (px). Judul halaman 24 piksel ketebalan 500,
judul kartu 17,6 piksel ketebalan 500, angka statistik 24 piksel ketebalan 600.

**Huruf kapital:** hanya di dua tempat, judul kelompok menu sidebar dan kepala tabel, keduanya
12 piksel dengan jarak huruf 0,04em. Mengikuti Vuexy, dan di tabel ia memisahkan baris label
dari baris data tanpa warna tambahan. Tidak ada kapital dengan jarak huruf lebar di tempat lain.

**Lebar sidebar:** 20rem. Montserrat lebih lebar dari huruf sebelumnya, dan nama menu terpanjang
(Corrective Maintenance beserta badge jumlahnya) baru menyisakan 21 piksel pada lebar ini.

---

## 6. Dial Liveliness

| Dial | Level | Artinya di proyek ini |
|---|---|---|
| ENERGY | 2 | Seimbang. Ungu dan bayangan membuat layar lebih hidup dari sebelumnya, tetapi tetap satu titik fokus per layar dan tanpa ilustrasi dekoratif |
| RHYTHM | 1 | Grid seragam yang disengaja. Semua halaman modul memakai pola sama: judul, filter, tabel, aksi |
| MOTION | 1 | Hover dan focus saja: butir menu bergeser 5 piksel saat disorot, tombol berwarna mendapat bayangan sewarna saat disorot. Keduanya dimatikan untuk `prefers-reduced-motion`. Tidak ada animasi muncul atau parallax |

**Alasan setting ini:** ENERGY naik satu tingkat karena itulah yang diminta dari Vuexy, rasa
dasbor yang lebih hidup. RHYTHM dan MOTION tetap 1 karena alasannya tidak berubah: aplikasi
dipakai 8 jam sehari, staf GA berpindah antar modul puluhan kali sehari, dan tombol yang sama
harus ada di tempat yang sama.

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
(brand name "GAIS", ungu Vuexy ketebalan 600) di tempat logo akan diletakkan. Logo Vuexy tidak
dipakai, dan tidak ada logo yang dibuat sendiri.

**Foto atau avatar:** tidak dipakai. Avatar memakai inisial nama pengguna bergaya avatar Vuexy:
latar warna tipis, inisial berwarna.

**Ilustrasi:** tidak dipakai. Ilustrasi karakter di demo Vuexy tidak disalin (R-22).

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
