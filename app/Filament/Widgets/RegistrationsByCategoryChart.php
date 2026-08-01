<?php

namespace App\Filament\Widgets;

use App\Models\CampCategory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RegistrationsByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Répartition par rôle';

    protected static ?int $sort = 6;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $rows = CampCategory::query()
            ->whereHas('camp', fn ($q) => $q->visibleTo(Auth::user()))
            ->withCount('registrations')
            ->get()
            ->groupBy('name')
            ->map(fn ($group) => $group->sum('registrations_count'))
            ->filter(fn (int $total) => $total > 0)
            ->sortDesc()
            ->take(6);

        return [
            'datasets' => [
                [
                    'data' => $rows->values()->all(),
                    'backgroundColor' => ['#106165', '#e07a5f', '#3d5a80', '#f2b134', '#9b5de5', '#43aa8b'],
                ],
            ],
            'labels' => $rows->keys()->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
