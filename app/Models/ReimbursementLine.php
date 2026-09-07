<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris adalah satu struk.
 *
 * Tanggalnya tanggal struk, bukan tanggal pengajuan, karena yang dibebankan ke anggaran
 * adalah kapan uangnya keluar.
 */
class ReimbursementLine extends Model
{
    use Auditable;

    protected $fillable = [
        'reimbursement_id',
        'expense_category_id',
        'expense_date',
        'description',
        'amount',
        'file_path',
        'original_name',
        'size_bytes',
    ];

    protected function casts(): array
    {
        return [
            'expense_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function reimbursement(): BelongsTo
    {
        return $this->belongsTo(Reimbursement::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function amountLabel(): string
    {
        return Rupiah::penuh((float) $this->amount);
    }

    /**
     * Keterangan bukti. Struk tanpa foto menyebutnya apa adanya, karena itu yang dicari
     * tim GA saat memeriksa, dan strip kosong tidak memberi tahu apa apa.
     */
    public function buktiLabel(): string
    {
        if (blank($this->file_path)) {
            return 'Belum ada foto struk';
        }

        $kb = (int) ($this->size_bytes ?? 0) / 1024;

        return $kb >= 1024
            ? number_format($kb / 1024, 1, ',', '.').' MB'
            : number_format(max($kb, 1), 0, ',', '.').' KB';
    }

    public function getAuditLabel(): string
    {
        return ($this->description ?? 'Struk').' '.$this->amountLabel();
    }
}
