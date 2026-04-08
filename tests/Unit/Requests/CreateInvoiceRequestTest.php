<?php

use Factuapi\PhpSdk\Connectors\FactuapiConnector;
use Factuapi\PhpSdk\Dto\CreditNotes\CreditNote;
use Factuapi\PhpSdk\Dto\CreditNotes\CreditNoteCategory;
use Factuapi\PhpSdk\Dto\CreditNotes\CreditNoteType;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceItem;
use Factuapi\PhpSdk\Dto\Invoices\RelatedInvoice;
use Factuapi\PhpSdk\Dto\Invoices\ExemptionReason;
use Factuapi\PhpSdk\Dto\Invoices\OperationQualification;
use Factuapi\PhpSdk\Dto\Invoices\TaxType;
use Factuapi\PhpSdk\Dto\Invoices\RegimeKeyIVA;
use Factuapi\PhpSdk\Dto\Recipients\OtherIdType;
use Factuapi\PhpSdk\Dto\Recipients\OtherRecipient;
use Factuapi\PhpSdk\Dto\Responses\CreateInvoiceResponse;
use Factuapi\PhpSdk\Requests\Invoices\CreateInvoiceRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('resolves to /invoices when no correctInvoiceId', function () {
    $request = new CreateInvoiceRequest(makeInvoice(), []);

    expect($request->resolveEndpoint())->toBe('/invoices');
});

it('resolves to /invoices/correct when correctInvoiceId is set', function () {
    $request = new CreateInvoiceRequest(
        makeInvoice(),
        [],
        correctInvoiceId: new InvoiceId(seriesCode: 'TEST', number: '1'),
    );

    expect($request->resolveEndpoint())->toBe('/invoices/correct');
});

it('builds correct body with SpanishRecipient', function () {
    $request = new CreateInvoiceRequest(makeInvoice(), ['verifactu' => true]);
    $body = $request->body()->all();
    $invoice = $body['invoice'];

    expect($invoice['invoice_series_code'])->toBe('TEST')
        ->and($invoice['invoice_number'])->toBe('1')
        ->and($invoice['issue_date'])->toBe('2024-01-15')
        ->and($invoice['invoice_type'])->toBe('Invoice')
        ->and($invoice['issuer_tax_number'])->toBe('B12345678')
        ->and($invoice['recipient_tax_number'])->toBe('A87654321')
        ->and($invoice['recipient_name'])->toBe('Recipient SL')
        ->and($body['process'])->toBe(['verifactu' => true]);
});

it('builds correct body with OtherRecipient', function () {
    $invoice = makeInvoice([
        'recipient' => new OtherRecipient(
            countryCode: 'FR',
            idType: OtherIdType::NifIva,
            idNumber: 'FR12345678',
            name: 'French SAS',
            address: '1 Rue de Paris',
            postCode: '75001',
            city: 'Paris',
            province: 'Île-de-France',
        ),
    ]);

    $body = (new CreateInvoiceRequest($invoice, []))->body()->all();
    $inv = $body['invoice'];

    expect($inv['recipient_id_other_type']['country_code'])->toBe('FR')
        ->and($inv['recipient_id_other_type']['id_type'])->toBe('NifIva')
        ->and($inv['recipient_id_other_type']['id'])->toBe('FR12345678')
        ->and($inv['recipient_name'])->toBe('French SAS')
        ->and($inv)->not->toHaveKey('recipient_tax_number');
});

it('includes previous_invoice when correctInvoiceId is set', function () {
    $request = new CreateInvoiceRequest(
        makeInvoice(),
        [],
        correctInvoiceId: new InvoiceId(seriesCode: 'ORIG', number: '99'),
    );

    $invoice = $request->body()->all()['invoice'];

    expect($invoice['previous_invoice']['invoice_series_code'])->toBe('ORIG')
        ->and($invoice['previous_invoice']['invoice_number'])->toBe('99');
});

it('does not include previous_invoice when correctInvoiceId is null', function () {
    $body = (new CreateInvoiceRequest(makeInvoice(), []))->body()->all();

    expect($body['invoice'])->not->toHaveKey('previous_invoice');
});

