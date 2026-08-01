<?php

namespace App\Filament\Resources\ArrondissementResource\Pages;

use App\Filament\Resources\ArrondissementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArrondissement extends EditRecord
{
    protected static string $resource = ArrondissementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
