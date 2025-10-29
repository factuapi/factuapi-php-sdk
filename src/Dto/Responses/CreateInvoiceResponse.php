<?php

namespace Factuapi\PhpSdk\Dto\Responses;

class CreateInvoiceResponse
{
    public function __construct(
        public ?string $message,
        public array $invoice,
        public array $data,
        public ?array $processes,
    ) {
    }

    public static function fromResponse(array $data): self
    {
        return new self(
            message: $data['message'] ?? null,
            invoice: $data['invoice'],
            data: $data['data'],
            processes: $data['processes'] ?? null,
        );
    }
}
