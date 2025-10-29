<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

class InvoiceTotals
{
    public function __construct(
        public float $basePrice,
        public float $taxPrice,
        public float $amount,
    ) {
    }
}
