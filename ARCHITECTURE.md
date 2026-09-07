# GAIS: Arsitektur

Dokumen ini menjelaskan bentuk sistem secara keseluruhan, supaya modul yang dibangun di tahap berikutnya
tidak perlu membongkar apa yang sudah ada di Tahap 1.

Terakhir diperbarui: 6 September 2026. Status: Tahap 1 sudah berjalan di mesin pengembangan. Tahap 2 kiriman A ditulis, belum dijalankan.

---

## 1. Keputusan Teknis

| Keputusan | Pilihan | Alasan |
|---|---|---|
| Framework | Laravel 13 | Dinaikkan dari 12 pada 6 September 2026 karena mesin pengembangan memakai PHP 8.5, sedangkan Laravel 12 hanya diuji resmi sampai PHP 8.4. Filament 5.x sudah menerima illuminate ^13.0 |
| UI admin | Filament 5 (di atas Livewire 4) | Tabel, form, filter, otorisasi, dan state kosong sudah tersedia dan konsisten. Untuk aplikasi internal padat data ini jauh lebih hemat daripada membangun tabel sendiri |
| Basis data | PostgreSQL | Butuh tipe JSON yang matang untuk nilai audit dan definisi izin, serta constraint yang ketat untuk data aset dan biaya |
| Otorisasi | Dibangun sendiri, tabel `modules` + `permissions` + `roles` | Kebutuhannya adalah role yang bisa mengatur modul mana yang tampil dan aksi apa yang boleh, dikelola dari layar oleh admin. Paket izin umum menyimpan izin sebagai string bebas, sedangkan di sini izin harus terikat ke registri modul supaya layar pengaturan role bisa menampilkan matriks modul kali aksi |
| Penomoran dokumen | Tabel `number_sequences` + `number_sequence_periods` | Nomor kode aset, tiket, dan reimbursement harus berurut dan tidak bentrok saat dua orang menyimpan bersamaan. Penghitungnya dipisah per periode karena impor aset lama memasukkan tahun 2019 dan 2026 secara berselang seling |
| Jejak audit | Tabel `audit_logs` + trait `Auditable` | Aset dan biaya menyentuh angka finansial, perubahannya harus bisa ditelusuri |
| Barcode | Kelas `Code128` sendiri, tanpa paket | Yang dibutuhkan hanya satu simbologi dan hasilnya harus SVG yang bisa ditempel langsung di halaman cetak. Tabel polanya sudah dicocokkan dengan implementasi acuan dan hasilnya di-decode balik untuk memastikan benar |
| Impor data | CSV, bukan XLSX | Membaca XLSX butuh paket tambahan, sedangkan CSV bisa dibaca PHP apa adanya. Excel menyimpan CSV dalam satu klik, dan pemisah titik koma maupun koma sama sama diterima |

## 2. Model Otorisasi

Empat lapis, dari yang paling umum ke paling spesifik:

```
modules (registri)  ->  permissions (modul x aksi)  ->  roles  ->  users
                                                          |          |
                                                    permission_role  role_user
                                                                     |
                                                            permission_user (override)
```

- **`modules`** adalah sumber kebenaran. Satu baris = satu modul yang benar-benar ada layarnya.
  Kolom `available_actions` (JSON) menentukan aksi apa yang masuk akal untuk modul itu.
  Modul aset misalnya butuh `create, read, update, delete, export, print`, sedangkan modul jejak audit
  hanya butuh `read, export`. Ini yang membuat matriks izin tidak menampilkan aksi yang tidak ada artinya.
- **`permissions`** dibuat dari kombinasi modul kali aksi, dengan `key` unik berbentuk `kode_modul.aksi`,
  misalnya `assets.create`. Baris ini di-sinkronkan dari registri modul, bukan diketik manual.
- **`roles`** menempel ke banyak izin. Satu pengguna boleh punya lebih dari satu role, izinnya digabung.
- **`permission_user`** adalah override per pengguna dengan kolom `granted` bernilai benar atau salah,
  untuk kasus nyata seperti "Budi staf GA, tapi khusus dia boleh menghapus data aset".
  Urutan evaluasi: super admin menang, lalu override pengguna, lalu gabungan izin dari role.
- **`roles.data_scope`** menyiapkan pembatasan baris untuk tahap berikutnya: `all`, `department`, `own`.
  Di Tahap 1 nilainya sudah tersimpan dan tampil di layar, tetapi belum ada modul transaksional yang
  memakainya. Ini ditulis di sini supaya tidak dikira fitur yang sudah aktif.

