# Anti AI Slop: Aturan Desain dan Copy

> Ikuti aturan ini setiap kali membuat atau membangun UI untuk website, aplikasi web, atau antarmuka apa pun.
> Tujuannya: desain harus terasa **dikerjakan desainer**, bukan dihasilkan AI.

Sumber: https://github.com/miqdadbadjuber/anti-slop (MIT). Berkas ini adalah adaptasi bahasa Indonesia
untuk proyek GAIS. ID aturan (R-01 sampai R-38) dipertahankan persis seperti sumbernya.

---

## Ini Apa (dan Bukan Apa)

`ANTISLOP.md` adalah **filter**, bukan style guide. Tugasnya mencegah agen menghasilkan UI "AI slop"
yang generik, tanpa jatuh ke kegagalan sebaliknya: hasil steril tanpa nyawa.

- Berkas ini **tidak** memaksakan estetika: tidak ada warna, font, layout, atau "house style" yang diwajibkan.
- Berkas ini **tidak** melarang teknik visual (gradien, glassmorphism, badge, grid kartu). Itu semua alat.
  Yang ditolak adalah **teknik tanpa tujuan**.
- Berkas ini hanya melakukan dua hal:
  1. Menguji setiap keputusan visual dengan **uji tujuan**: teknik ini melayani apa? Tulis alasannya.
  2. Menguji hasilnya dengan **batas keliveliness**: hasil harus hidup dan spesifik, bukan sekadar "bersih". Lihat Bagian 3.

Tiga berkas bekerja bersama:

- `DESIGN.md` memberi **jiwa**: identitas, kepribadian, palet, tipografi, suasana.
- `CLAUDE.md` mengarahkan agen: untuk tugas UI, baca `DESIGN.md` dulu, lalu `ANTISLOP.md` sebagai filter.
- `ANTISLOP.md` menolak slop dan menuntut liveliness. Berkas ini tidak menciptakan arah.

Menghapus slop tidak otomatis memunculkan desain bagus, yang tersisa adalah kekosongan. Liveliness harus
**ditambahkan**, bukan diasumsikan. Hasil steril berarti arahnya hilang atau liveliness tidak ditambahkan,
dan keduanya adalah kegagalan yang harus diperbaiki.

## Prinsip Inti

Sebelum memakai teknik visual apa pun, jawab: **ini melayani apa?** Kalau jawabannya cuma "biar aman" atau
"biar kelihatan modern", teknik itu harus dibuang atau dikerjakan ulang. Kalau jawabannya menyebut tujuan
hierarki, identitas, atau keterbacaan, teknik itu boleh dipakai dan alasannya ditulis.

Pertanyaan yang harus lolos sebelum sesuatu dinyatakan selesai:

> Kalau logo dan nama produknya diganti, apakah desain ini masih terasa khas dan punya karakter sendiri?

Kalau jawabannya **tidak**, desainnya terlalu generik. Ulangi.

Desain **selesai** hanya kalau ketiganya benar:
1. Setiap teknik lolos uji tujuan (Grup Purpose-Gate di Bagian 2).
2. Punya identitas dan karakter sendiri (Bagian 3).
3. Benar-benar berfungsi (Standar Kriya di bawah).

## Standar Kriya

### C-1 Intensionalitas
Setiap keputusan visual dan copy punya alasan yang bisa diucapkan. Kalau alasannya cuma "default AI", itu tanda bahaya.

### C-2 Kelengkapan Fungsi
Setiap elemen interaktif berfungsi, atau tidak ada. Tombol yang tidak melakukan apa-apa adalah cacat, bukan hiasan.

### C-3 Komposisi Digerakkan Konten
Setiap seksi ada karena konten produk membutuhkannya, bukan karena semua halaman AI punya seksi itu.

### C-4 Ketahanan
UI bertahan di semua state (kosong, memuat, error), semua tema yang dikirim, semua breakpoint, dan penggunaan keyboard saja.

