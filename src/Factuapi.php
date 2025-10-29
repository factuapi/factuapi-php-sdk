<?php

namespace Factuapi\PhpSdk;

use Factuapi\PhpSdk\Connectors\FactuapiConnector;
use Factuapi\PhpSdk\Dto\Company;
use Factuapi\PhpSdk\Dto\User;
use Factuapi\PhpSdk\Requests\CompanyRequest;
use Factuapi\PhpSdk\Requests\MeRequest;
use Factuapi\PhpSdk\Resources\Invoices;

class Factuapi
{
    protected FactuapiConnector $connector;

    public function __construct(
        string $apiToken,
        string $baseUrl = 'https://app.factuapi.com/api/',
        int $timeoutInSeconds = 10,
    ) {
        $this->connector = new FactuapiConnector(
            apiToken: $apiToken,
            baseUrl: $baseUrl,
            timeoutInSeconds: $timeoutInSeconds,
        );
    }

    public function me(): User
    {
        $request = new MeRequest();

        return $this->connector->send($request)->dto();
    }

    public function company(): Company
    {
        return $this->connector->send(new CompanyRequest)->dto();
    }

    public function invoices(): Invoices
    {
        return new Invoices($this->connector);
    }
}
