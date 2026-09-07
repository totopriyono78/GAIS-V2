<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\DepreciationEntry;
use App\Models\DepreciationPeriod;
use App\Models\Setting;
use App\Support\Periode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Penghubung antara mesin hitung penyusutan dan data aset yang sebenarnya.
 *
 * JadwalPenyusutan hanya tahu aritmetika. Kelas ini yang tahu aset mana yang boleh
 * disusutkan, kebijakan apa yang sedang berlaku, sampai bulan mana yang sudah pernah
 * dicatat, dan apa yang harus terjadi saat sebuah periode ditutup.
 *
 * Tiga aturan yang menentukan bentuk seluruh kelas ini:
 *
 * 1. Periode ditutup berurutan. Tidak ada Agustus yang ditutup setelah September, karena
 *    akumulasi tiap bulan bertumpu pada bulan sebelumnya.
 * 2. Angka yang sudah ditutup dibekukan di tabel entri, bukan dihitung ulang saat dibaca.
 *    Memperbaiki umur ekonomis sebuah aset hari ini tidak boleh mengubah laporan bulan lalu.
 * 3. Bulan yang terlewat tidak hilang, melainkan ikut terhitung sebagai beban susulan pada
 *    penutupan berikutnya, dengan keterangan bulan mana saja yang dicakupnya.
 */
class PenyusutanAset
{
    /** Kebijakan yang sedang berlaku, dibaca sekali per pemakaian. */
    private string $mulai;

    private string $nilaiSisa;

    private bool $habisAkhir;

    private ?string $periodeMulaiSetelan;

    /** @var array<int, DepreciationEntry|null> */
    private array $entriTerakhir = [];

    /** Periode tertutup paling awal, dibaca sekali saja per permintaan. */
    private string|false|null $periodeTerawal = false;

    public function __construct()
    {
        $this->mulai = (string) Setting::get('penyusutan.mulai', 'bulan_perolehan');
        $this->nilaiSisa = (string) Setting::get('penyusutan.nilai_sisa', 'nol');
        $this->habisAkhir = Setting::get('penyusutan.saldo_menurun_akhir', 'habiskan') !== 'sisakan';

        $setelan = Setting::get('penyusutan.periode_mulai');
        $this->periodeMulaiSetelan = Periode::sah($setelan) ? $setelan : null;
    }

    // ---------------------------------------------------------------- parameter aset

    /** Umur ekonomis yang berlaku: milik asetnya sendiri, kalau kosong ikut kategorinya. */
    public function umurBulan(Asset $aset): int
    {
        return (int) ($aset->useful_life_months ?: $aset->category?->useful_life_months ?: 0);
    }

    public function metode(Asset $aset): string
    {
        return (string) ($aset->depreciation_method
            ?: $aset->category?->depreciation_method
            ?: JadwalPenyusutan::TIDAK_DISUSUTKAN);
    }

    /**
     * Nilai sisa yang berlaku. Yang diisi langsung pada asetnya selalu menang, karena
     * itu keputusan yang dibuat orang untuk barang tertentu, bukan bawaan.
     */
    public function nilaiSisa(Asset $aset): float
    {
        if (filled($aset->residual_value)) {
            return (float) $aset->residual_value;
        }

        if ($this->nilaiSisa === 'ikut_kategori') {
            $persen = (float) ($aset->category?->residual_percent ?? 0);

            return round((float) $aset->acquisition_cost * $persen / 100, 2);
        }

        return 0.0;
    }

    /** Bulan pertama yang disusutkan menurut tanggal perolehan dan kebijakan. */
    public function periodeMulaiAset(Asset $aset): ?string
    {
        $periode = Periode::dariTanggal($aset->acquisition_date);

        if ($periode === null) {
            return null;
        }

        return $this->mulai === 'bulan_berikutnya' ? Periode::berikutnya($periode) : $periode;
    }

    /**
     * @return array<int, array{periode: string, urutan: int, beban: float, akumulasi: float, nilai_buku: float}>
     */
    public function jadwal(Asset $aset): array
    {
        $mulai = $this->periodeMulaiAset($aset);

        if ($mulai === null) {
            return [];
        }

        return JadwalPenyusutan::susun(
            (float) $aset->acquisition_cost,
            $this->nilaiSisa($aset),
            $this->umurBulan($aset),
            $this->metode($aset),
            $mulai,
            $this->habisAkhir,
        );
    }

