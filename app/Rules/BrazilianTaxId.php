<?php

namespace App\Rules;

use App\Enums\PersonType;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BrazilianTaxId implements ValidationRule
{
    public function __construct(private PersonType $personType) {}

    /**
     * @param  Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! is_string($value) && ! is_numeric($value)) {
            $fail(__('Informe um CPF ou CNPJ válido.'));

            return;
        }

        $digits = preg_replace('/\D/', '', (string) $value) ?? '';

        if (! self::isValid($this->personType, $digits)) {
            $fail($this->personType === PersonType::PF
                ? __('Informe um CPF válido.')
                : __('Informe um CNPJ válido.'));
        }
    }

    public static function isValid(PersonType $personType, string $digits): bool
    {
        $length = $personType->taxIdLength();

        if (strlen($digits) !== $length || preg_match('/^(\d)\1+$/', $digits) === 1) {
            return false;
        }

        return match ($personType) {
            PersonType::PF => self::cpfChecksum($digits),
            PersonType::PJ => self::cnpjChecksum($digits),
        };
    }

    private static function cpfChecksum(string $digits): bool
    {
        return self::cpfDigit($digits, 9) && self::cpfDigit($digits, 10);
    }

    private static function cpfDigit(string $digits, int $checkIndex): bool
    {
        $sum = 0;
        $weight = $checkIndex + 1;

        for ($i = 0; $i < $checkIndex; $i++) {
            $sum += (int) $digits[$i] * $weight--;
        }

        $digit = ($sum * 10) % 11;

        if ($digit === 10) {
            $digit = 0;
        }

        return (int) $digits[$checkIndex] === $digit;
    }

    private static function cnpjChecksum(string $digits): bool
    {
        return self::cnpjDigit($digits, [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2], 12)
            && self::cnpjDigit($digits, [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2], 13);
    }

    /**
     * @param  list<int>  $weights
     */
    private static function cnpjDigit(string $digits, array $weights, int $checkIndex): bool
    {
        $sum = 0;

        foreach ($weights as $index => $weight) {
            $sum += (int) $digits[$index] * $weight;
        }

        $remainder = $sum % 11;
        $digit = $remainder < 2 ? 0 : 11 - $remainder;

        return (int) $digits[$checkIndex] === $digit;
    }
}
