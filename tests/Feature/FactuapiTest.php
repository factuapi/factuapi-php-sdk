<?php

use Factuapi\PhpSdk\Dto\Company;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Responses\CreateInvoiceResponse;
use Factuapi\PhpSdk\Dto\User;
use Factuapi\PhpSdk\Exceptions\FactuapiException;
use Factuapi\PhpSdk\Exceptions\ValidationException;
use Factuapi\PhpSdk\Factuapi;
use Factuapi\PhpSdk\Requests\CompanyRequest;
use Factuapi\PhpSdk\Requests\Invoices\CancelInvoiceRequest;
use Factuapi\PhpSdk\Requests\Invoices\CreateInvoiceRequest;
use Factuapi\PhpSdk\Requests\MeRequest;
use Factuapi\PhpSdk\Resources\Invoices;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

function makeFactuapi(array $mockData): Factuapi
{
    $factuapi = new class('test-token') extends Factuapi {
        public function withMockClient(MockClient $mockClient): void
        {
            $this->connector->withMockClient($mockClient);
        }

        public function getLastPendingRequest(): ?PendingRequest
        {
            return $this->connector->getMockClient()?->getLastPendingRequest();
        }
    };

    $factuapi->withMockClient(new MockClient($mockData));

    return $factuapi;
}

// --- me() ---

test('Factuapi::me() returns a User DTO', function () {
    $factuapi = makeFactuapi([
        MeRequest::class => MockResponse::make(['name' => 'John Doe', 'email' => 'john@example.com']),
    ]);

    $user = $factuapi->me();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com');
});

// --- company() ---

test('Factuapi::company() returns a Company DTO', function () {
    $factuapi = makeFactuapi([
        CompanyRequest::class => MockResponse::make(['name' => 'Acme SL', 'vat_number' => 'B12345678']),
    ]);

    $company = $factuapi->company();

    expect($company)->toBeInstanceOf(Company::class)
        ->and($company->name)->toBe('Acme SL')
        ->and($company->vatNumber)->toBe('B12345678');
});

// --- invoices() ---

test('Factuapi::invoices() returns an Invoices resource', function () {
    $factuapi = makeFactuapi([]);

    expect($factuapi->invoices())->toBeInstanceOf(Invoices::class);
});

test('Factuapi::invoices()->create() returns a CreateInvoiceResponse', function () {
    $factuapi = makeFactuapi([
        CreateInvoiceRequest::class => MockResponse::make(createInvoiceResponseFixture()),
    ]);

    $result = $factuapi->invoices()->create(makeInvoice(), []);

    expect($result)->toBeInstanceOf(CreateInvoiceResponse::class)
        ->and($result->message)->toBe('Invoice created successfully');
});

test('Factuapi::invoices()->correct() returns a CreateInvoiceResponse', function () {
    $factuapi = makeFactuapi([
        CreateInvoiceRequest::class => MockResponse::make(createInvoiceResponseFixture()),
    ]);

    $result = $factuapi->invoices()->correct(
        previousInvoiceId: new InvoiceId(seriesCode: 'ORIG', number: '1'),
        invoice: makeInvoice(),
        process: [],
    );

    expect($result)->toBeInstanceOf(CreateInvoiceResponse::class);
});

test('Factuapi::invoices()->cancel() returns a CreateInvoiceResponse', function () {
    $factuapi = makeFactuapi([
        CancelInvoiceRequest::class => MockResponse::make(createInvoiceResponseFixture()),
    ]);

    $result = $factuapi->invoices()->cancel(new InvoiceId(seriesCode: 'TEST', number: '1'), []);

    expect($result)->toBeInstanceOf(CreateInvoiceResponse::class);
});

// --- Auth headers ---

it('sends Authorization Bearer token header', function () {
    $factuapi = makeFactuapi([
        MeRequest::class => MockResponse::make(['name' => 'John', 'email' => 'john@example.com']),
    ]);

    $factuapi->me();

    expect($factuapi->getLastPendingRequest()->headers()->get('Authorization'))->toBe('Bearer test-token');
});

it('sends Accept: application/json header', function () {
    $factuapi = makeFactuapi([
        MeRequest::class => MockResponse::make(['name' => 'John', 'email' => 'john@example.com']),
    ]);

    $factuapi->me();

    expect($factuapi->getLastPendingRequest()->headers()->get('Accept'))->toBe('application/json');
});

// --- Error handling ---

it('throws ValidationException on 422', function () {
    $factuapi = makeFactuapi([
        MeRequest::class => MockResponse::make([
            'message' => 'The given data was invalid.',
            'errors' => ['field' => ['Required.']],
        ], 422),
    ]);

    expect(fn () => $factuapi->me())->toThrow(ValidationException::class);
});

it('throws FactuapiException on server error', function () {
    $factuapi = makeFactuapi([
        MeRequest::class => MockResponse::make(['message' => 'Server Error'], 500),
    ]);

    expect(fn () => $factuapi->me())->toThrow(FactuapiException::class);
});
