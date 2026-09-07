<?php

namespace Tests\Unit;

use App\Services\JadwalPenyusutan;
use App\Support\Periode;
use PHPUnit\Framework\TestCase;

/**
 * Angka penyusutan adalah angka yang akan dibaca orang pajak dan auditor, jadi rumusnya
 * diuji terhadap hitungan tangan, bukan terhadap dirinya sendiri. Setiap angka harapan
 * di berkas ini dihitung manual lebih dulu, dan disebutkan cara mendapatkannya di nama
 * pengujiannya.
 *
 * Berkas ini tidak menyentuh basis data sama sekali, jadi bisa dijalankan cepat dan
 * sesering mungkin.
 */
class JadwalPenyusutanTest extends TestCase
{
    private function rupiah(float $nilai): string
    {
        return number_format($nilai, 2);
    }

    /** @param array<int, array<string, mixed>> $jadwal */
    private function totalBeban(array $jadwal): float
    {
        return round(array_sum(array_column($jadwal, 'beban')), 2);
    }

    public function test_garis_lurus_membagi_rata_sepanjang_umurnya(): void
    {
        // 12.000.000 dibagi 48 bulan = 250.000 sebulan.
        $jadwal = JadwalPenyusutan::susun(12000000, 0, 48, 'garis_lurus', '2026-01');

        $this->assertCount(48, $jadwal);
        $this->assertSame('250,000.00', $this->rupiah($jadwal[0]['beban']));
        $this->assertSame('2026-01', $jadwal[0]['periode']);
        $this->assertSame('2029-12', $jadwal[47]['periode']);
        $this->assertSame('12,000,000.00', $this->rupiah($jadwal[47]['akumulasi']));
        $this->assertSame('0.00', $this->rupiah($jadwal[47]['nilai_buku']));
    }

    public function test_garis_lurus_berhenti_di_nilai_sisa(): void
    {
        // Dasar penyusutan 12.000.000 dikurangi 1.200.000 = 10.800.000, dibagi 48 = 225.000.
        $jadwal = JadwalPenyusutan::susun(12000000, 1200000, 48, 'garis_lurus', '2026-01');

        $this->assertSame('225,000.00', $this->rupiah($jadwal[0]['beban']));
        $this->assertSame('1,200,000.00', $this->rupiah($jadwal[47]['nilai_buku']));
    }

    public function test_saldo_menurun_ganda_mengikuti_tarif_dua_kali_garis_lurus(): void
    {
        /*
         * Umur 4 tahun, tarif garis lurus 25 persen, tarif saldo menurun ganda 50 persen.
         * Tahun 1: 50 persen dari 12.000.000 = 6.000.000, sebulan 500.000.
         * Tahun 2: 50 persen dari sisa 6.000.000 = 3.000.000, sebulan 250.000.
         * Tahun 3: 50 persen dari sisa 3.000.000 = 1.500.000, sebulan 125.000.
         * Tahun 4 adalah tahun terakhir, sisanya 1.500.000 dihabiskan, sebulan 125.000.
         */
        $jadwal = JadwalPenyusutan::susun(12000000, 0, 48, 'saldo_menurun', '2026-01');

        $this->assertCount(48, $jadwal);
        $this->assertSame('500,000.00', $this->rupiah($jadwal[0]['beban']));
        $this->assertSame('6,000,000.00', $this->rupiah($jadwal[11]['akumulasi']));
        $this->assertSame('250,000.00', $this->rupiah($jadwal[12]['beban']));
        $this->assertSame('9,000,000.00', $this->rupiah($jadwal[23]['akumulasi']));
        $this->assertSame('125,000.00', $this->rupiah($jadwal[24]['beban']));
        $this->assertSame('10,500,000.00', $this->rupiah($jadwal[35]['akumulasi']));
        $this->assertSame('125,000.00', $this->rupiah($jadwal[36]['beban']));
        $this->assertSame('0.00', $this->rupiah($jadwal[47]['nilai_buku']));
        $this->assertSame(12000000.0, $this->totalBeban($jadwal));
    }

