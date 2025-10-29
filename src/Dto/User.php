<?php

namespace Factuapi\PhpSdk\Dto;

class User
{
    public function __construct(
        public string $name,
        public string $email,
    ) {
    }

    public static function fromResponse(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
        );
    }
}
