<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Sexe: string implements HasLabel
{
    case Masculin = 'Masculin';
    case Feminin = 'Feminin';

    public function getLabel(): string
    {
        return match ($this) {
            self::Masculin => 'Masculin',
            self::Feminin => 'Féminin',
        };
    }
}
