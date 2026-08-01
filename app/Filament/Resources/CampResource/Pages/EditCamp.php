<?php

namespace App\Filament\Resources\CampResource\Pages;

use App\Filament\Resources\CampResource;
use App\Filament\Resources\CampResource\Widgets\CategoryQuotas;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCamp extends EditRecord
{
    protected static string $resource = CampResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CategoryQuotas::class,
        ];
    }
}
