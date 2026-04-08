<?php

use Factuapi\PhpSdk\Dto\Responses\CreateInvoiceResponse;

it('maps all fields from response', function () {
    $data = [
        'message' => 'Invoice created successfully',
        'invoice' => ['invoice_series_code' => 'TEST', 'invoice_number' => '1'],
        'data' => ['id' => 'abc123'],
        'processes' => [['name' => 'verifactu', 'status' => 'pending']],
    ];

    $response = CreateInvoiceResponse::fromResponse($data);

    expect($response->message)->toBe('Invoice created successfully')
        ->and($response->invoice)->toBe($data['invoice'])
        ->and($response->data)->toBe($data['data'])
        ->and($response->processes)->toBe($data['processes']);
});

it('handles null message and null processes', function () {
    $data = [
        'invoice' => ['invoice_series_code' => 'TEST', 'invoice_number' => '1'],
        'data' => ['id' => 'abc123'],
    ];

    $response = CreateInvoiceResponse::fromResponse($data);

    expect($response->message)->toBeNull()
        ->and($response->processes)->toBeNull();
});
