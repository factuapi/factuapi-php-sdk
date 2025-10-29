<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

enum InvoiceType: string
{
    case Invoice = 'Invoice';

    case Simplified = 'Simplified';

    case SubstitutionForSimplified = 'SubstitutionForSimplified';

    case CreditNote = 'CreditNote';

}