    // ---------------------------------------------------------------- kelayakan

    /**
     * Alasan sebuah aset tidak disusutkan, atau null kalau aset itu memang disusutkan.
     * Dikembalikan sebagai kalimat, bukan sebagai benar salah, supaya layar bisa
     * menjelaskan kenapa sebuah aset tidak muncul di daftar tanpa orang harus menebak.
     */
    public function alasanTidakDisusutkan(Asset $aset): ?string
    {
        if ($aset->ownership_type === 'sewa') {
            return 'Aset sewaan, bukan milik perusahaan';
        }

        if ($this->metode($aset) === JadwalPenyusutan::TIDAK_DISUSUTKAN) {
            return 'Kategorinya ditandai tidak disusutkan';
        }

        if ($this->umurBulan($aset) <= 0) {
            return 'Umur ekonomisnya belum diisi';
        }

        if ((float) $aset->acquisition_cost <= 0) {
            return 'Nilai perolehannya belum diisi';
        }

        if ($this->periodeMulaiAset($aset) === null) {
            return 'Tanggal perolehannya belum diisi';
        }

        return null;
    }

    public function bisaDisusutkan(Asset $aset): bool
    {
        return $this->alasanTidakDisusutkan($aset) === null;
    }

    // ---------------------------------------------------------------- periode

    /**
     * Periode pertama yang dihitung aplikasi ini. Diambil dari pengaturan; kalau belum
     * diisi, dipakai periode paling awal yang sudah pernah ditutup, dan kalau belum ada
     * penutupan sama sekali, periode yang sedang akan ditutup.
     */
    public function periodeMulai(?string $calon = null): string
    {
        if ($this->periodeMulaiSetelan !== null) {
            return $this->periodeMulaiSetelan;
        }

        // Pratinjau memanggil ini sekali per aset. Tanpa ingatan, satu penutupan pada
        // seratus lima puluh aset berarti seratus lima puluh kueri yang jawabannya sama.
        if ($this->periodeTerawal === false) {
            $this->periodeTerawal = DepreciationPeriod::query()->min('period');
        }

        return $this->periodeTerawal ?: ($calon ?: Periode::sekarang());
    }

    /**
     * Periode berikutnya yang boleh ditutup, atau null kalau semuanya sudah tertutup
     * sampai bulan berjalan. Bulan yang belum selesai tidak boleh ditutup, karena aset
     * yang dibeli tanggal 28 masih akan masuk ke bulan itu.
     */
    public function periodeBerikutnya(): ?string
    {
        $terakhir = DepreciationPeriod::terakhir();
        $calon = $terakhir ? Periode::berikutnya($terakhir->period) : $this->periodeMulai();

        return $calon <= Periode::sekarang() ? $calon : null;
    }

    // ---------------------------------------------------------------- akumulasi

    /**
     * Akumulasi penyusutan yang sudah ada sebelum aplikasi ini mulai menghitung.
     *
     * Kalau diisi tangan pada asetnya, angka itu yang dipakai apa adanya. Kalau
     * dikosongkan, dihitung dari jadwal sampai bulan sebelum periode mulai. Yang kedua
     * ini yang benar untuk kebanyakan aset, dan artinya tidak ada data yang perlu
     * diketik ulang saat aplikasi ini mulai dipakai.
     */
    public function akumulasiAwal(Asset $aset): float
    {
        if (filled($aset->opening_accumulated_depreciation)) {
            return (float) $aset->opening_accumulated_depreciation;
        }

        $mulaiAset = $this->periodeMulaiAset($aset);

        if ($mulaiAset === null) {
            return 0.0;
        }

        $mulaiSistem = max($mulaiAset, $this->periodeMulai());

        return JadwalPenyusutan::akumulasiSampai($this->jadwal($aset), Periode::sebelumnya($mulaiSistem));
    }

