<?php

namespace Database\Seeders;

use App\Models\AssetDisposal;
use App\Models\AssetTransfer;
use App\Models\NumberSequence;
use App\Models\ServiceRequest;
use App\Models\VehicleBooking;
use App\Models\VendorBill;
use App\Models\StockOpname;
use App\Models\SupplyItem;
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
    }
}
