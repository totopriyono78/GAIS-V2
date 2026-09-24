<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\DocumentType;
use App\Services\SkemaMetadata;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * Metadata tetap melewati validator yang sama dengan jalur pembuatan.
     *
     * Kalau tidak, isian yang diubah lewat layar ubah bisa tersimpan dalam
     * bentuk yang berbeda dari isian yang sama saat pertama diunggah, dan
     * penyaring metadata akan menemukan yang satu tetapi tidak yang lain.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $jenis = DocumentType::query()->find($record->document_type_id) ?? $record->type;

        if ($jenis instanceof DocumentType) {
            $data['metadata'] = app(SkemaMetadata::class)->periksa($jenis, $data['metadata'] ?? []);
        }

        // Berkas tidak ikut diubah dari sini. Versi baru ditambahkan lewat
        // jalurnya sendiri, bukan dengan menimpa berkas yang sudah disahkan.
        unset($data['berkas']);

        $record->update($data);

        return $record;
    }
}
