<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

enum OperationQualification: string
{
    case SubjectToWithoutReverseCharge = 'SubjectToWithoutReverseCharge';

    case SubjectToWithReverseCharge = 'SubjectToWithReverseCharge';

    case NotSubject_Art_7_14_Others = 'NotSubject_Art_7_14_Others';

    case NotSubjectLocalizationRules = 'NotSubjectLocalizationRules';

    case Exempt = 'Exempt';

    case NotSubject = 'NotSubject';

    case NotSubjectExempt = 'NotSubjectExempt';
}
