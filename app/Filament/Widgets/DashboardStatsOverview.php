<?php

namespace App\Filament\Widgets;

use App\Enums\CampStatus;
use App\Models\Camp;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class DashboardStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $user = Auth::user();

        $camps = Camp::query()->visibleTo($user);
        $openCamps = (clone $camps)->where('statut', CampStatus::Ouvert);
        $registrations = Registration::query()->visibleTo($user);

        $totalCapacite = (clone $openCamps)->whereNotNull('capacite')->sum('capacite');
        $totalInscritsSurCampsAvecCapacite = Registration::query()
            ->visibleTo($user)
            ->whereHas('camp', fn ($q) => $q->where('statut', CampStatus::Ouvert)->whereNotNull('capacite'))
            ->count();

        $tauxRemplissage = $totalCapacite > 0
            ? round(($totalInscritsSurCampsAvecCapacite / $totalCapacite) * 100)
            : null;

        $inscriptionsCetteSemaine = (clone $registrations)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            Stat::make('Camps ouverts', (string) $openCamps->count())
                ->icon('heroicon-o-flag')
                ->color('success'),

            Stat::make('Total inscriptions', (string) $registrations->count())
                ->description("{$inscriptionsCetteSemaine} cette semaine")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make(
                'Taux de remplissage moyen',
                $tauxRemplissage !== null ? "{$tauxRemplissage}%" : 'N/A',
            )
                ->description($tauxRemplissage !== null ? "{$totalInscritsSurCampsAvecCapacite} sur {$totalCapacite} places" : 'Aucune capacité définie')
                ->icon('heroicon-o-chart-pie')
                ->color(match (true) {
                    $tauxRemplissage === null => 'gray',
                    $tauxRemplissage >= 90 => 'danger',
                    $tauxRemplissage >= 60 => 'warning',
                    default => 'success',
                }),

            Stat::make('Mineurs inscrits', (string) (clone $registrations)->where('statut', 'Mineur')->count())
                ->description('Nécessitent une autorisation parentale')
                ->icon('heroicon-o-identification')
                ->color('warning'),
        ];
    }
}
