<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Jenis permintaan perbaikan, beserta target waktu penyelesaiannya.
 */
class ServiceRequestCategory extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'description',
        'default_priority',
        'sla_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sla_hours' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function pickerLabel(): string
    {
        return $this->code.' '.$this->name;
    }

    /**
     * Target waktu sebagai kalimat. Kategori yang belum punya target mengatakannya apa
     * adanya, bukan menampilkan angka kosong yang terbaca sebagai nol jam.
     */
    public function slaLabel(): string
    {
        if (blank($this->sla_hours)) {
            return 'Belum ada target';
        }

        if ($this->sla_hours % 24 === 0) {
            $hari = intdiv($this->sla_hours, 24);

            return $hari.' hari kerja';
        }

        return $this->sla_hours.' jam';
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
