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

## Tahap 4: Fasilitas dan Kantor (selesai 8 September 2026)

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

**Kiriman P, kebersihan, sudah ditulis dan lulus gate:**

Empat keputusan pemilik proyek pada 8 September 2026: petugasnya campuran antara karyawan dan
tenaga rekanan, checklist diisi pengawas GA dan bukan petugasnya sendiri, jadwal shift mencatat
rencana sekaligus kehadiran, dan insiden bisa diteruskan menjadi tiket perbaikan.

- Data induk petugas dengan tabelnya sendiri, bukan menumpang di daftar karyawan. Daftar karyawan
  dipakai untuk hal hal yang tidak berlaku bagi tenaga rekanan: departemen yang dibebani biaya,
  atasan yang menyetujui, akun yang bisa masuk, dan NIP. Satu baris hanya boleh berbentuk salah
  satu, dan penjaganya bekerja dengan melihat kolom mana yang baru berubah, bukan kolom mana yang
  kebetulan terisi
- Area layanan dengan seberapa sering dibersihkan dan siapa penanggung jawabnya sebagai kolom,
  tanpa tabel jadwal tersendiri. Tabel jadwal baru berguna kalau satu area punya petugas berbeda
  per hari, dan itu belum jadi kebutuhan yang disebutkan
- Putaran pemeriksaan yang daftar areanya lahir sendiri saat putaran dibuat, berbeda dari opname
  yang menyusun daftarnya lewat tombol karena penyusunan di sana sekaligus membekukan stok
- Hasil pemeriksaan tiga tingkat, bukan dua. Tanpa tingkat tengah, area yang sudah disapu tetapi
  tempat sampahnya masih penuh akan dicatat sebagai bersih supaya tidak terasa berlebihan
- Nama area dan nama petugas disalin ke tiap baris, jadi lembar bulan lalu tidak ikut berpindah
  menuduh orang yang saat itu belum bertugas di sana

**Kiriman Q, keamanan, sudah ditulis dan lulus gate:**

- Jadwal jaga yang menyimpan rencana dan kenyataan berdampingan. Menimpa nama yang dijadwalkan
  dengan nama penggantinya menghapus dua pertanyaan sekaligus: berapa kali seseorang tidak masuk,
  dan berapa kali ia menggantikan orang lain
- Laporan insiden tanpa kolom status sendiri. Insiden yang butuh perbaikan fisik diteruskan
  menjadi tiket perbaikan yang alurnya sudah berjalan sejak kiriman G, dan menutup insiden
  sengaja tidak menutup tiketnya, karena urusan keamanan selesai bukan berarti perbaikannya
  selesai
- Tiket dibuat atas nama orang yang menekan tombol, bukan petugas yang melapor, karena satpam
  yang tenaga rekanan tidak punya departemen dan tiketnya tidak akan pernah bisa disetujui
  siapa pun
- Jam mulai tiap shift sengaja tidak ditulis di kode. Jam jaga adalah kesepakatan perusahaan,
  bukan angka yang boleh dikarang, dan yang tercatat hanya jam masuk dan jam pulang sebenarnya

Satu putaran pemeriksaan penuh dan satu insiden yang berakhir menjadi tiket dijalankan sampai
selesai pada 8 September 2026. Tercatat di `DELIVERY-GATE-17.md`, beserta enam cacat yang
ditemukan dan diperbaiki, dua di antaranya berupa tombol Simpan yang tidak menyimpan apa pun
tanpa satu pun pesan, dan satu pesan kosong yang menyuruh pembacanya menghapus pekerjaannya
sendiri.

Gate itu juga memuat satu kesalahan saya sendiri yang perlu dibaca: laporan keliru bahwa
lampiran berkas rusak di seluruh aplikasi, yang ternyata artefak cara saya memeriksa.

**Yang masih perlu Anda coba sendiri:** unggah satu foto pada satu area pemeriksaan, karena
jalur itu belum terbukti sampai berkasnya benar benar tersimpan. Alasannya ada di bagian 6
`DELIVERY-GATE-17.md`.

