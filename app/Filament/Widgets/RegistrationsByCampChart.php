<?php

namespace App\Filament\Widgets;

use App\Models\Camp;
use App\Enums\CampStatus;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RegistrationsByCampChart extends ChartWidget
{
    protected static ?string $heading = 'Inscriptions par camp';

    protected static ?int $sort = 3;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 2;

    protected function getData(): array
    {
        $camps = Camp::query()
            ->visibleTo(Auth::user())
            ->where('statut', '!=', CampStatus::Archive)
            ->withCount('registrations')
            ->orderByDesc('registrations_count')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'data' => $camps->pluck('registrations_count')->all(),
                    'backgroundColor' => '#106165',
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
            'indexAxis' => 'y',
            'scales' => [
                'x' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}
