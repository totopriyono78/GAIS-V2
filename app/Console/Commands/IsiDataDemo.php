<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use Illuminate\Console\Command;

/**
 * Mengisi data contoh untuk demo: departemen, lokasi, karyawan, dan aset.
 *
 * Semua yang dibuat perintah ini diberi penanda [DATA DEMO] pada keterangannya,
 * dan karyawannya memakai NIP berawalan DEMO. Penanda itu yang dipakai perintah
 * --hapus untuk membersihkannya kembali, dan yang membedakannya dari data yang
 * Anda masukkan sendiri. Data milik Anda tidak akan ikut terhapus.
 *
 * Nama orang, merek, dan angka di bawah ini karangan untuk keperluan peragaan.
 * Jangan dipakai sebagai dasar keputusan apa pun.
 */
class IsiDataDemo extends Command
{
    protected $signature = 'gais:demo-data
        {--jumlah=150 : Jumlah aset bergerak yang dibuat}
        {--hapus : Hapus kembali seluruh data demo}';

    protected $description = 'Mengisi departemen, lokasi, karyawan, dan aset contoh untuk demo';

    public const PENANDA = '[DATA DEMO]';

    private const DEPARTEMEN = [
        'GA' => 'General Affair',
        'FIN' => 'Finance',
        'IT' => 'Information Technology',
        'HRD' => 'Human Resources',
        'OPS' => 'Operations',
    ];

    private const KARYAWAN = [
        ['DEMO-001', 'Andi Prasetyo', 'GA', 'Staf General Affair'],
        ['DEMO-002', 'Siti Rahmawati', 'GA', 'Admin Aset'],
        ['DEMO-003', 'Budi Santoso', 'GA', 'Manajer General Affair'],
        ['DEMO-004', 'Dewi Anggraini', 'FIN', 'Staf Akuntansi'],
        ['DEMO-005', 'Rizal Hakim', 'FIN', 'Manajer Keuangan'],
        ['DEMO-006', 'Putri Maharani', 'IT', 'Staf Dukungan Teknis'],
        ['DEMO-007', 'Agus Setiawan', 'IT', 'Administrator Jaringan'],
        ['DEMO-008', 'Lestari Ningsih', 'HRD', 'Staf Kepegawaian'],
        ['DEMO-009', 'Hendra Wijaya', 'HRD', 'Manajer SDM'],
        ['DEMO-010', 'Maya Kusuma', 'OPS', 'Staf Operasional'],
        ['DEMO-011', 'Fajar Nugroho', 'OPS', 'Pengemudi'],
        ['DEMO-012', 'Ratna Sari', 'OPS', 'Staf Umum'],
    ];

    /**
     * kode kategori => [barang, [merek], nilai minimum, nilai maksimum]
     */
    private const BARANG = [
        'KOM' => [
            ['Laptop kerja', 'PC desktop', 'Monitor 24 inci', 'Printer laser', 'Pemindai dokumen', 'Switch 24 port', 'Access point', 'Server rak'],
            ['Dell', 'HP', 'Lenovo', 'Asus', 'Acer', 'Cisco', 'TP-Link', 'Epson'],
            4_500_000, 42_000_000,
        ],
        'MSK' => [
            ['Mesin fotokopi', 'Proyektor ruang rapat', 'Mesin penghancur kertas', 'Mesin absensi sidik jari', 'Laminator'],
            ['Fuji Xerox', 'Canon', 'Epson', 'Kyocera', 'GBC'],
            2_500_000, 55_000_000,
        ],
        'ALK' => [
            ['Pesawat telepon meja', 'Mesin faksimile', 'Radio genggam', 'Telepon konferensi'],
            ['Panasonic', 'Motorola', 'Polycom', 'Yealink'],
            750_000, 9_500_000,
        ],
        'PKY' => [
            ['Meja kerja kayu', 'Kursi tamu kayu', 'Lemari arsip kayu', 'Meja rapat kayu', 'Rak buku kayu'],
            ['Olympic', 'Ligna', 'Activ', 'Pro Design'],
            900_000, 18_000_000,
        ],
        'PLG' => [
            ['Filing cabinet 4 laci', 'Lemari besi arsip', 'Rak arsip besi', 'Loker karyawan', 'Brankas'],
            ['Brother', 'Elite', 'Daichiban', 'Krisbow'],
            1_800_000, 22_000_000,
        ],
        'PGU' => [
            ['AC split 1 PK', 'AC split 2 PK', 'AC standing 3 PK', 'Kipas angin dinding', 'Exhaust fan'],
            ['Daikin', 'Panasonic', 'LG', 'Sharp', 'Mitsubishi'],
            600_000, 28_000_000,
        ],
        'KR4' => [
            ['Kendaraan operasional', 'Kendaraan dinas', 'Mobil boks pengiriman'],
            ['Toyota Avanza', 'Toyota Innova', 'Mitsubishi L300', 'Isuzu Elf', 'Daihatsu Gran Max'],
            185_000_000, 520_000_000,
        ],
        'KR2' => [
            ['Sepeda motor operasional', 'Sepeda motor kurir'],
            ['Honda Vario', 'Honda Beat', 'Yamaha NMAX', 'Honda PCX'],
            18_000_000, 36_000_000,
        ],
        'PRS' => [
            ['Instalasi listrik lantai', 'Instalasi jaringan LAN lantai', 'Genset cadangan', 'Pompa air gedung', 'Sistem pemadam kebakaran'],
            ['Schneider', 'Panasonic', 'Perkins', 'Grundfos', 'Appron'],
            25_000_000, 480_000_000,
        ],
    ];

