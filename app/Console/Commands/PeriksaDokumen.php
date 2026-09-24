<?php

namespace App\Console\Commands;

use App\Enums\VersionStatus;
use App\Exceptions\MasalahVersiDokumen;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\DocumentVersion;
use App\Models\User;
use App\Services\NumberGenerator;
use App\Services\PengelolaDokumen;
use App\Services\SkemaMetadata;
use App\Support\Berkas;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

/**
 * Membuktikan jaminan basis data modul dokumen benar benar berlaku.
 *
 * Tiga hal di modul ini tidak bisa dijaga dari sisi PHP dan karena itu ditulis
 * sebagai constraint. Masalahnya, constraint yang salah tulis tetap membuat
 * migrasi berhasil, dan cacatnya baru terlihat saat dua orang menyetujui versi
 * bersamaan di hari yang sibuk. Perintah ini menyodorkan data yang seharusnya
 * ditolak, lalu memeriksa apakah memang ditolak.
 *
 * Seluruhnya berjalan di dalam transaksi yang dibatalkan di akhir, jadi tidak
 * ada satu baris pun yang tertinggal di basis data.
 *
 * Pemeriksaan yang seharusnya gagal dijalankan di dalam transaksi bersarang.
 * PostgreSQL membatalkan seluruh transaksi begitu satu perintah gagal, jadi
 * tanpa savepoint, pemeriksaan pertama yang berhasil ditolak akan membuat
 * seluruh pemeriksaan berikutnya ikut gagal karena alasan yang salah.
 */
class PeriksaDokumen extends Command
{
    protected $signature = 'gais:periksa-dokumen';

    protected $description = 'Membuktikan constraint dan penomoran modul dokumen bekerja';

    private int $lolos = 0;

    private int $gagal = 0;

    public function handle(): int
    {
        $this->newLine();
        $this->line('  <fg=gray>Seluruh pemeriksaan berjalan dalam transaksi yang dibatalkan di akhir.</>');
        $this->newLine();

        $this->periksaTabel();
        $this->periksaEkstensi();
        $this->periksaPenyimpanan();
        $this->periksaSkemaMetadata();

        try {
            DB::transaction(function () {
                $this->periksaConstraint();
                $this->periksaPenggantianHariSama();
                $this->periksaPenomoran();

                // Sengaja dibatalkan. Pemeriksaan ini tidak boleh meninggalkan apa pun.
                throw new BatalkanUji;
            });
        } catch (BatalkanUji) {
            // Sesuai rencana.
        }

        $this->newLine();

        if ($this->gagal > 0) {
            $this->components->error("{$this->lolos} lolos, {$this->gagal} gagal.");

            return self::FAILURE;
        }

        $this->components->info("{$this->lolos} pemeriksaan, semuanya lolos.");

        return self::SUCCESS;
    }

    private function periksaTabel(): void
    {
        foreach ([
            'document_types', 'document_categories', 'documents', 'document_versions',
            'document_links', 'document_contents', 'document_saved_filters', 'document_access_log',
        ] as $tabel) {
            $this->nilai("Tabel {$tabel} ada", Schema::hasTable($tabel));
        }

        $this->nilai('Kolom clearance_level ada di users', Schema::hasColumn('users', 'clearance_level'));
    }

    private function periksaEkstensi(): void
    {
        foreach (['btree_gist', 'pg_trgm', 'unaccent'] as $ekstensi) {
            $ada = DB::table('pg_extension')->where('extname', $ekstensi)->exists();
            $this->nilai("Ekstensi {$ekstensi} terpasang", $ada);
        }
    }

    private function periksaPenyimpanan(): void
    {
        $disk = config('filesystems.disks.'.Berkas::DISK);

        $this->nilai('Disk dokumen terdaftar di config', is_array($disk));

        if (! is_array($disk)) {
            return;
        }

        // Tiga hal ini yang membuat berkasnya tidak bisa diambil tanpa izin.
        $this->nilai('Disk dokumen bersifat privat', ($disk['visibility'] ?? null) === 'private');
        $this->nilai('Disk dokumen dilayani rute bertanda tangan', ($disk['serve'] ?? false) === true);
        $this->nilai(
            'Disk dokumen tidak berbagi alamat dengan disk public',
            ($disk['url'] ?? null) !== (config('filesystems.disks.public.url') ?? null),
        );

        $this->nilai('Rute unduhan dokumen terdaftar', app('router')->has('gais.dokumen.unduh'));
    }

