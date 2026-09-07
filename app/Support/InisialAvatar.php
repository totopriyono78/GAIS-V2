<?php

namespace App\Support;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Avatar berupa inisial, digambar sendiri di aplikasi ini.
 *
 * Filament bawaannya memanggil ui-avatars.com untuk menggambar lingkaran berisi inisial.
 * Artinya nama setiap pengguna yang membuka aplikasi dikirim ke layanan pihak ketiga,
 * dan di jaringan kantor tanpa akses internet keluar avatarnya tidak muncul sama sekali.
 * Dua duanya tidak sepadan untuk gambar sesederhana dua huruf.
 *
 * Gambarnya SVG yang ditempel langsung sebagai data URI: tidak ada permintaan jaringan,
 * tidak ada berkas yang perlu ditulis ke penyimpanan, dan tidak ada nama karyawan yang
 * meninggalkan server perusahaan.
 */
class InisialAvatar implements AvatarProvider
{
    /**
     * Palet latar. Warna dipilih dari nama, bukan diacak, supaya orang yang sama selalu
     * mendapat warna yang sama di layar mana pun dan bisa dikenali sekilas.
     *
     * Semuanya cukup gelap untuk menampung teks putih dengan rasio kontras di atas 4,5.
     */
    private const COLORS = [
        '#17505E', // hijau kebiruan, warna utama aplikasi
        '#7A3B2E', // bata
        '#3F4A7A', // nila
        '#5B4636', // cokelat
        '#2F5D3A', // hijau hutan
        '#6B3560', // ungu anggur
        '#1F4F70', // biru laut
        '#6A4E12', // kuning tua
    ];

    public function get(Model|Authenticatable $record): string
    {
        $nama = trim((string) Filament::getNameForDefaultAvatar($record));

        $inisial = $this->inisial($nama);
        $latar = self::COLORS[$this->indeksWarna($nama)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">'
            .'<rect width="100" height="100" fill="'.$latar.'"/>'
            .'<text x="50" y="50" fill="#FFFFFF" font-family="IBM Plex Sans, Segoe UI, sans-serif"'
            .' font-size="42" font-weight="600" text-anchor="middle" dominant-baseline="central">'
            .htmlspecialchars($inisial, ENT_QUOTES | ENT_XML1, 'UTF-8')
            .'</text></svg>';

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }

    /**
     * Huruf pertama dari kata pertama dan kata terakhir. Nama Indonesia sering terdiri
     * dari tiga kata atau lebih, dan tiga huruf tidak muat di lingkaran sekecil ini.
     *
     * Tanda baca di awal kata dilewati, supaya nama akun layanan seperti "[SISTEM] Admin"
     * tidak berinisial kurung siku.
     */
    private function inisial(string $nama): string
    {
        $kata = [];

        foreach (preg_split('/\s+/u', $nama, -1, PREG_SPLIT_NO_EMPTY) ?: [] as $bagian) {
            $bersih = preg_replace('/^[^\p{L}\p{N}]+/u', '', $bagian);

            if (filled($bersih)) {
                $kata[] = $bersih;
            }
        }

        if ($kata === []) {
            return '?';
        }

        $depan = mb_substr($kata[0], 0, 1);

        if (count($kata) === 1) {
            return mb_strtoupper($depan);
        }

        return mb_strtoupper($depan.mb_substr($kata[count($kata) - 1], 0, 1));
    }

    private function indeksWarna(string $nama): int
    {
        if ($nama === '') {
            return 0;
        }

        return abs(crc32($nama)) % count(self::COLORS);
    }
}
