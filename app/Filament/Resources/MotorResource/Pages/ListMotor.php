<?php

namespace App\Filament\Resources\MotorResource\Pages;

use App\Filament\Resources\MotorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;

class ListMotor extends ListRecords
{
    protected static string $resource = MotorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalHeading('Tambah Data Motor')
                ->visible(fn () => auth()->user()?->role === 'admin'),
        ];
    }

    
}
