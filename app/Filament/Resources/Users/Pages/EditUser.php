<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    /** @var array<int, array<string, bool>> */
    protected array $permissionOverrides = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return UserResource::fillOverrides($data, $this->record);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        [$data, $this->permissionOverrides] = UserResource::extractOverrides($data);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->permissionOverrides()->sync($this->permissionOverrides);
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
