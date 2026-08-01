<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RegistrationsTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Évolution des inscriptions (30 derniers jours)';

    protected static ?int $sort = 2;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();

        $counts = Registration::query()
            ->visibleTo(Auth::user())
            ->where('created_at', '>=', $start)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $labels = [];
        $data = [];

        for ($date = $start->copy(); $date->lte(now()); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->translatedFormat('d M');
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'data' => $data,
                    'borderColor' => '#106165',
                    'backgroundColor' => 'rgba(16, 97, 101, 0.15)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}
