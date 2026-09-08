<?php

namespace Database\Seeders;

use App\Models\AssetDisposal;
use App\Models\AssetTransfer;
use App\Models\BusinessTrip;
use App\Models\CleaningInspection;
use App\Models\IncidentReport;
use App\Models\Letter;
use App\Models\NumberSequence;
use App\Models\ParcelShipment;
use App\Models\ServiceRequest;
use App\Models\VehicleBooking;
use App\Models\Reimbursement;
use App\Models\VendorBill;
use App\Models\StockOpname;
use App\Models\SupplyItem;
use App\Models\SupplyOpname;
use App\Models\SupplyPurchase;
use App\Models\SupplyReceipt;
use App\Models\SupplyRequest;
use App\Models\SupplyTransaction;
use App\Models\WorkOrder;
use Illuminate\Database\Seeder;

/**
 * Penomoran dokumen. Satu baris per jenis dokumen, ditambah seiring modulnya dibangun.
 */
class NumberSequenceSeeder extends Seeder
{
    public function run(): void
    {
        NumberSequence::query()->updateOrCreate(
            ['code' => StockOpname::SEQUENCE_CODE],
            [
                'name' => 'Nomor sesi stock opname',
                'prefix' => 'SO',
                'separator' => '/',
                // Nomor urut kembali ke satu setiap bulan, mengikuti kebiasaan penomoran surat.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => SupplyItem::SEQUENCE_CODE],
            [
                'name' => 'Kode barang habis pakai',
                'prefix' => 'ATK',
                'separator' => '-',
                // Tanpa periode. Kode barang adalah penanda tetap, dan barang yang
                // didaftarkan tahun ini tidak berbeda jenis dari yang tahun lalu.
                // Kolomnya wajib isi, jadi "tanpa periode" ditulis sebagai teks kosong,
                // bukan null. NumberSequence::periodFor() memperlakukan keduanya sama.
                'period_format' => '',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => SupplyTransaction::SEQUENCE_CODE],
            [
                'name' => 'Nomor mutasi persediaan',
                'prefix' => 'MP',
                'separator' => '/',
                // Sama dengan nomor surat kantor, kembali ke satu setiap bulan.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => AssetTransfer::SEQUENCE_CODE],
            [
                'name' => 'Nomor serah terima aset',
                'prefix' => 'MA',
                'separator' => '/',
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => AssetDisposal::SEQUENCE_CODE],
            [
                'name' => 'Nomor pelepasan aset',
                'prefix' => 'PA',
                'separator' => '/',
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => WorkOrder::SEQUENCE_CODE],
            [
                'name' => 'Nomor perintah kerja',
                'prefix' => 'WO',
                'separator' => '/',
                // Kembali ke satu tiap bulan, sama seperti nomor surat lainnya.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => ServiceRequest::SEQUENCE_CODE],
            [
                'name' => 'Nomor permintaan perbaikan',
                'prefix' => 'PB',
                'separator' => '/',
                // Kembali ke satu tiap bulan. Tiket paling sering dicari dengan kalimat
                // "yang bulan lalu", jadi bulannya ikut terbaca di nomornya.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => VehicleBooking::SEQUENCE_CODE],
            [
                'name' => 'Nomor pemesanan kendaraan',
                'prefix' => 'PK',
                'separator' => '/',
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => VendorBill::SEQUENCE_CODE],
            [
                'name' => 'Nomor tagihan rekanan',
                'prefix' => 'TG',
                'separator' => '/',
                // Kembali ke satu tiap bulan. Tagihan hampir selalu dicari dengan kalimat
                // "yang masuk bulan lalu", jadi bulannya ikut terbaca di nomornya.
                // Nomor ini nomor internal GA, bukan nomor faktur rekanan, dan keduanya
                // disimpan terpisah supaya faktur ganda dari rekanan tetap ketahuan.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => Reimbursement::SEQUENCE_CODE],
            [
                'name' => 'Nomor penggantian biaya',
                'prefix' => 'PG',
                'separator' => '/',
                // Kembali ke satu tiap bulan, sama seperti nomor surat lainnya.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => SupplyRequest::SEQUENCE_CODE],
            [
                'name' => 'Nomor permintaan barang',
                /*
                 * Awalannya PM, bukan PB.
                 *
                 * Pada kiriman M awalan permintaan barang ditulis PB, padahal PB sudah dipakai
                 * permintaan perbaikan sejak kiriman G. Karena keduanya urutan yang terpisah,
                 * nomor PB/2026/09/0001 bisa lahir dua kali untuk dua dokumen yang sama sekali
                 * berbeda, dan orang yang menyebut nomor itu di telepon tidak akan pernah tahu
                 * yang mana yang dimaksud. Ketahuan saat kiriman O, dan diperbaiki di sini.
                 *
                 * Permintaan barang yang terlanjur bernomor PB tetap memakai nomor lamanya,
                 * karena nomor dokumen adalah penanda tetap yang tidak boleh berubah setelah
                 * disebut orang. Yang berubah hanya nomor berikutnya.
                 */
                'prefix' => 'PM',
                'separator' => '/',
                // Kembali ke satu tiap bulan. Permintaan ATK hampir selalu dicari dengan
                // kalimat "yang saya ajukan bulan lalu", jadi bulannya ikut di nomornya.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => SupplyPurchase::SEQUENCE_CODE],
            [
                'name' => 'Nomor pesanan pembelian barang',
                'prefix' => 'PP',
                'separator' => '/',
                // Nomor ini ikut tercetak di pesanan yang dikirim ke pemasok, jadi bulannya
                // sengaja ikut terbaca supaya pemasok bisa menyebutnya saat menanyakan
                // pesanan yang mana yang sedang ia kirim.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => SupplyReceipt::SEQUENCE_CODE],
            [
                'name' => 'Nomor penerimaan barang',
                'prefix' => 'PN',
                'separator' => '/',
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => BusinessTrip::SEQUENCE_CODE],
            [
                'name' => 'Nomor surat perjalanan dinas',
                'prefix' => 'SPD',
                'separator' => '/',
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => ParcelShipment::SEQUENCE_CODE],
            [
                'name' => 'Nomor pengiriman paket',
                'prefix' => 'KP',
                'separator' => '/',
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => Letter::SEQUENCE_MASUK],
            [
                'name' => 'Nomor agenda surat masuk',
                'prefix' => 'AM',
                'separator' => '/',
                // Kembali ke satu tiap bulan, mengikuti kebiasaan buku agenda kantor.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => Letter::SEQUENCE_KELUAR],
            [
                'name' => 'Nomor agenda surat keluar',
                /*
                 * Urutannya terpisah dari surat masuk, karena buku agenda masuk dan buku
                 * agenda keluar memang dua buku yang berbeda di hampir setiap kantor.
                 * Menyatukannya membuat nomor agenda melompat lompat tanpa sebab yang bisa
                 * diterangkan kepada orang yang sedang mencari surat.
                 *
                 * Nomor ini bukan nomor surat perusahaan. Nomor surat perusahaan diketik
                 * sendiri mengikuti formatnya, sesuai keputusan pemilik proyek pada
                 * 8 September 2026, dan disimpan di kolomnya sendiri.
                 */
                'prefix' => 'AK',
                'separator' => '/',
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => IncidentReport::SEQUENCE_CODE],
            [
                'name' => 'Nomor laporan insiden keamanan',
                'prefix' => 'LI',
                'separator' => '/',
                // Kembali ke satu tiap bulan. Laporan insiden hampir selalu dicari dengan
                // kalimat "yang kejadian bulan lalu", jadi bulannya ikut di nomornya.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => CleaningInspection::SEQUENCE_CODE],
            [
                'name' => 'Nomor pemeriksaan kebersihan',
                'prefix' => 'KB',
                'separator' => '/',
                // Kembali ke satu tiap bulan. Lembar pemeriksaan hampir selalu dicari dengan
                // kalimat "yang minggu lalu", jadi bulannya ikut terbaca di nomornya.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );

        NumberSequence::query()->updateOrCreate(
            ['code' => SupplyOpname::SEQUENCE_CODE],
            [
                'name' => 'Nomor opname barang habis pakai',
                'prefix' => 'OP',
                'separator' => '/',
                // Awalannya OP, dibedakan dari SO milik opname aset, supaya kedua jenis
                // lembar hitungan tidak pernah tertukar saat disebut lisan di gudang.
                'period_format' => 'Y/m',
                'padding' => 4,
            ],
        );
    }
}
