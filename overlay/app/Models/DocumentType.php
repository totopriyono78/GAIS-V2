<?php

namespace App\Models;

use App\Enums\Lifecycle;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Profil satu jenis dokumen, dan sumber kebenaran tunggal modul ini.
 *
 * metadata_schema dipakai untuk tiga hal sekaligus: membangun form unggah,
 * memvalidasi isiannya, dan merender filter di daftar dokumen. Menambah jenis
 * dokumen baru karena itu berarti menambah baris di tabel ini, bukan menulis
 * kode dan menunggu rilis berikutnya.
 *
 * Penomorannya menumpang number_sequences yang sudah dipakai seluruh GAIS.
 * Satu jenis dokumen yang punya awalan nomor mendapat satu baris urutan yang
 * dibuat dan disesuaikan sendiri oleh model ini, sehingga tim GA tidak perlu
 * mendaftarkannya dua kali di dua layar yang berbeda.
 */
class DocumentType extends Model
{
    use Auditable;

    /**
     * Tabel yang boleh dirujuk field metadata bertipe relasi.
     *
     * Daftarnya tertutup, bukan isian bebas. Field metadata yang menunjuk tabel
     * yang tidak ada akan terlihat baik baik saja saat jenis dokumennya disimpan,
     * dan baru gagal berbulan bulan kemudian saat ada yang mengisi form unggah.
     * Menutup daftarnya memindahkan kegagalan itu ke saat pengisian, tempat yang
     * jauh lebih murah.
     *
     * @var array<string, string>
     */
    public const TABEL_RELASI = [
        'departments' => 'Departemen',
        'locations' => 'Lokasi',
        'employees' => 'Karyawan',
        'users' => 'Pengguna sistem',
        'vendors' => 'Rekanan',
        'assets' => 'Aset',
        'vehicles' => 'Kendaraan',
        'documents' => 'Dokumen lain',
    ];

    protected $fillable = [
        'code',
        'name',
        'lifecycle',
        'is_versioned',
        'needs_approval',
        'has_validity',
        'retention_years',
        'number_prefix',
        'metadata_schema',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'lifecycle' => Lifecycle::class,
            'is_versioned' => 'boolean',
            'needs_approval' => 'boolean',
            'has_validity' => 'boolean',
            'is_active' => 'boolean',
            'retention_years' => 'integer',
            'sort_order' => 'integer',
            'metadata_schema' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(function (DocumentType $jenis) {
            $jenis->sesuaikanUrutanNomor();
        });
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Kode urutan nomor milik jenis ini di tabel number_sequences.
     *
     * Diawali dokumen. supaya tidak bertabrakan dengan kode urutan modul lain
     * yang kebetulan bernama sama.
     */
    public function kodeUrutan(): string
    {
        return 'dokumen.'.$this->code;
    }

    public function perluNomor(): bool
    {
        return $this->lifecycle->perluNomor() && filled($this->number_prefix);
    }

    /**
     * Field metadata yang boleh dipakai sebagai filter di daftar dokumen.
     *
     * @return array<string, array<string, mixed>>
     */
    public function fieldTerfilter(): array
    {
        return array_filter(
            $this->metadata_schema ?? [],
            static fn (array $def): bool => ($def['filterable'] ?? false) === true,
        );
    }

    /**
     * Field metadata yang ikut dibaca saat pencarian teks.
     *
     * @return array<string, array<string, mixed>>
     */
    public function fieldTercari(): array
    {
        return array_filter(
            $this->metadata_schema ?? [],
            static fn (array $def): bool => ($def['searchable'] ?? false) === true,
        );
    }

    /**
     * Membuat atau menyesuaikan baris urutan nomor milik jenis ini.
     *
     * Formatnya prefix/departemen/tahun/urut, misalnya SOP/GA/2026/0012, dan
     * itu tersusun sendiri dari awalan SOP/GA dengan periode Y. Kode departemen
     * masih diambil dari pengaturan karena standar kodenya belum ditetapkan;
     * begitu ditetapkan, yang berubah cukup satu baris pengaturan.
     *
     * Nomor yang sudah terpakai tidak pernah disentuh. Mengubah awalan hanya
     * mengubah bentuk nomor berikutnya, bukan nomor yang sudah dicetak di
     * dokumen yang beredar.
     */
    protected function sesuaikanUrutanNomor(): void
    {
        if (! $this->perluNomor()) {
            return;
        }

        // Dibungkus, karena model ini bisa tersimpan saat seeder berjalan di basis
        // data yang baru dibuat dan tabel pengaturannya belum tentu sudah terisi.
        try {
            $dept = Setting::get('dokumen.kode_departemen', 'GA');
        } catch (\Throwable) {
            $dept = 'GA';
        }

        NumberSequence::query()->updateOrCreate(
            ['code' => $this->kodeUrutan()],
            [
                'name' => 'Nomor '.$this->name,
                'prefix' => trim($this->number_prefix.'/'.$dept, '/'),
                'separator' => '/',
                'period_format' => 'Y',
                'padding' => 4,
            ],
        );
    }

    public function jumlahField(): int
    {
        return count($this->metadata_schema ?? []);
    }

    /**
     * Sifat jenis ini dalam satu kalimat, untuk ditaruh di bawah namanya.
     */
    public function sifatRingkas(): string
    {
        $sifat = [];

        $sifat[] = $this->is_versioned ? 'berversi' : 'tanpa versi';

        if ($this->needs_approval) {
            $sifat[] = 'perlu pengesahan';
        }

        if ($this->has_validity) {
            $sifat[] = 'punya masa berlaku';
        }

        $sifat[] = $this->jumlahField().' field metadata';

        return ucfirst(implode(', ', $sifat)).'.';
    }

    public function masaSimpanTerbaca(): string
    {
        if ($this->retention_years !== null) {
            return $this->retention_years.' tahun';
        }

        return $this->lifecycle === Lifecycle::Attachment ? 'Ikut induknya' : 'Permanen';
    }

    public function getAuditLabel(): string
    {
        return $this->name;
    }
}
