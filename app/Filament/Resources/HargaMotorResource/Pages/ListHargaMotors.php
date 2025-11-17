<?php

namespace App\Filament\Resources\HargaMotorResource\Pages;

use App\Filament\Resources\HargaMotorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;

class ListHargaMotors extends ListRecords
{
    protected static string $resource = HargaMotorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah')
                ->icon('heroicon-o-plus')
                ->tooltip('Tambah harga motor')
                ->visible(fn () => auth()->user()?->role === 'admin'),
        ];
    }

    
}
