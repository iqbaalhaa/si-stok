<?php

namespace App\Filament\Resources\StokKeluarResource\Pages;

use App\Filament\Resources\StokKeluarResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Barryvdh\DomPDF\Facade\Pdf;

class ListStokKeluar extends ListRecords
{
    protected static string $resource = StokKeluarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modalHeading('Tambah Stok Keluar')
                ->visible(fn () => auth()->user()?->role === 'admin'),
        ];
    }

    
}
