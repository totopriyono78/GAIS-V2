<?php

namespace App\Services;

use App\Models\BusinessTripExpense;
use App\Models\ExpenseCategory;
use App\Models\MaintenanceVisit;
use App\Models\ParcelShipment;
use App\Models\Reimbursement;
use App\Models\ReimbursementLine;
use App\Models\SupplyTransaction;
use App\Models\VehicleDocument;
use App\Models\VehicleRefueling;
use App\Models\VendorBill;
use App\Models\VendorBillLine;
use App\Models\WorkOrder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Menjumlahkan realisasi biaya dari catatan yang sudah ada.
 *
 * Ini kelas yang paling menentukan apakah modul anggaran berguna atau hanya jadi
 * spreadsheet kedua. Kalau realisasi diketik ulang orang, angkanya akan selalu tertinggal
 * dari kenyataan dan pelan pelan berhenti dipercaya. Karena itu tidak ada satu pun angka
 * di sini yang berasal dari pengetikan: semuanya dijumlahkan dari perintah kerja yang
 * ditutup, pengisian BBM yang dicatat, pajak yang diperpanjang, barang yang keluar,
 * tagihan rekanan, dan struk karyawan yang sudah disetujui.
 *
 * Pembebanan ke departemen mengikuti jejak yang sudah ada, dan jejak itu tidak selalu
 * lengkap. Biaya pemeliharaan menempel pada aset, dan aset yang belum diisi departemennya
 * menghasilkan biaya yang tidak bisa dibebankan ke siapa pun. Angka itu tidak dibuang dan
 * tidak dibagi rata, melainkan dilaporkan terpisah lewat belumTerbebankan(), karena biaya
 * yang hilang diam diam adalah cara tercepat membuat laporan anggaran meleset tanpa ada
 * yang tahu sebabnya.
 */
class RealisasiBiaya
{
    /**
     * Sumber realisasi yang dikenal, beserta kalimat penjelasnya untuk layar.
     *
     * @var array<string, string>
     */
    public const SOURCES = [
        'pemeliharaan' => 'Perintah kerja dan kunjungan pemeliharaan yang sudah selesai',
        'bbm' => 'Pengisian BBM kendaraan',
        'dokumen_kendaraan' => 'Pajak, perpanjangan STNK, KIR, dan asuransi kendaraan',
        'persediaan' => 'Barang habis pakai yang keluar dari gudang',
        'tagihan' => 'Tagihan rekanan dan penggantian biaya karyawan yang sudah disetujui',
        'kiriman' => 'Biaya pengiriman paket yang sudah berangkat',
        'perjalanan_dinas' => 'Biaya perjalanan dinas yang pertanggungjawabannya sudah ditutup',
    ];

    /**
     * Hasil per pasangan kategori dan tahun, diingat selama satu permintaan.
     *
     * Tanpa ini, satu halaman anggaran berisi tiga puluh baris akan menjalankan tiga
     * puluh kali penjumlahan yang isinya sama persis, karena tiap baris hanya berbeda
     * departemennya. Kelas ini didaftarkan sebagai singleton supaya ingatannya berlaku
     * untuk seluruh halaman, bukan untuk satu baris saja.
     *
     * @var array<string, array<int, float>>
     */
    protected array $ingatan = [];

    /** @var array<string, float> */
    protected array $ingatanTanpaDepartemen = [];

    /** @var array<string, array<int, float>> */
    protected array $ingatanTertunda = [];

    public function lupakanIngatan(): void
    {
        $this->ingatan = [];
        $this->ingatanTanpaDepartemen = [];
        $this->ingatanTertunda = [];
    }

