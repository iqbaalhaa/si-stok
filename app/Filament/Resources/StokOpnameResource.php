<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StokOpnameResource\Pages;
use App\Filament\Resources\StokOpnameResource\RelationManagers;
use App\Models\StokOpname;
use App\Models\Motor;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StokOpnameResource extends Resource
{
    protected static ?string $model = StokOpname::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Manajemen Stok';
    protected static ?string $navigationLabel = 'Stok Opname';
    protected static ?string $pluralModelLabel = 'Stok Opname';
    protected static ?string $slug = 'stok-opname';

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
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('stok_sistem', Motor::find($state)?->stok ?? 0)
                    ),

                Forms\Components\TextInput::make('stok_sistem')
                    ->label('Stok Sistem')
                    ->numeric()
                    ->readOnly(),

                Forms\Components\TextInput::make('stok_fisik')
                    ->label('Stok Fisik')
                    ->numeric()
                    ->required(),

                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('petugas')
                    ->label('Petugas')
                    ->default(auth()->user()->name ?? null),

                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal')
                    ->default(now())
                    ->required(),
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

                Tables\Columns\TextColumn::make('stok_sistem')
                    ->label('Stok Sistem'),

                Tables\Columns\TextColumn::make('stok_fisik')
                    ->label('Stok Fisik'),

                Tables\Columns\TextColumn::make('selisih')
                    ->label('Selisih')
                    ->sortable()
                    ->color(fn ($state) => $state < 0 ? 'danger' : ($state > 0 ? 'success' : 'gray')),

                Tables\Columns\TextColumn::make('petugas')
                    ->label('Petugas'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn () => in_array(auth()->user()?->role, ['admin', 'kepala'], true))
                    ->action(function (\Filament\Tables\Contracts\HasTable $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $pdf = Pdf::loadView('exports.stok_opnames', [
                            'records' => $records,
                            'title' => 'Laporan Stok Opname',
                        ]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'laporan-stok-opname.pdf', [
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListStokOpnames::route('/'),
        ];
    }
}
