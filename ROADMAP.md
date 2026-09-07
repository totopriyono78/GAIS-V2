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

Dipecah beberapa kiriman supaya setiap bagian bisa dicoba sebelum bagian berikutnya menumpuk di atasnya.

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

**Kiriman D2, kelengkapan data dan dokumen, sudah ditulis:**

Ditambahkan pada 7 September 2026 atas permintaan Anda, dikerjakan sebelum penyusutan supaya
penyusutan berdiri di atas data aset yang sudah lengkap.

- Status kepemilikan milik atau sewa, dengan masa sewa, nomor kontrak, dan pemberi sewa. Aset
  sewaan ditandai tidak disusutkan, dan itu akan dihormati saat penyusutan dibangun
- Keterangan perolehan yang lebih lengkap: dari pembelian atau dari proyek, barang baru atau bekas,
  bergaransi atau tidak beserta tanggal awal dan akhir garansinya
- Dokumen pendukung per aset: kartu garansi, buku manual, faktur, kontrak sewa, sertifikat, berita
  acara, dan foto, masing masing dengan jenis, nama, dan catatannya sendiri
- Nomor sertifikat tanah dan bangunan, muncul dari penanda per kategori, bukan dari daftar kategori
  yang ditulis di kode, jadi kategori tanah atau bangunan yang baru ikut memunculkannya sendiri
- Kategori ditulis `1202 - Bangunan permanen` di seluruh aplikasi: pemilih kategori, kolom tabel,
  legenda diagram, dan berita acara
- Halaman detail mutasi aset, sebelumnya tidak ada tautannya sama sekali
- Berita Acara Mutasi (BAM) dan Berita Acara Serah Terima (BAST) sebagai berkas PDF yang langsung
  terunduh, dengan kop dari tabel pengaturan dan tabel perubahan yang dihitung dari selisih, bukan
  diketik ulang
- Dasbor dipecah menjadi tab Ringkasan, Aset, dan Persediaan, dengan tab tersimpan di alamat halaman.
  Tab Aset berisi diagram status, diagram kategori, jatuh tempo garansi 1, 2, dan 3 bulan sebagai
  rentang yang tidak bertumpuk, dan daftar aset yang garansinya segera habis. Tab Persediaan berisi
  ringkasan stok, pemakaian per departemen enam bulan terakhir, dan daftar barang yang perlu dipesan

Kiriman ini juga menambal paket bahasa Indonesia Filament untuk tabel, yang sebelumnya menyisakan
"Yes", "No results", dan "Loading..." dalam bahasa Inggris di seluruh tabel aplikasi.

Selesai kalau: 1000 aset bisa diimpor, dicari dalam hitungan detik, labelnya dicetak dan dipindai,
satu siklus stock opname bisa dijalankan sampai laporan selisih, satu aset bisa dipindahkan antar
ruangan dengan serah terima yang tercatat, dan satu aset bisa dilepas dengan alasan dan dokumennya.

## Tahap 3: Penyusutan dan Pemeliharaan Aset (selesai, sudah diuji)

**Kiriman E, penyusutan, sudah ditulis dan lulus gate:**
- Mesin hitung terpisah yang tidak menyentuh basis data, dengan 17 pengujian yang mencocokkan
  angkanya dengan hitungan tangan, termasuk sapuan 240 kombinasi umur, metode, harga, dan nilai sisa
- Garis lurus dan saldo menurun ganda, dengan bulan terakhir menyerap sisa pembulatan supaya
  aset yang habis umurnya bernilai tepat nol, bukan mendekati
- Tiga kebijakan di layar Pengaturan dengan bawaan pajak Indonesia: kapan penyusutan mulai,
  nilai sisa bawaan, dan perlakuan saldo menurun di tahun terakhir
- Penutupan periode berurutan, angka dibekukan saat ditutup, hanya periode terakhir yang bisa
  dibuka kembali. Bulan yang terlewat masuk sebagai beban susulan dengan keterangan cakupannya
- Akumulasi awal dihitung sendiri dari jadwal untuk aset lama, dengan kolom untuk menimpanya
  kalau buku perusahaan berkata lain
- Nilai buku dan riwayat penyusutan bulanan di kartu aset
- Laba atau rugi pelepasan, yang sejak kiriman D sengaja dikosongkan karena menunggu nilai buku

