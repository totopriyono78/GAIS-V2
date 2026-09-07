<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\AssetTransfer;
use App\Models\StockOpnameLine;
use Illuminate\Support\Carbon;

/**
 * Menyusun kartu riwayat satu aset.
 *
 * Yang dijawab kartu ini: aset ini dulu di mana, siapa yang pernah memegangnya,
 * dan kenapa keadaannya berubah. Jawabannya tersebar di tiga tempat, yaitu dokumen
 * serah terima, baris pemeriksaan stock opname, dan dokumen pelepasan. Kelas ini
 * menggabungkan ketiganya menjadi satu daftar berurutan, supaya orang tidak perlu
 * membuka tiga layar lalu mencocokkan tanggal sendiri.
 *
 * Jejak audit sengaja tidak dipakai sebagai sumber. Jejak audit menyimpan nama kolom
 * dan nilai mentah, berguna untuk memeriksa siapa mengubah apa, tetapi tidak bisa
 * menjelaskan peristiwanya. Yang dibaca di sini adalah dokumen yang memang dibuat
 * untuk menjelaskan peristiwa itu.
 */
class RiwayatAset
{
    /**
     * Seluruh peristiwa yang diketahui tentang satu aset, terbaru di atas.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function untuk(Asset $asset): array
    {
        /*
         * Lokasi awal aset tidak boleh dibaca dari kolom lokasi sekarang, karena
         * kolom itu sudah ikut berubah setiap kali aset dipindah. Yang menyimpan
         * lokasi awal adalah kolom asal pada dokumen serah terima paling tua.
         */
        $serahTerimaPertama = AssetTransfer::query()
            ->where('asset_id', $asset->id)
            ->with('fromLocation')
            ->orderBy('transfer_date')
            ->orderBy('id')
            ->first();

        $baris = array_merge(
            self::serahTerima($asset),
            self::opname($asset),
            self::pelepasan($asset),
            [self::pencatatan($asset, $serahTerimaPertama?->fromLocation?->code)],
        );

        // Tanggal yang sama diurutkan lagi menurut urutan pencatatannya, supaya dua
        // peristiwa di hari yang sama tetap punya urutan yang tetap dan masuk akal.
        usort($baris, function (array $a, array $b): int {
            $selisih = $b['tanggal']->getTimestamp() <=> $a['tanggal']->getTimestamp();

            return $selisih !== 0 ? $selisih : ($b['urut'] <=> $a['urut']);
        });

