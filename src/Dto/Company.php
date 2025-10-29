<?php

namespace Factuapi\PhpSdk\Dto;

class Company
{
    public function __construct(
        public string $name,
        public string $vatNumber,
    ) {
    }

    public static function fromResponse(array $data): self
    {
        return new self(
            name: $data['name'],
            vatNumber: $data['vat_number'],
        );
    }
}
