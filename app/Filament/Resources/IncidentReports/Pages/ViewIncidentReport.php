<?php

namespace App\Filament\Resources\IncidentReports\Pages;

use App\Filament\Resources\IncidentReports\IncidentReportResource;
use App\Filament\Resources\ServiceRequests\ServiceRequestResource;
use App\Models\IncidentReport;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIncidentReport extends ViewRecord
{
    protected static string $resource = IncidentReportResource::class;

    public function getSubheading(): ?string
    {
        /** @var IncidentReport $insiden */
        $insiden = $this->getRecord();

        if ($insiden->isClosed()) {
            return 'Insiden ini sudah ditutup. Catatannya tetap tersimpan sebagai riwayat kejadian di gedung ini.';
        }

        if ($insiden->punyaTiket()) {
            return 'Sudah diteruskan menjadi tiket perbaikan '.$insiden->serviceRequest?->code
                .'. Pekerjaan perbaikannya berjalan di sana, dan halaman ini tinggal menunggu untuk ditutup.';
        }

        return 'Belum ditindaklanjuti. Teruskan menjadi tiket perbaikan kalau ada yang perlu diperbaiki, atau tutup kalau urusannya memang sudah selesai.';
    }

    protected function getHeaderActions(): array
    {
        return [
            IncidentReportResource::buatTiketAction(iconOnly: false),
            IncidentReportResource::tutupAction(iconOnly: false),
            /*
             * Tautan ke tiketnya memakai alamat halaman lihat tiket, bukan daftar yang
             * disaring. Kunci pencarian di alamat daftar bernama search, bukan tableSearch,
             * dan salah menuliskannya membuat tombol terbuka ke seluruh isi daftar tanpa
             * satu pun pesan galat. Itu cacat D-29 pada kiriman O, dan di sini dihindari
             * sekalian dengan menunjuk langsung ke tiketnya.
             */
            Action::make('buka_tiket')
                ->label('Open Repair Ticket')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->visible(fn (): bool => $this->getRecord()->punyaTiket()
                    && ServiceRequestResource::canViewAny())
                ->url(fn (): ?string => $this->getRecord()->serviceRequest === null
                    ? null
                    : ServiceRequestResource::getUrl('view', ['record' => $this->getRecord()->serviceRequest]))
                ->openUrlInNewTab(),
            EditAction::make()
                ->visible(fn (): bool => ! $this->getRecord()->isClosed()
                    && IncidentReportResource::canEdit($this->getRecord())),
        ];
    }
}