### C-5 Bukti di Atas Klaim
Apa pun yang disajikan sebagai fakta (testimoni, statistik, klaim keamanan) nyata dan bisa diverifikasi, atau tidak ditampilkan sama sekali.

---

## Bagian 1: Pola AI Slop (Tanda Bahaya)

Ini pola paling umum pada desain hasil AI. Pakai daftar ini untuk **mengaudit** hasil: cari kluster,
lalu tanya masing-masing "ini melayani apa?" Satu pola dari daftar ini boleh saja kalau ada tujuannya,
kecuali dilarang oleh aturan **Hard Gate** di Bagian 2. Yang membuat desain jadi slop adalah banyak pola
ini muncul bersamaan tanpa alasan.

### Visual dan Warna

| Pola | Tanda |
|---|---|
| Gradien biru ungu generik | Biru ke ungu, biru ke cyan, ungu ke pink, latar glow satu halaman penuh |
| Glassmorphism berlebihan | Blur di navbar, kartu, modal, sidebar sekaligus |
| Border radius berlebihan | Semua elemen berbentuk pil: tombol, input, kartu, badge, modal |
| Bayangan terlalu lembut | Semua komponen berbayang besar, seluruh halaman terasa melayang |
| Glow di mana-mana | Glow di kartu, tombol, ikon, badge, latar, dan border sekaligus |
| Grid latar | Kotak grid, garis blueprint, kertas milimeter |
| Terlalu banyak dekorasi | Blob, mesh gradient, glow, noise, pattern, grid tanpa tujuan, apalagi ditumpuk |
| Dark mode default tanpa alasan | Seluruh halaman gelap cuma karena kelihatan techy |
| Palet terlalu banyak warna | 5 sampai 7 warna berbeda di satu halaman tanpa sistem yang jelas |
| Aksen berlebihan | Satu warna aksen dipakai di tombol, ikon, badge, link, garis, latar, dan glow |
| Default steril | Putih rata, border abu tipis, radius kecil, font generik, tanpa identitas |
| Skeleton sebagai foto produk | Balok abu placeholder dipakai sebagai screenshot produk |

### Layout dan Komponen

| Pola | Tanda |
|---|---|
| Layout monoton | Hero, subjudul, 2 CTA, screenshot, grid fitur, testimoni, FAQ, CTA, footer |
| Kartu fitur copy paste | Ukuran, tinggi, ikon, layout, padding identik di semua kartu |
| Spacing seragam | Padding, margin, dan jarak elemen sama persis di semua seksi |
| Mobile rusak | Overflow horizontal, kartu terpotong, navbar rusak, teks bertabrakan |
| Animasi template | Semua elemen memakai Fade Up, Fade In, Floating, Scale, Bounce |
| "How It Works" selalu 3 langkah | Ikon bulat, angka 1 2 3, teks pendek, selalu tiga |
| Bar logo "Trusted By" | Deretan logo generik tepat di bawah hero |
| Kartu harga "Most Popular" | Tier tengah selalu disorot dengan badge kapsul |
| Footer template 4 kolom | Product / Company / Resources / Legal tanpa variasi |
| Ritme seksi seragam | Semua seksi berkomposisi sama: judul tengah, subjudul, grid kartu identik |
| Cuma latar selang seling | Satu satunya variasi antar seksi adalah warna latar bergantian |

### Copywriting dan Konten

| Pola | Tanda |
|---|---|
| Em dash | "Cepat, aman — dan siap dipakai." |
| CTA generik | Get Started, Learn More, Try Now, Explore, Discover |
| Buzzword marketing AI | AI Powered, Revolutionary, Next Generation, Seamless, Cutting Edge |
| Statistik palsu | 10K+ Users, 99.9% Uptime, 500M Requests |
| Testimoni palsu | Avatar AI, nama acak, jabatan acak, ulasan fiktif |
| Klaim kepercayaan karangan | "SOC 2 compliant", "ISO 27001", "300% lebih cepat" tanpa bukti |

