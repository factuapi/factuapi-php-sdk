<?php

namespace Factuapi\PhpSdk\Dto\Invoices;

enum RegimeKeyIGIC: string implements RegimeKey
{
    case General = 'General';

    case Export = 'Export';

    case UsedGoods = 'UsedGoods';

    case InvestmentGold = 'InvestmentGold';

    case TravelAgencies = 'TravelAgencies';

    case IVAGroupEntities = 'IVAGroupEntities';

    case CashAccountingScheme = 'CashAccountingScheme';

    case IPSI_IVA = 'IPSI_IVA';

    case TravelAgencyMediators = 'TravelAgencyMediators';

    case ThirdPartyCollections = 'ThirdPartyCollections'; // CobrosCuentaTerceros_HonorariosProfesionales_PropiedadIndustrial_Autor_ColegiosProfesionales

    case BusinessPremisesLease = 'BusinessPremisesLease';

    case IGICPendingAccrual_PublicAdministrationRecipient = 'IGICPendingAccrual_PublicAdministrationRecipient';

    case IGICPendingAccrual_SuccessiveInstallments = 'IGICPendingAccrual_SuccessiveInstallments';

    case RetailTrader = 'RetailTrader';

    case SmallBusiness = 'SmallBusiness';

    case DomesticExemptOperations = 'DomesticExemptOperations';

    public function getValue(): string
    {
        return $this->value;
    }
}
