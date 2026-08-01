<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CampStatus: string implements HasColor, HasLabel
{
    case Brouillon = 'brouillon';
    case Ouvert = 'ouvert';
    case Ferme = 'ferme';
    case Archive = 'archive';

    public function getLabel(): string
    {
        return match ($this) {
            self::Brouillon => 'Brouillon',
            self::Ouvert => 'Ouvert',
            self::Ferme => 'Fermé',
            self::Archive => 'Archivé',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Brouillon => 'gray',
            self::Ouvert => 'success',
            self::Ferme => 'danger',
            self::Archive => 'warning',
        };
    }
}