**Kiriman F, pemeliharaan, sudah ditulis dan lulus gate:**
- Rekanan sebagai data induk, dipakai juga oleh modul tagihan di Tahap 5 nanti
- Jadwal pemeliharaan preventif per aset, dengan pembuatan massal dari daftar aset
- Riwayat penjadwalan sebagai tabel tersendiri: satu baris per jatuh tempo, ditutup dengan
  Sudah dikerjakan atau Dilewati beserta alasannya. Inilah yang menjawab apakah servis kuartal
  lalu benar benar dikerjakan vendor, termasuk untuk jatuh tempo yang sengaja dilewatkan
- Selalu tepat satu kunjungan terbuka per jadwal; yang berikutnya lahir tiap kali satu ditutup
- Dua jalur mencatat pekerjaan: catat langsung untuk kunjungan vendor rutin, atau perintah kerja
  untuk pekerjaan yang perlu penugasan, lampiran foto, dan uraian panjang
- Perintah kerja korektif dari laporan kerusakan, dengan prioritas, penugasan, biaya, dan lampiran
- Riwayat dan total biaya pemeliharaan di kartu aset, serta tab dasbor Pemeliharaan

Pengingat sengaja hanya di layar, sesuai pilihan Anda pada 7 September 2026: dasbor, lencana
angka di menu, dan daftar yang selalu terurut dari yang paling mendesak. Tidak ada email.

Selesai kalau: penutupan penyusutan satu bulan menghasilkan angka yang cocok dengan hitungan manual
tim finance untuk sampel aset yang mereka pilih. **Sudah dibuktikan pada 7 September 2026** untuk
dua aset yang dihitung tangan sampai rupiah terakhir, tercatat di `DELIVERY-GATE-7.md`.

## Tahap 4: Fasilitas dan Kantor (sedang dikerjakan)

Urutannya Anda tentukan pada 7 September 2026: tiket perbaikan dulu, kendaraan menyusul.

**Kiriman G, permintaan perbaikan, sudah ditulis:**
- Jenis permintaan sebagai data induk, dengan prioritas bawaan dan target waktu penyelesaian.
  Target waktu sengaja dibiarkan kosong saat pertama diisi, karena berapa jam sebuah pekerjaan
  seharusnya selesai adalah janji perusahaan, bukan angka yang boleh dikarang pemrogram
- Tiket dari karyawan: jenis, prioritas, lokasi, aset yang bermasalah kalau ada, dan foto keadaan
- Persetujuan atasan, yaitu kepala departemen pemohon. Tidak ada hierarki atasan baru yang dibuat,
  karena data departemen sudah menyimpan kepalanya dan mengarang tabel baru berarti seluruhnya
  harus diisi ulang sebelum modul ini bisa dipakai
- Tiga keadaan melewati persetujuan supaya tiket tidak tersangkut selamanya: departemen belum
  punya kepala, pemohon adalah kepala departemen itu sendiri, dan prioritas mendesak. Alasannya
  ditulis di kolomnya sendiri sehingga lompatan itu terbaca di layar. Yang ketiga bisa dimatikan
  dari layar Pengaturan
- Tim GA menerima tiket, dan satu perintah kerja korektif lahir untuk mengerjakannya. Sejak saat
  itu pekerjaannya hidup di perintah kerja, dan tiketnya berubah menjadi catatan siapa melapor
  kapan dan berapa lama menunggu. Tidak ada dua daftar pekerjaan yang harus dicocokkan tangan
- Batas waktu dibekukan sekali saat tiket diterima, bukan dihitung ulang saat dibaca, supaya
  mengubah target kategori besok tidak menggeser janji yang sudah terlanjur dibuat hari ini
- Tiket ikut selesai saat perintah kerjanya selesai, dan kembali ke antrean kalau dibatalkan
- Penyempitan daftar lewat izin `service_requests.read_all`: tanpa izin itu seseorang hanya
  melihat tiketnya sendiri dan tiket departemen yang ia kepalai

**Kiriman H, kendaraan dinas dan dokumennya, sudah ditulis dan lulus gate:**
- Kendaraan sebagai keterangan tambahan di atas satu aset, bukan daftar tersendiri, sehingga
  penyusutan, mutasi, perintah kerja, dan pelepasannya memakai jalur aset yang sudah ada
- Nomor polisi, rangka, mesin, bahan bakar, transmisi, daya angkut, dan odometer
- Cara pakai kendaraan: pool yang dipesan bergantian, pegangan yang melekat pada satu orang,
  atau operasional untuk kirim barang. Kolom ini yang nanti menentukan kendaraan mana yang
  masuk daftar pemesanan
- Pajak tahunan, perpanjangan STNK lima tahunan, uji KIR, dan asuransi, masing masing sebagai
  riwayat masa berlaku, bukan satu baris yang ditimpa tiap tahun
