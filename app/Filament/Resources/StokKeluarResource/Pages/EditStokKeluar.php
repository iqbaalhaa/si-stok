<?php

namespace App\Filament\Resources\StokKeluarResource\Pages;

use App\Filament\Resources\StokKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStokKeluar extends EditRecord
{
    protected static string $resource = StokKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
