<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use Auditable;

    public const EMPLOYMENT_STATUSES = [
        'tetap' => 'Karyawan tetap',
        'kontrak' => 'Kontrak',
        'magang' => 'Magang',
        'outsource' => 'Outsource',
    ];

    protected $fillable = [
        'nip',
        'full_name',
        'email',
        'phone',
        'department_id',
        'position',
        'employment_status',
        'join_date',
        'user_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'join_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Departemen yang dikepalai karyawan ini.
     *
     * Jamak, bukan tunggal, karena satu orang sering merangkap kepala dua departemen
     * kecil. Ini satu satunya bentuk atasan yang tercatat di sistem, dan dipakai modul
     * permintaan perbaikan untuk menentukan siapa yang menyetujui.
     */
    public function headedDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'head_employee_id');
    }

    public function getAuditLabel(): string
    {
        return $this->nip.' '.$this->full_name;
    }
}