### Elemen Dekoratif

| Pola | Tanda |
|---|---|
| Ikon AI generik | Sparkle, Star, Magic, Lightning, Diamond, Cube, Robot, AI Orb |
| Panah kecil | Ditempel di hampir semua tombol sebagai hiasan |
| Badge kapsul AI | Pil, border tipis, glow, titik kecil, uppercase, isi "AI Powered", "Beta", "New" |
| Tipografi AI generik | Heading monospace besar, uppercase dengan tracking lebar |
| Typeface tanpa alasan | Font dipilih karena default AI, bukan karena cocok dengan karakter merek |
| Ilustrasi generik | Undraw, Storyset, karakter blob 3D tanpa hubungan dengan produk |

### Fungsi dan Konten

| Pola | Tanda |
|---|---|
| Elemen interaktif mati | Tombol tidak melakukan apa-apa, dropdown tidak terbuka, form tidak bisa dikirim |
| Cuma happy path | Tidak ada empty state, loading state, atau error state |
| FAQ tidak relevan | Pertanyaan template yang tidak ada hubungannya dengan produk |
| Logo dan foto profil karangan | Membuat logo, avatar, atau foto tanpa instruksi eksplisit |
| Link navbar ke ruang kosong | Navbar berisi link ke halaman yang tidak ada |
| Patching berkas lewat skrip | Fitur ditambahkan lewat skrip eksternal yang menimpa source atau CSS dengan string replace |

### Identitas dan Orisinalitas

| Pola | Tanda |
|---|---|
| Tanpa identitas visual | Logo diganti, desainnya tetap terasa sama, bisa milik produk mana saja |
| Klon produk populer | Visual meniru Linear, Vercel, Stripe, Notion tanpa diminta |

### Aksesibilitas

| Pola | Tanda |
|---|---|
| Kontras warna buruk | Teks abu di atas latar abu, teks putih di atas gradien yang terang di sebagian area |
| Tidak bisa dipakai keyboard | UI hanya bisa dipakai dengan mouse, tidak ada focus state yang terlihat |

---

## Bagian 2: Aturan Wajib (R-01 sampai R-38)

Semua 38 aturan berlaku. Dikelompokkan tiga tingkat: **Hard Gate** mutlak, **Purpose-Gate** membolehkan
teknik tetapi menuntut alasan tertulis, **Quality Locks** adalah syarat konsistensi.

### Grup 1: Hard Gate (mutlak, tanpa pengecualian)

#### R-02 Copywriting
- **DILARANG**: karakter em dash (`—`) di teks mana pun.
- Pakai koma, titik, titik dua, atau tanda kurung.
- Teks harus terasa wajar dan manusiawi.

#### R-03 Responsif Mobile
- **WAJIB**: layout mobile harus benar, bukan pikiran belakangan.
- Tidak ada overflow horizontal, teks tidak keluar kontainer, kartu tidak bertabrakan atau terpotong.
- Navbar tetap nyaman dipakai. Ukuran tombol memenuhi tap target minimum 44px.
- Spacing konsisten di semua breakpoint.

#### R-17 Data dan Angka
- **DILARANG**: angka dan statistik tanpa sumber nyata.
- Kalau data nyata tidak ada, jangan tampilkan angka apa pun. Kosong lebih baik daripada menyesatkan.

#### R-18 Testimoni
- **DILARANG**: avatar AI, nama acak, jabatan acak, ulasan fiktif.
- Kalau belum punya testimoni asli, jangan buat seksi testimoni.

#### R-19 lihat Grup 2.

#### R-23 Klarifikasi dan Aset Visual
- **WAJIB**: sebelum membuat aset tanpa instruksi eksplisit, tanya dulu atau pakai placeholder yang jelas.
- Yang wajib dikonfirmasi: logo atau ikon aplikasi, avatar dan foto orang, statistik dan angka,
  nama dan jabatan di testimoni, struktur navigasi dan layout halaman.