**Nama menu diganti pada 8 September 2026:** Maintenance Schedules menjadi Preventive
Maintenance, dan Service Requests menjadi Corrective Maintenance. Kode modul dan nama tabelnya
tidak berubah, jadi seluruh izin yang sudah diatur tetap utuh.

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

## Tahap 7: Menutup siklus ATK (selesai 8 September 2026)

Dipilih pemilik proyek pada 8 September 2026, mendahului Tahap 8, setelah membaca analisis
sistem referensi di `ROADMAP-REFERENSI.md`. Ditaruh lebih dulu karena seluruh bahannya sudah
ada di GAIS: barang habis pakai, buku stok, rekanan, kategori biaya, anggaran, dan tagihan
rekanan semuanya sudah jalan, jadi tahap ini hanya menyambungkan yang sudah ada.

Tiga keputusan pemilik proyek pada 8 September 2026:

| Pertanyaan | Jawaban |
|---|---|
| Cara membeli ATK | Dua duanya: barang rutin dipesan resmi ke pemasok, barang mendadak dibeli langsung |
| Siapa yang boleh mengajukan | Keduanya, dibedakan lewat izin, bukan lewat jenis permintaan baru |
| Persetujuan permintaan | Atasan lalu tim GA |

**Kiriman M, permintaan pemakaian ATK, sudah ditulis dan lulus gate:**

- Alur tiga langkah: karyawan meminta, atasan menyetujui, tim GA menyerahkan. Kembar dengan
  tiket perbaikan, pemesanan kendaraan, dan penggantian biaya, sengaja, supaya orang yang
  meminta pulpen tidak perlu mempelajari layar yang berbeda dari yang ia pakai melapor AC bocor
- Langkah terakhir bukan penandaan melainkan perubahan angka. Menyerahkan barang melahirkan
  satu mutasi barang keluar per baris, stok berkurang, dan sejak kiriman J stok yang berkurang
  itu langsung menjadi realisasi anggaran ATK departemen pemohon
- Seluruh penyerahan dibungkus satu transaksi basis data. Kalau baris kelima gagal, empat
  mutasi sebelumnya ikut batal, karena penyerahan yang berhenti di tengah meninggalkan gudang
  yang catatannya tidak sama dengan isinya
- Dua kolom jumlah yang berbeda pemiliknya: yang diminta ditulis pemohon dan terkunci setelah
  diajukan, yang diserahkan ditulis tim GA. Kejadian paling sering di gudang ATK adalah diminta
  sepuluh, ada tujuh, diserahkan tujuh, dan menimpa angkanya menghapus tiga yang tidak terpenuhi
- Baris yang jumlah serahnya nol tidak melahirkan mutasi apa pun, dan tetap tersimpan sebagai
  catatan permintaan yang tidak terpenuhi
- Departemen wajib diisi, berbeda dari penggantian biaya. Karena itu hanya dua keadaan yang
  melewati persetujuan atasan, bukan tiga
- Izin baru `request_for_others` yang memisahkan karyawan biasa dari perwakilan departemen.
  Tanpa izin itu pilihan pemohon terkunci pada diri sendiri, dan penguncian itu dijaga di
  daftar pilihannya, di atribut formulirnya, dan sekali lagi sebelum disimpan
- Izin baru `issue` untuk menyerahkan barang, dipisah dari `approve`, karena yang menyetujui
  keperluannya adalah atasan pemohon sedangkan yang membuka lemari adalah tim GA
- Pilihan barang membawa stok terkini di dalam kurung. Barang yang stoknya kosong tetap boleh
  diminta, karena permintaan yang tidak terpenuhi adalah data, bukan kesalahan
- Widget antrean permintaan di tab Office Supplies pada dasbor, dengan kolom kesiapan stok yang
  menjawab pertanyaan "mana yang bisa saya kerjakan hari ini", bukan hanya "mana yang menunggu"

Rantai permintaan sampai anggaran dibuktikan sampai rupiah terakhir pada 8 September 2026:
penyerahan 3 rim kertas menaikkan realisasi ATK Finance tepat Rp 186.000, sama dengan 3 dikali
harga satuan terakhir Rp 62.000. Tercatat di `DELIVERY-GATE-14.md`.

