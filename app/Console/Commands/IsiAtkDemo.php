<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Location;
use App\Models\NumberSequence;
use App\Models\SupplyItem;
use App\Models\SupplyTransaction;
use Illuminate\Console\Command;

/**
 * Mengisi barang habis pakai contoh beserta riwayat mutasinya untuk demo.
 *
 * Semua barang yang dibuat perintah ini diberi penanda [DATA DEMO] pada
 * catatannya. Penanda itu yang dipakai --hapus untuk membersihkannya kembali,
 * dan yang membedakannya dari barang yang Anda daftarkan sendiri.
 *
 * Nama barang, harga, dan jumlah di bawah ini karangan untuk keperluan peragaan.
 * Jangan dipakai sebagai dasar keputusan pengadaan.
 */
class IsiAtkDemo extends Command
{
    protected $signature = 'gais:atk-demo
        {--hapus : Hapus kembali seluruh barang habis pakai contoh}';

    protected $description = 'Mengisi barang habis pakai contoh beserta riwayat mutasinya untuk demo';

    public const PENANDA = '[DATA DEMO]';

    /**
     * nama, kategori, satuan, stok minimum, harga satuan, pengali stok akhir.
     *
     * Pengali menentukan posisi stok akhir terhadap stok minimum, supaya ketiga
     * keadaan di layar ada isinya: 0 berarti habis, 1 atau kurang berarti perlu
     * dipesan, lebih dari 1 berarti aman.
     */
    private const BARANG = [
        ['Kertas HVS A4 80 gram', 'kertas', 'rim', 20, 62_000, 2.4],
        ['Kertas HVS F4 70 gram', 'kertas', 'rim', 10, 58_000, 0.8],
        ['Amplop kabinet coklat', 'kertas', 'pak', 10, 34_000, 3.1],
        ['Kertas struk kasir', 'kertas', 'roll', 12, 6_500, 1.0],
        ['Pulpen tinta biru 0,5 mm', 'alat_tulis', 'pcs', 50, 4_200, 3.6],
        ['Pulpen tinta hitam 0,5 mm', 'alat_tulis', 'pcs', 50, 4_200, 0.6],
        ['Pensil kayu 2B', 'alat_tulis', 'pcs', 30, 3_100, 2.2],
        ['Spidol papan tulis hitam', 'alat_tulis', 'pcs', 12, 11_500, 0.0],
        ['Stapler ukuran sedang', 'alat_tulis', 'pcs', 5, 38_000, 2.8],
        ['Isi stapler nomor 10', 'alat_tulis', 'box', 20, 3_800, 1.9],
        ['Map plastik berkancing', 'alat_tulis', 'pcs', 40, 5_400, 2.5],
        ['Ordner arsip folio', 'alat_tulis', 'pcs', 15, 27_000, 1.6],
        ['Lakban bening 2 inci', 'alat_tulis', 'roll', 12, 12_000, 3.0],
        ['Tinta printer hitam', 'tinta', 'botol', 6, 92_000, 2.0],
        ['Tinta printer warna', 'tinta', 'botol', 6, 98_000, 0.7],
        ['Toner printer laser', 'tinta', 'pcs', 2, 780_000, 1.5],
        ['Sabun cuci tangan isi ulang', 'kebersihan', 'botol', 8, 26_000, 2.6],
        ['Tisu gulung', 'kebersihan', 'roll', 24, 8_500, 1.8],
        ['Pembersih lantai', 'kebersihan', 'botol', 6, 21_000, 0.5],
        ['Kantong sampah ukuran besar', 'kebersihan', 'pak', 10, 19_500, 2.3],
        ['Gula pasir kemasan 1 kilogram', 'pantry', 'kg', 5, 16_500, 2.0],
        ['Kopi bubuk kemasan', 'pantry', 'pak', 8, 24_000, 1.4],
        ['Teh celup kotak isi 25', 'pantry', 'box', 6, 12_500, 0.0],
        ['Air minum galon 19 liter', 'pantry', 'pcs', 10, 21_000, 2.7],
        ['Lampu LED 12 watt', 'listrik', 'pcs', 12, 34_000, 3.2],
        ['Baterai AA isi 4', 'listrik', 'pak', 6, 27_500, 1.2],
    ];

    public function handle(): int
    {
        return $this->option('hapus') ? $this->hapus() : $this->isi();
    }