- Tombol Perpanjang yang menyalin nomor dan penerbit lalu mengusulkan tanggal berakhir
  berikutnya sesuai jenisnya, dan sengaja mengosongkan biaya
- Pengingat jatuh tempo di lencana menu, di daftar kendaraan, dan di tab dasbor Kendaraan

**Kiriman I, pemakaian kendaraan, sudah ditulis dan lulus gate:**
- Pemesanan kendaraan dengan alur kembar permintaan perbaikan: karyawan memesan, atasan
  menyetujui, tim GA menugaskan kendaraan dan sopirnya. Pemohon tidak memilih kendaraan
- Pemeriksaan bentrok dua lapis: sebagai keterangan hidup saat memilih kendaraan, dan sekali
  lagi tepat sebelum disimpan, supaya dua orang yang membuka layar bersamaan tidak saling
  menimpa. Pesannya menyebut nomor pemesanan lain, jamnya, dan tujuannya
- Log perjalanan dua langkah: berangkat dicatat saat kunci diambil, kembali saat dikembalikan.
  Jaraknya selalu selisih dua angka odometer, tidak pernah disimpan
- Pemesanan ikut selesai sendiri saat perjalanannya ditutup
- Pengisian BBM per pengisian dengan odometer, dan konsumsi kilometer per liter yang hanya
  dihitung antara dua pengisian penuh. Baris yang belum bisa dihitung menyebut alasannya
- Satu penjaga odometer untuk seluruh aplikasi, dengan dua rasa aturan: penutupan perjalanan
  tidak boleh mundur dari angka tertinggi, pengisian BBM diperiksa menurut urutan tanggalnya
  supaya struk lama tetap bisa dimasukkan
- Foto kendaraan beserta tanggal pengambilan dan odometernya, atas permintaan pemilik proyek

**Belum dikerjakan di Tahap 4:**
- Kebersihan: area layanan, jadwal dan checklist harian petugas
- Keamanan: jadwal shift dan laporan insiden

Selesai kalau: karyawan bisa membuka tiket perbaikan dari akunnya, tim GA menugaskan dan menutupnya,
dan pemakaian kendaraan satu bulan bisa dilaporkan per kendaraan.

## Tahap 5: Biaya, Anggaran, dan Reimbursement (sedang dikerjakan)

Keputusan pemilik proyek pada 7 September 2026: anggaran disusun **per departemen per kategori**,
ditetapkan **tahunan**, dan reimbursement disetujui **atasan lalu GA tanpa batas nominal**.

**Kiriman J, kategori biaya dan anggaran, sudah ditulis dan lulus gate:**
- Kategori biaya GA dengan kolom sumber realisasi, plus tempat pemetaan ke akun perusahaan
  yang sengaja dibiarkan kosong sampai tim finance menyebutkannya
- Pagu per departemen per kategori per tahun, dijaga kunci unik supaya satu pasangan tidak
  pernah punya dua pagu
- Realisasi tidak pernah diketik dan tidak pernah disimpan sebagai kolom. Empat kategori
  menjumlahkannya sendiri: pemeliharaan dari perintah kerja dan kunjungan yang selesai, BBM
  dari pengisian, pajak kendaraan dari tanggal terbit dokumennya, dan ATK dari barang keluar
- Kategori yang belum bisa dihitung menulis "Belum dijumlahkan", bukan Rp 0
- Biaya yang tidak bisa dibebankan ke departemen mana pun dilaporkan terpisah di subjudul,
  bukan dibuang dan bukan dibagi rata

**Kiriman K, tagihan rekanan, sudah ditulis dan lulus gate:**
- Satu tagihan adalah satu faktur, dengan pindaian, nomor faktur rekanan, dan nomor internal
  sendiri. Keduanya disimpan terpisah supaya faktur ganda dari rekanan tetap bisa ditelusuri
- Nilainya tidak pernah disimpan. Ia dijumlahkan dari baris pembebanannya, dan tidak ada satu
  pun tempat di layar yang bisa mengetiknya, sehingga total dan rinciannya tidak bisa berbeda
- Satu tagihan bisa dibagi ke banyak departemen sekaligus, dan baris yang memang biaya bersama
  boleh tidak menyebut departemen. Angkanya lalu muncul terpisah di layar anggaran
- Tanggal faktur yang menentukan tahun anggaran, bukan tanggal bayar
- Alur tiga langkah dengan tiga orang: GA mencatat dan mengajukan, manajer menyetujui, yang
  memegang bukti transfer menandai lunas. Izin `approve` dan `pay` sengaja dipisah
