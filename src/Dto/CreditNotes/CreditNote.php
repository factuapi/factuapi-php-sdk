<?php

namespace Factuapi\PhpSdk\Dto\CreditNotes;

class CreditNote
{
    public function __construct(
        public CreditNoteType $type,
        public CreditNoteCategory $category,
        public float $basePrice,
        public float $taxPrice,
    ) {
    }
}
