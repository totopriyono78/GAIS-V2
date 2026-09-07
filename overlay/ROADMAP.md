# GAIS: Peta Tahapan

Satu tahap = satu paket yang bisa dijalankan dan dipakai, bukan potongan setengah jadi.
Setiap tahap ditutup dengan Delivery Gate `ANTISLOP.md` dan verifikasi jalan di mesin Anda.

Urutannya bukan urutan kepentingan, melainkan urutan ketergantungan data. Aset harus ada sebelum
penyusutan bisa dihitung, dan anggaran harus ada sebelum realisasi bisa dibandingkan.

---

## Tahap 1: Fondasi dan Hak Akses (selesai, sudah dijalankan)

Isi:
- Masuk, keluar, profil pengguna, dasbor ringkasan
- Registri modul, dengan daftar aksi yang masuk akal per modul
- Role dengan matriks izin modul kali aksi, plus override izin per pengguna
- Pengguna sistem dan penetapan role
- Data induk: departemen, lokasi Head Office (gedung, lantai, ruangan, area), karyawan
- Pengaturan sistem dan penomoran dokumen
- Jejak audit untuk setiap perubahan data

Selesai kalau: admin bisa membuat role baru, mencentang modul dan aksi yang boleh, menetapkannya ke
pengguna, lalu pengguna itu masuk dan hanya melihat menu yang diizinkan.

## Tahap 2: Aset dan Inventaris (sedang dikerjakan)

Dipecah tiga kiriman supaya setiap bagian bisa dicoba sebelum bagian berikutnya menumpuk di atasnya.

**Kiriman A, sudah ditulis:**
- Kategori aset dengan awalan kode, umur ekonomis, dan metode penyusutan bawaan
- Daftar aset tetap dan aset bergerak dengan lokasi, penanggung jawab, departemen, status, kondisi
- Kode aset otomatis berbentuk AWALAN-TAHUN-NOMOR, contohnya GA-KOM-2026-0001
- Barcode Code 128 dan halaman cetak label untuk lembar stiker A4, ukurannya diatur dari Pengaturan
- Impor CSV yang tahan data berantakan: baris bermasalah dilaporkan, kolom yang tidak dikenal dikosongkan
  dan dicatat sebagai peringatan, bukan menggagalkan seluruh berkas

**Kiriman B, stock opname, sudah ditulis:**
- Sesi opname dengan cakupan lokasi, departemen, dan kategori yang bisa digabung
- Daftar target disusun otomatis dari cakupan, lengkap dengan salinan lokasi dan kondisi saat disusun
- Pencatatan temuan per baris: sesuai catatan, pindah lokasi, kondisi berubah, atau tidak ditemukan
- Penyesuaian data aset dari hasil opname, dipisah sebagai tindakan tersendiri dan butuh izin khusus
- Unduh hasil opname sebagai CSV

**Kiriman C, barang habis pakai, sudah ditulis:**
- Daftar barang habis pakai dengan kategori, satuan, tempat simpan, dan batas pemesanan ulang
- Buku stok berisi barang masuk, barang keluar, koreksi tambah, dan koreksi kurang
- Stok tidak disimpan sebagai kolom, melainkan dijumlahkan dari buku stok, jadi tidak bisa melenceng
- Penanda keadaan tiga tingkat di daftar barang: Aman, Perlu dipesan, Habis, beserta penyaringnya
- Mutasi yang akan membuat stok minus ditolak sebelum tersimpan, lengkap dengan angka stok tersedia
- Barang keluar wajib menyebut departemen, karena angka itu yang nanti dipakai membandingkan
  anggaran ATK tiap departemen dengan pemakaian sebenarnya
- Data contoh terpisah dan bisa dihapus lagi: `php artisan gais:atk-demo`

Alur permintaan ATK oleh karyawan beserta persetujuannya tidak masuk kiriman ini. Anda memilih
stok dulu pada 6 September 2026, dan alur permintaan dikerjakan setelah kiriman ini lulus.

**Kiriman D, melengkapi siklus aset, sudah ditulis:**

Ditambahkan ke Tahap 2 pada 6 September 2026, setelah Anda menanyakan kelengkapan siklus aset.
Sebelumnya kedua hal ini tersebar di Tahap 3, dan memisahkannya membuat siklus aset tidak pernah
utuh sampai penyusutan selesai dibangun.

- Transfer atau mutasi aset: perpindahan lokasi, penanggung jawab, dan departemen sebagai dokumen
  tersendiri, bukan sekadar mengubah kolom di formulir edit
- Serah terima: siapa menyerahkan, siapa menerima, kapan, dan alasannya
- Kartu riwayat aset: satu daftar berurutan berisi perpindahan, hasil opname, dan perubahan kondisi,
  supaya pertanyaan "aset ini dulu di mana" bisa dijawab tanpa membuka jejak audit
- Pelepasan aset: tanggal, cara (dijual, dihibahkan, dimusnahkan, hilang), nilai jual, dan
  dokumen pendukung. Status Sudah dilepas berhenti menjadi label dan menjadi hasil dari satu proses