**Kiriman N, pembelian dan penerimaan ATK, sudah ditulis dan lulus gate:**

Tiga keputusan pemilik proyek pada 8 September 2026: pesanan disetujui manajer GA, penjual
pada pembelian langsung boleh ditulis bebas, dan tagihan dihubungkan ke pesanan sekalian.

- Dua bentuk pembelian di satu tabel dan satu layar, dibedakan kolom jenisnya. Pesanan resmi
  disetujui manajer sebelum dikirim ke pemasok, pembelian langsung dicatat setelah barangnya
  sudah di tangan dan tidak melewati persetujuan, karena persetujuan atas uang yang sudah
  keluar tidak bisa mencegah apa pun. Alasan lompatannya ditulis di kolomnya sendiri
- Penerimaan sebagai dokumen tersendiri, boleh berkali kali atas satu pesanan. Jumlah yang
  sudah diterima tidak pernah disimpan sebagai kolom melainkan dijumlahkan dari baris
  penerimaannya, pola yang sama dengan stok sejak kiriman C
- Penerimaan melahirkan mutasi barang masuk lengkap dengan harga pesanannya, dan harga itu
  memperbarui sendiri harga pembelian terakhir di kartu barang. Sejak kiriman ini, stok ATK
  tidak lagi bisa bertambah tanpa asal usul
- Kelebihan terima ditolak dengan menyebut nama barang dan kedua angkanya. Kekurangan tidak
  ditolak, karena barang yang datang kurang justru alasan penerimaan sebagian ada
- Menutup pesanan yang sisanya tidak akan datang, dengan alasan tertulis. Sisa yang batal tetap
  terbaca di barisnya, jadi menutup pesanan tidak menghapus catatan apa pun
- Tagihan rekanan boleh menunjuk pesanan, dan daftarnya disempitkan ke pesanan milik rekanan
  yang dipilih. Layar lalu menyebut selisih antara nilai tagihan dan nilai barang yang benar
  benar sudah diterima, bukan nilai pesanan, karena faktur yang menagih sepuluh box sementara
  yang datang baru tujuh adalah persis keadaan yang akan tersembunyi kalau dibandingkan dengan
  nilai pesanan. Kalimat itu muncul juga di kotak persetujuan, karena di situlah orang benar
  benar membacanya

Aritmetikanya dibuktikan dengan hitungan tangan pada 8 September 2026, termasuk penerimaan
bertahap 268.000 lalu 400.000 dan keempat cabang kalimat selisih tagihan. Tercatat di
`DELIVERY-GATE-15.md`, beserta satu cacat fatal dan satu cacat angka terbalik yang ditemukan
dan diperbaiki di ronde itu.

**Kiriman O, opname ATK dan penyesuaian stok, sudah ditulis dan lulus gate:**

Tiga keputusan pemilik proyek pada 8 September 2026: koreksi langsung boleh tetapi wajib
beralasan, sesi opname bebas seperti opname aset dan bukan per bulan, dan penyesuaian stok
perlu tindakan terpisah yang berizin sendiri.

- Sesi opname sebagai dokumen berumur, dengan cakupan kategori atau lokasi. Daftar barangnya
  lahir dari cakupan itu, bukan diketik satu per satu, dan penyusunannya sekaligus membekukan
  stok menurut catatan sebagai pembanding
- **Stok tidak tersentuh sampai langkah terakhir.** Menghitung tidak mengubah apa pun. Yang
  mengubah adalah Apply Adjustment, dan tindakan itu memakai izin `adjust` yang dipisah dari
  `update`, jadi staf boleh menghitung sementara yang menggeser angka gudang adalah manajer
- Penyesuaian melahirkan mutasi koreksi, satu per baris yang selisih, membawa nomor sesinya
  dan kedua angkanya di dalam keterangan. Barang yang cocok dan barang yang tidak jadi dihitung
  tidak melahirkan apa apa. Buku stok karenanya tetap bisa menjelaskan setiap perubahannya
