<?php

use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceItem;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceTotals;
use Factuapi\PhpSdk\Dto\Issuers\Issuer;
use Factuapi\PhpSdk\Dto\Recipients\SpanishRecipient;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceType;
use Factuapi\PhpSdk\Dto\Invoices\RegimeKeyIVA;
use Factuapi\PhpSdk\Dto\Invoices\TaxType;

function makeInvoice(array $overrides = []): Invoice
{
    return new Invoice(
        invoiceId: $overrides['invoiceId'] ?? new InvoiceId(seriesCode: 'TEST', number: '1'),
        issueDate: $overrides['issueDate'] ?? new DateTime('2024-01-15'),
        invoiceType: $overrides['invoiceType'] ?? InvoiceType::Invoice,
        issuer: $overrides['issuer'] ?? new Issuer(
            taxNumber: 'B12345678',
            name: 'Issuer SL',
            address: 'Calle Mayor 1',
            postCode: '28001',
            city: 'Madrid',
            province: 'Madrid',
        ),
        recipient: $overrides['recipient'] ?? new SpanishRecipient(
            taxNumber: 'A87654321',
            name: 'Recipient SL',
            address: 'Calle Menor 2',
            postCode: '08001',
            city: 'Barcelona',
            province: 'Barcelona',
        ),
        items: $overrides['items'] ?? [
            new InvoiceItem(
                description: 'Service',
                basePrice: 100.00,
                taxType: TaxType::IVA,
                regimeKey: RegimeKeyIVA::General,
                taxRate: 21.0,
                taxPrice: 21.00,
                amount: 121.00,
            ),
        ],
        totals: $overrides['totals'] ?? new InvoiceTotals(
            basePrice: 100.00,
            taxPrice: 21.00,
            amount: 121.00,
        ),
        itemDescription: $overrides['itemDescription'] ?? 'Professional services',
        creditNote: $overrides['creditNote'] ?? null,
        relatedInvoices: $overrides['relatedInvoices'] ?? null,
    );
}

function createInvoiceResponseFixture(): array
{
    return json_decode(file_get_contents(__DIR__.'/Fixtures/create_invoice.json'), true);
}
