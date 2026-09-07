<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use Auditable;

    public const TYPES = [
        'text' => 'Teks satu baris',
        'textarea' => 'Teks panjang',
        'number' => 'Angka',
        'boolean' => 'Ya atau tidak',
        'pilihan' => 'Pilihan tetap',
    ];

    /**
     * Pilihan yang sah untuk pengaturan bertipe pilihan.
     *
     * Pengaturan yang jawabannya cuma dua atau tiga kemungkinan tidak boleh diketik
     * bebas. Kotak teks berisi kata yang salah eja akan diam diam jatuh ke nilai bawaan,
     * dan orang yang mengetiknya akan yakin sudah mengubah sesuatu padahal tidak. Daftar
     * ini yang membuat layar pengaturan menampilkan daftar pilihan, bukan kotak teks.
     *
     * @var array<string, array<string, string>>
     */
    public const CHOICES = [
        'penyusutan.mulai' => [
            'bulan_perolehan' => 'Bulan perolehan ikut disusutkan penuh',
            'bulan_berikutnya' => 'Mulai bulan berikutnya setelah perolehan',
        ],
        'penyusutan.nilai_sisa' => [
            'nol' => 'Nol, seluruh nilai perolehan disusutkan',
            'ikut_kategori' => 'Ikut persen nilai sisa di kategori aset',
        ],
        'penyusutan.saldo_menurun_akhir' => [
            'habiskan' => 'Habiskan sisanya di tahun terakhir',
            'sisakan' => 'Biarkan menyisakan nilai buku di akhir umur',
        ],
        'layanan.mendesak_lewati_persetujuan' => [
            'ya' => 'Langsung diteruskan ke tim GA',
            'tidak' => 'Tetap menunggu persetujuan atasan',
        ],
    ];

    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'label',
        'description',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('gais.settings'));
        static::deleted(fn () => Cache::forget('gais.settings'));
    }

    public static function all_values(): array
    {
        return Cache::rememberForever('gais.settings', fn () => static::query()->pluck('value', 'key')->all());
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $values = static::all_values();

        return array_key_exists($key, $values) && $values[$key] !== null && $values[$key] !== ''
            ? $values[$key]
            : $default;
    }

    /**
     * @return array<string, string>
     */
    public static function choicesFor(?string $key): array
    {
        return self::CHOICES[$key] ?? [];
    }

    public function hasChoices(): bool
    {
        return $this->type === 'pilihan' && self::choicesFor($this->key) !== [];
    }

    /**
     * Nilai untuk dibaca manusia. Pengaturan bertipe pilihan menyimpan kunci pendek di
     * basis data, dan kunci itu tidak berarti apa apa bagi orang yang membaca daftarnya.
     */
    public function valueLabel(): ?string
    {
        if (blank($this->value)) {
            return null;
        }

        if ($this->type === 'boolean') {
            return $this->value === '1' ? 'Ya' : 'Tidak';
        }

        return self::choicesFor($this->key)[$this->value] ?? $this->value;
    }

    public function getAuditLabel(): string
    {
        return $this->label;
    }
}
