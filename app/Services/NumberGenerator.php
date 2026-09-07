<?php

namespace App\Services;

use App\Models\NumberSequence;
use App\Models\NumberSequencePeriod;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class NumberGenerator
{
    /**
     * Mengambil nomor berikutnya untuk satu kode urutan.
     *
     * Penghitungnya disimpan per periode, jadi memasukkan aset tahun 2019 di
     * tengah tengah aset tahun ini tidak mengacaukan urutan keduanya. Baris
     * penghitung dikunci selama transaksi supaya dua orang yang menyimpan
     * bersamaan tidak mendapat nomor yang sama.
     */
    public static function next(string $code, ?string $period = null): string
    {
        return DB::transaction(function () use ($code, $period) {
            $sequence = NumberSequence::query()->where('code', $code)->first();

            if ($sequence === null) {
                throw new RuntimeException("Urutan nomor dengan kode {$code} belum terdaftar.");
            }

            $period = $sequence->periodFor($period);

            NumberSequencePeriod::query()->insertOrIgnore([
                'number_sequence_id' => $sequence->id,
                'period' => $period,
                'next_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $counter = NumberSequencePeriod::query()
                ->where('number_sequence_id', $sequence->id)
                ->where('period', $period)
                ->lockForUpdate()
                ->first();

            $number = $counter->next_number;

            $counter->next_number = $number + 1;
            $counter->save();

            return $sequence->format($period, $number);
        });
    }
}