    /**
     * Entri terakhir yang tercatat untuk aset ini, kalau ada.
     *
     * Hasilnya diingat selama satu permintaan. Satu baris di layar daftar menanyakan hal
     * yang sama tiga kali, sekali untuk angkanya, sekali untuk warnanya, sekali untuk
     * keterangannya, dan tanpa ingatan ini satu halaman berubah menjadi ratusan kueri.
     */
    public function entriTerakhir(Asset $aset): ?DepreciationEntry
    {
        $kunci = (int) $aset->getKey();

        if (! array_key_exists($kunci, $this->entriTerakhir)) {
            $this->entriTerakhir[$kunci] = DepreciationEntry::query()
                ->where('asset_id', $kunci)
                ->orderByDesc('period')
                ->first();
        }

        return $this->entriTerakhir[$kunci];
    }

    /** Dipanggil setelah menutup atau membuka periode, karena ingatannya jadi basi. */
    public function lupakanIngatan(): void
    {
        $this->entriTerakhir = [];
        $this->periodeTerawal = false;
    }

    public function akumulasi(Asset $aset): float
    {
        $entri = $this->entriTerakhir($aset);

        return $entri ? (float) $entri->accumulated_after : $this->akumulasiAwal($aset);
    }

    public function nilaiBuku(Asset $aset): float
    {
        return round((float) $aset->acquisition_cost - $this->akumulasi($aset), 2);
    }

    // ---------------------------------------------------------------- hitungan periode

    /**
     * Hitungan satu aset untuk satu periode, tanpa menyimpan apa pun.
     *
     * @return array{beban: float, akumulasi: float, nilai_buku: float, bulan: int, dari: string}|null
     */
    public function hitungAset(Asset $aset, string $periode): ?array
    {
        if (! $this->bisaDisusutkan($aset) || $this->sudahDilepasSebelum($aset, $periode)) {
            return null;
        }

        $entri = $this->entriTerakhir($aset);

        if ($entri && $entri->period >= $periode) {
            return null;
        }

        $mulaiAset = $this->periodeMulaiAset($aset);
        $dari = $entri
            ? Periode::berikutnya($entri->period)
            : max($mulaiAset, $this->periodeMulai($periode));

        if ($dari > $periode) {
            return null;
        }

        $jadwal = $this->jadwal($aset);
        $beban = 0.0;
        $bulan = 0;

        foreach ($jadwal as $baris) {
            if ($baris['periode'] < $dari) {
                continue;
            }

            if ($baris['periode'] > $periode) {
                break;
            }

            $beban = round($beban + $baris['beban'], 2);
            $bulan++;
        }

        if ($bulan === 0 || $beban <= 0) {
            return null;
        }

        $akumulasiSebelum = $entri ? (float) $entri->accumulated_after : $this->akumulasiAwal($aset);
        $akumulasi = round($akumulasiSebelum + $beban, 2);

        return [
            'beban' => $beban,
            'akumulasi' => $akumulasi,
            'nilai_buku' => round((float) $aset->acquisition_cost - $akumulasi, 2),
            'bulan' => $bulan,
            'dari' => $dari,
        ];
    }

    /**
     * Aset yang dokumen pelepasannya bertanggal sebelum bulan ini sudah bukan milik
     * perusahaan lagi, jadi tidak ikut disusutkan. Bulan pelepasannya sendiri masih
     * ikut, karena barangnya masih dipakai sebagian bulan itu.
     */
    private function sudahDilepasSebelum(Asset $aset, string $periode): bool
    {
        $tanggal = $aset->disposal?->disposal_date;

        if (blank($tanggal)) {
            return false;
        }

        return (string) Periode::dariTanggal($tanggal) < $periode;
    }

    /**
     * Pratinjau seluruh aset untuk satu periode. Tidak menyimpan apa pun, jadi aman
     * dibuka berkali kali sebelum orang memutuskan menutup periodenya.
     *
     * @return array<int, array{aset: Asset, beban: float, akumulasi: float, nilai_buku: float, bulan: int, dari: string}>
     */
    public function pratinjau(string $periode): array
    {
        $hasil = [];

        Asset::query()
            ->with(['category', 'disposal'])
            ->orderBy('code')
            ->chunk(200, function ($aset) use ($periode, &$hasil) {
                foreach ($aset as $satu) {
                    $hitung = $this->hitungAset($satu, $periode);

                    if ($hitung === null) {
                        continue;
                    }

                    $hasil[] = ['aset' => $satu] + $hitung;
                }
            });

        return $hasil;
    }

