<?php

namespace App\Filament\Resources\StokOpnameResource\Pages;

use App\Filament\Resources\StokOpnameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStokOpname extends EditRecord
{
    protected static string $resource = StokOpnameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
