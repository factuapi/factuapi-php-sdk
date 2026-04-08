<?php

use Factuapi\PhpSdk\Dto\Company;

it('maps name and vat_number from response', function () {
    $company = Company::fromResponse(['name' => 'Acme SL', 'vat_number' => 'B12345678']);

    expect($company->name)->toBe('Acme SL')
        ->and($company->vatNumber)->toBe('B12345678');
});
