<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HargaMotorResource\Pages;
use App\Filament\Resources\HargaMotorResource\RelationManagers;
use App\Models\HargaMotor;
use App\Models\Motor;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HargaMotorResource extends Resource
{
    protected static ?string $model = HargaMotor::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $pluralLabel = 'Harga Motor';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('motor_id')
                ->label('Motor')
                ->options(Motor::query()->pluck('nama_motor', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('harga_cash')
                ->label('Harga Cash')
                ->numeric()
                ->prefix('Rp')
                ->required(),

            Forms\Components\TextInput::make('uang_muka')
                ->label('Uang Muka')
                ->numeric()
                ->prefix('Rp'),

            Forms\Components\TextInput::make('angsuran')
                ->label('Angsuran / Bulan')
                ->numeric()
                ->prefix('Rp'),

            Forms\Components\TextInput::make('lama_kredit')
                ->label('Lama Kredit (bulan)')
                ->numeric(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('motor.nama_motor')->label('Motor'),
                Tables\Columns\TextColumn::make('harga_cash')->money('IDR', true),
                Tables\Columns\TextColumn::make('uang_muka')->money('IDR', true),
                Tables\Columns\TextColumn::make('angsuran')->money('IDR', true),
                Tables\Columns\TextColumn::make('lama_kredit')->label('Bulan'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn () => in_array(auth()->user()?->role, ['admin', 'kepala'], true))
                    ->action(function (\Filament\Tables\Contracts\HasTable $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $pdf = Pdf::loadView('exports.harga_motors', [
                            'records' => $records,
                            'title' => 'Laporan Harga Motor',
                        ]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'laporan-harga-motor.pdf', [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
            ])
            
            
            ->filters([
                Tables\Filters\SelectFilter::make('motor_id')
                    ->label('Motor')
                    ->options(fn () => Motor::query()->pluck('nama_motor', 'id')->toArray()),
                Tables\Filters\Filter::make('harga_cash_range')
                    ->label('Harga Cash')
                    ->form([
                        Forms\Components\TextInput::make('min')->numeric()->prefix('Rp')->label('Min'),
                        Forms\Components\TextInput::make('max')->numeric()->prefix('Rp')->label('Max'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(($data['min'] ?? null) !== null && ($data['min'] ?? '') !== '', fn ($q) => $q->where('harga_cash', '>=', $data['min']))
                            ->when(($data['max'] ?? null) !== null && ($data['max'] ?? '') !== '', fn ($q) => $q->where('harga_cash', '<=', $data['max']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->role === 'admin'),
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
            'index' => Pages\ListHargaMotors::route('/'),
            'create' => Pages\CreateHargaMotor::route('/create'),
            'edit' => Pages\EditHargaMotor::route('/{record}/edit'),
        ];
    }
}
