<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AssetCategory;
use Illuminate\Console\Command;

/**
 * Mengisi nomor akun COA contoh supaya kategori aset langsung bisa dipakai saat demo.
 *
 * Nomor di bawah BUKAN bagan akun perusahaan mana pun. Ini pola penomoran yang lazim
 * dipakai di Indonesia: 12xx untuk aset tetap, 13xx untuk akumulasi penyusutan, dan
 * 62xx untuk beban penyusutan. Setiap kategori yang diisi perintah ini diberi penanda
 * [COA CONTOH] di keterangannya, supaya terlihat jelas di layar bahwa nomornya belum final.
 *
 * Nomor akun ikut tercetak di stiker aset, jadi sebelum aplikasi dipakai sungguhan,
 * jalankan perintah ini dengan opsi --hapus lalu isi nomor akun yang sebenarnya.
 */
class IsiCoaDemo extends Command
{
    protected $signature = 'gais:coa-demo
        {--hapus : Kosongkan kembali nomor akun contoh dan hapus penandanya}';

    protected $description = 'Mengisi nomor akun COA contoh untuk keperluan demo';

    public const PENANDA = '[COA CONTOH]';

    /**
     * kode kategori => [akun aset, akun akumulasi penyusutan, akun beban penyusutan]
     */
    public const AKUN = [
        'TNH' => ['1201', null, null],
        'BGN' => ['1202', '1302', '6202'],
        'BGT' => ['1203', '1303', '6203'],
        'PRS' => ['1204', '1304', '6204'],
        'KR4' => ['1205', '1305', '6205'],
        'KR2' => ['1206', '1306', '6206'],
        'PKY' => ['1207', '1307', '6207'],
        'PLG' => ['1208', '1308', '6208'],
        'KOM' => ['1209', '1309', '6209'],
        'MSK' => ['1210', '1310', '6210'],
        'ALK' => ['1211', '1311', '6211'],
        'PGU' => ['1212', '1312', '6212'],
        'ADP' => ['1213', null, null],
    ];

    public function handle(): int
    {
        return $this->option('hapus') ? $this->hapus() : $this->isi();
    }

    protected function isi(): int
    {
        $terisi = 0;
        $dilewati = [];

        foreach (self::AKUN as $kode => [$aset, $akumulasi, $beban]) {
            $category = AssetCategory::query()->where('code', $kode)->first();

            if ($category === null) {
                continue;
            }

            // Nomor akun yang sudah diisi orang tidak ditimpa, karena bisa jadi
            // itu nomor sungguhan dari tim finance.
            if (filled($category->account_asset) && ! $this->bertandaContoh($category)) {
                $dilewati[] = $category->code.' sudah punya akun '.$category->account_asset;

                continue;
            }

            $category->account_asset = $aset;
            $category->account_accumulated = $akumulasi;
            $category->account_expense = $beban;
            $category->description = $this->tambahPenanda($category->description);
            $category->save();

            $terisi++;
        }

        if ($terisi === 0 && $dilewati === []) {
            $this->error('Tidak ada kategori standar yang ditemukan.');
            $this->line('Jalankan php artisan db:seed --class=AssetCategorySeeder dulu.');

            return self::FAILURE;
        }

        $this->info("Nomor akun contoh diisi untuk {$terisi} kategori.");

        foreach ($dilewati as $pesan) {
            $this->line('Dilewati: '.$pesan);
        }

        $this->newLine();
        $this->warn('Nomor akun di atas hanya contoh untuk demo, bukan bagan akun perusahaan Anda.');
        $this->warn('Nomor ini ikut membentuk kode aset dan tercetak di stiker.');
        $this->warn('Sebelum dipakai sungguhan: php artisan gais:coa-demo --hapus, lalu isi nomor yang benar.');

        return self::SUCCESS;
    }

    protected function hapus(): int
    {
        $kategori = AssetCategory::query()
            ->whereIn('code', array_keys(self::AKUN))
            ->get()
            ->filter(fn (AssetCategory $category) => $this->bertandaContoh($category));

        if ($kategori->isEmpty()) {
            $this->info('Tidak ada kategori bernomor akun contoh. Tidak ada yang diubah.');

            return self::SUCCESS;
        }

        $terpakai = Asset::query()
            ->whereIn('asset_category_id', $kategori->pluck('id'))
            ->count();

        if ($terpakai > 0) {
            $this->warn("Ada {$terpakai} aset yang kodenya sudah memakai nomor akun contoh ini.");
            $this->warn('Mengosongkan nomor akun tidak mengubah kode aset yang terlanjur dibuat.');
            $this->warn('Aset demo itu perlu dihapus terpisah kalau tidak ingin terbawa ke data sungguhan.');

            if (! $this->confirm('Tetap kosongkan nomor akun contohnya?', false)) {
                $this->line('Dibatalkan, tidak ada yang diubah.');

                return self::SUCCESS;
            }
        }

        foreach ($kategori as $category) {
            $category->account_asset = null;
            $category->account_accumulated = null;
            $category->account_expense = null;
            $category->description = $this->hapusPenanda($category->description);
            $category->save();
        }

        $this->info("Nomor akun contoh dikosongkan untuk {$kategori->count()} kategori.");
        $this->line('Isi nomor akun yang sebenarnya di menu Aset, Kategori aset.');

        return self::SUCCESS;
    }

    protected function bertandaContoh(AssetCategory $category): bool
    {
        return str_contains((string) $category->description, self::PENANDA);
    }

    protected function tambahPenanda(?string $description): string
    {
        $description = trim((string) $description);

        if (str_contains($description, self::PENANDA)) {
            return $description;
        }

        return trim($description.' '.self::PENANDA);
    }

    protected function hapusPenanda(?string $description): ?string
    {
        $description = trim(str_replace(self::PENANDA, '', (string) $description));

        return $description === '' ? null : $description;
    }
}
