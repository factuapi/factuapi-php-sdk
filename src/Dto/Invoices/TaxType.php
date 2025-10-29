<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

enum TaxType: string
{
    case IVA = 'IVA';

    case IPSI = 'IPSI';

    case IGIC = 'IGIC';

    case Others = 'Others';
}
