<?php

namespace App\Filament\Resources\StokMasukResource\Pages;

use App\Filament\Resources\StokMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;

class ListStokMasuks extends ListRecords
{
    protected static string $resource = StokMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalHeading('Tambah Stok Masuk')
                ->visible(fn () => auth()->user()?->role === 'admin'),
        ];
    }

    
}