Menu tidak pernah dibuat manual. Setiap Filament Resource menyebut kode modulnya, dan Filament
menyembunyikan item navigasi ketika `canViewAny()` bernilai salah. Jadi menu selalu sama dengan izin,
dan tidak mungkin ada menu menuju halaman yang belum dibuat (R-24).

## 3. Skema Tahap 1

```
departments        locations (pohon)      employees
  id                 id                     id
  code (unik)        code (unik)            nip (unik)
  name               name                   full_name
  cost_center        type                   email
  parent_id  ---+    parent_id  ---+        phone
  head_employee_id   is_active             department_id -> departments
  is_active                                position
                                           employment_status
                                           join_date
                                           user_id -> users (nullable)
                                           is_active

users                     modules                 permissions
  id                        id                      id
  name                      code (unik)             module_id -> modules
  email (unik)              name                    action
  password                  description             key (unik) = code.action
  is_super_admin            group                   name
  is_active                 icon
  last_login_at             sort
                            is_active
                            available_actions (json)

roles                    permission_role        role_user        permission_user
  id                       role_id                role_id          user_id
  code (unik)              permission_id          user_id          permission_id
  name                                                             granted (bool)
  description
  is_system
  data_scope
  is_active

audit_logs                        settings              number_sequences
  id                                id                    id
  user_id (nullable)                group                 code (unik)
  user_name (snapshot)              key (unik)            prefix
  event                             value                 pattern
  auditable_type / auditable_id     type                  period_format
  module_code                                             current_period
  old_values (json)                                       next_number
  new_values (json)                                       padding
  url, ip_address, user_agent
  created_at
```

Catatan bentuk data:

- `users` dan `employees` sengaja dipisah. Tidak semua karyawan punya akun sistem, dan akun sistem
  bisa saja bukan karyawan (misalnya akun vendor pemeliharaan nanti). Hubungannya opsional lewat
  `employees.user_id`.
- `locations` berbentuk pohon dengan `parent_id` karena aset hanya ada di Head Office, sehingga yang
  dibutuhkan bukan multi cabang melainkan kedalaman: gedung, lantai, ruangan, area.
- `audit_logs.user_name` menyimpan salinan nama saat kejadian, supaya jejak tetap terbaca kalau
  pengguna dihapus.

## 3b. Skema Tahap 2 (kiriman A)

```
asset_categories                 assets
  id                               id
  code (unik)                      code (unik, dipakai barcode)
  name                             name
  description                      asset_category_id -> asset_categories
  useful_life_months               location_id -> locations
  depreciation_method              custodian_employee_id -> employees
  tax_group                        department_id -> departments (wajib)
  residual_percent                 asset_type (tetap, bergerak)
  account_asset                    brand, model, serial_number
  account_accumulated              acquisition_date
  account_expense                  acquisition_source
  is_active                        acquisition_cost, residual_value
                                   useful_life_months, depreciation_method
number_sequence_periods            status, condition
  id                               warranty_until
  number_sequence_id               notes, photo_path
  period (mis. 2026)
  next_number
  unik (number_sequence_id, period)
```

Catatan bentuk data:

**Format kode aset**

```
[Kode Departemen] - [Kode Akun COA] - [Tahun Perolehan] - [Nomor Urut]
        FIN        -      1201       -       2026        -     0001
   departments.code   asset_categories   assets.acquisition   number_sequence
                       .account_asset       _date (tahun)      _periods
```

Pola ini mengikuti kebiasaan penomoran aset di BUMN dan diputuskan pemilik proyek pada
6 September 2026. Tiga akibatnya di skema:

- `assets.department_id` dan `asset_categories.account_asset` menjadi wajib diisi, karena keduanya
  membentuk kode. Keduanya tetap nullable di basis data agar data lama yang sudah masuk sebelum
  aturan ini tidak menghalangi migrasi, tetapi form, importir, dan pembuat kode menolak yang kosong
  dengan pesan yang menyebut alasannya.
- Nomor urut dihitung per kombinasi departemen, akun, dan tahun. Satu baris `number_sequences`
  dibuat untuk tiap pasangan departemen dan akun dengan kode `asset.<kode departemen>.<nomor akun>`,
  lalu `number_sequence_periods` memegang penghitung per tahun. Jadi komputer Finance 2026 punya
  hitungan sendiri, terpisah dari kendaraan Finance 2026 maupun komputer departemen lain.
