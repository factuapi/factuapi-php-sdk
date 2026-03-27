<?php

use Factuapi\PhpSdk\Connectors\FactuapiConnector;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Responses\CreateInvoiceResponse;
use Factuapi\PhpSdk\Requests\Invoices\CancelInvoiceRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('resolves to /invoices/cancel endpoint', function () {
    $request = new CancelInvoiceRequest(
        invoiceId: new InvoiceId(seriesCode: 'TEST', number: '1'),
        process: [],
    );

    expect($request->resolveEndpoint())->toBe('/invoices/cancel');
});

it('builds correct request body', function () {
    $request = new CancelInvoiceRequest(
        invoiceId: new InvoiceId(seriesCode: 'TEST', number: '42'),
        process: ['verifactu' => true],
    );

    $body = $request->body()->all();

    expect($body['invoice_series_code'])->toBe('TEST')
        ->and($body['invoice_number'])->toBe('42')
        ->and($body['process'])->toBe(['verifactu' => true]);
});

it('maps response to CreateInvoiceResponse DTO', function () {
    $connector = new FactuapiConnector('token', 'https://app.factuapi.com/api/', 10);
    $connector->withMockClient(new MockClient([
        CancelInvoiceRequest::class => MockResponse::make(createInvoiceResponseFixture()),
    ]));

    $request = new CancelInvoiceRequest(
        invoiceId: new InvoiceId(seriesCode: 'TEST', number: '1'),
        process: [],
    );

    $response = $connector->send($request)->dto();

    expect($response)->toBeInstanceOf(CreateInvoiceResponse::class)
        ->and($response->message)->toBe('Invoice created successfully');
});
