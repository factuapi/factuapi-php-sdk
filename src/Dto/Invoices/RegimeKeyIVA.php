<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

enum RegimeKeyIVA: string implements RegimeKey
{
    case General = 'General';

    case Export = 'Export';

    case UsedGoods = 'UsedGoods';

    case InvestmentGold = 'InvestmentGold';

    case TravelAgencies = 'TravelAgencies';

    case IVAGroupEntities = 'IVAGroupEntities';

    case CashAccountingScheme = 'CashAccountingScheme';

    case IPSI_IGIC = 'IPSI_IGIC';

    case TravelAgencyMediators = 'TravelAgencyMediators';

    case ThirdPartyCollections = 'ThirdPartyCollections'; // CobrosCuentaTerceros_HonorariosProfesionales_PropiedadIndustrial_Autor_ColegiosProfesionales

    case BusinessPremisesLease = 'BusinessPremisesLease';

    case IVAPendingAccrual_PublicAdministrationRecipient = 'IVAPendingAccrual_PublicAdministrationRecipient';

    case IVAPendingAccrual_SuccessiveInstallments = 'IVAPendingAccrual_SuccessiveInstallments';

    case OSS_IOSS = 'OSS_IOSS';

    case EquivalenceSurcharge = 'EquivalenceSurcharge';

    case REAGYP = 'REAGYP';

    case Simplified = 'Simplified';

    public function getValue(): string
    {
        return $this->value;
    }
}
