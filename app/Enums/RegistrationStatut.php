<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RegistrationStatut: string implements HasColor, HasLabel
{
    case Majeur = 'Majeur';
    case Mineur = 'Mineur';

    public function getLabel(): string
    {
        return $this->value;
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Majeur => 'success',
            self::Mineur => 'warning',
        };
    }
}
