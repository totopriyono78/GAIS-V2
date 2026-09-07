# Delivery Gate: GAIS Tahap 1

Laporan wajib sesuai `ANTISLOP.md`. Dikeluarkan bersama hasil Tahap 1 pada 6 September 2026.

**Status keseluruhan: BELUM LULUS.** Satu item Hard Gate, yaitu R-35, belum bisa dijawab karena kode ini
belum pernah dijalankan. Lingkungan tempat saya bekerja tidak bisa memasang paket Composer dan tidak bisa
menjalankan perintah di komputer Anda. Karena itu Tahap 1 diserahkan sebagai **kode yang siap dipasang,
bukan hasil yang sudah terverifikasi.** Bagian 5 di bawah berisi daftar yang perlu Anda jalankan supaya
gate ini tertutup, dan saya perbaiki apa pun yang gagal.

---

## 1. Design Read

> Saya baca ini sebagai aplikasi internal padat data untuk staf dan manajer GA, gaya dokumen kantor
> yang dicetak rapi, dial ENERGY 1 / RHYTHM 1 / MOTION 1.

## 2. Alasan satu baris tiap keputusan besar (R-31)

| Keputusan | Alasan |
|---|---|
| Warna utama petrol `#17505E` | Warna gelap yang tenang untuk aksi utama, kontras 8.9:1 dengan teks putih, dan bukan biru neon yang jadi default |
| Warna aksen terracotta `#A2542F` | Satu satunya warna hangat, dipakai hanya untuk garis motif dan penanda butuh tindakan, sehingga tetap berarti |
| Latar kertas hangat `#FAF7F2` | Layar dipandang delapan jam sehari, putih murni menambah silau tanpa menambah keterbacaan |
| Layout seragam antar modul | Staf berpindah antar modul puluhan kali sehari, tombol yang sama harus ada di tempat yang sama |
| Serif pada judul, sans pada isi | Judul memberi karakter dokumen kantor, isi tetap memakai huruf yang terbaca di ukuran kecil |
| Angka memakai tabular numerals | Kolom nilai dan kode aset hanya terbaca cepat kalau digitnya selebar sama |
| Spacing bawaan Filament tidak diubah | Kepadatan bawaannya sudah pas untuk tabel panjang, mengubahnya hanya menambah risiko tanpa manfaat |
| Kartu tidak dipakai untuk data | Data GA berbentuk daftar panjang, tabel lebih cepat dipindai daripada kartu |
| Tidak ada ilustrasi | Tidak ada ilustrasi yang punya kaitan nyata dengan pekerjaan GA, jadi lebih baik tidak ada |
| Satu tema terang saja | Ruang kerja terang dan dokumen pendamping berlatar putih, satu tema yang rapi lebih jujur daripada dua tema setengah jadi |

## 3. Blok 1: Hard Gate

| Aturan | Status | Bukti |
|---|---|---|
| R-02 em dash | PASS | Tidak ada karakter em dash di berkas kode, label, helper text, maupun dokumen, kecuali satu contoh di dalam `ANTISLOP.md` yang memang dikecualikan aturannya |
| R-03 mobile | BELUM DIVERIFIKASI | Filament responsif secara bawaan dan tidak ada CSS layout kustom yang menimpanya, tapi saya belum membukanya di layar sempit |
| R-17 statistik | PASS | Satu satunya angka di layar berasal dari `count()` ke basis data di `RingkasanTahapSatu`. Tidak ada angka yang ditulis di kode |
| R-18 testimoni | PASS | Tidak ada seksi testimoni |
| R-23 aset visual | PASS | Tidak ada logo atau avatar yang dibuat. Nama merek diambil dari pengaturan, nilai bawaannya teks GAIS, dan pengaturan `perusahaan.logo` berisi penanda `[LOGO]` |
| R-24 navigasi | PASS | Menu dibuat oleh Filament dari Resource yang ada, dan disembunyikan kalau `canViewAny()` salah. Tidak ada item menu yang ditulis manual, jadi tidak mungkin ada menu ke halaman yang belum dibuat |
| R-25 kontras | PASS untuk palet | Rasio dihitung: teks utama di atas latar sekitar 16:1, putih di atas petrol 8.9:1, di atas terracotta 5.4:1, di atas hijau sukses 6.5:1, di atas merah error 7.6:1. Hasil render belum dicek |
| R-26 elemen interaktif | PASS | Setiap tombol yang ada terhubung ke aksi Filament yang nyata. Tidak ada tombol placeholder |
| R-27 state UI | PASS sebagian | Setiap tabel punya `emptyStateHeading` dan `emptyStateDescription` khusus modulnya. Loading dan error memakai perilaku bawaan Filament dan Laravel, dan itu belum saya lihat sendiri |
| R-28 FAQ | PASS | Tidak ada FAQ |
| R-32 keyboard | BELUM DIVERIFIKASI | Filament bisa dipakai dengan keyboard secara bawaan, dan CSS GAIS menambah indikator fokus, bukan menghapusnya. Belum saya coba dengan Tab dan Escape |
| R-33 patching skrip | PASS | `setup.ps1` hanya menjalankan composer, artisan, npm, dan menyalin berkas. Tidak ada string replace ke berkas source |
| R-34 tema | PASS | Hanya satu tema yang dikirim, dan alasannya ditulis di `DESIGN.md` bagian 4 |
| R-35 verifikasi | **FAIL** | Aplikasi belum pernah dijalankan. Ini yang membuat gate belum tertutup |
| R-36 klaim | PASS | Tidak ada klaim keamanan, kepatuhan, atau performa di layar maupun di dokumen |
| R-37 arah desain | PASS | `DESIGN.md` diisi lebih dulu dan disetujui untuk diisi oleh pemilik proyek, dial dinyatakan di bagian 6 |
| R-38 konten karangan | PASS | Nama perusahaan, alamat, telepon, dan email sengaja dibiarkan kosong. Data contoh dipisah ke `DemoDataSeeder` dan semua recordnya berawalan CONTOH |