    /** Aset besar yang jumlahnya memang hanya satu atau dua di kantor. */
    private const ASET_TUNGGAL = [
        ['TNH', 'Tanah kantor pusat', null, null, 12_000_000_000, 'GA', 'tetap'],
        ['BGN', 'Gedung Head Office', null, null, 24_500_000_000, 'GA', 'tetap'],
        ['BGT', 'Gudang semi permanen belakang', null, null, 640_000_000, 'OPS', 'tetap'],
        ['ADP', 'Renovasi ruang rapat lantai 3', null, null, 185_000_000, 'GA', 'tetap'],
    ];

    public function handle(): int
    {
        return $this->option('hapus') ? $this->hapus() : $this->isi();
    }

    protected function isi(): int
    {
        $kategoriSiap = AssetCategory::query()
            ->where('is_active', true)
            ->whereNotNull('account_asset')
            ->where('account_asset', '!=', '')
            ->get()
            ->keyBy('code');

        if ($kategoriSiap->isEmpty()) {
            $this->error('Belum ada kategori aset yang nomor akun COA-nya terisi.');
            $this->line('Jalankan dua perintah ini dulu:');
            $this->line('  php artisan db:seed --class=AssetCategorySeeder');
            $this->line('  php artisan gais:coa-demo');

            return self::FAILURE;
        }

        // Titik awal angka acak dipatok supaya komposisi merek, nilai, dan tanggal
        // relatif mirip tiap kali dijalankan. Pemilihan lokasi dan pemegang memakai
        // pengacak lain, jadi susunannya tidak persis sama, dan itu tidak masalah
        // untuk keperluan peragaan.
        mt_srand(20260906);

        $departemen = $this->buatDepartemen();
        $lokasi = $this->buatLokasi();
        $karyawan = $this->buatKaryawan($departemen);

        $jumlah = max((int) $this->option('jumlah'), 1);
        $dibuat = $this->buatAset($kategoriSiap, $departemen, $lokasi, $karyawan, $jumlah);

        $this->newLine();
        $this->info("Data demo dimasukkan: {$departemen->count()} departemen, {$lokasi->count()} lokasi, "
            ."{$karyawan->count()} karyawan, dan {$dibuat} aset.");
        $this->warn('Semuanya data karangan untuk peragaan, bukan data perusahaan Anda.');
        $this->warn('Bersihkan sebelum data sungguhan masuk: php artisan gais:demo-data --hapus');

        return self::SUCCESS;
    }

    protected function buatDepartemen()
    {
        $hasil = collect();

        foreach (self::DEPARTEMEN as $kode => $nama) {
            $hasil[$kode] = Department::query()->updateOrCreate(
                ['code' => $kode],
                [
                    'name' => $nama,
                    'cost_center' => 'CC-'.$kode,
                    'is_active' => true,
                ],
            );
        }

        return $hasil;
    }