    /**
     * Kunci ingatan satu kategori.
     *
     * Empat sumber pertama tidak peduli kategorinya yang mana: seluruh kategori bersumber
     * BBM menjumlahkan pengisian yang sama persis, jadi cukup disimpan satu kali per
     * sumber. Sumber tagihan berbeda, karena tiap kategori punya baris fakturnya sendiri,
     * dan menyamakan ingatannya akan membuat biaya listrik muncul sebagai biaya sewa.
     */
    protected function kunci(ExpenseCategory $kategori, int $tahun): string
    {
        return $kategori->source === 'tagihan'
            ? 'tagihan:'.$kategori->getKey().':'.$tahun
            : $kategori->source.':'.$tahun;
    }

    /**
     * Realisasi satu kategori pada satu departemen sepanjang satu tahun.
     */
    public function untuk(ExpenseCategory $kategori, int $departmentId, int $tahun): float
    {
        return $this->perDepartemen($kategori, $tahun)[$departmentId] ?? 0.0;
    }

    /**
     * Realisasi satu kategori sepanjang satu tahun, dikelompokkan menurut departemen.
     *
     * @return array<int, float> berkunci department_id
     */
    public function perDepartemen(ExpenseCategory $kategori, int $tahun): array
    {
        $kunci = $this->kunci($kategori, $tahun);

        if (array_key_exists($kunci, $this->ingatan)) {
            return $this->ingatan[$kunci];
        }

        [$dari, $sampai] = $this->rentang($tahun);

        return $this->ingatan[$kunci] = match ($kategori->source) {
            'pemeliharaan' => $this->pemeliharaan($dari, $sampai),
            'bbm' => $this->bbm($dari, $sampai),
            'dokumen_kendaraan' => $this->dokumenKendaraan($dari, $sampai),
            'persediaan' => $this->persediaan($dari, $sampai),
            'tagihan' => $this->tagihan($kategori, $dari, $sampai),
            'kiriman' => $this->kiriman($dari, $sampai),
            'perjalanan_dinas' => $this->perjalananDinas($dari, $sampai),
            default => [],
        };
    }

    /**
     * Nilai yang sudah pasti keluar tetapi belum disetujui, dikelompokkan per departemen.
     *
     * Angka ini bukan realisasi dan tidak pernah ikut dijumlahkan ke dalamnya. Ia ada
     * karena tumpukan faktur yang belum disetujui adalah cara termudah membuat pagu
     * terlihat sehat padahal uangnya sudah habis, dan orang yang membaca layar anggaran
     * berhak tahu ada berapa yang sedang menunggu di belakangnya.
     *
     * @return array<int, float> berkunci department_id
     */
    public function tertundaPerDepartemen(ExpenseCategory $kategori, int $tahun): array
    {
        if ($kategori->source !== 'tagihan') {
            return [];
        }

        $kunci = $this->kunci($kategori, $tahun);

        if (array_key_exists($kunci, $this->ingatanTertunda)) {
            return $this->ingatanTertunda[$kunci];
        }

        [$dari, $sampai] = $this->rentang($tahun);

        return $this->ingatanTertunda[$kunci] = $this->tagihan($kategori, $dari, $sampai, belumDisetujui: true);
    }

    public function tertunda(ExpenseCategory $kategori, int $departmentId, int $tahun): float
    {
        return $this->tertundaPerDepartemen($kategori, $tahun)[$departmentId] ?? 0.0;
    }

    /**
     * Seluruh nilai yang belum disetujui pada satu kategori, termasuk baris yang tidak
     * menyebut departemen.
     *
     * Dipakai subjudul halaman anggaran. Menjumlahkannya dari baris pagu yang kebetulan
     * ada akan menyembunyikan tagihan yang jatuh ke departemen yang belum diberi pagu,
     * dan itu justru departemen yang paling perlu diketahui sedang membelanjakan sesuatu.
     */
    public function tertundaSeluruhnya(ExpenseCategory $kategori, int $tahun): float
    {
        if ($kategori->source !== 'tagihan') {
            return 0.0;
        }

        [$dari, $sampai] = $this->rentang($tahun);

        return round(
            array_sum($this->tertundaPerDepartemen($kategori, $tahun))
            + (float) $this->tagihan($kategori, $dari, $sampai, tanpaDepartemen: true, belumDisetujui: true),
            2,
        );
    }

