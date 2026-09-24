<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\DocumentType;
use App\Services\PengelolaDokumen;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;

    /**
     * Penyimpanannya diserahkan ke lapisan layanan, bukan dikerjakan Filament.
     *
     * Bawaan Filament akan menyimpan baris dokumen apa adanya dari isian form.
     * Itu tidak cukup di sini: satu dokumen baru berarti satu baris dokumen,
     * satu baris versi, satu berkas yang dihitung sidik jarinya, satu nomor
     * yang diambil dari urutan, dan metadata yang divalidasi terhadap skema
     * jenisnya. Kelimanya harus terjadi bersama atau tidak sama sekali.
     *
     * Menyerahkannya ke PengelolaDokumen juga yang membuat dokumen yang masuk
     * lewat layar ini dan lewat modul lain tersimpan dengan aturan yang sama
     * persis, bukan dua jalur yang perlahan berbeda.
     */
    protected function handleRecordCreation(array $data): Model
    {
        $jenis = DocumentType::query()->findOrFail($data['document_type_id']);

        $berkas = $data['berkas'] ?? null;

        // storeFiles(false) menyerahkan larik berisi objek unggahan, bukan satu
        // objek, meski hanya satu berkas yang boleh diunggah.
        if (is_array($berkas)) {
            $berkas = reset($berkas);
        }

        abort_unless($berkas instanceof UploadedFile, 422, 'Berkas dokumennya belum terunggah dengan benar.');

        return app(PengelolaDokumen::class)->buat(
            jenis: $jenis,
            file: $berkas,
            judul: $data['title'],
            metadata: $data['metadata'] ?? [],
            user: Auth::user(),
            kategoriId: $data['category_id'] ?? null,
            kerahasiaan: $data['confidentiality'] ?? null,
            departemenId: $data['owner_department_id'] ?? null,
        );
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
