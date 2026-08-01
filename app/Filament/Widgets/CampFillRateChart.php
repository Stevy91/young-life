<?php

namespace App\Filament\Widgets;

use App\Enums\CampStatus;
use App\Models\Camp;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class CampFillRateChart extends ChartWidget
{
    protected static ?string $heading = 'Taux de remplissage par camp';

    protected static ?int $sort = 4;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $camps = Camp::query()
            ->visibleTo(Auth::user())
            ->where('statut', CampStatus::Ouvert)
            ->whereNotNull('capacite')
            ->withCount('registrations')
            ->orderByDesc('capacite')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Inscrits',
                    'data' => $camps->pluck('registrations_count')->all(),
                    'backgroundColor' => '#106165',
                    'borderRadius' => 4,
                ],
                [
                    'label' => 'Places restantes',
                    'data' => $camps->map(fn (Camp $c) => max($c->capacite - $c->registrations_count, 0))->all(),
                    'backgroundColor' => '#e5e7eb',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $camps->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['stacked' => true],
                'y' => ['stacked' => true, 'beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
        ];
    }
}