- Tahun diambil dari tanggal perolehan, bukan tanggal input, supaya aset lama yang diimpor memakai
  tahunnya sendiri. Aset tanpa tanggal perolehan memakai tahun berjalan.

`assets.code` dilebarkan menjadi 80 karakter karena kode departemen dan nomor akun bisa panjang.
Semakin panjang kodenya, semakin rapat barcodenya: kode 18 karakter menghasilkan lebar modul
sekitar 0,27 mm pada label 70 mm, masih di atas batas aman cetak laser.

**Klasifikasi kategori**

- `asset_categories.tax_group` menyimpan kelompok harta berwujud, dan model menurunkan
  `useful_life_months` dari kelompok itu memakai tabel PMK 72 Tahun 2023: kelompok 1 sampai 4
  masing masing 4, 8, 16, dan 20 tahun, bangunan permanen 20 tahun, bangunan tidak permanen 10 tahun.
  Masa manfaat yang diisi manual selalu menang, untuk kasus kebijakan akuntansi yang berbeda dari pajak.
- `AssetCategorySeeder` mengisi 13 kelas aset yang lazim di kantor, mengikuti pengelompokan aset tetap
  pada PSAK 216. Nomor akun COA sengaja dikosongkan karena bagan akun berbeda di tiap perusahaan dan
  nomornya ikut tercetak di stiker.
- `useful_life_months` dan `depreciation_method` ada di dua tempat. Nilai di kategori adalah bawaan,
  nilai di aset adalah pengecualian per barang. Perhitungannya sendiri baru dibangun di Tahap 3.
- Kategori yang sudah punya aset tidak bisa dihapus, karena kodenya sudah tercetak di stiker.

## 3c. Skema stock opname

```
stock_opnames                         stock_opname_lines
  id                                    id
  code (unik, SO/2026/09/0001)          stock_opname_id -> stock_opnames
  name                                  asset_id -> assets
  scope_location_id -> locations        asset_code, asset_name (salinan)
  scope_department_id -> departments    expected_location_id -> locations
  scope_asset_category_id -> categories expected_condition, expected_status
  status (draft, berjalan,              checked (bool)
          selesai, dibatalkan)          found (bool, null = belum diperiksa)
  started_at / started_by_user_id       found_location_id -> locations
  finished_at / finished_by_user_id     found_condition
  adjusted_at / adjusted_by_user_id     notes
  notes                                 checked_at / checked_by_user_id
                                        unik (stock_opname_id, asset_id)
```

Catatan bentuk data:

- Tiga kolom cakupan digabung dengan DAN, dan yang kosong berarti tidak membatasi. Memilih satu lantai
  ikut mencakup seluruh ruangan di dalamnya, ditelusuri sampai lima tingkat.
- `asset_code` dan `asset_name` disalin saat daftar disusun. Kalau aset berganti nama di tengah sesi,
  lembar hasilnya tetap menunjukkan apa yang dibawa petugas ke lapangan.
- Hasil pemeriksaan tidak disimpan sebagai kolom. Nilainya disimpulkan dari perbandingan antara kolom
  `expected_*` dan `found_*`, sehingga tidak mungkin ada baris yang kesimpulannya bertentangan dengan datanya.
- Penyesuaian data aset dipisah dari penutupan sesi dan memakai izin `stock_opnames.approve`. Yang diubah
  hanya lokasi dan kondisi. Aset yang tidak ditemukan sengaja tidak diubah statusnya, karena tidak ketemu
  saat opname belum tentu berarti hilang, dan mengubahnya otomatis akan menyembunyikan masalah yang justru
  harus ditindaklanjuti.
- Nomor sesi memakai `number_sequences` dengan periode `Y/m`, jadi nomor urut kembali ke satu tiap bulan.

## 4. Rencana Skema Modul Berikutnya

Ditulis sekarang supaya keputusan Tahap 1 tidak menghalangi. Belum ada tabelnya.

**Aset dan inventaris (Tahap 2)**
`asset_categories` dan `assets` ada di bagian 3b, `stock_opnames` dan `stock_opname_lines` di bagian 3c.
Yang masih menyusul: `consumable_items` dan `consumable_movements` (ATK habis pakai, stok masuk keluar,
batas minimum).
Belum dijadwalkan karena tidak dipilih: `asset_movements` (perpindahan lokasi atau penanggung jawab)
dan `asset_loans` (peminjaman).

