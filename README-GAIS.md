# GAIS: General Affair Information System

Aplikasi internal untuk tim General Affair. Dibangun bertahap, satu tahap satu paket yang bisa dipakai.

Stack: Laravel 13, PostgreSQL, Livewire 4, Filament 5, Tailwind 4.

## Peta berkas

| Berkas | Isi |
|---|---|
| `SETUP.md` | Cara memasang. Mulai dari sini |
| `setup.ps1` | Skrip pemasangan untuk PowerShell |
| `ARCHITECTURE.md` | Bentuk sistem, skema tabel, model otorisasi, titik integrasi aset dan finance |
| `ROADMAP.md` | Isi tiap tahap dan keputusan yang dibutuhkan sebelum tahap itu dimulai |
| `DESIGN.md` | Arah gaya: palet, tipografi, dial, aturan copy |
| `ANTISLOP.md` | Filter kualitas yang wajib dilewati sebelum sesuatu diserahkan |
| `DELIVERY-GATE.md` | Laporan gate Tahap 1 dan daftar verifikasinya |
| `DELIVERY-GATE-2.md` | Laporan gate Tahap 2 kiriman A dan daftar verifikasinya |
| `DELIVERY-GATE-3.md` | Laporan gate Tahap 2 kiriman B, stock opname |
| `CLAUDE.md` | Aturan kerja untuk agen di proyek ini |
| `env-gais.txt` | Contoh isi berkas `.env`, disalin jadi `.env` oleh skrip pemasangan |
| `overlay/` | Berkas GAIS yang disalin ke atas kerangka Laravel |

## Status

Tahap 1 (fondasi dan hak akses) sudah berjalan di mesin pengembangan.
Tahap 2 kiriman A (aset, barcode, impor) sudah dipakai di mesin pengembangan.
Kiriman B (stock opname) sudah ditulis, belum diverifikasi jalan.
Baca `DELIVERY-GATE-3.md` bagian 4 untuk daftar yang perlu dicoba.

## Isi Tahap 1

Masuk dan keluar, dasbor ringkasan, registri modul, role dengan matriks izin per modul per aksi,
override izin per pengguna, pengguna sistem, departemen, lokasi Head Office, karyawan,
pengaturan sistem, dan jejak audit untuk setiap perubahan data.

## Isi Tahap 2 kiriman B

Sesi stock opname dengan cakupan lokasi, departemen, dan kategori yang bisa digabung; daftar target
disusun otomatis; pencatatan temuan per baris lewat pemindaian barcode; penyesuaian data aset yang
dipisah sebagai tindakan tersendiri dan butuh izin khusus; serta unduhan hasil CSV.

## Isi Tahap 2 kiriman A

Kategori aset dengan awalan kode dan bawaan penyusutan, daftar aset tetap dan bergerak,
kode aset otomatis berbentuk GA-KOM-2026-0001, barcode Code 128 dan halaman cetak label untuk
lembar stiker A4, serta impor CSV yang melaporkan baris bermasalah tanpa menggagalkan seluruh berkas.