    /**
     * Biaya yang terjaring sumbernya tetapi tidak bisa dibebankan ke departemen mana pun,
     * hampir selalu karena asetnya belum punya departemen.
     */
    public function belumTerbebankan(ExpenseCategory $kategori, int $tahun): float
    {
        $kunci = $this->kunci($kategori, $tahun);

        if (array_key_exists($kunci, $this->ingatanTanpaDepartemen)) {
            return $this->ingatanTanpaDepartemen[$kunci];
        }

        [$dari, $sampai] = $this->rentang($tahun);

        return $this->ingatanTanpaDepartemen[$kunci] = match ($kategori->source) {
            'pemeliharaan' => $this->pemeliharaan($dari, $sampai, tanpaDepartemen: true),
            'bbm' => $this->bbm($dari, $sampai, tanpaDepartemen: true),
            'kiriman' => $this->kiriman($dari, $sampai, tanpaDepartemen: true),
            'perjalanan_dinas' => $this->perjalananDinas($dari, $sampai, tanpaDepartemen: true),
            'dokumen_kendaraan' => $this->dokumenKendaraan($dari, $sampai, tanpaDepartemen: true),
            'persediaan' => $this->persediaan($dari, $sampai, tanpaDepartemen: true),
            'tagihan' => $this->tagihan($kategori, $dari, $sampai, tanpaDepartemen: true),
            default => 0.0,
        };
    }

    /** @return array{0: string, 1: string} */
    protected function rentang(int $tahun): array
    {
        return [
            Carbon::create($tahun, 1, 1)->startOfDay()->toDateString(),
            Carbon::create($tahun, 12, 31)->endOfDay()->toDateString(),
        ];
    }

    /**
     * Biaya pemeliharaan, dengan aturan yang sama persis seperti BiayaPemeliharaan:
     * seluruh kunjungan yang dikerjakan, ditambah perintah kerja selesai yang tidak
     * menempel pada kunjungan mana pun, supaya tidak ada yang terhitung dua kali.
     *
     * @return array<int, float>|float
     */
    protected function pemeliharaan(string $dari, string $sampai, bool $tanpaDepartemen = false): array|float
    {
        $kunjungan = MaintenanceVisit::query()
            ->join('assets', 'assets.id', '=', 'maintenance_visits.asset_id')
            ->where('maintenance_visits.status', 'dikerjakan')
            ->whereBetween('maintenance_visits.completed_date', [$dari, $sampai])
            ->when($tanpaDepartemen, fn ($q) => $q->whereNull('assets.department_id'))
            ->when(! $tanpaDepartemen, fn ($q) => $q->whereNotNull('assets.department_id'))
            ->select('assets.department_id', DB::raw('sum(maintenance_visits.cost) as total'))
            ->groupBy('assets.department_id');

        /*
         * leftJoin, bukan join. Sejak permintaan perbaikan dibangun, satu perintah kerja
         * boleh tidak menyebut aset sama sekali: lampu mati di koridor bukan kerusakan
         * aset yang terdaftar. Dengan join biasa, biaya pekerjaan seperti itu hilang dari
         * kedua sisi laporan, tidak masuk departemen mana pun dan tidak masuk angka belum
         * terbebankan. Uang yang keluar tetapi tidak muncul di laporan mana pun adalah
         * cacat yang paling sulit ditemukan orang.
         */
        $korektif = WorkOrder::query()
            ->leftJoin('assets', 'assets.id', '=', 'work_orders.asset_id')
            ->selesai()
            ->whereNull('work_orders.maintenance_visit_id')
            ->whereBetween('work_orders.completed_date', [$dari, $sampai])
            ->when($tanpaDepartemen, fn ($q) => $q->whereNull('assets.department_id'))
            ->when(! $tanpaDepartemen, fn ($q) => $q->whereNotNull('assets.department_id'))
            ->select('assets.department_id', DB::raw('sum(work_orders.cost) as total'))
            ->groupBy('assets.department_id');

        return $this->gabung([$kunjungan, $korektif], $tanpaDepartemen);
    }

