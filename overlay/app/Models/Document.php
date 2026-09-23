<?php

namespace App\Models;

use App\Enums\Confidentiality;
use App\Enums\DocumentStatus;
use App\Enums\VersionStatus;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Satu dokumen, apa pun jenisnya.
 *
 * ## current_version_id menunjuk versi yang BERLAKU
 *
 * Bukan versi yang terakhir diunggah. Ini perbedaan yang menentukan modul ini
 * benar atau tidak. Kalau keduanya disamakan, seseorang mengunggah draf revisi
 * SOP dan sejak saat itu seluruh perusahaan membuka draf itu sebagai SOP yang
 * berlaku, tanpa seorang pun menyadarinya.
 *
 * ## Otorisasinya tiga lapis yang berdiri sendiri
 *
 * 1. Izin modul menjawab boleh melakukan apa. Ditegakkan trait AuthorizesModule
 *    di resource, sama seperti modul GAIS lainnya.
 * 2. Klasifikasi menjawab boleh melihat yang mana. clearance_level pengguna
 *    dibandingkan dengan confidentiality dokumen.
 * 3. Cakupan data menjawab baris mana saja. data_scope pada peran dibandingkan
 *    dengan departemen pemilik dokumen.
 *
 * Ketiganya tidak boleh dilebur menjadi satu daftar nama izin. Begitu muncul
 * nama seperti documents.read.confidential.own_department, dua dimensi sedang
 * dikalikan ke dalam satu ruang nama dan jumlahnya tidak akan pernah selesai.
 */
class Document extends Model
{
    use Auditable;

    /**
     * archived_at dan destroyed_at wajib ada di sini.
     *
     * Keduanya sempat di-cast tetapi tidak fillable di rancangan asalnya, dan
     * akibatnya pengarsipan diam diam tidak menuliskan tanggalnya: dokumen
     * terlihat terarsip tanpa jejak kapan. Mass assignment mengabaikan atribut
     * yang tidak fillable tanpa memberi galat sama sekali, jadi cacat seperti
     * ini tidak terlihat sampai ada yang memeriksa isi kolomnya.
     */
    protected $fillable = [
        'document_type_id',
        'category_id',
        'document_number',
        'title',
        'description',
        'metadata',
        'confidentiality',
        'current_version_id',
        'status',
        'owner_department_id',
        'created_by_user_id',
        'archived_at',
        'destroyed_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'confidentiality' => Confidentiality::class,
            'status' => DocumentStatus::class,
            'archived_at' => 'datetime',
            'destroyed_at' => 'datetime',
        ];
    }

    // ---------------------------------------------------------------- relasi

    public function type(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderByDesc('version_number');
    }

    public function currentVersion(): BelongsTo
    {
        return $this->belongsTo(DocumentVersion::class, 'current_version_id');
    }

    public function links(): HasMany
    {
        return $this->hasMany(DocumentLink::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class);
    }

    public function ownerDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'owner_department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    // ---------------------------------------------------------------- cakupan

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', DocumentStatus::Aktif->value);
    }

    /**
     * Menyaring dokumen yang boleh dilihat seorang pengguna.
     *
     * Dua lapis sekaligus: klasifikasi kerahasiaan lalu cakupan data. Keduanya
     * dikerjakan sebagai query, bukan sebagai penyaringan di PHP, supaya jumlah
     * baris di penghitung dan di halaman berikutnya ikut benar. Menyaring hasil
     * setelah paginasi adalah cara paling umum membuat halaman kedua kosong
     * tanpa ada yang tahu sebabnya.
     */
    public function scopeTerlihatOleh(Builder $query, User $user): Builder
    {
        if ($user->is_super_admin) {
            return $query;
        }

        $tingkat = $user->clearanceLevel();

        $boleh = array_values(array_map(
            static fn (Confidentiality $k): string => $k->value,
            array_filter(
                Confidentiality::cases(),
                static fn (Confidentiality $k): bool => $k->tingkat() <= $tingkat,
            ),
        ));

        $query->whereIn('confidentiality', $boleh);

        return match ($user->dataScope()) {
            'all' => $query,
            'department' => $query->where('owner_department_id', $user->departmentId()),
            'own' => $query->where('created_by_user_id', $user->id),
            // Cakupan yang tidak dikenali menutup semuanya, bukan membuka
            // semuanya. Nilai yang salah ketik tidak boleh berakhir sebagai
            // akses penuh.
            default => $query->whereRaw('false'),
        };
    }

    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if (blank($kata)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('title', 'ilike', '%'.$kata.'%')
                ->orWhere('document_number', 'ilike', '%'.$kata.'%');
        });
    }

    /**
     * Filter berdasarkan satu field di dalam kolom metadata.
     *
     * Memakai operator @> supaya indeks documents_metadata_gin terpakai. Bentuk
     * yang biasa dipakai Laravel, metadata->field = nilai, menghasilkan
     * ekspresi yang tidak bisa memanfaatkan indeks itu dan berubah menjadi
     * pemindaian seluruh tabel begitu dokumennya banyak.
     */
    public function scopeMetadata(Builder $query, string $field, mixed $nilai): Builder
    {
        return $query->whereRaw('metadata @> ?::jsonb', [
            json_encode([$field => $nilai], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        ]);
    }

    // ---------------------------------------------------------------- bantu

    /**
     * Versi yang berlaku pada satu tanggal, termasuk tanggal yang sudah lewat.
     *
     * Inilah pertanyaan yang ditanyakan auditor, dan alasan status versi lama
     * sengaja tidak diubah saat versi baru disahkan.
     */
    public function versiBerlakuPada(Carbon|string $tanggal): ?DocumentVersion
    {
        $tanggal = $tanggal instanceof Carbon ? $tanggal->toDateString() : $tanggal;

        return $this->versions()
            ->where('status', VersionStatus::Disahkan->value)
            ->whereRaw("daterange(effective_from, effective_until, '[)') @> ?::date", [$tanggal])
            ->first();
    }

    public function versiTerakhir(): ?DocumentVersion
    {
        return $this->versions()->first();
    }

    public function nomorVersiBerikutnya(): int
    {
        return (int) $this->versions()->max('version_number') + 1;
    }

    public function terarsip(): bool
    {
        return $this->status === DocumentStatus::Diarsipkan;
    }

    public function getAuditLabel(): string
    {
        return trim(($this->document_number ?? '').' '.$this->title);
    }
}
