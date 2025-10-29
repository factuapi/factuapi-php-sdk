<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

use DateTime;

class RelatedInvoice
{
    public function __construct(
        public InvoiceId $invoiceId,
        public DateTime $issueDate,
        public string $issuerTaxNumber,
    ) {
    }
}
