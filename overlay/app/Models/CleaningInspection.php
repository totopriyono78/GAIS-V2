<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

/**
 * Satu putaran pemeriksaan kebersihan.
 *
 *   Dibuat (daftar area lahir sendiri)  ->  Pengawas berkeliling dan mencatat  ->  Selesai
 *
 * Tidak ada langkah penerapan seperti pada opname, karena pemeriksaan ini tidak mengubah
 * angka apa pun di tempat lain. Yang dihasilkannya adalah catatan temuan, dan catatan itu
 * sendiri sudah merupakan hasil akhirnya.
 */
class CleaningInspection extends Model
{
    use Auditable;

    public const SEQUENCE_CODE = 'cleaning_inspection';

    public const STATUSES = [
        'berjalan' => 'Sedang diperiksa',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    protected $fillable = [
        'code',
        'inspection_date',
        'scope_category',
        'scope_frequency',
        'scope_location_id',
        'status',
        'finished_at',
        'notes',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'inspection_date' => 'date',
            'finished_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (CleaningInspection $putaran) {
            if (blank($putaran->code)) {
                $putaran->code = NumberGenerator::next(self::SEQUENCE_CODE);
            }

            if (blank($putaran->created_by_user_id)) {
                $putaran->created_by_user_id = Auth::id();
            }
        });
    }

    // ---------------------------------------------------------------- relasi

    public function lines(): HasMany
    {
        return $this->hasMany(CleaningInspectionLine::class);
    }

    /*
     * Namanya tidak diawali kata scope walaupun kolomnya scope_location_id, dengan alasan
     * yang sama seperti pada kedua opname: Eloquent memperlakukan setiap metode berawalan
     * scope sebagai local query scope.
     */
    public function targetLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'scope_location_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function isRunning(): bool
    {
        return $this->status === 'berjalan';
    }

    public function isFinished(): bool
    {
        return $this->status === 'selesai';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'dibatalkan';
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'berjalan' => 'warning',
            'selesai' => $this->jumlahTemuan() > 0 ? 'danger' : 'success',
            default => 'gray',
        };
    }

    /** Boleh dicatat temuannya. */
    public function bisaDicatat(): bool
    {
        return $this->isRunning();
    }

    // ---------------------------------------------------------------- hitungan

    public function jumlahBaris(): int
    {
        return $this->lines()->count();
    }

    public function sudahDiperiksa(): int
    {
        return $this->lines()->where('checked', true)->count();
    }

    public function belumDiperiksa(): int
    {
        return $this->lines()->where('checked', false)->count();
    }

    /** Area yang hasilnya kurang rapi atau kotor. Inilah yang perlu ditindaklanjuti. */
    public function jumlahTemuan(): int
    {
        return $this->lines()->whereIn('result', ['kurang', 'kotor'])->count();
    }

    public function jumlahBersih(): int
    {
        return $this->lines()->where('result', 'bersih')->count();
    }

    public function kemajuanLabel(): string
    {
        $total = $this->jumlahBaris();

        if ($total === 0) {
            return 'Belum ada area di daftarnya';
        }

        return $this->sudahDiperiksa().' dari '.$total.' area';
    }

    public function temuanLabel(): string
    {
        $diperiksa = $this->sudahDiperiksa();

        if ($diperiksa === 0) {
            return 'Belum ada yang diperiksa';
        }

        return $this->jumlahTemuan().' dari '.$diperiksa.' yang sudah diperiksa';
    }

    public function tahapLabel(): string
    {
        return match (true) {
            $this->isRunning() && $this->jumlahBaris() === 0 => 'Daftar areanya kosong',
            $this->isRunning() => $this->belumDiperiksa().' area belum diperiksa',
            $this->isFinished() && $this->jumlahTemuan() === 0 => 'Selesai, seluruh area bersih',
            $this->isFinished() => 'Selesai dengan '.$this->jumlahTemuan().' temuan',
            default => 'Dibatalkan',
        };
    }

    /** Ringkasan cakupan dalam satu kalimat. */
    public function describeScope(): string
    {
        $bagian = [];

        if (filled($this->scope_frequency)) {
            $bagian[] = 'yang dijadwalkan '.mb_strtolower(ServiceArea::FREQUENCIES[$this->scope_frequency] ?? $this->scope_frequency);
        }

        if (filled($this->scope_category)) {
            $bagian[] = 'jenis '.mb_strtolower(ServiceArea::CATEGORIES[$this->scope_category] ?? $this->scope_category);
        }

        if ($this->scope_location_id !== null) {
            $bagian[] = 'di '.($this->targetLocation?->name ?? 'lokasi yang sudah dihapus');
        }

        return $bagian === []
            ? 'Seluruh area yang masih dipakai'
            : 'Terbatas pada area '.implode(', ', $bagian);
    }

    // ---------------------------------------------------------------- alur

    /** Area yang masuk cakupan putaran ini. */
    public function targetQuery(): Builder
    {
        $query = ServiceArea::query()->where('is_active', true);

        if (filled($this->scope_category)) {
            $query->where('category', $this->scope_category);
        }

        if (filled($this->scope_frequency)) {
            $query->where('frequency', $this->scope_frequency);
        }

        if ($this->scope_location_id !== null) {
            $query->where('location_id', $this->scope_location_id);
        }

        return $query;
    }

    /**
     * Menyusun ulang daftar areanya dari cakupan.
     *
     * Baris lama dihapus lebih dulu, dan itu sebabnya tindakan ini menyebutkan akibatnya di
     * layar sebelum dijalankan: temuan yang terlanjur dicatat ikut hilang bersamanya.
     */
    public function generateLines(): int
    {
        /*
         * Foto baris lama dihapus dari penyimpanan lebih dulu. Baris yang dihapus lewat kueri
         * tidak memanggil peristiwa model mana pun, jadi tanpa langkah ini setiap penyusunan
         * ulang meninggalkan berkas yang tidak lagi ditunjuk baris mana pun dan tidak akan
         * pernah terhapus oleh apa pun.
         */
        $this->lines()
            ->whereNotNull('file_path')
            ->get()
            ->each(fn (CleaningInspectionLine $baris) => $baris->hapusFoto());

        $this->lines()->delete();

        $jumlah = 0;

        $this->targetQuery()
            ->with('staff.employee')
            ->orderBy('code')
            ->chunk(200, function ($areas) use (&$jumlah) {
                $baris = [];

                foreach ($areas as $area) {
                    $baris[] = [
                        'cleaning_inspection_id' => $this->id,
                        'service_area_id' => $area->id,
                        'area_code' => $area->code,
                        'area_name' => $area->name,
                        'staff_name' => $area->staff?->namaLengkap(),
                        'result' => null,
                        'checked' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if ($baris !== []) {
                    CleaningInspectionLine::query()->insert($baris);
                    $jumlah += count($baris);
                }
            });

        return $jumlah;
    }

    /** Alasan putaran ini belum bisa diselesaikan, atau null kalau sudah bisa. */
    public function alasanBelumBisaDiselesaikan(): ?string
    {
        if (! $this->isRunning()) {
            return 'Putaran ini sudah tidak berjalan lagi.';
        }

        if ($this->jumlahBaris() === 0) {
            return 'Daftar areanya masih kosong. Susun ulang daftarnya lebih dulu, atau batalkan putaran ini kalau cakupannya memang salah.';
        }

        if ($this->sudahDiperiksa() === 0) {
            return 'Belum ada satu pun area yang diperiksa. Putaran yang kosong tidak menerangkan apa apa, jadi periksa dulu setidaknya satu area.';
        }

        return null;
    }

    public function selesaikan(): void
    {
        $this->forceFill([
            'status' => 'selesai',
            'finished_at' => now(),
        ])->save();
    }

    public function batalkan(): void
    {
        $this->forceFill(['status' => 'dibatalkan'])->save();
    }

    public function getAuditLabel(): string
    {
        return $this->code;
    }
}
