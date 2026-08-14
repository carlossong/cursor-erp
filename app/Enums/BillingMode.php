<?php

namespace App\Enums;

enum BillingMode: string
{
    case RequiresWorkOrder = 'exige_os';
    case Immediate = 'faturamento_imediato';

    public function label(): string
    {
        return match ($this) {
            self::RequiresWorkOrder => 'Exige OS',
            self::Immediate => 'Faturamento imediato',
        };
    }
}
