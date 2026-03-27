<?php

use Factuapi\PhpSdk\Connectors\FactuapiConnector;
use Factuapi\PhpSdk\Dto\Company;
use Factuapi\PhpSdk\Requests\CompanyRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('resolves to /company endpoint', function () {
    expect((new CompanyRequest())->resolveEndpoint())->toBe('/company');
});

it('maps response to Company DTO', function () {
    $connector = new FactuapiConnector('token', 'https://app.factuapi.com/api/', 10);
    $connector->withMockClient(new MockClient([
        CompanyRequest::class => MockResponse::make(['name' => 'Acme SL', 'vat_number' => 'B12345678']),
    ]));

    $company = $connector->send(new CompanyRequest())->dto();

    expect($company)->toBeInstanceOf(Company::class)
        ->and($company->name)->toBe('Acme SL')
        ->and($company->vatNumber)->toBe('B12345678');
});
