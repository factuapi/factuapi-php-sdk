<?php

namespace Factuapi\PhpSdk\Requests;

use Factuapi\PhpSdk\Dto\Company;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class CompanyRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/company';
    }

    public function createDtoFromResponse(Response $response): Company
    {
        return Company::fromResponse($response->json());
    }
}
