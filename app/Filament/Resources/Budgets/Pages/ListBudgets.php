<?php

namespace App\Filament\Resources\Budgets\Pages;

use App\Filament\Resources\Budgets\BudgetResource;
use App\Models\Budget;
use App\Models\ExpenseCategory;
use App\Services\RealisasiBiaya;
use App\Support\Rupiah;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgets extends ListRecords
{
    protected static string $resource = BudgetResource::class;

    /**
     * Subjudul menjawab dua hal yang selalu ditanyakan lebih dulu: berapa banyak pagu yang
     * sudah lewat, dan berapa biaya yang terjaring tetapi tidak bisa dibebankan ke
     * departemen mana pun. Yang kedua penting karena angka itulah yang membuat jumlah
     * seluruh realisasi tidak cocok dengan jumlah biaya sebenarnya, dan tanpa disebut,
     * selisihnya akan dikira kesalahan hitung.
     */
    public function getSubheading(): ?string
    {
        $tahun = (int) now()->format('Y');

        $anggaran = Budget::query()->tahun($tahun)->with('category')->get();

        if ($anggaran->isEmpty()) {
            return 'Belum ada pagu untuk tahun '.$tahun.'. Realisasi tetap dijumlahkan dari catatan yang ada, tetapi tidak punya pembanding sampai pagunya ditetapkan.';
        }

        $lewat = $anggaran->filter(fn (Budget $b): bool => $b->keadaan() === 'lewat')->count();
        $mendekati = $anggaran->filter(fn (Budget $b): bool => $b->keadaan() === 'mendekati')->count();

        $realisasi = app(RealisasiBiaya::class);

        $kategori = ExpenseCategory::query()->where('is_active', true)->get();

        /*
         * Biaya yang tidak bisa dibebankan ke departemen mana pun dipisah menurut
         * sebabnya, karena sebabnya berbeda dan tindakannya juga berbeda. Yang dari
         * catatan operasional muncul karena asetnya belum diisi departemennya, dan itu
         * bisa diperbaiki di layar aset. Yang dari tagihan muncul karena barisnya memang
         * sengaja tidak menyebut departemen, misalnya listrik koridor, dan itu bukan
         * kesalahan yang perlu diperbaiki. Menyatukan keduanya di bawah satu alasan akan
         * mengirim orang membetulkan data aset yang sebenarnya sudah benar.
         */
        $belumTerbebankanAset = $kategori
            ->reject(fn (ExpenseCategory $k): bool => $k->dariTagihan())
            ->sum(fn (ExpenseCategory $k): float => $realisasi->belumTerbebankan($k, $tahun));

        $belumTerbebankanTagihan = $kategori
            ->filter(fn (ExpenseCategory $k): bool => $k->dariTagihan())
            ->sum(fn (ExpenseCategory $k): float => $realisasi->belumTerbebankan($k, $tahun));

        /*
         * Nilai faktur yang sudah masuk tetapi belum disetujui. Bukan realisasi, dan
         * sengaja tidak dijumlahkan ke dalamnya, tetapi disebut di sini karena tumpukan
         * faktur yang belum ditandatangani adalah cara termudah membuat seluruh pagu di
         * layar ini terlihat sehat pada hari uangnya justru sudah habis.
         *
         * Dijumlahkan menurut kategori, bukan menurut baris pagu yang ada. Menjumlahkannya
         * dari pagu akan menyembunyikan tagihan yang jatuh ke departemen yang belum diberi
         * pagu, dan itu justru departemen yang paling perlu diketahui sedang berbelanja.
         */
        $tertunda = $kategori
            ->sum(fn (ExpenseCategory $k): float => $realisasi->tertundaSeluruhnya($k, $tahun));

        $bagian = [];

        if ($lewat > 0) {
            $bagian[] = $lewat.' pagu sudah terlewati';
        }

        if ($mendekati > 0) {
            $bagian[] = $mendekati.' mendekati batas';
        }

        if ($tertunda > 0) {
            $bagian[] = Rupiah::penuh($tertunda).' dari tagihan rekanan dan struk karyawan sudah masuk tetapi belum disetujui, jadi belum terhitung sebagai realisasi';
        }

        if ($belumTerbebankanAset > 0) {
            $bagian[] = Rupiah::penuh($belumTerbebankanAset).' belum bisa dibebankan ke departemen mana pun, karena asetnya belum punya departemen';
        }

        if ($belumTerbebankanTagihan > 0) {
            $bagian[] = Rupiah::penuh($belumTerbebankanTagihan).' berupa biaya bersama pada tagihan dan pengajuan yang memang tidak menyebut departemen';
        }

        if ($bagian === []) {
            return 'Tahun '.$tahun.', seluruh pagu masih aman. Realisasi dijumlahkan sendiri dari catatan yang sudah ada.';
        }

        return 'Tahun '.$tahun.'. '.ucfirst(implode(', ', $bagian)).'.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Set Budget')
                ->modalHeading('Set Budget')
                ->modalSubmitActionLabel('Save Budget'),
        ];
    }
}
