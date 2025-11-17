<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\StokMasuk;
use App\Models\StokKeluar;
use Illuminate\Support\Carbon;

class StockMovementChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Stok Masuk vs Keluar (6 Bulan)';

    protected function getData(): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $end = now()->endOfMonth();

        $months = collect(range(0, 5))
            ->map(fn (int $i) => now()->startOfMonth()->subMonths(5 - $i));

        $masuk = StokMasuk::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym, SUM(jumlah) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $keluar = StokKeluar::query()
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as ym, SUM(jumlah) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = $months->map(fn (Carbon $m) => $m->format('M Y'));
        $keys = $months->map(fn (Carbon $m) => $m->format('Y-m'));

        $masukData = $keys->map(fn (string $k) => (int) ($masuk[$k] ?? 0));
        $keluarData = $keys->map(fn (string $k) => (int) ($keluar[$k] ?? 0));

        return [
            'datasets' => [
                [
                    'label' => 'Stok Masuk',
                    'data' => $masukData->toArray(),
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Stok Keluar',
                    'data' => $keluarData->toArray(),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}