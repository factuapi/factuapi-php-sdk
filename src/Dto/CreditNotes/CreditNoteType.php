<?php

namespace Factuapi\PhpSdk\Dto\CreditNotes;

enum CreditNoteType: string
{
    case BySubstitution = 'BySubstitution';

    case ByDifferences = 'ByDifferences';
}
