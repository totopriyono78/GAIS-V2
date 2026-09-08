<?php

namespace App\Filament\Widgets;

use App\Services\BiayaPemeliharaan;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

/**
 * Biaya pemeliharaan yang benar benar keluar, enam bulan terakhir.
 *
 * Hanya pekerjaan yang selesai yang dihitung, dan dikelompokkan menurut bulan selesainya,
 * bukan bulan pekerjaan itu dilaporkan. Uang keluar saat pekerjaan selesai, bukan saat
 * kerusakan dilaporkan, dan mencampur keduanya membuat angka bulanan tidak bisa
 * dicocokkan dengan catatan kas siapa pun.
 *
 * Angkanya diambil lewat BiayaPemeliharaan supaya kunjungan preventif yang ditutup tanpa
 * perintah kerja ikut terhitung, sama seperti di kartu ringkasan di atasnya.
 */
class PemeliharaanBiayaChart extends ChartWidget
{
    protected ?string $heading = 'Maintenance Cost, Last Six Months';

    protected ?string $description = 'Dihitung dari pekerjaan yang sudah selesai, menurut bulan selesainya.';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return Auth::user()?->hasPermission('work_orders.read') ?? false;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $mulai = now()->copy()->subMonths(5)->startOfMonth();

        $hitung = BiayaPemeliharaan::perBulan($mulai, 6);

        $label = [];
        $nilai = [];

        // Enam bulan selalu digambar semua, termasuk bulan yang nol. Bulan yang hilang
        // dari sumbu membuat orang mengira datanya belum masuk, padahal memang tidak ada
        // pekerjaan yang selesai bulan itu.
        for ($i = 0; $i < 6; $i++) {
            $bulan = $mulai->copy()->addMonths($i);
            $kunci = $bulan->format('Y-m');

            $label[] = $bulan->translatedFormat('M Y');
            $nilai[] = round((float) ($hitung[$kunci] ?? 0), 2);
        }

        return [
            'datasets' => [[
                'label' => 'Biaya pemeliharaan',
                'data' => $nilai,
                'backgroundColor' => '#38707F',
                'borderColor' => '#17505E',
            ]],
            'labels' => $label,
        ];
    }
}
