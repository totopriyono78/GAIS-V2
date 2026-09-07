<?php

namespace App\Models;

use App\Support\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NumberSequence extends Model
{
    use Auditable;

    protected $fillable = [
        'code',
        'name',
        'prefix',
        'separator',
        'period_format',
        'padding',
    ];

    protected function casts(): array
    {
        return [
            'padding' => 'integer',
        ];
    }

    public function periods(): HasMany
    {
        return $this->hasMany(NumberSequencePeriod::class);
    }

    public function periodFor(?string $period = null): string
    {
        if ($period !== null) {
            return $period;
        }

        return $this->period_format !== null && $this->period_format !== ''
            ? now()->format($this->period_format)
            : '';
    }

    public function format(string $period, int $number): string
    {
        $parts = array_filter(
            [$this->prefix, $period, str_pad((string) $number, $this->padding, '0', STR_PAD_LEFT)],
            fn ($part) => $part !== '' && $part !== null,
        );

        return implode($this->separator ?: '/', $parts);
    }

    /**
     * Contoh nomor berikutnya untuk ditampilkan di layar, tanpa memakai nomor urut.
     */
    public function preview(): string
    {
        return $this->format($this->periodFor(), 1);
    }

    public function getAuditLabel(): string
    {
        return $this->code.' '.$this->name;
    }
}
