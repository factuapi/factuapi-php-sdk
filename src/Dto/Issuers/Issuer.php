<?php

namespace Factuapi\PhpSdk\Dto\Issuers;

class Issuer
{
    public function __construct(
        public string $taxNumber,
        public string $name,
        public string $address,
        public string $postCode,
        public string $city,
        public string $province,
    ) {
    }
}