- Rincian terkunci begitu diajukan. Tagihan yang ditolak bisa dikembalikan ke draf untuk
  diperbaiki, dan alasan penolakannya tetap terbaca selama perbaikan
- Pilihan kategori dibatasi pada kategori bersumber tagihan, supaya biaya pemeliharaan dan BBM
  yang sudah dijumlahkan dari catatan aslinya tidak terhitung dua kali
- Sumber realisasi "manual" berganti nama menjadi "tagihan" lewat migrasi. Enam kategori yang
  dulu menulis "Menunggu tagihan" sekarang menjumlahkan sendiri dari faktur yang disetujui
- Tagihan yang belum disetujui tidak masuk realisasi, tetapi nilainya disebut di bawah angka
  realisasi tiap pagu dan di subjudul, karena tumpukan faktur yang belum ditandatangani adalah
  cara termudah membuat pagu terlihat sehat pada hari uangnya sudah habis

**Kiriman L, penggantian biaya karyawan, sudah ditulis dan lulus gate:**
- Alur empat langkah dengan empat orang: karyawan mengumpulkan struk, atasan menyetujui, tim
  GA memeriksa, lalu ditandai sudah ditransfer. Tanpa batas nominal
- Dua persetujuan karena ada dua pertanyaan. Atasan menjawab "benar ini keperluan kerja",
  tim GA menjawab "struknya ada dan angkanya cocok". Izinnya dipisah bertiga: approve,
  verify, dan pay
- Satu baris adalah satu struk, dengan tanggal, kategori biaya, nilai, dan fotonya sendiri.
  Nilai pengajuan dijumlahkan dari barisnya dan tidak pernah bisa diketik
- Foto struk boleh kosong dan tidak memblokir pengajuan, karena struk memang kadang hilang.
  Yang belum ada fotonya dihitung dan disebutkan di kotak persetujuan tim GA
- Tanggal struk yang menentukan tahun anggaran, bukan tanggal pengajuan. Pengajuan yang
  struknya menyeberang tahun menyebutkannya sendiri
- Departemen ada di kepala pengajuan, bukan di tiap struk, karena struk milik satu orang
  hampir selalu jatuh ke satu departemen. Boleh dikosongkan untuk belanja kantor bersama
- Persetujuan atasan dilewati kalau departemennya belum punya kepala, kalau pemohon adalah
  kepala departemen itu sendiri, atau kalau pengajuan tidak dibebankan ke departemen mana
  pun. Alasannya ditulis ke kolomnya sendiri supaya lompatan itu terbaca di layar
- Yang ditolak bisa dikembalikan ke draf, dan persetujuan atasan diulang dari awal setelah
  diajukan ulang

**Catatan penting:** kolom kepala departemen masih kosong di seluruh departemen. Selama itu,
setiap pengajuan melewati langkah atasan dan langsung ke tim GA, dengan alasannya tertulis
di layar. Mengisi kepala departemen adalah satu satunya hal yang perlu dilakukan supaya
langkah atasan benar benar berjalan.

**Belum dikerjakan di Tahap 5:**
- Laporan anggaran versus realisasi yang bisa diunduh

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
| Tahap 4 | Sudah dijawab pada 7 September 2026: tiket menjadi pintu masuk yang berubah jadi perintah kerja, atasan pemohon menyetujui lebih dulu, dan tiket dikerjakan sebelum kendaraan. Untuk kendaraan: ada pool car yang dipesan, ada sopir, ada kendaraan operasional, dan BBM dicatat per pengisian dengan odometer supaya kilometer per liter bisa dihitung. Target SLA per jenis masih menunggu kesepakatan tim GA, dan kolomnya sengaja dibiarkan kosong sampai itu terjadi |
| Tahap 5 | Sudah dijawab pada 7 September 2026: anggaran per departemen per kategori, ditetapkan tahunan, dan reimbursement disetujui atasan lalu GA tanpa batas nominal |
| Tahap 6 | Sistem akuntansi yang dipakai, daftar nomor akun, dan apakah pertukaran data lewat berkas atau API |

## Yang belum masuk peta ini

Ditulis supaya jelas bahwa ini memang belum direncanakan, bukan terlupa:
pengadaan dan tender vendor, kontrak sewa gedung, absensi dan perjalanan dinas, aplikasi mobile,
dan integrasi dengan sistem HR. Kalau salah satunya dibutuhkan, sebutkan dan saya masukkan ke peta.
