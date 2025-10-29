<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

use DateTime;
use Factuapi\PhpSdk\Dto\CreditNotes\CreditNote;
use Factuapi\PhpSdk\Dto\Issuers\Issuer;
use Factuapi\PhpSdk\Dto\Recipients\Recipient;

class Invoice
{
    /**
     * @param  array<InvoiceItem>  $items
     * @param  null|array<RelatedInvoice>  $relatedInvoices
     */
    public function __construct(
        public InvoiceId $invoiceId,
        public DateTime $issueDate,
        public InvoiceType $invoiceType,
        public Issuer $issuer,
        public Recipient $recipient,
        public array $items,
        public InvoiceTotals $totals,
        public string $itemDescription,
        public ?CreditNote $creditNote = null,
        public ?array $relatedInvoices = null,
    ) {
    }
}