- **Selisih dihitung terhadap stok terbaru, bukan terhadap angka beku.** Kalau ia dihitung
  terhadap angka beku, setiap penyerahan yang terjadi di sela sela penghitungan akan terkoreksi
  balik dan terhapus dari buku stok tanpa jejak. Angka beku dipakai untuk hal lain: mendeteksi
  bahwa barangnya sempat bergerak, lalu keadaan itu disebutkan di layar di tiga tempat sekaligus,
  tidak diperbaiki diam diam
- Koreksi langsung di luar opname tetap boleh, tetapi kolom catatannya berubah sendiri menjadi
  Alasan koreksi yang wajib diisi begitu jenis koreksi dipilih
- Awalan nomor permintaan barang diperbaiki dari `PB` menjadi `PM`, karena `PB` sudah dipakai
  permintaan perbaikan sejak kiriman G dan nomor yang sama bisa lahir dua kali. Nomor yang
  terlanjur terbit tidak diubah

Satu sesi penuh atas 9 barang dijalankan sampai selesai pada 8 September 2026, menghasilkan
tepat tiga mutasi koreksi dan tidak satu pun untuk barang yang cocok maupun yang tidak
dihitung. Tercatat di `DELIVERY-GATE-16.md`, beserta empat cacat yang ditemukan dan diperbaiki,
termasuk satu tombol yang membuka daftar tanpa saring dan satu pengukuran jujur bahwa aturan
tap target 44 piksel belum terpenuhi di seluruh aplikasi.

**Siklus ATK sekarang tertutup dari ujung ke ujung:** karyawan meminta, atasan menyetujui, GA
menyerahkan dan stok turun menjadi realisasi anggaran, GA memesan, manajer menyetujui, barang
datang dan stok naik beserta harga terakhirnya, rekanan menagih dan selisihnya dibandingkan
dengan barang yang benar benar datang, lalu gudang dihitung fisik dan angkanya disesuaikan.

**Pertanyaan kebijakan yang masih menunggu jawaban Anda:** anggaran ATK sekarang menghitung
pemakaian, bukan pembelian, sehingga faktur pembelian ATK tidak bisa dibebankan ke kategori
ATK. Kedua cara sama sama dipakai perusahaan sungguhan, dan uraiannya ada di bagian 6
`DELIVERY-GATE-15.md`.

**Satu ketidakseragaman yang dicatat, tidak diperbaiki:** opname aset memakai izin `approve`
untuk konsep yang di opname ATK bernama `adjust`. Merapikannya menyentuh izin yang mungkin
sudah Anda atur sendiri di layar Role, jadi menunggu permintaan Anda.

## Kelengkapan demo: surat, paket, dan perjalanan dinas (selesai 8 September 2026)

Diminta pemilik proyek pada 8 September 2026, mendahului sisa Tahap 5 dan Tahap 6, dengan
alasan yang disebut sendiri: kelengkapan fitur lebih mendesak supaya sistemnya bisa
diperlihatkan dan dicoba dalam demo. Ketiganya diambil dari peta lanjutan di
`ROADMAP-REFERENSI.md`, dan yang dibangun adalah alur intinya saja, bukan seluruh cabangnya.

Empat keputusan pemilik proyek pada 8 September 2026:

| Pertanyaan | Jawaban |
|---|---|
| Kedalaman | Alur inti ketiganya dulu |
| Nomor surat | Diketik mengikuti format perusahaan, terpisah dari nomor agenda |
| Biaya kiriman dan perjalanan | Masuk ke realisasi anggaran |
| Uang jalan | Ada uang muka, dan dipertanggungjawabkan sepulangnya |

**Kiriman R, surat masuk dan surat keluar:** satu buku agenda dengan dua urutan nomor yang
terpisah (AM dan AK), nomor surat perusahaan diketik sendiri, pindaian dilampirkan, dan surat
masuk punya satu langkah lanjutan yaitu serah terima yang mencatat penerima sebenarnya.

