<?php

namespace Factuapi\PhpSdk\Dto\CreditNotes;

enum CreditNoteCategory: string
{
    /**
     * Art. 80.1, 80.2, 80.6
     */
    case AdjustmentForDiscountsOrReturns = 'AdjustmentForDiscountsOrReturns';

    /**
     * Art. 80.3
     */
    case AdjustmentForBankruptcy = 'AdjustmentForBankruptcy';

    /**
     * Art. 80.4
     */
    case AdjustmentForUncollectibleDebts = 'AdjustmentForUncollectibleDebts';

    case OtherAdjustments = 'OtherAdjustments';

    case SimplifiedInvoiceAdjustment = 'SimplifiedInvoiceAdjustment';

}