        return $baris;
    }

    /** @return array<int, array<string, mixed>> */
    protected static function serahTerima(Asset $asset): array
    {
        return AssetTransfer::query()
            ->where('asset_id', $asset->id)
            ->with(['fromLocation', 'toLocation', 'fromCustodian', 'toCustodian', 'fromDepartment', 'toDepartment', 'handedOverBy', 'receivedBy'])
            ->orderBy('transfer_date')
            ->get()
            ->map(fn (AssetTransfer $transfer): array => [
                'jenis' => 'Serah terima',
                'warna' => 'petrol',
                'tanggal' => $transfer->transfer_date,
                'urut' => $transfer->id,
                'nomor' => $transfer->code,
                'judul' => $transfer->reasonLabel(),
                'rincian' => $transfer->changes(),
                'keterangan' => trim(implode(' ', array_filter([
                    $transfer->handedOverBy ? 'Diserahkan oleh '.$transfer->handedOverBy->full_name.'.' : null,
                    $transfer->receivedBy ? 'Diterima oleh '.$transfer->receivedBy->full_name.'.' : null,
                    $transfer->reference ? 'Berita acara '.$transfer->reference.'.' : null,
                    $transfer->notes,
                ]))),
            ])
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    protected static function opname(Asset $asset): array
    {
        return StockOpnameLine::query()
            ->where('asset_id', $asset->id)
            ->where('checked', true)
            ->with(['opname', 'expectedLocation', 'foundLocation'])
            ->get()
            ->filter(fn (StockOpnameLine $line): bool => $line->opname !== null)
            ->map(function (StockOpnameLine $line): array {
                $rincian = [];

                if ($line->found === false) {
                    $rincian[] = ['label' => 'Hasil', 'dari' => 'Tercatat ada', 'ke' => 'Tidak ditemukan'];
                } else {
                    if ($line->found_location_id !== null && $line->found_location_id !== $line->expected_location_id) {
                        $rincian[] = [
                            'label' => 'Lokasi',
                            'dari' => $line->expectedLocation?->code ?? 'Belum diisi',
                            'ke' => $line->foundLocation?->code ?? 'Belum diisi',
                        ];
                    }

                    if ($line->found_condition !== null && $line->found_condition !== $line->expected_condition) {
                        $rincian[] = [
                            'label' => 'Kondisi',
                            'dari' => Asset::CONDITIONS[$line->expected_condition] ?? (string) $line->expected_condition,
                            'ke' => Asset::CONDITIONS[$line->found_condition] ?? (string) $line->found_condition,
                        ];
                    }
                }

                return [
                    'jenis' => 'Stock opname',
                    'warna' => $rincian === [] ? 'hijau' : 'terakota',
                    'tanggal' => $line->checked_at ?? $line->opname->created_at,
                    'urut' => $line->id,
                    'nomor' => $line->opname->code,
                    'judul' => $line->resultLabel(),
                    'rincian' => $rincian,
                    'keterangan' => trim(implode(' ', array_filter([
                        'Sesi '.$line->opname->name.'.',
                        $line->notes,
                    ]))),
                ];
            })
            ->values()
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    protected static function pelepasan(Asset $asset): array
    {
        $pelepasan = AssetDisposal::query()->where('asset_id', $asset->id)->with('approvedBy')->first();

        if ($pelepasan === null) {
            return [];
        }

        $rincian = [];

        if ($pelepasan->previous_status !== null) {
            $rincian[] = [
                'label' => 'Status',
                'dari' => Asset::STATUSES[$pelepasan->previous_status] ?? $pelepasan->previous_status,
                'ke' => 'Sudah dilepas',
            ];
        }

        return [[
            'jenis' => 'Pelepasan',
            'warna' => 'merah',
            'tanggal' => $pelepasan->disposal_date,
            'urut' => $pelepasan->id,
            'nomor' => $pelepasan->code,
            'judul' => $pelepasan->methodLabel(),
            'rincian' => $rincian,
            'keterangan' => trim(implode(' ', array_filter([
                $pelepasan->counterparty ? 'Pihak terkait '.$pelepasan->counterparty.'.' : null,
                $pelepasan->proceeds !== null
                    ? 'Hasil pelepasan Rp '.number_format((float) $pelepasan->proceeds, 0, ',', '.').'.'
                    : null,
                $pelepasan->approvedBy ? 'Disetujui oleh '.$pelepasan->approvedBy->full_name.'.' : null,
                $pelepasan->reference ? 'Berita acara '.$pelepasan->reference.'.' : null,
                $pelepasan->reason,
            ]))),
        ]];
    }

    /**
     * Peristiwa paling awal. Selalu ada, jadi kartu riwayat tidak pernah kosong,
     * bahkan untuk aset yang belum pernah dipindah maupun diperiksa.
     *
     * @return array<string, mixed>
     */
    protected static function pencatatan(Asset $asset, ?string $lokasiAwal = null): array
    {
        // Kalau aset belum pernah dipindah, lokasi sekarang memang lokasi awalnya.
        $lokasiAwal ??= $asset->location?->code;

        return [
            'jenis' => 'Pencatatan',
            'warna' => 'abu',
            'tanggal' => $asset->acquisition_date ?? Carbon::parse($asset->created_at),
            'urut' => 0,
            'nomor' => $asset->code,
            'judul' => $asset->acquisition_date
                ? 'Diperoleh lewat '.(Asset::SOURCES[$asset->acquisition_source] ?? $asset->acquisition_source)
                : 'Dicatat ke dalam sistem',
            'rincian' => array_filter([
                $lokasiAwal ? ['label' => 'Lokasi awal tercatat', 'dari' => null, 'ke' => $lokasiAwal] : null,
            ]),
            'keterangan' => $asset->acquisition_cost > 0
                ? 'Nilai perolehan Rp '.number_format((float) $asset->acquisition_cost, 0, ',', '.').'.'
                : 'Nilai perolehan tidak tercatat.',
        ];
    }
}
