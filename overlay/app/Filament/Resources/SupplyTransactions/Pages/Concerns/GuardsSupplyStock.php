<?php

namespace App\Filament\Resources\SupplyTransactions\Pages\Concerns;

use App\Models\SupplyTransaction;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

/**
 * Menahan mutasi yang akan membuat stok jadi minus, sebelum tersimpan.
 *
 * Modelnya juga menolak keadaan yang sama, tetapi penolakan di model berupa
 * pengecualian yang berujung halaman galat. Di sini penolakannya berupa pesan
 * di bawah kolom Jumlah, ditambah pemberitahuan, supaya orang tahu berapa stok
 * yang sebenarnya tersedia dan bisa langsung memperbaiki angkanya.
 */
trait GuardsSupplyStock
{
    protected function guardStock(array $data, ?int $ignoreTransactionId = null): void
    {
        $masalah = SupplyTransaction::stockProblem(
            $data['supply_item_id'] ?? null,
            $data['type'] ?? null,
            $data['quantity'] ?? null,
            $ignoreTransactionId,
        );

        if ($masalah === null) {
            return;
        }

        Notification::make()
            ->title('Mutasi tidak disimpan')
            ->body($masalah)
            ->danger()
            ->persistent()
            ->send();

        throw ValidationException::withMessages([
            'data.quantity' => $masalah,
        ]);
    }
}