- Kalau tidak bisa bertanya: pakai placeholder yang jelas dan jangan menyamarkannya sebagai final.
  Logo: nama produk sebagai teks, atau penanda `[LOGO]`. Foto profil: avatar inisial. Statistik: tidak ditampilkan.

#### R-24 Navigasi
- **DILARANG**: menaruh link di navigasi menuju halaman atau seksi yang tidak ada.
- Setiap item navigasi harus punya tujuan nyata yang bisa diakses.
- Kalau fitur belum dibangun, jangan masukkan ke navigasi, atau beri label jelas bahwa belum tersedia.

#### R-25 Kontras Warna
- **WAJIB**: semua teks memenuhi WCAG AA. Teks normal minimal 4.5:1, teks besar (18px ke atas) minimal 3:1.
- **DILARANG**: teks abu terang di atas latar abu, teks putih di atas gradien yang terang di sebagian area.
- Uji kontras di seluruh area yang dilewati teks, bukan di satu titik.

#### R-26 Elemen Interaktif
Setiap elemen interaktif harus punya perilaku nyata, atau dihapus:
- Link atau tombol yang menuju seksi yang benar-benar ada
- Modal atau dialog yang bisa dibuka dan ditutup (bisa ditutup dengan Escape)
- Toggle state (menu mobile, tema, accordion, tab)
- Aksi eksternal (`mailto:`, URL produk nyata)
- Form yang terkirim dan memberi umpan balik

**DILARANG**: tombol dan link yang tidak melakukan apa-apa. Placeholder hanya boleh dengan komentar
`// TODO` di kode DAN label yang terlihat pengguna, misalnya "Belum tersedia".

#### R-27 State UI
- **WAJIB**: setiap UI yang menampilkan data punya minimal tiga state: kosong, memuat, error.
- UI yang hanya dirancang untuk kondisi ideal belum siap dipakai.

#### R-28 FAQ
- **DILARANG**: FAQ berisi pertanyaan template yang tidak spesifik untuk produk.
- Kalau tidak tahu pertanyaan yang benar-benar ditanyakan, jangan buat seksi FAQ.

#### R-32 Aksesibilitas Keyboard
- **WAJIB**: semua elemen interaktif bisa dicapai dan dioperasikan dengan keyboard.
  Tab dan Shift+Tab mengikuti urutan visual, tombol aktif dengan Enter atau Space, dialog tertutup dengan Escape.
- **WAJIB**: setiap elemen yang difokus punya indikator fokus yang jelas terlihat.
- **DILARANG**: menghapus focus outline tanpa menggantinya dengan indikator fokus kustom yang lebih baik.

#### R-33 Tanpa Patching Berkas lewat Skrip
- **DILARANG**: mengubah fitur UI dengan skrip eksternal yang menimpa source atau CSS memakai string replace.
- Bangun fitur langsung di source tempat seharusnya.

#### R-34 Semua Tema yang Dikirim Harus Berfungsi
- Kalau mengirim toggle tema, kedua mode wajib berfungsi penuh.
- Kontras, warna, dan setiap komponen diverifikasi di masing-masing mode.

#### R-35 Verifikasi Sebelum Menyerahkan
- Jalankan atau build aplikasi sebelum menyatakan selesai.
- Periksa console, coba setiap elemen interaktif, cek semua tema dan breakpoint mobile.
- Desain yang belum pernah dijalankan belum selesai.

#### R-36 Tanpa Klaim Karangan
- **DILARANG**: mengarang klaim keamanan, kepatuhan, atau performa tanpa bukti nyata.
- Kalau tidak ada data nyata, jangan tampilkan klaim.

