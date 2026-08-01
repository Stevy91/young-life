<?php

namespace App\Filament\Widgets;

use App\Enums\Sexe;
use App\Models\Registration;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RegistrationsBySexeChart extends ChartWidget
{
    protected static ?string $heading = 'Répartition par sexe';

    protected static ?int $sort = 5;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $counts = Registration::query()
            ->visibleTo(Auth::user())
            ->selectRaw('sexe, COUNT(*) as total')
            ->groupBy('sexe')
            ->pluck('total', 'sexe');

        return [
            'datasets' => [
                [
                    'data' => [
                        (int) ($counts[Sexe::Masculin->value] ?? 0),
                        (int) ($counts[Sexe::Feminin->value] ?? 0),
                    ],
                    'backgroundColor' => ['#106165', '#e07a5f'],
                ],
            ],
            'labels' => ['Masculin', 'Féminin'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