    /** @return array<int, float>|float */
    protected function bbm(string $dari, string $sampai, bool $tanpaDepartemen = false): array|float
    {
        $query = VehicleRefueling::query()
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_refuelings.vehicle_id')
            ->join('assets', 'assets.id', '=', 'vehicles.asset_id')
            ->whereBetween('vehicle_refuelings.filled_at', [$dari, $sampai])
            ->when($tanpaDepartemen, fn ($q) => $q->whereNull('assets.department_id'))
            ->when(! $tanpaDepartemen, fn ($q) => $q->whereNotNull('assets.department_id'))
            ->select('assets.department_id', DB::raw('sum(vehicle_refuelings.cost) as total'))
            ->groupBy('assets.department_id');

        return $this->gabung([$query], $tanpaDepartemen);
    }

    /**
     * Biaya pengiriman paket yang sudah berangkat.
     *
     * Menurut tanggal kirim, bukan tanggal permintaan, karena yang dibebankan ke anggaran
     * adalah kapan uangnya keluar. Permintaan yang dibatalkan tidak pernah menghasilkan uang
     * keluar, jadi ia tidak ikut dijumlahkan.
     *
     * Rumus totalnya sengaja ditulis ulang di sini dalam bahasa SQL, dan itu satu satunya
     * penggandaan yang saya biarkan di kelas ini. Alasannya: menjumlahkan lewat PHP berarti
     * mengambil seluruh baris pengiriman satu tahun ke memori hanya untuk menjumlahkannya,
     * padahal halaman anggaran memanggil ini untuk setiap kategori. Kalau rumus di
     * ParcelShipment::totalBiaya() kelak berubah, baris ini harus ikut berubah, dan komentar
     * ini ada supaya orang yang mengubahnya tahu ada tempat kedua.
     *
     * @return array<int, float>|float
     */
    protected function kiriman(string $dari, string $sampai, bool $tanpaDepartemen = false): array|float
    {
        /*
         * Departemen wajib isi pada pengiriman, jadi tidak akan pernah ada biaya kiriman yang
         * tidak terbebankan. Cabang tanpaDepartemen tetap ditulis supaya kelas ini punya
         * bentuk yang sama untuk seluruh sumbernya, dan hasilnya memang selalu nol.
         */
        if ($tanpaDepartemen) {
            return 0.0;
        }

        $query = ParcelShipment::query()
            ->where('status', 'dikirim')
            ->whereBetween('shipped_date', [$dari, $sampai])
            ->select('department_id', DB::raw(
                'sum(coalesce(shipping_cost, 0) + coalesce(insurance_cost, 0)'
                .' + coalesce(packing_cost, 0) - coalesce(discount_amount, 0)'
                .' + coalesce(tax_amount, 0)) as total'
            ))
            ->groupBy('department_id');

        return $this->gabung([$query], false);
    }