#### R-37 Arah Desain Wajib Ada
- Sebelum membangun UI, muat arah gaya: `DESIGN.md` atau arahan merek eksplisit dari pengguna.
- Kalau tidak ada arah, tanya. Kalau tidak bisa bertanya, hasilnya WAJIB dilabeli
  *"draft without direction"* dan memakai dial jujur **ENERGY 1 / RHYTHM 1 / MOTION 1**.
- **DILARANG**: mendesain tanpa arah lalu diam diam jatuh ke default netral yang steril.

#### R-38 Konten Nyata atau Placeholder Jujur
- Setiap klaim, fitur, testimoni, statistik, item navigasi, atau elemen visual berasal dari informasi nyata
  ATAU merupakan placeholder yang dilabeli eksplisit.
- **DILARANG**: mengarang konten yang terlihat realistis.
- Placeholder ditulis apa adanya: `[DATA NYATA]`, "Belum tersedia", tidak pernah disamarkan sebagai final.

### Grup 2: Purpose-Gate (teknik boleh, alasan wajib ditulis)

#### R-01 Warna dan Gradien
- **DILARANG sebagai default tanpa tujuan**: gradien biru ke ungu, biru ke cyan, ungu ke pink sebagai warna
  utama, latar glow berwarna, tombol biru neon.
- **BOLEH** kalau warna atau gradien itu bagian dari identitas merek yang sudah ada ATAU melayani tujuan
  hierarki yang dinyatakan, dengan alasan tertulis.

#### R-04 Ikon
- **DILARANG sebagai default tanpa tujuan**: Sparkle, Star, Magic, Lightning, Diamond, Orb, Robot sebagai ikon fitur.
- Ikon harus benar benar relevan dengan konten yang diwakili, relevansinya ditulis.
- Kalau tidak ada ikon yang tepat, lebih baik tanpa ikon.

#### R-06 Tipografi
- **DILARANG sebagai default tanpa tujuan**: font monospace besar demi estetika terminal, label uppercase
  dengan letter spacing ekstrem.
- Pilih typeface berdasar karakter merek, tulis alasannya.

#### R-07 Latar
- **DILARANG sebagai default tanpa tujuan**: grid kotak, garis blueprint, kertas milimeter sebagai latar.

#### R-08 Panah Tombol
- Panah bukan identitas default setiap tombol. Kalau dipakai, ukurannya proporsional dan tujuannya ditulis.

#### R-09 Badge
- **DILARANG sebagai default tanpa tujuan**: badge kapsul berisi "AI Powered", "Beta", "New", "Secure", "Fast" tanpa konteks.
- Badge hanya untuk status atau label nyata. Hindari menggabungkan kapsul, border tipis, glow, titik kecil, dan uppercase sekaligus.

#### R-10 Glassmorphism
- Hanya aksen, bukan karakter seluruh UI. **Batas dosis**: blur pada maksimal 1 sampai 2 elemen.

#### R-12 Bayangan
- Bayangan mendukung hierarki, bukan membuat semua elemen melayang. Alasan elevasi ditulis.

#### R-13 Glow
- Hanya aksen fokus pada maksimal 1 sampai 2 elemen penting.

#### R-14 Kartu Fitur
- **DILARANG sebagai default tanpa tujuan**: semua kartu berukuran, berikon, berpadding, dan berlayout identik.
- Buat variasi visual yang mencerminkan hierarki konten, alasannya ditulis.

#### R-19 Animasi
- Animasi harus punya tujuan UX yang jelas dan ditulis.
- **DILARANG sebagai default tanpa tujuan**: semua elemen memakai Fade Up, Floating, Scale, Bounce sekaligus.
- Gerak harus sesuai dial MOTION yang dideklarasikan.

#### R-22 Ilustrasi
- **DILARANG sebagai default tanpa tujuan**: Undraw, Storyset, karakter blob 3D generik.
- Ilustrasi harus punya kaitan langsung dengan produk, kaitannya ditulis.

### Grup 3: Quality Locks (konsistensi)

