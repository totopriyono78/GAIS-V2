<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\VehicleRefueling;
use App\Models\VehicleTrip;
use Illuminate\Support\Carbon;

/**
 * Satu tempat yang memegang aturan odometer.
 *
 * Tiga layar menulis angka odometer: penutupan perjalanan, pencatatan pengisian BBM,
 * dan pendataan awal kendaraan. Kalau masing masing memvalidasi sendiri, cepat atau
 * lambat ketiganya akan berbeda, dan yang paling longgar yang akan dipakai orang.
 *
 * Aturannya satu kalimat: odometer tidak pernah mundur. Angka yang lebih kecil dari
 * yang terakhir tercatat hampir selalu salah ketik, dan membiarkannya masuk membuat
 * seluruh perhitungan jarak dan konsumsi bahan bakar sesudahnya ikut salah tanpa ada
 * yang menyadarinya.
 */
class OdometerKendaraan
{
    /**
     * Angka odometer tertinggi yang pernah tercatat untuk kendaraan ini, dari mana pun
     * asalnya: pendataan awal, penutupan perjalanan, atau pengisian BBM.
     *
     * Diambil dari ketiganya, bukan hanya dari kolom di tabel kendaraan, supaya baris
     * yang terlanjur masuk lewat satu jalur tidak bisa dilangkahi lewat jalur lain.
     */
    public function tertinggi(Vehicle $kendaraan): ?int
    {
        $angka = array_filter([
            $kendaraan->last_odometer_km,
            VehicleTrip::query()->where('vehicle_id', $kendaraan->getKey())->max('end_odometer_km'),
            VehicleTrip::query()->where('vehicle_id', $kendaraan->getKey())->max('start_odometer_km'),
            VehicleRefueling::query()->where('vehicle_id', $kendaraan->getKey())->max('odometer_km'),
        ], fn ($nilai) => filled($nilai));

        return $angka === [] ? null : (int) max($angka);
    }

    /**
     * Kalimat penolakan kalau angka ini mundur, atau null kalau angkanya masuk akal.
     *
     * Mengembalikan kalimat, bukan boolean, karena pesan yang berguna harus menyebut
     * angka pembandingnya. "Odometer tidak boleh mundur" tanpa menyebut angka terakhir
     * hanya membuat orang menebak nebak.
     */
    public function alasanDitolak(Vehicle $kendaraan, ?int $angka): ?string
    {
        if (blank($angka)) {
            return null;
        }

        $tertinggi = $this->tertinggi($kendaraan);

        if ($tertinggi === null || $angka >= $tertinggi) {
            return null;
        }

        return 'Odometer terakhir yang tercatat '.number_format($tertinggi, 0, ',', '.')
            .' km, jadi angka yang lebih kecil dari itu tidak bisa disimpan.';
    }

    /**
     * Pemeriksaan untuk catatan yang punya tanggalnya sendiri, seperti pengisian BBM.
     *
     * Aturan "tidak boleh lebih kecil dari yang tertinggi" benar untuk penutupan
     * perjalanan, yang selalu terjadi sekarang, tetapi salah untuk pengisian BBM.
     * Saat pertama memakai aplikasi ini, orang akan memasukkan struk struk lama, dan
     * struk bulan Juli memang odometernya lebih kecil daripada perjalanan minggu lalu.
     * Menolaknya berarti memaksa orang membuang riwayat yang justru paling dibutuhkan
     * untuk menghitung konsumsi.
     *
     * Yang diperiksa di sini adalah urutannya: angka ini tidak boleh lebih kecil
     * daripada catatan sebelum tanggalnya, dan tidak boleh lebih besar daripada catatan
     * sesudah tanggalnya. Salah ketik tetap tertangkap, riwayat lama tetap bisa masuk.
     */
    public function alasanDitolakPadaTanggal(Vehicle $kendaraan, ?int $angka, ?Carbon $tanggal, ?int $abaikanId = null): ?string
    {
        if (blank($angka) || blank($tanggal)) {
            return null;
        }

        $sebelum = VehicleRefueling::query()
            ->where('vehicle_id', $kendaraan->getKey())
            ->when($abaikanId, fn ($q) => $q->whereKeyNot($abaikanId))
            ->whereDate('filled_at', '<=', $tanggal)
            ->max('odometer_km');

        $perjalananSebelum = VehicleTrip::query()
            ->where('vehicle_id', $kendaraan->getKey())
            ->whereNotNull('end_odometer_km')
            ->whereDate('departed_at', '<=', $tanggal)
            ->max('end_odometer_km');

        $batasBawah = max((int) $sebelum, (int) $perjalananSebelum);

        if ($batasBawah > 0 && $angka < $batasBawah) {
            return 'Sampai '.$tanggal->translatedFormat('d F Y').' odometer sudah tercatat '
                .number_format($batasBawah, 0, ',', '.').' km, jadi angka yang lebih kecil tidak masuk akal.';
        }

        $sesudah = VehicleRefueling::query()
            ->where('vehicle_id', $kendaraan->getKey())
            ->when($abaikanId, fn ($q) => $q->whereKeyNot($abaikanId))
            ->whereDate('filled_at', '>', $tanggal)
            ->min('odometer_km');

        if (filled($sesudah) && $angka > (int) $sesudah) {
            return 'Ada pengisian setelah tanggal ini dengan odometer '
                .number_format((int) $sesudah, 0, ',', '.').' km, jadi angka yang lebih besar tidak masuk akal.';
        }

        return null;
    }

    /**
     * Memajukan odometer kendaraan setelah satu perjalanan atau pengisian dicatat.
     *
     * Kolom `last_odometer_km` memang turunan, dan disimpan dengan alasan yang ditulis
     * di migrasinya. Yang menjaganya tetap benar adalah metode ini, bukan ingatan orang.
     */
    public function majukan(Vehicle $kendaraan, ?int $angka, ?Carbon $tanggal = null): void
    {
        if (blank($angka)) {
            return;
        }

        if (filled($kendaraan->last_odometer_km) && $angka <= $kendaraan->last_odometer_km) {
            return;
        }

        $kendaraan->forceFill([
            'last_odometer_km' => $angka,
            'last_odometer_date' => ($tanggal ?? now())->toDateString(),
        ])->save();
    }
}
