<?php

namespace App\Support\Concerns;

use BackedEnum;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * Dipasang di setiap Filament Resource. Resource menyebut kode modulnya lewat properti
 * $moduleCode, dan izin dibaca dari tabel permissions.
 *
 * ## Kenapa berkas ini berbentuk begini
 *
 * Filament membaca izin lewat dua jalur yang berbeda, dan itu tidak terlihat sampai
 * seseorang memakai peran berizin sempit:
 *
 * - Halaman memanggil canViewAny(), canEdit(), canDelete(), dan seterusnya.
 * - Tombol di tabel memanggil getViewAnyAuthorizationResponse(),
 *   getEditAuthorizationResponse(), getDeleteAuthorizationResponse(), dan seterusnya.
 *
 * Sebelum 9 September 2026 berkas ini hanya menimpa jalur pertama. Jalur kedua tetap turun
 * ke bawaan Filament, yang mengizinkan apa pun ketika model tidak punya Policy, dan aplikasi
 * ini memang tidak memakai Policy sama sekali. Akibatnya tombol Ubah dan Hapus tergambar
 * untuk siapa pun yang bisa membuka daftarnya, lalu menolak dengan 403 begitu ditekan.
 *
 * Cacat keduanya lebih serius dan tidak terlihat dari layar. Sejumlah resource menambahkan
 * aturan sendiri dengan pola `parent::canDelete($record) && aturan`. Kata `parent` di situ
 * menunjuk ke kelas Resource bawaan Filament, bukan ke trait ini, jadi bagian izinnya
 * selalu bernilai benar karena Policy-nya tidak ada. Peran yang punya read tetapi tidak
 * punya update atau delete karenanya lolos pemeriksaan izin di modul modul tersebut.
 *
 * ## Bentuk sekarang
 *
 * Satu sumber kebenaran, yaitu allows(). Di atasnya ada satu lapis can* yang boleh ditimpa
 * tiap resource untuk menambahkan aturan keadaan, misalnya tagihan yang sudah disetujui
 * tidak boleh diubah. Di atas keduanya, getAuthorizationResponse() meneruskan pertanyaan
 * Filament ke lapis can* yang sama, sehingga tombol dan halaman menjawab dengan aturan yang
 * sama persis.
 *
 * Resource yang menambahkan aturan menuliskannya begini, dan **tidak boleh** memakai
 * `parent::`, karena `parent` melompati trait ini dan mengembalikan cacat di atas:
 *
 *     public static function canDelete(Model $record): bool
 *     {
 *         return static::allows('delete') && $record->bisaDihapus();
 *     }
 */
trait AuthorizesModule
{
    /**
     * Nama tindakan bawaan Filament, diterjemahkan ke nama tindakan di tabel permissions.
     *
     * Yang tidak ada di daftar ini ditolak. Menolak yang tidak dikenal lebih aman daripada
     * mengizinkannya, karena tindakan baru bisa muncul dari Filament tanpa kita tahu.
     *
     * @var array<string, string>
     */
    protected const PEMETAAN_IZIN = [
        'viewAny' => 'read',
        'view' => 'read',
        'create' => 'create',
        'replicate' => 'create',
        'update' => 'update',
        'reorder' => 'update',
        'delete' => 'delete',
        'deleteAny' => 'delete',
        'restore' => 'delete',
        'restoreAny' => 'delete',
        'forceDelete' => 'delete',
        'forceDeleteAny' => 'delete',
    ];

    public static function moduleCode(): string
    {
        return static::$moduleCode;
    }

    /**
     * Pemeriksaan izin mentah. Satu satunya tempat tabel permissions dibaca.
     *
     * Dipakai juga oleh tombol tindakan di luar CRUD, misalnya approve, issue, dan print.
     * Filament tidak pernah memeriksa tindakan buatan sendiri, jadi tiap tombol semacam itu
     * wajib menyebut izinnya sendiri lewat ->visible(fn () => static::allows('...')).
     */
    protected static function allows(string $action): bool
    {
        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        return $user->hasPermission(static::moduleCode().'.'.$action);
    }

    // ------------------------------------------------------------------ lapis can*

    public static function canViewAny(): bool
    {
        return static::allows('read');
    }

    public static function canView(Model $record): bool
    {
        return static::allows('read');
    }

    public static function canCreate(): bool
    {
        return static::allows('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::allows('update');
    }

    public static function canDelete(Model $record): bool
    {
        return static::allows('delete');
    }

    public static function canDeleteAny(): bool
    {
        return static::allows('delete');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    // ------------------------------------------------------------------ pintu Filament

    /**
     * Jawaban izin untuk seluruh tindakan bawaan Filament.
     *
     * Seluruh get*AuthorizationResponse() milik Filament bermuara ke sini, dan dari sini
     * pertanyaannya diteruskan ke lapis can* di atas. Itu yang membuat tombol di tabel dan
     * halaman yang dibukanya tidak pernah berbeda pendapat.
     *
     * Perhatikan bahwa metode ini tidak boleh dipanggil dari lapis can*, karena arah
     * panggilannya memang satu arah. Resource yang menambah aturan memakai allows(),
     * bukan parent::.
     */
    public static function getAuthorizationResponse(string | UnitEnum $action, ?Model $record = null): Response
    {
        $nama = match (true) {
            $action instanceof BackedEnum => (string) $action->value,
            $action instanceof UnitEnum => $action->name,
            default => $action,
        };

        if (! array_key_exists($nama, self::PEMETAAN_IZIN)) {
            return Response::deny('Tindakan ini tidak dikenali oleh pengaturan hak akses.');
        }

        $boleh = match ($nama) {
            'viewAny' => static::canViewAny(),
            'view' => $record === null ? static::canViewAny() : static::canView($record),
            'create', 'replicate' => static::canCreate(),
            'update' => $record === null ? static::allows('update') : static::canEdit($record),
            'reorder' => static::allows('update'),
            'delete', 'restore', 'forceDelete' => $record === null
                ? static::canDeleteAny()
                : static::canDelete($record),
            default => static::canDeleteAny(),
        };

        return $boleh
            ? Response::allow()
            : Response::deny('Anda tidak punya hak akses untuk melakukan itu di modul ini.');
    }
}
