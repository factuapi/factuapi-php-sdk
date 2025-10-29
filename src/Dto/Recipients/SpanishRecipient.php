<?php

namespace Factuapi\PhpSdk\Dto\Recipients;

class SpanishRecipient implements Recipient
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
