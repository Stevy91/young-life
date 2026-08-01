<?php

namespace App\Filament\Resources\CampResource\Pages;

use App\Filament\Resources\CampResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCamps extends ListRecords
{
    protected static string $resource = CampResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