**Penyusutan dan pemeliharaan (Tahap 3)**
`depreciation_schedules` (baris per aset per bulan: beban, akumulasi, nilai buku),
`maintenance_plans` (preventif, interval), `work_orders` (korektif dan preventif, biaya, vendor),
`asset_disposals` (pelepasan, nilai buku saat lepas, laba atau rugi).

**Fasilitas dan kantor (Tahap 4)**
`vehicles` (menunjuk ke `assets`), `vehicle_documents` (STNK, pajak, asuransi, tanggal jatuh tempo),
`vehicle_bookings` (pool car), `vehicle_trips` (odometer awal dan akhir, tujuan, pengemudi),
`fuel_logs` (liter, biaya, konsumsi), `service_areas` dan `cleaning_schedules`,
`security_shifts` dan `incident_reports`, `tickets` (permintaan AC, listrik, kebersihan, dengan SLA,
prioritas, penugasan, lampiran).

**Biaya dan reimbursement (Tahap 5)**
`expense_categories` (memetakan ke COA), `budgets` dan `budget_lines` (per departemen, per kategori,
per periode), `expenses` (tagihan vendor termasuk transportasi daring, alokasi ke departemen),
`reimbursements` dan `reimbursement_lines` (pengajuan karyawan, lampiran bukti),
`approvals` (alur persetujuan berjenjang yang dipakai bersama oleh tiket, reimbursement, dan work order).

## 5. Titik Integrasi Aset dan Finance

Ini yang dimaksud "modul GA yang perlu integrasi antara aset dan finance". Semuanya bertemu di satu
tabel realisasi anggaran dan satu tabel jurnal.

| Kejadian di sisi GA | Yang terjadi di sisi finance |
|---|---|
| Aset dicatat perolehannya | Baris jurnal: aset tetap bertambah, kas atau utang berkurang, memakai akun dari kategori aset |
| Penutupan penyusutan bulanan | Baris jurnal per departemen: beban penyusutan bertambah, akumulasi penyusutan bertambah. Sumber angkanya `depreciation_schedules` |
| Work order pemeliharaan selesai | Biaya menempel di aset untuk perhitungan biaya kepemilikan, sekaligus jadi realisasi anggaran departemen pemilik aset |
| Pengisian BBM dan servis kendaraan | Realisasi anggaran operasional kendaraan, dan biaya per kilometer per kendaraan |
| Aset dilepas atau dihapus | Nilai buku dihentikan, selisih dengan hasil pelepasan dicatat sebagai laba atau rugi pelepasan |
| Tagihan vendor dan reimbursement disetujui | Realisasi anggaran kategori terkait, dan jurnal beban ke cost center departemen |

Dua tabel penghubung yang akan dibuat di Tahap 6:

- `budget_realizations`: satu baris untuk setiap kejadian yang memakan anggaran, menunjuk ke sumbernya
  secara polimorfik (work order, BBM, tagihan, reimbursement). Laporan budget versus actual membaca ini.
- `journal_entries` dan `journal_lines`: jurnal yang bisa diekspor ke sistem akuntansi.
  Pemetaan akun disimpan di `asset_categories` dan `expense_categories`, jadi tim finance yang menentukan
  nomor akunnya, bukan pengembang.

Yang perlu diputuskan bersama tim finance sebelum Tahap 6 dimulai:
metode penyusutan yang dipakai (garis lurus atau saldo menurun), tanggal mulai penyusutan
(bulan perolehan atau bulan berikutnya), daftar nomor akun, dan apakah jurnal dikirim lewat berkas
atau lewat API sistem akuntansi yang dipakai perusahaan.

## 6. Konvensi Kode

- Nama tabel jamak dan bahasa Inggris, isi data dan label layar bahasa Indonesia.
- Uang disimpan sebagai `decimal(18,2)`, tidak pernah float.
- Setiap model transaksional memakai trait `Auditable`.
- Setiap Filament Resource memakai trait `AuthorizesModule` dan mendeklarasikan `$moduleCode`.
- Setiap tabel wajib mengisi `emptyStateHeading` dan `emptyStateDescription` dengan kalimat spesifik modul (R-27).
- Menambah modul baru berarti: menambah baris di `ModuleSeeder`, menjalankan `php artisan gais:sync-permissions`,
  lalu membuat Resource yang menyebut kode modul itu.
