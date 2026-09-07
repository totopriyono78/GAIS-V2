<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Impor data aset dari berkas CSV.
 *
 * Dirancang untuk data yang belum rapi. Baris yang kekurangan hal yang tidak
 * bisa ditebak akan ditolak dan dilaporkan, sedangkan hal yang bisa dilanjutkan
 * tanpa menebak nebak (misalnya lokasi yang belum terdaftar) tetap diimpor
 * dengan kolom itu dikosongkan, lalu dicatat sebagai peringatan. Dengan begitu
 * data bisa dibereskan sambil jalan, bukan harus bersih sebelum masuk.
 */
class AssetImporter
{
    /** Nama kolom yang dikenali, ditulis dalam bentuk yang sudah dinormalkan. */
    public const COLUMN_ALIASES = [
        'name' => ['nama', 'nama_aset', 'nama_barang', 'deskripsi'],
        'code' => ['kode', 'kode_aset', 'nomor_aset'],
        'category' => ['kategori', 'kategori_aset', 'jenis_barang', 'kelompok'],
        'location' => ['lokasi', 'ruangan', 'penempatan'],
        'custodian' => ['penanggung_jawab', 'pemegang', 'pengguna', 'nip'],
        'department' => ['departemen', 'bagian', 'divisi', 'unit'],
        'asset_type' => ['jenis', 'jenis_aset', 'tipe_aset'],
        'brand' => ['merek', 'merk', 'brand'],
        'model' => ['model', 'tipe', 'type'],
        'serial_number' => ['nomor_seri', 'serial', 'serial_number', 'sn'],
        'acquisition_date' => ['tanggal_perolehan', 'tgl_perolehan', 'tanggal_beli', 'tanggal_pembelian'],
        'acquisition_source' => ['sumber_perolehan', 'sumber', 'asal'],
        'acquisition_cost' => ['nilai_perolehan', 'harga', 'harga_perolehan', 'nilai', 'harga_beli'],
        'status' => ['status'],
        'condition' => ['kondisi'],
        'warranty_until' => ['garansi_sampai', 'akhir_garansi', 'garansi'],
        'notes' => ['catatan', 'keterangan'],
    ];

    /** @var array<int, array{baris: int, pesan: string, isi: string}> */
    public array $errors = [];

    /** @var array<int, array{baris: int, pesan: string, isi: string}> */
    public array $warnings = [];

    public int $total = 0;

    public int $imported = 0;

    /** @var array<string, int> */
    private array $categoryCache = [];

    /** @var array<string, int> */
    private array $locationCache = [];

    /** @var array<string, int> */
    private array $employeeCache = [];

    /** @var array<string, int> */
    private array $departmentCache = [];

    /** @var array<string, true> */
    private array $seenCodes = [];

    public function __construct(private bool $dryRun = true)
    {
    }

    public static function normalizeHeader(string $header): string
    {
        $header = strtolower(trim($header));
        $header = preg_replace('/[^a-z0-9]+/', '_', $header) ?? '';

        return trim($header, '_');
    }

    /**
     * Membaca angka yang ditulis dengan gaya Indonesia maupun gaya Inggris.
     * Contoh yang diterima: 1.500.000, 1500000, Rp 1.500.000,50, 1,500,000.50
     */
    public static function parseMoney(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || $value === '-') {
            return null;
        }

        $clean = preg_replace('/[^0-9,.\-]/', '', $value) ?? '';

        if ($clean === '' || $clean === '-') {
            return null;
        }

        $lastComma = strrpos($clean, ',');
        $lastDot = strrpos($clean, '.');

        if ($lastComma !== false && $lastDot !== false) {
            // Dua jenis tanda dipakai sekaligus, jadi yang paling belakang adalah pemisah desimal.
            if ($lastComma > $lastDot) {
                $clean = str_replace('.', '', $clean);
                $clean = str_replace(',', '.', $clean);
            } else {
                $clean = str_replace(',', '', $clean);
            }
        } elseif ($lastComma !== false || $lastDot !== false) {
            $symbol = $lastComma !== false ? ',' : '.';
            $position = $lastComma !== false ? $lastComma : $lastDot;
            $occurrences = substr_count($clean, $symbol);
            $decimals = strlen($clean) - $position - 1;

            // Pemisah ribuan selalu memisah kelompok tiga angka dan biasanya muncul
            // lebih dari sekali. Satu tanda dengan satu atau dua angka di belakangnya
            // hampir pasti pemisah desimal, contohnya 750,5 dan 99.5.
            $isDecimalSeparator = $occurrences === 1 && $decimals >= 1 && $decimals <= 2;

            $clean = $isDecimalSeparator
                ? str_replace($symbol, '.', $clean)
                : str_replace($symbol, '', $clean);
        }

