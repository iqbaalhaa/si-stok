<?php

namespace App\Filament\Resources\SystemSettingResource\Pages;

use App\Filament\Resources\SystemSettingResource;
use App\Models\SystemSetting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSystemSettings extends ListRecords
{
    protected static string $resource = SystemSettingResource::class;

    public function mount(): void
    {
        $record = SystemSetting::first() ?? SystemSetting::create();
        redirect()->to(SystemSettingResource::getUrl('edit', ['record' => $record]));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()?->role === 'admin'),
        ];
    }
}
