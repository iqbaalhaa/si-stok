<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MotorResource\Pages;
use App\Filament\Resources\MotorResource\RelationManagers;
use App\Models\Motor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\{TextInput, Select, FileUpload};
use Filament\Tables\Columns\{TextColumn, ImageColumn, BadgeColumn};

class MotorResource extends Resource
{
    protected static ?string $model = Motor::class;
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';
    protected static ?string $navigationGroup = 'Data Master';
    protected static ?string $modelLabel = 'Motor';
    protected static ?string $pluralModelLabel = 'Motor';
    protected static ?string $navigationLabel = 'Data Motor';
    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('kode_motor')
                    ->label('Kode Motor')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(20),

                TextInput::make('nama_motor')
                    ->label('Nama Motor')
                    ->required(),

                TextInput::make('tipe')
                    ->label('Tipe Motor')
                    ->placeholder('Contoh: Matic, Sport, Bebek'),

                TextInput::make('warna')
                    ->label('Warna'),

                TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(date('Y')),

                TextInput::make('harga_beli')
                    ->label('Harga Beli')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                TextInput::make('harga_jual')
                    ->label('Harga Jual')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                TextInput::make('stok')
                    ->label('Stok')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Nonaktif' => 'Nonaktif',
                    ])
                    ->default('Aktif'),

                FileUpload::make('foto')
                    ->label('Foto Motor')
                    ->directory('motor')
                    ->disk('public')
                    ->image()
                    ->imageEditor()
                    ->visibility('public')
                    ->maxSize(2048),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->height(50)
                    ->width(50),

                TextColumn::make('kode_motor')->label('Kode')->searchable()->sortable(),
                TextColumn::make('nama_motor')->label('Nama')->searchable()->sortable(),
                TextColumn::make('tipe')->label('Tipe'),
                TextColumn::make('warna')->label('Warna'),
                TextColumn::make('tahun')->label('Tahun')->sortable(),

                TextColumn::make('harga_beli')
                    ->label('Harga Beli')
                    ->money('IDR', true),

                TextColumn::make('harga_jual')
                    ->label('Harga Jual')
                    ->money('IDR', true),

                TextColumn::make('stok')
                    ->label('Stok')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 3 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),

                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'Aktif',
                        'danger' => 'Nonaktif',
                    ])
                    ->label('Status'),
            ])
            ->defaultSort('nama_motor', 'asc')
            ->searchable()
            ->headerActions([
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->visible(fn () => in_array(auth()->user()?->role, ['admin', 'kepala'], true))
                    ->action(function (\Filament\Tables\Contracts\HasTable $livewire) {
                        $records = $livewire->getFilteredTableQuery()->get();
                        $pdf = Pdf::loadView('exports.motors', [
                            'records' => $records,
                            'title' => 'Laporan Stok Motor',
                        ]);
                        return response()->streamDownload(fn () => print($pdf->output()), 'laporan-motor.pdf', [
                            'Content-Type' => 'application/pdf',
                        ]);
                    }),
            ])
            
            
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Aktif' => 'Aktif',
                        'Nonaktif' => 'Nonaktif',
                    ]),
                Tables\Filters\SelectFilter::make('tipe')
                    ->options(fn () => Motor::query()
                        ->whereNotNull('tipe')
                        ->where('tipe', '!=', '')
                        ->distinct()
                        ->pluck('tipe', 'tipe')
                        ->toArray()),
                Tables\Filters\Filter::make('stok_level')
                    ->label('Level Stok')
                    ->form([
                        Forms\Components\Select::make('level')
                            ->options([
                                'low' => '≤ 3 (Menipis)',
                                'mid' => '4–10',
                                'high' => '> 10',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['level'] ?? null) {
                            'low' => $query->where('stok', '<=', 3),
                            'mid' => $query->whereBetween('stok', [4, 10]),
                            'high' => $query->where('stok', '>', 10),
                            default => $query,
                        };
                    }),
                Tables\Filters\SelectFilter::make('tahun')
                    ->options(fn () => Motor::query()->select('tahun')->distinct()->pluck('tahun', 'tahun')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->modalHeading('Edit Data Motor')
                    ->visible(fn () => auth()->user()?->role === 'admin'),
                Tables\Actions\DeleteAction::make()
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
            'index' => Pages\ListMotor::route('/'),
        ];
    }
}
