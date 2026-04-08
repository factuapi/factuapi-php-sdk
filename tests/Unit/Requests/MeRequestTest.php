<?php

use Factuapi\PhpSdk\Connectors\FactuapiConnector;
use Factuapi\PhpSdk\Dto\User;
use Factuapi\PhpSdk\Requests\MeRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('resolves to /user endpoint', function () {
    expect((new MeRequest())->resolveEndpoint())->toBe('/user');
});

it('maps response to User DTO', function () {
    $connector = new FactuapiConnector('token', 'https://app.factuapi.com/api/', 10);
    $connector->withMockClient(new MockClient([
        MeRequest::class => MockResponse::make(['name' => 'John Doe', 'email' => 'john@example.com']),
    ]));

    $user = $connector->send(new MeRequest())->dto();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com');
});