    protected function buatLokasi()
    {
        $gedung = Location::query()->updateOrCreate(
            ['code' => 'HO'],
            ['name' => 'Head Office', 'type' => 'gedung', 'parent_id' => null, 'description' => self::PENANDA, 'is_active' => true],
        );

        $hasil = collect(['HO' => $gedung]);

        $ruangan = [
            1 => ['Lobi utama', 'Ruang resepsionis', 'Ruang tunggu tamu', 'Ruang arsip'],
            2 => ['Ruang Finance', 'Ruang HRD', 'Ruang rapat kecil', 'Pantry lantai 2'],
            3 => ['Ruang IT', 'Ruang server', 'Ruang rapat besar', 'Ruang General Affair'],
        ];

        foreach ($ruangan as $nomor => $daftar) {
            $lantai = Location::query()->updateOrCreate(
                ['code' => 'HO-L'.$nomor],
                ['name' => 'Lantai '.$nomor, 'type' => 'lantai', 'parent_id' => $gedung->id, 'description' => self::PENANDA, 'is_active' => true],
            );

            $hasil['HO-L'.$nomor] = $lantai;

            foreach ($daftar as $urut => $nama) {
                $kode = sprintf('HO-L%d-R%02d', $nomor, $urut + 1);

                $hasil[$kode] = Location::query()->updateOrCreate(
                    ['code' => $kode],
                    ['name' => $nama, 'type' => 'ruangan', 'parent_id' => $lantai->id, 'description' => self::PENANDA, 'is_active' => true],
                );
            }
        }

        $hasil['HO-GDG'] = Location::query()->updateOrCreate(
            ['code' => 'HO-GDG'],
            ['name' => 'Gudang belakang', 'type' => 'area', 'parent_id' => $gedung->id, 'description' => self::PENANDA, 'is_active' => true],
        );

        $hasil['HO-PRK'] = Location::query()->updateOrCreate(
            ['code' => 'HO-PRK'],
            ['name' => 'Area parkir kendaraan dinas', 'type' => 'area', 'parent_id' => $gedung->id, 'description' => self::PENANDA, 'is_active' => true],
        );

        return $hasil;
    }

    protected function buatKaryawan($departemen)
    {
        $hasil = collect();

        foreach (self::KARYAWAN as [$nip, $nama, $kodeDepartemen, $jabatan]) {
            $hasil[$nip] = Employee::query()->updateOrCreate(
                ['nip' => $nip],
                [
                    'full_name' => $nama,
                    'department_id' => $departemen[$kodeDepartemen]->id,
                    'position' => $jabatan,
                    'employment_status' => 'tetap',
                    'join_date' => now()->subYears(mt_rand(1, 12))->subDays(mt_rand(0, 300))->toDateString(),
                    'is_active' => true,
                ],
            );
        }

        return $hasil;
    }