    /**
     * Penyeragaman bentuk nilai adalah bagian yang paling mudah dianggap sepele
     * dan paling sunyi kalau salah. Angka yang tersimpan sebagai teks terlihat
     * sama persis di layar, tetapi tidak akan pernah muncul di hasil penyaringan
     * metadata, karena penyaringnya membandingkan bentuk simpannya.
     */
    private function periksaSkemaMetadata(): void
    {
        $skema = new SkemaMetadata;

        $definisi = [
            'nomor' => ['type' => 'string', 'label' => 'Nomor', 'required' => true],
            'nilai' => ['type' => 'number', 'label' => 'Nilai', 'required' => true, 'min' => 1],
            'mulai' => ['type' => 'date', 'label' => 'Mulai'],
            'auto' => ['type' => 'boolean', 'label' => 'Otomatis'],
            'vendor' => ['type' => 'reference', 'label' => 'Vendor', 'reference' => 'vendors'],
            'jenis' => ['type' => 'select', 'label' => 'Jenis', 'options' => ['NIB', 'SIUP, TDP']],
        ];

        $aturan = $skema->aturan($definisi);

        $this->nilai('Field wajib menghasilkan aturan required', ($aturan['nomor'][0] ?? null) === 'required');
        $this->nilai('Field tidak wajib menghasilkan aturan nullable', ($aturan['mulai'][0] ?? null) === 'nullable');
        $this->nilai('Batas bawah angka ikut menjadi aturan', in_array('min:1', $aturan['nilai'], true));

        // Pilihan yang mengandung koma akan terpecah menjadi dua kalau aturannya
        // ditulis sebagai teks in:a,b. Rule::in menutup lubang itu.
        $pakaiRuleIn = false;

        foreach ($aturan['jenis'] as $satu) {
            if (is_object($satu)) {
                $pakaiRuleIn = true;
            }
        }

        $this->nilai('Pilihan memakai Rule::in, bukan aturan berbentuk teks', $pakaiRuleIn);

        $bersih = $skema->buangYangTakDikenal($definisi, ['nomor' => 'A1', 'sisa_form_lama' => 'x']);
        $this->nilai('Field yang tidak dikenal skema dibuang', ! array_key_exists('sisa_form_lama', $bersih));

        $seragam = $skema->seragamkan($definisi, [
            'nilai' => '250000',
            'vendor' => '7',
            'auto' => '1',
            'mulai' => '2026-03-15T00:00:00.000Z',
            'nomor' => '',
        ]);

        $this->nilai('Angka disimpan sebagai angka, bukan teks', $seragam['nilai'] === 250000, var_export($seragam['nilai'], true));
        $this->nilai('Relasi disimpan sebagai integer', $seragam['vendor'] === 7, var_export($seragam['vendor'], true));
        $this->nilai('Ya atau tidak disimpan sebagai boolean', $seragam['auto'] === true, var_export($seragam['auto'], true));
        $this->nilai('Tanggal dipotong menjadi sepuluh karakter', $seragam['mulai'] === '2026-03-15', var_export($seragam['mulai'], true));
        $this->nilai('Isian kosong disimpan sebagai null', $seragam['nomor'] === null);
    }

    private function periksaConstraint(): void
    {
        $user = User::query()->first();
        $jenis = DocumentType::query()->where('code', 'sop')->first();

        if ($user === null || $jenis === null) {
            $this->nilai('Ada pengguna dan jenis dokumen sop untuk diuji', false);

            return;
        }

        $dokumen = Document::query()->create([
            'document_type_id' => $jenis->id,
            'title' => '[DATA UJI] Pemeriksaan constraint',
            'created_by_user_id' => $user->id,
        ]);

        // Versi pertama berlaku sejak 1 Januari, tanpa batas akhir.
        $this->nilai(
            'Versi pertama yang disahkan diterima',
            $this->berhasil(fn () => $this->versi($dokumen, $user, 1, 'approved', '2026-01-01', null)),
        );

        // Versi kedua mulai berlaku di tengah periode versi pertama.
        $this->nilai(
            'Versi bertindih ditolak basis data dengan SQLSTATE 23P01',
            $this->ditolakDengan(fn () => $this->versi($dokumen, $user, 2, 'approved', '2026-06-01', null), '23P01'),
        );

        // Versi disahkan tanpa tanggal mulai berlaku.
        $this->nilai(
            'Versi disahkan tanpa tanggal berlaku ditolak',
            $this->ditolakDengan(fn () => $this->versi($dokumen, $user, 3, 'approved', null, null), '23514'),
        );

        // Draf tanpa tanggal tidak terkena constraint sama sekali.
        $this->nilai(
            'Draf tanpa tanggal berlaku tetap diterima',
            $this->berhasil(fn () => $this->versi($dokumen, $user, 4, 'draft', null, null)),
        );

        // Tutup versi pertama, lalu sisipkan penggantinya. Urutan ini yang benar.
        DocumentVersion::query()
            ->where('document_id', $dokumen->id)
            ->where('version_number', 1)
            ->update(['effective_until' => '2026-07-01']);

        $this->nilai(
            'Setelah versi lama ditutup, penggantinya diterima',
            $this->berhasil(fn () => $this->versi($dokumen, $user, 5, 'approved', '2026-07-01', null)),
        );

        // Inilah pertanyaan yang ditanyakan auditor.
        $dokumen->refresh();

        $maret = $dokumen->versiBerlakuPada('2026-03-15');
        $agustus = $dokumen->versiBerlakuPada('2026-08-15');

        $this->nilai(
            'Versi yang berlaku 15 Maret 2026 adalah versi 1',
            $maret?->version_number === 1,
            'dijawab versi '.($maret?->version_number ?? 'kosong'),
        );

        $this->nilai(
            'Versi yang berlaku 15 Agustus 2026 adalah versi 5',
            $agustus?->version_number === 5,
            'dijawab versi '.($agustus?->version_number ?? 'kosong'),
        );

        $this->nilai(
            'Riwayat kedua versi tetap berstatus disahkan, tidak ada yang diubah',
            DocumentVersion::query()->where('document_id', $dokumen->id)->where('status', 'approved')->count() === 2,
        );
    }