#### R-05 Layout dan Struktur Halaman
- **DILARANG**: layout template AI (Hero + 3 kartu, Hero + 6 fitur, Hero + statistik palsu).
- **DILARANG**: "How It Works" selalu 3 langkah, bar logo "Trusted By" tepat di bawah hero,
  footer template 4 kolom tanpa variasi, semua seksi memakai pola internal yang sama.
- Struktur halaman dibangun dari kebutuhan konten nyata. Komposisi seksi harus cocok dengan dial RHYTHM.

#### R-11 Border Radius
- Radius konsisten dengan design system. **DILARANG**: membuat semua elemen berbentuk pil.
- Variasi radius adalah alat hierarki, pakai secara sengaja.

#### R-15 CTA
- **DILARANG**: "Get Started", "Learn More", "Try Now", "Explore", "Discover" sebagai CTA default.
- CTA harus spesifik terhadap konteks produk dan aksi yang dimaksud.

#### R-16 Copywriting dan Buzzword
- **DILARANG**: "AI Powered", "Next Generation", "Revolutionary", "Seamless", "Cutting Edge",
  "Intelligent", "Ultimate", "Powerful", "Effortless".
- Pakai bahasa spesifik yang menjelaskan manfaat nyata.

#### R-20 Identitas Visual
- Desain harus punya identitas kuat: palet spesifik, typeface yang dipilih dengan alasan, komposisi khas.
- Setiap seksi punya hierarki yang jelas.

#### R-21 Dark Mode
- Pilih tema berdasar identitas merek, jenis produk, dan pengguna sasaran.
- Kalau produk tidak punya alasan kuat untuk tema tetap, **bangun toggle terang gelap yang berfungsi**.
- **DILARANG**: memakai aturan apa pun sebagai alasan menunda pekerjaan yang diminta.

#### R-29 Palet Warna
- **WAJIB**: batasi palet aktif maksimal 2 sampai 3 warna inti + 1 warna aksen.
- **DILARANG**: memakai 5 warna atau lebih di satu halaman tanpa sistem yang jelas.
- Warna netral tidak dihitung.

#### R-30 Jangan Mengklon Produk Populer
- **DILARANG**: membangun visual yang secara keseluruhan meniru produk lain tanpa diminta
  (Linear, Vercel, Stripe, Notion, Apple).
- Referensi visual boleh jadi inspirasi, bukan template untuk disalin.

#### R-31 Setiap Keputusan Wajib Punya Alasan (Tulis)
Sebelum desain selesai, tulis **alasan satu baris** untuk setiap keputusan besar: kenapa warna ini,
kenapa layout ini, kenapa tipografi ini, kenapa spacing ini, kenapa memakai kartu, kenapa ikon atau ilustrasi ini.
Kalau alasannya tidak bisa ditulis dalam satu baris, keputusan itu tidak valid dan harus ditinjau ulang.
Ini aturan kunci dokumen ini.

---

## Bagian 3: Liveliness Toolkit

Filter bisa membuang slop, tapi tidak bisa menambah energi. Liveliness harus ditambahkan sengaja.

### Tiga Dial (wajib)

| Dial | 1 (Kalem) | 2 (Seimbang) | 3 (Berani) | Menjawab apa |
|---|---|---|---|---|
| ENERGY | Linear, GOV.UK | Stripe, Vercel | Awwwards, portfolio agensi | Seberapa keras desain ini menyapa |
| RHYTHM | Grid seragam, terduga | Konsisten dengan beberapa jeda | Asimetris, komposisi campur | Seberapa berbeda antar seksi |
| MOTION | Hover saja | Scroll reveal, transisi | Parallax, pin, koreografi | Seberapa banyak gerak, dan kenapa |

Nama nama itu referensi rasa untuk menilai level, bukan untuk ditiru.

### Levers (cara dial jadi keputusan visual)

