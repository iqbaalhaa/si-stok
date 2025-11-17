<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Motor;
use App\Models\StokMasuk;
use App\Models\StokKeluar;

class InventoryStatsOverview extends StatsOverviewWidget
{
    protected function getCards(): array
    {
        $threshold = 3;

        $motorCount = Motor::query()->count();
        $totalStock = (int) Motor::query()->sum('stok');
        $lowStockCount = Motor::query()->where('stok', '<=', $threshold)->count();

        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        $masukBulanIni = (int) StokMasuk::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah');

        $keluarBulanIni = (int) StokKeluar::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->sum('jumlah');

        $months = collect(range(0, 5))
            ->map(fn (int $i) => now()->startOfMonth()->subMonths(5 - $i));
        $keys = $months->map(fn ($m) => $m->format('Y-m'));
        $masuk = StokMasuk::query()
            ->whereBetween('tanggal', [$months->first()->toDateString(), $months->last()->endOfMonth()->toDateString()])
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym, SUM(jumlah) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');
        $keluar = StokKeluar::query()
            ->whereBetween('tanggal', [$months->first()->toDateString(), $months->last()->endOfMonth()->toDateString()])
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym, SUM(jumlah) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');
        $masukSeries = $keys->map(fn (string $k) => (int) ($masuk[$k] ?? 0));
        $keluarSeries = $keys->map(fn (string $k) => (int) ($keluar[$k] ?? 0));

        return [
            Card::make('Total Motor', (string) $motorCount)
                ->icon('heroicon-o-rectangle-stack')
                ->color('primary')
                ->extraAttributes(['class' => 'fi-color-custom fi-color-primary bg-gradient-to-br from-custom-50 to-white dark:from-custom-400/10 dark:to-gray-900 ring-custom-500/10']),
            Card::make('Total Stok', (string) $totalStock)
                ->icon('heroicon-o-cube')
                ->color('info')
                ->extraAttributes(['class' => 'fi-color-custom fi-color-info bg-gradient-to-br from-custom-50 to-white dark:from-custom-400/10 dark:to-gray-900 ring-custom-500/10']),
            Card::make('Stok Menipis', (string) $lowStockCount)
                ->description('≤ ' . $threshold)
                ->descriptionIcon('heroicon-o-bell-alert')
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->extraAttributes(['class' => 'fi-color-custom fi-color-warning bg-gradient-to-br from-custom-50 to-white dark:from-custom-400/10 dark:to-gray-900 ring-custom-500/10']),
            Card::make('Masuk Bulan Ini', (string) $masukBulanIni)
                ->icon('heroicon-o-arrow-trending-up')
                ->chart($masukSeries->toArray())
                ->color('success')
                ->extraAttributes(['class' => 'fi-color-custom fi-color-success bg-gradient-to-br from-custom-50 to-white dark:from-custom-400/10 dark:to-gray-900 ring-custom-500/10']),
            Card::make('Keluar Bulan Ini', (string) $keluarBulanIni)
                ->icon('heroicon-o-arrow-trending-down')
                ->chart($keluarSeries->toArray())
                ->color('danger')
                ->extraAttributes(['class' => 'fi-color-custom fi-color-danger bg-gradient-to-br from-custom-50 to-white dark:from-custom-400/10 dark:to-gray-900 ring-custom-500/10']),
        ];
    }
}