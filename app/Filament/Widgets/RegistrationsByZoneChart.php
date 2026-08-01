<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RegistrationsByZoneChart extends ChartWidget
{
    protected static ?string $heading = 'Inscriptions par zone';

    protected static ?int $sort = 7;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 2;

    /**
     * Only meaningful for a Super Admin — a zone-scoped user only ever sees
     * their own zone's data everywhere else, so a "by zone" breakdown for
     * them would just be a single bar.
     */
    public static function canView(): bool
    {
        return Auth::user()?->isSuperAdmin() ?? false;
    }

    protected function getData(): array
    {
        $counts = Registration::query()
            ->join('camps', 'camps.id', '=', 'registrations.camp_id')
            ->join('zones', 'zones.id', '=', 'camps.zone_id')
            ->selectRaw('zones.name as zone_name, COUNT(*) as total')
            ->groupBy('zones.name')
            ->orderByDesc('total')
            ->pluck('total', 'zone_name');

        return [
            'datasets' => [
                [
                    'label' => 'Inscriptions',
                    'data' => $counts->values()->all(),
                    'backgroundColor' => '#106165',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $counts->keys()->all(),
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
                'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0]],
            ],
            'plugins' => ['legend' => ['display' => false]],
        ];
    }
}