    public function test_saldo_menurun_murni_memang_menyisakan_nilai_buku(): void
    {
        /*
         * Tanpa aturan tahun terakhir, tiap tahun tinggal separuhnya:
         * 12.000.000 menjadi 6, 3, 1,5, lalu 750.000 di akhir tahun keempat.
         * Ini bukan kebocoran pembulatan, melainkan sifat metodenya.
         */
        $jadwal = JadwalPenyusutan::susun(12000000, 0, 48, 'saldo_menurun', '2026-01', habisAkhir: false);

        $this->assertSame('750,000.00', $this->rupiah($jadwal[47]['nilai_buku']));
    }

    public function test_bangunan_permanen_dua_puluh_tahun(): void
    {
        // 24.500.000.000 dibagi 240 bulan = 102.083.333,333..., dibulatkan 102.083.333,33.
        $jadwal = JadwalPenyusutan::susun(24500000000, 0, 240, 'garis_lurus', '2026-01');

        $this->assertCount(240, $jadwal);
        $this->assertSame('102,083,333.33', $this->rupiah($jadwal[0]['beban']));
        $this->assertSame(24500000000.0, $this->totalBeban($jadwal));
    }

    public function test_bulan_terakhir_menyerap_recehan_pembulatan(): void
    {
        /*
         * 239 bulan pertama membulatkan ke bawah, jadi tersisa 80 sen. Kalau bulan
         * terakhir ikut memakai angka bulanan biasa, delapan puluh sen itu akan
         * menempel di nilai buku selamanya dan aset yang sudah habis umurnya tidak
         * pernah benar benar bernilai nol.
         */
        $jadwal = JadwalPenyusutan::susun(24500000000, 0, 240, 'garis_lurus', '2026-01');

        $this->assertSame('102,083,334.13', $this->rupiah($jadwal[239]['beban']));
        $this->assertSame('0.00', $this->rupiah($jadwal[239]['nilai_buku']));
    }

    public function test_umur_yang_bukan_kelipatan_dua_belas(): void
    {
        $jadwal = JadwalPenyusutan::susun(9000000, 0, 30, 'saldo_menurun', '2026-01');

        $this->assertCount(30, $jadwal);
        $this->assertSame('0.00', $this->rupiah($jadwal[29]['nilai_buku']));
        $this->assertSame(9000000.0, $this->totalBeban($jadwal));
    }

    public function test_akumulasi_tidak_pernah_melewati_dasar_penyusutan(): void
    {
        // 2.694.000 dikurangi nilai sisa 269.400 = 2.424.600, dan tidak boleh lebih.
        $jadwal = JadwalPenyusutan::susun(2694000, 269400, 96, 'saldo_menurun', '2025-03');

        $this->assertSame('2,424,600.00', $this->rupiah($jadwal[95]['akumulasi']));
    }

    public function test_tidak_ada_recehan_tersisa_pada_ratusan_kombinasi(): void
    {
        $bocor = [];

        foreach ([48, 96, 192, 240, 120, 30, 18, 7] as $umur) {
            foreach (['garis_lurus', 'saldo_menurun'] as $metode) {
                foreach ([0, 0.05, 0.1] as $persenSisa) {
                    foreach ([1140000, 12000000, 24500000000, 2694000, 999999] as $harga) {
                        $sisa = round($harga * $persenSisa, 2);
                        $jadwal = JadwalPenyusutan::susun($harga, $sisa, $umur, $metode, '2020-01');
                        $akhir = end($jadwal);

                        if ($akhir === false) {
                            continue;
                        }

                        if (abs($akhir['nilai_buku'] - $sisa) > 0.005) {
                            $bocor[] = "{$metode} {$umur} bulan {$harga} sisa {$sisa}";
                        }
                    }
                }
            }
        }

        $this->assertSame([], $bocor);
    }

