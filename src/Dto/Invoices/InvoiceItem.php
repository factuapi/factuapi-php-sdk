<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

class InvoiceItem
{
    public function __construct(
        public string $description,
        public float $basePrice,
        public TaxType $taxType,
        public RegimeKey $regimeKey,
        public float $taxRate,
        public float $taxPrice,
        public float $amount,
        public ?float $equivalenceSurchargeType = null,
        public ?float $equivalenceSurchargePrice = null,
        public ?ExemptionReason $exemptionReason = null,
        public ?OperationQualification $operationQualification = null,
    ) {
    }
}
