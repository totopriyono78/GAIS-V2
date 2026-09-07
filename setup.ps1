# GAIS Tahap 1: pemasangan di Windows.
# Skrip ini hanya menjalankan perintah composer, artisan, dan npm, lalu menyalin
# berkas dari folder overlay. Skrip ini tidak pernah menyunting isi berkas source.

$ErrorActionPreference = 'Stop'
Set-Location $PSScriptRoot

function Langkah($nomor, $teks) {
    Write-Host ""
    Write-Host "[$nomor] $teks" -ForegroundColor Cyan
}

Langkah 1 "Memeriksa perkakas yang dibutuhkan"
foreach ($cmd in @('php', 'composer', 'node', 'npm')) {
    if (-not (Get-Command $cmd -ErrorAction SilentlyContinue)) {
        throw "Perintah '$cmd' tidak ditemukan di PATH. Pasang dulu, lalu jalankan skrip ini lagi."
    }
}
php -r "exit(version_compare(PHP_VERSION, '8.3.0', '>=') ? 0 : 1);"
if ($LASTEXITCODE -ne 0) { throw "Butuh PHP 8.3 atau lebih baru." }
php -r "exit(extension_loaded('pdo_pgsql') ? 0 : 1);"
if ($LASTEXITCODE -ne 0) { throw "Ekstensi PHP pdo_pgsql belum aktif. Aktifkan di php.ini, lalu ulangi." }
Write-Host "Perkakas lengkap." -ForegroundColor Green

Langkah 2 "Menyiapkan kerangka Laravel"
if (Test-Path 'artisan') {
    Write-Host "Kerangka Laravel sudah ada, langkah ini dilewati."
} else {
    composer create-project "laravel/laravel:^13.0" .laravel-tmp --no-interaction
    if ($LASTEXITCODE -ne 0) { throw "composer create-project gagal." }
    Get-ChildItem -Path '.laravel-tmp' -Force | ForEach-Object {
        Move-Item -LiteralPath $_.FullName -Destination (Join-Path $PWD.Path $_.Name) -Force
    }
    Remove-Item '.laravel-tmp' -Recurse -Force
}

Langkah 3 "Memasang Filament 5"
if (-not (Test-Path 'vendor/filament/filament')) {
    composer require "filament/filament:~5.0" --no-interaction
    if ($LASTEXITCODE -ne 0) { throw "Pemasangan Filament gagal." }
}
php artisan filament:install --panels --no-interaction

Langkah 4 "Menyalin berkas GAIS ke dalam aplikasi"
Copy-Item -Path (Join-Path $PSScriptRoot 'overlay\*') -Destination $PSScriptRoot -Recurse -Force
Write-Host "Berkas GAIS disalin." -ForegroundColor Green

Langkah 5 "Menyiapkan berkas .env"
$perluEnv = $true
if (Test-Path '.env') {
    $perluEnv = -not (Select-String -Path '.env' -Pattern 'GAIS_ADMIN_EMAIL' -Quiet)
}
if ($perluEnv) {
    Copy-Item 'env-gais.txt' '.env' -Force
    php artisan key:generate
}
Write-Host ""
Write-Host "Sekarang buka berkas .env dan pastikan tiga hal:" -ForegroundColor Yellow
Write-Host "  1. Database bernama gais sudah dibuat di PostgreSQL Anda"
Write-Host "  2. DB_USERNAME dan DB_PASSWORD sudah benar"
Write-Host "  3. DB_PORT sesuai (bawaan 5432)"
Read-Host "Tekan Enter kalau .env sudah benar"

Langkah 6 "Membuat tabel dan mengisi data awal"
php artisan migrate --seed
if ($LASTEXITCODE -ne 0) { throw "Migrasi gagal. Periksa kembali pengaturan database di .env." }

Langkah 7 "Menerbitkan aset dan membangun tampilan"
php artisan filament:assets
php artisan storage:link
npm install
npm run build

Write-Host ""
Write-Host "Pemasangan selesai." -ForegroundColor Green
Write-Host "Jalankan aplikasi dengan: php artisan serve"
Write-Host "Lalu buka: http://localhost:8000/admin"
Write-Host "Masuk dengan email dari GAIS_ADMIN_EMAIL di .env dan kata sandi yang dicetak di Langkah 6."
Write-Host ""
Write-Host "Setelah masuk, kerjakan daftar verifikasi di DELIVERY-GATE.md." -ForegroundColor Yellow
