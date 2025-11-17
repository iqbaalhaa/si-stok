<?php

namespace App\Filament\Resources\SystemSettingResource\Pages;

use App\Filament\Resources\SystemSettingResource;
use Filament\Pages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\SystemSetting;

class EditSystemSetting extends EditRecord
{
    protected static string $resource = SystemSettingResource::class;

    public function mount($record = null): void
    {
        $record = SystemSetting::first() ?? SystemSetting::create();
        parent::mount($record->id);
    }

    public function getTitle(): string
    {
        return 'Pengaturan Sistem';
    }

    protected function getRedirectUrl(): string
    {
        return Pages\Dashboard::getUrl();
    }
}