# Factuapi PHP SDK

A PHP SDK for interacting with the Factuapi API. This SDK allows you to easily create, manage, and process invoices through the Factuapi platform, including support for Spanish invoicing requirements like equivalence surcharge and VIES VAT numbers.

## Installation

```bash
composer require factuapi/factuapi-php-sdk
```

## Usage

To authenticate, you'll need an API token. You can create one in
the API tokens screen at Factuapi.

```php
use Factuapi\PhpSdk\Factuapi;

$factuapi = new Factuapi('your-api-token');
```

### Create invoice

You can create invoices using the `invoices()->create()` method. The method requires an `Invoice` object with all the necessary information.

```php
use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceItem;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceTotals;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceType;
use Factuapi\PhpSdk\Dto\Invoices\TaxType;
use Factuapi\PhpSdk\Dto\Invoices\RegimeKeyIVA;
use Factuapi\PhpSdk\Dto\Issuer;
use Factuapi\PhpSdk\Dto\Recipients\SpanishRecipient;

$factuapi
  ->invoices()
  ->create(
    invoice: new Invoice(
      invoiceId: new InvoiceId(
        seriesCode: "2025",
        number: "101",
      ),
      issueDate: new DateTime("2025-11-14"),
      invoiceType: InvoiceType::Invoice,
      issuer: new Issuer(
        taxNumber: "B12345678",
        name: "Example Company SL",
        address: "C/ Example Street 123",
        postCode: "08001",
        city: "Barcelona",
        province: "Barcelona"
      ),
      recipient: new SpanishRecipient(
        taxNumber: "B87654321",
        name: "Client Company SL",
        address: "Av. Client 456",
        postCode: "08002",
        city: "Barcelona",
        province: "Barcelona"
      ),
      items: [
        new InvoiceItem(
          description: "Professional services",
          basePrice: 100,
          taxType: TaxType::IVA,
          regimeKey: RegimeKeyIVA::General,
          taxRate: 21,
          taxPrice: 21,
          amount: 121
        )
      ],
      totals: new InvoiceTotals(basePrice: 100, taxPrice: 21, amount: 121),
      itemDescription: "Invoice for services"
    ),
    process: ["verifactu"]
  );
```

#### Equivalence surcharge

For invoices that require equivalence surcharge (recargo de equivalencia), you can specify the `equivalenceSurchargeType` and `equivalenceSurchargePrice` in the invoice items:

```php
use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceType;
use Factuapi\PhpSdk\Dto\Invoices\TaxType;
use Factuapi\PhpSdk\Dto\Invoices\RegimeKeyIVA;

$factuapi
  ->invoices()
  ->create(
    invoice: new Invoice(
      invoiceId: new InvoiceId(
        seriesCode: "2025",
        number: "110",
      )
      issueDate: new DateTime("2025-11-14"),
      invoiceType: InvoiceType::Invoice,
      issuer: new Issuer(
        taxNumber: "B12345678",
        name: "Example Company SL",
        address: "C/ Example Street 123",
        postCode: "08001",
        city: "Barcelona",
        province: "Barcelona"
      ),
      recipient: new SpanishRecipient(
        taxNumber: "B87654321",
        name: "Client Company SL",
        address: "Av. Client 456",
        postCode: "08002",
        city: "Barcelona",
        province: "Barcelona"
      ),
      items: [
        new InvoiceItem(
          description: "Office supplies",
          basePrice: 150.00,
          taxType: TaxType::IVA,
          regimeKey: RegimeKeyIVA::General,
          taxRate: 21,
          taxPrice: 31.50,
          // Equivalence surcharge fields
          equivalenceSurchargeType: 5.2,
          equivalenceSurchargePrice: 7.80,
          amount: 189.30
        )
      ],
      totals: new InvoiceTotals(
        basePrice: 150.00,
        taxPrice: 39.30, // Regular tax + equivalence surcharge
        amount: 189.30
      ),
      itemDescription: "Invoice with equivalence surcharge"
    ),
    process: ["verifactu"]
  );
```

#### VIES VAT number

For intra-community invoices with VIES VAT numbers, use the `OtherRecipient` class with the appropriate country code and ID type:

```php
use Factuapi\PhpSdk\Dto\Recipients\OtherRecipient;
use Factuapi\PhpSdk\Dto\Recipients\OtherIdType;
use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceType;
use Factuapi\PhpSdk\Dto\Invoices\TaxType;
use Factuapi\PhpSdk\Dto\Invoices\RegimeKeyIVA;

$factuapi
  ->invoices()
  ->create(
    invoice: new Invoice(
      invoiceId: new InvoiceId(
        seriesCode: "2025",
        number: "116",
      ),
      issueDate: new DateTime("2025-11-21"),
      invoiceType: InvoiceType::Invoice,
      issuer: new Issuer(
        taxNumber: "B12345678",
        name: "Example Company SL",
        address: "C/ Example Street 123",
        postCode: "08001",
        city: "Barcelona",
        province: "Barcelona"
      ),
      recipient: new OtherRecipient(
        countryCode: "DE",
        idType: OtherIdType::NifIva,
        idNumber: "DE123456789",
        name: "German Client GmbH",
        address: "Hauptstrasse 1",
        postCode: "10115",
        city: "Berlin",
        province: "Berlin"
      ),
      items: [
        new InvoiceItem(
          description: "Consulting services",
          basePrice: 100,
          taxType: TaxType::IVA,
          regimeKey: RegimeKeyIVA::General,
          taxRate: 21,
          taxPrice: 21,
          amount: 121
        )
      ],
      totals: new InvoiceTotals(basePrice: 100, taxPrice: 21, amount: 121),
      itemDescription: "International invoice"
    ),
    process: ["verifactu"]
  );
```

### Create credit note

To create a credit note (rectificativa), use the `invoices()->create()` method with `invoiceType: "CreditNote"` and include the `creditNote` and `relatedInvoices` properties:

```php
use Factuapi\PhpSdk\Dto\CreditNotes\CreditNote;
use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\RelatedInvoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceType;
use Factuapi\PhpSdk\Dto\Invoices\TaxType;
use Factuapi\PhpSdk\Dto\Invoices\RegimeKeyIVA;
use \Factuapi\PhpSdk\Dto\CreditNotes\CreditNoteType;
use \Factuapi\PhpSdk\Dto\CreditNotes\CreditNoteCategory;

$factuapi
  ->invoices()
  ->create(
    invoice: new Invoice(
      invoiceId: new InvoiceId(
        seriesCode: "REC2025",
        number: "1",      
      ),
      issueDate: new DateTime("2025-11-14"),
      invoiceType: InvoiceType::CreditNote,
      issuer: new Issuer(
        taxNumber: "B12345678",
        name: "Example Company SL",
        address: "C/ Example Street 123",
        postCode: "08001",
        city: "Barcelona",
        province: "Barcelona"
      ),
      recipient: new SpanishRecipient(
        taxNumber: "B87654321",
        name: "Client Company SL",
        address: "Av. Client 456",
        postCode: "08002",
        city: "Barcelona",
        province: "Barcelona"
      ),
      items: [
        new InvoiceItem(
          description: "Correction for invoice 2025-101",
          basePrice: -10,
          taxType: TaxType::IVA,
          regimeKey: RegimeKeyIVA::General,
          taxRate: 21,
          taxPrice: -2.1,
          amount: -12.1
        )
      ],
      totals: new InvoiceTotals(basePrice: -10, taxPrice: -2.1, amount: -12.1),
      itemDescription: "Credit note",
      creditNote: new CreditNote(
        type: CreditNoteType::ByDifferences,
        category: CreditNoteCategory::AdjustmentForDiscountsOrReturns,
        basePrice: 100,
        taxPrice: 21
      ),
      relatedInvoices: [
        new RelatedInvoice(
          invoiceId: new InvoiceId(
            seriesCode: "2025",
            number: "101",            
          ),
          issueDate: new DateTime("2025-11-14"),
          issuerTaxNumber: "B12345678"
        )
      ]
    ),
    process: ["verifactu"]
  );
```

### Correct invoice

To correct an invoice without fiscal impact (e.g., changing recipient details but not totals), use the `invoices()->correct()` method:

```php
use Factuapi\PhpSdk\Dto\Invoices\InvoiceType;
use Factuapi\PhpSdk\Dto\Invoices\Invoice;
use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Invoices\TaxType;
use Factuapi\PhpSdk\Dto\Invoices\RegimeKeyIVA;

$factuapi
  ->invoices()
  ->correct(
    previousInvoiceId: new InvoiceId(
      seriesCode: "2025",
      number: "105",
    ),
    invoice: new Invoice(
      invoiceId: new InvoiceId(
        seriesCode: "2025",
        number: "105",
      ),
      issueDate: new DateTime("2025-11-14"),
      invoiceType: InvoiceType::Invoice,
      issuer: new Issuer(
        taxNumber: "B12345678",
        name: "Example Company SL",
        address: "C/ Example Street 123",
        postCode: "08001",
        city: "Barcelona",
        province: "Barcelona"
      ),
      recipient: new SpanishRecipient(
        taxNumber: "B98765432",
        name: "Updated Client SL",
        address: "C/ New Address 789",
        postCode: "08003",
        city: "Barcelona",
        province: "Barcelona"
      ),
      items: [
        new InvoiceItem(
          description: "Professional services",
          basePrice: 100,
          taxType: TaxType::IVA,
          regimeKey: RegimeKeyIVA::General,
          taxRate: 21,
          taxPrice: 21,
          amount: 121
        )
      ],
      totals: new InvoiceTotals(basePrice: 100, taxPrice: 21, amount: 121),
      itemDescription: "Invoice"
    ),
    process: ["verifactu"]
  );
```

For corrections with fiscal impact (changing amounts), you should create a credit note (rectificativa) instead.

### Cancel invoice

Only use invoice cancellation when the invoice was generated by error (e.g., during testing or for a service that was not ultimately contracted). Never use cancellation to modify an invoice - in those cases, you should create a credit note (rectificativa) or correction instead.

To cancel an invoice, use the `invoices()->cancel()` method with the invoice number:

```php
$factuapi
    ->invoices()
    ->cancel(
        invoiceId: new InvoiceId(
          seriesCode: "2025",
          number: "103",
        ),
        process: ["verifactu"]
    );
```

This will mark the invoice as cancelled in the system.

### Verifactu Integration

Verifactu is a service that facilitates compliance with Spanish tax authority (AEAT) anti-fraud requirements. By integrating
with Verifactu, your invoices are automatically verified and secured with cryptographic hashes, ensuring regulatory 
compliance with anti-fraud measures and providing official verification.

#### Enabling Verifactu Processing

To submit an invoice to the Verifactu service, add `"verifactu"` to the `process` array parameter when creating, 
correcting, or canceling an invoice:

```php
$factuapi
  ->invoices()
  ->create(
    invoice: new Invoice(),
    process: ["verifactu"]
  );
```

#### Understanding the Response

When an invoice is processed through Verifactu, the response will include verification data with the following components:

```php
[
    "processes" => [
        "type" => "verifactu",
        "status" => "pending",
        "info" => [
            "hash" => "8C6C8FB8DA[...]",                      // Unique invoice hash for verification
            "previous_invoice_hash" => "702A8AD6A[...]",      // Hash of the previous invoice in the series
            "generated_at" => "2025-11-21T16:23:53.301000Z",  // Timestamp of verification request
            "qr_url" => "https://[...]",                      // Public verification URL
            "qr_base64" => "iVBORw[...]",                     // Base64-encoded QR code image to include on invoices
        ],
        "messages" => [],                                      // Any messages from the Verifactu response
    ]
]
```

#### Processing Status

The initial status will always be `"pending"` as Verifactu processes submissions in batches. Your invoice will typically
be processed within one minute of submission. You can check the final status by retrieving the invoice details later.

#### QR Code Implementation

The response includes two options for implementing the required verification QR code on your invoices:

1. `qr_base64`: A ready-to-use base64-encoded QR code image that can be directly embedded in digital or printed invoices
2. `qr_url`: The verification URL that can be used to generate your own QR code if you prefer to use a custom QR code generator

Including this QR code on your invoices is mandatory for compliance in Spain.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
