<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    /** @var array<int, string> */
    protected array $permissionKeys = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        [$data, $this->permissionKeys] = RoleResource::extractPermissionKeys($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->syncPermissionKeys($this->permissionKeys);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
