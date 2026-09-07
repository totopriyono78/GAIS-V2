<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int, string> */
    protected array $permissionKeys = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return RoleResource::fillPermissionKeys($data, $this->record);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        [$data, $this->permissionKeys] = RoleResource::extractPermissionKeys($data);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->syncPermissionKeys($this->permissionKeys);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
