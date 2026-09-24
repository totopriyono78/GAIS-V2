<?php

namespace App\Services;

use App\Enums\MetadataFieldType;
use App\Models\Asset;
use App\Models\Department;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Location;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Vendor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Membangun komponen form dari skema metadata sebuah jenis dokumen.
 *
 * Ini bagian ketiga dari satu definisi yang dipakai tiga kali. Dua yang lain
 * ada di SkemaMetadata: memvalidasi isian dan menyeragamkan bentuk simpannya.
 *
 * Dari luar, metadata dinamis terlihat seperti "form dengan field yang berubah
 * ubah". Yang sebenarnya dibangun adalah mesin kecil, dan kelas ini bagian yang
 * menggambarnya ke layar. Menambah jenis dokumen baru karena itu tidak pernah
 * menyentuh kode ini.
 */
class FormMetadata
{
    /**
     * Tabel yang boleh dirujuk, beserta cara menuliskan namanya di layar.
     *
     * Daftarnya sengaja tertutup dan cocok dengan DocumentType::TABEL_RELASI.
     * Relasi ke tabel yang tidak ada di sini akan digambar sebagai isian teks
     * biasa, bukan menggagalkan seluruh form: satu field yang salah definisi
     * tidak pantas membuat layar unggah tidak bisa dibuka sama sekali.
     *
     * @return array{0: class-string<Model>, 1: list<string>, 2: callable(Model): string}|null
     */
    private function relasi(string $tabel): ?array
    {
        return match ($tabel) {
            'departments' => [Department::class, ['name', 'code'], fn (Department $m): string => $m->name],
            'locations' => [Location::class, ['name', 'code'], fn (Location $m): string => $m->name],
            'employees' => [Employee::class, ['full_name', 'nip'], fn (Employee $m): string => $m->full_name],
            'users' => [User::class, ['name', 'email'], fn (User $m): string => $m->name],
            'vendors' => [Vendor::class, ['name', 'code'], fn (Vendor $m): string => $m->code.' '.$m->name],
            'assets' => [Asset::class, ['name', 'code'], fn (Asset $m): string => $m->code.' '.$m->name],
            'vehicles' => [Vehicle::class, ['plate_number'], fn (Vehicle $m): string => $m->plate_number],
            'documents' => [Document::class, ['title', 'document_number'], fn (Document $m): string => trim(($m->document_number ?? '').' '.$m->title)],
            default => null,
        };
    }

    /**
     * Nama yang terbaca orang untuk satu nilai relasi.
     *
     * Metadata bertipe relasi disimpan sebagai nomor baris, dan nomor itu tidak
     * berarti apa apa di layar detail. Dipakai daftar tabel yang sama dengan
     * yang dipakai formulir, supaya nama yang muncul saat mengisi dan saat
     * membaca tidak pernah berbeda.
     */
    public function labelRelasi(string $tabel, mixed $nilai): ?string
    {
        $relasi = $this->relasi($tabel);

        if ($relasi === null || blank($nilai)) {
            return null;
        }

        [$model, , $keLabel] = $relasi;

        $baris = $model::query()->find($nilai);

        return $baris === null ? null : $keLabel($baris);
    }

    /**
     * Komponen form untuk mengisi metadata.
     *
     * @param  array<string, array<string, mixed>>  $skema
     * @return list<\Filament\Schemas\Components\Component>
     */
    public function komponen(array $skema): array
    {
        $komponen = [];

        foreach ($skema as $nama => $definisi) {
            $komponen[] = $this->satuField($nama, $definisi, wajibkan: true);
        }

        return $komponen;
    }

    /**
     * Komponen untuk menyaring, dibangun dari skema yang sama.
     *
     * Bedanya dua: tidak ada yang wajib diisi, dan hanya field bertanda
     * filterable yang ikut. Menyaring dengan field yang wajib diisi akan
     * membuat penyaringnya menolak dikosongkan lagi.
     *
     * @param  array<string, array<string, mixed>>  $skema
     * @return list<\Filament\Schemas\Components\Component>
     */
    public function komponenFilter(array $skema): array
    {
        $komponen = [];

        foreach ($skema as $nama => $definisi) {
            if (($definisi['filterable'] ?? false) !== true) {
                continue;
            }

            $komponen[] = $this->satuField($nama, $definisi, wajibkan: false);
        }

        return $komponen;
    }

