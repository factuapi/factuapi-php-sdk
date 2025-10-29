<?php

namespace Factuapi\PhpSdk\Dto\Recipients;

class OtherRecipient implements Recipient
{
    public function __construct(
        public string $countryCode,
        public OtherIdType $idType,
        public string $idNumber,
        public string $name,
        public string $address,
        public string $postCode,
        public string $city,
        public string $province,
    ) {
    }
}