- Perhitungan laba atau rugi pelepasan menyusul di Tahap 3, karena angkanya butuh nilai buku
- Status Sudah dilepas dibuang dari pilihan di formulir aset, jadi status itu hanya bisa lahir
  dari dokumen pelepasan

Peminjaman aset belum masuk juga. Status Dipinjam sudah ada sebagai label, tetapi alur pinjam dan
kembalinya belum dibangun, dan itu dicatat di sini supaya tidak terlupa.

Selesai kalau: 1000 aset bisa diimpor, dicari dalam hitungan detik, labelnya dicetak dan dipindai,
satu siklus stock opname bisa dijalankan sampai laporan selisih, satu aset bisa dipindahkan antar
ruangan dengan serah terima yang tercatat, dan satu aset bisa dilepas dengan alasan dan dokumennya.

## Tahap 3: Penyusutan dan Pemeliharaan Aset

Isi:
- Perhitungan penyusutan bulanan, garis lurus dan saldo menurun, dengan penutupan periode
- Kartu aset: nilai perolehan, akumulasi penyusutan, nilai buku, riwayat biaya
- Jadwal pemeliharaan preventif per aset atau per kategori, dengan pengingat
- Work order pemeliharaan korektif, penugasan teknisi atau vendor, biaya, lampiran
- Perhitungan laba atau rugi pelepasan, memakai pencatatan pelepasan dari Tahap 2 kiriman D

Selesai kalau: penutupan penyusutan satu bulan menghasilkan angka yang cocok dengan hitungan manual
tim finance untuk sampel aset yang mereka pilih.

## Tahap 4: Fasilitas dan Kantor

Isi:
- Kendaraan dinas: data kendaraan terhubung ke aset, dokumen STNK, pajak, asuransi, dan pengingat jatuh tempo
- Pemesanan pool car, jadwal pemakaian, log perjalanan dengan odometer
- Pencatatan BBM, konsumsi kilometer per liter, dan biaya per kendaraan
- Permintaan perbaikan (AC, listrik, kebocoran, dan lainnya): tiket dengan kategori, prioritas, SLA,
  penugasan, lampiran foto, dan riwayat status
- Kebersihan: area layanan, jadwal dan checklist harian petugas
- Keamanan: jadwal shift dan laporan insiden

Selesai kalau: karyawan bisa membuka tiket perbaikan dari akunnya, tim GA menugaskan dan menutupnya,
dan pemakaian kendaraan satu bulan bisa dilaporkan per kendaraan.

## Tahap 5: Biaya, Anggaran, dan Reimbursement

Isi:
- Kategori biaya GA yang dipetakan ke akun perusahaan
- Anggaran per departemen per kategori per periode
- Pencatatan tagihan vendor, termasuk transportasi daring dan langganan, dengan alokasi ke departemen
- Pengajuan reimbursement karyawan dengan lampiran bukti dan alur persetujuan berjenjang
- Laporan anggaran versus realisasi, per departemen dan per kategori

Selesai kalau: satu pengajuan reimbursement bisa berjalan dari karyawan sampai disetujui dan tercatat
sebagai realisasi anggaran, dan laporan anggaran versus realisasi cocok dengan rekap manual.

## Tahap 6: Integrasi Aset dan Finance

Isi:
- Pemetaan akun untuk kategori aset dan kategori biaya
- Jurnal otomatis untuk perolehan aset, penyusutan bulanan, pelepasan aset, biaya pemeliharaan,
  tagihan, dan reimbursement
- Register aset tetap untuk keperluan audit
- Ekspor jurnal ke sistem akuntansi perusahaan
- Laporan biaya kepemilikan aset dan biaya operasional kendaraan

Selesai kalau: tim finance menerima berkas jurnal satu periode dan bisa memasukkannya tanpa koreksi manual.

---

## Yang perlu Anda putuskan sebelum tahap terkait dimulai

| Sebelum | Keputusan yang dibutuhkan |
|---|---|
| Tahap 2 | Sudah dijawab pada 6 September 2026: kode dibuat otomatis, label dicetak di printer biasa dengan stiker A4, data awal ada tapi berantakan sehingga impor dibuat toleran |
| Tahap 3 | Metode penyusutan, tanggal mulai penyusutan, dan apakah ada aset lama yang sudah berjalan penyusutannya |
| Tahap 4 | Alur persetujuan tiket dan pemesanan kendaraan, siapa yang menyetujui apa, serta target SLA per kategori |
| Tahap 5 | Struktur anggaran GA, tingkat persetujuan reimbursement, dan batas nominal tiap tingkat |
| Tahap 6 | Sistem akuntansi yang dipakai, daftar nomor akun, dan apakah pertukaran data lewat berkas atau API |

## Yang belum masuk peta ini

Ditulis supaya jelas bahwa ini memang belum direncanakan, bukan terlupa:
pengadaan dan tender vendor, kontrak sewa gedung, absensi dan perjalanan dinas, aplikasi mobile,
dan integrasi dengan sistem HR. Kalau salah satunya dibutuhkan, sebutkan dan saya masukkan ke peta.
