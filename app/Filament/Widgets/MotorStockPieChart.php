<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Motor;

class MotorStockPieChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Stok Motor';

    protected function getData(): array
    {
        $motors = Motor::query()
            ->select(['nama_motor', 'stok'])
            ->orderByDesc('stok')
            ->limit(5)
            ->get();

        $labels = $motors->pluck('nama_motor')->toArray();
        $data = $motors->pluck('stok')->map(fn ($v) => (int) $v)->toArray();

        return [
            'datasets' => [[
                'label' => 'Stok',
                'data' => $data,
                'backgroundColor' => [
                    '#e30613', // amber
                    '#10b981', // emerald
                    '#3b82f6', // blue
                    '#ef4444', // red
                    '#8b5cf6', // violet
                ],
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}