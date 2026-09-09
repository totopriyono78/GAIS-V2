#!/usr/bin/env sh

#
# Dijalankan Railway sebagai Pre-Deploy Command, sebelum versi baru mulai melayani.
#
# Isinya hanya pekerjaan basis data. Menyimpan cache konfigurasi, rute, atau tampilan
# tidak ada gunanya di sini, karena perintah pra-deploy berjalan di wadah yang berbeda
# dari wadah yang melayani permintaan, jadi berkas cache yang ditulis di sini tidak
# pernah sampai ke aplikasi yang jalan. Cache itu sudah disiapkan saat citra dibangun.
#
# Seluruh perintah di bawah aman diulang. Kalau deploy gagal di tengah, menjalankannya
# lagi tidak merusak apa pun dan tidak menggandakan satu baris pun.
#

set -e

php artisan migrate --force

#
# Urutan tiga langkah berikut penting, dan bukan urutan yang bisa ditebak.
#
# ModuleSeeder mendaftarkan modul. gais:sync-permissions menurunkan izin dari modul itu,
# lalu melekatkan seluruhnya ke peran administrator. Baru setelah izinnya ada,
# RoleSeeder bisa melekatkan sebagian izin ke Manajer GA dan Staf GA.
#
# DatabaseSeeder memanggil ModuleSeeder dan RoleSeeder berurutan tanpa sinkronisasi di
# antaranya, jadi pada basis data yang benar benar kosong RoleSeeder akan mencari izin
# yang belum lahir. Karena itu ModuleSeeder dipanggil lebih dulu di sini, izinnya
# disinkronkan, baru seluruh seeder dijalankan. Pengulangan ModuleSeeder tidak
# menimbulkan apa apa, seluruh seeder di proyek ini memakai updateOrCreate.
#
php artisan db:seed --class=ModuleSeeder --force
php artisan gais:sync-permissions
php artisan db:seed --force

#
# Akun demo hanya dibuat kalau daftarnya memang akan ditampilkan.
#
# Satu saklar untuk dua hal sekaligus, dan itu disengaja: tidak mungkin ada keadaan
# di mana akun demo berkata sandi seragam hidup di basis data sementara tidak ada
# satu pun yang menerangkan asalnya di layar. Mematikan saklarnya membuat daftarnya
# hilang dari halaman masuk, tetapi akunnya tetap ada, jadi hapus keempatnya lewat
# menu Users kalau sistem ini beralih menjadi pemakaian sungguhan.
#
# Nilainya dikecilkan hurufnya dulu, dan beberapa ejaan diterima.
#
# Versi pertama skrip ini membandingkan langsung dengan "true" huruf kecil. Nilai yang
# diketik di dasbor Railway berbunyi TRUE, tidak cocok, dan akun demonya diam diam tidak
# pernah dibuat. Tidak ada pesan apa pun waktu itu, jadi yang terlihat hanya halaman masuk
# tanpa daftar akun, tanpa keterangan kenapa. Perbandingan yang peka huruf besar kecil
# terhadap nilai yang diketik orang di layar lain memang perangkap, dan ini jebakannya.
DEMO=$(printf '%s' "${GAIS_DEMO_LOGIN:-}" | tr '[:upper:]' '[:lower:]')

case "$DEMO" in
    true | 1 | yes | on)
        php artisan db:seed --class=DemoUserSeeder --force
        ;;
    *)
        echo "Akun demo dilewati. GAIS_DEMO_LOGIN bernilai '${GAIS_DEMO_LOGIN:-kosong}', diperlukan true."
        ;;
esac

echo "Pra-deploy selesai."