    public function test_aset_yang_tidak_disusutkan_tidak_menghasilkan_jadwal(): void
    {
        $this->assertSame([], JadwalPenyusutan::susun(12000000000, 0, 0, 'tidak_disusutkan', '2026-01'));
        $this->assertSame([], JadwalPenyusutan::susun(12000000000, 0, 240, 'tidak_disusutkan', '2026-01'));
        $this->assertSame([], JadwalPenyusutan::susun(12000000, 0, 0, 'garis_lurus', '2026-01'));
    }

    public function test_nilai_sisa_sebesar_perolehan_tidak_menghasilkan_jadwal(): void
    {
        // Tidak ada yang tersisa untuk disusutkan, jadi jangan membuat baris berisi nol.
        $this->assertSame([], JadwalPenyusutan::susun(5000000, 5000000, 48, 'garis_lurus', '2026-01'));
    }

    public function test_periode_mulai_yang_tidak_sah_ditolak(): void
    {
        $this->assertSame([], JadwalPenyusutan::susun(12000000, 0, 48, 'garis_lurus', '2026-13'));
        $this->assertSame([], JadwalPenyusutan::susun(12000000, 0, 48, 'garis_lurus', 'Januari'));
    }

    public function test_mengambil_satu_periode_dari_jadwal(): void
    {
        $jadwal = JadwalPenyusutan::susun(12000000, 0, 48, 'garis_lurus', '2026-01');

        $this->assertSame('250,000.00', $this->rupiah(JadwalPenyusutan::pada($jadwal, '2027-06')['beban']));
        $this->assertSame(17, JadwalPenyusutan::pada($jadwal, '2027-06')['urutan']);
        $this->assertNull(JadwalPenyusutan::pada($jadwal, '2025-12'));
        $this->assertNull(JadwalPenyusutan::pada($jadwal, '2030-01'));
    }

    public function test_akumulasi_sampai_periode_tertentu(): void
    {
        $jadwal = JadwalPenyusutan::susun(12000000, 0, 48, 'garis_lurus', '2026-01');

        // Enam bulan pertama: 6 kali 250.000.
        $this->assertSame('1,500,000.00', $this->rupiah(JadwalPenyusutan::akumulasiSampai($jadwal, '2026-06')));
        // Sebelum jadwal dimulai, belum ada yang tersusutkan.
        $this->assertSame('0.00', $this->rupiah(JadwalPenyusutan::akumulasiSampai($jadwal, '2025-12')));
        // Setelah umurnya habis, akumulasi berhenti di angka terakhir.
        $this->assertSame('12,000,000.00', $this->rupiah(JadwalPenyusutan::akumulasiSampai($jadwal, '2099-01')));
    }

    public function test_hitungan_periode(): void
    {
        $this->assertSame('2027-02', Periode::tambah('2026-11', 3));
        $this->assertSame('2026-01', Periode::tambah('2026-01', 0));
        $this->assertSame('2025-12', Periode::sebelumnya('2026-01'));
        $this->assertSame('2026-01', Periode::berikutnya('2025-12'));
        $this->assertSame(11, Periode::selisih('2026-01', '2026-12'));
        $this->assertSame(100, Periode::selisih('2018-05', '2026-09'));
        $this->assertSame(-1, Periode::selisih('2026-02', '2026-01'));
    }

    public function test_periode_yang_sah(): void
    {
        $this->assertTrue(Periode::sah('2026-01'));
        $this->assertTrue(Periode::sah('2026-12'));
        $this->assertFalse(Periode::sah('2026-13'));
        $this->assertFalse(Periode::sah('2026-00'));
        $this->assertFalse(Periode::sah('2026-1'));
        $this->assertFalse(Periode::sah(null));
        $this->assertFalse(Periode::sah(''));
    }

    public function test_label_periode_dalam_bahasa_indonesia(): void
    {
        $this->assertSame('September 2026', Periode::label('2026-09'));
        $this->assertSame('Januari 2027', Periode::label('2027-01'));
        $this->assertSame('Belum ditentukan', Periode::label(null));
    }
}
