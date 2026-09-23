<?php

namespace App\Console\Commands;

use App\Support\Berkas;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Memindahkan berkas unggahan lama dari disk public ke disk dokumen.
 *
 * Dijalankan sekali setelah perubahan 23 September 2026. Sebelum itu seluruh
 * lampiran disimpan di disk public dan bisa diunduh siapa pun yang memegang
 * tautannya, tanpa pemeriksaan izin. Jalur berkas di basis data tidak berubah,
 * hanya disknya, jadi tidak ada baris yang perlu disentuh.
 *
 * Aman diulang. Berkas yang sudah ada di tujuan dilewati, dan berkas asal baru
 * dihapus setelah salinannya terbukti ada di tujuan dengan ukuran yang sama.
 */
class PindahBerkas extends Command
{
    protected $signature = 'gais:pindah-berkas
        {--kering : Hanya menghitung, tidak memindahkan apa pun}';

    protected $description = 'Memindahkan lampiran lama dari disk public ke disk dokumen';

    /**
     * Folder yang dipakai FileUpload di seluruh layar. Ditulis di sini, bukan
     * dipindai dari kode, supaya perintah ini tidak ikut memindahkan folder yang
     * memang bukan lampiran, misalnya berkas impor CSV yang umurnya sekali pakai.
     */
    private const FOLDER = [
        'bukti-perjalanan',
        'dokumen-aset',
        'dokumen-kendaraan',
        'foto-aset',
        'foto-kendaraan',
        'lampiran-perintah-kerja',
        'lampiran-permintaan',
        'pelepasan-aset',
        'pemeriksaan-kebersihan',
        'struk-penggantian',
        'surat',
        'tagihan-rekanan',
    ];

    public function handle(): int
    {
        $asal = Storage::disk('public');
        $tujuan = Berkas::disk();
        $kering = (bool) $this->option('kering');

        $pindah = 0;
        $lewat = 0;
        $gagal = 0;

        foreach (self::FOLDER as $folder) {
            if (! $asal->directoryExists($folder)) {
                continue;
            }

            foreach ($asal->allFiles($folder) as $jalur) {
                if ($tujuan->exists($jalur)) {
                    $lewat++;

                    continue;
                }

                if ($kering) {
                    $pindah++;

                    continue;
                }

                $isi = $asal->get($jalur);

                if ($isi === null) {
                    $gagal++;
                    $this->components->error("Tidak bisa dibaca: {$jalur}");

                    continue;
                }

                $tujuan->put($jalur, $isi);

                // Berkas asal baru dilepas setelah salinannya terbukti ada dan
                // ukurannya sama. Pemeriksaan ini yang membuat perintah aman
                // diulang kalau prosesnya terputus di tengah.
                if ($tujuan->exists($jalur) && $tujuan->size($jalur) === $asal->size($jalur)) {
                    $asal->delete($jalur);
                    $pindah++;
                } else {
                    $gagal++;
                    $this->components->error("Salinan tidak cocok, asal tidak dihapus: {$jalur}");
                }
            }
        }

        $this->newLine();

        if ($kering) {
            $this->components->info("Uji kering. {$pindah} berkas akan dipindahkan, {$lewat} sudah ada di tujuan.");

            return self::SUCCESS;
        }

        $this->components->info("Selesai. {$pindah} berkas dipindahkan, {$lewat} dilewati, {$gagal} gagal.");

        if ($pindah > 0) {
            $this->components->warn('Folder public yang sekarang kosong boleh dihapus manual. Symlink public/storage tidak lagi dipakai lampiran.');
        }

        return $gagal > 0 ? self::FAILURE : self::SUCCESS;
    }
}
