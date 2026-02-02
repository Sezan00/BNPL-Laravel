<?php

namespace App\Filament\Resources\InstallmentPackegs\Pages;

use App\Filament\Resources\InstallmentPackegs\InstallmentPackegResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInstallmentPackeg extends EditRecord
{
    protected static string $resource = InstallmentPackegResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