        if (! is_numeric($clean)) {
            return null;
        }

        return (float) $clean;
    }

    public static function parseDate(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || $value === '-') {
            return null;
        }

        $formats = ['Y-m-d', 'd/m/Y', 'd-m-Y', 'd.m.Y', 'Y/m/d', 'j/n/Y', 'd/m/y', 'Y-m-d H:i:s'];

        foreach ($formats as $format) {
            $date = DateTimeImmutable::createFromFormat('!'.$format, $value);

            // Tanggal seperti 32/13/2024 tetap terbaca oleh createFromFormat, tapi
            // hasilnya meluber ke bulan berikutnya. Menuliskannya kembali dan
            // membandingkan dengan teks aslinya menutup celah itu.
            if ($date === false || $date->format($format) !== $value) {
                continue;
            }

            $year = (int) $date->format('Y');

            if ($year >= 1900 && $year <= 2200) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Menebak pemisah kolom dari baris judul. Excel berbahasa Indonesia
     * biasanya menyimpan CSV dengan titik koma.
     */
    public static function detectDelimiter(string $headerLine): string
    {
        $candidates = [';' => substr_count($headerLine, ';'), ',' => substr_count($headerLine, ','), "\t" => substr_count($headerLine, "\t")];

        arsort($candidates);

        $best = array_key_first($candidates);

        return $candidates[$best] > 0 ? $best : ',';
    }

    public function run(string $path): self
    {
        if (! is_readable($path)) {
            throw new RuntimeException('Berkas impor tidak bisa dibaca.');
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('Berkas impor tidak bisa dibuka.');
        }

        $firstLine = fgets($handle);

        if ($firstLine === false) {
            fclose($handle);

            throw new RuntimeException('Berkas impor kosong.');
        }

        // Buang penanda BOM yang sering ikut saat Excel menyimpan CSV UTF-8.
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine) ?? $firstLine;
        $delimiter = self::detectDelimiter($firstLine);

        $header = str_getcsv(rtrim($firstLine, "\r\n"), $delimiter, '"', '');
        $map = $this->mapHeader($header);

        if (! isset($map['name'])) {
            fclose($handle);

            throw new RuntimeException('Kolom nama aset tidak ditemukan. Pastikan baris pertama berisi judul kolom.');
        }

        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, $delimiter, '"', '')) !== false) {
            $rowNumber++;

            if ($this->isBlankRow($row)) {
                continue;
            }

            $this->total++;
            $this->handleRow($rowNumber, $row, $map);
        }

        fclose($handle);

        return $this;
    }

    /**
     * @return array<string, int>
     */
    private function mapHeader(array $header): array
    {
        $map = [];

        foreach ($header as $index => $title) {
            $normalized = self::normalizeHeader((string) $title);

            foreach (self::COLUMN_ALIASES as $field => $aliases) {
                if (in_array($normalized, $aliases, true) && ! isset($map[$field])) {
                    $map[$field] = $index;
                }
            }
        }

        return $map;
    }

    private function isBlankRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function value(array $row, array $map, string $field): ?string
    {
        if (! isset($map[$field])) {
            return null;
        }

        $value = $row[$map[$field]] ?? null;

        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function handleRow(int $rowNumber, array $row, array $map): void
    {
        $raw = implode(' | ', array_map(fn ($v) => trim((string) $v), $row));
        $raw = mb_substr($raw, 0, 300);

        $name = $this->value($row, $map, 'name');

        if ($name === null) {
            $this->errors[] = ['baris' => $rowNumber, 'pesan' => 'Nama aset kosong.', 'isi' => $raw];

            return;
        }

        $categoryValue = $this->value($row, $map, 'category');
        $category = $categoryValue !== null ? $this->findCategoryModel($categoryValue) : null;

        if ($category === null) {
            $this->errors[] = [
                'baris' => $rowNumber,
                'pesan' => $categoryValue === null
                    ? 'Kategori kosong. Nomor akun kategori jadi segmen kedua kode aset, jadi tidak bisa dikosongkan.'
                    : "Kategori \"{$categoryValue}\" belum terdaftar. Tambahkan dulu di menu Kategori aset.",
                'isi' => $raw,
            ];

            return;
        }

        if (blank($category->account_asset)) {
            $this->errors[] = [
                'baris' => $rowNumber,
                'pesan' => "Kategori \"{$category->name}\" belum punya nomor akun COA, jadi kode asetnya belum bisa dibentuk. Isi dulu di menu Kategori aset.",
                'isi' => $raw,
            ];

            return;
        }

        $departmentValue = $this->value($row, $map, 'department');
        $departmentId = $departmentValue !== null ? $this->findDepartment($departmentValue) : null;

        if ($departmentId === null) {
            $this->errors[] = [
                'baris' => $rowNumber,
                'pesan' => $departmentValue === null
                    ? 'Departemen kosong. Kode departemen jadi segmen pertama kode aset, jadi tidak bisa dikosongkan.'
                    : "Departemen \"{$departmentValue}\" belum terdaftar. Tambahkan dulu di menu Departemen.",
                'isi' => $raw,
            ];

            return;
        }

        $code = $this->value($row, $map, 'code');

        if ($code !== null) {
            if (isset($this->seenCodes[$code])) {
                $this->errors[] = ['baris' => $rowNumber, 'pesan' => "Kode {$code} muncul dua kali di berkas ini.", 'isi' => $raw];

                return;
            }

            if (Asset::query()->where('code', $code)->exists()) {
                $this->errors[] = ['baris' => $rowNumber, 'pesan' => "Kode {$code} sudah dipakai aset lain.", 'isi' => $raw];

                return;
            }

            $this->seenCodes[$code] = true;
        }

        $costValue = $this->value($row, $map, 'acquisition_cost');
        $cost = self::parseMoney($costValue);

        if ($costValue !== null && $cost === null) {
            $this->errors[] = ['baris' => $rowNumber, 'pesan' => "Nilai perolehan \"{$costValue}\" tidak terbaca sebagai angka.", 'isi' => $raw];

            return;
        }

        $attributes = [
            'code' => $code,
            'name' => mb_substr($name, 0, 200),
            'asset_category_id' => $category->id,
            'department_id' => $departmentId,
            'acquisition_cost' => $cost ?? 0,
            'brand' => $this->trimTo($this->value($row, $map, 'brand'), 100),
            'model' => $this->trimTo($this->value($row, $map, 'model'), 100),
            'serial_number' => $this->trimTo($this->value($row, $map, 'serial_number'), 100),
            'notes' => $this->value($row, $map, 'notes'),
        ];

        $attributes['location_id'] = $this->resolveOptional(
            $rowNumber, $raw, $this->value($row, $map, 'location'), 'Lokasi',
            fn (string $v) => $this->findLocation($v),
        );

        $attributes['custodian_employee_id'] = $this->resolveOptional(
            $rowNumber, $raw, $this->value($row, $map, 'custodian'), 'Penanggung jawab',
            fn (string $v) => $this->findEmployee($v),
        );

        $dateValue = $this->value($row, $map, 'acquisition_date');
        $date = self::parseDate($dateValue);

        if ($dateValue !== null && $date === null) {
            $this->warnings[] = [
                'baris' => $rowNumber,
                'pesan' => "Tanggal perolehan \"{$dateValue}\" tidak terbaca, dikosongkan. Kode aset memakai tahun sekarang.",
                'isi' => $raw,
            ];
        }

        $attributes['acquisition_date'] = $date;

        $warrantyValue = $this->value($row, $map, 'warranty_until');
        $attributes['warranty_until'] = self::parseDate($warrantyValue);

        $attributes['asset_type'] = $this->matchOption(
            $rowNumber, $raw, $this->value($row, $map, 'asset_type'), Asset::TYPES, 'bergerak', 'Jenis aset',
        );

        $attributes['acquisition_source'] = $this->matchOption(
            $rowNumber, $raw, $this->value($row, $map, 'acquisition_source'), Asset::SOURCES, 'data_lama', 'Sumber perolehan',
        );

        $attributes['status'] = $this->matchOption(
            $rowNumber, $raw, $this->value($row, $map, 'status'), Asset::STATUSES, 'aktif', 'Status',
        );

        $attributes['condition'] = $this->matchOption(
            $rowNumber, $raw, $this->value($row, $map, 'condition'), Asset::CONDITIONS, 'baik', 'Kondisi',
        );

        if ($this->dryRun) {
            $this->imported++;

            return;
        }

        DB::transaction(function () use ($attributes) {
            Asset::query()->create(array_filter(
                $attributes,
                fn ($value, $key) => ! ($key === 'code' && $value === null),
                ARRAY_FILTER_USE_BOTH,
            ));
        });

        $this->imported++;
    }

    private function trimTo(?string $value, int $length): ?string
    {
        return $value === null ? null : mb_substr($value, 0, $length);
    }

    private function resolveOptional(int $rowNumber, string $raw, ?string $value, string $label, callable $finder): ?int
    {
        if ($value === null) {
            return null;
        }

        $id = $finder($value);

        if ($id === null) {
            $this->warnings[] = [
                'baris' => $rowNumber,
                'pesan' => "{$label} \"{$value}\" belum terdaftar, kolomnya dikosongkan.",
                'isi' => $raw,
            ];
        }

        return $id;
    }

    /**
     * @param  array<string, string>  $options
     */
    private function matchOption(int $rowNumber, string $raw, ?string $value, array $options, string $default, string $label): string
    {
        if ($value === null) {
            return $default;
        }

        $needle = self::normalizeHeader($value);

        foreach ($options as $key => $optionLabel) {
            if ($needle === $key || $needle === self::normalizeHeader($optionLabel)) {
                return $key;
            }
        }

        $this->warnings[] = [
            'baris' => $rowNumber,
            'pesan' => "{$label} \"{$value}\" tidak dikenal, dipakai nilai bawaan \"{$options[$default]}\".",
            'isi' => $raw,
        ];

        return $default;
    }

    private function findCategoryModel(string $value): ?AssetCategory
    {
        $id = $this->lookup($this->categoryCache, $value, function (string $needle) {
            $category = AssetCategory::query()
                ->whereRaw('lower(code) = ?', [$needle])
                ->orWhereRaw('lower(name) = ?', [$needle])
                ->first();

            return $category?->id;
        });

        return $id === null ? null : AssetCategory::query()->find($id);
    }

    private function findLocation(string $value): ?int
    {
        return $this->lookup($this->locationCache, $value, function (string $needle) {
            $location = Location::query()
                ->whereRaw('lower(code) = ?', [$needle])
                ->orWhereRaw('lower(name) = ?', [$needle])
                ->first();

            return $location?->id;
        });
    }

    private function findEmployee(string $value): ?int
    {
        return $this->lookup($this->employeeCache, $value, function (string $needle) {
            $employee = Employee::query()
                ->whereRaw('lower(nip) = ?', [$needle])
                ->orWhereRaw('lower(full_name) = ?', [$needle])
                ->first();

            return $employee?->id;
        });
    }

    private function findDepartment(string $value): ?int
    {
        return $this->lookup($this->departmentCache, $value, function (string $needle) {
            $department = Department::query()
                ->whereRaw('lower(code) = ?', [$needle])
                ->orWhereRaw('lower(name) = ?', [$needle])
                ->first();

            return $department?->id;
        });
    }

    private function lookup(array &$cache, string $value, callable $finder): ?int
    {
        $needle = mb_strtolower(trim($value));

        if (array_key_exists($needle, $cache)) {
            return $cache[$needle] ?: null;
        }

        $id = $finder($needle);

        $cache[$needle] = $id ?? 0;

        return $id;
    }

    /**
     * Isi berkas laporan kesalahan, siap ditulis ke disk atau diunduh.
     */
    public function reportCsv(): string
    {
        $lines = ['jenis;baris;pesan;isi_baris'];

        foreach ($this->errors as $error) {
            $lines[] = 'Ditolak;'.$error['baris'].';"'.str_replace('"', "'", $error['pesan']).'";"'.str_replace('"', "'", $error['isi']).'"';
        }

        foreach ($this->warnings as $warning) {
            $lines[] = 'Peringatan;'.$warning['baris'].';"'.str_replace('"', "'", $warning['pesan']).'";"'.str_replace('"', "'", $warning['isi']).'"';
        }

        return implode("\n", $lines)."\n";
    }

    public static function templateCsv(): string
    {
        $header = 'nama;kategori;departemen;lokasi;penanggung_jawab;jenis;merek;model;nomor_seri;'
            .'tanggal_perolehan;sumber_perolehan;nilai_perolehan;status;kondisi;garansi_sampai;catatan';

        // Baris di bawah ini contoh isian, bukan data nyata. Hapus sebelum mengunggah.
        $example = 'CONTOH Laptop Dell Latitude 5440;KOM;FIN;HO-L3-R05;198702001;bergerak;Dell;Latitude 5440;SN12345678;'
            .'12/03/2024;pembelian;15.750.000;aktif;baik;12/03/2027;hapus baris contoh ini sebelum mengunggah';

        return $header."\n".$example."\n";
    }
}
