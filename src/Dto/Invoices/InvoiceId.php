<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

class InvoiceId
{
    public function __construct(
        public string $seriesCode,
        public string $number,
    ) {
    }

    public function toString(): string
    {
        return $this->seriesCode.$this->number;
    }
}
