<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

enum ExemptionReason: string
{
    case Article20 = 'Article20';

    case Article21 = 'Article21';

    case Article22 = 'Article22';

    case Article23_24 = 'Article23_24';

    case Article25 = 'Article25';

    case Other = 'Other';
}
