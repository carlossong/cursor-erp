<?php

namespace App\Enums;

enum PersonType: string
{
    case PF = 'pf';
    case PJ = 'pj';

    public function label(): string
    {
        return match ($this) {
            self::PF => 'Pessoa física',
            self::PJ => 'Pessoa jurídica',
        };
    }

    public function taxIdLength(): int
    {
        return match ($this) {
            self::PF => 11,
            self::PJ => 14,
        };
    }
}
