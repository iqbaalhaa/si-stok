<?php

namespace App\Filament\Resources\HargaMotorResource\Pages;

use App\Filament\Resources\HargaMotorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHargaMotor extends EditRecord
{
    protected static string $resource = HargaMotorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