## 4. Blok 2, 3, dan 4

**Blok 2, Purpose-Gate.** Semua jawaban tidak.
Tidak ada gradien sama sekali (R-01). Ikon yang dipakai adalah ikon Heroicons yang menggambarkan isi
modulnya: perisai untuk role, kartu identitas untuk karyawan, pin peta untuk lokasi, papan klip untuk
jejak audit (R-04). Tipografi punya alasan tertulis di `DESIGN.md` bagian 5, monospace hanya untuk kode
dan nomor dokumen (R-06). Tidak ada pola latar (R-07). Tidak ada panah di tombol (R-08). Badge hanya
dipakai untuk status dan kode yang nyata, misalnya kode role dan jenis kejadian audit (R-09). Tidak ada
blur (R-10). Bayangan memakai bawaan Filament sebagai penanda elevasi kartu dan modal, tidak ditambah
(R-12). Tidak ada glow (R-13). Tidak ada grid kartu fitur (R-14). Tidak ada animasi tambahan di luar
transisi bawaan Filament, sesuai MOTION 1 (R-19). Tidak ada ilustrasi (R-22).

**Blok 3, Liveliness.** Semua jawaban ya.
Dial dinyatakan di `DESIGN.md` dan di bagian 1 dokumen ini. Hasilnya konsisten dengan ENERGY 1 dan
MOTION 1 karena tidak ada gerak dan tidak ada elemen dekoratif, dan konsisten dengan RHYTHM 1 karena
semua halaman modul memakai pola yang sama secara sengaja. Focal point tiap layar adalah tabel utamanya,
kecuali di halaman role yang focal pointnya adalah matriks izin. Whitespace memakai skala bawaan Filament
yang memisahkan seksi form. Ada satu aksen sengaja, yaitu terracotta pada garis motif. Motif identitas
adalah garis aksen 3px di kiri judul seksi ditambah tabular numerals, keduanya ditulis di
`resources/css/gais.css`.

Satu catatan jujur untuk blok ini: liveliness di Tahap 1 memang tipis, karena isi layarnya masih data
induk dan pengaturan. Karakter yang lebih terlihat baru muncul di Tahap 2 ketika ada kartu aset,
label barcode, dan status kondisi.

**Blok 4, Kriya dan Quality Locks.** Semua jawaban tidak.
Setiap keputusan punya alasan di tabel bagian 2 (C-1). Tidak ada kontrol mati (C-2). Tidak ada seksi
pengisi, dasbor hanya menampilkan angka yang dihitung dan hanya untuk pengguna yang berizin (C-3).
Ketahanan belum bisa dijawab penuh sampai aplikasi dijalankan (C-4, ini terkait R-35). Tidak ada klaim
karangan (C-5). Layout bukan template AI, tidak ada hero, tidak ada bar logo, tidak ada footer empat
kolom (R-05). Radius memakai sistem bawaan Filament dan tidak semuanya pil (R-11). CTA spesifik:
"Tambah role", "Tambah karyawan", "Tambah lokasi", "Ubah nilai" (R-15). Tidak ada buzzword (R-16).
Identitas ada di palet, serif judul, dan motif garis (R-20). Tema terang dipilih dengan alasan (R-21).
Palet dua warna inti plus satu aksen (R-29). Tidak meniru produk lain (R-30). Semua alasan bisa ditulis
satu baris (R-31).

## 5. Yang perlu Anda jalankan untuk menutup R-35

Ini bukan pekerjaan rumah tambahan, ini bagian dari gate yang tidak bisa saya kerjakan dari sini.
Kerjakan setelah `setup.ps1` selesai, lalu kabari hasilnya.

1. Aplikasi terbuka di `http://localhost:8000/admin` dan Anda bisa masuk dengan akun administrator
2. Console peramban bersih, tidak ada error merah, buka dengan F12
3. Setiap menu diklik satu per satu: Role, Pengguna, Modul, Departemen, Lokasi, Karyawan, Pengaturan, Jejak audit
4. Buat satu departemen, satu lokasi, dan satu karyawan, lalu ubah dan hapus salah satunya
5. Buat role baru bernama Uji Coba, centang hanya Karyawan aksi Lihat, simpan
6. Buat pengguna kedua tanpa tanda super admin, beri role Uji Coba, keluar, lalu masuk sebagai dia.
   Yang harus terjadi: hanya menu Karyawan yang muncul, dan tombol Tambah karyawan tidak ada
7. Kembali sebagai administrator, buka Jejak audit. Semua perubahan di langkah 4 sampai 6 harus tercatat
   beserta nama pelakunya
8. Perkecil jendela peramban sampai selebar ponsel. Tidak boleh ada scroll ke samping atau teks terpotong
9. Coba navigasi dengan Tab saja, tanpa mouse. Setiap elemen yang difokus harus terlihat jelas,
   dan modal harus bisa ditutup dengan Escape

Kirimkan yang gagal apa adanya, termasuk pesan errornya. Setelah semuanya lolos, gate ini saya perbarui
menjadi lulus, dan Tahap 2 baru dimulai.
