<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StokKeluarResource\Pages;
use App\Filament\Resources\StokKeluarResource\RelationManagers;
use App\Models\StokKeluar;
use App\Models\Motor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StokKeluarResource extends Resource
{
    protected static ?string $model = StokKeluar::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static ?string $navigationGroup = 'Manajemen Stok';
    protected static ?string $navigationLabel = 'Stok Keluar';
    protected static ?string $pluralModelLabel = 'Stok Keluar';
    protected static ?string $slug = 'stok-keluar';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('motor_id')
                    ->label('Motor')
                    ->options(Motor::all()->pluck('nama_motor', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Keluar')
                    ->default(now())
                    ->required(),

                Forms\Components\TextInput::make('jumlah')
                    ->label('Jumlah')
                    ->numeric()
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('motor.nama_motor')
                    ->label('Motor')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y'),

                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->sortable(),

                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(30),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn () => in_array(auth()->user()?->role, ['admin', 'kepala'], true))
                    ->action(function (\Filament\Tables\Contracts\HasTable $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $pdf = Pdf::loadView('exports.stok_keluars', [
                            'records' => $records,
                            'title' => 'Laporan Stok Keluar',
                        ]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'laporan-stok-keluar.pdf', [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
            ])
            
            ->filters([
                Tables\Filters\SelectFilter::make('motor_id')
                    ->label('Motor')
                    ->options(fn () => Motor::query()->pluck('nama_motor', 'id')->toArray()),
                Tables\Filters\Filter::make('tanggal_range')
                    ->label('Periode')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Dari'),
                        Forms\Components\DatePicker::make('until')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '>=', $date))
                            ->when($data['until'] ?? null, fn ($q, $date) => $q->whereDate('tanggal', '<=', $date));
                    }),
            ])
            
            ->actions([
                Tables\Actions\EditAction::make()->modalHeading('Edit Stok Keluar')
                    ->visible(fn () => auth()->user()?->role === 'admin'),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->role === 'admin'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->role === 'admin'),
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
            'index' => Pages\ListStokKeluar::route('/'),
        ];
    }
}
