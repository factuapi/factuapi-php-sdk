<?php

namespace Factuapi\PhpSdk\Dto\Recipients;

enum OtherIdType: string
{
    /**
     * NIF-IVA
     */
    case NifIva = 'NifIva';

    /**
     * Pasaporte
     */
    case Pasaporte = 'Passport';

    /**
     * Documento oficial de identificación expedido por el país o territorio de residencia
     */
    case ExpedidoPorPaisResidencia = 'OficialIdDocument';

    /**
     * Certificado de residencia
     */
    case CertificadoResidencia = 'CertificateOfResidence';

    /**
     * Otro documento probatorio
     */
    case Otro = 'OtherSupportingDocument';

    /**
     * No censado
     */
    case NoCensado = 'NotRegistered';
}
