# CLAUDE.md

Entry point untuk agen di proyek ini. File ini sengaja pendek. Tugasnya hanya mengarahkan
ke file aturan yang relevan, supaya konteks tidak habis terpakai saat mengerjakan tugas
yang tidak ada hubungannya dengan tampilan.

## Proyek

- **Nama:** GAIS (General Affair Information System)
- **Stack:** Laravel 13, PostgreSQL, Livewire 4, Filament 5, Tailwind 4
- **Menjalankan:** `php artisan serve` dan `npm run dev`
- **Build:** `npm run build`
- **Test:** `php artisan test`

## Router

**Jika tugas melibatkan pembuatan atau pengeditan UI/UX, baca `DESIGN.md` (arah gaya) lalu `ANTISLOP.md` (filter) sebelum menghasilkan apa pun. Sebelum mulai, tanyakan kapan antislop diterapkan (saat pengerjaan, atau setelah selesai) dan jangan mulai sebelum dijawab.**

Jawaban pemilik proyek pada 6 September 2026: **DURING**, aturan dipakai sejak perencanaan.

Yang termasuk tugas UI/UX: membuat halaman atau komponen baru, mengubah layout, styling,
warna, tipografi, spacing, animasi, copy yang tampil di layar, atau state komponen.

Yang tidak termasuk: perubahan API, query database, konfigurasi build, skrip, dan test
yang tidak menyentuh tampilan.

## Dua mode penerapan

- **DURING:** aturan dipakai sejak perencanaan, mencegah hasil generik sejak awal.
- **AFTER:** aturan dipakai untuk mengaudit hasil yang sudah jadi. Keluarkan temuan bernomor,
  masing-masing menyebut ID rule yang dilanggar, lokasi file, dan perbaikan yang disarankan.

## Yang wajib ditampilkan di akhir tiap tugas UI

1. **Design Read** di awal: "Saya baca ini sebagai [jenis halaman] untuk [audiens],
   gaya [bahasa visual], dial [ENERGY/RHYTHM/MOTION]."
2. **Alasan satu baris** untuk keputusan warna, layout, tipografi, spacing, kartu, dan ilustrasi (R-31).
3. **Delivery Gate checklist** dari `ANTISLOP.md` bagian akhir, dijawab eksplisit per blok.
   Kalau ada jawaban Blok 1 atau Blok 2 yang bermasalah, perbaiki dan ulangi sebelum diserahkan.
4. **Bukti verifikasi** (R-35): aplikasi sudah dijalankan, console bersih, tombol dan link
   sudah dicoba, semua tema dan breakpoint sudah dicek.

Hasilnya ditulis ke `DELIVERY-GATE.md`, satu berkas per tahap.

## Aturan khusus proyek ini

- Menu tidak pernah ditulis manual. Menu lahir dari Filament Resource yang ada, dan disembunyikan
  lewat izin. Ini yang menjaga R-24.
- Angka di layar selalu dihitung dari basis data. Tidak ada angka yang ditulis di kode (R-17).
- Nama perusahaan, alamat, dan logo diambil dari tabel pengaturan, tidak pernah dikarang (R-38).
- Setiap tabel wajib punya `emptyStateHeading` dan `emptyStateDescription` yang spesifik (R-27).
- Setiap tabel maksimal menampilkan **6 kolom secara bawaan**. Kolom selebihnya tetap boleh ada,
  tetapi ditulis `->toggleable(isToggledHiddenByDefault: true)` supaya pembaca memanggilnya lewat
  Pilih kolom saat butuh. Keterangan yang tidak muat sebagai kolom sendiri dipindahkan ke
  `->description()` di bawah kolom yang paling berhubungan, bukan dibuang.
  Berlaku juga untuk tabel di dalam widget dasbor dan relation manager.
- Setiap modul baru: daftarkan di `ModuleSeeder`, jalankan `php artisan db:seed --class=ModuleSeeder`
  lalu `php artisan gais:sync-permissions`, baru buat Resource yang memakai trait `AuthorizesModule`.
  Perintah sync sekaligus melekatkan seluruh izin ke role administrator, karena tanpa itu
  modul baru terbuat tetapi menunya tidak muncul pada siapa pun. Tambahkan juga izinnya ke
  `RoleSeeder` untuk Manajer GA dan Staf GA, kalau tidak modul itu hanya bisa dibuka administrator.
- Relation manager di halaman Lihat: panel sudah disetel
  `readOnlyRelationManagersOnResourceViewPagesByDefault(false)`. Bawaan Filament membuat seluruh
  relation manager di halaman Lihat menjadi hanya baca, dan tombol Tambah, Ubah, serta Hapus di
  dalamnya hilang tanpa pesan apa pun. Jangan hapus baris itu dari `AdminPanelProvider`.

## Larangan yang paling sering dilanggar

- Tombol atau link yang tidak melakukan apa-apa (R-26)
- Link navbar menuju section yang tidak ada (R-24)
- Tidak ada empty state, loading state, dan error state (R-27)
- Angka statistik, testimoni, atau klaim keamanan yang dikarang (R-17, R-18, R-36, R-38)
- Mobile rusak, teks terpotong, tap target di bawah 44px (R-03)
- Em dash di dalam copy (R-02)
- CTA generik seperti "Get Started" dan buzzword seperti "seamless" (R-15, R-16)

Sumber aturan: https://github.com/miqdadbadjuber/anti-slop