- **Satu focal point per layar**: tepat satu elemen yang jelas paling penting, sisanya mengalah.
- **Kontras hierarkis**: ukuran, berat, dan warna dibedakan dengan sengaja.
- **Whitespace sebagai struktur**: ruang kosong memisahkan dan mengatur ritme, bukan sisa.
- **Satu aksen sengaja**: satu warna atau gestur dipakai hemat di momen kunci. Nol aksen itu steril, aksen di mana mana itu slop.
- **Motif identitas**: satu pola, gestur, atau suara tipografis yang spesifik dan diulang.

### Design Read (cara dial ditetapkan)

Sebelum menghasilkan apa pun, nyatakan satu baris:

> Saya baca ini sebagai `<jenis halaman>` untuk `<audiens>`, gaya `<bahasa visual>`, dial `<ENERGY/RHYTHM/MOTION>`.

1. **Ada arah** (`DESIGN.md` atau brief): turunkan dial dari sana lalu lanjut.
2. **Arah ambigu**: tanya tepat SATU pertanyaan penentu, bukan borongan pertanyaan.
3. **Tidak ada arah dan tidak bisa bertanya**: labeli *"draft without direction"*, set dial ENERGY 1 / RHYTHM 1 / MOTION 1.

## Pola Fungsional

"Berfungsi" berarti salah satu dari ini: anchor ke seksi nyata, scroll ke konten relevan, membuka modal
yang bisa ditutup dengan Escape, toggle state, aksi eksternal nyata, atau submit form dengan umpan balik terlihat.
Kalau tidak ada satu pun yang berlaku untuk sebuah elemen, elemen itu tidak perlu ada.

---

## Delivery Gate (Wajib)

Jalankan gate ini SEBELUM menyerahkan. Keluarkan statusnya bersama hasil sebagai laporan **PASS/FAIL**:
satu baris per item, dan setiap `PASS` didukung bukti konkret. Kalau ada item **FAIL**, jangan diserahkan:
perbaiki dulu, lalu ulangi.

### Blok 1: Hard Gate (mutlak)
Semua jawaban harus **tidak**:

- [ ] Ada em dash (`—`) di teks mana pun? *(R-02)*
- [ ] Ada overflow horizontal, teks keluar kontainer, atau layout rusak di mobile? *(R-03)*
- [ ] Ada statistik tanpa sumber nyata? *(R-17)*
- [ ] Ada testimoni fiktif? *(R-18)*
- [ ] Ada aset visual (logo, avatar, statistik, testimoni, struktur navigasi) yang dibuat tanpa instruksi eksplisit atau tanpa placeholder jujur? *(R-23)*
- [ ] Ada link navigasi ke seksi atau halaman yang tidak ada? *(R-24)*
- [ ] Ada teks dengan kontras di bawah WCAG AA? *(R-25)*
- [ ] Ada tombol, dropdown, atau form yang tidak melakukan apa-apa tanpa `// TODO` dan label terlihat? *(R-26)*
- [ ] UI tidak punya empty state, loading state, atau error state? *(R-27)*
- [ ] FAQ berisi pertanyaan generik yang tidak relevan? *(R-28)*
- [ ] UI tidak bisa dinavigasi keyboard atau tidak ada focus state terlihat? *(R-32)*
- [ ] Ada fitur yang ditambahkan dengan patching source atau CSS lewat skrip? *(R-33)*
- [ ] Kalau ada toggle tema, apakah salah satu mode rusak? *(R-34)*
- [ ] Apakah aplikasi belum dijalankan dan elemen interaktifnya belum dicoba sebelum diserahkan? *(R-35)*
- [ ] Ada klaim keamanan, kepatuhan, performa, atau pelanggan yang dikarang? *(R-36)*
- [ ] Desain dibangun tanpa arah gaya, atau tanpa arah tapi tidak dilabeli "draft without direction" dengan dial 1/1/1? *(R-37)*
- [ ] Ada konten bergaya realistis yang dikarang tanpa sumber nyata? *(R-38)*