    /**
     * Biaya perjalanan dinas yang pertanggungjawabannya sudah ditutup.
     *
     * Dua syarat, dan keduanya perlu. Perjalanannya harus sudah ditutup, karena rincian yang
     * masih bisa diubah akan membuat angka anggaran bergerak sendiri tanpa ada yang
     * menyentuhnya. Dan yang dijumlahkan adalah barisnya, menurut tanggal tiap pengeluaran,
     * bukan menurut tanggal perjalanan atau tanggal penutupannya. Perjalanan yang berangkat
     * akhir Desember dan pulang awal Januari karenanya terbelah ke dua tahun anggaran sesuai
     * kapan uangnya benar benar keluar.
     *
     * @return array<int, float>|float
     */
    protected function perjalananDinas(string $dari, string $sampai, bool $tanpaDepartemen = false): array|float
    {
        // Departemen wajib isi pada perjalanan dinas, jadi tidak akan pernah ada biaya yang
        // tidak terbebankan. Cabangnya tetap ditulis supaya bentuknya sama dengan sumber lain.
        if ($tanpaDepartemen) {
            return 0.0;
        }

        $query = BusinessTripExpense::query()
            ->join('business_trips', 'business_trips.id', '=', 'business_trip_expenses.business_trip_id')
            ->where('business_trips.status', 'selesai')
            ->whereBetween('business_trip_expenses.expense_date', [$dari, $sampai])
            ->select('business_trips.department_id', DB::raw('sum(business_trip_expenses.amount) as total'))
            ->groupBy('business_trips.department_id');

        return $this->gabung([$query], false);
    }

    /** @return array<int, float>|float */
    protected function dokumenKendaraan(string $dari, string $sampai, bool $tanpaDepartemen = false): array|float
    {
        $query = VehicleDocument::query()
            ->join('vehicles', 'vehicles.id', '=', 'vehicle_documents.vehicle_id')
            ->join('assets', 'assets.id', '=', 'vehicles.asset_id')
            // Menurut tanggal terbit, bukan tanggal berakhir, karena yang dibebankan ke
            // anggaran adalah kapan uangnya keluar, bukan kapan masa berlakunya habis.
            ->whereBetween('vehicle_documents.issued_date', [$dari, $sampai])
            ->when($tanpaDepartemen, fn ($q) => $q->whereNull('assets.department_id'))
            ->when(! $tanpaDepartemen, fn ($q) => $q->whereNotNull('assets.department_id'))
            ->select('assets.department_id', DB::raw('sum(vehicle_documents.cost) as total'))
            ->groupBy('assets.department_id');

        return $this->gabung([$query], $tanpaDepartemen);
    }

    /**
     * Barang habis pakai yang keluar dari gudang.
     *
     * Nilainya memakai harga pada mutasi itu sendiri kalau ada, dan kalau tidak, harga
     * pembelian terakhir barangnya. Barang yang belum pernah punya harga sama sekali
     * bernilai nol dan tidak menaikkan realisasi, karena menebak harganya berarti
     * mengarang angka yang akan dibandingkan orang dengan pagu anggarannya.
     *
     * @return array<int, float>|float
     */
    protected function persediaan(string $dari, string $sampai, bool $tanpaDepartemen = false): array|float
    {
        $query = SupplyTransaction::query()
            ->join('supply_items', 'supply_items.id', '=', 'supply_transactions.supply_item_id')
            ->where('supply_transactions.type', 'keluar')
            ->whereBetween('supply_transactions.transaction_date', [$dari, $sampai])
            ->when($tanpaDepartemen, fn ($q) => $q->whereNull('supply_transactions.department_id'))
            ->when(! $tanpaDepartemen, fn ($q) => $q->whereNotNull('supply_transactions.department_id'))
            ->select('supply_transactions.department_id', DB::raw(
                'sum(abs(supply_transactions.quantity) * coalesce(supply_transactions.unit_price, supply_items.last_price, 0)) as total'
            ))
            ->groupBy('supply_transactions.department_id');

        return $this->gabung([$query], $tanpaDepartemen);
    }

