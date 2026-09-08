<?php

namespace App\Models;

use App\Services\NumberGenerator;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * Satu surat, masuk atau keluar.
 *
 * Nomor agendanya lahir dari urutan yang berbeda menurut arahnya, karena buku agenda surat
 * masuk dan buku agenda surat keluar memang dua buku yang terpisah di hampir setiap kantor.
 * Menyatukan urutannya membuat nomor agenda melompat lompat tanpa sebab yang bisa diterangkan
 * kepada orang yang sedang mencari surat.
 */
class Letter extends Model
{
    use Auditable;

    public const SEQUENCE_MASUK = 'letter_in';

    public const SEQUENCE_KELUAR = 'letter_out';

    public const DIRECTIONS = [
        'masuk' => 'Surat masuk',
        'keluar' => 'Surat keluar',
    ];

    public const CATEGORIES = [
        'undangan' => 'Undangan',
        'penawaran' => 'Penawaran',
        'tagihan' => 'Tagihan dan faktur',
        'pemberitahuan' => 'Pemberitahuan',
        'permohonan' => 'Permohonan',
        'surat_tugas' => 'Surat tugas',
        'kontrak' => 'Kontrak dan perjanjian',
        'lainnya' => 'Lainnya',
    ];

    protected $fillable = [
        'direction',
        'code',
        'letter_number',
        'letter_date',
        'logged_date',
        'category',
        'counterparty',
        'subject',
        'notes',
        'assigned_employee_id',
        'handed_over_at',
        'handed_over_to_employee_id',
        'handover_note',
        'signer_employee_id',
        'file_path',
        'original_name',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'letter_date' => 'date',
            'logged_date' => 'date',
            'handed_over_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Letter $surat) {
            if (blank($surat->code)) {
                $surat->code = NumberGenerator::next($surat->sequenceCode());
            }

            if (blank($surat->created_by_user_id)) {
                $surat->created_by_user_id = Auth::id();
            }
        });

        /*
         * Kolom yang tidak berlaku pada arahnya dikosongkan sebelum disimpan.
         *
         * Formulirnya memang menyembunyikan kolom yang tidak dipakai, tetapi kolom yang
         * disembunyikan tidak ikut terkirim, jadi isi lamanya bertahan di basis data. Surat
         * masuk yang diubah menjadi surat keluar akan menyimpan nama penerima disposisi yang
         * tidak pernah ada, dan nama itu akan muncul lagi di laporan berbulan bulan kemudian
         * tanpa ada yang tahu dari mana asalnya. Cacat yang sama sudah ditemui pada data induk
         * petugas di kiriman P.
         */
        static::saving(function (Letter $surat) {
            if ($surat->isMasuk()) {
                $surat->signer_employee_id = null;

                return;
            }

            $surat->assigned_employee_id = null;
            $surat->handed_over_at = null;
            $surat->handed_over_to_employee_id = null;
            $surat->handover_note = null;
        });
    }

    public function sequenceCode(): string
    {
        return $this->isMasuk() ? self::SEQUENCE_MASUK : self::SEQUENCE_KELUAR;
    }

    // ---------------------------------------------------------------- relasi

    public function assignedEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'assigned_employee_id');
    }

    public function handedOverTo(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'handed_over_to_employee_id');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'signer_employee_id');
    }

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ---------------------------------------------------------------- keadaan

    public function isMasuk(): bool
    {
        return $this->direction === 'masuk';
    }

    public function isKeluar(): bool
    {
        return $this->direction === 'keluar';
    }

    public function directionLabel(): string
    {
        return self::DIRECTIONS[$this->direction] ?? $this->direction;
    }

    public function categoryLabel(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function sudahDiserahkan(): bool
    {
        return $this->handed_over_at !== null;
    }

    /** Label lawan bicaranya, berbeda kata menurut arah suratnya. */
    public function counterpartyLabel(): string
    {
        return ($this->isMasuk() ? 'Dari ' : 'Kepada ').$this->counterparty;
    }

    /**
     * Keadaan surat ini dalam satu kalimat.
     *
     * Surat keluar tidak punya alur lanjutan: ia dicatat setelah ditandatangani dan dikirim,
     * jadi mencatatnya sudah berarti selesai. Yang punya alur hanya surat masuk, dan alurnya
     * satu langkah saja, yaitu sampai ke meja orang yang dituju.
     */
    public function keadaanLabel(): string
    {
        if ($this->isKeluar()) {
            return 'Tercatat';
        }

        if ($this->sudahDiserahkan()) {
            return 'Sudah diserahkan';
        }

        return $this->assigned_employee_id === null
            ? 'Belum ditentukan tujuannya'
            : 'Menunggu diserahkan';
    }

    public function keadaanColor(): string
    {
        return match (true) {
            $this->isKeluar() => 'gray',
            $this->sudahDiserahkan() => 'success',
            $this->assigned_employee_id === null => 'danger',
            default => 'warning',
        };
    }

    /** Jawaban atas pertanyaan "surat itu sudah sampai ke siapa". */
    public function serahTerimaKalimat(): string
    {
        if ($this->isKeluar()) {
            return 'Surat keluar tidak melewati serah terima di dalam kantor.';
        }

        if (! $this->sudahDiserahkan()) {
            return $this->assigned_employee_id === null
                ? 'Belum ditentukan surat ini untuk siapa, jadi belum bisa diserahkan.'
                : 'Menunggu diserahkan kepada '.($this->assignedEmployee?->full_name ?? 'orang yang dituju').'.';
        }

        $penerima = $this->handedOverTo?->full_name ?? 'orang yang namanya sudah dihapus';
        $dituju = $this->assignedEmployee?->full_name;

        $kalimat = 'Diterima '.$penerima.' pada '.$this->handed_over_at->format('d M Y, H:i').'.';

        /*
         * Kalau yang menerima bukan orang yang dituju, itu disebutkan. Surat yang dititipkan
         * ke rekan semeja adalah kejadian sehari hari dan bukan kesalahan, tetapi ia justru
         * keterangan yang paling dicari saat suatu surat dinyatakan tidak pernah sampai.
         */
        if ($dituju !== null && $this->handed_over_to_employee_id !== $this->assigned_employee_id) {
            $kalimat .= ' Surat ini ditujukan kepada '.$dituju.', dan diterima orang lain atas namanya.';
        }

        return $kalimat;
    }

    public function pindaianUrl(): ?string
    {
        return filled($this->file_path)
            ? Storage::disk('public')->url($this->file_path)
            : null;
    }

    public function hapusPindaian(): void
    {
        if (filled($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }
    }

    /** Surat masuk yang sudah punya tujuan tetapi belum sampai ke mejanya. */
    public function scopeMenungguDiserahkan(Builder $query): Builder
    {
        return $query->where('direction', 'masuk')
            ->whereNotNull('assigned_employee_id')
            ->whereNull('handed_over_at');
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->subject;
    }
}
