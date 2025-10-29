<?php

namespace Factuapi\PhpSdk\Requests\Invoices;

use Factuapi\PhpSdk\Dto\Invoices\InvoiceId;
use Factuapi\PhpSdk\Dto\Responses\CreateInvoiceResponse;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CancelInvoiceRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly InvoiceId $invoiceId,
        public readonly array $process,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return "/invoices/cancel";
    }

    protected function defaultBody(): array
    {
        return [
            'invoice_series_code' => $this->invoiceId->seriesCode,
            'invoice_number' => $this->invoiceId->number,
            'process' => $this->process,
        ];
    }

    public function createDtoFromResponse(Response $response): CreateInvoiceResponse
    {
        return CreateInvoiceResponse::fromResponse($response->json());
    }
}
