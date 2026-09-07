<?php

namespace App\Models;

use App\Services\RealisasiBiaya;
use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kategori biaya GA.
 *
 * Kolom `source` yang menentukan dari mana realisasinya dihitung. Tidak ada satu pun
 * sumber yang realisasinya diketik orang: empat menjumlahkan dari catatan pekerjaan
 * sehari hari, dan satu lagi dari faktur rekanan yang sudah disetujui.
 */
class ExpenseCategory extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'description',
        'source',
        'account_code',
        'is_active',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function sourceLabel(): string
    {
        return RealisasiBiaya::SOURCES[$this->source] ?? $this->source;
    }

    /**
     * Apakah realisasinya datang dari faktur rekanan, bukan dari catatan pekerjaan
     * sehari hari.
     *
     * Keduanya sama sama dijumlahkan sendiri oleh aplikasi. Bedanya ada pada seberapa
     * cepat angkanya lengkap: biaya BBM sudah tercatat pada hari pengisian, sedangkan
     * biaya listrik baru muncul setelah fakturnya masuk dan disetujui. Layar anggaran
     * memakai perbedaan itu untuk menyebut berapa yang masih menunggu persetujuan.
     */
    public function dariTagihan(): bool
    {
        return $this->source === 'tagihan';
    }

    /**
     * Kalimat pendek untuk lencana di layar kategori: seberapa cepat angkanya lengkap.
     */
    public function kesiapanLabel(): string
    {
        return $this->dariTagihan() ? 'Setelah faktur disetujui' : 'Seketika';
    }

    public function pickerLabel(): string
    {
        return $this->code.' '.$this->name;
    }

    /**
     * Nomor akun sebagai kalimat. Kategori yang belum dipetakan mengatakannya apa adanya,
     * karena mengarang nomor akun berarti menaruh angka palsu di jalur menuju jurnal.
     */
    public function accountLabel(): string
    {
        return filled($this->account_code) ? $this->account_code : 'Belum dipetakan';
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