    /**
     * Penggantian versi di hari yang sama.
     *
     * Kasus ini terlewat sampai ada yang menemukannya di lingkungan sungguhan.
     * Pemeriksaan constraint di atas memakai 1 Januari dan 1 Juli, dan
     * pengujian lewat antarmuka juga memakai dua tanggal yang berjauhan, jadi
     * tidak satu pun menyentuh keadaan yang paling wajar terjadi: dokumen
     * disahkan pagi ini, penggantinya disahkan sore ini juga.
     *
     * Berbeda dari periksaConstraint yang menyisipkan baris langsung, di sini
     * yang dipanggil adalah PengelolaDokumen, yaitu jalan yang sama persis
     * dengan yang dilalui tombol Approve.
     */
    private function periksaPenggantianHariSama(): void
    {
        $user = User::query()->first();
        $jenis = DocumentType::query()->where('code', 'sop')->first();

        if ($user === null || $jenis === null) {
            $this->nilai('Ada pengguna dan jenis dokumen sop untuk diuji', false);

            return;
        }

        $pengelola = app(PengelolaDokumen::class);
        $hari = now()->toDateString();

        $dokumen = Document::query()->create([
            'document_type_id' => $jenis->id,
            'title' => '[DATA UJI] Penggantian di hari yang sama',
            'created_by_user_id' => $user->id,
        ]);

        $v1 = $this->versi($dokumen, $user, 1, 'draft', null, null);
        $v2 = $this->versi($dokumen, $user, 2, 'draft', null, null);
        $v3 = $this->versi($dokumen, $user, 3, 'draft', null, null);

        $this->nilai(
            'Versi pertama bisa disahkan berlaku hari ini',
            $this->berhasil(fn () => $pengelola->sahkan($v1, $user, now())),
        );

        $this->nilai(
            'Versi kedua bisa disahkan di hari yang sama',
            $this->berhasil(fn () => $pengelola->sahkan($v2, $user, now())),
            $hari,
        );

        $v1->refresh();

        $this->nilai(
            'Versi pertama ditutup pada tanggal mulai berlakunya sendiri',
            $v1->effective_from?->toDateString() === $hari && $v1->effective_until?->toDateString() === $hari,
            ($v1->effective_from?->toDateString() ?? 'kosong').' sampai '.($v1->effective_until?->toDateString() ?? 'kosong'),
        );

        $dokumen->refresh();
        $berlaku = $dokumen->versiBerlakuPada($hari);

        $this->nilai(
            'Yang berlaku hari ini dijawab versi 2, bukan versi 1',
            $berlaku?->version_number === 2,
            'dijawab versi '.($berlaku?->version_number ?? 'kosong'),
        );

        $this->nilai(
            'Versi 1 tidak terjawab berlaku hari ini, juga dari sisi PHP',
            $v1->berlakuPada($hari) === false,
        );

        $this->nilai(
            'Status versi 1 tetap disahkan, riwayatnya tidak diubah',
            $v1->status === VersionStatus::Disahkan,
            $v1->status->value,
        );

        $this->nilai(
            'current_version_id menunjuk versi 2',
            $dokumen->current_version_id === $v2->id,
        );

        // Tanggal yang mundur bukan penggantian di hari yang sama, dan memang
        // harus ditolak. Yang diperiksa di sini bukan cuma penolakannya, tetapi
        // bahwa penolakannya berupa pesan yang bisa dibaca, bukan QueryException
        // 23514 yang berakhir sebagai layar 500.
        $this->nilai(
            'Pengesahan dengan tanggal lebih awal dari versi berjalan ditolak dengan pesan terbaca',
            $this->ditolakDenganPesan(fn () => $pengelola->sahkan($v3, $user, now()->subDay())),
        );

        $this->nilai(
            'Versi 3 tetap draf setelah penolakan itu',
            $v3->refresh()->status === VersionStatus::Draf,
            $v3->status->value,
        );

        // Penarikan tanpa pengganti di hari yang sama, lewat jalan yang sama.
        $this->nilai(
            'Versi yang berlaku bisa ditarik di hari yang sama',
            $this->berhasil(fn () => $pengelola->tarikTanpaPengganti($v2, now())),
        );

        $this->nilai(
            'Penarikan dengan tanggal lebih awal dari mulai berlaku ditolak dengan pesan terbaca',
            $this->ditolakDenganPesan(fn () => $pengelola->tarikTanpaPengganti($v2->refresh(), now()->subDay())),
        );
    }

