<?php

namespace App\Filament\Resources\CampResource\Widgets;

use App\Models\Camp;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CategoryQuotas extends StatsOverviewWidget
{
    public ?Camp $record = null;

    protected function getStats(): array
    {
        return $this->record
            ->categories()
            ->withCount('registrations')
            ->get()
            ->map(function ($category) {
                $isFull = $category->quota !== null && $category->registrations_count >= $category->quota;

                return Stat::make(
                    $category->name,
                    $category->quota !== null
                        ? "{$category->registrations_count} sur {$category->quota}"
                        : (string) $category->registrations_count,
                )->color($isFull ? 'danger' : 'success');
            })
            ->all();
    }
}
