<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemSettingResource\Pages;
use App\Filament\Resources\SystemSettingResource\RelationManagers;
use App\Models\SystemSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SystemSettingResource extends Resource
{
    protected static ?string $model = SystemSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $navigationLabel = 'Pengaturan Sistem';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nama_sistem')
                ->label('Nama Sistem'),

            Forms\Components\TextInput::make('nama_perusahaan')
                ->label('Nama Perusahaan'),

            Forms\Components\Textarea::make('alamat')
                ->label('Alamat')
                ->columnSpanFull(),

            Forms\Components\TextInput::make('telepon')
                ->label('Telepon'),

            Forms\Components\FileUpload::make('logo')
                ->image()
                ->label('Logo')
                ->directory('system/logo')
                ->disk('public')
                ->visibility('public'),

            Forms\Components\FileUpload::make('login_logo')
                ->image()
                ->label('Logo Login')
                ->helperText('Logo khusus untuk halaman login')
                ->directory('system/login-logo')
                ->disk('public')
                ->visibility('public'),

            Forms\Components\FileUpload::make('favicon')
                ->image()
                ->label('Favicon')
                ->directory('system/favicon')
                ->disk('public')
                ->visibility('public'),

            Forms\Components\TextInput::make('footer_text')
                ->label('Footer Text'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_sistem')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_perusahaan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('logo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('favicon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('footer_text')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemSettings::route('/'),
            'edit' => Pages\EditSystemSetting::route('/{record}/edit'),
        ];
    }
}
