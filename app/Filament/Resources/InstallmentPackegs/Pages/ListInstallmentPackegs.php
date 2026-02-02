<?php

namespace App\Filament\Resources\InstallmentPackegs\Pages;

use App\Filament\Resources\InstallmentPackegs\InstallmentPackegResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInstallmentPackegs extends ListRecords
{
    protected static string $resource = InstallmentPackegResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
