<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NumberSequencePeriod extends Model
{
    protected $fillable = [
        'number_sequence_id',
        'period',
        'next_number',
    ];

    protected function casts(): array
    {
        return [
            'next_number' => 'integer',
        ];
    }

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(NumberSequence::class, 'number_sequence_id');
    }
}