    /**
     * Nama komponennya sengaja polos, tanpa awalan metadata.
     *
     * Jalur penyimpanannya dipegang wadahnya lewat statePath('metadata'), bukan
     * ditulis di nama tiap field. Bedanya bukan gaya penulisan: ketika nama
     * field membawa awalannya sendiri, Filament tidak pernah menyiapkan larik
     * metadata di state, sehingga setiap komponen Alpine yang menautkan diri ke
     * data.metadata.sesuatu gagal menemukan tempatnya. Akibatnya console penuh
     * galat penautan, dan pemilih tanggal membuka dokumen lama dengan kotak
     * kosong meski tanggalnya tersimpan.
     */
    private function satuField(string $jalur, array $definisi, bool $wajibkan)
    {
        $tipe = MetadataFieldType::tryFrom($definisi['type'] ?? '') ?? MetadataFieldType::Teks;
        $label = $definisi['label'] ?? str_replace('_', ' ', $jalur);
        $wajib = $wajibkan && ($definisi['required'] ?? false) === true;
        $bantuan = $definisi['help'] ?? null;

        $field = match ($tipe) {
            MetadataFieldType::TeksPanjang => Textarea::make($jalur)->rows(3)->maxLength(10000)->columnSpanFull(),
            MetadataFieldType::Angka => TextInput::make($jalur)
                ->numeric()
                ->when(isset($definisi['min']), fn (TextInput $t): TextInput => $t->minValue($definisi['min'])),
            MetadataFieldType::Tanggal => DatePicker::make($jalur)->native(false),
            MetadataFieldType::YaTidak => Toggle::make($jalur),
            MetadataFieldType::Pilihan => Select::make($jalur)
                ->options(array_combine($definisi['options'] ?? [], $definisi['options'] ?? []))
                ->searchable(count($definisi['options'] ?? []) > 8),
            MetadataFieldType::Relasi => $this->fieldRelasi($jalur, $definisi['reference'] ?? ''),
            default => TextInput::make($jalur)->maxLength(255),
        };

        return $field
            ->label($label)
            ->required($wajib)
            ->when($bantuan !== null, fn ($f) => $f->helperText($bantuan));
    }

    private function fieldRelasi(string $jalur, string $tabel)
    {
        $relasi = $this->relasi($tabel);

        if ($relasi === null) {
            // Definisi yang menunjuk tabel tak dikenal tetap bisa diisi, dan
            // isinya tetap tersimpan. Yang hilang cuma daftar pilihannya.
            return TextInput::make($jalur)
                ->numeric()
                ->helperText('Tabel rujukan '.$tabel.' tidak dikenali, jadi daftar pilihannya tidak bisa ditampilkan. Isi dengan nomor barisnya.');
        }

        [$model, $kolomCari, $keLabel] = $relasi;

        return Select::make($jalur)
            ->searchable()
            // Tidak memuat seluruh baris di muka. Tabel aset dan karyawan bisa
            // berisi ribuan baris, dan memuatnya hanya untuk satu field metadata
            // membuat form unggah terasa berat tanpa alasan.
            ->getSearchResultsUsing(fn (string $cari): array => $model::query()
                ->where(function (Builder $q) use ($kolomCari, $cari) {
                    foreach ($kolomCari as $kolom) {
                        $q->orWhere($kolom, 'ilike', '%'.$cari.'%');
                    }
                })
                ->limit(50)
                ->get()
                ->mapWithKeys(fn (Model $m): array => [$m->getKey() => $keLabel($m)])
                ->all())
            ->getOptionLabelUsing(function ($value) use ($model, $keLabel): ?string {
                $baris = $model::query()->find($value);

                return $baris === null ? null : $keLabel($baris);
            })
            ->placeholder('Ketik untuk mencari');
    }
}