    protected function buatAset($kategori, $departemen, $lokasi, $karyawan, int $jumlah): int
    {
        $ruangan = $lokasi->filter(fn (Location $l) => $l->type === 'ruangan')->values();
        $area = $lokasi->filter(fn (Location $l) => $l->type === 'area')->values();
        $statusUmum = ['aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'aktif', 'dipinjam', 'perbaikan', 'tidak_dipakai'];
        $kondisiUmum = ['baik', 'baik', 'baik', 'baik', 'baik', 'baik', 'perlu_perbaikan', 'perlu_perbaikan', 'rusak'];
        $kodeDepartemen = array_keys(self::DEPARTEMEN);

        $dibuat = 0;
        $bar = $this->output->createProgressBar($jumlah + count(self::ASET_TUNGGAL));
        $bar->start();

        foreach (self::ASET_TUNGGAL as [$kodeKategori, $nama, $merek, $model, $nilai, $kodeDept, $jenis]) {
            if (! $kategori->has($kodeKategori) || ! $departemen->has($kodeDept)) {
                $bar->advance();

                continue;
            }

            $this->simpanAset([
                'name' => $nama,
                'asset_category_id' => $kategori[$kodeKategori]->id,
                'department_id' => $departemen[$kodeDept]->id,
                'location_id' => $lokasi['HO']->id,
                'asset_type' => $jenis,
                'brand' => $merek,
                'model' => $model,
                'acquisition_date' => now()->subYears(mt_rand(6, 14))->startOfYear()->addDays(mt_rand(0, 300))->toDateString(),
                'acquisition_source' => 'pembelian',
                'acquisition_cost' => $nilai,
                'status' => $kodeKategori === 'ADP' ? 'tidak_dipakai' : 'aktif',
                'condition' => 'baik',
            ]);

            $dibuat++;
            $bar->advance();
        }

        $kodeBarang = array_values(array_filter(array_keys(self::BARANG), fn ($k) => $kategori->has($k)));

        if ($kodeBarang === []) {
            $bar->finish();

            return $dibuat;
        }

        for ($i = 0; $i < $jumlah; $i++) {
            $kodeKategori = $kodeBarang[array_rand($kodeBarang)];
            [$daftarBarang, $daftarMerek, $minimum, $maksimum] = self::BARANG[$kodeKategori];

            $kendaraan = in_array($kodeKategori, ['KR2', 'KR4'], true);
            $melekat = $kodeKategori === 'PRS';

            $tempat = match (true) {
                $kendaraan => $area->firstWhere('code', 'HO-PRK'),
                $melekat => $lokasi['HO-L'.mt_rand(1, 3)] ?? null,
                default => $ruangan->random(),
            };

            // Sebagian sengaja dikosongkan lokasi atau penanggung jawabnya, supaya
            // penyaring "belum punya lokasi" dan "belum punya penanggung jawab"
            // ada isinya saat diperagakan.
            $tanpaLokasi = mt_rand(1, 20) === 1;
            $tanpaPemegang = $melekat || mt_rand(1, 12) === 1;

            $this->simpanAset([
                'name' => $daftarBarang[array_rand($daftarBarang)],
                'asset_category_id' => $kategori[$kodeKategori]->id,
                'department_id' => $departemen[$kodeDepartemen[array_rand($kodeDepartemen)]]->id,
                'location_id' => $tanpaLokasi ? null : $tempat?->id,
                'custodian_employee_id' => $tanpaPemegang ? null : $karyawan->random()->id,
                'asset_type' => $melekat ? 'tetap' : 'bergerak',
                'brand' => $daftarMerek[array_rand($daftarMerek)],
                'model' => 'Tipe '.mt_rand(100, 999),
                'serial_number' => strtoupper($kodeKategori).'-'.mt_rand(100000, 999999),
                'acquisition_date' => now()->subYears(mt_rand(0, 8))->startOfYear()->addDays(mt_rand(0, 330))->toDateString(),
                'acquisition_source' => mt_rand(1, 15) === 1 ? 'hibah' : 'pembelian',
                'acquisition_cost' => mt_rand((int) ($minimum / 1000), (int) ($maksimum / 1000)) * 1000,
                'status' => $statusUmum[array_rand($statusUmum)],
                'condition' => $kondisiUmum[array_rand($kondisiUmum)],
            ]);

            $dibuat++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        return $dibuat;
    }

    protected function simpanAset(array $attributes): void
    {
        Asset::query()->create($attributes + [
            'notes' => self::PENANDA.' Data karangan untuk peragaan, bukan aset sungguhan.',
        ]);
    }

    protected function hapus(): int
    {
        $idAset = Asset::query()
            ->where('notes', 'like', '%'.self::PENANDA.'%')
            ->pluck('id');

        $idKaryawan = Employee::query()->where('nip', 'like', 'DEMO-%')->pluck('id');

        // Jejak audit data demo ikut dibersihkan, supaya layar Jejak audit tidak
        // penuh riwayat barang yang sudah tidak ada.
        $jumlahJejak = AuditLog::query()
            ->where(function ($query) use ($idAset, $idKaryawan) {
                $query->where(fn ($q) => $q->where('auditable_type', Asset::class)->whereIn('auditable_id', $idAset))
                    ->orWhere(fn ($q) => $q->where('auditable_type', Employee::class)->whereIn('auditable_id', $idKaryawan));
            })
            ->delete();

        $jumlahAset = Asset::query()->whereIn('id', $idAset)->delete();
        $jumlahKaryawan = Employee::query()->whereIn('id', $idKaryawan)->delete();

        $jumlahLokasi = 0;

        // Ruangan dihapus lebih dulu, baru lantai, baru gedung, supaya tidak ada
        // baris yang masih menjadi induk saat dihapus.
        foreach (['ruangan', 'area', 'lantai', 'gedung'] as $jenis) {
            $jumlahLokasi += Location::query()
                ->where('type', $jenis)
                ->where('description', 'like', '%'.self::PENANDA.'%')
                ->delete();
        }

        $jumlahDepartemen = 0;

        foreach (array_keys(self::DEPARTEMEN) as $kode) {
            $departemen = Department::query()->where('code', $kode)->first();

            if ($departemen === null) {
                continue;
            }

            if ($departemen->assets()->exists() || $departemen->employees()->exists()) {
                $this->warn("Departemen {$kode} tidak dihapus karena masih dipakai data lain.");

                continue;
            }

            $departemen->delete();
            $jumlahDepartemen++;
        }

        $this->info("Data demo dihapus: {$jumlahAset} aset, {$jumlahKaryawan} karyawan, "
            ."{$jumlahLokasi} lokasi, {$jumlahDepartemen} departemen, {$jumlahJejak} baris jejak audit.");
        $this->line('Nomor urut kode aset tidak ikut mundur, jadi kode aset berikutnya melanjutkan dari nomor terakhir.');
        $this->line('Nomor akun COA contoh dibersihkan terpisah: php artisan gais:coa-demo --hapus');

        return self::SUCCESS;
    }
}
