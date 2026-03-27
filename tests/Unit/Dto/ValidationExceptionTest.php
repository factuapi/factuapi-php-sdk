<?php

use Factuapi\PhpSdk\Connectors\FactuapiConnector;
use Factuapi\PhpSdk\Exceptions\ValidationException;
use Factuapi\PhpSdk\Requests\MeRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

function connector422(array $body): FactuapiConnector
{
    $connector = new FactuapiConnector('token', 'https://app.factuapi.com/api/', 10);
    $connector->withMockClient(new MockClient([
        MeRequest::class => MockResponse::make($body, 422),
    ]));

    return $connector;
}

function catchValidationException(FactuapiConnector $connector): ValidationException
{
    try {
        $connector->send(new MeRequest());
    } catch (ValidationException $e) {
        return $e;
    }

    throw new RuntimeException('Expected ValidationException was not thrown');
}

$errorsBody = [
    'message' => 'The given data was invalid.',
    'errors' => [
        'invoice_number' => ['The invoice number is required.', 'Must be numeric.'],
        'issue_date' => ['The issue date is invalid.'],
    ],
];

it('extracts errors from a 422 response', function () use ($errorsBody) {
    $exception = catchValidationException(connector422($errorsBody));

    expect($exception->getErrors())->toBe($errorsBody['errors']);
});

it('getErrorsForField returns errors for a given field', function () use ($errorsBody) {
    $exception = catchValidationException(connector422($errorsBody));

    expect($exception->getErrorsForField('invoice_number'))
        ->toBe(['The invoice number is required.', 'Must be numeric.'])
        ->and($exception->getErrorsForField('nonexistent'))->toBe([]);
});

it('hasErrorsForField returns true/false correctly', function () use ($errorsBody) {
    $exception = catchValidationException(connector422($errorsBody));

    expect($exception->hasErrorsForField('invoice_number'))->toBeTrue()
        ->and($exception->hasErrorsForField('nonexistent'))->toBeFalse();
});

it('getAllErrorMessages flattens all messages', function () use ($errorsBody) {
    $exception = catchValidationException(connector422($errorsBody));

    expect($exception->getAllErrorMessages())->toBe([
        'The invoice number is required.',
        'Must be numeric.',
        'The issue date is invalid.',
    ]);
});

it('builds exception message from errors', function () use ($errorsBody) {
    $exception = catchValidationException(connector422($errorsBody));

    expect($exception->getMessage())->toContain('The given data was invalid.')
        ->and($exception->getMessage())->toContain('invoice_number');
});

it('uses base message when no errors present', function () {
    $exception = catchValidationException(connector422(['message' => 'Custom error']));

    expect($exception->getMessage())->toBe('Custom error');
});
