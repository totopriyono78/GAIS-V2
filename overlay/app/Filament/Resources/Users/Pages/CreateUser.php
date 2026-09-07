<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /** @var array<int, array<string, bool>> */
    protected array $permissionOverrides = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        [$data, $this->permissionOverrides] = UserResource::extractOverrides($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->permissionOverrides()->sync($this->permissionOverrides);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