**Kiriman S, pengiriman paket:** permintaan kirim, keberangkatan lewat kurir yang memakai
data rekanan yang sudah ada, dan biaya yang dipecah per komponen supaya cocok dengan cara
kurir menagih. Biayanya masuk realisasi anggaran begitu paketnya berangkat.

**Kiriman T, perjalanan dinas:** lima langkah berizin terpisah, yaitu mengajukan, menyetujui,
membayarkan uang muka, mempertanggungjawabkan, dan menutup. Biayanya baru masuk realisasi
anggaran setelah pertanggungjawabannya ditutup, karena hanya rincian yang sudah diperiksa yang
pantas memotong pagu.

**Rombongan, diminta 8 September 2026 setelah kiriman T selesai:** satu perjalanan bisa
memberangkatkan beberapa orang dengan satu penanggung jawab yang memegang uang dan
pertanggungjawabannya. Penanggung jawab tetap menjadi orang yang menentukan departemen yang
dibebani, atasan yang menyetujui, dan penerima uang muka, sehingga tidak ada satu pun angka
kiriman T yang berubah.

Ketiganya diverifikasi pada 8 September 2026 dan tercatat di `DELIVERY-GATE-18.md`, beserta
satu cacat yang ditemukan dan diperbaiki (D-37).

**Yang belum ada pada ketiganya:** disposisi surat berjenjang, pelacakan resi otomatis, dan
tarif uang harian per golongan. Ketiganya ada di uraian lengkap Tahap 8 sampai Tahap 10 di
`ROADMAP-REFERENSI.md` dan menunggu giliran tahapnya.

---

## Yang perlu Anda putuskan sebelum tahap terkait dimulai

| Sebelum | Keputusan yang dibutuhkan |
|---|---|
| Tahap 2 | Sudah dijawab pada 6 September 2026: kode dibuat otomatis, label dicetak di printer biasa dengan stiker A4, data awal ada tapi berantakan sehingga impor dibuat toleran |
| Tahap 3 | Metode penyusutan, tanggal mulai penyusutan, dan apakah ada aset lama yang sudah berjalan penyusutannya |
| Tahap 4 | Sudah dijawab pada 7 September 2026: tiket menjadi pintu masuk yang berubah jadi perintah kerja, atasan pemohon menyetujui lebih dulu, dan tiket dikerjakan sebelum kendaraan. Untuk kendaraan: ada pool car yang dipesan, ada sopir, ada kendaraan operasional, dan BBM dicatat per pengisian dengan odometer supaya kilometer per liter bisa dihitung. Target SLA per jenis masih menunggu kesepakatan tim GA, dan kolomnya sengaja dibiarkan kosong sampai itu terjadi |
| Tahap 5 | Sudah dijawab pada 7 September 2026: anggaran per departemen per kategori, ditetapkan tahunan, dan reimbursement disetujui atasan lalu GA tanpa batas nominal |
| Tahap 7 | Sudah dijawab pada 8 September 2026: ATK dibeli dua cara (pesanan resmi dan pembelian langsung), pemohon dibedakan lewat izin, dan permintaan disetujui atasan lalu tim GA |
| Tahap 6 | Sistem akuntansi yang dipakai, daftar nomor akun, dan apakah pertukaran data lewat berkas atau API |

## Yang belum masuk peta ini

Ditulis supaya jelas bahwa ini memang belum direncanakan, bukan terlupa:
kontrak sewa gedung, absensi, aplikasi mobile, dan integrasi dengan sistem HR. Kalau salah
satunya dibutuhkan, sebutkan dan saya masukkan ke peta.

Pengadaan vendor, surat dan paket, parkir, serta perjalanan dinas sudah tidak ada di daftar ini
lagi. Ketiganya masuk peta lanjutan pada 8 September 2026 dan diuraikan di
`ROADMAP-REFERENSI.md` sebagai Tahap 8 sampai Tahap 10. Alur inti surat, paket, dan perjalanan
dinas malah sudah dibangun lebih dulu pada 8 September 2026 untuk keperluan demo, dan tercatat
di bagian Kelengkapan demo di atas.
