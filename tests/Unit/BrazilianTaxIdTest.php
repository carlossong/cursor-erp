<?php

use App\Enums\PersonType;
use App\Rules\BrazilianTaxId;

test('accepts a valid cpf and cnpj', function () {
    expect(BrazilianTaxId::isValid(PersonType::PF, '52998224725'))->toBeTrue()
        ->and(BrazilianTaxId::isValid(PersonType::PJ, '11444777000161'))->toBeTrue();
});

test('rejects repeated digits and wrong length', function () {
    expect(BrazilianTaxId::isValid(PersonType::PF, '11111111111'))->toBeFalse()
        ->and(BrazilianTaxId::isValid(PersonType::PJ, '11111111111111'))->toBeFalse()
        ->and(BrazilianTaxId::isValid(PersonType::PF, '123'))->toBeFalse();
});