    private function periksaPenomoran(): void
    {
        $jenis = DocumentType::query()->where('code', 'sop')->first();

        if ($jenis === null || ! $jenis->perluNomor()) {
            $this->nilai('Jenis sop terdaftar dan perlu nomor dokumen', false);

            return;
        }

        try {
            $pertama = NumberGenerator::next($jenis->kodeUrutan());
            $kedua = NumberGenerator::next($jenis->kodeUrutan());
        } catch (Throwable $e) {
            $this->nilai('Penomoran dokumen berjalan', false, $e->getMessage());

            return;
        }

        $tahun = date('Y');

        $this->nilai(
            "Nomor berbentuk SOP/GA/{$tahun}/0001",
            (bool) preg_match('#^SOP/[A-Z0-9]+/'.$tahun.'/\d{4}$#', $pertama),
            $pertama,
        );

        $this->nilai(
            'Nomor berikutnya berbeda dan berurutan',
            $kedua !== $pertama && (int) substr($kedua, -4) === (int) substr($pertama, -4) + 1,
            $pertama.' lalu '.$kedua,
        );
    }

    // ------------------------------------------------------------------ bantu

    private function versi(Document $dokumen, User $user, int $nomor, string $status, ?string $mulai, ?string $sampai): DocumentVersion
    {
        return DocumentVersion::query()->create([
            'document_id' => $dokumen->id,
            'version_number' => $nomor,
            'storage_path' => 'uji/berkas.pdf',
            'file_hash' => str_repeat('a', 64),
            'file_size' => 1024,
            'mime_type' => 'application/pdf',
            'original_name' => 'uji.pdf',
            'status' => $status,
            'effective_from' => $mulai,
            'effective_until' => $sampai,
            'uploaded_by_user_id' => $user->id,
        ]);
    }

    private function berhasil(callable $aksi): bool
    {
        try {
            DB::transaction($aksi);

            return true;
        } catch (Throwable $e) {
            $this->line('    <fg=gray>'.$e->getMessage().'</>');

            return false;
        }
    }

    private function ditolakDengan(callable $aksi, string $sqlstate): bool
    {
        try {
            DB::transaction($aksi);

            return false;
        } catch (QueryException $e) {
            return $e->getCode() === $sqlstate;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * Benar kalau aksinya ditolak lewat MasalahVersiDokumen, yaitu penolakan
     * yang sudah punya pesan untuk pemakainya. QueryException yang lolos ke
     * sini dihitung gagal, sebab artinya penolakannya baru terjadi di basis
     * data dan pemakainya cuma melihat layar galat.
     */
    private function ditolakDenganPesan(callable $aksi): bool
    {
        try {
            DB::transaction($aksi);

            return false;
        } catch (MasalahVersiDokumen $e) {
            $this->line('    <fg=gray>'.$e->getMessage().'</>');

            return true;
        } catch (Throwable $e) {
            $this->line('    <fg=red>'.$e::class.': '.$e->getMessage().'</>');

            return false;
        }
    }

    private function nilai(string $judul, bool $lolos, ?string $keterangan = null): void
    {
        $lolos ? $this->lolos++ : $this->gagal++;

        $tanda = $lolos ? '<fg=green>OK  </>' : '<fg=red>GAGAL</>';
        $this->line("  {$tanda} {$judul}".($keterangan !== null ? " <fg=gray>({$keterangan})</>" : ''));
    }
}

/**
 * Dilempar untuk membatalkan transaksi pemeriksaan. Bukan tanda ada kesalahan.
 */
class BatalkanUji extends \RuntimeException {}