it('includes credit_note when set on Invoice', function () {
    $invoice = makeInvoice([
        'creditNote' => new CreditNote(
            type: CreditNoteType::ByDifferences,
            category: CreditNoteCategory::AdjustmentForDiscountsOrReturns,
            basePrice: 100.0,
            taxPrice: 21.0,
        ),
    ]);

    $inv = (new CreateInvoiceRequest($invoice, []))->body()->all()['invoice'];

    expect($inv['credit_note']['type'])->toBe('ByDifferences')
        ->and($inv['credit_note']['category'])->toBe('AdjustmentForDiscountsOrReturns')
        ->and($inv['credit_note']['base_price'])->toBe(100.0)
        ->and($inv['credit_note']['tax_price'])->toBe(21.0);
});

it('includes related_invoices when set', function () {
    $relatedId = new InvoiceId(seriesCode: 'REL', number: '5');
    $invoice = makeInvoice([
        'relatedInvoices' => [
            new RelatedInvoice(
                invoiceId: $relatedId,
                issueDate: new DateTime('2023-06-01'),
                issuerTaxNumber: 'B12345678',
            ),
        ],
    ]);

    $inv = (new CreateInvoiceRequest($invoice, []))->body()->all()['invoice'];

    expect($inv['related_invoices'])->toHaveCount(1)
        ->and($inv['related_invoices'][0]['invoice_series_code'])->toBe('REL')
        ->and($inv['related_invoices'][0]['invoice_number'])->toBe('5')
        ->and($inv['related_invoices'][0]['issue_date'])->toBe('2023-06-01')
        ->and($inv['related_invoices'][0]['issuer_tax_number'])->toBe('B12345678');
});

it('includes equivalence_surcharge fields when set on InvoiceItem', function () {
    $item = new InvoiceItem(
        description: 'Service',
        basePrice: 100.0,
        taxType: TaxType::IVA,
        regimeKey: RegimeKeyIVA::EquivalenceSurcharge,
        taxRate: 21.0,
        taxPrice: 21.0,
        amount: 121.0,
        equivalenceSurchargeType: 5.2,
        equivalenceSurchargePrice: 5.2,
    );

    $inv = (new CreateInvoiceRequest(makeInvoice(['items' => [$item]]), []))->body()->all()['invoice'];

    expect($inv['items'][0])->toHaveKey('equivalence_surcharge_type')
        ->and($inv['items'][0])->toHaveKey('equivalence_surcharge_price');
});

it('includes exemption_reason when set on InvoiceItem', function () {
    $item = new InvoiceItem(
        description: 'Exempt Service',
        basePrice: 100.0,
        taxType: TaxType::IVA,
        regimeKey: RegimeKeyIVA::General,
        taxRate: 0.0,
        taxPrice: 0.0,
        amount: 100.0,
        exemptionReason: ExemptionReason::Article20,
    );

    $inv = (new CreateInvoiceRequest(makeInvoice(['items' => [$item]]), []))->body()->all()['invoice'];

    expect($inv['items'][0]['exemption_reason'])->toBe('Article20');
});

it('includes operation_qualification when set on InvoiceItem', function () {
    $item = new InvoiceItem(
        description: 'Qualified Service',
        basePrice: 100.0,
        taxType: TaxType::IVA,
        regimeKey: RegimeKeyIVA::General,
        taxRate: 21.0,
        taxPrice: 21.0,
        amount: 121.0,
        operationQualification: OperationQualification::SubjectToWithReverseCharge,
    );

    $inv = (new CreateInvoiceRequest(makeInvoice(['items' => [$item]]), []))->body()->all()['invoice'];

    expect($inv['items'][0]['operation_qualification'])->toBe('SubjectToWithReverseCharge');
});

it('maps response to CreateInvoiceResponse DTO', function () {
    $connector = new FactuapiConnector('token', 'https://app.factuapi.com/api/', 10);
    $connector->withMockClient(new MockClient([
        CreateInvoiceRequest::class => MockResponse::make(createInvoiceResponseFixture()),
    ]));

    $response = $connector->send(new CreateInvoiceRequest(makeInvoice(), []))->dto();

    expect($response)->toBeInstanceOf(CreateInvoiceResponse::class)
        ->and($response->message)->toBe('Invoice created successfully');
});
