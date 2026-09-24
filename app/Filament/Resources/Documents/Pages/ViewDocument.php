<?php

namespace App\Filament\Resources\Documents\Pages;

use App\Enums\MetadataFieldType;
use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use App\Services\FormMetadata;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

/**
 * Halaman satu dokumen: keterangannya, metadatanya, pratinjau berkasnya, dan
 * riwayatnya dalam satu layar.
 *
 * Bagian metadata dibangun dari skema jenis dokumennya, sama seperti formulir
 * unggah. Menampilkan kolom metadata apa adanya akan menghasilkan daftar
 * berisi nama teknis seperti pic_internal dan angka 3, sedangkan yang berguna
 * bagi pembacanya adalah label yang tertulis di skema dan nama orangnya.
 */
class ViewDocument extends ViewRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('unduh')
                ->label('Download File')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(fn (Document $record): ?string => $record->currentVersion === null
                    ? null
                    : route('gais.dokumen.unduh', [$record, $record->currentVersion]))
                ->openUrlInNewTab()
                ->visible(fn (Document $record): bool => $record->currentVersion !== null
                    && $record->currentVersion->bolehDiunduh()
                    && DocumentResource::bolehUnduh()),
            EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Document')
                ->columns(3)
                ->schema([
                    TextEntry::make('document_number')
                        ->label('Nomor dokumen')
                        ->badge()
                        ->color('gray')
                        ->placeholder('Tanpa nomor, karena jenisnya lampiran'),
                    TextEntry::make('type.name')
                        ->label('Jenis dokumen'),
                    TextEntry::make('category_id')
                        ->label('Kategori')
                        ->state(fn (Document $record): string => $record->category?->namaLengkap() ?? 'Belum ditentukan'),
                    TextEntry::make('title')
                        ->label('Judul')
                        ->columnSpanFull(),
                    TextEntry::make('description')
                        ->label('Keterangan')
                        ->placeholder('Tidak ada')
                        ->columnSpanFull(),
                    TextEntry::make('confidentiality')
                        ->label('Klasifikasi')
                        ->badge()
                        ->color(fn (Document $record): string => $record->confidentiality->warna())
                        ->state(fn (Document $record): string => $record->confidentiality->label())
                        ->helperText(fn (Document $record): string => $record->confidentiality->keterangan()),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn (Document $record): string => $record->status->warna())
                        ->state(fn (Document $record): string => $record->status->label()),
                    TextEntry::make('ownerDepartment.name')
                        ->label('Departemen pemilik')
                        ->placeholder('Tidak ditentukan'),
                    TextEntry::make('current_version_id')
                        ->label('Versi yang berlaku')
                        ->badge()
                        ->color(fn (Document $record): string => $record->currentVersion === null ? 'warning' : 'success')
                        ->state(fn (Document $record): string => $record->currentVersion === null
                            ? 'Belum ada'
                            : 'v'.$record->currentVersion->version_number)
                        /*
                         * Keterangan ini penting dan sengaja panjang. Dokumen
                         * berjenis yang perlu pengesahan memang belum punya versi
                         * berlaku sampai disahkan, dan tanpa penjelasan, kolom
                         * yang berbunyi "Belum ada" terbaca seperti kerusakan.
                         */
                        ->helperText(fn (Document $record): string => match (true) {
                            $record->currentVersion !== null => 'Berlaku sejak '
                                .$record->currentVersion->effective_from?->translatedFormat('d F Y'),
                            (bool) $record->type?->needs_approval => 'Jenis dokumen ini perlu pengesahan, '
                                .'jadi versinya masih berstatus draf dan belum berlaku.',
                            default => 'Dokumen ini belum punya versi yang berlaku.',
                        }),
                    TextEntry::make('creator.name')
                        ->label('Diunggah oleh')
                        ->placeholder('Tidak diketahui'),
                    TextEntry::make('created_at')
                        ->label('Dibuat')
                        ->dateTime('d F Y, H:i'),
                ]),

            Section::make('Metadata')
                ->columnSpanFull()
                ->columns(3)
                ->visible(fn (Document $record): bool => filled($record->type?->metadata_schema))
                ->schema(fn (Document $record): array => static::entriMetadata($record)),

            Section::make('Preview')
                ->columnSpanFull()
                ->schema([
                    ViewEntry::make('pratinjau')
                        ->hiddenLabel()
                        ->view('filament.dokumen.pratinjau'),
                ]),

            Section::make('History')
                ->columnSpanFull()
                ->collapsible()
                ->schema([
                    ViewEntry::make('riwayat')
                        ->hiddenLabel()
                        ->view('filament.dokumen.riwayat'),
                ]),
        ]);
    }

    /**
     * Satu entri per field metadata, dengan label dan nilai yang terbaca orang.
     *
     * Field relasi disimpan sebagai nomor baris, dan nomor itu tidak berarti
     * apa apa bagi pembacanya. Di sini nomornya ditukar kembali menjadi nama,
     * memakai daftar tabel yang sama dengan yang dipakai formulir unggah.
     *
     * @return list<TextEntry>
     */
    protected static function entriMetadata(Document $record): array
    {
        $skema = $record->type?->metadata_schema ?? [];
        $nilai = $record->metadata ?? [];
        $entri = [];

        foreach ($skema as $field => $definisi) {
            $tipe = MetadataFieldType::tryFrom($definisi['type'] ?? '') ?? MetadataFieldType::Teks;
            $isi = $nilai[$field] ?? null;

            $entri[] = TextEntry::make('metadata.'.$field)
                ->label($definisi['label'] ?? str_replace('_', ' ', $field))
                ->placeholder('Belum diisi')
                ->state(static::bacaNilai($tipe, $definisi, $isi))
                ->when(
                    $tipe === MetadataFieldType::TeksPanjang,
                    fn (TextEntry $e): TextEntry => $e->columnSpanFull(),
                );
        }

        return $entri;
    }

    private static function bacaNilai(MetadataFieldType $tipe, array $definisi, mixed $isi): ?string
    {
        if ($isi === null || $isi === '') {
            return null;
        }

        return match ($tipe) {
            MetadataFieldType::YaTidak => $isi ? 'Ya' : 'Tidak',
            MetadataFieldType::Angka => number_format((float) $isi, 0, ',', '.'),
            MetadataFieldType::Tanggal => Carbon::parse((string) $isi)->translatedFormat('d F Y'),
            MetadataFieldType::Relasi => app(FormMetadata::class)->labelRelasi($definisi['reference'] ?? '', $isi)
                ?? ('#'.$isi.' (tidak ditemukan)'),
            default => (string) $isi,
        };
    }
}