    protected function isi(): int
    {
        $departemen = Department::query()->where('is_active', true)->get();

        if ($departemen->isEmpty()) {
            $this->error('Belum ada departemen aktif.');
            $this->line('Jalankan dulu: php artisan gais:demo-data');

            return self::FAILURE;
        }

        $urutanSiap = NumberSequence::query()
            ->whereIn('code', [SupplyItem::SEQUENCE_CODE, SupplyTransaction::SEQUENCE_CODE])
            ->count() === 2;

        if (! $urutanSiap) {
            $this->error('Urutan nomor untuk barang dan mutasi persediaan belum terdaftar.');
            $this->line('Jalankan dulu: php artisan db:seed --class=NumberSequenceSeeder');

            return self::FAILURE;
        }

        $karyawan = Employee::query()->where('is_active', true)->get();
        $gudang = Location::query()->where('code', 'HO-GDG')->first();

        // Titik awal angka acak dipatok supaya susunan mutasinya mirip tiap kali
        // dijalankan, jadi layar demo tidak berubah ubah tanpa alasan.
        mt_srand(20260907);

        $bar = $this->output->createProgressBar(count(self::BARANG));
        $bar->start();

        $jumlahBarang = 0;
        $jumlahMutasi = 0;

        foreach (self::BARANG as [$nama, $kategori, $satuan, $minimum, $harga, $pengali]) {
            $item = SupplyItem::query()->create([
                'name' => $nama,
                'category' => $kategori,
                'unit' => $satuan,
                'minimum_stock' => $minimum,
                'location_id' => $gudang?->id,
                'is_active' => true,
                'notes' => self::PENANDA.' Barang karangan untuk peragaan, bukan persediaan sungguhan.',
            ]);

            $jumlahBarang++;
            $jumlahMutasi += $this->buatMutasi($item, $minimum, $harga, $pengali, $departemen, $karyawan);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $menipis = SupplyItem::query()->needsRestock()->count();

        $this->info("Data demo ATK dimasukkan: {$jumlahBarang} barang dan {$jumlahMutasi} mutasi.");
        $this->line("Dari jumlah itu, {$menipis} barang berada di bawah atau tepat di batas minimumnya, "
            .'jadi penyaring Perlu dipesan ada isinya saat diperagakan.');
        $this->warn('Semuanya data karangan untuk peragaan, bukan persediaan perusahaan Anda.');
        $this->warn('Bersihkan sebelum data sungguhan masuk: php artisan gais:atk-demo --hapus');

        return self::SUCCESS;
    }

    /**
     * Satu kali barang masuk di awal, lalu beberapa kali barang keluar sampai
     * stoknya mendarat di angka yang diinginkan. Barang masuk selalu dibuat lebih
     * dulu supaya stok tidak pernah sempat minus.
     */
    protected function buatMutasi(SupplyItem $item, int $minimum, int $harga, float $pengali, $departemen, $karyawan): int
    {
        $stokAkhir = (int) round(max($minimum, 4) * $pengali);
        $jumlahKeluar = mt_rand(3, 6);

        // Tiap pengambilan besarnya sekitar seperempat batas minimum, dibulatkan
        // ke atas supaya tidak ada pengambilan bernilai nol.
        $besarSekali = max(1, (int) ceil($minimum / 4));
        $totalKeluar = 0;
        $rencana = [];

        for ($i = 0; $i < $jumlahKeluar; $i++) {
            $jumlah = mt_rand(1, $besarSekali * 2);
            $rencana[] = $jumlah;
            $totalKeluar += $jumlah;
        }

        $stokMasuk = $stokAkhir + $totalKeluar;

        SupplyTransaction::query()->create([
            'supply_item_id' => $item->id,
            'type' => 'masuk',
            'quantity' => $stokMasuk,
            'unit_price' => $harga,
            'transaction_date' => now()->subMonths(4)->addDays(mt_rand(0, 20))->toDateString(),
            'supplier' => 'Toko contoh untuk peragaan',
            'reference' => 'FKT-'.mt_rand(10000, 99999),
            'notes' => self::PENANDA.' Pembelian awal untuk peragaan.',
        ]);

        $dibuat = 1;

        foreach ($rencana as $urut => $jumlah) {
            SupplyTransaction::query()->create([
                'supply_item_id' => $item->id,
                'type' => 'keluar',
                'quantity' => $jumlah,
                'transaction_date' => now()
                    ->subMonths(3)
                    ->addDays($urut * mt_rand(9, 22))
                    ->min(now())
                    ->toDateString(),
                'department_id' => $departemen->random()->id,
                'employee_id' => $karyawan->isNotEmpty() && mt_rand(1, 4) > 1 ? $karyawan->random()->id : null,
                'notes' => self::PENANDA.' Pengambilan untuk peragaan.',
            ]);

            $dibuat++;
        }

        return $dibuat;
    }

    protected function hapus(): int
    {
        $idBarang = SupplyItem::query()
            ->where('notes', 'like', '%'.self::PENANDA.'%')
            ->pluck('id');

        if ($idBarang->isEmpty()) {
            $this->info('Tidak ada barang habis pakai bertanda data demo.');

            return self::SUCCESS;
        }

        $idMutasi = SupplyTransaction::query()->whereIn('supply_item_id', $idBarang)->pluck('id');

        $jumlahJejak = AuditLog::query()
            ->where(function ($query) use ($idBarang, $idMutasi) {
                $query->where(fn ($q) => $q->where('auditable_type', SupplyItem::class)->whereIn('auditable_id', $idBarang))
                    ->orWhere(fn ($q) => $q->where('auditable_type', SupplyTransaction::class)->whereIn('auditable_id', $idMutasi));
            })
            ->delete();

        // Mutasi dihapus massal, tanpa lewat model, supaya penjaga stok minus tidak
        // ikut menilai penghapusan yang memang bermaksud membuang semuanya.
        $jumlahMutasi = SupplyTransaction::query()->whereIn('id', $idMutasi)->delete();
        $jumlahBarang = SupplyItem::query()->whereIn('id', $idBarang)->delete();

        $this->info("Data demo ATK dihapus: {$jumlahBarang} barang, {$jumlahMutasi} mutasi, "
            ."{$jumlahJejak} baris jejak audit.");
        $this->line('Nomor urut kode barang tidak ikut mundur, jadi kode berikutnya melanjutkan dari nomor terakhir.');

        return self::SUCCESS;
    }
}
