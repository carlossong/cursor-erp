<?php

namespace App\Enums;

enum Unit: string
{
    case Hour = 'hora';
    case Unit = 'un';
    case Sqm = 'm2';
    case Month = 'mes';
    case Job = 'vb';

    public function label(): string
    {
        return match ($this) {
            self::Hour => 'Hora',
            self::Unit => 'Unidade',
            self::Sqm => 'm²',
            self::Month => 'Mês',
            self::Job => 'Verba',
        };
    }
}