### Blok 2: Purpose-Gate
FAIL kalau teknik muncul sebagai default tanpa tujuan, atau alasannya tidak ditulis:

- [ ] Gradien atau glow muncul sebagai default tanpa tujuan hierarki atau merek? *(R-01)*
- [ ] Ada ikon generik atau ikon tidak relevan tanpa relevansi tertulis? *(R-04)*
- [ ] Ada monospace besar, uppercase tracking lebar, atau typeface tanpa alasan karakter tertulis? *(R-06)*
- [ ] Ada grid latar, blueprint, atau kertas milimeter tanpa tujuan identitas tertulis? *(R-07)*
- [ ] Panah ditempel di hampir semua tombol sebagai hiasan tanpa tujuan tertulis? *(R-08)*
- [ ] Ada badge kapsul tanpa fungsi nyata, atau kombinasi kapsul, border tipis, glow, uppercase sekaligus? *(R-09)*
- [ ] Glassmorphism dipakai di lebih dari 1 sampai 2 elemen sekaligus? *(R-10)*
- [ ] Bayangan besar dipakai di semua komponen tanpa alasan elevasi tertulis? *(R-12)*
- [ ] Glow dipakai di kartu, tombol, badge, ikon, latar, dan border sekaligus? *(R-13)*
- [ ] Semua kartu fitur identik tanpa alasan hierarki tertulis? *(R-14)*
- [ ] Semua elemen memakai animasi template sekaligus tanpa tujuan UX tertulis, atau gerak bertentangan dengan dial MOTION? *(R-19)*
- [ ] Ada ilustrasi generik tanpa kaitan produk tertulis? *(R-22)*

### Blok 3: Liveliness
Semua jawaban harus **ya**:

- [ ] Dial sudah diset dan eksplisit (ENERGY / RHYTHM / MOTION)?
- [ ] Hasilnya konsisten dengan dial yang diklaim?
- [ ] Ada minimal satu focal point jelas per layar?
- [ ] Whitespace bersifat struktural, bukan sisa?
- [ ] Ada satu aksen sengaja, tidak nol dan tidak di mana mana?
- [ ] Ada motif identitas, satu pola atau gestur spesifik yang diulang?
- [ ] Design Read dinyatakan sebelum mulai menghasilkan?

### Blok 4: Kriya dan Quality Locks
Semua jawaban harus **tidak**:

- [ ] C-1: Ada keputusan visual atau copy yang alasannya cuma "default AI"?
- [ ] C-2: Ada elemen interaktif yang tidak melakukan apa-apa tanpa label jelas?
- [ ] C-3: Ada seksi yang ada hanya untuk mengisi template?
- [ ] C-4: UI rusak di salah satu state, tema, breakpoint, atau tanpa mouse?
- [ ] C-5: Ada testimoni, statistik, atau klaim yang dikarang?
- [ ] Layout mengikuti template AI, atau ritme seksi bertentangan dengan dial RHYTHM? *(R-05)*
- [ ] Semua elemen dibuat berbentuk pil tanpa variasi radius? *(R-11)*
- [ ] CTA masih generik? *(R-15)*
- [ ] Ada buzzword marketing AI? *(R-16)*
- [ ] Desain masih terasa generik kalau logo dan nama produk diganti? *(R-20)*
- [ ] Dark mode dipaksa jadi default tanpa alasan, atau toggle yang diminta ditunda dengan alasan? *(R-21)*
- [ ] Palet melebihi 2 sampai 3 warna inti + 1 aksen tanpa sistem jelas? *(R-29)*
- [ ] Desain keseluruhan terlihat seperti klon produk populer? *(R-30)*
- [ ] Ada keputusan visual besar yang alasannya tidak bisa ditulis satu baris? *(R-31)*

Kalau satu saja jawaban salah, jangan diserahkan. Perbaiki, ulangi gate, baru kirim.