    /**
     * Tagihan rekanan dan penggantian biaya karyawan.
     *
     * Berbeda dari empat sumber lainnya, sumber ini bergantung pada kategorinya, karena
     * tiap baris faktur dan tiap struk menyebut sendiri kategori mana yang dibebani.
     * Karena itu kategorinya ikut masuk sebagai penyaring, bukan hanya sebagai penentu
     * sumber.
     *
     * Yang dijumlahkan hanya yang sudah disetujui dan yang sudah dibayar. Draf dan yang
     * masih menunggu tanda tangan dijumlahkan terpisah lewat tertundaPerDepartemen(),
     * supaya angkanya terlihat tanpa ikut menaikkan realisasi.
     *
     * Tanggalnya tanggal faktur dan tanggal struk, bukan tanggal bayar. Yang keluar
     * uangnya Desember tetap membebani tahun lalu meski dibayar Januari, dan itu yang
     * dipakai tim finance saat menutup buku.
     *
     * Departemen dibaca dari tempat yang berbeda pada keduanya, dan itu disengaja. Satu
     * faktur listrik dibagi ke banyak departemen sekaligus sehingga departemennya ada di
     * tiap baris, sedangkan struk milik satu karyawan hampir selalu jatuh ke satu
     * departemen sehingga cukup disebut sekali di kepala pengajuannya.
     *
     * @return array<int, float>|float
     */
    protected function tagihan(
        ExpenseCategory $kategori,
        string $dari,
        string $sampai,
        bool $tanpaDepartemen = false,
        bool $belumDisetujui = false,
    ): array|float {
        $faktur = VendorBillLine::query()
            ->join('vendor_bills', 'vendor_bills.id', '=', 'vendor_bill_lines.vendor_bill_id')
            ->where('vendor_bill_lines.expense_category_id', $kategori->getKey())
            ->whereBetween('vendor_bills.invoice_date', [$dari, $sampai])
            ->when($belumDisetujui,
                fn ($q) => $q->whereIn('vendor_bills.status', VendorBill::PENDING_STATUSES),
                fn ($q) => $q->whereIn('vendor_bills.status', VendorBill::COUNTED_STATUSES),
            )
            ->when($tanpaDepartemen, fn ($q) => $q->whereNull('vendor_bill_lines.department_id'))
            ->when(! $tanpaDepartemen, fn ($q) => $q->whereNotNull('vendor_bill_lines.department_id'))
            ->select('vendor_bill_lines.department_id', DB::raw('sum(vendor_bill_lines.amount) as total'))
            ->groupBy('vendor_bill_lines.department_id');

        $struk = ReimbursementLine::query()
            ->join('reimbursements', 'reimbursements.id', '=', 'reimbursement_lines.reimbursement_id')
            ->where('reimbursement_lines.expense_category_id', $kategori->getKey())
            ->whereBetween('reimbursement_lines.expense_date', [$dari, $sampai])
            ->when($belumDisetujui,
                fn ($q) => $q->whereIn('reimbursements.status', Reimbursement::PENDING_STATUSES),
                fn ($q) => $q->whereIn('reimbursements.status', Reimbursement::COUNTED_STATUSES),
            )
            ->when($tanpaDepartemen, fn ($q) => $q->whereNull('reimbursements.department_id'))
            ->when(! $tanpaDepartemen, fn ($q) => $q->whereNotNull('reimbursements.department_id'))
            ->select('reimbursements.department_id', DB::raw('sum(reimbursement_lines.amount) as total'))
            ->groupBy('reimbursements.department_id');

        return $this->gabung([$faktur, $struk], $tanpaDepartemen);
    }

    /**
     * Menggabungkan beberapa query yang sudah dikelompokkan menurut departemen.
     *
     * @param  array<int, \Illuminate\Database\Eloquent\Builder>  $queries
     * @return array<int, float>|float
     */
    protected function gabung(array $queries, bool $tanpaDepartemen): array|float
    {
        $hasil = [];
        $total = 0.0;

        foreach ($queries as $query) {
            foreach ($query->get() as $baris) {
                $nilai = round((float) $baris->total, 2);

                if ($tanpaDepartemen) {
                    $total += $nilai;

                    continue;
                }

                $id = (int) $baris->department_id;
                $hasil[$id] = round(($hasil[$id] ?? 0) + $nilai, 2);
            }
        }

        return $tanpaDepartemen ? round($total, 2) : $hasil;
    }
}
