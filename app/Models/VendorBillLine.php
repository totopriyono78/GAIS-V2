<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use App\Support\Rupiah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu baris pembebanan pada satu tagihan.
 *
 * Baris inilah yang menyambungkan faktur ke anggaran: kategori biayanya, departemen yang
 * memakainya, dan nilainya. Departemen boleh dikosongkan untuk biaya yang memang milik
 * kantor bersama, dan angka itu lalu muncul di layar anggaran sebagai biaya yang belum
 * terbebankan, bukan hilang diam diam.
 */
class VendorBillLine extends Model
{
    use Auditable;

    protected $fillable = [
        'vendor_bill_id',
        'expense_category_id',
        'department_id',
        'description',
        'amount',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2'];
    }

    public function bill(): BelongsTo
    {
        return $this->belongsTo(VendorBill::class, 'vendor_bill_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function amountLabel(): string
    {
        return Rupiah::penuh((float) $this->amount);
    }

    /**
     * Departemen yang dibebani, sebagai kalimat. Baris tanpa departemen menyebut
     * akibatnya, karena "kosong" saja tidak memberi tahu ke mana angkanya pergi.
     */
    public function departemenLabel(): string
    {
        return $this->department?->name ?? 'Belum terbebankan ke departemen';
    }

    public function getAuditLabel(): string
    {
        return ($this->category?->name ?? 'Kategori terhapus').' '.$this->amountLabel();
    }
}