    /**
     * @return array{total: float, jumlah_aset: int, jumlah_susulan: int}
     */
    public function ringkasanPratinjau(string $periode): array
    {
        $baris = $this->pratinjau($periode);

        return [
            'total' => round(array_sum(array_column($baris, 'beban')), 2),
            'jumlah_aset' => count($baris),
            'jumlah_susulan' => count(array_filter($baris, fn (array $b): bool => $b['bulan'] > 1)),
        ];
    }

    // ---------------------------------------------------------------- tutup dan buka

    /**
     * Menutup satu periode. Seluruh entri dan barisnya dibuat di dalam satu transaksi,
     * jadi tidak mungkin ada periode yang tercatat tertutup tetapi entrinya separuh.
     */
    public function tutup(string $periode, ?string $catatan = null): DepreciationPeriod
    {
        $baris = $this->pratinjau($periode);

        $this->lupakanIngatan();

        return DB::transaction(function () use ($periode, $catatan, $baris): DepreciationPeriod {
            $tutup = DepreciationPeriod::query()->create([
                'period' => $periode,
                'closed_at' => now(),
                'closed_by_user_id' => Auth::id(),
                'total_expense' => round(array_sum(array_column($baris, 'beban')), 2),
                'asset_count' => count($baris),
                'catch_up_count' => count(array_filter($baris, fn (array $b): bool => $b['bulan'] > 1)),
                'notes' => $catatan,
            ]);

            foreach ($baris as $satu) {
                /** @var Asset $aset */
                $aset = $satu['aset'];

                DepreciationEntry::query()->create([
                    'depreciation_period_id' => $tutup->getKey(),
                    'asset_id' => $aset->getKey(),
                    'period' => $periode,
                    'expense' => $satu['beban'],
                    'accumulated_after' => $satu['akumulasi'],
                    'book_value_after' => $satu['nilai_buku'],
                    'method' => $this->metode($aset),
                    'useful_life_months' => $this->umurBulan($aset),
                    'acquisition_cost' => (float) $aset->acquisition_cost,
                    'residual_value' => $this->nilaiSisa($aset),
                    'months_covered' => $satu['bulan'],
                    'covers_from' => $satu['bulan'] > 1 ? $satu['dari'] : null,
                ]);
            }

            return $tutup;
        });
    }

    /**
     * Membuka kembali periode terakhir. Entrinya ikut terhapus lewat cascade, dan
     * penghapusan barisnya tercatat di jejak audit lewat trait Auditable.
     */
    public function bukaKembali(DepreciationPeriod $periode): bool
    {
        if (! $periode->canBeReopened()) {
            return false;
        }

        $terhapus = (bool) $periode->delete();

        $this->lupakanIngatan();

        return $terhapus;
    }

    // ---------------------------------------------------------------- pelepasan

    /**
     * Laba atau rugi pelepasan: hasil penjualan dikurangi nilai buku pada saat dilepas.
     * Positif berarti laba, negatif berarti rugi.
     *
     * Nilai buku yang dipakai adalah nilai buku setelah penyusutan terakhir yang sudah
     * ditutup. Kalau periode bulan pelepasannya belum ditutup, angka ini masih akan
     * bergerak, dan layar harus mengatakannya.
     */
    public function labaRugiPelepasan(Asset $aset): ?float
    {
        $pelepasan = $aset->disposal;

        if ($pelepasan === null) {
            return null;
        }

        return round((float) $pelepasan->proceeds - $this->nilaiBuku($aset), 2);
    }

    /**
     * Apakah bulan pelepasan aset ini sudah ikut ditutup. Selama belum, laba rugi yang
     * ditampilkan masih bisa berubah.
     */
    public function periodePelepasanSudahDitutup(Asset $aset): bool
    {
        $periode = Periode::dariTanggal($aset->disposal?->disposal_date);

        if ($periode === null) {
            return false;
        }

        return DepreciationPeriod::query()->where('period', '>=', $periode)->exists();
    }
}
